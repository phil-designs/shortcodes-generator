=== Phil.Designs Shortcode  Generator ===
Tags: css, jQuery, php
Requires at least: 3.6.0
Tested up to: 6.9.1
License: GPL2

== Description ==
A shortcode generator to add buttons, columns, tabs, toggles and more to your theme.

= Tested on =
* Firefox 
* Safari
* Chrome
* Opera
* MS Edge

= Website =
http://www.alchemyandaim.com/

== Installation ==

AS a plugin:

1. Upload ‘shortcodes-generator’ to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click the ‘+ Insert Shortcode’ button in the visual editor on a page or post
4. Insert details into the desired shortcode popup window to add to your page or post

INTEGRATED in your theme:

1. Upload ‘shortcodes-generator’ to the '/wp-content/themes/your-theme-name’ directory
2. Add the following code to your functions.php:
	// Run this code on 'after_theme_setup', when plugins have already been loaded.
	add_action('after_setup_theme', 'load_shortcodes');
	// Run plugin in theme mode.	
	add_filter( 'pdsc_theme_mode', '__return_true' );
	// This function loads the plugin.
	function load_shortcodes() {
	if( ! class_exists('Shortcodes') ) {	
	// load if not already loaded
	include_once( TEMPLATEPATH. ‘/shortcodes-generator/shortcodes-generator.php' );		
	}
	}
3. Click the ‘+ Insert Shortcode’ button in the visual editor on a page or post, or under the plugin icon on the top sidebar within the Gutenberg editor
4. Insert details into the desired shortcode popup window to add to your page or post

== Changelog ==
Version 4.0.0
* Added settings page (Settings → Shortcodes) allowing admins to provide custom CSS which is output inline after the plugin stylesheet.
* Added support for both Gutenberg & ACF Visual Editors.
* Added additional shortcodes for animations form animate.css
* Restructured the columns markup to use flexbox

Version 3.0.0
• Recoded entire shortcode
• Eliminated depreciated TinyMce plugin

Version 2.0.0
• Code & style adjustments 
• Added additional shortcodes
• Added fontawesome

Version 1.0.0
• Initial build phase 1 (unreleased)