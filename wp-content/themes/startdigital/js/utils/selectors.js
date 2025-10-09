import TomSelect from 'tom-select'

export default function initSelectors() {
	const selectEls = document.querySelectorAll('.select')

	if (!selectEls.length > 0) return

	const settings = {
		create: false,
		copyClassesToDropdown: true,
	}

	selectEls.forEach((el) => {
		new TomSelect(el, settings)
	})
}
