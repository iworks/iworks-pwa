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
 * Ask for vote
 *
 * @since 1.0.0
 */
if ( is_ssl() ) {
	include_once $includes . '/iworks/rate/rate.php';
	do_action(
		'iworks-register-plugin',
		plugin_basename( __FILE__ ),
		__( 'iWorks PWA', 'iworks-pwa' ),
		'iworks-pwa'
	);
}

