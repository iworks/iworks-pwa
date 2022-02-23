<?php
/**
 * OG — Better Share on Social Media
 * https://wordpress.org/plugins/og/
 *
 * @since 1.2.2
 */
class iWorks_PWA_Integrations_OG extends iWorks_PWA_Integrations {

	public function __construct( $options ) {
		/**
		 * OG — Better Share on Social Media
		 * Hooks
		 */
		add_filter( 'og_image_init', array( $this, 'filter_og_image_init' ) );
		/**
		 * set options
		 */
		$this->options = $options;
	}

	/**
	 * Filter `og_image_init`
	 *
	 * @since 1.2.2
	 */
	public function filter_og_image_init( $image ) {
		$i = $this->get_image_for_og_image();
		if ( ! empty( $i ) ) {
			return array( $i );
		}
		return $image;
	}

	/**
	 * get og:image
	 *
	 * @since 1.2.2
	 */
	private function get_image_for_og_image() {
		$attachment_id = $this->options->get_option( 'icon_app' );
		if ( empty( $attachment_id ) ) {
			return array();
			return $image;
		}
		$mime_type = get_post_mime_type( $attachment_id );
		if ( ! preg_match( '/^image/', $mime_type ) ) {
			return array();
		}
		$data = wp_get_attachment_image_src( $attachment_id, 'full' );
		if ( empty( $data ) ) {
			return array();
		}
		return array(
			'url'    => $data[0],
			'width'  => $data[1],
			'height' => $data[2],
			'mime'   => $mime_type,
			'alt'    => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		);
	}
}

