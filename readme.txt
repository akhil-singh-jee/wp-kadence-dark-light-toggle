=== KD Dark Light Toggle ===
Contributors: asj
Tags: dark mode, kadence, dark light toggle, night mode, color palette
Requires at least: 5.7
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 2.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free dark mode and light mode switch for the Kadence theme. Flip between your Kadence Global Palettes with one click.

== Description ==

If you use the Kadence theme, you already have Global Palettes. This plugin lets you pick one palette as your light look and another as your dark look. Visitors get a small sun / moon switch and the whole site changes colour right away.

**Why people like it**

* Made for the Kadence theme, uses the colours you already set
* Optional auto dark mode that follows the visitor's phone or computer
* Remembers the visitor's choice in their own browser (no cookies)
* Very light, no extra libraries
* Safe with caching plugins
* Floating button, shortcode `[kd_toggle]`, or widget
* Show or hide the switch on chosen pages
* Free for personal and business use

== Installation ==

1. Go to Plugins > Add New > Upload Plugin and upload the zip.
2. Activate the plugin.
3. Go to Settings > KD Dark / Light.
4. Choose which palette is light and which is dark (they must be different).
5. Save. The switch now shows on your site.

== Frequently Asked Questions ==

= Does it only work with Kadence? =
It is built for Kadence and reads the Kadence Global Palette. With other themes the switch stays off.

= The switch does not show up. =
Check that the light and dark palettes are different, that you saved your Global Palette once in the Kadence Customizer, and that the page is not hidden in the visibility settings.

= Does it use cookies? =
No. The choice is stored in the visitor's browser (local storage) only.

= How do I put the switch in my menu or a page? =
Use the shortcode [kd_toggle] in a Shortcode or HTML block, or add the widget under Appearance > Widgets.

== Changelog ==

= 2.2.0 =
* Renamed to KD Dark Light Toggle. Author: ASJ.

= 2.1.2 =
* Updates now replace the existing plugin (fixed folder and settings name). Older settings are carried over.

= 2.1.1 =
* Fixed a fatal "Cannot redeclare function" error with other plugins by giving everything a unique prefix.
* Added LICENSE and README.

= 2.1.0 =
* Dark / Light switch with auto dark mode, floating button, shortcode, widget and page visibility rules.
