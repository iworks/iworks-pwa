=== iWorks PWA ===
Contributors: iworks
Donate link: http://iworks.pl/donate/iworks-pwa.php
Tags: 
Requires at least: 5.6
Tested up to: 5.7
Stable tag: PLUGIN_VERSION
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

PLUGIN_TAGLINE

== Description ==

Super Simple PWA implementation /manifest.json and service worker for
offline.

No configuration.


== Installation ==

There are 3 ways to install this plugin:

= 1. The super easy way =
1. In your Admin, go to menu Plugins > Add
1. Search for `iWorks PWA`
1. Click to install
1. Activate the plugin

= 2. The easy way =
1. Download the plugin (.zip file) on the right column of this page
1. In your Admin, go to menu Plugins > Add
1. Select button `Upload Plugin`
1. Upload the .zip file you just downloaded
1. Activate the plugin

= 3. The old and reliable way (FTP) =
1. Upload `iworks-pwa` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How to change meta theme-color? =

First plugin try to get `theme_mod` with default set to `f0f0f0`:

`get_theme_mod( 'background_color', 'f0f0f0');`

Please use `iworks_pwa_configuration_theme_color` filter to change this
value:

`
add_filter( 'iworks_pwa_configuration_theme_color', function( $color ) {
    return '#f20';
}
`
As returned value you can return any valid color, bu remember alpha
value will be ignored (for rgba, hsla or hex with alpha).


== Screenshots ==

== Changelog ==


= 0.0.2 (2021-04-26) =

* Added meta `theme-color`. Props for [forexonlineproductionltd](https://wordpress.org/support/users/forexonlineproductionltd/).

= 0.0.1 (2021-03-18) =

* Init.

== Upgrade Notice ==

