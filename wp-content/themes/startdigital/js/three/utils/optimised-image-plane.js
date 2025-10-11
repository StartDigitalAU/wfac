import * as THREE from 'three'
import { getLenis } from '../../utils/smooth-scroll'
import simplexNoise from '../materials/shaders/simplex-noise'

class OptimisedImagePlane {
	constructor(scene, camera, element, container = null, config = {}) {
		this.scene = scene
		this.camera = camera
		this.element = element
		this.lenis = getLenis()
		this.container = container

		// Configuration options
		this.config = {
			borderRadius: config.borderRadius || 16,
			zPosition: config.zPosition || 0,
			speed: config.speed || 0,
			progress: config.progress || 0.3,
			...config,
		}

		this.enabled = true
		this.targetSpeed = this.config.speed
		this.lerpFactor = 0.05

		// Create simple geometry with more subdivisions for deformation
		this.geometry = new THREE.PlaneGeometry(1, 1, 1, 8)
		this.material = this.createMaterial()
		this.mesh = new THREE.Mesh(this.geometry, this.material)
		this.mesh.frustumCulled = false
		this.scene.add(this.mesh)

		// Track dimensions for visibility culling
		this.dimensions = { x: 0, y: 0, width: 0, height: 0 }

		this.setupListeners()
		this.updatePlane()
	}

	createMaterial() {
		const rect = this.element.getBoundingClientRect()
		const imgElement = this.element.querySelector('img')

		if (!imgElement) {
			return new THREE.MeshBasicMaterial({ color: 0xff0000 })
		}

		imgElement.style.opacity = 0
		const imageSrc = imgElement.src

		const vertexShader = /* glsl */ `
			uniform vec2 u_containerSize;
			uniform vec2 u_domXY;
			uniform vec2 u_domWH;
			uniform vec2 uTextureSize;
			uniform vec2 uQuadSize;
			uniform float uSpeed;
			
			varying vec2 vUvCover;
			varying vec2 vUv;
			
			float PI = 3.141592653589793;

			// UV cover function
			vec2 getCoverUvVert(vec2 uv, vec2 textureSize, vec2 quadSize) {
				vec2 ratio = vec2(
					min((quadSize.x / quadSize.y) / (textureSize.x / textureSize.y), 1.0),
					min((quadSize.y / quadSize.x) / (textureSize.y / textureSize.x), 1.0)
				);
				return vec2(
					uv.x * ratio.x + (1.0 - ratio.x) * 0.5,
					uv.y * ratio.y + (1.0 - ratio.y) * 0.5
				);
			}

			vec3 deformationCurve(vec3 position, vec2 uv, float speed) {
				float limitedSpeed = clamp(speed * 0.2, -0.25, 0.25);
				
				// Apply deformation in normalized space
				position.x = position.x - sin(uv.y * PI) * -1.0 * limitedSpeed;
				position.z = position.z - 1.0 * abs(limitedSpeed);
				return position;
			}

			void main() {
				vUvCover = getCoverUvVert(uv, uTextureSize, uQuadSize);
				vUv = uv;
				
				// Apply deformation in normalized space (-0.5 to 0.5)
				vec3 deformedPos = deformationCurve(position, vUvCover, uSpeed);
				
				// Now scale to pixel space
				vec2 vertexOffset = deformedPos.xy * u_domWH;
				
				// Calculate pixel position within container
				vec2 pixelXY = u_domXY + u_domWH * 0.5;
				
				// Flip Y because container coordinates are top-down, WebGL is bottom-up
				pixelXY.y = u_containerSize.y - pixelXY.y;
				
				// Add vertex offset
				pixelXY += vertexOffset;
				
				// Convert to NDC space (-1 to 1)
				vec2 ndc = (pixelXY / u_containerSize) * 2.0 - 1.0;
				
				gl_Position = vec4(ndc, deformedPos.z, 1.0);
			}
		`

		const fragmentShader = /* glsl */ `
			uniform sampler2D uTexture;
			uniform float uTime;
			uniform vec2 uQuadSize;
			uniform vec2 u_domWH;
			uniform float uProgress;
			uniform float u_borderRadius;
			
			varying vec2 vUvCover;
			varying vec2 vUv;

			${simplexNoise}

			// Rounded rectangle SDF
			float roundedBoxSDF(vec2 centerPos, vec2 size, float radius) {
				return length(max(abs(centerPos) - size + radius, 0.0)) - radius;
			}

			void main() {
				// Border radius with antialiasing
				vec2 pixelSize = 1.0 / u_domWH;
				vec2 pos = (vUv - 0.5) * u_domWH;
				vec2 halfSize = u_domWH * 0.5;
				
				float dist = roundedBoxSDF(pos, halfSize, u_borderRadius);
				float edgeWidth = length(pixelSize * u_domWH) * 0.5;
				float borderAlpha = 1.0 - smoothstep(-edgeWidth, edgeWidth, dist);
				
				// Original image material logic
				vec3 texture = texture2D(uTexture, vUvCover).rgb;

				// Square UV for noise
				vec2 squareUv = vUv;
				float aspect = uQuadSize.x / uQuadSize.y;
				
				if (aspect > 1.0) {
					squareUv.x = (vUv.x - 0.5) * aspect + 0.5;
				} else {
					squareUv.y = (vUv.y - 0.5) / aspect + 0.5;
				}

				// Distance from center
				vec2 centeredUv = vUv - 0.5;
				float distanceFromCenter = length(centeredUv);

				// Add noise
				float scale = 4.5;
				vec3 coord = vec3(squareUv * scale, uTime * 0.3);
				float n = snoise(coord);
				float noiseStrength = 0.1;

				float maxRadius = sqrt(2.0) * 0.55;
				float currentRadius = 1.0 - uProgress * maxRadius;
				
				float modulatedRadius = currentRadius + n * noiseStrength;
				float progressAlpha = step(distanceFromCenter, modulatedRadius);

				// Combine both alphas
				float finalAlpha = borderAlpha * progressAlpha;

				gl_FragColor = vec4(texture, finalAlpha);
			}
		`

		const textureLoader = new THREE.TextureLoader()
		const placeholderTexture = new THREE.Texture()

		const containerRect = this.getContainerRect()

		const material = new THREE.ShaderMaterial({
			uniforms: {
				// Container-relative positioning
				u_containerSize: {
					value: new THREE.Vector2(containerRect.width, containerRect.height),
				},
				u_domXY: { value: new THREE.Vector2(0, 0) },
				u_domWH: { value: new THREE.Vector2(rect.width, rect.height) },
				u_borderRadius: { value: this.config.borderRadius },

				// ImageMaterial uniforms
				uTexture: { value: placeholderTexture },
				uTextureSize: { value: new THREE.Vector2(1024, 1024) },
				uQuadSize: { value: new THREE.Vector2(rect.width, rect.height) },
				uTime: { value: 0 },
				uSpeed: { value: this.config.speed },
				uProgress: { value: this.config.progress },
			},
			vertexShader,
			fragmentShader,
			transparent: true,
			depthWrite: false,
			depthTest: false,
		})

		// Load texture
		textureLoader.load(
			imageSrc,
			(loadedTexture) => {
				material.uniforms.uTexture.value = loadedTexture
				material.uniforms.uTextureSize.value = new THREE.Vector2(
					loadedTexture.image.width,
					loadedTexture.image.height
				)
				material.needsUpdate = true
			},
			undefined,
			(error) => {
				console.error('Error loading texture:', error)
			}
		)

		return material
	}

	updatePlane() {
		if (!this.enabled || !this.element) {
			this.mesh.visible = false
			return
		}

		const rect = this.element.getBoundingClientRect()

		// Early visibility check with buffer zone BEFORE expensive operations
		const buffer = 200
		const isNearViewport =
			rect.top < window.innerHeight + buffer && rect.bottom > -buffer

		if (!isNearViewport) {
			this.mesh.visible = false
			return
		}

		const containerRect = this.getContainerRect()

		// Calculate position relative to container's top-left corner
		const relativeX = rect.left - containerRect.left
		const relativeY = rect.top - containerRect.top

		// Update uniforms
		this.material.uniforms.u_containerSize.value.set(
			containerRect.width,
			containerRect.height
		)
		this.material.uniforms.u_domXY.value.set(relativeX, relativeY)
		this.material.uniforms.u_domWH.value.set(rect.width, rect.height)
		this.material.uniforms.uQuadSize.value.set(rect.width, rect.height)

		// Final visibility check for container bounds
		this.mesh.visible =
			relativeY < containerRect.height &&
			relativeY + rect.height > 0 &&
			relativeX < containerRect.width &&
			relativeX + rect.width > 0
	}

	setupListeners() {
		this.lenis.on('scroll', () => {
			this.updatePlane()
		})

		window.addEventListener('resize', () => this.onResize())
	}

	onResize() {
		this.updatePlane()
	}

	// Public API methods
	enable() {
		this.enabled = true
		this.updatePlane()
	}

	disable() {
		this.enabled = false
		this.mesh.visible = false
	}

	setElement(element) {
		this.element = element
		this.updatePlane()
	}

	setBorderRadius(borderRadius) {
		this.config.borderRadius = borderRadius
		this.material.uniforms.u_borderRadius.value = borderRadius
	}

	getBorderRadius() {
		return this.config.borderRadius
	}

	updateTime(time) {
		this.material.uniforms.uTime.value = time
	}

	updateSpeed(speed) {
		this.targetSpeed = speed
	}

	updateLerp() {
		const currentSpeed = this.material.uniforms.uSpeed.value
		const difference = Math.abs(currentSpeed - this.targetSpeed)

		if (difference < 0.01) {
			this.material.uniforms.uSpeed.value = this.targetSpeed
			return false
		}

		this.material.uniforms.uSpeed.value = THREE.MathUtils.lerp(
			currentSpeed,
			this.targetSpeed,
			this.lerpFactor
		)

		return true
	}

	updateProgress(progress) {
		this.material.uniforms.uProgress.value = progress
	}

	getMaterial() {
		return this.material
	}

	getQuadSize() {
		return new THREE.Vector2(this.dimensions.width, this.dimensions.height)
	}

	getContainerRect() {
		if (this.container) {
			return this.container.getBoundingClientRect()
		}
		if (this.scene.userData && this.scene.userData.container) {
			return this.scene.userData.container.getBoundingClientRect()
		}

		return {
			left: 0,
			top: 0,
			width: window.innerWidth,
			height: window.innerHeight,
		}
	}

	dispose() {
		this.scene.remove(this.mesh)
		this.geometry.dispose()
		if (this.material.dispose) {
			this.material.dispose()
		}
	}
}

export default OptimisedImagePlane
