if ('serviceWorker' in navigator) {
	navigator.serviceWorker.register(window.iworks_pwa.serviceWorkerUri)
		.then(function(reg) {})
		.catch(function(err) {});
}