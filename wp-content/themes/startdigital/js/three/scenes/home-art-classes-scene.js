import * as THREE from 'three'
import gsap from 'gsap'
import BaseScene from '../base-scene'
import TrackedPlane from '../utils/tracked-plane'
import WhiteNoiseMaterial from '../materials/white-noise-material'

class HomeArtClassesScene extends BaseScene {
	setupScene() {
		this.heroContainer = document.querySelector('#home-art-classes')
	}

	createMaterials() {
		this.whiteNoiseMaterial = new WhiteNoiseMaterial()
	}

	createObjects() {
		this.heroPlane = new TrackedPlane(
			this.scene,
			this.camera,
			this.heroContainer,
			this.container,
			{
				material: this.whiteNoiseMaterial.getMaterial(),
			}
		)
		const quadSize = this.heroPlane.getQuadSize()
		this.whiteNoiseMaterial.setQuadSize(quadSize.x, quadSize.y)
	}

	animate(deltaTime) {
		this.time += deltaTime
		this.whiteNoiseMaterial.updateTime(this.time)
	}
}

export default HomeArtClassesScene
