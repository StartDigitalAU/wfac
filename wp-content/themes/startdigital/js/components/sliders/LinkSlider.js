import Core from 'smooothy'
import gsap from 'gsap'
import { damp } from 'smooothy'

export class LinkSlider extends Core {
	constructor(container, config = {}) {
		super(container.querySelector('[data-slider]'), config)
		gsap.ticker.add(this.update.bind(this))

		this.container = container
		this.autoplayInterval = null
		this.autoplayIntervalDuration = null
		this.isHovered = false
		this.autoScrollSpeed = 0

		this.lagElements = this.items
			.map((item) => item.querySelector('[data-lag]') || item.firstElementChild)
			.filter(Boolean)

		this.lagPositions = new Array(this.lagElements.length).fill(0)

		this.lagConfig = {
			speedMultiplier: 120,
			dampingFactor: 5.0,
			...config.lag,
		}

		this.#handleLinks()
		this.#setupArrows(config.arrows)
		this.#setupAutoplay(config.autoplay)
		this.#setupDragReset()
	}

	update() {
		if (!this.isDragging && !this.isHovered && this.autoScrollSpeed !== 0) {
			this.target += this.autoScrollSpeed
		}

		super.update()
		// this.#applyLagEffect()
	}

	#applyLagEffect() {
		if (!this.parallaxValues || !this.lagElements.length) return

		this.lagElements.forEach((element, i) => {
			if (!element || !this.parallaxValues) return

			const parallaxValue = this.parallaxValues[i] || 0

			const targetOffset =
				Math.abs(this.speed) * this.lagConfig.speedMultiplier * parallaxValue

			this.lagPositions[i] = damp(
				this.lagPositions[i],
				targetOffset,
				this.lagConfig.dampingFactor,
				this.deltaTime
			)
			element.style.transform = `translateX(${this.lagPositions[i]}px)`
		})
	}

	#getVisibleItemsCount() {
		if (!this.wrapper || !this.items.length) return 1

		const wrapperWidth = window.innerWidth
		let visibleWidth = 0
		let count = 0

		// Calculate how many items fit in the wrapper width
		for (let i = 0; i < this.items.length; i++) {
			const itemWidth = this.items[i].offsetWidth
			visibleWidth += itemWidth

			if (visibleWidth <= wrapperWidth) {
				count++
			} else {
				break
			}
		}

		return Math.max(1, count)
	}

	setAutoScrollSpeed(speed) {
		this.autoScrollSpeed = speed
	}

	stopAutoScroll() {
		this.autoScrollSpeed = 0
	}

	getCurrentSpeed() {
		return this.speed || 0
	}

	getAbsoluteSpeed() {
		return Math.abs(this.speed || 0)
	}

	getSpeedDirection() {
		const speed = this.speed || 0
		return speed > 0 ? 1 : speed < 0 ? -1 : 0
	}

	#setupArrows(arrowsConfig) {
		if (!arrowsConfig) return

		const { prev, next } = arrowsConfig

		if (prev) {
			const prevButton =
				typeof prev === 'string' ? this.container.querySelector(prev) : prev

			if (prevButton) {
				prevButton.onclick = () => {
					this.goToPrev()
					this.resetAutoplay()
				}
			}
		}

		if (next) {
			const nextButton =
				typeof next === 'string' ? this.container.querySelector(next) : next

			if (nextButton) {
				nextButton.onclick = () => {
					this.goToNext()
					this.resetAutoplay()
				}
			}
		}
	}

	#setupDragReset() {
		// Reset autoplay when user starts dragging
		const originalOnPointerDown = this.onPointerDown?.bind(this)
		if (originalOnPointerDown) {
			this.onPointerDown = (...args) => {
				originalOnPointerDown(...args)
				this.resetAutoplay()
			}
		}

		// Alternative: listen for drag start on the wrapper
		if (this.wrapper) {
			this.wrapper.addEventListener('mousedown', () => {
				this.resetAutoplay()
			})
			this.wrapper.addEventListener('touchstart', () => {
				this.resetAutoplay()
			})
		}
	}

	#setupAutoplay(autoplayConfig) {
		if (!autoplayConfig) return

		const { interval = 3000, pauseOnHover = true } =
			typeof autoplayConfig === 'object'
				? autoplayConfig
				: { interval: autoplayConfig }

		this.autoplayIntervalDuration = interval

		// Pause on hover
		if (pauseOnHover) {
			this.container.addEventListener('mouseenter', () => {
				this.isHovered = true
			})

			this.container.addEventListener('mouseleave', () => {
				this.isHovered = false
			})
		}

		this.startAutoplay(interval)
	}

	stopAutoplay() {
		if (this.autoplayInterval) {
			clearInterval(this.autoplayInterval)
			this.autoplayInterval = null
		}
	}

	resetAutoplay() {
		if (this.autoplayIntervalDuration) {
			this.startAutoplay(this.autoplayIntervalDuration)
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

	goToNext() {
		if (this.isAnimating) return

		this.isAnimating = true
		const visibleCount = this.#getVisibleItemsCount()
		const nextTarget = this.config.infinite
			? Math.round(this.target - visibleCount)
			: Math.max(this.maxScroll, Math.round(this.target - visibleCount))

		const distance = nextTarget - this.target

		gsap.to(this, {
			target: nextTarget,
			duration: 0.35,
			ease: 'power1.inOut',
			onUpdate: () => {
				const deltaTarget = this.target - (this._prevTarget || this.target)
				this.speed = deltaTarget * 10
				this._prevTarget = this.target
			},
			onComplete: () => {
				this.isAnimating = false
				this._prevTarget = undefined
			},
		})
	}

	goToPrev() {
		if (this.isAnimating) return

		this.isAnimating = true
		const visibleCount = this.#getVisibleItemsCount()
		const prevTarget = this.config.infinite
			? Math.round(this.target + visibleCount)
			: Math.min(0, Math.round(this.target + visibleCount))

		const distance = prevTarget - this.target

		gsap.to(this, {
			target: prevTarget,
			duration: 0.35,
			ease: 'power1.inOut',
			onUpdate: () => {
				const deltaTarget = this.target - (this._prevTarget || this.target)
				this.speed = deltaTarget * 10
				this._prevTarget = this.target
			},
			onComplete: () => {
				this.isAnimating = false
				this._prevTarget = undefined
			},
		})
	}

	startAutoplay(interval = 3000) {
		this.stopAutoplay()

		this.autoplayInterval = setInterval(() => {
			if (
				!this.isDragging &&
				this.isVisible &&
				!this.isHovered &&
				!this.isAnimating
			) {
				this.goToNext()
			}
		}, interval)
	}
}
