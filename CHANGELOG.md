# Changelog

Entries below cover releases before 1.6.0. For recent changes, see the [readme.txt changelog](readme.txt) or [GitHub releases](https://github.com/iworks/iworks-pwa/releases).

## 1.5.9 - 2024-02-24

- Added several icon sizes. [#3](https://github.com/iworks/iworks-pwa/issues/4). Thanks to [wfrank94](https://wordpress.org/support/users/wfrank94/).
- Changed the plugin URL to [GitHub](https://github.com/iworks/iworks-pwa).
- Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.9.2.
- Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.1.8.

## 1.5.8 - 2023-12-27

- Added a check for the `viewport` meta tag following plugin activation changes.
- Fixed a dynamic property deprecation issue.
- Replaced the `parse_url()` function with `wp_parse_url()`.
- Added a nonce check to the "viewport" detection feature.
- Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.9.0.
- Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.1.6.

## 1.5.7 - 2023-11-17

- [Fixed empty shortcuts in the `manifest.json` file](https://github.com/iworks/iworks-pwa/issues/4). Thanks to [elmando111](https://wordpress.org/support/users/elmando111/).
- Added the `iworks-pwa/manifest/shortcuts` filter, which allows modifying the shortcuts array in the `manifest.json` file.
- Added the `iworks-pwa/manifest/shortcuts/element/url/campain` filter, which allows modifying the campaign parameter in shortcut URLs.
- Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.8.

## 1.5.6 - 2023-10-27

- Fixed an incorrect function name, `get_color_background()`. Thanks to [bodhisattvac](https://wordpress.org/support/users/bodhisattvac/).
- Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.7.
- Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.1.3.

## 1.5.5 - 2023-07-10

- Added a check for the PWA files required for the plugin to operate.
- Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.5.
- Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.1.2.

## 1.5.4 - 2023-06-30

- Added the `apple-mobile-web-app-capable` meta tag.
- Removed trailing slashes from `link` and `meta` tags.
- Completely rewrote the Apple-related PWA features. Thanks to [James](https://wordpress.org/support/users/glidem/).

## 1.5.3 - 2022-11-16

- Fixed a translation string.
- Added the `id` property to `manifest.json`. [Read more](https://developer.chrome.com/blog/pwa-manifest-id/).
- Added a 45-character limit on the `name` property of `manifest.json`, applied when the plugin is installed.
- Added a 15-character limit on the `short_name` property of `manifest.json`, applied when the plugin is installed.

## 1.5.2 - 2022-09-26

- Fixed an issue with `apple-touch-icon`, which was not applied even when it was defined.
- Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.1.1.

## 1.5.1 - 2022-09-10

- Added a check for the `viewport` meta tag; it is now added when missing. Thanks to [Bert](https://wordpress.org/support/users/bertluch/).
- Changed the [iWorks Rate](https://github.com/iworks/iworks-rate) module repository to GitHub.

## 1.5.0 - 2022-08-03

- Added an "Add to Home screen" button that displays the browser's application installation prompt. See [browser compatibility](https://developer.mozilla.org/en-US/docs/Web/API/BeforeInstallPromptEvent#browser_compatibility).
- Added a Google Analytics campaign parameter to `start_url` in the `manifest.json` file.

## 1.4.3 - 2022-05-06

- Fixed an issue with the Microsoft square icon. Thanks to [chickendipper](https://wordpress.org/support/users/chickendipper/).
- Fixed compatibility with PHP versions lower than 8.0. Thanks to [bamsik001](https://wordpress.org/support/users/bamsik001/).
- Added caching for the HTML head section containing Microsoft data.

## 1.4.2 - 2022-04-08

- Added default parameters to the function invoked by the `wp_nav_menu_item_custom_fields` action to prevent a PHP warning caused by improper calls. Thanks to [vmaxs](https://wordpress.org/support/users/vmaxs/).
- Added an option to permanently hide the menu pointer for users who have visited the PWA settings page.

## 1.4.1 - 2022-04-05

- Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.4.

## 1.4.0 - 2022-04-05

- Added integration with the [Menu Icons by ThemeIsle](https://wordpress.org/plugins/menu-icons/) plugin for PWA shortcuts.
- Added a notice when the site permalink points to a subdirectory, which this plugin does not support.
- Added the PWA Shortcuts menu. [Read more about PWA shortcuts](https://developer.mozilla.org/en-US/docs/Web/Manifest/shortcuts).
- Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.3.

## 1.3.3 - 2022-03-22

- Replaced an incorrect cache function with the proper one.
- Fixed an issue with deleting the general icon.

## 1.3.2 - 2022-03-22

- Fixed an incorrect option name.

## 1.3.1 - 2022-03-22

- Added the `/ieconfig.xml` link to the debug tab.
- Fixed long site titles breaking the offline page layout. Thanks to [tanohex](https://wordpress.org/support/users/tanohex/).
- Fixed a missing translation domain in several strings.
- Improved the use of the transient cache. Thanks to [tanohex](https://wordpress.org/support/users/tanohex/).
- Removed images from the debug tab.

## 1.3.0 - 2022-03-16

- Added an object cache for settings.
- Fixed a warning related to `protected $option_name_icons`.

## 1.2.3 - 2022-02-23

- Removed `console.log` statements from JavaScript files.

## 1.2.2 - 2022-02-21

- Added the `iworks_plugin_get_options` filter to allow filtering of the plugin's core configuration.
- Added integration with the [OG — Better Share on Social Media](https://wordpress.org/plugins/og/) plugin.
- Added plugin information to the PWA script elements.
- Added the "any maskable" purpose to the largest icon. Thanks to [vmaxs](https://wordpress.org/support/users/vmaxs/).
- Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.2.
- Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.1.0.

## 1.2.1 - 2022-02-16

- Added a notice when the site permalink is set to "plain", which this plugin does not support.
- Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.1.

## 1.2.0 - 2022-02-15

- Added the ability to change the text of the offline page.
- Added a version parameter to cache control.
- Added integration with the WPML plugin.
- Moved the service worker JavaScript from a PHP class into a separate template.

## 1.1.6 - 2022-01-27

- Added screenshots to `readme.txt`.
- Removed debugging functions.

## 1.1.5 - 2022-01-27

- Added the Apple pinned tab icon.
- Added the Apple launch icon title.
- Added Microsoft pinned site support.
- Added Microsoft Live Tile support for IE11.
- Added an HTML prefetch for `manifest.json`.
- Refactored the options handling.

## 1.1.4 - 2022-01-24

- Fixed a typographical error.

## 1.1.3 - 2022-01-24

- Fixed a class loading order issue.

## 1.1.2 - 2022-01-24

- Added a `method_exists` check to verify that the iWorks Options class provides the `set_plugin` method.
- Changed the plugin name to "PWA — easy way to Progressive Web App".
- Removed unwanted values from `manifest.json`.
- Improved handling of `/manifest.json`.

## 1.1.1 - 2022-01-23

- Added configuration for Apple splash screen icons.
- Added configuration for the Apple touch icon.
- Added configuration for IE11.
- Added configuration for Microsoft tile icons.

## 1.1.0 - 2022-01-21

- Added configuration for the application name.
- Added configuration for the application colours.
- Added configuration for the application description.
- Added configuration for the application display mode.
- Added configuration for the application icons.
- Added configuration for the application orientation.
- Added configuration for the application short name.
- Changed the plugin name from "iWorks PWA" to "PWA — easy way to Progressive Web App".
- Updated the [iWorks Options](https://github.com/iworks/wordpress-options-class) module to version 2.8.0.
- Updated the [iWorks Rate](https://github.com/iworks/iworks-rate) module to version 2.0.6.

## 1.0.0 - 2022-01-04

- First stable release.
- Added a check for non-SSL sites, as SSL is required for a PWA.
- Added the "Rate" module.
- Fixed duplicates in the offline URL set.
- Increased the offline version to 2.

## 0.0.2 - 2021-04-26

- Added the `theme-color` meta tag. Thanks to [forexonlineproductionltd](https://wordpress.org/support/users/forexonlineproductionltd/).

## 0.0.1 - 2021-03-18

- Initial release.
