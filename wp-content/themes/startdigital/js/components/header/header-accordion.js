import { animateFor } from '../../utils/animate-for'

export default function initMenuAccordions() {
	const accordionHeaders = document.querySelectorAll('.menu-header')
	const menuInnerContainer = document.querySelector('[data-menu-inner]')
	const menuBg = document.querySelector('[data-menu-bg]')
	if (!accordionHeaders) {
		return
	}

	accordionHeaders.forEach((header) => {
		const accordionContent = header.nextElementSibling

		const isMobile = window.innerWidth < 1024

		if (isMobile) {
			const isActive = header.classList.contains('active')
			accordionContent.style.maxHeight = isActive
				? accordionContent.scrollHeight + 'px'
				: '0'
		} else {
			accordionContent.style.maxHeight = 'none'
			header.classList.remove('active')
		}

		header.addEventListener('click', () => {
			if (window.innerWidth >= 1024) return

			header.classList.toggle('active')
			const icon = header.querySelector('.menu-icon')

			animateFor(300, () => {
				const { width, height, top, left } =
					menuInnerContainer.getBoundingClientRect()
				menuBg.style.width = width + 'px'
				menuBg.style.height = height + 'px'
			})

			if (header.classList.contains('active')) {
				accordionContent.style.maxHeight = accordionContent.scrollHeight + 'px'
				header.setAttribute('aria-expanded', 'true')
				if (icon) icon.style.transform = 'rotate(180deg)'
			} else {
				accordionContent.style.maxHeight = '0'
				header.setAttribute('aria-expanded', 'false')
				if (icon) icon.style.transform = 'rotate(0deg)'
			}
		})
	})

	// Handle window resize
	window.addEventListener('resize', () => {
		accordionHeaders.forEach((header) => {
			const accordionContent = header.nextElementSibling
			if (window.innerWidth >= 1024) {
				accordionContent.style.maxHeight = 'none'
				header.classList.remove('active')
			} else if (!header.classList.contains('active')) {
				accordionContent.style.maxHeight = '0'
			}
		})
	})
}
