import BaseScene from '../base-scene'
import TrackedPlane from '../utils/tracked-plane'
import FooterMaterial from '../materials/footer-material'
import { LinkSlider } from '../../components/sliders/LinkSlider'

class FooterScene extends BaseScene {
	setupScene() {
		this.footerContainer = document.querySelector('footer')
		this.sliderContainer = document.querySelector('footer [data-link-slider]')
		this.createSlider()
	}

	createMaterials() {
		this.footerMaterial = new FooterMaterial()
	}

	createObjects() {
		this.heroPlane = new TrackedPlane(
			this.scene,
			this.camera,
			this.footerContainer,
			this.container,
			{
				borderRadius: 0,
				material: this.footerMaterial.getMaterial(),
			}
		)

		this.updateContainerBounds()
	}

	updateContainerBounds() {
		if (this.heroPlane && this.footerContainer) {
			this.footerMaterial.setContainerFromElement(
				this.footerContainer,
				this.heroPlane
			)
		}
	}

	createSlider() {
		if (!this.sliderContainer) return

		const config = {
			snap: false,
			infinite: true,
		}

		this.linkSlider = new LinkSlider(this.sliderContainer, config)
		this.linkSlider.setAutoScrollSpeed(0.003)
	}

	onResize() {
		super.onResize()
		this.updateContainerBounds()
	}

	animate(deltaTime) {
		this.time += deltaTime
		this.footerMaterial.updateTime(this.time)
	}
}

export default FooterScene
