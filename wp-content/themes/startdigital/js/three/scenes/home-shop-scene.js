import * as THREE from 'three'
import gsap from 'gsap'
import BaseScene from '../base-scene'
import TrackedPlane from '../utils/tracked-plane'
import WhiteNoiseMaterial from '../materials/white-noise-material'
import { LinkSlider } from '../../components/sliders/LinkSlider'

class HomeShopScene extends BaseScene {
	setupScene() {
		this.speed = 0
		this.heroContainer = document.querySelector('#home-shop')
		this.imageContainers = document.querySelectorAll(
			'#home-shop .image-container'
		)
		this.articleContainers = document.querySelectorAll('#home-shop article')
		this.sliderContainer = document.querySelector(
			'#home-shop [data-link-slider]'
		)
		this.hoverAnimations = []
		this.createSlider()
	}

	createMaterials() {
		this.whiteNoiseMaterial = new WhiteNoiseMaterial(1)
	}

	createObjects() {
		this.heroPlane = new TrackedPlane(
			this.scene,
			this.camera,
			this.heroContainer,
			this.container,
			{
				material: this.whiteNoiseMaterial.getMaterial(),
			}
		)
		const quadSize = this.heroPlane.getQuadSize()
		this.whiteNoiseMaterial.setQuadSize(quadSize.x, quadSize.y)
		this.whiteNoiseMaterial.setColor(new THREE.Vector3(0.306, 0.137, 0.133))

		this.trackedPlanes = []
		this.imageMaterials = []

		this.imageContainers.forEach((imageContainer) => {
			const imagePlane = new TrackedPlane(
				this.scene,
				this.camera,
				imageContainer,
				this.container
			)

			this.trackedPlanes.push(imagePlane)
			this.imageMaterials.push(imagePlane.getImageMaterial())
		})
	}

	createMouseListeners() {
		this.articleContainers.forEach((articleContainer, index) => {
			const imageMaterial = this.imageMaterials[index]

			articleContainer.addEventListener('mouseenter', () => {
				this.updateImageProgress(imageMaterial, true)
			})

			articleContainer.addEventListener('mouseleave', () => {
				this.updateImageProgress(imageMaterial, false)
			})
		})
	}

	updateImageProgress(imageMaterial, isHovered = false) {
		const index = this.imageMaterials.indexOf(imageMaterial)

		if (this.hoverAnimations[index]) {
			this.hoverAnimations[index].kill()
		}

		const currentProgress = imageMaterial.getMaterial().uniforms.uProgress.value
		const targetProgress = isHovered ? 0.8 : 0.3

		this.hoverAnimations[index] = gsap.to(
			{ value: currentProgress },
			{
				value: targetProgress,
				duration: 0.45,
				ease: 'power2.out',
				onUpdate: function () {
					imageMaterial.updateProgress(this.targets()[0].value)
				},
			}
		)
	}

	createSlider() {
		if (!this.sliderContainer) return

		const config = {
			onUpdate: () => this.updatePlanes(),
			autoplay: {
				interval: 6000,
				pauseOnHover: true,
			},
		}

		this.linkSlider = new LinkSlider(this.sliderContainer, config)
	}

	updatePlanes() {
		if (!this.trackedPlanes || !this.imageMaterials) return

		this.speed = this.linkSlider.getCurrentSpeed()
		this.trackedPlanes.forEach((plane, i) => {
			plane.updatePlane()
			this.imageMaterials[i].updateSpeed(this.speed)
		})
	}

	onResize(width, height) {
		super.onResize(width, height)
		this.trackedPlanes.forEach((plane) => plane.updatePlane())
	}

	animate(deltaTime) {
		this.time += deltaTime
		this.whiteNoiseMaterial.updateTime(this.time)

		this.imageMaterials.forEach((imageMaterial) => {
			imageMaterial.updateTime(this.time)
			imageMaterial.updateLerp()
		})
	}
}

export default HomeShopScene
