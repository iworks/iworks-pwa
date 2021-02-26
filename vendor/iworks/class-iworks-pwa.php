<?php


abstract class iWorks_PWA {

	protected $configuration = array();

	protected $url;

	protected $debug = false;

	protected $version = 'PLUGIN_VERSION';

	protected function __construct() {
		$this->url   = rtrim( plugin_dir_url( dirname( dirname( __FILE__ ) ) ), '/' );
		$this->debug = defined( 'WP_DEBUG' ) && WP_DEBUG;
		add_action( 'init', array( $this, 'configuration' ) );
	}

	public function configuration() {
		$root                = sprintf( '%s/assets/images/icons/favicon', $this->url );
		$this->configuration = apply_filters(
			'iworks_pwa_configuration',
			array(
				'name'             => apply_filters( 'iworks_pwa_configuration_name', get_bloginfo( 'name' ) ),
				'short_name'       => apply_filters( 'iworks_pwa_configuration_short_name', get_bloginfo( 'name' ) ),
				'theme_color'      => apply_filters( 'iworks_pwa_configuration_theme_color', '#ffffff' ),
				'background_color' => apply_filters( 'iworks_pwa_configuration_background_color', '#ffffff' ),
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
					),
				),
			)
		);
	}

	protected function get_name( $name ) {
		return preg_replace( '/_/', '-', strtolower( $name ) );
	}

}
