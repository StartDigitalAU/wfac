import gsap from 'gsap'

export default function initButtonAnimation() {
	const buttons = document.querySelectorAll('.button-animation')

	if (buttons.length === 0) return

	buttons.forEach((button) => {
		const mouseFollower = button.querySelector('.button__bg')
		const circle = button.querySelector('.button__circle')

		if (!mouseFollower) return

		const tl = gsap.timeline({ paused: true })

		tl.to(mouseFollower, {
			width: '200%',
			duration: 0.75,
			ease: 'power1.inOut',
		}).to(
			circle,
			{
				height: '150%',
				duration: 0.5,
				ease: 'power1.inOut',
			},
			'<='
		)

		const followerXTo = gsap.quickTo(mouseFollower, 'x', {
			duration: 0.6,
			ease: 'power3.out',
		})
		const followerYTo = gsap.quickTo(mouseFollower, 'y', {
			duration: 0.6,
			ease: 'power3.out',
		})

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

			gsap.set(mouseFollower, {
				x: x,
				y: y,
				xPercent: -50,
				yPercent: -50,
				width: '0%',
			})

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

			followerXTo(x)
			followerYTo(y)

			if (circle) {
				circleXTo(x)
				circleYTo(y)
			}
		})

		button.addEventListener('mouseleave', (e) => {
			const rect = button.getBoundingClientRect()
			const x = e.clientX - rect.left
			const y = e.clientY - rect.top

			followerXTo(x)
			followerYTo(y)

			if (circle) {
				circleXTo(x)
				circleYTo(y)
			}

			tl.reverse()
		})
	})
}
