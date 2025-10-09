import { createCanvas } from './utils'
import SceneManager from './scene-manager'
import HomeHeroScene from './scenes/home/home-hero-scene'
import HomeOnNowScene from './scenes/home/home-on-now'
import HomeArtClassesScene from './scenes/home/home-art-classes-scene'
import HomeShopScene from './scenes/home/home-shop-scene'
import HomeStoriesScene from './scenes/home/home-stories-scene'
import ProgramScene from './scenes/program/program-scene'
import FooterScene from './scenes/footer/footer-scene'

const SCENE_CONFIG = [
	{
		selector: '#home-hero',
		scene: HomeHeroScene,
		name: 'homeHero',
	},
	{
		selector: '#home-on-now',
		scene: HomeOnNowScene,
		name: 'homeOnNow',
	},
	{
		selector: '#home-art-classes',
		scene: HomeArtClassesScene,
		name: 'homeArtClasses',
	},
	{
		selector: '#home-shop',
		scene: HomeShopScene,
		name: 'homeShop',
	},
	{
		selector: '#home-stories',
		scene: HomeStoriesScene,
		name: 'homeStories',
	},
	{
		selector: '#program-archive',
		scene: ProgramScene,
		name: 'program',
	},
	{
		selector: 'footer',
		scene: FooterScene,
		name: 'footer',
	},
]

function initThree() {
	const canvas = createCanvas()
	const sceneManager = new SceneManager(canvas)

	SCENE_CONFIG.forEach(({ selector, scene, name }) => {
		const container = document.querySelector(selector)
		if (container) {
			sceneManager.addScene(scene, name, container)
		}
	})

	sceneManager.start()
}

export default initThree
