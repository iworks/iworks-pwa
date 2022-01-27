=== PWA — easy way to Progressive Web App ===
Contributors: iworks
Donate link: https://ko-fi.com/iworks?utm_source=iworks-pwa&utm_medium=readme-donate
Tags: PWA, Progressive Web Application
Requires at least: 5.6
Tested up to: 5.9
Stable tag: PLUGIN_VERSION
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

PLUGIN_TAGLINE

== Description ==

Progressive Web Apps (PWA) is a technology that combines the best of mobile web and the best of mobile apps to create a superior mobile web experience. They are installed on the phone like a normal app (web app) and can be accessed from the home screen.

Users can come back to your website by launching the app from their home screen and interact with your website through an app-like interface.  Your return visitors will experience almost-instant loading times and enjoy the great performance benefits of your PWA!

iWorks PWA makes it easy for you to convert your WordPress website into a Progressive Web App instantly!

Once this plugin is installed, users browsing your website from a supported mobile device will see a “Add To Home Screen” notice (from the bottom of the screen) and will be able to ‘install your website’ on the home screen of their device. Every page visited is stored locally on their device and will be available to read even when they are offline!

iWorks PWA is easy to configure, it takes less than a minute to set-up your Progressive Web App!


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

Please use `iworks_pwa_configuration_theme_color` filter to change this value:

`
add_filter( 'iworks_pwa_configuration_theme_color', function( $color ) {
    return '#f20';
}
`
As returned value you can return any valid color, but remember alpha value will be ignored (for rgba, hsla or hex with alpha).


== Screenshots ==

1. General configuration.
1. Generic configuration.
1. Apple configuration.
1. Microsoft configuration.

== Changelog ==

= 1.1.5 (2022-01-27) =
* Added Apple Pinned Tab Icon.
* Added Apple Launch Icon Title.
* Added Microsoft Pinned Site.
* Added Microsoft Live Tile for IE11.
* Added HTML prefetch for `manifest.json`.
* Refactored options.

= 1.1.4 (2022-01-24) =
* Fixed typo.

= 1.1.3 (2022-01-24) =
* Fixed class load order issue.

= 1.1.2 (2022-01-24) =
* Added `method_exists` to check iWorks Option Class has method `set_plugin`.
* Changed plugin name into "PWA — easy way to Progressive Web App".
* Cleared `manifest.json` from unwanted values.
* Improved handle `/manifest.json`.

= 1.1.1 (2022-01-23) =
* Added configuration for Apple Splash Screen Icons.
* Added configuration for Apple Touch Icon.
* Added configuration for IE11.
* Added configuration for Microsoft Tile Icons.

= 1.1.0 (2022-01-21) =
* Added configuration for application name.
* Added configuration fot application colors.
* Added configuration fot application description.
* Added configuration fot application display.
* Added configuration fot application icons.
* Added configuration fot application orientation.
* Added configuration fot application short name.
* Changed plugin name from "iWorks PWA" into "PWA — simple way to Progressive Web App".
* Updated iWorks Options to 2.8.0.
* Updated iWorks Rate to 2.0.6.

= 1.0.0 (2022-01-04) =
* First stable release.
* Added check for non SSL site - SSL is required for PWA.
* Added "Rate" module.
* Fixed duplicates in offline urls set.
* Bumped offline version to 2.

= 0.0.2 (2021-04-26) =
* Added meta `theme-color`. Props for [forexonlineproductionltd](https://wordpress.org/support/users/forexonlineproductionltd/).

= 0.0.1 (2021-03-18) =
* Init.

== Upgrade Notice ==

