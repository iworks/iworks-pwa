<?php
/**
 *
 * @since 1.0.0
 */

require_once dirname( dirname( __FILE__ ) ) . '/class-iworks-pwa.php';


class iWorks_PWA_HTML_Head extends iWorks_PWA {

	public function __construct() {
		parent::__construct();
		if ( ! is_object( $this->options ) ) {
			$this->options = get_iworks_pwa_options();
		}
		add_action( 'wp_head', array( $this, 'html_head' ), PHP_INT_MAX - 10 );
	}

	/**
	 *
	 * @since 1.0.0
	 */
	public function html_head() {
		if ( $this->debug ) {
			printf(
				'<!-- %s %s -->%s',
				esc_html__( 'iWorks PWA', 'iworks-pwa' ),
				$this->version,
				$this->eol
			);
		}
		printf(
			'<link rel="manifest" href="%s" />%s',
			wp_make_link_relative( home_url( 'manifest.json' ) ),
			$this->eol
		);
		printf(
			'<link rel="prefetch" href="%s" />%s',
			wp_make_link_relative( home_url( 'manifest.json' ) ),
			$this->eol
		);
		printf(
			'<meta name="theme-color" content="%s" />%s',
			$this->get_configuration_color_theme(),
			$this->eol
		);
		/**
		 * Microsoft
		 */
		if ( $this->debug ) {
			echo '<!-- Microsoft -->';
			echo PHP_EOL;
		}
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

