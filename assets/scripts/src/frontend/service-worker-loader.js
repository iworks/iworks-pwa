if ('serviceWorker' in navigator) {
    // console.log('CLIENT: service worker registration in progress. serviceWorker');
    navigator.serviceWorker.register(window.iworks_pwa.serviceWorkerUri)
        .then(function(reg) {
            // console.log('Service worker registered! 😎', reg);
        })
        .catch(function(err) {
            console.log('😥 Service worker registration failed: ', err);
        });
}