export function animateFor(duration, callback, onComplete = null) {
	const startTime = performance.now()

	function animate() {
		const currentTime = performance.now()
		const elapsed = currentTime - startTime
		const progress = Math.min(elapsed / duration, 1)

		callback(progress, elapsed)

		if (progress < 1) {
			requestAnimationFrame(animate)
		} else if (onComplete) {
			onComplete()
		}
	}

	requestAnimationFrame(animate)
}
