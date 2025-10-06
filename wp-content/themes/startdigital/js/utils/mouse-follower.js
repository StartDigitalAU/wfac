import gsap from 'gsap'

export default function initMouseFollowers() {
	whatsOnMouseFollower()
}

function whatsOnMouseFollower() {
	const mousefollowerElement = document.querySelector('[data-whats-on-mouse]')
	const mousefollowerInner = mousefollowerElement.querySelector(':scope > div')
	const mousefollowerText = mousefollowerInner.querySelector('span')

	if (!mousefollowerElement) return

	gsap.set(mousefollowerElement, { xPercent: -50, yPercent: -100, scale: 0 })

	let xTo = gsap.quickTo(mousefollowerElement, 'x', {
			duration: 0.75,
			ease: 'power3',
		}),
		yTo = gsap.quickTo(mousefollowerElement, 'y', {
			duration: 0.75,
			ease: 'power3',
		})

	window.addEventListener('mousemove', (e) => {
		xTo(e.clientX)
		yTo(e.clientY)
	})

	const tl = gsap.timeline({ paused: true })

	tl.fromTo(
		mousefollowerElement,
		{
			scale: 0,
		},
		{
			scale: 1,
			duration: 0.75,
			ease: 'power4.inOut',
		}
	).fromTo(
		mousefollowerInner,
		{
			width: 0,
			opacity: 0,
		},
		{
			width: () => {
				return mousefollowerInner.scrollWidth
			},
			opacity: 1,
			duration: 0.5,
			ease: 'power2.out',
		},
		'<=75%'
	)

	const containers = document.querySelectorAll('.post-tease-container')

	containers.forEach((container) => {
		container.addEventListener('mouseenter', (e) => {
			const mouseText = container.getAttribute('data-mouse')
			if (mouseText) {
				mousefollowerText.textContent = mouseText
			}
			tl.invalidate().play()
		})

		container.addEventListener('mouseleave', (e) => {
			tl.reverse()
		})
	})
}
