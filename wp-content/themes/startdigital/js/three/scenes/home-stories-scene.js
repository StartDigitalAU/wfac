import * as THREE from 'three'
import gsap from 'gsap'
import BaseScene from '../base-scene'
import TrackedPlane from '../utils/tracked-plane'
import { LinkSlider } from '../../components/sliders/LinkSlider'

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
			arrows: {
				prev: document.querySelector('#home-stories [data-prev]'),
				next: document.querySelector('#home-stories [data-next]'),
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
	animate(deltaTime) {
		this.time += deltaTime

		this.imageMaterials.forEach((imageMaterial) => {
			imageMaterial.updateTime(this.time)
			imageMaterial.updateLerp()
		})
	}
}

export default HomeStoriesScene
