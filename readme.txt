=== PhilDesigns Shortcodes Generator ===
Contributors: phildesigns
Tags: shortcodes, buttons, columns, tabs, toggles
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 4.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A shortcode generator to add buttons, columns, tabs, toggles, accordions, animations, social icons, Google Maps, count-up numbers, and video embeds.

== Description ==

PhilDesigns Shortcodes Generator gives you a library of ready-to-use shortcodes for common page-building tasks — all insertable from a popup inside the Classic Editor, Gutenberg, and ACF WYSIWYG fields.

= Available Shortcodes =

* **Buttons** — primary and ghost styles with configurable border radius and custom colours
* **Columns** — 12-column flexbox grid with configurable gap
* **Tabs** — jQuery UI tabbed content panels
* **Toggles** — collapsible content blocks with open/closed default state
* **Accordions** — jQuery UI accordion with configurable default section
* **Animations** — scroll-triggered animate.css effects via WOW.js
* **Alert Boxes** — info, note, download, and warning styles with optional close button
* **Count Up** — animated number counters with prefix, suffix, and thousands separator
* **Google Maps** — embed a map by address, with optional API key for the Maps Embed API v1
* **Video Embeds** — YouTube and Vimeo embeds with autoplay and related-video controls
* **Social Icons** — Font Awesome icon links in plain, circle, or square styles

= Settings =

Go to **Settings → Shortcodes Generator** to configure global defaults for every shortcode type, load or disable FontAwesome, toggle individual shortcodes, and add custom CSS.

== Installation ==

1. Upload the `shortcodes-generator` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Click the **Insert Shortcode** button in the Classic Editor toolbar, or use the shortcode icon in the Gutenberg sidebar
4. Choose a shortcode type, fill in the fields, and click **Insert Shortcode**

== Frequently Asked Questions ==

= Does this work with Gutenberg? =

Yes. A shortcode panel is available in the Gutenberg sidebar. Shortcodes can also be used directly in a Shortcode block.

= Does this work with ACF? =

Yes. The Insert Shortcode button appears in ACF WYSIWYG fields.

= Can I disable shortcodes I don't use? =

Yes. Go to **Settings → Shortcodes Generator → General** and uncheck any shortcodes you don't need. Disabled shortcodes will not be registered and their scripts will not be loaded.

= Can I use my own FontAwesome version? =

Yes. Uncheck **Load FontAwesome** if your theme already loads it, or enter a custom CDN URL in the **FontAwesome CDN URL** field to use a specific version. By default the plugin bundles FontAwesome 6.6.0 locally.

== Screenshots ==

1. The Insert Shortcode popup with the Buttons form open.
2. The settings page showing the tabbed interface.
3. The Gutenberg sidebar panel.
4. Example front-end output with buttons, columns, and alert boxes.

== Changelog ==

= 4.1.0 =
* Expanded settings page into a tabbed interface with per-shortcode and global options.
* General: toggle FontAwesome loading and override the CDN URL; disable scroll animations on mobile; enable/disable individual shortcodes independently.
* Buttons: default style and link target; border radius preset; colour pickers for primary and ghost button styles.
* Toggles: global default open/closed state.
* Accordions: global default open section.
* Google Maps: optional API key, default height and zoom level.
* Count Up: animation duration, thousands separator, global prefix and suffix.
* Video Embeds: default source, autoplay toggle, show/hide related videos.
* Animations: global default speed.
* Social Icons: style (plain/circle/square) and size.
* Alert Boxes: default alert type.
* Columns: configurable gap size.

= 4.0.1 =
* Added Count Up shortcode.

= 4.0.0 =
* Added settings page.
* Added Gutenberg and ACF Visual Editor support.
* Added animation shortcodes via animate.css.
* Restructured columns markup to use flexbox.

= 3.0.0 =
* Recoded entire plugin.

= 2.0.0 =
* Added additional shortcodes.
* Added FontAwesome.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 4.1.0 =
Adds a full tabbed settings page with per-shortcode defaults and style controls.
