(function() {
    // ServiceWorker is a progressive technology. Ignore unsupported browsers
    if ('serviceWorker' in navigator) {
        console.log('CLIENT: service worker registration in progress. serviceWorker');
        navigator.serviceWorker.register( window.iworks_pwa.pwa.root + 'service-worker.php').then(function() {
            console.log('CLIENT: service worker registration complete.');
        }, function() {
            console.log('CLIENT: service worker registration failure.');
        });
    } else {
        console.log('CLIENT: service worker is not supported.');
        // po aktywacji chcę skasować wszystkie cache w naszej domenie, które nie są naszym cache (to opcjonalne)
        self.addEventListener('activate', function(event) {
            event.waitUntil(
                caches.keys().then(function(cacheNames) {
                    return Promise.all(
                        cacheNames.filter(function(cacheName) {
                            return cacheName !== MY_CACHE;
                        }).map(function(cacheName) {
                            return caches.delete(cacheName);
                        })
                    );
                })
            );
        });
        // strategia 'Network falling back to cache'
        self.addEventListener('fetch', function(event) {
            event.respondWith(
                fetch(event.request).catch(function() {
                    return caches.match(event.request);
                })
            );
        });
    }
})();

