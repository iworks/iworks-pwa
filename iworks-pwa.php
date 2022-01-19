<?php
/*
Plugin Name: iWorks PWA
Text Domain: iworks-pwa
Plugin URI: http://iworks.pl/iworks-pwa/
Description: PLUGIN_TAGLINE
Version: PLUGIN_VERSION
Author: Marcin Pietrzak
Author URI: http://iworks.pl/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2021 Marcin Pietrzak (marcin@iworks.pl)

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

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * static options
 */
$base     = dirname( __FILE__ );
$includes = $base . '/includes';

/**
 * get plugin settings
 *
 * @since 1.0.1
 */
include_once $base . '/etc/options.php';

/**
 * @since 1.0.6
 */
if ( ! class_exists( 'iworks_options' ) ) {
	include_once $includes . '/iworks/options/options.php';
}



/**
 * i18n
 */
load_plugin_textdomain( 'iworks-pwa', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

/**
 * load
 */
require_once $includes . '/iworks/pwa/class-iworks-pwa-manifest.php';
/**
 * run
 */
new iWorks_PWA_manifest;

/**
 * load options
 *
 * since 2.6.8
 *
 */
global $iworks_pwa_options;
$iworks_pwa_options = null;

function get_iworks_pwa_options() {
	global $iworks_pwa_options;
	if ( is_object( $iworks_pwa_options ) ) {
		return $iworks_pwa_options;
	}
	$iworks_pwa_options = new iworks_options();
	$iworks_pwa_options->set_option_function_name( 'iworks_pwa_options' );
	$iworks_pwa_options->set_option_prefix( 'iworks_pwa_' );
	$iworks_pwa_options->set_plugin( basename( __FILE__ ) );
	return $iworks_pwa_options;
}

/**
 * Ask for vote
 *
 * @since 1.0.0
 */
include_once $includes . '/iworks/rate/rate.php';
do_action(
	'iworks-register-plugin',
	plugin_basename( __FILE__ ),
	__( 'iWorks PWA', 'iworks-pwa' ),
	'iworks-pwa'
);

