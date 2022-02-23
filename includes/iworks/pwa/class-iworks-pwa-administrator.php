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
		add_filter( 'iworks_plugin_get_options', array( $this, 'filter_maybe_add_advertising' ), 10, 2 );
		/**
		 * WordPress Hooks
		 */
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'print_admin_pointer' ) );
		add_action( 'admin_notices', array( $this, 'check_permalinks' ) );
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
		$pointer = 'iworks_pwa_browsing';
		// Skip showing admin pointer if dismissed.
		$dismissed_pointers = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
		if ( in_array( $pointer, $dismissed_pointers, true ) ) {
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
			pointer: <?php echo wp_json_encode( $pointer ); ?>,
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
	public function check_permalinks() {
		$permalink_structure = get_option( 'permalink_structure' );
		if ( ! empty( $permalink_structure ) ) {
			return;
		}
		echo '<div class="notice notice-error">';
		echo wpautop(
			sprintf(
				__( 'PWA plugin uses WordPress does not support the plain permalink structure. <a href="%s">Please change your permalinks settings</a> to other structure to use PWA plugin.', 'iworks-pwa' ),
				admin_url( 'options-permalink.php' )
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

}

