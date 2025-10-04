import initMenus from './components/menus'
import initRemoveLinks from './utils/removeLink'
import initHeaderOnScroll from './utils/headerOnScroll'
import initThree from './three/three-bootstrap'
import initSmoothScrolling from './utils/smooth-scroll'
import initMorphSVG from './components/svg-morph'
import initMouseFollowers from './utils/mouse-follower'
import initButtonAnimation from './components/button'

document.addEventListener('DOMContentLoaded', () => {
	initSmoothScrolling()
	initMenus()
	initRemoveLinks()
	initHeaderOnScroll()
	initButtonAnimation()
	initMouseFollowers()
	initThree()
	initMorphSVG()
})
