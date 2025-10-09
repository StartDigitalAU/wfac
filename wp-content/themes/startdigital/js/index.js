import initThree from './three/three-bootstrap'
import initSmoothScrolling from './utils/smooth-scroll'
import initMorphSVG from './components/svg-morph'
import initMouseFollowers from './utils/mouse-follower'
import initButtonAnimation from './components/button'
import initHeaderHoverAnimation from './components/header/header-hover'
import MenuStateManager from './components/header/menu-state-manager'
import initMenuAccordions from './components/header/header-accordion'
import initSelectors from './utils/selectors'
import initDatePickers from './utils/date-pickers'
import initProgramMenu from './components/program/program-menu'

document.addEventListener('DOMContentLoaded', () => {
	initSmoothScrolling()
	initDatePickers()
	initSelectors()
	initHeaderHoverAnimation()
	initMenuAccordions()
	new MenuStateManager()
	initButtonAnimation()
	initMouseFollowers()
	initProgramMenu()
	initThree()
	initMorphSVG()
})
