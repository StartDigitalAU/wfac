import * as THREE from 'three'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import BaseScene from '../base-scene'
import TrackedPlane from '../utils/tracked-plane'
import NoiseMaterial from '../materials/noise-material'

gsap.registerPlugin(ScrollTrigger)

class HomeHeroScene extends BaseScene {
	setupScene() {
		this.heroContainer = document.querySelector('#home-hero')
		this.heroVideo = document.querySelector('#home-hero-video')
		this.heroScrollerContainer = document.querySelector('#home-hero-scroller')
		this.heroSvg = document.querySelector('.hero-svg')
	}

	createMaterials() {
		this.heroMaterial = new NoiseMaterial(this.heroVideo, this.heroSvg)

		const updateVideoRes = () =>
			this.heroMaterial.updateVideoResolution(
				this.heroVideo.videoWidth,
				this.heroVideo.videoHeight
			)

		this.heroVideo.videoWidth
			? updateVideoRes()
			: this.heroVideo.addEventListener('loadedmetadata', updateVideoRes, {
					once: true,
			  })

		this.heroVideo.play()
	}

	createObjects() {
		this.heroPlane = new TrackedPlane(
			this.scene,
			this.camera,
			this.heroContainer,
			this.container,
			{
				borderRadius: 0,
				material: this.heroMaterial.getMaterial(),
			}
		)

		this.updateMaterialResolution()
		// this.updateSvgBounds()
	}

	updateMaterialResolution() {
		const rect = this.heroContainer.getBoundingClientRect()
		this.heroMaterial.updateResolution(rect.width, rect.height)
	}

	updateSvgBounds() {
		const svgRect = this.heroSvg.getBoundingClientRect()
		const canvasRect = this.container.getBoundingClientRect()

		this.heroMaterial.updateSvgBounds(
			svgRect.left - canvasRect.left,
			svgRect.top - canvasRect.top,
			svgRect.width,
			svgRect.height,
			canvasRect.width,
			canvasRect.height
		)
	}

	createScrollTriggers() {
		const that = this
		const tl = gsap.timeline({
			scrollTrigger: {
				trigger: this.heroScrollerContainer,
				pin: this.heroContainer,
				pinSpacing: false,
				start: 'top top',
				end: 'bottom bottom',
				scrub: true,
			},
		})

		tl.to(
			{},
			{
				onUpdate: function () {
					const progress = this.progress()
					that.heroMaterial.updateProgress(progress)
				},
			}
		).to(
			'.hero__bottom-text',
			{
				opacity: 0,
				duration: 0.25,
			},
			'<='
		)

		ScrollTrigger.addEventListener('refreshInit', () => {
			this.updateMaterialResolution()
			this.updateSvgBounds()
		})
	}

	animate(deltaTime) {
		this.time += deltaTime
		this.heroMaterial.updateTime(this.time)
	}
}

export default HomeHeroScene
