import gsap from 'gsap'
import BaseScene from '../../base-scene'
import OptimisedImagePlane from '../../utils/optimised-image-plane'
import { getLenis } from '../../../utils/smooth-scroll'

class ProgramScene extends BaseScene {
	constructor(id, container) {
		super(id, container)
		window.programScene = this
		this.planeMap = new Map() // O(1) lookups
		this.planeIdCounter = 0
	}

	setupScene() {
		const visibleGrids = document.querySelectorAll('.program-grid')

		this.lenis = getLenis()
		this.imageContainers = []
		this.articleContainers = []

		visibleGrids.forEach((grid) => {
			const gridImages = grid.querySelectorAll('.image-container')
			const gridArticles = grid.querySelectorAll('article')

			this.imageContainers.push(...gridImages)
			this.articleContainers.push(...gridArticles)
		})

		this.hoverAnimations = new Map()
	}

	createObjects() {
		this.imagePlanes = []
		this.createImagePlanes(this.imageContainers)
	}

	createImagePlanes(imageContainers, options = {}) {
		const defaultOptions = {
			borderRadius: 16,
			speed: 0,
			progress: 0.3,
			...options,
		}

		const newPlanes = []

		imageContainers.forEach((imageContainer) => {
			// Generate unique ID
			const planeId = `plane-${this.planeIdCounter++}`
			imageContainer.setAttribute('data-webgl-initialized', 'true')
			imageContainer.setAttribute('data-plane-id', planeId)

			const imagePlane = new OptimisedImagePlane(
				this.scene,
				this.camera,
				imageContainer,
				this.container,
				defaultOptions
			)

			imagePlane.planeId = planeId
			newPlanes.push(imagePlane)
			this.imagePlanes.push(imagePlane)
			this.planeMap.set(planeId, imagePlane)
		})

		return newPlanes
	}

	createMouseListeners() {
		// Get parent container once and cache it
		this.parentContainer =
			document.getElementById('programs-container') || this.container

		// Single delegated listener for mouseenter
		this.handleMouseEnter = (e) => {
			const article = e.target.closest('article')
			if (!article) return

			const imageContainer = article.querySelector(
				'.image-container[data-plane-id]'
			)
			if (!imageContainer) return

			const planeId = imageContainer.getAttribute('data-plane-id')
			const imagePlane = this.planeMap.get(planeId)

			if (imagePlane) {
				this.updateImageProgress(imagePlane, true)
			}
		}

		// Single delegated listener for mouseleave
		this.handleMouseLeave = (e) => {
			const article = e.target.closest('article')
			if (!article) return

			const imageContainer = article.querySelector(
				'.image-container[data-plane-id]'
			)
			if (!imageContainer) return

			const planeId = imageContainer.getAttribute('data-plane-id')
			const imagePlane = this.planeMap.get(planeId)

			if (imagePlane) {
				this.updateImageProgress(imagePlane, false)
			}
		}

		// Attach listeners (capture phase for mouseenter/leave)
		this.parentContainer.addEventListener(
			'mouseenter',
			this.handleMouseEnter,
			true
		)
		this.parentContainer.addEventListener(
			'mouseleave',
			this.handleMouseLeave,
			true
		)
	}

	updateImageProgress(imagePlane, isHovered = false) {
		const planeId = imagePlane.planeId

		// Kill existing animation if any
		const existingAnimation = this.hoverAnimations.get(planeId)
		if (existingAnimation) {
			existingAnimation.kill()
		}

		const currentProgress = imagePlane.getMaterial().uniforms.uProgress.value
		const targetProgress = isHovered ? 0.8 : 0.3

		const animation = gsap.to(
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

		this.hoverAnimations.set(planeId, animation)
	}

	onResize(width, height) {
		super.onResize(width, height)
		this.imagePlanes.forEach((plane) => plane.updatePlane())
	}

	animate(deltaTime) {
		this.time += deltaTime

		this.imagePlanes.forEach((plane) => {
			plane.updateTime(this.time)
			if (this.lenis.isStopped) plane.updatePlane()
		})
	}

	dispose() {
		if (this.parentContainer) {
			this.parentContainer.removeEventListener(
				'mouseenter',
				this.handleMouseEnter,
				true
			)
			this.parentContainer.removeEventListener(
				'mouseleave',
				this.handleMouseLeave,
				true
			)
		}

		this.hoverAnimations.forEach((anim) => anim.kill())
		this.hoverAnimations.clear()
		this.planeMap.clear()

		super.dispose?.()
	}
}

export default ProgramScene
