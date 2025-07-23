<?php
/**
 * Administrator implementation for iWorks PWA plugin
 * Manages all admin-related functionality including settings, notifications,
 * cache management, and plugin integration.
 *
 * @package iWorks_PWA
 * @subpackage Administrator
 * @since 1.0.0
 */

require_once dirname( __DIR__, 1 ) . '/class-iworks-pwa.php';


/**
 * Administrator class that extends the main iWorks_PWA class
 * This class manages all admin-specific functionality for the PWA plugin.
 */
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
	/**
 * The name of the WordPress pointer used for user guidance
 *
 * @since 1.4.2
 * @var string
 */
	private $pointer_name = 'iworks_pwa_browsing';

	/**
	 * Need check URLS option name
	 *
	 * @since 1.5.5
	 */
	/**
 * Option name for checking plugin URLs
 * Used to store and retrieve URL checking settings.
 *
 * @since 1.5.5
 * @var string
 */
	private $option_name_check_plugin_urls = 'check_urls';

	/**
 * Constructor for the Administrator class
 * Initializes the class and sets up WordPress hooks for admin functionality.
 * Also handles SSL warnings and plugin integration.
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
		add_action( 'admin_enqueue_scripts', array( $this, 'action_admin_enqueue_scripts' ) );
		add_action( 'wp_ajax_iworks_pwa_notice_check_url_hide', array( $this, 'action_wp_ajax_close_for_3_months' ) );
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
		 * check viewport again if plugins where been changed.
		 *
		 * @since 1.5.8
		 */
		add_action( 'update_option_active_plugins', array( $this, 'meta_viewport_delete' ) );
		/**
		 * A check for required PWA files (URLs)
		 *
		 * @since 1.5.5
		 */
		add_action( 'shutdown', array( $this, 'action_shutdown_maybe_check_requested_files' ) );
		/**
		 * check SAFE_SVG_VERSION
		 */
		add_filter( 'iworks_plugin_get_options', array( $this, 'filter_options_check_safe_svg' ), 10, 2 );
	}

	/**
	 * add inffo about Safe SVG plugin
	 *
	 * @since 1.6.4
	 */
	public function filter_options_check_safe_svg( $options, $slug ) {
		if ( 'iworks-pwa' !== $slug ) {
			return $options;
		}
		if ( ! defined( 'SAFE_SVG_VERSION' ) ) {
			$action                                   = 'install-plugin';
			$slug                                     = 'safe-svg';
			$url                                      = wp_nonce_url(
				add_query_arg(
					array(
						'action' => $action,
						'plugin' => $slug,
					),
					admin_url( 'update.php' )
				),
				$action . '_' . $slug
			);
			$options['index']['options']['apple_pti'] = wp_parse_args(
				array(
					'value'       => sprintf(
						/* translators: %s: "Safe SVG" plugin name with link */
						esc_html__( 'This field requires an SVG file. To securely upload SVG files, please install the %s plugin.', 'iworks-pwa' ),
						sprintf(
							'<a href="%s">%s</a>',
							esc_url( $url ),
							esc_html__( 'Safe SVG', 'iworks-pwa' )
						)
					),
					'type'        => 'info',
					'description' => false,
				),
				$options['index']['options']['apple_pti']
			);
		}

		return $options;
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
		printf( '<h2>%s</h2>', esc_html__( 'PWA — easy way to Progressive Web App', 'iworks-pwa' ) );
		echo wp_kses_post(
			wpautop(
				sprintf(
					/* translators: %s; url to permalinks settings */
					__( 'This plugin does not support the plain permalink structure. <a href="%s">Please change your permalinks settings</a> to other structure to use PWA plugin.', 'iworks-pwa' ),
					admin_url( 'options-permalink.php' )
				)
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
		$components = wp_parse_url( get_site_url() );
		if ( ! isset( $components['path'] ) ) {
			return;
		}
		if ( empty( $components['path'] ) ) {
			return;
		}
		echo '<div class="notice notice-error">';
		printf( '<h2>%s</h2>', esc_html__( 'PWA — easy way to Progressive Web App', 'iworks-pwa' ) );
		echo wp_kses_post(
			wpautop(
				esc_html__( 'This plugin does not support installation in a subdirectory and will not work properly.', 'iworks-pwa' )
			)
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
	 * Limit usage of some methods.
	 *
	 * @since 1.6.8
	 */
	/**
 * Checks if the current context allows certain operations to run
 * Ensures operations only run in appropriate contexts (not during autosave, AJAX, etc.).
 *
 * @since 1.6.8
 * @return bool True if operations can run, false otherwise
 */
	private function can_i_run() {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return false;
		}
		if ( is_admin() && function_exists( 'get_current_screen' ) ) {
			return 'settings_page_iworks_pwa_index' === get_current_screen()->base;
		}
		return false;
	}

	/**
	 * Clear cache
	 *
	 * @since 1.3.0
	 */
	/**
 * Clears cache when specific actions occur
 * Automatically clears cache when settings are changed or related options are updated.
 *
 * @since 1.3.0
 */
	public function clear_cache() {
		if ( ! $this->can_i_run() ) {
			return;
		}
		$key = $this->options->get_option_name( $this->settings_cache_option_name );
		delete_transient( $key );
	}

	/**
	 * After user enter settings page, there is no sens to show pointer
	 *
	 * @since 1.4.2
	 */
	/**
 * Closes the admin pointer for the current user
 * Marks the PWA browsing pointer as dismissed in user meta.
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
	/**
 * Checks for viewport meta tag on admin shutdown
 * Ensures the viewport meta tag exists and is properly configured.
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
		/**
		 * check is timestamp?
		 *
		 * @since 1.6.1
		 */
		if ( preg_match( '/^timestamp\:(\d+)$/', $value, $matches ) ) {
			if ( time() - intval( $matches[1] ) > DAY_IN_SECONDS ) {
				$this->meta_viewport_delete();
				return;
			}
		}
		/**
		 * verify nonce if is true, then leave it, do not check!
		 *
		 * @since 1.5.8
		 */
		if ( isset( $_GET['_wpnonce'] ) ) {
			if ( wp_verify_nonce( filter_input( INPUT_GET, '_wpnonce' ), 'iworks-pwa-viewport' ) ) {
				return;
			}
		}
		/**
		 * build check viewport url
		 */
		$url      = wp_nonce_url(
			add_query_arg(
				array(
					$this->option_name_check_meta_viewport => 'checking',
					'timestamp'                            => time(),
				),
				home_url(),
			),
			'iworks-pwa-viewport'
		);
		$response = wp_remote_get( $url, array( 'sslverify' => false ) );
		if ( is_wp_error( $response ) ) {
			/**
			 * set timestamp to recheck in the future
			 *
			 * @since 1.6.1
			 */
			update_option( $this->option_name_check_meta_viewport, 'timestamp:' . time() );
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
			$response = wp_remote_get( site_url( '/' . $request, array( 'sslverify' => false ) ) );
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
		if ( ! $this->can_i_run() ) {
			return;
		}
		$value = $this->options->get_option( $this->option_name_check_plugin_urls );
		if ( ! empty( $value ) && preg_match( '/^timestamp:(\d+)$/', $value, $matches ) ) {
			if ( time() > $matches[1] ) {
				$this->options->update_option( $this->option_name_check_plugin_urls, 'need-to-check', false );
			}
			return;
		}
		switch ( $value ) {
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
					$this->freeze_check_for_3_months();
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
		printf(
			'<div class="iworks-pwa-notice-check-url notice notice-error is-dismissible" data-nonce="%s" data-action="iworks_pwa_notice_check_url_hide">',
			esc_attr( wp_create_nonce( 'iworks-pwa', 'iworks-pwa' ) )
		);
		printf( '<h2>%s</h2>', esc_html__( 'ERROR: PWA — easy way to Progressive Web App', 'iworks-pwa' ) );
		echo wp_kses_post(
			wpautop(
				sprintf(
					/* translators: %s: filename as link */
					__( 'The "%s" file is no reachable.', 'iworks-pwa' ),
					sprintf(
						'<a href="%s" target="_blank">%s</a>',
						site_url( '/' . $request ),
						$request
					)
				)
			)
		);
		echo wp_kses_post(
			wpautop(
				sprintf(
					/* translators: %s: link to permalinks settings */
					__( '<a href="%s">Please change your permalinks settings</a> or server rewrite rules.', 'iworks-pwa' ),
					admin_url( 'options-permalink.php' )
				)
			)
		);
		switch ( $request ) {
			case 'ieconfig.xml':
				printf(
					'<h3>%s</h3>',
					sprintf(
					/* translators: %s: current request */
						esc_html__( 'Server rewrite rule for "%s" request', 'iworks-pwa' ),
						esc_url( $request )
					)
				);
				echo '<dl>';
				echo '<dt>apache</dt>';
				echo '<dd><pre class="code">';
				echo 'RewriteEngine on', PHP_EOL;
				echo 'RewriteCond %{QUERY_STRING} ^$', PHP_EOL;
				echo 'RewriteRule ^ieconfig\.xml$ /index.php?/ieconfig.xml [L]';
				echo '</pre></code></dd>';
				echo '<dl>';
				echo '<dt>nginx</dt>';
				echo '<dd><code>';
				echo 'rewrite ^/ieconfig.xml$ /index.php?/ieconfig.xml last;';
				echo '</code></dd>';
				echo '</dl>';
				break;
			case 'manifest.json':
				printf(
					'<h3>%s</h3>',
					sprintf(
					/* translators: %s: request */
						esc_html__( 'Server rewrite rule for "%s" request', 'iworks-pwa' ),
						esc_url( $request )
					)
				);
				echo '<dl>';
				echo '<dt>apache</dt>';
				echo '<dd><pre class="code">';
				echo 'RewriteEngine on', PHP_EOL;
				echo 'RewriteCond %{QUERY_STRING} ^$', PHP_EOL;
				echo 'RewriteRule ^manifest\.json$ /index.php?/manifest.json [L]';
				echo '</pre></code></dd>';
				echo '<dl>';
				echo '<dt>nginx</dt>';
				echo '<dd><code>';
				echo 'rewrite ^/manifest.json$ /index.php?/manifest.json last;';
				echo '</code></dd>';
				echo '</dl>';
				break;
		}
		echo '</div>';
	}

	/**
	 * Equeue script to handle closing notice.
	 *
	 * @since 1.6.5
	 */
	public function action_admin_enqueue_scripts() {
		if ( 'error' === $this->options->get_option( $this->option_name_check_plugin_urls ) ) {
			$this->admin_enqueue();
		}
	}

	/**
	 * Close notice for 3 months.
	 *
	 * @since 1.6.5
	 */
	public function action_wp_ajax_close_for_3_months() {
		if ( wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'iworks-pwa' ) ) {
			$this->freeze_check_for_3_months();
		}
	}

	/**
	 * Value to stop checking for 3 months.
	 *
	 * @since 1.6.5
	 */
	private function freeze_check_for_3_months() {
		$value = 'timestamp:' . ( time() + 3 * MONTH_IN_SECONDS );
		$this->options->update_option( $this->option_name_check_plugin_urls, $value );
	}
}

