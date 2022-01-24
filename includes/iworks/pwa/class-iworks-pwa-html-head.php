<?php
/**
 *
 * @since 1.0.0
 */

require_once dirname( dirname( __FILE__ ) ) . '/class-iworks-pwa.php';


class iWorks_PWA_HTML_Head extends iWorks_PWA {

	private $apple_touch_icons_option_name = 'ati';

	public function __construct() {
		parent::__construct();
		if ( ! is_object( $this->options ) ) {
			$this->options = get_iworks_pwa_options();
		}
		$option_name = $this->options->get_option_name( 'icon_apple' );
		add_action( 'wp_head', array( $this, 'html_head' ), PHP_INT_MAX );
		add_action( 'update_option_' . $option_name, array( $this, 'action_flush_icons' ), 10, 3 );
		/**
		 * Apple Touch Icons
		 */
		$this->icons = array(
			180 => array(
				'sizes'   => '180x180',
				'default' => true,
			),
			167 => array(
				'sizes' => '167x167',
			),
			152 => array(
				'sizes' => '152x152',
			),
			120 => array(
				'sizes' => '120x120',
			),
			114 => array(
				'sizes' => '114x114',
			),
			76  => array(
				'sizes' => '76x76',
			),
			72  => array(
				'sizes' => '72x72',
			),
			57  => array(
				'sizes' => '57x57',
			),
		);
	}

	private function print_apple_touch_icons() {
		$icons = $this->get_apple_touch_icons();
		if ( empty( $icons ) ) {
			return;
		}
		if ( $this->debug ) {
			echo '<!-- Apple Touch Icons -->';
			echo $this->eol;
		}
		foreach ( $icons as $one ) {
			printf(
				'<link rel="apple-touch-icon"%s href="%s">%s',
				isset( $one['default'] ) && $one['default'] ? '' : sprintf( ' sizes="%s"', esc_attr( $one['sizes'] ) ),
				wp_make_link_relative( $one['src'] ),
				$this->eol
			);
		}
	}

	private function get_apple_touch_icons() {
		$icons = $this->options->get_option( $this->apple_touch_icons_option_name );
		if ( ! empty( $icons ) ) {
			return apply_filters( 'iworks_pwa_configuration_apple_touch_icons', $icons );
		}
		$value = intval( $this->options->get_option( 'icon_apple' ) );
		if ( 0 < $value ) {
			$value = intval( $this->options->get_option( 'icon_app' ) );
		}
		if ( 1 > $value ) {
			return array();
		}
		$image = $this->get_wp_image_object_from_attachement_id( $value );
		if ( ! is_wp_error( $image ) ) {
			$size = min( $image->get_size() );
			$ext  = $this->get_image_ext_from_attachement_id( $value );
			krsort( $this->icons );
			foreach ( $this->icons as $width => $data ) {
				if ( $width > $size ) {
					continue;
				}
				$name         = sprintf( 'apple-touch-icon-%s.%s', $width, $ext );
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
			$this->options->update_option( $this->apple_touch_icons_option_name, $icons );
			return $icons;
		}
	}

	public function action_flush_icons( $old_value, $value, $option ) {
		delete_option( $this->options->get_option_name( $this->apple_touch_icons_option_name ) );
	}

	/**
	 *
	 * @since 1.0.0
	 */
	public function html_head() {
		if ( $this->debug ) {
			printf(
				'<!-- %s %s -->',
				esc_html__( 'iWorks PWA', 'iworks-pwa' ),
				$this->version
			);
			echo $this->eol;
		}
		printf(
			'<link rel="manifest" href="%s" />',
			wp_make_link_relative( home_url( 'manifest.json' ) )
		);
		echo $this->eol;
		printf( '<meta name="theme-color" content="%s" />', $this->get_configuration_color_theme() );
		echo $this->eol;
		/**
		 * Apple Touch Icon
		 */
		$this->print_apple_touch_icons();
		/**
		 * Apple Pinned Tab Icon
		 */
		$value = $this->options->get_option( 'apple_pti' );
		if ( ! empty( $value ) ) {
			$value = wp_get_attachment_url( $value );
			if ( ! empty( $value ) ) {
				printf(
					'<link rel="mask-icon" href="%s" color="%s">%s',
					wp_make_link_relative( $value ),
					$this->options->get_option( 'apple_ptic' ),
					$this->eol
				);
			}
		}
		/**
		 *  apple-touch-startup-image
		 */
		$icons = $this->options->get_options_by_group( 'apple-touch-startup-image' );
		if ( is_array( $icons ) && ! empty( $icons ) ) {
			if ( $this->debug ) {
				echo '<!-- Apple Splash Screen -->';
				echo PHP_EOL;
			}
			foreach ( $icons as $one ) {
				$value = $this->options->get_option( $one['name'] );
				if ( empty( $value ) ) {
					continue;
				}
				$value = wp_get_attachment_url( $value );
				if ( empty( $value ) ) {
					continue;
				}
				printf(
					'<link rel="apple-touch-startup-image" sizes="%s" href="%s"/>%s',
					$one['sizes'],
					wp_make_link_relative( $value ),
					$this->eol
				);
			}
		}
		/**
		 * Microsoft
		 */
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
		printf(
			'<meta name="application-name" content="%s" />%s',
			esc_attr( $this->get_configuration_name() ),
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
		if ( $this->debug ) {
			printf(
				'<!-- /%s %s -->',
				esc_html__( 'iWorks PWA', 'iworks-pwa' ),
				$this->version
			);
			echo $this->eol;
		}
	}

}

