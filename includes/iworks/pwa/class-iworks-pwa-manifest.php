<?php
/**
 *
 * @since 1.0.0
 */

require_once dirname( dirname( __FILE__ ) ) . '/class-iworks-pwa.php';


class iWorks_PWA_manifest extends iWorks_PWA {

	/**
	 * OFFLINE_VERSION
	 *
	 * @since 1.0.0
	 */
	private $offline_version = 2;

	public function __construct() {
		parent::__construct();
		/**
		 * handle special requests
		 */
		add_action( 'parse_request', array( $this, 'parse_request' ) );
		/**
		 * js
		 */
		add_action( 'init', array( $this, 'register_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue' ), PHP_INT_MAX );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), PHP_INT_MAX );
		add_filter( 'wp_localize_script_iworks_pwa_manifest', array( $this, 'add_pwa_data' ) );
		/**
		 * debug
		 */
		add_filter( 'iworks_pwa_administrator_debug_info', array( $this, 'filter_debug_info' ), 200 );
		/**
		 * Clear generated icons
		 *
		 * @since 1.0.1
		 */
		$option_name = $this->options->get_option_name( 'icon_app' );
		add_action( 'update_option_' . $option_name, array( $this, 'action_flush_icons' ), 10, 3 );
	}

	public function register_scripts() {
		wp_register_script(
			$this->get_name( __CLASS__ ),
			$this->url . sprintf( '/assets/scripts/frontend.%sjs', $this->debug ? '' : 'min.' ),
			array(),
			$this->version,
			true
		);
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_script( $this->get_name( __CLASS__ ) );
	}

	public function add_pwa_data( $data ) {
		$data['pwa'] = array(
			'root' => $this->url . '/assets/pwa/',
		);
		return $data;
	}

	public function parse_request() {
		if (
			! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}
		$uri = remove_query_arg( array_keys( $_GET ), $_SERVER['REQUEST_URI'] );
		switch ( $uri ) {
			case '/manifest.json':
				$this->print_manifest_json();
				break;
			case '/iworks-pwa-service-worker-js':
				$this->print_iworks_pwa_service_worker_js();
				break;
			case '/iworks-pwa-offline':
				$this->print_iworks_pwa_offline();
				break;
		}
	}

	private function print_iworks_pwa_offline() {
		header( 'Content-Type: text/html' );
		$data = apply_filters( 'iworks_pwa_offline_file', null );
		if ( empty( $data ) ) {
			$data = file_get_contents( $this->root . '/assets/pwa/offline.html' );
		}
		/**
		 * WP
		 */
		$data = preg_replace( '/%HTML_LANGUAGE_ATTRIBUTES%/', get_language_attributes( 'html' ), $data );
		$data = preg_replace( '/%CHARSET%/', get_bloginfo( 'charset' ), $data );
		/**
		 * title
		 */
		$data = preg_replace( '/%SORRY%/', apply_filters( 'iworks_pwa_offline_sorry', __( 'Sorry!', 'iworks-pwa' ) ), $data );
		$data = preg_replace( '/%NAME%/', $this->get_configuration_name(), $data );
		/**
		 * content
		 */
		$content  = '';
		$content .= wpautop( __( 'We were unable to load the page you requested.', 'iworks-pwa' ) );
		$content .= wpautop( __( 'Please check your network connection and try again.', 'iworks-pwa' ) );
		$data     = preg_replace( '/%CONTENT%/', apply_filters( 'iworks_pwa_offline_content', $content ), $data );
		/**
		 * SVG
		 */
		$svg  = '<svg viewBox="0, 0, 24, 24"><path d="M23.64 7c-.45-.34-4.93-4-11.64-4-1.5 0-2.89.19-4.15.48L18.18 13.8 23.64 7zm-6.6 8.22L3.27 1.44 2 2.72l2.05 2.06C1.91 5.76.59 6.82.36 7l11.63 14.49.01.01.01-.01 3.9-4.86 3.32 3.32 1.27-1.27-3.46-3.46z"></path></svg>';
		$data = preg_replace( '/%SVG%/', apply_filters( 'iworks_pwa_offline_svg', $svg ), $data );
		/**
		 * print
		 */
		echo $data;
		exit;
	}

	private function helper_join_strings( $string ) {
		return sprintf(
			"'%s'",
			preg_replace( "/'/", "\\'", $string )
		);
	}

	private function print_iworks_pwa_service_worker_js() {
		header( 'Content-Type: text/javascript' );
		$set = array(
			home_url(),
		);
		$url = get_privacy_policy_url();
		if ( ! empty( $url ) ) {
			$set[] = $url;
		}
		$set = array_unique( apply_filters( 'iworks_pwa_offline_urls_set', $set ) );
		if ( empty( $set ) ) {
			$set = '';
		} else {
			$set = implode( ', ', array_map( array( $this, 'helper_join_strings' ), $set ) );
		}
		?>
const OFFLINE_VERSION = <?php echo intval( apply_filters( 'iworks_pwa_offline_version', $this->offline_version ) ); ?>;
const CACHE_NAME = '<?php echo apply_filters( 'iworks_pwa_offline_cache_name', 'iworks-pwa-offline-cache-name' ); ?>';
const OFFLINE_URL = 'iworks-pwa-offline';

const OFFLINE_URLS_SET = [<?php echo $set; ?>];

self.addEventListener('install', (event) => {
  event.waitUntil((async () => {
	const cache = await caches.open(CACHE_NAME);
	await cache.add(new Request(OFFLINE_URL, {cache: 'reload'}));
	caches.open(CACHE_NAME).then(function(cache) {
		return cache.addAll(OFFLINE_URLS_SET);
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
		  return cacheName !== CACHE_NAME;
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
		const cache = await caches.open(CACHE_NAME);
		const cachedResponse = await cache.match(OFFLINE_URL);
		return cachedResponse;
	  }
	})());
  }
});
		<?php
		exit;
	}

	/**
	 * Handle "/manifest.json" request.
	 *
	 * @since 1.0.0
	 */
	private function print_manifest_json() {
		$data          = $this->get_configuration();
		$icons         = $data['icons'];
		$data['icons'] = array();
		$allowed_keys  = array( 'sizes', 'type', 'density', 'src', 'purpose' );
		foreach ( $icons as $one ) {
			foreach ( array_keys( $one ) as $key ) {
				if ( in_array( $key, $allowed_keys ) ) {
					continue;
				}
				unset( $one[ $key ] );
			}
			array_unshift( $data['icons'], $one );
		}
		header( 'Content-Type: application/json' );
		echo json_encode( $data );
		exit;
	}

	public function filter_debug_info( $content ) {
		$content .= sprintf( '<h2>%s</h2>', esc_html__( 'Icons', 'iworks-pwa' ) );
		$icons    = $this->get_configuration_icons();
		if ( empty( $icons ) ) {
			$content .= sprintf( '<p>%s</p>', esc_html__( 'First you need to set some icons.', 'iworks-pwa' ) );
		} else {
			$content .= '<table>';
			foreach ( $icons as $one ) {
				$content .= '<tr>';
				$content .= sprintf( '<td>%s - %s</td>', $one['sizes'], $one['type'] );
				$content .= sprintf(
					'<td><a href="%1$s" target="_blank"><img src="%1$s" width="64" height="64" /></a></td>',
					$one['src']
				);
				$content .= '</tr>';
			}
			$content .= '</table>';
		}
		return $content;
	}

	public function filter_iworks_pwa_flush_icons_list( $list ) {
		$list[] = 'icons_manifest';
		return $list;
	}

}

