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
		add_action( 'init', array( $this, 'configuration' ) );
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

}
