import gsap from 'gsap'

export default class HomeGridFilter {
	constructor(scene, options = {}) {
		this.scene = scene
		this.config = { initialFilter: 'featured', ...options }
		this.isAnimating = false
		this.init()
	}

	init() {
		const buttons = document.querySelectorAll('[data-home-filter]')
		const grids = document.querySelectorAll(
			'.post-tease-container[data-home-filter]'
		)

		if (!buttons.length || !grids.length) return

		this.showGrid(this.config.initialFilter, grids)

		buttons.forEach((btn) => {
			btn.addEventListener('click', (e) => {
				e.preventDefault()
				const filter = btn.getAttribute('data-home-filter')
				this.showGrid(filter, grids)
				this.updateButtonStates(filter, buttons)
			})
		})
	}

	showGrid(filter, grids) {
		if (this.isAnimating) return

		const activeGrid = this.findGrid(filter)
		const inactiveGrids = this.getInactiveGrids(grids, filter)

		if (!activeGrid) return

		this.isAnimating = true
		this.animateTransition(activeGrid, inactiveGrids)
	}

	animateTransition(activeGrid, inactiveGrids) {
		const tl = gsap.timeline({
			onComplete: () => {
				this.isAnimating = false
			},
			onUpdate: () => {
				this.scene.trackedPlanes.forEach((plane) => plane.updatePlane())
			},
		})

		tl.to(
			inactiveGrids,
			{
				opacity: 0,
				duration: 0.75,
				ease: 'power4.inOut',
				onComplete: () => {
					inactiveGrids.forEach((grid) => {
						grid.style.display = 'none'
					})
				},
			},
			'<='
		)

		this.addMaterialAnimation(tl, 0.0, 1.4, 'power1.in', '<=')

		tl.set(activeGrid, { display: 'grid', opacity: 0 }).to(activeGrid, {
			opacity: 1,
			duration: 0.75,
			ease: 'power4.inOut',
		})

		this.addMaterialAnimation(tl, 1.4, 0.0, 'power1.out', '<=')
	}

	addMaterialAnimation(timeline, from, to, ease, position) {
		this.scene.imageMaterials.forEach((material) => {
			timeline.to(
				{ value: from },
				{
					value: to,
					duration: 0.75,
					ease: ease,
					onUpdate: function () {
						material.updateProgress(this.targets()[0].value)
					},
				},
				position
			)
		})
	}

	updateButtonStates(activeFilter, buttons) {
		buttons.forEach((btn) => {
			const isActive = btn.getAttribute('data-home-filter') === activeFilter
			btn.disabled = isActive
			btn.classList.toggle('active', isActive)
		})
	}

	getIsAnimating() {
		return this.isAnimating
	}

	findGrid(filter) {
		return document.querySelector(
			`.post-tease-container[data-home-filter="${filter}"]`
		)
	}

	getInactiveGrids(grids, activeFilter) {
		return Array.from(grids).filter(
			(grid) => grid.getAttribute('data-home-filter') !== activeFilter
		)
	}
}
