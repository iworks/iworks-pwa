<?php


require_once dirname( dirname( __FILE__ ) ) . '/class-iworks-pwa.php';

class iWorks_PWA_Apple extends iWorks_PWA {

	private $option_name_icons = 'ati';

	public function __construct() {
		parent::__construct();
		/**
		 * Check & set options object
		 */
		if ( ! is_object( $this->options ) ) {
			$this->options = get_iworks_pwa_options();
		}
		/**
		 * WordPress Hooks
		 */
		add_action( 'wp_head', array( $this, 'html_head' ), PHP_INT_MAX );
		/**
		 * Clear generated icons
		 *
		 * @since 1.1.5
		 */
		$option_name = $this->options->get_option_name( 'icon_apple' );
		add_action( 'update_option_' . $option_name, array( $this, 'action_flush_icons' ), 10, 3 );
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
		$icons = $this->options->get_option( $this->option_name_icons );
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
			$size                = min( $image->get_size() );
			$ext                 = $this->get_image_ext_from_attachement_id( $value );
			$icons_configuration = $this->options->get_group( 'apple_touch_icons' );
			krsort( $icons_configuration );
			foreach ( $icons_configuration as $width => $data ) {
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
			$this->options->update_option( $this->option_name_icons, $icons );
			return $icons;
		}
	}

	/**
	 *
	 * @since 1.0.5
	 */
	public function html_head() {
		if ( $this->debug ) {
			echo '<!-- Apple -->';
			echo PHP_EOL;
		}
		printf(
			'<meta name="apple-mobile-web-app-title" content="%s" />%s',
			esc_attr( $this->get_configuration_short_name() ),
			$this->eol
		);
		printf(
			'<meta name="apple-mobile-web-app-status-bar-style" content="%s" />%s',
			$this->options->get_option( 'apple_sbc' ),
			$this->eol
		);
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
	}
}

