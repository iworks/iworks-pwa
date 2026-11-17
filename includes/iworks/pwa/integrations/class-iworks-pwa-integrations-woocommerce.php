<?php
/**
 * WooCommerce
 * https://wordpress.org/plugins/woocommerce/
 *
 * Excludes WooCommerce transactional pages (cart, checkout, my account)
 * and AJAX endpoints from service-worker caching. Caching these breaks
 * carts, checkouts and logged-in states.
 *
 * @since 1.9.0
 */
class iWorks_PWA_Integrations_WooCommerce extends iWorks_PWA_Integrations {

	public function __construct( $options ) {
		$this->options = $options;
		add_filter( 'iworks_pwa_cache_excluded_patterns', array( $this, 'filter_cache_excluded_patterns' ) );
		add_filter( 'iworks_pwa_offline_urls_set', array( $this, 'filter_offline_urls_set_remove_wc_pages' ) );
	}

	/**
	 * Add WooCommerce pages and endpoints to the service-worker exclude list.
	 *
	 * @since 1.9.0
	 *
	 * @param array $patterns Exclusion regex patterns.
	 * @return array
	 */
	public function filter_cache_excluded_patterns( $patterns ) {
		if ( ! is_array( $patterns ) ) {
			$patterns = array();
		}
		/**
		 * transactional pages: cart, checkout, my account
		 */
		$pages = array(
			(int) get_option( 'woocommerce_cart_page_id' ),
			(int) get_option( 'woocommerce_checkout_page_id' ),
			(int) get_option( 'woocommerce_myaccount_page_id' ),
		);
		foreach ( $pages as $page_id ) {
			if ( 0 < $page_id ) {
				$url        = wp_make_link_relative( get_permalink( $page_id ) );
				$patterns[] = '^' . preg_quote( $url, '/' ) . '/?$';
			}
		}
		/**
		 * WooCommerce AJAX and add-to-cart requests: never intercept.
		 */
		$patterns[] = '^\\/\\?wc-ajax=';
		$patterns[] = 'add-to-cart=';
		$patterns[] = '^\\/wp-json\\/wc/';
		$patterns[] = '^\\/wc-api\\/';
		return $patterns;
	}

	/**
	 * Keep transactional pages out of the precache set.
	 *
	 * @since 1.9.0
	 *
	 * @param array $urls Precached URLs.
	 * @return array
	 */
	public function filter_offline_urls_set_remove_wc_pages( $urls ) {
		if ( ! is_array( $urls ) ) {
			return $urls;
		}
		$excluded = array();
		foreach ( array( 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'woocommerce_myaccount_page_id' ) as $option ) {
			$page_id = (int) get_option( $option );
			if ( 0 < $page_id ) {
				$excluded[] = get_permalink( $page_id );
			}
		}
		if ( empty( $excluded ) ) {
			return $urls;
		}
		return array_values(
			array_filter(
				$urls,
				function ( $url ) use ( $excluded ) {
					return ! in_array( $url, $excluded, true );
				}
			)
		);
	}
}
