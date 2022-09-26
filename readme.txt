=== PWA — easy way to Progressive Web App ===
Contributors: iworks
Donate link: https://ko-fi.com/iworks?utm_source=iworks-pwa&utm_medium=readme-donate
Tags: PWA, Progressive Web Application, progressive web app, progressive, manifest.json, installable, add to homescreen, offline, service worker, https
Requires at least: 5.6
Tested up to: 6.0
Stable tag: PLUGIN_VERSION
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

PLUGIN_TAGLINE

== Description ==

Progressive Web Apps (PWA) is a technology that combines the best of mobile web and the best of mobile apps to create a superior mobile web experience. They are installed on the phone like a normal app (web app) and can be accessed from the home screen.

Users can come back to your website by launching the app from their home screen and interact with your website through an app-like interface. Your return visitors will experience almost-instant loading times and enjoy the great performance benefits of your PWA!

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
1. A new location `PWA Shortcuts Menu` in `Display location` will appear in Appearance -> Menu.

= 2. The easy way =
1. Download the plugin (.zip file) on the right column of this page.
1. In your Admin, go to menu Plugins > Add.
1. Select button `Upload Plugin`.
1. Upload the .zip file you just downloaded.
1. Activate the plugin.
1. A new menu `PWA` in `Settings` will appear in your Admin.
1. A new location `PWA Shortcuts Menu` in `Display location` will appear in Appearance -> Menu.

= 3. The old and reliable way (FTP) =
1. Upload `iworks-pwa` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. A new menu `PWA` in `Settings` will appear in your Admin.
1. A new location `PWA Shortcuts Menu` in `Display location` will appear in Appearance -> Menu.

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

= What is "Add to Home screen"? =

Add to Home screen (or A2HS for short) is a feature available in modern browsers that allows a user to "install" a web app, ie. add a shortcut to their Home screen representing their favorite web app (or site) so they can subsequently access it with a single tap.

A2HS is supported in all mobile browsers, except iOS webview. It's also supported in some Chromium desktop browsers.

== Screenshots ==

1. General configuration.
1. Generic configuration.
1. Apple configuration.
1. Microsoft configuration.
1. Installation app on Android.
1. Shortcuts menu on Android.

== Changelog ==

= 1.5.2 (2023-09-26) =
* Fixed issue with "apple-touch-icon" - it wasn't used even defined.
* Updated iWorks Rate to 2.1.1.

= 1.5.1 (2022-09-10) =
* Added checking for a tag meta with the "viewport" value. Add it if it is missing. Props for [Bert](https://wordpress.org/support/users/bertluch/)
* Changed [iWorks Rate Module](https://github.com/iworks/iworks-rate) repository to GitHub.

= 1.5.0 (2022-08-03) =
* Added `Add to Home screen` button to show browser prompt to install "app". Check [Browser compatibility](https://developer.mozilla.org/en-US/docs/Web/API/BeforeInstallPromptEvent#browser_compatibility).
* Added google campaign track to "start_url" in `manifest.json` file.

= 1.4.3 (2022-05-06) =
* Fixed issue with Microsoft Square Icon. Props for [chickendipper](https://wordpress.org/support/users/chickendipper/).
* Fixed issue lower than 8 PHP. Props for [bamsik001](https://wordpress.org/support/users/bamsik001/).
* Added cache for html head with Microsoft data.

= 1.4.2 (2022-04-08) =
* Added params defaults to function called in action `wp_nav_menu_item_custom_fields` to avoid PHP warning for improper call. Props for [vmaxs](https://wordpress.org/support/users/vmaxs/)
* Added permanent hide for menu pointer when user visit PWA Settings page.

= 1.4.1 (2022-04-05) =
* Updated iWorks Options to 2.8.3. (Fixed PHP 7.x compatibility).

= 1.4.0 (2022-04-05) =
* Added [Menu Icons by ThemeIsle](https://wordpress.org/plugins/menu-icons/) plugin integration for PWA Shortcuts.
* Added a message when site permalink is installed in a sub-directory - this plugin does not support it.
* Added PWA Shortcuts Menu. [Read more about PWA Shortcuts](https://developer.mozilla.org/en-US/docs/Web/Manifest/shortcuts).
* Updated iWorks Options to 2.8.3.

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
* Added a message when site permalink is "plain" - this plugin does not support it.
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

