<?php


abstract class iWorks_PWA {

	protected $configuration = array();

	protected $url;

	protected $debug = false;

	protected $version = 'PLUGIN_VERSION';

	protected $root = '';

	private $icons = array();

	private $media_dir_name = 'pwa';

	protected $icons_to_flush;

	protected $option_name_icons;

	/**
	 * iWorks Options object
	 *
	 * @since 1.0.1
	 */
	protected $options;

	/**
	 * End of line
	 *
	 * @since 1.1.1
	 */
	protected $eol;

	/**
	 * Check for OG plugin: https://wordpress.org/plugins/og/
	 *
	 * @since 1.2.2
	 */
	protected $is_og_installed = null;

	/**
	 * Settigs cache option name
	 *
	 * @since 1.3.0
	 */
	protected $settings_cache_option_name = 'cache';

	protected function __construct() {
		$file        = dirname( dirname( __FILE__ ) );
		$this->url   = rtrim( plugin_dir_url( $file ), '/' );
		$this->root  = rtrim( plugin_dir_path( $file ), '/' );
		$this->debug = defined( 'WP_DEBUG' ) && WP_DEBUG;
		/**
		 * End of line
		 */
		$this->eol = $this->debug ? PHP_EOL : '';
		/**
		 * set options
		 */
		$this->options = get_iworks_pwa_options();
		$this->_set_configuration();
		/**
		 * icons
		 */
		$this->icons = $this->options->get_group( 'icons' );
		/**
		 * integrations wiith external plugins
		 *
		 * @since 1.2.0
		 */
		add_action( 'plugins_loaded', array( $this, 'maybe_load_integrations' ) );
		/**
		 * clear cache
		 *
		 * @since 1.3.0
		 */
		add_action( 'load-settings_page_iworks_pwa_index', array( $this, 'action_cache_clear' ) );
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

	protected function get_configuration() {
		if ( empty( $this->configuration ) ) {
			$this->_set_configuration();
		}
		return $this->configuration;
	}

	private function _set_configuration() {
		$key   = $this->options->get_option_name( $this->settings_cache_option_name );
		$value = get_transient( $key );
		if ( empty( $value ) ) {
			$value = apply_filters(
				'iworks_pwa_configuration',
				array(
					'plugin'           => 'PLUGIN_TITLE - PLUGIN_VERSION',
					'name'             => $this->get_configuration_name(),
					'short_name'       => $this->get_configuration_short_name(),
					'description'      => $this->get_configuration_description(),
					'theme_color'      => $this->get_configuration_color_theme(),
					'background_color' => $this->get_configuration_color_background(),
					'orientation'      => $this->get_configuration_orientation(),
					'display'          => $this->get_configuration_display(),
					'Scope'            => apply_filters( 'iworks_pwa_configuration_Scope', '/' ),
					'start_url'        => apply_filters( 'iworks_pwa_configuration_start_url', '/' ),
					'splash_pages'     => apply_filters( 'iworks_pwa_configuration_splash_pages', null ),
					'icons'            => $this->get_configuration_icons(),
				)
			);
			if ( ! is_admin() ) {
				set_transient( $key, $value, DAY_IN_SECONDS );
			}
		}
		$this->configuration = apply_filters( 'iworks_pwa_configuration_raw', $value );
	}

	protected function get_icons_base_url() {
		$dir = wp_get_upload_dir();
		return $dir['baseurl'] . '/' . $this->media_dir_name;
	}

	protected function get_icons_directory() {
		$dir = wp_get_upload_dir();
		$dir = $dir['basedir'] . '/' . $this->media_dir_name;
		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}
		return $dir;
	}

	protected function image_resize_and_save( $image, $width, $destfilename ) {
		$image->resize( $width, $width );
		if ( is_file( $destfilename ) ) {
			unlink( $destfilename );
		}
		return $image->save( $destfilename );
	}

	protected function get_image_ext_from_attachement_id( $attachement_id ) {
		return pathinfo( wp_get_original_image_path( $attachement_id ), PATHINFO_EXTENSION );
	}

	protected function get_wp_image_object_from_attachement_id( $attachement_id ) {
		$path  = wp_get_original_image_path( $attachement_id );
		$args  = array(
			'path'      => $path,
			'mime_type' => get_post_mime_type( $attachement_id ),
		);
		$image = wp_get_image_editor( $path, $args );
		if ( ! is_wp_error( $image ) ) {
			$size = min( $image->get_size() );
			$image->resize( $size, $size, true );
		}
		return $image;
	}

	/**
	 * Defaults icon set
	 *
	 * @since 1.3.3
	 */
	private function get_defaults_icons() {
		$root  = sprintf( '%s/assets/images/icons/favicon', $this->url );
		$icons = apply_filters(
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
		);
		return $icons;
	}

	protected function get_configuration_icons( $group = 'manifest' ) {
		$value = intval( $this->options->get_option( 'icon_app' ) );
		if ( empty( $value ) ) {
			return $this->get_defaults_icons();
		}
		/**
		 * Handle cache
		 *
		 * @since 1.3.0
		 */
		$cache_key = sprintf(
			'%s_%s',
			$this->settings_cache_option_name,
			$group
		);
		/**
		 * cache get
		 */
		$icons = get_transient( $cache_key );
		if ( ! empty( $icons ) ) {
			return apply_filters( 'iworks_pwa_configuration_icons', $icons );
		}
		$icons_option_name = 'icons_' . $group;
		$icons             = $this->options->get_option( $icons_option_name );
		$root              = $this->get_icons_directory();
		if ( ! empty( $icons ) ) {
			$icons = $this->maybe_add_purpose_maskable( $icons );
			/**
			 * Handle cache
			 *
			 * @since 1.3.0
			 */
			set_transient( $cache_key, $icons, DAY_IN_SECONDS );
			return apply_filters( 'iworks_pwa_configuration_icons', $icons );
		}
		if ( 0 < $value ) {
			$image = $this->get_wp_image_object_from_attachement_id( $value );
			if ( ! is_wp_error( $image ) ) {
				$size = min( $image->get_size() );
				$ext  = $this->get_image_ext_from_attachement_id( $value );
				krsort( $this->icons );
				foreach ( $this->icons as $width => $data ) {
					$width = intval( $width );
					if ( $width > $size ) {
						continue;
					}
					if ( ! in_array( $group, $data['group'] ) ) {
						continue;
					}
					$name         = sprintf( 'icon-pwa-%s.%s', $width, $ext );
					$destfilename = $this->get_icons_directory() . '/' . $name;
					$result       = $this->image_resize_and_save( $image, $width, $destfilename );
					if ( ! is_wp_error( $result ) ) {
						$one             = $data;
						$one['src']      = sprintf(
							'%s/%s?v=%s',
							$this->get_icons_base_url(),
							$name,
							time()
						);
						$icons[ $width ] = $one;
					}
				}
			}
		}
		if ( ! empty( $icons ) ) {
			$icons = $this->maybe_add_purpose_maskable( $icons );
			$this->options->update_option( $icons_option_name, $icons );
			/**
			 * Handle cache
			 *
			 * @since 1.3.0
			 */
			set_transient( $cache_key, $icons, DAY_IN_SECONDS );
			return apply_filters( 'iworks_pwa_configuration_icons', $icons );
		}
		/**
		 * defaults for empty, only for basic
		 */
		if ( 'manifest' !== $group ) {
			return apply_filters( 'iworks_pwa_configuration_icons', array() );
		}
		$icons = $this->get_defaults_icons();
		/**
		 * Handle cache
		 *
		 * @since 1.3.0
		 */
		set_transient( $cache_key, $icons, MINUTE_IN_SECONDS );
		return apply_filters( 'iworks_pwa_configuration_icons', $icons );
	}

	protected function get_name( $name ) {
		return preg_replace( '/_/', '-', strtolower( $name ) );
	}

	/**
	 * get background color (meta: theme-color)
	 *
	 * @since 0.0.2
	 */
	protected function get_configuration_color_background() {
		$color = $this->options->get_option( 'color_bg' );
		if ( empty( $color ) ) {
			$color = get_theme_mod( 'background_color', 'f0f0f0' );
			if ( preg_match( '/^[0-9a-f]+$/', $color ) ) {
				$color = '#' . $color;
			}
		}
		return apply_filters( 'iworks_pwa_configuration_background_color', $color );
	}

	/**
	 * get color theme
	 */
	protected function get_configuration_color_theme() {
		$color = $this->options->get_option( 'color_theme' );
		if ( empty( $color ) ) {
			$color = $this->get_color_background();
		}
		return apply_filters( 'iworks_pwa_configuration_theme_color', $color );
	}

	/**
	 * get application name
	 */
	protected function get_configuration_name() {
		$value = $this->options->get_option( 'app_name' );
		if ( empty( $value ) ) {
			$value = get_bloginfo( 'name' );
		}
		return apply_filters( 'iworks_pwa_configuration_name', $value );
	}

	/**
	 * get application short name
	 */
	protected function get_configuration_short_name() {
		$value = $this->options->get_option( 'app_short_name' );
		if ( empty( $value ) ) {
			$value = get_bloginfo( 'name' );
		}
		return apply_filters( 'iworks_pwa_configuration_short_name', $value );
	}

	/**
	 * get application description
	 */
	protected function get_configuration_description() {
		$value = $this->options->get_option( 'app_description' );
		if ( empty( $value ) ) {
			$value = get_bloginfo( 'description' );
		}
		return apply_filters( 'iworks_pwa_configuration_description', $value );
	}

	protected function get_configuration_orientation() {
		$value = $this->options->get_option( 'app_orientation' );
		if ( empty( $value ) ) {
			$value = 'portrait';
		}
		$options = $this->options->get_values( 'app_orientation' );
		if ( ! array_key_exists( $value, $options ) ) {
			$value = 'portrait';
		}
		return apply_filters( 'iworks_pwa_configuration_orientation', $value );
	}

	protected function get_configuration_display() {
		$value = $this->options->get_option( 'app_display' );
		if ( empty( $value ) ) {
			$value = 'standalone';
		}
		$options = $this->options->get_values( 'app_display' );
		if ( ! array_key_exists( $value, $options ) ) {
			$value = 'standalone';
		}
		return apply_filters( 'iworks_pwa_configuration_display', $value );
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

	/**
	 * maybe load integrations
	 *
	 * @since 1.2.0
	 */
	public function maybe_load_integrations() {
		$plugins = get_option( 'active_plugins' );
		if ( empty( $plugins ) ) {
			return;
		}
		$root = dirname( __file__ ) . '/pwa';
		include_once $root . '/class-iworks-pwa-integrations.php';
		$root .= '/integrations';
		foreach ( $plugins as $plugin ) {
			/**
			 * WPML
			 * https://wpml.org
			 *
			 * @since 1.2.0
			 */
			if ( preg_match( '/sitepress\.php$/', $plugin ) ) {
				include_once $root . '/class-iworks-pwa-integrations-wpml.php';
				new iWorks_PWA_Integrations_WPML( $this->options );
			}
			/**
			 * OG — Better Share on Social Media
			 * https://wordpress.org/plugins/og/
			 *
			 * @since 1.2.2
			 */
			if ( preg_match( '/og\.php$/', $plugin ) ) {
				include_once $root . '/class-iworks-pwa-integrations-og.php';
				new iWorks_PWA_Integrations_OG( $this->options );
			}
		}
	}

	protected function get_configuration_offline_page_content() {
		$content = apply_filters(
			'iworks_pwa_configuration_offline_page_content',
			$this->options->get_option( 'offline_content' )
		);
		if ( empty( $content ) ) {
			$content  = '';
			$content .= __( 'We were unable to load the page you requested.', 'iworks-pwa' );
			$content .= PHP_EOL;
			$content .= PHP_EOL;
			$content .= __( 'Please check your network connection and try again.', 'iworks-pwa' );
		}
		$content = wpautop( $content );
		return apply_filters( 'iworks_pwa_offline_content', $content );
	}

	/**
	 * check for OG plugin
	 *
	 * @since 1.2.2
	 */
	public function check_og_plugin() {
		if ( null !== $this->is_og_installed ) {
			return;
		}
		$this->is_og_installed = false;
		$plugins               = get_option( 'active_plugins' );
		if ( empty( $plugins ) ) {
			return;
		}
		foreach ( $plugins as $plugin ) {
			if ( preg_match( '/og\.php$/', $plugin ) ) {
				$this->is_og_installed = true;
				return;
			}
		}
	}

	public function action_flush_icons( $old_value, $value, $option ) {
		delete_option( $this->options->get_option_name( $this->option_name_icons ) );
	}

	/**
	 * Try to add purpose "any maskable" if it is not present
	 *
	 * @since 1.2.2
	 */
	private function maybe_add_purpose_maskable( $icons ) {
		$max = 0;
		foreach ( $icons as $size => $icon ) {
			if ( isset( $icon['purpose'] ) && preg_match( '/maskable/', $icon['purpose'] ) ) {
				return $icons;
			}
			if ( $size > $max ) {
				$max = $size;
			}
		}
		if ( 0 < $max ) {
			$icons[ $max ]['purpose'] = 'any maskable';
		}
		return $icons;
	}

	/**
	 * clear cache
	 *
	 * @since 1.3.0
	 */
	public function action_cache_clear() {
		$keys = array(
			$this->settings_cache_option_name,
			$this->settings_cache_option_name . '_manifest',
			$this->settings_cache_option_name . '_widnows8',
			$this->settings_cache_option_name . '_ie11',
		);
		foreach ( $keys as $cache_key ) {
			delete_transient( $cache_key );
		}
	}

}
