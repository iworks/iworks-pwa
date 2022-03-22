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
		/**
		 * WordPress Hooks
		 */
		add_action( 'wp_head', array( $this, 'html_head' ), PHP_INT_MAX - 10 );
	}

	/**
	 *
	 * @since 1.0.0
	 */
	public function html_head() {
		printf(
			'<!-- %s %s -->%s',
			esc_html__( 'iWorks PWA', 'iworks-pwa' ),
			$this->version,
			$this->eol
		);
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
			$this->configuration['theme_color'],
			$this->eol
		);
	}

}

