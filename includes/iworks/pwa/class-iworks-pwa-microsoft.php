<?php


require_once dirname( dirname( __FILE__ ) ) . '/class-iworks-pwa.php';

class iWorks_PWA_Microsoft extends iWorks_PWA {

	/**
	 * IE Config file name
	 *
	 * @since 1.1.5
	 */
	private $ieconfig_filename = '/ieconfig.xml';

	/**
	 * Icons name
	 *
	 * @since 1.1.5
	 */
	protected $option_name_icons = 'icons_ie11';

	public function __construct() {
		parent::__construct();
		/**
		 * WordPress Hooks
		 */
		add_action( 'wp_head', array( $this, 'html_head' ), PHP_INT_MAX - 8 );
		add_action( 'parse_request', array( $this, 'parse_request' ) );
		add_action( 'init', array( $this, 'action_init_setup_local' ) );
	}

	/**
	 * Clear generated icons
	 *
	 * @since 1.1.5
	 */
	public function action_init_setup_local() {
		$option_name = $this->options->get_option_name( 'ms_square' );
		add_action( 'update_option_' . $option_name, array( $this, 'action_flush_icons' ), 10, 3 );
	}

	private function get_ms_tile_icons() {
		$icons = $this->options->get_option( $this->option_name_icons );
		if ( ! empty( $icons ) ) {
			return apply_filters( 'iworks_pwa_configuration_ms_tile_icons', $icons );
		}
		$value = intval( $this->options->get_option( 'ms_square' ) );
		$image = $this->get_wp_image_object_from_attachement_id( $value );
		if ( ! is_wp_error( $image ) ) {
			$size                = min( $image->get_size() );
			$ext                 = $this->get_image_ext_from_attachement_id( $value );
			$icons_configuration = $this->options->get_group( 'ms_tile_square' );
			krsort( $icons_configuration );
			foreach ( $icons_configuration as $width => $data ) {
				if ( $width > $size ) {
					continue;
				}
				$name         = sprintf( 'ms-tile-icon-%s.%s', $width, $ext );
				$destfilename = $this->get_icons_directory() . '/' . $name;
				$result       = $this->image_resize_and_save( $image, $width, $destfilename );
				if ( ! is_wp_error( $result ) ) {
					$one        = $data;
					$one['src'] = sprintf(
						'%s/%s?v=%s',
						$this->get_icons_base_url(),
						$name,
						time()
					);
					$icons[]    = $one;
				}
			}
		}
		if ( ! empty( $icons ) ) {
			$this->options->update_option( $this->option_name_icons, $icons );
			return $icons;
		}
		return array();
	}

	/**
	 * Handle "/browserconfig.xml" request.
	 *
	 * @since 1.0.0
	 */
	public function parse_request() {
		if (
			! isset( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}
		$uri = remove_query_arg( array_keys( $_GET ), $_SERVER['REQUEST_URI'] );
		if ( $this->ieconfig_filename !== $_SERVER['REQUEST_URI'] ) {
			return;
		}
		header( 'Content-type: text/xml' );
		echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>';
		echo PHP_EOL;
		echo '<browserconfig>';
		echo '<msapplication>';
		echo '<tile>';
		$icons = $this->get_ms_tile_icons();
		if ( is_array( $icons ) ) {
			foreach ( $icons as $one ) {
				printf(
					'<square%1$dx%1$dlogo src="%2$s"/>',
					esc_attr( $one['sizes'] ),
					esc_attr( wp_make_link_relative( esc_url( $one['src'] ) ) )
				);
			}
		}
		$value = $this->options->get_option( 'ms_wide' );
		if ( ! empty( $value ) ) {
			$value = wp_get_attachment_url( $value );
			if ( ! empty( $value ) ) {
				printf(
					'<square310x310logo src="%s"/>',
					esc_attr( wp_make_link_relative( esc_url( $value ) ) )
				);
			}
		}
		printf( '<TileColor>%s</TileColor>', esc_html( $this->configuration['theme_color'] ) );
		echo '</tile>';
		echo '</msapplication>';
		echo '</browserconfig>';
		exit;
	}

	public function html_head() {
		/**
		 * Handle cache
		 *
		 * @since 1.4.3
		 */
		$cache_key = $this->settings_cache_option_name . 'head_microsoft';
		$value     = get_transient( $cache_key );
		if ( ! empty( $value ) ) {
			echo $value;
			return;
		}
		/**
		 * Microsoft
		 */
		$content = '';
		if ( $this->debug ) {
			$content .= '<!-- Microsoft -->';
			$content .= PHP_EOL;
		}
		$content .= sprintf(
			'<meta name="msapplication-config" content="%s">%s',
			esc_attr( $this->ieconfig_filename ),
			esc_html( $this->eol )
		);
		$content .= sprintf(
			'<meta name="application-name" content="%s">%s',
			esc_attr( $this->configuration['name'] ),
			esc_html( $this->eol )
		);
		$content .= sprintf(
			'<meta name="msapplication-tooltip" content="%s">%s',
			esc_attr( $this->configuration['description'] ),
			esc_html( $this->eol )
		);
		$content .= sprintf(
			'<meta name="msapplication-starturl" content="%s">%s',
			esc_url( get_home_url() ),
			esc_html( $this->eol )
		);
		$content .= sprintf(
			'<meta name="msapplication-navbutton-color" content="%s">%s',
			esc_attr( $this->configuration['theme_color'] ),
			esc_html( $this->eol )
		);
		if ( $this->debug ) {
			$content .= '<!-- Windows 8 Tiles -->';
			$content .= PHP_EOL;
		}
		/**
		 * msapplication-TileImage
		 */
		$icons = array();
		/**
		 * get dedicated icon
		 *
		 * @since 1.4.3
		 */
		$attachement_id = $this->options->get_option( 'ms_square' );
		if ( ! empty( $attachement_id ) ) {
			$image = $this->get_wp_image_object_from_attachement_id( $attachement_id );
			$value = wp_get_attachment_image_src( $attachement_id, 'full' );
			if ( is_array( $value ) ) {
				$value['sizes'] = '144x144';
				$value['src']   = $value[0];
				$icons[]        = $value;
			}
		}
		if ( empty( $icons ) ) {
			$icons = $this->get_configuration_icons( 'windows8' );
		}
		if ( is_array( $icons ) ) {
			foreach ( $icons as $one ) {
				if ( '144x144' === $one['sizes'] ) {
					$content .= sprintf(
						'<meta name="msapplication-TileImage" content="%s">%s',
						esc_attr( wp_make_link_relative( $one['src'] ) ),
						esc_html( $this->eol )
					);
				}
			}
		}
		$content .= sprintf(
			'<meta name="msapplication-TileColor" content="%s">%s',
			esc_attr( $this->configuration['theme_color'] ),
			esc_html( $this->eol )
		);
		$icons    = $this->get_configuration_icons( 'ie11' );
		if ( is_array( $icons ) ) {
			if ( $this->debug ) {
				$content .= '<!-- Internet Explorer 11 Tiles -->';
				$content .= PHP_EOL;
			}
			foreach ( $icons as $data ) {
				$content .= sprintf(
					'<meta name="msapplication-square%slogo" content="%s">%s',
					esc_attr( $data['sizes'] ),
					esc_attr( wp_make_link_relative( $data['src'] ) ),
					esc_html( $this->eol )
				);
			}
		}
		/**
		 * Handle cache
		 *
		 * @since 1.4.3
		 */
		set_transient( $cache_key, $content, DAY_IN_SECONDS );
		echo $content;
	}

}

