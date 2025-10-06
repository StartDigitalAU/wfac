import * as THREE from 'three'
import simplexNoise from './shaders/simplex-noise'
import uvCoverFrag from './shaders/uv-cover-frag'

class NoiseMaterial {
	constructor(videoElement, svgElement) {
		this.videoTexture = null
		this.svgTexture = null

		if (videoElement) {
			this.videoTexture = new THREE.VideoTexture(videoElement)
			this.videoTexture.minFilter = THREE.LinearFilter
			this.videoTexture.magFilter = THREE.LinearFilter
			this.videoTexture.format = THREE.RGBFormat
		}

		if (svgElement) {
			this.createSVGTexture(svgElement)
		}

		this.uniforms = {
			uTime: { value: 0.0 },
			uProgress: { value: 0.0 },
			uResolution: { value: new THREE.Vector2(1, 1) },
			uVideoTexture: { value: this.videoTexture },
			uVideoResolution: { value: new THREE.Vector2(1920, 1080) },
			uSvgTexture: { value: this.svgTexture },
			uSvgBounds: { value: new THREE.Vector4(0, 0, 0, 0) },
		}

		this.material = this.createMaterial()
	}

	createSVGTexture(svgElement) {
		const canvas = document.createElement('canvas')
		const ctx = canvas.getContext('2d')
		const img = new Image()

		// Get SVG data
		const svgData = new XMLSerializer().serializeToString(svgElement)
		const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' })
		const url = URL.createObjectURL(svgBlob)

		img.onload = () => {
			// Calculate canvas dimensions to match SVG aspect ratio
			const maxSize = 4096
			const aspectRatio = img.width / img.height

			if (aspectRatio > 1) {
				// Wider than tall
				canvas.width = maxSize
				canvas.height = maxSize / aspectRatio
			} else {
				// Taller than wide or square
				canvas.width = maxSize * aspectRatio
				canvas.height = maxSize
			}

			ctx.fillStyle = 'black'
			ctx.fillRect(0, 0, canvas.width, canvas.height)

			const padding = 0.0
			const availableWidth = canvas.width * (1 - padding * 2)
			const availableHeight = canvas.height * (1 - padding * 2)

			const scale = Math.min(
				availableWidth / img.width,
				availableHeight / img.height
			)
			const drawWidth = img.width * scale
			const drawHeight = img.height * scale
			const offsetX = (canvas.width - drawWidth) / 2
			const offsetY = (canvas.height - drawHeight) / 2

			ctx.drawImage(img, offsetX, offsetY, drawWidth, drawHeight)

			// Invert and threshold to pure black/white
			const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height)
			const data = imageData.data
			const threshold = 128

			for (let i = 0; i < data.length; i += 4) {
				const inverted = 255 - data[i]
				const value = inverted > threshold ? 255 : 0
				data[i] = value // red
				data[i + 1] = value // green
				data[i + 2] = value // blue
			}
			ctx.putImageData(imageData, 0, 0)

			this.svgTexture = new THREE.CanvasTexture(canvas)
			this.svgTexture.minFilter = THREE.LinearFilter
			this.svgTexture.magFilter = THREE.LinearFilter
			this.svgTexture.generateMipmaps = false
			this.svgTexture.needsUpdate = true
			this.svgTexture.anisotropy = 16
			this.uniforms.uSvgTexture.value = this.svgTexture

			// Store the actual UV bounds of where the SVG was drawn
			// this.uniforms.uSvgBounds.value.set(
			// 	offsetX / canvas.width,
			// 	offsetY / canvas.height,
			// 	drawWidth / canvas.width,
			// 	drawHeight / canvas.height
			// )

			URL.revokeObjectURL(url)
		}

		img.src = url
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
            uniform float uProgress;
            uniform float uTime;
            uniform vec2 uResolution;
            uniform sampler2D uVideoTexture;
            uniform vec2 uVideoResolution;
            uniform sampler2D uSvgTexture;
            uniform vec4 uSvgBounds;

            varying vec2 vUv;

            ${simplexNoise}
            ${uvCoverFrag}

            void main() {
                vec2 videoUv = getCoverUvFrag(vUv, uVideoResolution, uResolution);
                vec4 videoColor = texture2D(uVideoTexture, videoUv);
                
                vec2 svgLocalUv = (vUv - uSvgBounds.xy) / uSvgBounds.zw;
                float inBounds = step(0.0, svgLocalUv.x) * step(svgLocalUv.x, 1.0) * 
                                step(0.0, svgLocalUv.y) * step(svgLocalUv.y, 1.0);
                
                vec4 svgSample = texture2D(uSvgTexture, svgLocalUv);
                float svgMask = (1.0 - svgSample.r) * inBounds;
                
                vec2 squareUv = vUv;
                float aspect = uResolution.x / uResolution.y;
                
                if (aspect > 1.0) {
                    squareUv.x = (vUv.x - 0.5) * aspect + 0.5;
                } else {
                    squareUv.y = (vUv.y - 0.5) / aspect + 0.5;
                }
                
                vec2 centeredUv = vUv - 0.5;
                float distanceFromCenter = length(centeredUv);

                // Add noise
                float scale = 8.0;
                vec3 coord = vec3(squareUv * scale, uTime * 0.3);
                float n = snoise(coord);
                float noiseStrength = 0.1;
				
				if(uProgress < 0.07) {
					noiseStrength = 0.1 * (uProgress/0.07);
				};
                
                // Calculate the base expanding circle
                float maxRadius = sqrt(2.0) * 0.65;
                float currentRadius = uProgress * maxRadius;
                
                float modulatedRadius = currentRadius + n * noiseStrength;
                
                float circleMask = step(distanceFromCenter, modulatedRadius);
                float alpha = 1.0 - max(circleMask, svgMask);
                
                vec3 colour = videoColor.rgb;
				colour = mix(colour, vec3(0.0), (0.25 + 0.75 * uProgress) );
                
                gl_FragColor = vec4(colour, alpha);
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

	updateResolution(width, height) {
		if (this.material?.uniforms?.uResolution) {
			this.material.uniforms.uResolution.value.set(width, height)
		}
	}

	updateVideoResolution(width, height) {
		if (this.material?.uniforms?.uVideoResolution) {
			this.material.uniforms.uVideoResolution.value.set(width, height)
		}
	}

	updateSvgBounds(x, y, width, height, canvasWidth, canvasHeight) {
		if (this.material?.uniforms?.uSvgBounds) {
			// Normalize to 0-1 range
			this.material.uniforms.uSvgBounds.value.set(
				x / canvasWidth,
				y / canvasHeight,
				width / canvasWidth,
				height / canvasHeight
			)
		}
	}
}

export default NoiseMaterial
