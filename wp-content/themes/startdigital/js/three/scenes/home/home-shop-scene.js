import * as THREE from 'three'
import gsap from 'gsap'
import BaseScene from '../../base-scene'
import TrackedPlane from '../../utils/tracked-plane'
import OptimisedImagePlane from '../../utils/optimised-image-plane'
import WhiteNoiseMaterial from '../../materials/white-noise-material'
import { LinkSlider } from '../../../components/sliders/LinkSlider'

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
		// Keep TrackedPlane for hero with custom material
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

		// Use OptimisedImagePlane for images
		this.imagePlanes = []

		this.imageContainers.forEach((imageContainer) => {
			const imagePlane = new OptimisedImagePlane(
				this.scene,
				this.camera,
				imageContainer,
				this.container,
				{
					borderRadius: 16,
					speed: 0,
					progress: 0.3,
				}
			)

			this.imagePlanes.push(imagePlane)
		})
	}

	createMouseListeners() {
		this.articleContainers.forEach((articleContainer, index) => {
			const imagePlane = this.imagePlanes[index]

			articleContainer.addEventListener('mouseenter', () => {
				this.updateImageProgress(imagePlane, true)
			})

			articleContainer.addEventListener('mouseleave', () => {
				this.updateImageProgress(imagePlane, false)
			})
		})
	}

	updateImageProgress(imagePlane, isHovered = false) {
		const index = this.imagePlanes.indexOf(imagePlane)

		if (this.hoverAnimations[index]) {
			this.hoverAnimations[index].kill()
		}

		const currentProgress = imagePlane.getMaterial().uniforms.uProgress.value
		const targetProgress = isHovered ? 0.8 : 0.3

		this.hoverAnimations[index] = gsap.to(
			{ value: currentProgress },
			{
				value: targetProgress,
				duration: 0.45,
				ease: 'power2.out',
				onUpdate: function () {
					imagePlane.updateProgress(this.targets()[0].value)
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
		if (!this.imagePlanes) return

		this.speed = this.linkSlider.getCurrentSpeed()
		this.imagePlanes.forEach((plane) => {
			plane.updatePlane()
			plane.updateSpeed(this.speed)
		})
	}

	onResize(width, height) {
		super.onResize(width, height)
		this.heroPlane.updatePlane()
		this.imagePlanes.forEach((plane) => plane.updatePlane())
	}

	animate(deltaTime) {
		this.time += deltaTime
		this.whiteNoiseMaterial.updateTime(this.time)

		this.imagePlanes.forEach((plane) => {
			plane.updateTime(this.time)
			plane.updateLerp()
		})
	}
}

export default HomeShopScene
