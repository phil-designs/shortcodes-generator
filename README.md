# PhilDesigns Shortcodes Generator

**A shortcode generator to add buttons, columns, tabs, toggles, accordions, animations, social icons, Google Maps, count-up numbers, and video embeds to any page or post.**

Tags: shortcodes, buttons, columns, tabs, toggles
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 7.4
License: GPL-2.0-or-later

---

## Description

PhilDesigns Shortcodes Generator gives you a library of ready-to-use shortcodes for common page-building tasks — all insertable from a popup inside the Classic Editor, Gutenberg, and ACF WYSIWYG fields.

**Available shortcodes:**

- **Buttons** — primary and ghost styles with configurable border radius and custom colours
- **Columns** — 12-column flexbox grid with configurable gap
- **Tabs** — jQuery UI tabbed content panels
- **Toggles** — collapsible content blocks with open/closed default state
- **Accordions** — jQuery UI accordion with configurable default section
- **Animations** — scroll-triggered animate.css effects via WOW.js
- **Alert Boxes** — info, note, download, and warning styles with optional close button
- **Count Up** — animated number counters with prefix, suffix, and thousands separator
- **Google Maps** — embed a map by address, with optional API key for Maps Embed API v1
- **Video Embeds** — YouTube and Vimeo embeds with autoplay and related-video controls
- **Social Icons** — Font Awesome icon links in plain, circle, or square styles

## Website

https://phildesigns.com

---

## Installation

1. Upload the `shortcodes-generator` folder to `/wp-content/plugins/`
2. Activate the plugin through the **Plugins** menu in WordPress
3. Click the **Insert Shortcode** button in the Classic Editor toolbar, or use the shortcode icon in the Gutenberg sidebar
4. Choose a shortcode type, fill in the fields, and click **Insert Shortcode**

### Theme Mode

The plugin can also be loaded from a theme by adding the following to `functions.php`:

```php
add_action( 'after_setup_theme', 'load_shortcodes_generator' );
add_filter( 'pdsc_theme_mode', '__return_true' );

function load_shortcodes_generator() {
    if ( ! class_exists( 'PDSC_Shortcodes' ) ) {
        include_once TEMPLATEPATH . '/shortcodes-generator/shortcodes-generator.php';
    }
}
```

---

## Changelog

### 4.1.0
- Expanded settings page into a tabbed interface with per-shortcode and global options.
- General: toggle FontAwesome loading and override the CDN URL; disable scroll animations on mobile; enable/disable individual shortcodes independently.
- Buttons: default style and link target; border radius preset; colour pickers for primary and ghost button styles.
- Toggles: global default open/closed state.
- Accordions: global default open section.
- Google Maps: optional API key, default height and zoom level.
- Count Up: animation duration, thousands separator, global prefix and suffix.
- Video Embeds: default source, autoplay toggle, show/hide related videos.
- Animations: global default speed.
- Social Icons: style (plain/circle/square) and size.
- Alert Boxes: default alert type.
- Columns: configurable gap size.

### 4.0.1
- Added Count Up shortcode.

### 4.0.0
- Added settings page.
- Added Gutenberg and ACF Visual Editor support.
- Added animation shortcodes via animate.css.
- Restructured columns markup to use flexbox.

### 3.0.0
- Recoded entire plugin.

### 2.0.0
- Added additional shortcodes and FontAwesome.

### 1.0.0
- Initial release.
