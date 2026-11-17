/**
 * PLUGIN_TITLE - PLUGIN_VERSION
 */

/**
 * Configuration injected from PHP.
 */
const CACHE_NAME = '<?php echo $args['cache_name']; ?>';
const OFFLINE_URL = '<?php echo $args['offline_url']; ?>';
const PRECACHE_URLS = [ <?php echo $args['offline_urls_set']; ?> ];
const EXCLUDED_PATTERNS = [ <?php echo $args['excluded_patterns']; ?> ];
const CACHE_STRATEGY = '<?php echo $args['cache_strategy']; ?>';
const ASSET_EXTENSIONS = [
	'css', 'js', 'mjs', 'map',
	'png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'avif', 'ico',
	'woff', 'woff2', 'ttf', 'eot', 'otf',
];

/**
 * Check if a request should be handled by the service worker at all.
 *
 * @param {string} url Request URL.
 * @return {boolean} True when the request should bypass the worker.
 */
function isExcluded(url) {
	for (const pattern of EXCLUDED_PATTERNS) {
		if (url.pathname.match(pattern) || url.href.match(pattern)) {
			return true;
		}
	}
	return false;
}

/**
 * Check if a URL points to a static asset that can be cache-first.
 *
 * @param {URL} url Parsed URL.
 * @return {boolean} True when the URL looks like a static asset.
 */
function isAsset(url) {
	const extension = url.pathname.split('.').pop().toLowerCase();
	return ASSET_EXTENSIONS.includes(extension);
}

/**
 * Network-first strategy: try network, fall back to cache, then offline page.
 * Successful responses update the cache in the background.
 *
 * @param {Request} request    Request to handle.
 * @param {boolean} navigate   Whether this is a navigation request.
 * @return {Promise<Response>} Response.
 */
async function strategyNetworkFirst(request, navigate) {
	const cache = await caches.open(CACHE_NAME);
	try {
		const response = await fetch(request);
		if (response && response.ok) {
			cache.put(request, response.clone());
		}
		return response;
	} catch (error) {
		const cachedResponse = await cache.match(request, { ignoreSearch: navigate });
		if (cachedResponse) {
			return cachedResponse;
		}
		if (navigate) {
			const offline = await cache.match(OFFLINE_URL);
			if (offline) {
				return offline;
			}
		}
		throw error;
	}
}

/**
 * Stale-while-revalidate strategy: serve cached instantly, refresh in background.
 * Falls back to network, then to the offline page for navigations.
 *
 * @param {Request} request  Request to handle.
 * @param {boolean} navigate Whether this is a navigation request.
 * @return {Promise<Response>} Response.
 */
async function strategyStaleWhileRevalidate(request, navigate) {
	const cache = await caches.open(CACHE_NAME);
	const cachedResponse = await cache.match(request, { ignoreSearch: navigate });
	const networkFetch = fetch(request)
		.then((response) => {
			if (response && response.ok) {
				cache.put(request, response.clone());
			}
			return response;
		})
		.catch(() => null);
	if (cachedResponse) {
		networkFetch.catch(() => null);
		return cachedResponse;
	}
	try {
		const response = await networkFetch;
		if (response) {
			return response;
		}
	} catch (error) {
		// Fall through to the offline page below.
	}
	if (navigate) {
		const offline = await cache.match(OFFLINE_URL);
		if (offline) {
			return offline;
		}
	}
	return new Response('', { status: 504, statusText: 'Offline' });
}

/**
 * Cache-first strategy: serve cache when possible (assets).
 *
 * @param {Request} request Request to handle.
 * @return {Promise<Response>} Response.
 */
async function strategyCacheFirst(request) {
	const cache = await caches.open(CACHE_NAME);
	const cachedResponse = await cache.match(request);
	if (cachedResponse) {
		return cachedResponse;
	}
	const response = await fetch(request);
	if (response && response.ok && 'basic' === response.type) {
		cache.put(request, response.clone());
	}
	return response;
}

self.addEventListener('install', (event) => {
	event.waitUntil((async () => {
		const cache = await caches.open(CACHE_NAME);
		/**
		 * The offline page is critical — cache it with cache:reload so the
		 * HTTP cache cannot poison it.
		 */
		await cache.add(new Request(OFFLINE_URL, {
			cache: 'reload'
		}));
		/**
		 * Precache set is best-effort: fetch each URL individually so one
		 * failing URL cannot abort the whole install and wedge the worker
		 * in "trying to install" forever.
		 */
		await Promise.allSettled(
			PRECACHE_URLS.map((url) => cache.add(new Request(url, {
				cache: 'reload'
			})))
		);
	})());
});

self.addEventListener('activate', (event) => {
	event.waitUntil((async () => {
		if ('navigationPreload' in self.registration) {
			await self.registration.navigationPreload.enable();
		}
	})());
	event.waitUntil((async () => {
		const cacheNames = await caches.keys();
		await Promise.all(
			cacheNames.filter((cacheName) => CACHE_NAME !== cacheName).map((cacheName) => {
				return caches.delete(cacheName);
			})
		);
		await self.clients.claim();
	})());
});

self.addEventListener('fetch', (event) => {
	const url = new URL(event.request.url);

	/**
	 * Never handle excluded URLs, non-GET requests or cross-origin requests.
	 */
	if ('GET' !== event.request.method || isExcluded(url) || self.location.origin !== url.origin) {
		return;
	}

	/**
	 * Cache-first for static assets, regardless of the page strategy.
	 */
	if (isAsset(url)) {
		event.respondWith(strategyCacheFirst(event.request));
		return;
	}

	/**
	 * Navigations follow the configured strategy.
	 */
	if ('navigate' === event.request.mode) {
		event.respondWith((async () => {
			if ('stale-while-revalidate' === CACHE_STRATEGY) {
				return strategyStaleWhileRevalidate(event.request, true);
			}
			if ('cache-first' === CACHE_STRATEGY) {
				return strategyStaleWhileRevalidate(event.request, true);
			}
			return strategyNetworkFirst(event.request, true);
		})());
		return;
	}

	/**
	 * Same-origin asset-like subresource requests follow cache strategies too,
	 * but only for requests without credentials-sensitive semantics.
	 */
	if ('no-cors' === event.request.mode || 'cors' === event.request.mode) {
		if ('cache-first' === CACHE_STRATEGY || 'stale-while-revalidate' === CACHE_STRATEGY) {
			event.respondWith(strategyCacheFirst(event.request));
			return;
		}
		event.respondWith(strategyNetworkFirst(event.request, false));
	}
});

/**
 * Allow the page to tell a waiting worker to take over immediately
 * (used by the "New version available" toast).
 */
self.addEventListener('message', (event) => {
	if (event.data && 'SKIP_WAITING' === event.data.type) {
		self.skipWaiting();
	}
});
