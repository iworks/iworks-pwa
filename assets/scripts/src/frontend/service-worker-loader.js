if ('serviceWorker' in navigator) {
    console.log('CLIENT: service worker registration in progress. serviceWorker');
    navigator.serviceWorker.register( window.iworks_pwa.pwa.root + 'service-worker.js').then(function() {
        console.log('CLIENT: service worker registration complete.');
    }, function() {
        console.log('CLIENT: service worker registration failure.');
    });
}
