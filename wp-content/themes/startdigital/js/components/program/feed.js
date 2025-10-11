import gsap from 'gsap'
import { Observer } from 'gsap/Observer'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { getLenis } from '../../utils/smooth-scroll'

gsap.registerPlugin(Observer, ScrollTrigger)

export default function initFeed(programLoader) {
	const programFeed = document.querySelector('#program-feed')
	if (!programFeed) return

	const lenis = getLenis()
	let currentIndex = 0
	let isAnimating = false
	let isLocked = false
	let observer = null

	function getFeedItems() {
		return document.querySelectorAll('.program-feed-item')
	}

	let feedItems = getFeedItems()

	function positionAllItems() {
		feedItems = getFeedItems()
		feedItems.forEach((item, i) => {
			gsap.set(item, {
				position: 'absolute',
				top: 0,
				left: 0,
				width: '100%',
				yPercent: (i - currentIndex) * 100,
			})
		})
	}

	positionAllItems()

	// Listen for new items being loaded
	window.addEventListener('programsLoaded', positionAllItems)

	ScrollTrigger.create({
		anticipatePin: 1,
		trigger: programFeed,
		start: 'top top+=32px',
		end: 'bottom+=32px bottom',
		onEnter: () => lockScroll(),
		onEnterBack: () => lockScroll(),
		onLeave: () => unlockScroll(),
		onLeaveBack: () => unlockScroll(),
	})

	function lockScroll() {
		if (isLocked) return
		isLocked = true
		if (lenis) lenis.stop()

		observer = Observer.create({
			target: window,
			type: 'wheel,touch',
			preventDefault: true,
			tolerance: 50,
			onChangeY: (self) => {
				if (isAnimating) return
				feedItems = getFeedItems()

				if (self.deltaY > 0) {
					if (currentIndex === feedItems.length - 1) {
						unlockScroll()
						if (lenis)
							lenis.scrollTo(programFeed.offsetTop + programFeed.offsetHeight)
					} else {
						gotoSection(currentIndex + 1)
					}
				} else {
					if (currentIndex === 0) {
						unlockScroll()
						if (lenis)
							lenis.scrollTo(programFeed.offsetTop - window.innerHeight)
					} else {
						gotoSection(currentIndex - 1)
					}
				}
			},
		})
	}

	function unlockScroll() {
		if (!isLocked) return
		isLocked = false
		if (lenis) lenis.start()
		if (observer) {
			observer.kill()
			observer = null
		}
	}

	function gotoSection(index) {
		feedItems = getFeedItems()
		if (index < 0 || index >= feedItems.length) return

		isAnimating = true

		// Trigger load when 3 items from the end
		if (feedItems.length - index === 3 && programLoader) {
			programLoader.loadMore()
		}

		gsap.to(feedItems, {
			yPercent: (i) => (i - index) * 100,
			duration: 0.8,
			ease: 'power2.inOut',
			onComplete: () => {
				currentIndex = index
				isAnimating = false
			},
		})
	}
}
