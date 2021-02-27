<?php
/**
 *
 * @since 1.0.0
 */

require_once dirname( dirname( __FILE__ ) ) . '/class-iworks-pwa.php';


class iWorks_PWA_manifest extends iWorks_PWA {

	public function __construct() {
		parent::__construct();
		/**
		 * handle special requests
		 */
		add_action( 'parse_request', array( $this, 'parse_request' ) );
		add_action( 'wp_head', array( $this, 'html_head' ), PHP_INT_MAX );
		/**
		 * js
		 */
		add_action( 'init', array( $this, 'register_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue' ), PHP_INT_MAX );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), PHP_INT_MAX );
		add_filter( 'wp_localize_script_iworks_pwa_manifest', array( $this, 'add_pwa_data' ) );
	}

	public function register_scripts() {
		wp_register_script(
			$this->get_name( __CLASS__ ),
			$this->url . sprintf( '/assets/scripts/frontend.%sjs', $this->debug ? '' : 'min.' ),
			array(),
			$this->version,
			true
		);
		$data = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		);
		$data = apply_filters( 'wp_localize_script_iworks_pwa_manifest', $data );
		wp_localize_script( $this->get_name( __CLASS__ ), 'iworks_pwa', $data );
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

	/**
	 *
	 * @since 1.0.0
	 */
	public function html_head() {
		echo '<link rel="manifest" href="/manifest.json" />';
		echo PHP_EOL;
	}

	public function parse_request() {
		if (
			! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}
		switch ( $_SERVER['REQUEST_URI'] ) {
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
		$data = preg_replace( '/%NAME%/', get_bloginfo( 'name' ), $data );
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

	private function print_iworks_pwa_service_worker_js() {
		header( 'Content-Type: text/javascript' );
		include $this->root . '/assets/pwa/service-worker.js';
		exit;
	}

	/**
	 * Handle "/manifest.json" request.
	 *
	 * @since 1.0.0
	 */
	private function print_manifest_json() {
		header( 'Content-Type: application/json' );
		echo json_encode( $this->configuration );
		exit;
	}

}

