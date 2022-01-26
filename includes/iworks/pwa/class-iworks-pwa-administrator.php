<?php
/**
 *
 * @since 1.0.0
 */

require_once dirname( dirname( __FILE__ ) ) . '/class-iworks-pwa.php';


class iWorks_PWA_Administrator extends iWorks_PWA {

	/**
	 * OFFLINE_VERSION
	 *
	 * @since 1.0.0
	 */

	public function __construct() {
		parent::__construct();
		/**
		 * Show SSL warning for non debug
		 */
		if ( ! $this->debug && ! is_ssl() ) {
			add_action( 'load-index.php', array( $this, 'load_no_ssl_warning' ) );
		}
		/**
		 * iWorks PWA
		 */
		add_filter( 'iworks_pwa_options', array( $this, 'filter_add_debug_urls_to_config' ) );
		add_filter( 'iworks_pwa_administrator_debug_info', array( $this, 'filter_debug_info' ), 100 );
		/**
		 * WordPress Hooks
		 */
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		/**
		 * change logo for rate
		 */
		add_filter( 'iworks_rate_notice_logo_style', array( $this, 'filter_plugin_logo' ), 10, 2 );
	}

	public function filter_add_debug_urls_to_config( $options ) {
		if ( ! $this->debug ) {
			return $options;
		}
		$options['options'][] = array(
			'type'  => 'heading',
			'label' => __( 'Debug info', 'iworks-pwa' ),
		);
		$options['options'][] = array(
			'type'   => 'special',
			'filter' => 'iworks_pwa_administrator_debug_info',
		);
		return $options;
	}

	public function filter_debug_info( $content ) {
		$content .= sprintf( '<h2>%s</h2>', esc_html__( 'Main files', 'iworks-pwa' ) );
		$row      = '<li><a href="%1$s" target="_blank">%2$s</a></li>';
		$content .= '<ul>';
		$content .= sprintf( $row, '/manifest.json', esc_html__( 'manifest.json', 'iworks-pwa' ) );
		$content .= sprintf( $row, '/iworks-pwa-service-worker-js', esc_html__( 'Service Worker', 'iworks-pwa' ) );
		$content .= sprintf( $row, '/iworks-pwa-offline', esc_html__( 'Offline page', 'iworks-pwa' ) );
		$content .= '</ul>';
		return $content;
	}

	public function admin_init() {
		$this->options->options_init();
	}

	/**
	 * load no SSL warning
	 *
	 * @since 1.0.0
	 */
	public function load_no_ssl_warning() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
		add_action( 'admin_notices', array( $this, 'show_no_ssl' ) );
	}


}

