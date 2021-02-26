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
		include $this->root . '/assets/pwa/offline.html';
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

