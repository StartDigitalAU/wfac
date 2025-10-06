import Core from 'smooothy'
import gsap from 'gsap'

export class LinkSlider extends Core {
	constructor(container, config = {}) {
		super(container.querySelector('[data-slider]'), config)
		gsap.ticker.add(this.update.bind(this))

		this.container = container
		this.autoplayInterval = null
		this.isHovered = false
		this.autoScrollSpeed = 0

		this.#handleLinks()
		this.#setupArrows(config.arrows)
		this.#setupAutoplay(config.autoplay)
	}

	update() {
		if (!this.isDragging && !this.isHovered && this.autoScrollSpeed !== 0) {
			this.target += this.autoScrollSpeed
		}
		super.update()
	}

	// Add method to control auto-scroll
	setAutoScrollSpeed(speed) {
		this.autoScrollSpeed = speed
	}

	stopAutoScroll() {
		this.autoScrollSpeed = 0
	}

	#setupArrows(arrowsConfig) {
		if (!arrowsConfig) return

		const { prev, next } = arrowsConfig

		if (prev) {
			const prevButton =
				typeof prev === 'string' ? this.container.querySelector(prev) : prev

			if (prevButton) {
				prevButton.onclick = () => this.goToPrev()
			}
		}

		if (next) {
			const nextButton =
				typeof next === 'string' ? this.container.querySelector(next) : next

			if (nextButton) {
				nextButton.onclick = () => this.goToNext()
			}
		}
	}

	#setupAutoplay(autoplayConfig) {
		if (!autoplayConfig) return

		const { interval = 3000, pauseOnHover = true } =
			typeof autoplayConfig === 'object'
				? autoplayConfig
				: { interval: autoplayConfig }

		// Pause on hover
		if (pauseOnHover) {
			this.container.addEventListener('mouseenter', () => {
				this.isHovered = true
			})

			this.container.addEventListener('mouseleave', () => {
				this.isHovered = false
			})
		}

		// Start autoplay
		this.startAutoplay(interval)
	}

	startAutoplay(interval = 3000) {
		this.stopAutoplay()

		this.autoplayInterval = setInterval(() => {
			if (!this.isDragging && this.isVisible && !this.isHovered) {
				this.goToNext()
			}
		}, interval)
	}

	stopAutoplay() {
		if (this.autoplayInterval) {
			clearInterval(this.autoplayInterval)
			this.autoplayInterval = null
		}
	}

	#handleLinks() {
		;[...this.wrapper.querySelectorAll('a')].forEach((item, i) => {
			let startX = 0
			let startY = 0
			let startTime = 0
			let isDragging = false

			item.style.pointerEvents = 'none'

			const handleMouseDown = (e) => {
				e.preventDefault()
				startX = e.clientX
				startY = e.clientY
				startTime = Date.now()
				isDragging = false
			}

			const handleMouseMove = (e) => {
				if (!startTime) return

				const deltaX = Math.abs(e.clientX - startX)
				const deltaY = Math.abs(e.clientY - startY)

				if (deltaX > 5 || deltaY > 5) {
					isDragging = true
				}
			}

			const handleMouseUp = (e) => {
				const deltaTime = Date.now() - startTime

				if (!isDragging && deltaTime < 200) {
					item.click()
				}

				startTime = 0
				isDragging = false
			}

			const parent = item.parentElement

			parent.addEventListener('mousedown', handleMouseDown)
			parent.addEventListener('mousemove', handleMouseMove)
			parent.addEventListener('mouseup', handleMouseUp)

			parent.style.userSelect = 'none'
		})
	}
}
