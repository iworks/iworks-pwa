<?php


require_once dirname( dirname( __FILE__ ) ) . '/class-iworks-pwa.php';


class iWorks_PWA_Microsoft extends iWorks_PWA {
public function __construct() {
		parent::_construct();
		add_action( 'parse_request', array( $this, 'browserconfig_xml' ) );
	/**
	 * Handle "/browserconfig.xml" request.
	 *
	 * @since 1.0.0
	 */
public function browserconfig_xml() {
	if (
		! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	if ( '/browserconfig.xml' !== $_SERVER['REQUEST_URI'] ) {
		return;
	}
	header( 'Content-type: text/xml' );
	echo '<' . '?xml version="1.0" encoding="utf-8"?' . '>';
	echo PHP_EOL;
	echo '<browserconfig>';
	echo '<msapplication>';
	echo '<tile>';
	$sizes = array( 70, 150, 310 );
	foreach ( $sizes as $size ) {
		$url = $this->get_asset_url(
			sprintf(
				'icons/favicon/ms-icon-%1$dx%1$d.png',
				$size
			)
		);
		printf( '<square%1$dx%1$dlogo src="%2$s"/>', $size, esc_url( $url ) );
	}
	printf( '<TileColor>%s</TileColor>', $this->color_title );
	echo '</tile>';
	echo '</msapplication>';
	echo '</browserconfig>';
	exit;
}
