<?php
/*

Copyright 2025-PLUGIN_TILL_YEAR Marcin Pietrzak (marcin@iworks.pl)

this program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

 */
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'iworks_pwa_github' ) ) {
	return;
}

class iworks_pwa_github {

	private string $repository = 'iworks/iworks-pwa';
	private string $basename   = 'iworks-pwa';
	private $github_response;

	public function __construct() {
		/**
		 * WordPress Hooks
		 */
		add_action( 'init', array( $this, 'action_init_load_plugin_textdomain' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_popup' ), 10, 3 );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'modify_transient' ) );
		add_filter( 'upgrader_post_install', array( $this, 'install_update' ), 10, 3 );
	}

	/**
	 * i18n
	 *
	 * @since 1.0.0
	 */
	public function action_init_load_plugin_textdomain() {
		$dir  = plugin_basename( dirname( dirname( dirname( __DIR__ ) ))). '/languages';
		load_plugin_textdomain( 'iworks-pwa', false, $dir);
	}

	/**
	 * Get the latest release from the selected repository
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return array
	 */
	private function get_latest_repository_release(): array {
		// Create the request URI
		$request_uri = sprintf(
			'https://api.github.com/repos/%s/releases',
			$this->repository
		);
		// Get the response from the API
		$request = wp_remote_get( $request_uri );
		// If the API response has an error code, stop
		$response_codes = wp_remote_retrieve_response_code( $request );
		if ( $response_codes < 200 || $response_codes >= 300 ) {
			return array();
		}
		// Decode the response body
		$response = json_decode( wp_remote_retrieve_body( $request ), true );
		// If the response is an array, return the first item
		if ( is_array( $response ) && ! empty( $response[0] ) ) {
			$response = $response[0];
		}
		return $response;
	}

	/**
	 * Private method to get repository information for a plugin
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return array $response
	 */
	private function get_repository_info(): array {
		if ( ! empty( $this->github_response ) ) {
			return $this->github_response;
		}

		// Get the latest repo
		$response = $this->get_latest_repository_release();

		// Set the github_response property for later use
		$this->github_response = $response;

		// Return the response
		return $response;
	}

	/**
	 * Add details to the plugin popup
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param boolean $result
	 * @param string $action
	 * @param object $args
	 * @return boolean|object|array $result
	 */
	public function plugin_popup( $result, $action, $args ) {
		if ( $action !== 'plugin_information' ) {
			return $result;
		}
		if ( $args->slug !== $this->basename ) {
			return $result;
		}
		$repo = $this->get_repository_info();
		if ( empty( $repo ) ) {
			return $result;
		}
		$details = get_plugin_data( dirname( $this->base ) . '/' . $this->plugin_file );
		// Create array to hold the plugin data
		$plugin = array(
			'name'              => $details['Name'],
			'slug'              => $this->basename,
			'requires'          => $details['RequiresWP'],
			'requires_php'      => $details['RequiresPHP'],
			'version'           => $repo['tag_name'],
			'author'            => $details['AuthorName'],
			'author_profile'    => $details['AuthorURI'],
			'last_updated'      => $repo['published_at'],
			'homepage'          => $details['PluginURI'],
			'short_description' => $details['Description'],
			'sections'          => array(
				'Description' => $details['Description'],
				'Updates'     => $repo['body'],
			),
			'download_link'     => $repo['assets'][0]['browser_download_url'],
		);
		// Return the plugin data as an object
		return (object) $plugin;
	}

	/**
	 * Modify transient for module
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param object $transient
	 * @return object
	 */
	public function modify_transient( object $transient ): object {
		// Stop if the transient does not have a checked property
		if ( ! isset( $transient->checked ) ) {
			return $transient;
		}

		// Check if WordPress has checked for updates
		$checked = $transient->checked;

		// Stop if WordPress has not checked for updates
		if ( empty( $checked ) ) {
			return $transient;
		}

		// If the basename is not in $checked, stop
		if ( ! array_key_exists( $this->plugin_file, $checked ) ) {
			return $transient;
		}

		// Get the repo information
		$repo_info = $this->get_repository_info();

		// Stop if the repository information is empty
		if ( empty( $repo_info ) ) {
			return $transient;
		}

		// Github version, trim v if exists
		$github_version = ltrim( $repo_info['tag_name'], 'v' );

		// Compare the module's version to the version on GitHub
		$out_of_date = version_compare(
			$github_version,
			$checked[ $this->plugin_file ],
			'gt'
		);

		// Stop if the module is not out of date
		if ( ! $out_of_date ) {
			return $transient;
		}

		// Add our module to the transient
		$transient->response[ $this->plugin_file ] = (object) array(
			'id'          => $repo_info['html_url'],
			'url'         => $repo_info['html_url'],
			'slug'        => current( explode( '/', $this->basename ) ),
			'package'     => $repo_info['zipball_url'],
			'new_version' => $repo_info['tag_name'],
		);

		return $transient;
	}

	/**
	 * Install the plugin from GitHub
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @param boolean $response
	 * @param array $hook_extra
	 * @param array $result
	 * @return boolean|array $result
	 */
	public function install_update( $response, $hook_extra, $result ) {
		global $wp_filesystem;
		$directory = plugin_dir_path( $this->plugin_file );

		// Get the correct directory name
		$correct_directory_name = basename( $directory );

		// Get the path to the downloaded directory
		$downloaded_directory_path = $result['destination'];

		// Get the path to the parent directory
		$parent_directory_path = dirname( $downloaded_directory_path );

		// Construct the correct path
		$correct_directory_path = $parent_directory_path . '/' . $correct_directory_name;

		// Move and rename the downloaded directory
		$wp_filesystem->move( $downloaded_directory_path, $correct_directory_path );

		// Update the destination in the result
		$result['destination'] = $correct_directory_path;

		// If the plugin was active, reactivate it
		if ( is_plugin_active( $this->plugin_file ) ) {
			activate_plugin( $this->plugin_file );
		}

		// Return the result
		return $result;
	}
}

