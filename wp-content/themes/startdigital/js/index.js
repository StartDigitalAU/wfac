import initRemoveLinks from './utils/removeLink'
import initHeaderOnScroll from './utils/headerOnScroll'
import initThree from './three/three-bootstrap'
import initSmoothScrolling from './utils/smooth-scroll'
import initMorphSVG from './components/svg-morph'
import initMouseFollowers from './utils/mouse-follower'
import initButtonAnimation from './components/button'
import initHeaderHoverAnimation from './components/header/header-hover'
import MenuStateManager from './components/header/menu-state-manager'
import initMenuAccordions from './components/header/header-accordion'

document.addEventListener('DOMContentLoaded', () => {
	initSmoothScrolling()
	initHeaderHoverAnimation()
	initMenuAccordions()
	new MenuStateManager()
	initRemoveLinks()
	initHeaderOnScroll()
	initButtonAnimation()
	initMouseFollowers()
	initThree()
	initMorphSVG()
})
