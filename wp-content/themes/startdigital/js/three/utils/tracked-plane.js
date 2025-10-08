import { Mesh, MeshBasicMaterial, Shape, ShapeGeometry } from 'three'
import * as THREE from 'three'
import { getLenis } from '../../utils/smooth-scroll'
import ImageMaterial from '../materials/image-material'

class TrackedPlane {
	constructor(scene, camera, element, container = null, config = {}) {
		this.scene = scene
		this.camera = camera
		this.element = element
		this.lenis = getLenis()
		this.container = container

		// Configuration options
		this.config = {
			material: config.material,
			zPosition: config.zPosition || 0,
			borderRadius: config.borderRadius || 0.025,
			smoothness: config.smoothness || 8,
			...config,
		}

		this.material = this.config.material || this.createMaterial()
		this.enabled = true

		this.smoothVelocity = 0
		this.velocityLerpFactor = 0.1

		// Track last dimensions to avoid unnecessary geometry updates
		this.lastDimensions = null

		this.geometry = this.createRoundedRectGeometry(1, 1)
		this.mesh = new Mesh(this.geometry, this.material)
		this.scene.add(this.mesh)

		this.setupListeners()
		this.updatePlane()
	}

	createRoundedRectGeometry(width, height) {
		const radius = Math.min(width, height) * this.config.borderRadius
		const r = Math.min(radius, Math.min(width, height) / 2)

		// Create a highly subdivided plane
		const segmentsX = Math.max(50, Math.floor(width * 20))
		const segmentsY = Math.max(50, Math.floor(height * 20))

		const geometry = new THREE.PlaneGeometry(
			width,
			height,
			segmentsX,
			segmentsY
		)

		// Get position attribute
		const positions = geometry.attributes.position

		// Define corner centers
		const left = -width / 2
		const right = width / 2
		const bottom = -height / 2
		const top = height / 2

		// Adjust vertices in corner regions to follow the curve
		for (let i = 0; i < positions.count; i++) {
			const px = positions.getX(i)
			const py = positions.getY(i)
			const pz = positions.getZ(i)

			let newPx = px
			let newPy = py

			// Bottom-left corner
			if (px < left + r && py < bottom + r) {
				const dx = px - (left + r)
				const dy = py - (bottom + r)
				const dist = Math.sqrt(dx * dx + dy * dy)

				if (dist > r) {
					// Project onto circle
					const angle = Math.atan2(dy, dx)
					newPx = left + r + Math.cos(angle) * r
					newPy = bottom + r + Math.sin(angle) * r
				}
			}
			// Bottom-right corner
			else if (px > right - r && py < bottom + r) {
				const dx = px - (right - r)
				const dy = py - (bottom + r)
				const dist = Math.sqrt(dx * dx + dy * dy)

				if (dist > r) {
					const angle = Math.atan2(dy, dx)
					newPx = right - r + Math.cos(angle) * r
					newPy = bottom + r + Math.sin(angle) * r
				}
			}
			// Top-right corner
			else if (px > right - r && py > top - r) {
				const dx = px - (right - r)
				const dy = py - (top - r)
				const dist = Math.sqrt(dx * dx + dy * dy)

				if (dist > r) {
					const angle = Math.atan2(dy, dx)
					newPx = right - r + Math.cos(angle) * r
					newPy = top - r + Math.sin(angle) * r
				}
			}
			// Top-left corner
			else if (px < left + r && py > top - r) {
				const dx = px - (left + r)
				const dy = py - (top - r)
				const dist = Math.sqrt(dx * dx + dy * dy)

				if (dist > r) {
					const angle = Math.atan2(dy, dx)
					newPx = left + r + Math.cos(angle) * r
					newPy = top - r + Math.sin(angle) * r
				}
			}

			positions.setXY(i, newPx, newPy)
		}

		positions.needsUpdate = true

		this.fixUVs(geometry, width, height)

		return geometry
	}

	fixUVs(geometry, width, height) {
		const uvAttribute = geometry.attributes.uv
		const posAttribute = geometry.attributes.position

		for (let i = 0; i < posAttribute.count; i++) {
			const x = posAttribute.getX(i)
			const y = posAttribute.getY(i)

			const u = (x + width / 2) / width
			const v = (y + height / 2) / height

			uvAttribute.setXY(i, u, v)
		}

		uvAttribute.needsUpdate = true
	}

	needsGeometryUpdate(worldDimensions) {
		if (!this.lastDimensions) return true

		const threshold = 0.001
		return (
			Math.abs(this.lastDimensions.width - worldDimensions.width) > threshold ||
			Math.abs(this.lastDimensions.height - worldDimensions.height) > threshold
		)
	}

	updateGeometry(worldDimensions) {
		this.geometry.dispose()
		this.geometry = this.createRoundedRectGeometry(
			worldDimensions.width,
			worldDimensions.height
		)
		this.mesh.geometry = this.geometry

		if (this.imageMaterial) {
			this.imageMaterial.material.uniforms.uQuadSize.value.set(
				worldDimensions.width,
				worldDimensions.height
			)
		}

		this.lastDimensions = { ...worldDimensions }
	}

	updatePosition(rect, containerRect) {
		const centerX = rect.left + rect.width / 2 - containerRect.left
		const centerY = rect.top + rect.height / 2 - containerRect.top

		const ndcX = (centerX / containerRect.width) * 2 - 1
		const ndcY = -((centerY / containerRect.height) * 2 - 1)

		const { width, height } = this.getFrustumDimensions(this.config.zPosition)
		const worldX = ndcX * (width / 2)
		const worldY = ndcY * (height / 2)

		this.mesh.position.set(worldX, worldY, this.config.zPosition)
	}

	updatePlane() {
		if (!this.enabled || !this.element) {
			this.mesh.visible = false
			return
		}

		this.mesh.visible = true

		const rect = this.element.getBoundingClientRect()
		const containerRect = this.getContainerRect()

		const worldDimensions = this.getWorldSizeFromPixels({
			width: rect.width,
			height: rect.height,
		})

		// Only recreate geometry if dimensions changed
		if (this.needsGeometryUpdate(worldDimensions)) {
			this.updateGeometry(worldDimensions)
		}

		// Always update position (cheap operation)
		this.updatePosition(rect, containerRect)
	}

	createMaterial() {
		const rect = this.element.getBoundingClientRect()
		const imgElement = this.element.querySelector('img')

		if (!imgElement) {
			return new MeshBasicMaterial({ color: 0xff0000 })
		}

		imgElement.style.opacity = 0
		const imageSrc = imgElement.src

		const worldDimensions = this.getWorldSizeFromPixels({
			width: rect.width,
			height: rect.height,
		})

		this.imageMaterial = new ImageMaterial({
			uTexture: new THREE.Texture(),
			uTextureSize: new THREE.Vector2(1024, 1024),
			uQuadSize: new THREE.Vector2(
				worldDimensions.width,
				worldDimensions.height
			),
		})

		const textureLoader = new THREE.TextureLoader()

		textureLoader.load(
			imageSrc,
			(loadedTexture) => {
				this.imageMaterial.material.uniforms.uTexture.value = loadedTexture
				this.imageMaterial.material.uniforms.uTextureSize.value =
					new THREE.Vector2(
						loadedTexture.image.width,
						loadedTexture.image.height
					)
				this.material.needsUpdate = true
			},
			undefined,
			(error) => {
				console.error('Error loading texture:', error)
			}
		)

		return this.imageMaterial.getMaterial()
	}

	setupListeners() {
		this.lenis.on('scroll', () => {
			this.updatePlane()
		})
		window.addEventListener('resize', this.updatePlane.bind(this))
	}

	enable() {
		this.enabled = true
		this.updatePlane()
	}

	disable() {
		this.enabled = false
		this.mesh.visible = false
	}

	setMaterial(material) {
		this.material = material
		this.mesh.material = material
	}

	setElement(element) {
		this.element = element
		this.updatePlane()
	}

	getMaterial() {
		return this.material
	}

	getImageMaterial() {
		return this.imageMaterial
	}

	getQuadSize() {
		if (!this.element) {
			return new THREE.Vector2(0, 0)
		}

		const rect = this.element.getBoundingClientRect()
		const worldDimensions = this.getWorldSizeFromPixels({
			width: rect.width,
			height: rect.height,
		})

		return new THREE.Vector2(worldDimensions.width, worldDimensions.height)
	}

	setZPosition(zPosition) {
		this.config.zPosition = zPosition
		this.lastDimensions = null // Force geometry update on next update
		this.updatePlane()
	}

	getZPosition() {
		return this.config.zPosition
	}

	setBorderRadius(borderRadius) {
		this.config.borderRadius = borderRadius
		this.lastDimensions = null // Force geometry update on next update
		this.updatePlane()
	}

	getBorderRadius() {
		return this.config.borderRadius
	}

	getFrustumDimensions(zPosition = 0) {
		const distance = Math.abs(this.camera.position.z - zPosition)
		const fov = this.camera.fov * (Math.PI / 180)
		const aspect = this.camera.aspect
		const height = 2 * Math.tan(fov / 2) * distance
		const width = height * aspect
		return { width, height }
	}

	getWorldSizeFromPixels(options) {
		const containerRect = this.getContainerRect()
		const { width: frustumWidth, height: frustumHeight } =
			this.getFrustumDimensions(this.config.zPosition)
		const result = {}

		if (options.width !== undefined) {
			const worldUnitsPerPixel = frustumWidth / containerRect.width
			result.width = options.width * worldUnitsPerPixel
		}

		if (options.height !== undefined) {
			const worldUnitsPerPixel = frustumHeight / containerRect.height
			result.height = options.height * worldUnitsPerPixel
		}

		return result
	}

	getContainerRect() {
		if (this.container) {
			return this.container.getBoundingClientRect()
		}
		if (this.scene.userData && this.scene.userData.container) {
			return this.scene.userData.container.getBoundingClientRect()
		}

		return {
			left: 0,
			top: 0,
			width: window.innerWidth,
			height: window.innerHeight,
		}
	}

	dispose() {
		this.scene.remove(this.mesh)
		this.geometry.dispose()
		if (this.material.dispose) {
			this.material.dispose()
		}
		if (this.imageMaterial && this.imageMaterial.dispose) {
			this.imageMaterial.dispose()
		}
		window.removeEventListener('resize', this.updatePlane.bind(this))
	}
}

export default TrackedPlane
