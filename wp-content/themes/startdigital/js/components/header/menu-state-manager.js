import gsap from 'gsap'
import { getLenis } from '../../utils/smooth-scroll'
import { animateFor } from '../../utils/animate-for'

class MenuStateManager {
	constructor() {
		this.currentState = 'closed' // 'closed', 'menu', 'search'
		this.lenis = getLenis()
		this.timelines = {}
		this.elements = {}
		this.isAnimating = false
		this.init()
	}

	init() {
		this.elements = {
			headerInnerContainer: document.querySelector('[data-header-inner]'),
			menuInnerContainer: document.querySelector('[data-menu-inner]'),
			searchInnerContainer: document.querySelector('[data-search-inner]'),
			menuBg: document.querySelector('[data-menu-bg]'),
			searchBg: document.querySelector('[data-search-bg]'),
			menuButtons: document.querySelectorAll('[data-toggle-menu]'),
			searchButtons: document.querySelectorAll('[data-toggle-search]'),
			accordionHeaders: document.querySelectorAll('.menu-header'),
		}

		this.createTimelines()
		this.bindEvents()
	}

	createTimelines() {
		if (this.elements.menuInnerContainer) {
			this.timelines.menu = this.createMenuTimeline()
		}

		if (this.elements.searchInnerContainer) {
			this.timelines.search = this.createSearchTimeline()
		}
	}

	closeAccordions() {
		this.elements.accordionHeaders.forEach((header) => {
			header.classList.remove('active')
			const accordionContent = header.nextElementSibling
			accordionContent.style.maxHeight = '0'
			const icon = header.querySelector('.menu-icon')
			if (icon) icon.style.transform = 'rotate(0deg)'
			console.log('fired')
		})

		animateFor(300, () => {
			const { width, height, top, left } =
				this.elements.menuInnerContainer.getBoundingClientRect()
			this.elements.menuBg.style.width = width + 'px'
			this.elements.menuBg.style.height = height + 'px'
		})
	}

	createMenuTimeline() {
		const { width, height, top, left } =
			this.elements.menuInnerContainer.getBoundingClientRect()
		const { top: headerTop, left: headerLeft } =
			this.elements.headerInnerContainer.getBoundingClientRect()

		const tl = gsap.timeline({
			paused: true,
		})
		const targetTop = top - headerTop
		const targetLeft = left - headerLeft

		tl.to(this.elements.menuBg, {
			width,
			height,
			top: targetTop,
			left: targetLeft,
			ease: 'power4.inOut',
			duration: 0.75,
		})
			.to(
				'[data-menu-bg], [data-search-bg], .header__bg, .header__circle',
				{
					backgroundColor: '#3C0C0B',
					ease: 'power4.inOut',
					duration: 0.75,
				},
				'<='
			)
			.to(
				'header .bg-wfac-dark-red',
				{
					backgroundColor: '#F0F0EE',
					ease: 'power4.inOut',
					duration: 0.75,
				},
				'<='
			)
			.to(
				'header .text-wfac-dark-red',
				{
					color: '#F0F0EE',
					ease: 'power4.inOut',
					duration: 0.65,
				},
				'<='
			)
			.to(
				'.menu-line:first-of-type',
				{
					rotate: 45,
					y: 7.5,
					ease: 'power2.inOut',
					duration: 0.55,
				},
				'<='
			)
			.to(
				'.menu-line:nth-of-type(2)',
				{
					opacity: 0,
					ease: 'power2.inOut',
					duration: 0.55,
				},
				'<='
			)
			.to(
				'.menu-line:last-of-type',
				{
					rotate: -45,
					y: -7.5,
					ease: 'power2.inOut',
					duration: 0.55,
				},
				'<='
			)
			.set(
				'[data-menu-inner]',
				{
					opacity: 1,
					pointerEvents: 'auto',
				},
				'<='
			)
			.fromTo(
				'.menu-top .menu-animate',
				{
					opacity: 0,
					yPercent: 100,
				},
				{
					opacity: 1,
					yPercent: 0,
					stagger: 0.075,
					ease: 'power4.out',
					duration: 0.55,
				},
				'<=50%'
			)
			.fromTo(
				'.menu-below .menu-animate',
				{
					opacity: 0,
					yPercent: 100,
				},
				{
					opacity: 1,
					yPercent: 0,
					stagger: 0.01,
					ease: 'power2.out',
					duration: 0.5,
					onReverseComplete: () => this.closeAccordions(),
				},
				'<=65%'
			)

		return tl
	}

	createSearchTimeline() {
		const { width, height, top, left } =
			this.elements.searchInnerContainer.getBoundingClientRect()
		const { top: headerTop, left: headerLeft } =
			this.elements.headerInnerContainer.getBoundingClientRect()

		const tl = gsap.timeline({ paused: true })
		const targetTop = top - headerTop
		const targetLeft = left - headerLeft

		tl.to(this.elements.searchBg, {
			width,
			height,
			top: targetTop,
			left: targetLeft,
			ease: 'power4.inOut',
			duration: 0.75,
		})
			.to(
				'[data-menu-bg], [data-search-bg], .header__bg, .header__circle',
				{
					backgroundColor: '#3C0C0B',
					ease: 'power4.inOut',
					duration: 0.75,
				},
				'<='
			)
			.to(
				'header .bg-wfac-dark-red',
				{
					backgroundColor: '#F0F0EE',
					ease: 'power4.inOut',
					duration: 0.75,
				},
				'<='
			)
			.to(
				'header .text-wfac-dark-red',
				{
					color: '#F0F0EE',
					ease: 'power4.inOut',
					duration: 0.65,
				},
				'<='
			)
			.set(
				'[data-search-inner]',
				{
					opacity: 1,
					pointerEvents: 'auto',
				},
				'<='
			)
			.fromTo(
				'[data-search-animate]',
				{
					opacity: 0,
					yPercent: 100,
				},
				{
					opacity: 1,
					yPercent: 0,
					stagger: 0.125,
					ease: 'power4.out',
					duration: 0.55,
				},
				'<=50%'
			)

		return tl
	}

	async transitionTo(newState) {
		// Prevent multiple animations at once
		if (this.isAnimating) return

		// If clicking the same toggle, close it
		if (this.currentState === newState) {
			newState = 'closed'
		}

		this.isAnimating = true

		// If transitioning from one open state to another
		if (this.currentState !== 'closed' && newState !== 'closed') {
			await this.closeCurrentState()
			await this.openState(newState)
		} else if (newState === 'closed') {
			// Closing current state
			await this.closeCurrentState()
		} else {
			// Opening from closed state
			await this.openState(newState)
		}

		this.currentState = newState
		this.updateScrollState()
		this.isAnimating = false
	}

	closeCurrentState() {
		return new Promise((resolve) => {
			if (this.timelines[this.currentState]) {
				const tl = this.timelines[this.currentState]
				tl.eventCallback('onReverseComplete', () => {
					tl.eventCallback('onReverseComplete', null)
					resolve()
				})
				tl.reverse()
			} else {
				resolve()
			}
		})
	}

	openState(state) {
		return new Promise((resolve) => {
			if (this.timelines[state]) {
				const tl = this.timelines[state]
				tl.eventCallback('onComplete', () => {
					tl.eventCallback('onComplete', null)
					resolve()
				})
				tl.play()
			} else {
				resolve()
			}
		})
	}

	updateScrollState() {
		if (this.currentState === 'closed') {
			this.lenis.start()
		} else {
			this.lenis.stop()
		}
	}

	bindEvents() {
		// Menu button events
		this.elements.menuButtons.forEach((btn) => {
			btn.addEventListener('click', () => {
				this.transitionTo('menu')
			})
		})

		// Search button events
		this.elements.searchButtons.forEach((btn) => {
			btn.addEventListener('click', () => {
				this.transitionTo('search')
			})
		})

		// Close when clicking outside
		document.addEventListener('click', (e) => {
			if (this.currentState === 'closed') return

			const isClickInsideMenu = this.elements.menuInnerContainer?.contains(
				e.target
			)
			const isClickInsideSearch = this.elements.searchInnerContainer?.contains(
				e.target
			)
			const isClickOnMenuButton = Array.from(this.elements.menuButtons).some(
				(btn) => btn.contains(e.target)
			)
			const isClickOnSearchButton = Array.from(
				this.elements.searchButtons
			).some((btn) => btn.contains(e.target))

			if (
				!isClickInsideMenu &&
				!isClickInsideSearch &&
				!isClickOnMenuButton &&
				!isClickOnSearchButton
			) {
				this.transitionTo('closed')
			}
		})
	}
}

export default MenuStateManager
