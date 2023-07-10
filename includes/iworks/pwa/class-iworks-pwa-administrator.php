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

	/**
	 * Pointer name
	 *
	 * @since 1.4.2
	 */
	private $pointer_name = 'iworks_pwa_browsing';

	/**
	 * Need check URLS option name
	 *
	 * @since 1.5.5
	 */
	private $option_name_check_plugin_urls = 'check_urls';

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
		add_filter( 'iworks_plugin_get_options', array( $this, 'filter_maybe_add_advertising' ), 10, 2 );
		add_filter( 'iworks_pwa_administrator_debug_info', array( $this, 'filter_debug_info' ), 100 );
		add_filter( 'iworks_pwa_options', array( $this, 'filter_add_debug_urls_to_config' ) );
		/**
		 * WordPress Hooks
		 */
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_notices', array( $this, 'action_admin_notices_check_permalinks' ) );
		add_action( 'admin_notices', array( $this, 'action_admin_notices_check_subdirectory' ) );
		add_action( 'admin_notices', array( $this, 'action_admin_notices_maybe_show_check_url_error' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_admin_pointer' ) );
		add_action( 'update_option_rewrite_rules', array( $this, 'action_update_option_rewrite_rules_set_to_check_urls' ), PHP_INT_MAX );
		/**
		 * change logo for rate
		 */
		add_filter( 'iworks_rate_notice_logo_style', array( $this, 'filter_plugin_logo' ), 10, 2 );
		/**
		 * check for OG plugin & integrate
		 *
		 * @since 1.1.0
		 */
		$this->check_og_plugin();
		/**
		 * Clear cache
		 *
		 * @since 1.3.0
		 */
		add_action( 'load-settings_page_iworks_pwa_index', array( $this, 'clear_cache' ) );
		add_action( 'load-settings_page_iworks_pwa_index', array( $this, 'close_pointer' ) );
		add_action( 'save_post', array( $this, 'clear_cache' ) );
		add_action( 'update_option_active_plugins', array( $this, 'clear_cache' ) );
		add_action( 'update_option_blogdescription', array( $this, 'clear_cache' ) );
		add_action( 'update_option_blogname', array( $this, 'clear_cache' ) );
		add_action( 'update_option_db_version', array( $this, 'clear_cache' ) );
		add_action( 'update_option_home', array( $this, 'clear_cache' ) );
		add_action( 'update_option_page_for_posts', array( $this, 'clear_cache' ) );
		add_action( 'update_option_page_on_front', array( $this, 'clear_cache' ) );
		add_action( 'update_option_permalink_structure', array( $this, 'clear_cache' ) );
		add_action( 'update_option_show_on_front', array( $this, 'clear_cache' ) );
		add_action( 'update_option_site_icon', array( $this, 'clear_cache' ) );
		add_action( 'update_option_siteurl', array( $this, 'clear_cache' ) );
		add_action( 'update_option_stylesheet', array( $this, 'clear_cache' ) );
		/**
		 * check meta viewport actions
		 *
		 * @since 1.5.1
		 */
		add_action( 'after_switch_theme', array( $this, 'meta_viewport_delete' ) );
		add_action( 'shutdown', array( $this, 'meta_viewport_check' ) );
		/**
		 * A check for required PWA files (URLs)
		 *
		 * @since 1.5.5
		 */
		add_action( 'shutdown', array( $this, 'action_shutdown_maybe_check_requested_files' ) );
	}

	public function filter_add_debug_urls_to_config( $options ) {
		if ( ! is_array( $options ) ) {
			return $options;
		}
		if ( ! $this->debug ) {
			return $options;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $options;
		}
		if ( ! is_admin() ) {
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
		$content .= sprintf( $row, site_url( '/manifest.json' ), esc_html__( 'manifest.json', 'iworks-pwa' ) );
		$content .= sprintf( $row, site_url( '/iworks-pwa-service-worker-js' ), esc_html__( 'Service Worker', 'iworks-pwa' ) );
		$content .= sprintf( $row, site_url( '/iworks-pwa-offline' ), esc_html__( 'Offline page', 'iworks-pwa' ) );
		$content .= sprintf( $row, site_url( '/ieconfig.xml' ), esc_html__( 'IE config xml', 'iworks-pwa' ) );
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

	/**
	 * Add pointer for fresh install
	 */
	public function print_admin_pointer() {
		// Skip showing admin pointer if not relevant.
		if (
			'options-general' === get_current_screen()->id
			|| 'settings_page_iworks_pwa_index' === get_current_screen()->id
			|| ! current_user_can( 'manage_options' )
		) {
			return;
		}
		// Skip showing admin pointer if dismissed.
		$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		if ( in_array( $this->pointer_name, $dismissed_pointers, true ) ) {
			return;
		}
		wp_print_scripts( array( 'wp-pointer' ) );
		wp_print_styles( array( 'wp-pointer' ) );
		$content  = '<h3>' . esc_html__( 'PWA', 'iworks-pwa' ) . '</h3>';
		$content .= '<p>' . esc_html__( 'PWA settings are now available in Settings.', 'iworks-pwa' ) . '</p>';
		$args     = array(
			'content'  => $content,
			'position' => array(
				'align' => 'middle',
				'edge'  => is_rtl() ? 'right' : 'left',
			),
		);

		?>
<script>
jQuery( function( $ ) {
	const menuSettingsItem = $( '#menu-settings' );
	const readingSettingsItem = menuSettingsItem.find( 'li:has( a[href="options-general.php"] )' );
	if ( readingSettingsItem.length === 0 ) {
		return;
	}
	const options = $.extend( <?php echo wp_json_encode( $args ); ?>, {
		close: function() {
			$.post( ajaxurl, {
			pointer: <?php echo wp_json_encode( $this->pointer_name ); ?>,
				action: 'dismiss-wp-pointer'
			});
		}
	});
	let target = menuSettingsItem;
	if ( menuSettingsItem.hasClass( 'wp-menu-open' ) ) {
		target = readingSettingsItem;
	}
	target.pointer( options ).pointer( 'open' );
} );
</script>
		<?php
	}

	/**
	 * add messsage when permalinks area "plain"
	 *
	 * @since 1.2.1
	 */
	public function action_admin_notices_check_permalinks() {
		$permalink_structure = get_option( 'permalink_structure' );
		if ( ! empty( $permalink_structure ) ) {
			return;
		}
		echo '<div class="notice notice-error">';
		printf( '<h2>%s</h2>', esc_html__( 'PWA — simple way to Progressive Web App', 'iworks-pwa' ) );
		echo wpautop(
			sprintf(
				__( 'This plugin does not support the plain permalink structure. <a href="%s">Please change your permalinks settings</a> to other structure to use PWA plugin.', 'iworks-pwa' ),
				admin_url( 'options-permalink.php' )
			)
		);
		echo '</div>';
	}

	/**
	 * add messsage when site is in directory
	 *
	 * @since 1.4.0
	 */
	public function action_admin_notices_check_subdirectory() {
		$components = parse_url( get_site_url() );
		if ( ! isset( $components['path'] ) ) {
			return;
		}
		if ( empty( $components['path'] ) ) {
			return;
		}
		echo '<div class="notice notice-error">';
		printf( '<h2>%s</h2>', esc_html__( 'PWA — simple way to Progressive Web App', 'iworks-pwa' ) );
		echo wpautop(
			__( 'This plugin does not support installation in a subdirectory and will not work properly.', 'iworks-pwa' )
		);
		echo '</div>';
	}

	/**
	 * Filter options for some advertising
	 *
	 * @since 1.2.2
	 */
	public function filter_maybe_add_advertising( $options, $plugin ) {
		if ( 'iworks-pwa' !== $plugin ) {
			return $options;
		}
		if ( ! isset( $options['index']['metaboxes'] ) ) {
			$options['index']['metaboxes'] = array();
		}
		if ( ! $this->is_og_installed ) {
			$data = apply_filters( 'iworks_rate_advertising_og', array() );
			if ( ! empty( $data ) ) {
				$options['index']['metaboxes'] = array_merge( $options['index']['metaboxes'], $data );
			}
		}
		return $options;
	}

	/**
	 * Clear cache
	 *
	 * @since 1.3.0
	 */
	public function clear_cache() {
		$key = $this->options->get_option_name( $this->settings_cache_option_name );
		delete_transient( $key );
	}

	/**
	 * After user enter settings page, there is no sens to show pointer
	 *
	 * @since 1.4.2
	 */
	public function close_pointer() {
		$dismissed_pointers = array_filter(
			explode(
				',',
				(string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true )
			)
		);
		if ( in_array( $this->pointer_name, $dismissed_pointers, true ) ) {
			return;
		}
		$dismissed_pointers[] = $this->pointer_name;
		update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', implode( ',', $dismissed_pointers ) );
	}

	/**
	 * check meta viewport - on shutdown
	 *
	 * @since 1.5.1
	 */
	public function meta_viewport_check() {
		if ( ! is_admin() ) {
			return;
		}
		$value = get_option( $this->option_name_check_meta_viewport );
		if ( ! empty( $value ) ) {
			return;
		}
		if ( isset( $_GET[ $this->option_name_check_meta_viewport ] ) ) {
			return;
		}
		$url      = add_query_arg(
			array(
				$this->option_name_check_meta_viewport => 'checking',
				'timestamp'                            => time(),
			),
			home_url()
		);
		$response = wp_remote_get( $url, array( 'sslverify' => false ) );
		if ( is_wp_error( $response ) ) {
			return;
		}
		$html = wp_remote_retrieve_body( $response );
		if ( preg_match_all( '/<meta[^>]+/', $html, $matches ) ) {
			foreach ( $matches[0] as $one ) {
				if (
					preg_match( '/name=["\']viewport["\']/', $one )
					&& preg_match( '/initial-scale/', $one )
				) {
					update_option( $this->option_name_check_meta_viewport, 'valid' );
					return;
				}
			}
		}
		update_option( $this->option_name_check_meta_viewport, 'missing' );
	}

	/**
	 * delete meta viewport - on after_switch_theme
	 *
	 * @since 1.5.1
	 */
	public function meta_viewport_delete() {
		delete_option( $this->option_name_check_meta_viewport );
	}

	/**
	 * Check single URL
	 *
	 * @since 1.5.5
	 */
	private function check_url( $request ) {
		$success = true;
		if ( $success ) {
			add_filter( 'https_ssl_verify', '__return_false' );
			$response = wp_remote_get( site_url( '/' . $request ) );
			if ( is_wp_error( $response ) ) {
				return false;
			}
		}
		if ( $success ) {
			$success = 200 === wp_remote_retrieve_response_code( $response );
		}
		if ( ! $success ) {
			$this->options->update_option( $this->option_name_check_plugin_urls, 'error' );
			$this->options->update_option( $this->option_name_check_plugin_urls . '_error', $request );
		}
		return $success;
	}


	/**
	 * Check URLS
	 *
	 * @since 1.5.5
	 */
	public function action_shutdown_maybe_check_requested_files() {
		if ( ! is_admin() ) {
			return;
		}
		switch ( $this->options->get_option( $this->option_name_check_plugin_urls ) ) {
			case 'need-to-check';
				$this->options->update_option( $this->option_name_check_plugin_urls, 'need-to-check-manifest-json' );
			break;
			case 'need-to-check-manifest-json':
				$file = 'manifest.json';
				if ( $this->check_url( $file ) ) {
					$this->options->update_option( $this->option_name_check_plugin_urls, 'need-to-check-iworks-pwa-service-worker-js' );
				}
				break;
			case 'need-to-check-iworks-pwa-service-worker-js':
				$file = 'iworks-pwa-service-worker-js';
				if ( $this->check_url( $file ) ) {
					$this->options->update_option( $this->option_name_check_plugin_urls, 'need-to-check-iworks-pwa-offline' );
				}
				break;
			case 'need-to-check-iworks-pwa-offline':
				$file = 'iworks-pwa-service-worker-js';
				if ( $this->check_url( $file ) ) {
					$this->options->update_option( $this->option_name_check_plugin_urls, 'need-to-check-ieconfig-xml' );
				}
				break;
			case 'need-to-check-ieconfig-xml':
				$file = 'ieconfig.xml';
				if ( $this->check_url( $file ) ) {
					$this->options->update_option( $this->option_name_check_plugin_urls, 'done' );
				}
				break;
			case 'error':
				break;
			default:
				$this->options->add_option( $this->option_name_check_plugin_urls, 'need-to-check', false );
		}
	}

	/**
	 * Re-Check URLS after rewrite_rules
	 *
	 * @since 1.5.5
	 */
	public function action_update_option_rewrite_rules_set_to_check_urls() {
		$this->options->update_option( $this->option_name_check_plugin_urls, null );
	}

	/**
	 * add messsage when check URLs failed
	 *
	 * @since 1.5.5
	 */
	public function action_admin_notices_maybe_show_check_url_error() {
		if ( 'error' !== $this->options->get_option( $this->option_name_check_plugin_urls ) ) {
			return;
		}
		$request = $this->options->get_option( $this->option_name_check_plugin_urls . '_error' );
		echo '<div class="notice notice-error">';
		printf( '<h2>%s</h2>', esc_html__( 'ERROR: PWA — simple way to Progressive Web App', 'iworks-pwa' ) );
		echo wpautop(
			sprintf(
				__( 'The "%s" file is no reachable.', 'iworks-pwa' ),
				sprintf(
					'<a href="%s" target="_blank">%s</a>',
					site_url( '/' . $request ),
					$request
				)
			)
		);
		echo wpautop(
			sprintf(
				__( '<a href="%s">Please change your permalinks settings</a> or server rewrite rules.', 'iworks-pwa' ),
				admin_url( 'options-permalink.php' )
			)
		);
		echo '</div>';
	}
}

