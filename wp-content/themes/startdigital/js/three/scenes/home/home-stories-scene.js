import * as THREE from 'three'
import gsap from 'gsap'
import BaseScene from '../../base-scene'
import OptimisedImagePlane from '../../utils/optimised-image-plane'
import { LinkSlider } from '../../../components/sliders/LinkSlider'

class HomeStoriesScene extends BaseScene {
	setupScene() {
		this.speed = 0
		this.imageContainers = document.querySelectorAll(
			'#home-stories .image-container'
		)
		this.articleContainers = document.querySelectorAll('#home-stories article')
		this.sliderContainer = document.querySelector(
			'#home-stories [data-link-slider]'
		)
		this.hoverAnimations = []
		this.createSlider()
	}

	createObjects() {
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
			arrows: {
				prev: document.querySelector('#home-stories [data-prev]'),
				next: document.querySelector('#home-stories [data-next]'),
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
		this.imagePlanes.forEach((plane) => plane.updatePlane())
	}

	animate(deltaTime) {
		this.time += deltaTime

		this.imagePlanes.forEach((plane) => {
			plane.updateTime(this.time)
			plane.updateLerp()
		})
	}
}

export default HomeStoriesScene
