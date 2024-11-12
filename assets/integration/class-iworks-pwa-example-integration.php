<?php


class iWorks_PWA_Eexample_Integration {

	/**
	 * Configuration for:
	 * /manifest.json
	 * /browserconfig.xml
	 *
	 * @since 1.0.0
	 */
	private $color_title      = '#2d2683';
	private $color_theme      = '#000000';
	private $color_background = '#f0f0ff';
	private $short_name       = 'Example integration';

	private $url  = 'https://example.com/wp-content/themes/example/';
	private $root = '/var/virtuals/wordpress/example/';

	public function __construct() {
		add_filter( 'iworks_pwa_configuration', array( $this, 'iworks_pwa_configuration' ) );
		add_filter( 'iworks_pwa_offline_svg', array( $this, 'iworks_pwa_offline_svg' ) );
		add_filter( 'iworks_pwa_offline_file', array( $this, 'iworks_pwa_offline_file' ) );
		add_filter( 'iworks_pwa_offline_urls_set', array( $this, 'iworks_pwa_offline_urls_set' ) );
	}

	public function iworks_pwa_offline_urls_set( $set ) {
		$url = get_privacy_policy_url();
		if ( ! empty( $url ) ) {
			$set[] = $url;
		}
		$set[] = $this->url . '/assets/images/icons/favicon/favicon.ico';
		return $set;
	}

	public function iworks_pwa_offline_file( $data ) {
		return file_get_contents( $this->root . '/assets/pwa/offline.html' );
	}

	public function iworks_pwa_configuration( $data ) {
		return wp_parse_args( $this->manifest_json_data(), $data );
	}

	public function iworks_pwa_offline_svg( $svg ) {
		$svg = file_get_contents( get_stylesheet_directory() . '/assets/images/logo.svg' );
		return $svg;
	}

	private function manifest_json_data() {
		return array(
			'name'             => get_bloginfo( 'sitename' ),
			'short_name'       => $this->short_name,
			'theme_color'      => $this->color_theme,
			'background_color' => $this->color_background,
			'display'          => 'standalone',
			'Scope'            => '/',
			'start_url'        => '/',
			'icons'            => array(
				array(
					'src'     => esc_url( $this->get_asset_url( 'icons/favicon/android-icon-36x36.png' ) ),
					'sizes'   => '36x36',
					'type'    => 'image/png',
					'density' => '0.75',
				),
				array(
					'src'     => esc_url( $this->get_asset_url( 'icons/favicon/android-icon-48x48.png' ) ),
					'sizes'   => '48x48',
					'type'    => 'image/png',
					'density' => '1.0',
				),
				array(
					'src'     => esc_url( $this->get_asset_url( 'icons/favicon/android-icon-72x72.png' ) ),
					'sizes'   => '72x72',
					'type'    => 'image/png',
					'density' => '1.5',
				),
				array(
					'src'     => esc_url( $this->get_asset_url( 'icons/favicon/android-icon-96x96.png' ) ),
					'sizes'   => '96x96',
					'type'    => 'image/png',
					'density' => '2.0',
				),
				array(
					'src'     => esc_url( $this->get_asset_url( 'icons/favicon/android-icon-144x144.png' ) ),
					'sizes'   => '144x144',
					'type'    => 'image/png',
					'density' => '3.0',
				),
				array(
					'src'     => esc_url( $this->get_asset_url( 'icons/favicon/android-icon-192x192.png' ) ),
					'sizes'   => '192x192',
					'type'    => 'image/png',
					'density' => '4.0',
				),
				array(
					'src'     => esc_url( $this->get_asset_url( 'icons/favicon/android-icon-512x512.png' ) ),
					'sizes'   => '512x512',
					'type'    => 'image/png',
					'purpose' => 'any',
				),
				array(
					'src'     => esc_url( $this->get_asset_url( 'icons/favicon/maskable.png' ) ),
					'sizes'   => '1024x1024',
					'type'    => 'image/png',
					'purpose' => 'maskable',
				),
			),
			'splash_pages'     => null,
		);
	}

	/**
	 * Get assets URL
	 *
	 * @since 1.0.0
	 *
	 * @param string $file File name.
	 * @param string $group Group, default "images".
	 *
	 * @return string URL into asset.
	 */
	private function get_asset_url( $file, $group = 'images' ) {
		$url = sprintf(
			'%s/assets/%s/%s',
			$this->url,
			$group,
			$file
		);
		return esc_url( $url );
	}

}

