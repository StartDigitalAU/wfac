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
			uProgress: 0.3,
		}

		this.options = { ...defaults, ...options }
		this.material = this.createMaterial()
	}

	createMaterial() {
		const vertexShader = /* glsl */ `
			uniform vec2 uTextureSize;
			uniform vec2 uQuadSize;
			varying vec2 vUvCover;
			varying vec2 vUv;
			

			${uvCoverVert}

			void main() {
				vUvCover = getCoverUvVert(uv, uTextureSize, uQuadSize);
				vUv = uv;
				gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
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
				uProgress: { value: this.options.uProgress },
			},
			vertexShader,
			fragmentShader,
			transparent: true,
			depthWrite: false,
		})
	}

	getMaterial() {
		return this.material
	}

	updateTime(time) {
		this.material.uniforms.uTime.value = time
	}

	updateProgress(progress) {
		this.material.uniforms.uProgress.value = progress
	}
}

export default ImageMaterial
