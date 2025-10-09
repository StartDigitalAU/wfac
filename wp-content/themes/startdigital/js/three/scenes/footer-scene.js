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
		this.footerPlane = new TrackedPlane(
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
		if (this.footerPlane && this.footerContainer) {
			this.footerMaterial.setContainerFromElement(
				this.footerContainer,
				this.footerPlane
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
		this.footerPlane.updatePlane()
	}

	animate(deltaTime) {
		this.time += deltaTime
		this.footerMaterial.updateTime(this.time)
	}
}

export default FooterScene
