self.addEventListener('install', (event) => {
    event.waitUntil((async () => {
        const cache = await caches.open('<?php echo $args['cache_name']; ?>');
        await cache.add(new Request('<?php echo $args['offline_url']; ?>', {
                cache: 'reload'
            }));
        caches.open('<?php echo $args['cache_name']; ?>').then(function(cache) {
            return cache.addAll([ <?php echo $args['offline_urls_set']; ?> ]);
        });
    })());
});

self.addEventListener('activate', (event) => {
    event.waitUntil((async () => {
        if ('navigationPreload' in self.registration) {
            await self.registration.navigationPreload.enable();
        }
    })());
    self.clients.claim();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.filter(function(cacheName) {
                    return '<?php echo $args['cache_name']; ?>' !== cacheName;
                }).map(function(cacheName) {
                    return caches.delete(cacheName);
                })
            );
        })
    );
});

self.addEventListener('fetch', (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith((async () => {
            try {
                // First, try to use the navigation preload response if it's supported.
                const preloadResponse = await event.preloadResponse;
                if (preloadResponse) {
                    return preloadResponse;
                }
                const networkResponse = await fetch(event.request);
                return networkResponse;
            } catch (error) {
                console.log('Fetch failed; returning offline page instead.', error);
                const cache = await caches.open('<?php echo $args['cache_name']; ?>');
                const cachedResponse = await cache.match('<?php echo $args['offline_url']; ?>');
                return cachedResponse;
            }
        })());
    }
});
