=== PWA — easy way to Progressive Web App ===
Contributors: iworks
Donate link: https://ko-fi.com/iworks?utm_source=iworks-pwa&utm_medium=readme-donate
Tags: PWA, Progressive Web Application, progressive web app, progressive, manifest.json, installable, add to homescreen, offline, service worker, https
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

Once this plugin is installed, users browsing your website from a supported mobile device will see a “Add To Home Screen” notice (from the bottom of the screen) and will be able to ‘install your website’ on the home screen of their device.

iWorks PWA allow to add shortcuts for context menu to be displayed by the operating system when a user engages with the web app's icon.

iWorks PWA is easy to configure, it takes less than a minute to set-up your Progressive Web App!


== Installation ==

There are 3 ways to install this plugin:

= 1. The super easy way =
1. In your Admin, go to menu Plugins > Add.
1. Search for `iWorks PWA`.
1. Click to install.
1. Activate the plugin.
1. A new menu `PWA` in `Settings` will appear in your Admin.

= 2. The easy way =
1. Download the plugin (.zip file) on the right column of this page.
1. In your Admin, go to menu Plugins > Add.
1. Select button `Upload Plugin`.
1. Upload the .zip file you just downloaded.
1. Activate the plugin.
1. A new menu `PWA` in `Settings` will appear in your Admin.

= 3. The old and reliable way (FTP) =
1. Upload `iworks-pwa` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. A new menu `PWA` in `Settings` will appear in your Admin.

== Frequently Asked Questions ==

= How can I translate manifest.json values?

It is only possible with WPML package.

1. Install and activate `WPML Multilingual CMS` and `WPML String Translation` plugins.
1. Save `manifest.json` data on `WP Admin` -> `Settings` -> `WPA`.
1. Open `WP Admin` -> `WPML` -> String translation`.
1. Select domain `iworks-pwa`.
1. Translate strings.

= How can I add context menu? =

The shortcuts member defines an array of shortcuts or links to key tasks or pages within a web app. A user agent can use these values to assemble a context menu to be displayed by the operating system when a user engages with the web app's icon.

1. Install [Menu Icons by ThemeIsle](https://wordpress.org/plugins/menu-icons/) plugin.
1. Go to WPA -> Appearance -> Menu.
1. Open "Menu Icon Setting" from "Add menu items" column (it should be on the bottom).
1. Be sure you have "Image" checked.
1. Create custom menu and set "Display location" to "PWA Shortcuts Menu".
1. Add item.
1. Select icon - it is recommended that you use a single 192x192 pixel icon.
1. Save menu.

== Screenshots ==

1. General configuration.
2. Generic configuration.
3. Apple configuration.
4. Microsoft configuration.

== Changelog ==

= 1.4.0 (2022-03-xx) =
* Added PWA Shortcuts Menu. [Read more about PWA Shortcuts](https://developer.mozilla.org/en-US/docs/Web/Manifest/shortcuts).
* Added [Menu Icons by ThemeIsle](https://wordpress.org/plugins/menu-icons/) plugin integration for PWA Shortcuts.

= 1.3.3 (2022-03-22) =
* Replaced cache function o proper one.
* Fixed problem with deleting general icon.

= 1.3.2 (2022-03-22) =
* Fixed wrong option name.

= 1.3.1 (2022-03-22) =
* Added `/ieconfig.xml` link on debug tab.
* Fixed long site title on offline page. Props for [tanohex](https://wordpress.org/support/users/tanohex/).
* Fixed missed translation domain in few strings.
* Improved usage of transient cache. Props for [tanohex](https://wordpress.org/support/users/tanohex/).
* Removed images from debug tab.

= 1.3.0 (2022-03-16) =
* Added object cache for settings.
* Fixed `protected $option_name_icons` warning.

= 1.2.3 (2022-02-23) =
* Removed `console.log` from JavaScript files.

= 1.2.2 (2022-02-21) =
* Added filter `iworks_plugin_get_options' to allow filtering plugin core configuration.
* Added [OG — Better Share on Social Media](https://wordpress.org/plugins/og/) plugin integration.
* Added plugin information into PWA scripts elements.
* Added purpose "any maskable" to the biggest icon. Props for [vmaxs](https://wordpress.org/support/users/vmaxs/).
* Updated iWorks Options to 2.8.2.
* Updated iWorks Rate to 2.1.0.

= 1.2.1 (2022-02-16) =
* Added message when site permalink is "plain" - plugin does not support it.
* Updated iWorks Options to 2.8.1.

= 1.2.0 (2022-02-15) =
* Added ability to change text of offline page.
* Added version to cache control.
* Added WPML plugin integration.
* Moved worker JavaScript from PHP class to separate template.

= 1.1.6 (2022-01-27) =
* Added screenshots to `readme.txt`.
* Removed debug functions.

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

