import gsap from 'gsap'
import { getLenis } from '../../utils/smooth-scroll'

export default function initProgramMenu() {
	const buttons = document.querySelectorAll('[data-program-button]')

	if (!buttons.length > 0) return

	const menu = document.querySelector('[data-program-menu]')

	if (!menu) return

	const lenis = getLenis()

	const formItems = menu.querySelectorAll('form > *')

	const tl = gsap.timeline({
		paused: true,
		defaults: {
			duration: 0.65,
			ease: 'power3.inOut',
		},
		onComplete: () => {
			gsap.set(formItems, { clearProps: 'all' })
		},
	})

	const initialPath = `M100 0 L100 ${window.innerHeight} Q-100 ${
		window.innerHeight / 2
	} 100 0`

	const targetPath = `M100 0 L100 ${window.innerHeight} Q100 ${
		window.innerHeight / 2
	} 100 0`

	tl.to(menu, {
		x: 0,
	})
		.fromTo(
			'.program-svg path',
			{
				attr: { d: initialPath },
			},
			{
				attr: { d: targetPath },
			},
			'<='
		)
		.from(
			formItems,
			{
				xPercent: 100,
				stagger: {
					from: 'center',
					each: 0.01,
				},
				ease: 'power2.out',
			},
			'<=10%'
		)

	let isOpen = false

	const closeMenu = () => {
		if (isOpen) {
			tl.reverse()
			lenis.start()
			isOpen = false
		}
	}

	buttons.forEach((button) => {
		button.addEventListener('click', () => {
			if (isOpen) {
				closeMenu()
			} else {
				tl.play()
				lenis.stop()
				isOpen = true
			}
		})
	})

	document.addEventListener('click', (e) => {
		if (!isOpen) return

		const isClickInsideMenu = menu.contains(e.target)
		const isClickOnButton = Array.from(buttons).some((button) =>
			button.contains(e.target)
		)

		const isClickOnDatepicker = e.target.closest('.air-datepicker')

		if (!isClickInsideMenu && !isClickOnButton && !isClickOnDatepicker) {
			closeMenu()
		}
	})
}
