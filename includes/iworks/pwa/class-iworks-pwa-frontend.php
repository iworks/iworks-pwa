<?php
/**
 * Frontend implementation for iWorks PWA plugin
 * Handles all frontend-related functionality including Add to Home Screen button,
 * manifest configuration, and viewport meta tag management.
 *
 * @package iWorks_PWA
 * @subpackage Frontend
 * @since 1.0.0
 */

require_once dirname( __DIR__, 1 ) . '/class-iworks-pwa.php';


/**
 * Frontend class that extends the main iWorks_PWA class
 * This class manages all frontend-specific functionality for the PWA plugin.
 */
class iWorks_PWA_Frontend extends iWorks_PWA {

	/**
 * Constructor for the Frontend class
 * Initializes the class and sets up WordPress hooks for frontend functionality.
 *
 * @since 1.0.0
 */
	public function __construct() {
		parent::__construct();
		/**
		 * WordPress Hooks
		 */
		add_action( 'wp_head', array( $this, 'html_head' ), PHP_INT_MAX - 10 );
		add_action( 'init', array( $this, 'action_init_setup_local' ) );
	}

	/**
	 * Load scripts
	 *
	 * @since 1.0.1
	 */
	/**
 * Sets up local initialization for the Add to Home Screen button
 * Configures where the A2HS button should appear based on user settings.
 *
 * @since 1.0.1
 */
	public function action_init_setup_local() {
		switch ( $this->options->get_option( 'button_a2hs_position' ) ) {
			case 'wp_footer':
				$this->add( 'wp_footer' );
				break;
			case 'wp_body_open':
				$this->add( 'wp_body_open' );
				break;
		}
	}

	private function add( $action ) {
		add_action( $action, array( $this, 'add_to_home_screen' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_styles' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * register styles
	 *
	 * @since 1.5.0
	 */
	/**
 * Registers frontend styles for the plugin
 * Adds the Add to Home Screen button styles to WordPress.
 *
 * @since 1.5.0
 */
	public function register_styles() {
		wp_register_style(
			$this->options->get_option_name( 'a2hs' ),
			plugins_url( 'assets/styles/frontend/add-to-home-screen.css', $this->root . '/iworks-pwa.php' ),
			array(),
			$this->version
		);
	}

	/**
	 * Enquque styles
	 *
	 * @since 1.5.0
	 */
	/**
 * Enqueues registered styles if enabled in settings
 * Checks if custom CSS is enabled before loading the styles.
 *
 * @since 1.5.0
 */
	public function enqueue_styles() {
		if ( $this->options->get_option( 'button_a2hs_css' ) ) {
			wp_enqueue_style( $this->options->get_option_name( 'a2hs' ) );
		}
	}

	private function before() {
		printf(
			'<!-- %s %s -->%s',
			esc_html__( 'iWorks PWA', 'iworks-pwa' ),
			esc_html( $this->version ),
			esc_html( $this->eol )
		);
	}

	/**
	 *
	 * @since 1.0.0
	 */
	/**
 * Adds necessary meta tags to the HTML head
 * Includes viewport meta tag and manifest.json link.
 *
 * @since 1.0.0
 */
	public function html_head() {
		$this->before();
		/**
		 * add meta viewport - on wp_head
		 *
		 * @since 1.5.1
		 */
		if ( 'missing' === get_option( $this->option_name_check_meta_viewport ) ) {
			printf(
				'<meta name="viewport" content="width=device-width, initial-scale=1">%s',
				esc_html( $this->eol )
			);
		}
		/**
		 * manifest.json
		 */
		printf(
			'<link rel="manifest" href="%s">%s',
			esc_attr( wp_make_link_relative( home_url( 'manifest.json' ) ) ),
			esc_html( $this->eol )
		);
		printf(
			'<link rel="prefetch" href="%s">%s',
			esc_attr( wp_make_link_relative( home_url( 'manifest.json' ) ) ),
			esc_html( $this->eol )
		);
		/**
		 * theme color
		 */
		printf(
			'<meta name="theme-color" content="%s">%s',
			esc_attr( $this->configuration['theme_color'] ),
			esc_html( $this->eol )
		);
	}

	/**
	 *
	 * @since 1.5.0
	 */
	public function add_to_home_screen() {
		$this->before();
		$text = $this->options->get_option( 'button_a2hs_text' );
		if ( empty( $text ) ) {
			$text = __( 'Add to home screen', 'iworks-pwa' );
		}
		?>
<div id="iworks-pwa-add-button-container" style="display:none">
<button id="iworks-pwa-add-button"><?php echo esc_html( $text ); ?></button>
</div>
		<?php
	}
}

