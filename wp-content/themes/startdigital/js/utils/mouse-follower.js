import gsap from 'gsap'

export default function initMouseFollowers() {
	whatsOnMouseFollower()
}

function whatsOnMouseFollower() {
	const mousefollowerElement = document.querySelector('[data-whats-on-mouse]')
	const mousefollowerInner = mousefollowerElement.querySelector(':scope > div')

	if (!mousefollowerElement) return

	const innerWidth = mousefollowerInner.offsetWidth

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

	tl.to(mousefollowerElement, {
		scale: 1,
		duration: 0.75,
		ease: 'power4.inOut',
	}).fromTo(
		mousefollowerInner,
		{
			width: 0,
			opacity: 0,
		},
		{
			width: innerWidth,
			opacity: 1,
			duration: 0.5,
			ease: 'power2.out',
		},
		'<=75%'
	)

	document.addEventListener(
		'mouseenter',
		(e) => {
			if (e.target.closest('.post-tease')) {
				tl.play()
			}
		},
		true
	)

	document.addEventListener(
		'mouseleave',
		(e) => {
			if (e.target.closest('.post-tease')) {
				tl.reverse()
			}
		},
		true
	)
}
