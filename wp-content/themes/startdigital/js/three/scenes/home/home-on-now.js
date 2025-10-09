import gsap from 'gsap'
import BaseScene from '../../base-scene'
import TrackedPlane from '../../utils/tracked-plane'
import HomeGridFilter from '../../../components/filters/home-grid-filter'

class HomeOnNowScene extends BaseScene {
	setupScene() {
		const visibleGrids = document.querySelectorAll(
			'#home-on-now .post-tease-container'
		)

		this.imageContainers = []
		this.articleContainers = []

		visibleGrids.forEach((grid) => {
			const gridImages = grid.querySelectorAll('.image-container')
			const gridArticles = grid.querySelectorAll('article')

			this.imageContainers.push(...gridImages)
			this.articleContainers.push(...gridArticles)
		})

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

		// To filter the home screen
		this.homeGridFilter = new HomeGridFilter(this)
	}

	createMouseListeners() {
		this.articleContainers.forEach((articleContainer, index) => {
			const imageMaterial = this.imageMaterials[index]

			articleContainer.addEventListener('mouseenter', () => {
				// Dont allow hover animations if the grid is filtering
				if (this.homeGridFilter.getIsAnimating()) return

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

	onResize(width, height) {
		super.onResize(width, height)
		this.trackedPlanes.forEach((plane) => plane.updatePlane())
	}

	animate(deltaTime) {
		this.time += deltaTime

		this.imageMaterials.forEach((imageMaterial) =>
			imageMaterial.updateTime(this.time)
		)
	}
}

export default HomeOnNowScene
