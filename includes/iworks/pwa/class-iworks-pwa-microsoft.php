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
	private $option_name_icons = 'icons_ie11';

	public function __construct() {
		parent::__construct();
		if ( ! is_object( $this->options ) ) {
			$this->options = get_iworks_pwa_options();
		}
		/**
		 * WordPress Hooks
		 */
		add_action( 'wp_head', array( $this, 'html_head' ), PHP_INT_MAX - 8 );
		add_action( 'parse_request', array( $this, 'parse_request' ) );
		/**
		 * Clear generated icons
		 *
		 * @since 1.1.5
		 */
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
		foreach ( $icons as $one ) {
			printf(
				'<square%1$dx%1$dlogo src="%2$s"/>',
				esc_attr( $one['sizes'] ),
				esc_attr( wp_make_link_relative( $one['src'] ) )
			);
		}
		$value = $this->options->get_option( 'ms_wide' );
		if ( ! empty( $value ) ) {
			$value = wp_get_attachment_url( $value );
			if ( ! empty( $value ) ) {
				printf(
					'<square310x310logo src="%s"/>',
					esc_attr( wp_make_link_relative( $value ) )
				);
			}
		}
		printf( '<TileColor>%s</TileColor>', $this->get_configuration_color_theme() );
		echo '</tile>';
		echo '</msapplication>';
		echo '</browserconfig>';
		exit;
	}

	public function html_head() {
		/**
		 * Microsoft
		 */
		if ( $this->debug ) {
			echo '<!-- Microsoft -->';
			echo PHP_EOL;
		}
		printf(
			'<meta name="msapplication-config" content="%s" />%s',
			esc_attr( $this->ieconfig_filename ),
			$this->eol
		);
		printf(
			'<meta name="application-name" content="%s" />%s',
			esc_attr( $this->get_configuration_name() ),
			$this->eol
		);
		printf(
			'<meta name="msapplication-tooltip" content="%s" />%s',
			esc_attr( $this->get_configuration_description() ),
			$this->eol
		);
		printf(
			'<meta name="msapplication-starturl" content="%s" />%s',
			get_home_url(),
			$this->eol
		);
		printf(
			'<meta name="msapplication-navbutton-color" content="%s" />%s',
			$this->get_configuration_color_theme(),
			$this->eol
		);
		if ( $this->debug ) {
			echo '<!-- Windows 8 Tiles -->';
			echo PHP_EOL;
		}
		$icons = $this->get_configuration_icons( 'windows8' );
		if ( is_array( $icons ) ) {
			foreach ( $icons as $one ) {
				if ( '144x144' === $one['sizes'] ) {
					printf(
						'<meta name="msapplication-TileImage" content="%s" />%s',
						esc_attr( wp_make_link_relative( $one['src'] ) ),
						$this->eol
					);
				}
			}
		}
		printf(
			'<meta name="msapplication-TileColor" content="%s" />%s',
			esc_attr( $this->get_configuration_color_theme() ),
			$this->eol
		);
		$icons = $this->get_configuration_icons( 'ie11' );
		if ( is_array( $icons ) ) {
			if ( $this->debug ) {
				echo '<!-- Internet Explorer 11 Tiles -->';
				echo PHP_EOL;
			}
			foreach ( $icons as $data ) {
				printf(
					'<meta name="msapplication-square%slogo" content="%s" />%s',
					esc_attr( $data['sizes'] ),
					esc_attr( wp_make_link_relative( $data['src'] ) ),
					$this->eol
				);
			}
		}
	}

}

