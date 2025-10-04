import * as THREE from 'three'
import gsap from 'gsap'
import BaseScene from '../base-scene'
import TrackedPlane from '../utils/tracked-plane'
import WhiteNoiseMaterial from '../materials/white-noise-material'

class HomeShopScene extends BaseScene {
	setupScene() {
		this.heroContainer = document.querySelector('#home-shop')
	}

	createMaterials() {
		this.whiteNoiseMaterial = new WhiteNoiseMaterial(1)
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
		this.whiteNoiseMaterial.setColor(new THREE.Vector3(0.306, 0.137, 0.133))
	}

	animate(deltaTime) {
		this.time += deltaTime
		this.whiteNoiseMaterial.updateTime(this.time)
	}
}

export default HomeShopScene
