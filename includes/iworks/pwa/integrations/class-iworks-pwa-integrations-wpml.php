<?php
/**
 * WPML
 * https://wpml.org
 *
 * @since 1.2.0
 */
class iWorks_PWA_Integrations_WPML extends iWorks_PWA_Integrations {

	private $languages = array();
	private $options;
	private $current_lang;
	private $codes = array();

	public function __construct( $options ) {
		add_action( 'admin_init', array( $this, 'add_update_options_hook' ) );
		/**
		 * own filters
		 */
		add_filter( 'iworks_pwa_configuration_description', array( $this, 'filter_get_configuration_description' ) );
		add_filter( 'iworks_pwa_configuration_name', array( $this, 'filter_get_configuration_name' ) );
		add_filter( 'iworks_pwa_configuration_offline_page_content', array( $this, 'filter_get_configuration_offline_page_content' ) );
		add_filter( 'iworks_pwa_configuration_short_name', array( $this, 'filter_get_configuration_short_name' ) );
		add_filter( 'iworks_pwa_manifest_is_manifest_json_request', array( $this, 'is_manifest_json_request' ), 10, 2 );
		add_filter( 'iworks_pwa_manifest_is_offline_page_request', array( $this, 'is_offline_page_request' ), 10, 2 );
		add_filter( 'iworks_pwa_manifest_is_service_worker_request', array( $this, 'is_service_worker_request' ), 10, 2 );
		add_filter( 'iworks_pwa_offline_cache_name', array( $this, 'filter_add_language_to_cache_name' ) );
		/**
		 * set options
		 */
		$this->options = $options;
		/**
		 * set current lang
		 */
		$this->current_lang = apply_filters( 'wpml_current_language', null );
	}

	/**
	 * register wpml for certain options
	 *
	 * @since 1.2.0
	 */
	public function add_update_options_hook() {
		$options_names = array(
			'app_name',
			'app_short_name',
			'app_description',
			'offline_content',
		);
		foreach ( $options_names as $name ) {
			$option_name = $this->options->get_option_name( $name );
			add_action( 'update_option_' . $option_name, array( $this, 'wpml_register_single_string' ), 10, 3 );
		}
	}

	/**
	 * WPML register strings
	 *
	 * @since 1.2.0
	 */
	public function wpml_register_single_string( $old, $value, $option ) {
		do_action( 'wpml_register_single_string', 'iworks-pwa', $option, $value, false, $this->current_lang );
	}

	/**
	 * check is manifest.json
	 *
	 * @since 1.2.0
	 */
	public function is_manifest_json_request( $status, $uri ) {
		return $this->check( $status, $uri, 'manifest.json' );
	}

	/**
	 * check is service worker
	 *
	 * @since 1.2.0
	 */
	public function is_service_worker_request( $status, $uri ) {
		return $this->check( $status, $uri, 'service-worker-handler' );
	}

	/**
	 * check is offline page
	 *
	 * @since 1.2.0
	 */
	public function is_offline_page_request( $status, $uri ) {
		return $this->check( $status, $uri, 'offline-page' );
	}

	/**
	 * meta checker
	 *
	 * @since 1.2.0
	 */
	private function check( $status, $uri, $key ) {
		$to_check = $this->options->get_group( $key );
		if ( ! preg_match( '/' . $to_check . '/', $uri ) ) {
			return $status;
		}
		if ( empty( $this->languages ) ) {
			if ( function_exists( 'icl_get_languages' ) ) {
				$this->languages = icl_get_languages();
			}
		}
		if ( empty( $this->languages ) ) {
			return $status;
		}
		if ( empty( $this->codes ) ) {
			foreach ( $this->languages as $lang ) {
				$this->codes[] = $lang['code'];
			}
		}
		if ( empty( $this->codes ) ) {
			return $status;
		}
		$re = sprintf(
			'/(%s)\/%s$/',
			implode( '|', $this->codes ),
			$to_check
		);
		return preg_match( $re, $uri );
	}

	/**
	 * get Application Name by WPML
	 *
	 * @since 1.2.0
	 */
	public function filter_get_configuration_name( $value ) {
		return apply_filters(
			'wpml_translate_single_string',
			$value,
			'iworks-pwa',
			$this->options->get_option_name( 'app_name' ),
			$this->current_lang
		);
	}

	/**
	 * get Application Short Name by WPML
	 *
	 * @since 1.2.0
	 */
	public function filter_get_configuration_short_name( $value ) {
		return apply_filters(
			'wpml_translate_single_string',
			$value,
			'iworks-pwa',
			$this->options->get_option_name( 'app_short_name' ),
			$this->current_lang
		);
	}

	/**
	 * get Application Description by WPML
	 *
	 * @since 1.2.0
	 */
	public function filter_get_configuration_description( $value ) {
		return apply_filters(
			'wpml_translate_single_string',
			$value,
			'iworks-pwa',
			$this->options->get_option_name( 'app_description' ),
			$this->current_lang
		);
	}

	/**
	 * get Offline Page Content by WPML
	 *
	 * @since 1.2.0
	 */
	public function filter_get_configuration_offline_page_content( $value ) {
		return apply_filters(
			'wpml_translate_single_string',
			$value,
			'iworks-pwa',
			$this->options->get_option_name( 'offline_content' ),
			$this->current_lang
		);
	}

	/**
	 * Add language code to cache name
	 *
	 * @since 1.2.0
	 */
	public function filter_add_language_to_cache_name( $cache_name ) {
		if ( empty( $this->current_lang ) ) {
			return $cache_name;
		}
		return sprintf( '%s-%s', $this->options->get_group( 'cache-name' ), $this->current_lang );
	}

}

