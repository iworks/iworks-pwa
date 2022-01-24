<?php
/**
 * Add SVG mime types to WordPress
 */

class iWorks_SVG {

	public function __construct() {
		add_filter( 'upload_mimes', array( $this, 'upload_mimes' ), 99 );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'upload_check' ), 10, 4 );
	}

	/**
	 * Add Mime Types
	 */
	public function upload_mimes( $mimes = array() ) {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
		return $mimes;
	}

	/**
	 * Check Mime Types
	 */
	public function upload_check( $checked, $file, $filename, $mimes ) {
		if ( $checked['type'] ) {
			return $checked;
		}
		$check_filetype  = wp_check_filetype( $filename, $mimes );
		$ext             = $check_filetype['ext'];
		$type            = $check_filetype['type'];
		$proper_filename = $filename;
		if ( $type && 0 === strpos( $type, 'image/' ) && $ext !== 'svg' ) {
			$ext = $type = false;
		}
		$checked = compact( 'ext', 'type', 'proper_filename' );
		return $checked;
	}

}

