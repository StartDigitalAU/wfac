import gsap from 'gsap'
import { MorphSVGPlugin } from 'gsap/MorphSVGPlugin'

gsap.registerPlugin(MorphSVGPlugin)

export default function initMorphSVG() {
	const tl = gsap.timeline()

	tl.to(
		'#svg-a',
		{
			repeat: -1,
			yoyo: true,
			repeatDelay: 1,
			morphSVG: '#svg-funky-a',
			duration: 1.0,
			ease: 'power4.inOut',
		},
		0
	)
	tl.to(
		'#svg-shadow-a',
		{
			repeat: -1,
			yoyo: true,
			repeatDelay: 1,
			morphSVG: '#svg-shadow-funky-a',
			duration: 1.0,
			ease: 'power4.inOut',
		},
		Math.random() * 2
	)

	tl.to(
		'#svg-shadow-w',
		{
			repeat: -1,
			yoyo: true,
			repeatDelay: 1,
			morphSVG: { shape: '#svg-shadow-funky-w' },
			duration: 1.0,
			ease: 'power4.inOut',
		},
		Math.random() * 2
	)
}
