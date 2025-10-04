import * as THREE from 'three'
import gsap from 'gsap'
import BaseScene from '../base-scene'
import TrackedPlane from '../utils/tracked-plane'

class HomeOnNowScene extends BaseScene {
	setupScene() {
		this.imageContainers = document.querySelectorAll(
			'#home-on-now .image-container'
		)

		this.articleContainers = document.querySelectorAll('#home-on-now article')
		this.hoverAnimations = []
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

	animate(deltaTime) {
		this.time += deltaTime

		this.imageMaterials.forEach((imageMaterial) =>
			imageMaterial.updateTime(this.time)
		)
	}
}

export default HomeOnNowScene
