self.addEventListener('install', function( event )  {
    event.waitUntil(
        caches.open('v1').then( function( cache ) {
            return cache.addAll([
                '/wp-content/themes/opi-jobs/assets/pwa/index.php',
                // '/wp-content/themes/opi-jobs/assets/main.bundle.js?ver=1',
                '/wp-content/themes/opi-jobs/assets/pwa/style.css'
            ]);
        }, function( error ) {
            console.log(`Installation failed with error: ${error}`);
        })
    );
});

self.addEventListener('activate', function( event ) {
    let cacheKeepList = ['v1'];
    event.waitUntil(
        caches.keys().then( function ( keyList ) {
            return Promise.all(keyList.map(function(key) {
                if (cacheKeepList.indexOf(key) === -1) {
                    return caches.delete(key);
                }
            }));
        })
    );
});

self.addEventListener('fetch', function( event ) {
    console.log('[Service Worker] Fetched resource ' + event.request.url);
    if (event.request.method != 'GET') {
        return;
    }
    event.respondWith(async function() {
        const cache = await caches.open('v1');
        const cachedResponse = await cache.match(event.request);
        if (cachedResponse) {
            event.waitUntil(cache.add(event.request));
            return cachedResponse;
        }
        return fetch(event.request);
    }());
});
