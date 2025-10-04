import * as THREE from 'three'
import simplexNoise from './shaders/simplex-noise'

class WhiteNoiseMaterial {
	constructor(seed = 0) {
		this.uniforms = {
			uTime: { value: 0.0 },
			uProgress: { value: 0.75 },
			uQuadSize: { value: new THREE.Vector2(1.0, 1.0) },
			uColor: { value: new THREE.Vector3(0.941, 0.941, 0.933) },
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
            uniform vec3 uColor;
            uniform float uSeed;
            varying vec2 vUv;

            ${simplexNoise}

            void main() {
                vec2 squareUv = vUv;
                float aspect = uQuadSize.x / uQuadSize.y;
                
                if (aspect > 1.0) {
                    squareUv.x = (vUv.x - 0.5) * aspect + 0.5;
                } else {
                    squareUv.y = (vUv.y - 0.5) / aspect + 0.5;
                }

                vec2 centeredUv = vUv - 0.5;
                float distanceFromCenter = length(centeredUv);

                // Add noise with seed offset
                float scale = 1.1;
                vec3 coord = vec3(squareUv * scale + uSeed * 100.0, uTime * 0.05 + uSeed * 50.0);
                float n = snoise(coord);
                float noiseStrength = 0.9;

                float maxRadius = sqrt(2.0) * 0.5;
                float currentRadius = uProgress * maxRadius;
                
                float modulatedRadius = currentRadius + n * noiseStrength;
                
                float alpha = step(distanceFromCenter, modulatedRadius);

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

	setColor(color) {
		this.uniforms.uColor.value = color
	}

	setSeed(seed) {
		this.uniforms.uSeed.value = seed
	}
}

export default WhiteNoiseMaterial
