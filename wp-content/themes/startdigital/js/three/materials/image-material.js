import * as THREE from 'three'
import uvCoverVert from './shaders/uv-cover-vert'
import simplexNoise from './shaders/simplex-noise'

class ImageMaterial {
	constructor(options = {}) {
		const defaults = {
			uTexture: new THREE.Texture(),
			uTextureSize: new THREE.Vector2(0.0, 0.0),
			uQuadSize: new THREE.Vector2(0.0, 0.0),
			uTime: 0.0,
			uSpeed: 0,
			uProgress: 0.3,
		}

		this.options = { ...defaults, ...options }
		this.material = this.createMaterial()
		this.targetSpeed = this.options.uSpeed
		this.lerpFactor = 0.05
	}

	createMaterial() {
		const vertexShader = /* glsl */ `

			float PI = 3.141592653589793;

			uniform vec2 uTextureSize;
			uniform vec2 uQuadSize;
			uniform float uSpeed;
			varying vec2 vUvCover;
			varying vec2 vUv;
			

			${uvCoverVert}

			vec3 deformationCurve(vec3 position, vec2 uv) {
				float limitedSpeed = clamp(uSpeed* 0.25, -0.45, 0.45); // Clamp between -2 and 2
				
				position.x = position.x - sin(uv.y * PI) * -1.0 * limitedSpeed;
				position.z = position.z - 1.0 * abs(limitedSpeed);
				return position;
			}

			void main() {
				vUvCover = getCoverUvVert(uv, uTextureSize, uQuadSize);
				vUv = uv;
				vec3 deformedPosition = deformationCurve(position, vUvCover);
                gl_Position = projectionMatrix * modelViewMatrix * vec4(deformedPosition, 1.0);
			}
		`

		const fragmentShader = /* glsl */ `	
			uniform sampler2D uTexture;
			uniform float uTime;
			uniform vec2 uQuadSize;
			varying vec2 vUvCover;
			varying vec2 vUv;
			uniform float uProgress;

			${simplexNoise}

			void main() {
				
				vec3 texture = texture2D(uTexture, vUvCover).rgb;

				// Use original UV for square calculation and distance
				vec2 squareUv = vUv;
                float aspect = uQuadSize.x / uQuadSize.y;
                
                if (aspect > 1.0) {
                    squareUv.x = (vUv.x - 0.5) * aspect + 0.5;
                } else {
                    squareUv.y = (vUv.y - 0.5) / aspect + 0.5;
                }

				// Use original UV for distance calculation
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
                
                float alpha = step(distanceFromCenter, modulatedRadius);

				gl_FragColor = vec4(texture, alpha);
			}
		`

		return new THREE.ShaderMaterial({
			uniforms: {
				uTexture: { value: this.options.uTexture },
				uTextureSize: { value: this.options.uTextureSize },
				uQuadSize: { value: this.options.uQuadSize },
				uTime: { value: this.options.uTime },
				uSpeed: { value: this.options.uSpeed },
				uProgress: { value: this.options.uProgress },
			},
			vertexShader,
			fragmentShader,
			transparent: true,
			depthWrite: false,
			depthTest: false,
		})
	}

	getMaterial() {
		return this.material
	}

	updateTime(time) {
		this.material.uniforms.uTime.value = time
	}

	updateSpeed(speed) {
		this.targetSpeed = speed
	}

	updateLerp() {
		this.material.uniforms.uSpeed.value = THREE.MathUtils.lerp(
			this.material.uniforms.uSpeed.value,
			this.targetSpeed,
			this.lerpFactor
		)
	}

	updateProgress(progress) {
		this.material.uniforms.uProgress.value = progress
	}
}

export default ImageMaterial
