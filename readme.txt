=== PWA — easy way to Progressive Web App ===
Contributors: iworks
Donate link: https://ko-fi.com/iworks?utm_source=iworks-pwa&utm_medium=readme-donate
Tags: PWA, Progressive Web Application, progressive web app, progressive, manifest.json
Requires at least: PLUGIN_REQUIRES_WORDPRESS
Tested up to: PLUGIN_TESTED_WORDPRESS
Stable tag: PLUGIN_VERSION
Requires PHP: PLUGIN_REQUIRES_PHP
License: GPLv3 or later

PLUGIN_TAGLINE

== Description ==

Progressive Web Apps (PWA) is a technology that combines the best of mobile web and the best of mobile apps to create a superior mobile web experience. They are installed on the phone like a normal app (web app) and can be accessed from the home screen.

Users can come back to your website by launching the app from their home screen and interact with your website through an app-like interface. Your return visitors will experience almost-instant loading times and enjoy the great performance benefits of your PWA!

iWorks PWA makes it easy for you to convert your WordPress website into a Progressive Web App instantly!

Once this plugin is installed, users browsing your website from a supported mobile device will see a “Add To Home Screen” notice (from the bottom of the screen) and will be able to ‘install your website’ on the home screen of their device.

iWorks PWA allow to add shortcuts for context menu to be displayed by the operating system when a user engages with the web app's icon.

iWorks PWA is easy to configure, it takes less than a minute to set-up your Progressive Web App!

= See room for improvement? =

Great! There are several ways you can get involved to help make PWA — easy way to Progressive Web App better:

1. **Report Bugs:** If you find a bug, error or other problem, please report it! You can do this by [creating a new topic](https://wordpress.org/support/plugin/iworks-pwa/) in the plugin forum. Once a developer can verify the bug by reproducing it, they will create an official bug report in GitHub where the bug will be worked on.
2. **Suggest New Features:** Have an awesome idea? Please share it! Simply [create a new topic](https://wordpress.org/support/plugin/iworks-pwa/) in the plugin forum to express your thoughts on why the feature should be included and get a discussion going around your idea.
3. **Issue Pull Requests:** If you're a developer, the easiest way to get involved is to help out on [issues already reported](https://github.com/iworks/iworks-pwa/issues) in GitHub. Be sure to check out the [contributing guide](https://github.com/iworks/iworks-pwa/blob/master/contributing.md) for developers.

Thank you for wanting to make PWA — easy way to Progressive Web App better for everyone!

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

= How can I add a context menu? =

The shortcuts member defines an array of shortcuts or links to key tasks or pages within a web app. A user agent can use these values to assemble a context menu to be displayed by the operating system when a user engages with the web app's icon.

1. Install [Menu Icons by ThemeIsle](https://wordpress.org/plugins/menu-icons/) plugin.
1. Go to WPA -> Appearance -> Menu.
1. Open "Menu Icon Setting" from the "Add menu items" column (it should be on the bottom).
1. Be sure you have "Image" checked.
1. Create a custom menu and set "Display location" to "PWA Shortcuts Menu".
1. Add item.
1. Select icon - it is recommended that you use a single 192x192 pixel icon.
1. Save the menu.

= What is "Add to Home screen"? =

Add to Home screen (or A2HS for short) is a feature available in modern browsers that allows a user to "install" a web app, ie. add a shortcut to their Home screen representing their favourite web app (or site) so they can subsequently access it with a single tap.

A2HS is supported in all mobile browsers, except iOS web view. It's also supported in some Chromium desktop browsers.

== Screenshots ==

1. General configuration.
1. Generic configuration.
1. Apple configuration.
1. Microsoft configuration.
1. Installation app on Android.
1. Shortcuts menu on Android.

== Changelog ==

= 1.5.9 (2024-02-24) =
* A few sizes have been added. [#3](https://github.com/iworks/iworks-pwa/issues/4). Props for [wfrank94](https://wordpress.org/support/users/wfrank94/).
* The plugin URL has been changed to [github](https://github.com/iworks/iworks-pwa).
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.9.2.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.1.8.

= 1.5.8 (2023-12-27) =
* The check for a tag meta with the "viewport" value has been added after changes in plugins activations.
* The dynamic property has been fixed.
* The function `parse_url()` has been replaced by the function `wp_parse_url()`.
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.9.0.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.1.6.
* The nonce check has been added to check "viewport" feature.

= 1.5.7 (2023-11-17) =
* [Empty shortcuts in the `manifest.json` file have been fixed](https://github.com/iworks/iworks-pwa/issues/4). Props for [elmando111](https://wordpress.org/support/users/elmando111/).
* The filter `iworks-pwa/manifest/shortcuts` has been added. It's allowed to modify the shortcuts array in the `manifest.json` file.
* The filter `iworks-pwa/manifest/shortcuts/element/url/campain` has been added. It's allowed to modify the campaign in shortcuts urls.
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.8.8.

= 1.5.6 (2023-10-27) =
* Wrong function name `get_color_background()` has been fixed. Props for [bodhisattvac](https://wordpress.org/support/users/bodhisattvac/).
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.8.7.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.1.3.

= 1.5.5 (2023-07-10) =
* A check for PWA files required to work has been added.
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.8.5.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.1.2.

= 1.5.4 (2023-06-30) =
* The meta tag `apple-mobile-web-app-capable` has been added.
* Trailing slashes from `link` and `meta` tags have been removed.
* The Apple-related PWA has been completely rewritten. Props for [James](https://wordpress.org/support/users/glidem/).

= 1.5.3 (2022-11-16) =
* Fixed translation string.
* Added id property to manifest.json. [Read more](https://developer.chrome.com/blog/pwa-manifest-id/).
* Added manifest.json property `name` limit on plugin install to 45 characters.
* Added manifest.json property `short_name` limit on plugin install to 15 characters.

= 1.5.2 (2022-09-26) =
* Fixed issue with "apple-touch-icon" - it wasn't used even defined.
* Updated iWorks Rate to 2.1.1.

= 1.5.1 (2022-09-10) =
* Added check for a tag meta with the "viewport" value. Add it if it is missing. Props for [Bert](https://wordpress.org/support/users/bertluch/)
* Changed [iWorks Rate Module](https://github.com/iworks/iworks-rate) repository to GitHub.

= 1.5.0 (2022-08-03) =
* Added `Add to Home screen` button to show browser prompt to install "app". Check [Browser compatibility](https://developer.mozilla.org/en-US/docs/Web/API/BeforeInstallPromptEvent#browser_compatibility).
* Added google campaign track to "start_url" in the `manifest.json` file.

= 1.4.3 (2022-05-06) =
* Fixed issue with Microsoft Square Icon. Props for [chickendipper](https://wordpress.org/support/users/chickendipper/).
* Fixed issues lower than 8 PHP. Props for [bamsik001](https://wordpress.org/support/users/bamsik001/).
* Added cache for HTML head with Microsoft data.

= 1.4.2 (2022-04-08) =
* Added params defaults to function called in action `wp_nav_menu_item_custom_fields` to avoid PHP warning for an improper call. Props for [vmaxs](https://wordpress.org/support/users/vmaxs/)
* Added permanent hide for menu pointer when a user visits the PWA Settings page.

= 1.4.1 (2022-04-05) =
* Updated iWorks Options to 2.8.3. (Fixed PHP 7.x compatibility).

= 1.4.0 (2022-04-05) =
* Added [Menu Icons by ThemeIsle](https://wordpress.org/plugins/menu-icons/) plugin integration for PWA Shortcuts.
* Added a message when the site permalink is installed in a sub-directory - this plugin does not support it.
* Added PWA Shortcuts Menu. [Read more about PWA Shortcuts](https://developer.mozilla.org/en-US/docs/Web/Manifest/shortcuts).
* Updated iWorks Options to 2.8.3.

= 1.3.3 (2022-03-22) =
* Replaced cache function o proper one.
* Fixed problem with deleting general icon.

= 1.3.2 (2022-03-22) =
* Fixed wrong option name.

= 1.3.1 (2022-03-22) =
* Added `/ieconfig.xml` link on debug tab.
* Fixed long site title on the offline page. Props for [tanohex](https://wordpress.org/support/users/tanohex/).
* Fixed missed translation domain in a few strings.
* Improved usage of the transient cache. Props for [tanohex](https://wordpress.org/support/users/tanohex/).
* Removed images from debug tab.

= 1.3.0 (2022-03-16) =
* Added object cache for settings.
* Fixed `protected $option_name_icons` warning.

= 1.2.3 (2022-02-23) =
* Removed `console.log` from JavaScript files.

= 1.2.2 (2022-02-21) =
* Added filter `iworks_plugin_get_options' to allow filtering plugin core configuration.
* Added [OG — Better Share on Social Media](https://wordpress.org/plugins/og/) plugin integration.
* Added plugin information into PWA script elements.
* Added purpose "any maskable" to the biggest icon. Props for [vmaxs](https://wordpress.org/support/users/vmaxs/).
* Updated iWorks Options to 2.8.2.
* Updated iWorks Rate to 2.1.0.

= 1.2.1 (2022-02-16) =
* Added a message when the site permalink is "plain" - this plugin does not support it.
* Updated iWorks Options to 2.8.1.

= 1.2.0 (2022-02-15) =
* Added the ability to change the text of the offline page.
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
* Added configuration for the application name.
* Added configuration for application colours.
* Added configuration for application description.
* Added configuration for application display.
* Added configuration for application icons.
* Added configuration for application orientation.
* Added configuration for application short name.
* Changed plugin name from "iWorks PWA" to "PWA — simple way to Progressive Web App".
* Updated iWorks Options to 2.8.0.
* Updated iWorks Rate to 2.0.6.

= 1.0.0 (2022-01-04) =
* First stable release.
* Added check for the non-SSL site - SSL is required for PWA.
* Added "Rate" module.
* Fixed duplicates in offline URLs set.
* Bumped offline version to 2.

= 0.0.2 (2021-04-26) =
* Added meta `theme-color`. Props for [forexonlineproductionltd](https://wordpress.org/support/users/forexonlineproductionltd/).

= 0.0.1 (2021-03-18) =
* Init.

== Upgrade Notice ==

