<?php
/**
 *
 * @since 1.0.0
 */

require_once dirname( dirname( __FILE__ ) ) . '/class-iworks-pwa.php';


class iWorks_PWA_manifest extends iWorks_PWA {

	/**
	 * Menu ID used in `manifest.json` as `shortcuts`.
	 *
	 * @since 1.4.0
	 */
	private $menu_location_id = 'iworks-pwa-shortcuts';

	/**
	 * Meta name for `manifest.json` short_name value.
	 *
	 * @since 1.4.0
	 */
	private $meta_option_name_sort_menu_name = 'iworks-pwa-short-name';

	public function __construct() {
		parent::__construct();
		/**
		 * handle special requests
		 */
		add_action( 'parse_request', array( $this, 'parse_request' ) );
		/**
		 * js
		 */
		add_action( 'after_setup_theme', array( $this, 'action_after_setup_theme_register_menu' ), PHP_INT_MAX );
		add_action( 'init', array( $this, 'register_scripts' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue' ), PHP_INT_MAX );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), PHP_INT_MAX );
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'action_wp_nav_menu_item_custom_fields_add_short_name' ), 10, 5 );
		add_action( 'wp_update_nav_menu', array( $this, 'action_wp_update_nav_menu_create_pwa_shortcuts' ), 10, 2 );
		add_action( 'wp_update_nav_menu', array( $this, 'action_wp_update_nav_menu_save' ), 10, 2 );
		/**
		 * Clear generated icons
		 *
		 * @since 1.0.1
		 */
		$option_name = $this->options->get_option_name( 'icon_app' );
		add_action( 'update_option_' . $option_name, array( $this, 'action_flush_icons' ), 10, 3 );
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
			'serviceWorkerUri' => add_query_arg(
				'version',
				$this->options->get_option( 'cache_version' ),
				wp_make_link_relative(
					home_url(
						apply_filters( 'iworks_pwa_service_worker_uri', $this->options->get_group( 'service-worker-handler' ) )
					)
				)
			),
			'root'             => $this->url . '/assets/pwa/',
		);
		wp_localize_script(
			$this->get_name( __CLASS__ ),
			'iworks_pwa',
			apply_filters( 'wp_localize_script_iworks_pwa_manifest', $data )
		);
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue() {
		wp_enqueue_script( $this->get_name( __CLASS__ ) );
	}

	public function parse_request() {
		if (
			! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}
		$uri = remove_query_arg( array_keys( $_GET ), $_SERVER['REQUEST_URI'] );
		/**
		 * manifest.json
		 */
		if ( $this->is_manifest_json_request( $uri ) ) {
			$this->print_manifest_json();
			return;
		}
		/**
		 * service-worker
		 */
		if ( $this->is_service_worker_request( $uri ) ) {
				$this->print_iworks_pwa_service_worker_js();
			return;
		}
		/**
		 * offline page
		 */
		if ( $this->is_offline_page_request( $uri ) ) {
			$this->print_iworks_pwa_offline();
			return;
		}
	}

	private function print_iworks_pwa_offline() {
		header( 'Content-Type: text/html' );
		$data = apply_filters( 'iworks_pwa_offline_file', null );
		if ( empty( $data ) ) {
			$data = file_get_contents( $this->root . '/assets/pwa/offline.html' );
		}
		/**
		 * WP
		 */
		$data = preg_replace( '/%HTML_LANGUAGE_ATTRIBUTES%/', get_language_attributes( 'html' ), $data );
		$data = preg_replace( '/%CHARSET%/', get_bloginfo( 'charset' ), $data );
		/**
		 * title
		 */
		$data = preg_replace( '/%SORRY%/', apply_filters( 'iworks_pwa_offline_sorry', __( 'Sorry!', 'iworks-pwa' ) ), $data );
		$data = preg_replace( '/%NAME%/', $this->configuration['name'], $data );
		/**
		 * content
		 */
		$data = preg_replace( '/%CONTENT%/', $this->get_configuration_offline_page_content(), $data );
		/**
		 * SVG
		 */
		$svg  = '<svg viewBox="0, 0, 24, 24"><path d="M23.64 7c-.45-.34-4.93-4-11.64-4-1.5 0-2.89.19-4.15.48L18.18 13.8 23.64 7zm-6.6 8.22L3.27 1.44 2 2.72l2.05 2.06C1.91 5.76.59 6.82.36 7l11.63 14.49.01.01.01-.01 3.9-4.86 3.32 3.32 1.27-1.27-3.46-3.46z"></path></svg>';
		$data = preg_replace( '/%SVG%/', apply_filters( 'iworks_pwa_offline_svg', $svg ), $data );
		/**
		 * print
		 */
		echo $data;
		exit;
	}

	private function helper_join_strings( $string ) {
		return sprintf(
			"'%s'",
			preg_replace( "/'/", "\\'", $string )
		);
	}

	private function print_iworks_pwa_service_worker_js() {
		header( 'Content-Type: text/javascript' );
		$set = array(
			home_url(),
		);
		$url = get_privacy_policy_url();
		if ( ! empty( $url ) ) {
			$set[] = $url;
		}
		$set = array_unique( apply_filters( 'iworks_pwa_offline_urls_set', $set ) );
		if ( empty( $set ) ) {
			$set = '';
		} else {
			$set = implode( ', ', array_map( array( $this, 'helper_join_strings' ), $set ) );
		}
		$file = $this->root . '/assets/templates/service-worker.js';
		$args = array(
			'cache_name'       => sprintf(
				'%s-%d',
				apply_filters( 'iworks_pwa_offline_cache_name', $this->options->get_group( 'cache-name' ) ),
				apply_filters( 'iworks_pwa_offline_version', $this->options->get_option( 'cache_version' ) )
			),
			'offline_urls_set' => $set,
			'offline_url'      => 'iworks-pwa-offline',
		);
		load_template( $file, true, $args );
		exit;
	}

	/**
	 * Handle "/manifest.json" request.
	 *
	 * @since 1.0.0
	 */
	private function print_manifest_json() {
		$data          = $this->get_configuration();
		$icons         = $data['icons'];
		$data['icons'] = array();
		$allowed_keys  = array( 'sizes', 'type', 'density', 'src', 'purpose' );
		foreach ( $icons as $one ) {
			foreach ( array_keys( $one ) as $key ) {
				if ( in_array( $key, $allowed_keys ) ) {
					continue;
				}
				unset( $one[ $key ] );
			}
			array_unshift( $data['icons'], $one );
		}
		/**
		 * Check shortcuts and use it obnly when not empty.
		 *
		 * @since 1.5.7
		 */
		$shortcuts = $this->get_shortcuts();
		if ( ! empty( $shortcuts ) && is_array( $shortcuts ) ) {
			$data['shortcuts'] = $shortcuts;
		}
		/**
		 * app id
		 */
		$data['id'] = $this->get_configuration_app_id();
		header( 'Content-Type: application/json' );
		/**
		 * filter manifest data
		 */
		$data = apply_filters(
			/**
			 * Allow to change manifest.json data.
			 *
			 * @since 1.6.0
			 *
			 * @param array
			 */
			'iworks-pwa/manifest/data',
			$data
		);
		echo json_encode( $data );
		exit;
	}

	public function filter_iworks_pwa_flush_icons_list( $list ) {
		$list[] = 'icons_manifest';
		return $list;
	}

	private function is_manifest_json_request( $uri ) {
		if ( '/manifest.json' === $uri ) {
			return true;
		}
		return apply_filters( 'iworks_pwa_manifest_is_manifest_json_request', false, $uri );
	}

	private function is_service_worker_request( $uri ) {
		if ( '/' . $this->options->get_group( 'service-worker-handler' ) === $uri ) {
			return true;
		}
		return apply_filters( 'iworks_pwa_manifest_is_service_worker_request', false, $uri );
	}

	private function is_offline_page_request( $uri ) {
		if ( '/' . $this->options->get_group( 'offline-page' ) === $uri ) {
			return true;
		}
		return apply_filters( 'iworks_pwa_manifest_is_offline_page_request', false, $uri );
	}

	/**
	 * Register menu for manifest shortcuts
	 *
	 * @since 1.4.0
	 */
	public function action_after_setup_theme_register_menu() {
		register_nav_menu( $this->menu_location_id, __( 'PWA Shortcuts Menu', 'iworks-pwa' ) );
	}

	/**
	 * get shortcuts for manifest.json
	 *
	 * @since 1.4.0
	 */
	private function get_shortcuts() {
		return apply_filters(
			/**
			 * aloow to change shortcuts array
			 *
			 * @since 1.5.7
			 *
			 * @param array
			 */
			'iworks-pwa/manifest/shortcuts',
			$this->options->get_option( $this->menu_location_id )
		);
	}

	public function action_wp_update_nav_menu_create_pwa_shortcuts( $menu_id, $menu_data = array() ) {
		$this->options->update_option( $this->menu_location_id, array() );
		$locations = get_nav_menu_locations( $menu_id );
		if ( empty( $locations ) ) {
			return;
		}
		if ( ! array_key_exists( $this->menu_location_id, $locations ) ) {
			return;
		}
		$items = wp_get_nav_menu_items( $menu_id );
		if ( empty( $items ) ) {
			return;
		}
		$shortcuts = array();
		foreach ( $items as $one ) {
			$element = array(
				'name' => $one->title,
				'url'  => add_query_arg(
					apply_filters(
						/**
						 * aloow to change shortcut url campain
						 *
						 * @since 1.5.7
						 *
						 * @param array
						 */
						'iworks-pwa/manifest/shortcuts/element/url/campain',
						array(
							'utm_source'   => 'manifest.json',
							'utm_medium'   => 'application',
							'utm_campaign' => 'iworks-pwa',
						)
					),
					wp_make_link_relative( $one->url )
				),
			);
			if ( ! empty( $one->description ) ) {
				$element['description'] = $one->description;
			}
			$value = get_post_meta( $one->ID, $this->meta_option_name_sort_menu_name, true );
			if ( ! empty( $value ) ) {
				$element['short_name'] = $value;
			}
			if ( 'post_type_archive' === $one->type ) {
				$el = get_post_type_object( $one->object );
				if ( ! empty( $el->menu_icon ) && wp_http_validate_url( $el->menu_icon ) ) {
					$element['icons'] = array(
						array(
							'src' => $el->menu_icon,
						),
					);
				}
			}
			$shortcuts[] = apply_filters( 'iworks_pwa_manifest_shortcut_element', $element, $one );
		}
		$this->options->update_option( $this->menu_location_id, $shortcuts );
	}

	/**
	 * Add field short_name form `manifest.json` shortcuts.
	 *
	 * @since 1.4.0
	 */
	public function action_wp_nav_menu_item_custom_fields_add_short_name( $item_id, $menu_item, $depth = 0, $args = null, $current_object_id = 0 ) {
		global $nav_menu_selected_id;
		global $menu_locations;
		if ( ! is_array( $menu_locations ) ) {
			return;
		}
		if ( ! isset( $menu_locations[ $this->menu_location_id ] ) ) {
			return;
		}
		if ( $nav_menu_selected_id !== $menu_locations[ $this->menu_location_id ] ) {
			return;
		}
		$value = get_post_meta( $item_id, $this->meta_option_name_sort_menu_name, true );
		?>
<p class="field-short-name description description-wide">
	<label for="edit-menu-item-short-name-<?php echo $item_id; ?>">
		<?php _e( 'Short Name (PWA)', 'iworks-pwa' ); ?><br />
		<input type="text" id="edit-menu-item-a-short-name-<?php echo $item_id; ?>" class="widefat code edit-menu-item-short-name" name="menu-item-short-name[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $value ); ?>" />
	</label>
</p>
		<?php
	}

	/**
	 * save short_name form `manifest.json` shortcuts.
	 *
	 * @since 1.4.0
	 */
	public function action_wp_update_nav_menu_save( $menu_id, $menu_data = array() ) {
		if ( ! isset( $_POST['menu-item-short-name'] ) ) {
			return;
		}
		if ( ! is_array( $_POST['menu-item-short-name'] ) ) {
			return;
		}
		foreach ( $_POST['menu-item-short-name'] as $post_id => $value ) {
			$value = esc_html( $value );
			if ( empty( $value ) ) {
				delete_post_meta( $post_id, $this->meta_option_name_sort_menu_name );
				continue;
			}
			if ( update_post_meta( $post_id, $this->meta_option_name_sort_menu_name, $value ) ) {
				continue;
			}
			add_post_meta( $post_id, $this->meta_option_name_sort_menu_name, $value, true );
		}
	}
}

