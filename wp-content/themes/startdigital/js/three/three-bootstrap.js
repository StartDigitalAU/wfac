import { createCanvas } from './utils'
import SceneManager from './scene-manager'
import HomeHeroScene from './scenes/home-hero-scene'
import HomeOnNowScene from './scenes/home-on-now'
import HomeArtClassesScene from './scenes/home-art-classes-scene'
import HomeShopScene from './scenes/home-shop-scene'
import HomeStoriesScene from './scenes/home-stories-scene'
import FooterScene from './scenes/footer-scene'

function initThree() {
	const homeHeroContainer = document.querySelector('#home-hero')
	const homeOnNowContainer = document.querySelector('#home-on-now')
	const homeArtClassesContainer = document.querySelector('#home-art-classes')
	const homeShopContainer = document.querySelector('#home-shop')
	const homeStoriesContainer = document.querySelector('#home-stories')
	const footerContainer = document.querySelector('footer')

	const canvas = createCanvas()
	const sceneManager = new SceneManager(canvas)

	if (homeHeroContainer)
		sceneManager.addScene(HomeHeroScene, 'homeHero', homeHeroContainer)

	if (homeOnNowContainer)
		sceneManager.addScene(HomeOnNowScene, 'homeOnNow', homeOnNowContainer)

	if (homeArtClassesContainer)
		sceneManager.addScene(
			HomeArtClassesScene,
			'homeArtClasses',
			homeArtClassesContainer
		)

	if (homeShopContainer)
		sceneManager.addScene(HomeShopScene, 'homeShop', homeShopContainer)

	if (homeStoriesContainer)
		sceneManager.addScene(HomeStoriesScene, 'homeStories', homeStoriesContainer)

	if (footerContainer)
		sceneManager.addScene(FooterScene, 'footer', footerContainer)

	sceneManager.start()
}

export default initThree
