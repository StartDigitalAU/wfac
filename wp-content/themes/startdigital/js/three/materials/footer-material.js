import * as THREE from 'three'
import simplexNoise from './shaders/simplex-noise'

class FooterMaterial {
	constructor(seed = 0) {
		this.uniforms = {
			uTime: { value: 0.0 },
			uProgress: { value: 0.175 },
			uQuadSize: { value: new THREE.Vector2(1.0, 1.0) },
			uContainerSize: { value: new THREE.Vector2(1.0, 1.0) },
			uContainerCenter: { value: new THREE.Vector2(0.5, 0.5) },
			uColor: { value: new THREE.Vector3(0.992, 0.38, 0.29) },
			uSeed: { value: seed },
		}

		this.material = this.createMaterial()
	}

	createMaterial() {
		const vertexShader = /* glsl */ `
            varying vec2 vUv;
            void main() {
                vUv = uv;
                gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
            }
        `

		const fragmentShader = /* glsl */ `
            uniform float uTime;
            uniform float uProgress;
            uniform vec2 uQuadSize;
            uniform vec2 uContainerSize;
            uniform vec2 uContainerCenter;
            uniform vec3 uColor;
            uniform float uSeed;
            varying vec2 vUv;

            ${simplexNoise}

            void main() {
				// Convert UV to world space coordinates
				vec2 worldPos = (vUv - 0.5) * uQuadSize;
				
				vec2 containerCenterUV = vec2(uContainerCenter.x, 1.0 - uContainerCenter.y);
				vec2 containerWorldCenter = (containerCenterUV - 0.5) * uQuadSize;
				
				vec2 distanceVec = worldPos - containerWorldCenter;
				
				// Make it oval by scaling the distance differently on each axis
				// Adjust these values to control the oval shape
				float ovalScaleX = 1.0; // Horizontal stretch
				float ovalScaleY = 2.0; // Vertical compression (< 1.0 makes it wider, > 1.0 makes it taller)
				
				vec2 scaledDistance = distanceVec * vec2(ovalScaleX, ovalScaleY);
				float distanceFromCenter = length(scaledDistance);
				
				// Add noise with seed offset
				vec2 squareUv = vUv;
				float aspect = uQuadSize.x / uQuadSize.y;
				
				if (aspect > 1.0) {
					squareUv.x = (vUv.x - 0.5) * aspect + 0.5;
				} else {
					squareUv.y = (vUv.y - 0.5) / aspect + 0.5;
				}
				
				float scale = 3.0;
				vec3 coord = vec3(squareUv * scale + uSeed * 100.0, uTime * 0.1 + uSeed * 50.0);
				float n = snoise(coord);
				float noiseStrength = 0.15;
				
				// Max radius is half the smaller dimension of the container
				float maxRadius = min(uContainerSize.x, uContainerSize.y) * 0.5;
				float currentRadius = uProgress * maxRadius;
				float modulatedRadius = currentRadius + n * noiseStrength;
				
				// Alpha based on distance from container center
				float alpha = step(distanceFromCenter, modulatedRadius);
				alpha = 1.0 - alpha;
				gl_FragColor = vec4(uColor, alpha);
			}
        `
		return new THREE.ShaderMaterial({
			uniforms: this.uniforms,
			vertexShader,
			fragmentShader,
			transparent: true,
		})
	}

	getMaterial() {
		return this.material
	}

	updateTime(time) {
		if (this.material?.uniforms?.uTime) {
			this.material.uniforms.uTime.value = time
		}
	}

	updateProgress(progress) {
		this.material.uniforms.uProgress.value = progress
	}

	setQuadSize(width, height) {
		this.uniforms.uQuadSize.value.set(width, height)
	}

	// New method to set container dimensions and position
	setContainerBounds(containerWidth, containerHeight, centerX, centerY) {
		this.uniforms.uContainerSize.value.set(containerWidth, containerHeight)
		this.uniforms.uContainerCenter.value.set(centerX, centerY)
	}

	// Helper method to automatically get container bounds from DOM element
	setContainerFromElement(element, trackedPlane) {
		if (!element) {
			console.warn('Element not found')
			return
		}

		const textContainer = element.querySelector('#footer-text-container')
		if (!textContainer) {
			console.warn('#footer-text-container not found')
			return
		}

		const footerRect = element.getBoundingClientRect()
		const textRect = textContainer.getBoundingClientRect()

		// Get plane dimensions using TrackedPlane's method
		const quadSize = trackedPlane.getQuadSize()
		const planeWidth = quadSize.x
		const planeHeight = quadSize.y

		// Calculate text container position relative to footer (0-1 range)
		const relativeLeft = (textRect.left - footerRect.left) / footerRect.width
		const relativeTop = (textRect.top - footerRect.top) / footerRect.height
		const relativeWidth = textRect.width / footerRect.width
		const relativeHeight = textRect.height / footerRect.height

		// Calculate center position in UV coordinates
		const centerX = relativeLeft + relativeWidth / 2
		const centerY = relativeTop + relativeHeight / 2

		// Convert to plane coordinates
		const containerWidthInPlane = relativeWidth * planeWidth
		const containerHeightInPlane = relativeHeight * planeHeight

		this.setContainerBounds(
			containerWidthInPlane,
			containerHeightInPlane,
			centerX,
			centerY
		)
	}

	setColor(color) {
		this.uniforms.uColor.value = color
	}

	setSeed(seed) {
		this.uniforms.uSeed.value = seed
	}
}

export default FooterMaterial
