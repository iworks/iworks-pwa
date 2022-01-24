/*! PWA — easy way to Progressive Web App - v1.1.4
 * https://iworks.pl/
 * Copyright (c) 2022; * Licensed GPLv2+
 */
if ('serviceWorker' in navigator) {
    // console.log('CLIENT: service worker registration in progress. serviceWorker');
    navigator.serviceWorker.register( '/iworks-pwa-service-worker-js' ).then(function() {
        // console.log('CLIENT: service worker registration complete.');
    }, function() {
        // console.log('CLIENT: service worker registration failure.');
    });
}
