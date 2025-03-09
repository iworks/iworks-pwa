/*! PWA — easy way to Progressive Web App - 1.6.9
 * https://github.com/iworks/iworks-pwa
 * Copyright (c) 2025; * Licensed GPL-3.0 */
window.addEventListener('load', function(event) {
	var iworks_pwa_deferred_prompt;
	const iworks_pwa_add_button = document.querySelector('#iworks-pwa-add-button');
	const iworks_pwa_add_button_container = document.querySelector('#iworks-pwa-add-button-container');
	if (iworks_pwa_add_button) {
		window.addEventListener('beforeinstallprompt', function(event) {
			// Prevent Chrome 67 and earlier from automatically showing the prompt
			event.preventDefault();
			// Stash the event so it can be triggered later.
			iworks_pwa_deferred_prompt = event;
			// Update UI to notify the user they can add to home screen
			iworks_pwa_add_button_container.style.display = 'block';
			iworks_pwa_add_button.addEventListener('click', function() {
				// hide our user interface that shows our A2HS button
				iworks_pwa_add_button_container.style.display = 'none';
				// Show the prompt
				iworks_pwa_deferred_prompt.prompt();
				// Wait for the user to respond to the prompt
				iworks_pwa_deferred_prompt.userChoice.then(function(choiceResult) {
					if (choiceResult.outcome === 'accepted') {
						// console.log('User accepted the A2HS prompt');
					} else {
						// console.log('User dismissed the A2HS prompt');
					}
					iworks_pwa_deferred_prompt = null;
				});
			});
		});
	}
});
if ('serviceWorker' in navigator) {
	navigator.serviceWorker.register(window.iworks_pwa.serviceWorkerUri)
		.then(function(reg) {})
		.catch(function(err) {});
}