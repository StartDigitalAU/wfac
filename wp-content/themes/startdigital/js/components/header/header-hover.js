import gsap from 'gsap'

export default function initHeaderHoverAnimation() {
	const buttons = document.querySelectorAll('.header-animation')

	if (buttons.length === 0) return

	buttons.forEach((button) => {
		const mouseFollower = button.querySelector('.header__bg')
		const circle = button.querySelector('.header__circle')

		if (!mouseFollower) return

		const tl = gsap.timeline({ paused: true })

		tl.to(
			circle,
			{
				height: '130%',
				duration: 0.5,
				ease: 'power1.inOut',
			},
			'<='
		)

		let circleXTo, circleYTo
		if (circle) {
			circleXTo = gsap.quickTo(circle, 'x', {
				duration: 0.6,
				ease: 'power3.out',
			})
			circleYTo = gsap.quickTo(circle, 'y', {
				duration: 0.6,
				ease: 'power3.out',
			})
		}

		button.addEventListener('mouseenter', (e) => {
			const rect = button.getBoundingClientRect()
			const x = e.clientX - rect.left
			const y = e.clientY - rect.top

			if (circle) {
				gsap.set(circle, {
					x: x,
					y: y,
					xPercent: -50,
					yPercent: -50,
				})
			}

			tl.play()
		})

		button.addEventListener('mousemove', (e) => {
			const rect = button.getBoundingClientRect()
			const x = e.clientX - rect.left
			const y = e.clientY - rect.top

			if (circle) {
				circleXTo(x)
				circleYTo(y)
			}
		})

		button.addEventListener('mouseleave', (e) => {
			const rect = button.getBoundingClientRect()
			const x = e.clientX - rect.left
			const y = e.clientY - rect.top

			if (circle) {
				circleXTo(x)
				circleYTo(y)
			}

			tl.reverse()
		})
	})
}
