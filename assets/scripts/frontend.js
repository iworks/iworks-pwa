/*! iWorks PWA - v0.0.1
 * https://iworks.pl/
 * Copyright (c) 2021; * Licensed GPLv2+
 */
if ('serviceWorker' in navigator) {
    console.log('CLIENT: service worker registration in progress. serviceWorker');
    navigator.serviceWorker.register(window.iworks_pwa.pwa.root + 'service-worker.js').then(function() {
        console.log('CLIENT: service worker registration complete.');
    }, function() {
        console.log('CLIENT: service worker registration failure.');
    });
}