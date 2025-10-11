class ProgramLoader {
	constructor() {
		this.container = document.getElementById('programs-container')

		if (!this.container) return

		const urlParams = new URLSearchParams(window.location.search)
		this.display = urlParams.get('display') || 'grid'

		this.pageId = ajaxData.pageId || 0

		this.page = 1
		this.loading = false
		this.hasMore = true

		this.init()
	}

	init() {
		this.setupInfiniteScroll()
	}

	setupInfiniteScroll() {
		if (this.display === 'feed') {
			// Feed mode: loading controlled by initFeed
		} else {
			this.setupGridListScroll()
		}
	}

	setupGridListScroll() {
		const sentinel = document.createElement('div')
		sentinel.id = 'scroll-sentinel'
		sentinel.className = 'h-20'
		this.container.parentElement.appendChild(sentinel)

		this.observer = new IntersectionObserver(
			(entries) => {
				if (entries[0].isIntersecting && !this.loading && this.hasMore) {
					this.loadMore()
				}
			},
			{
				rootMargin: '200px',
				threshold: 0,
			}
		)

		this.observer.observe(sentinel)
		this.sentinel = sentinel
	}

	async loadMore() {
		if (this.loading || !this.hasMore) return

		this.loading = true
		this.showLoadingIndicator()

		try {
			const formData = new FormData()
			formData.append('action', 'load_more_programs')
			formData.append('nonce', ajaxData.nonce)
			formData.append('paged', this.page + 1)
			formData.append('display', this.display)
			formData.append('pageId', this.pageId)

			const response = await fetch(ajaxData.ajaxUrl, {
				method: 'POST',
				body: formData,
			})

			const responseText = await response.text()

			let data
			try {
				data = JSON.parse(responseText)
			} catch (e) {
				console.error('Response is not JSON:', responseText)
			}

			if (data.success) {
				this.container.insertAdjacentHTML('beforeend', data.data.html)
				this.createWebglImages()
				this.page++
				this.hasMore = data.data.has_more

				// Dispatch event for feed to listen to
				window.dispatchEvent(new CustomEvent('programsLoaded'))

				if (!this.hasMore) {
					this.cleanup()
				}
			} else {
				console.error('AJAX Error:', data.data)
			}
		} catch (error) {
			console.error('Error loading posts:', error)
		} finally {
			this.loading = false
			this.hideLoadingIndicator()
		}
	}

	createWebglImages() {
		if (this.display == 'list') return
		const newImages = this.container.querySelectorAll(
			'.image-container:not([data-webgl-initialized])'
		)

		if (window.programScene && newImages.length > 0) {
			newImages.forEach((img) =>
				img.setAttribute('data-webgl-initialized', 'true')
			)

			window.programScene.createImagePlanes(newImages)
		}
	}

	showLoadingIndicator() {}

	hideLoadingIndicator() {}

	cleanup() {
		if (this.observer) {
			this.observer.disconnect()
		}
		if (this.sentinel) {
			this.sentinel.remove()
		}
	}
}

export default ProgramLoader
