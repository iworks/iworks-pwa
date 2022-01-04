<?php


abstract class iWorks_PWA {

	protected $configuration = array();

	protected $url;

	protected $debug = false;

	protected $version = 'PLUGIN_VERSION';

	protected $root = '';

	protected function __construct() {
		$file        = dirname( dirname( __FILE__ ) );
		$this->url   = rtrim( plugin_dir_url( $file ), '/' );
		$this->root  = rtrim( plugin_dir_path( $file ), '/' );
		$this->debug = defined( 'WP_DEBUG' ) && WP_DEBUG;
		if ( ! is_ssl() ) {
			add_action( 'load-index.php', array( $this, 'load_no_ssl_warning' ) );
			return;
		}
		add_action( 'init', array( $this, 'configuration' ) );
		/**
		 * change logo for rate
		 */
		add_filter( 'iworks_rate_notice_logo_style', array( $this, 'filter_plugin_logo' ), 10, 2 );
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

	/**
	 * show no SSL warning
	 *
	 * @since 1.0.0
	 */
	public function show_no_ssl() {
		$file            = $this->root . '/assets/templates/no-ssl.php';
		$args            = array(
			'title'       => __( 'iWorks PWA', 'iworks-pwa' ),
			'url'         => esc_url( _x( 'https://wordpress.org/plugins/iworks-pwa', 'plugins home', 'iworks-pwa' ) ),
			'logo'        => $this->get_logo_url(),
			'classes'     => array(),
			'support_url' => _x( 'https://wordpress.org/support/plugin/iworks-pwa', 'plugins support home', 'iworks-pwa' ),
			'slug'        => 'iworks-pwa',
		);
		$args['classes'] = array(
			'iworks-rate',
			'iworks-rate-' . $args['slug'],
			'iworks-rate-notice',
			'has-logo',
		);
		load_template( $file, true, $args );
	}

	/**
	 * Action handler for 'load-index.php'
	 * Set-up the Dashboard notification.
	 *
	 * @since  1.0.0
	 */
	public function admin_enqueue() {
		wp_enqueue_style(
			__CLASS__,
			plugin_dir_url( __FILE__ ) . 'rate/admin.css',
			array(),
			$this->version
		);
	}

	public function configuration() {
		$background_color    = $this->get_background_color();
		$root                = sprintf( '%s/assets/images/icons/favicon', $this->url );
		$this->configuration = apply_filters(
			'iworks_pwa_configuration',
			array(
				'name'             => apply_filters( 'iworks_pwa_configuration_name', get_bloginfo( 'name' ) ),
				'short_name'       => apply_filters( 'iworks_pwa_configuration_short_name', get_bloginfo( 'name' ) ),
				'theme_color'      => $background_color,
				'background_color' => apply_filters( 'iworks_pwa_configuration_background_color', $background_color ),
				'display'          => apply_filters( 'iworks_pwa_configuration_display', 'standalone' ),
				'orientation'      => apply_filters( 'iworks_pwa_configuration_orientation', 'portrait' ),
				'Scope'            => apply_filters( 'iworks_pwa_configuration_Scope', '/' ),
				'start_url'        => apply_filters( 'iworks_pwa_configuration_start_url', '/' ),
				'splash_pages'     => apply_filters( 'iworks_pwa_configuration_splash_pages', null ),
				'icons'            => apply_filters(
					'iworks_pwa_configuration_icons',
					array(
						array(
							'src'     => sprintf( '%s/android-icon-36x36.png', $root ),
							'sizes'   => '36x36',
							'type'    => 'image/png',
							'density' => '0.75',
						),
						array(
							'src'     => sprintf( '%s/android-icon-48x48.png', $root ),
							'sizes'   => '48x48',
							'type'    => 'image/png',
							'density' => '1.0',
						),
						array(
							'src'     => sprintf( '%s/android-icon-72x72.png', $root ),
							'sizes'   => '72x72',
							'type'    => 'image/png',
							'density' => '1.5',
						),
						array(
							'src'     => sprintf( '%s/android-icon-96x96.png', $root ),
							'sizes'   => '96x96',
							'type'    => 'image/png',
							'density' => '2.0',
						),
						array(
							'src'     => sprintf( '%s/android-icon-144x144.png', $root ),
							'sizes'   => '144x144',
							'type'    => 'image/png',
							'density' => '3.0',
						),
						array(
							'src'     => sprintf( '%s/android-icon-192x192.png', $root ),
							'sizes'   => '192x192',
							'type'    => 'image/png',
							'density' => '4.0',
						),
						array(
							'src'   => sprintf( '%s/android-icon-512x512.png', $root ),
							'sizes' => '512x512',
							'type'  => 'image/png',
						),
						array(
							'src'     => sprintf( '%s/maskable.png', $root ),
							'sizes'   => '1024x1024',
							'type'    => 'image/png',
							'purpose' => 'any maskable',
						),
					)
				),
			)
		);
	}

	protected function get_name( $name ) {
		return preg_replace( '/_/', '-', strtolower( $name ) );
	}

	/**
	 * get background color (meta: theme-color)
	 *
	 * @since 0.0.2
	 */
	protected function get_background_color() {
		$background_color = get_theme_mod( 'background_color', 'f0f0f0' );
		if ( preg_match( '/^[0-9a-f]+$/', $background_color ) ) {
			$background_color = '#' . $background_color;
		}
		return apply_filters( 'iworks_pwa_configuration_theme_color', $background_color );
	}

	/**
	 * Plugin logo for rate messages
	 *
	 * @since 1.0.0
	 *
	 * @param string $logo Logo, can be empty.
	 * @param object $plugin Plugin basic data.
	 */
	public function filter_plugin_logo( $logo, $plugin ) {
		if ( is_object( $plugin ) ) {
			$plugin = (array) $plugin;
		}
		if ( 'iworks-pwa' === $plugin['slug'] ) {
			return $this->get_logo_url();
		}
		return $logo;
	}

	/**
	 * get logo url
	 *
	 * @since 1.0.0
	 */
	private function get_logo_url() {
		return plugin_dir_url( dirname( dirname( __FILE__ ) ) ) . 'assets/images/icon.svg';
	}

}
