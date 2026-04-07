<?php
/*
Plugin Name: Phil.Designs Shortcodes Generator
Plugin URI: http://www.phildesigns.com
Description: A shortcode generator to add buttons, columns, tabs, toggles and more to your theme.
Version: 4.1.0
Author: phil.designs | Phillip De Vita
Author URI: http://www.phildesigns.com
*/

class Shortcodes {

	function __construct()
	{
		define( 'PDSC_VERSION', '4.1.0' );
		// option name for user entered CSS
		if ( ! defined( 'PDSC_CUSTOM_CSS_OPTION' ) ) {
			define( 'PDSC_CUSTOM_CSS_OPTION', 'pdsc_custom_css' );
		}

		/** Create a filter for "Theme Mode" **/
		define( 'PDSC_THEME_MODE', apply_filters( 'pdsc_theme_mode', false ) );

		if ( false == PDSC_THEME_MODE) {
			define( 'PDSC_PLUGIN_URL', plugin_dir_url( __FILE__ ));
			define( 'PDSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
		} else {
			if ( true == PDSC_THEME_MODE ) {
				$path = ltrim( end( @explode( get_stylesheet(), str_replace( '\\', '/', dirname( __FILE__ ) ) ) ), '/' );
				define( 'PDSC_PLUGIN_URL',  trailingslashit(trailingslashit(get_bloginfo('template_directory') ). $path));
				define( 'PDSC_PLUGIN_DIR', plugin_dir_path( __FILE__ ));
			}
		}

		require_once( PDSC_PLUGIN_DIR .'includes/shortcodes.php' );

		// enqueue scripts/styles on the front end at the proper time
		add_action( 'wp_enqueue_scripts', array( $this, 'init' ) );
		add_action( 'admin_init', array(&$this, 'admin_init') );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		// always load tinymce helper script on admin pages so any editor can register the plugin
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		// add settings link under plugin name on Plugins screen
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_settings_link' ) );
	}

	/**
	 * Enqueue front end scripts and styles
	 *
	 * @return void
	 */
	function init()
	{
		if ( ! is_admin() ) {
			$disabled = (array) get_option( 'pdsc_disabled_shortcodes', array() );

			// FontAwesome
			$load_fa_option = get_option( 'pdsc_load_fontawesome', '1' );
			if ( $load_fa_option !== '0' ) {
				$already_loaded = false;
				$handles = array( 'fontawesome', 'font-awesome', 'fa' );
				foreach ( $handles as $handle ) {
					if ( wp_style_is( $handle, 'registered' ) || wp_style_is( $handle, 'enqueued' ) ) {
						$already_loaded = true;
						break;
					}
				}
				$load_fa = apply_filters( 'pdsc_load_fontawesome', ! $already_loaded );
				if ( $load_fa ) {
					$fa_url = get_option( 'pdsc_fontawesome_cdn_url', '' );
					if ( empty( $fa_url ) ) {
						$fa_url = apply_filters( 'pdsc_fontawesome_cdn_url', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css' );
					}
					wp_enqueue_style( 'fontawesome', $fa_url );
				}
			}

			wp_enqueue_style( 'shortcodes', PDSC_PLUGIN_URL . 'assets/css/shortcodes.css' );

			// Build and inject dynamic CSS then custom CSS as one inline block
			$inline_css = $this->get_dynamic_css();
			$custom_css = get_option( PDSC_CUSTOM_CSS_OPTION, '' );
			if ( ! empty( $custom_css ) ) {
				$inline_css .= $custom_css;
			}
			if ( ! empty( $inline_css ) ) {
				wp_add_inline_style( 'shortcodes', $inline_css );
			}

			wp_enqueue_script( 'shortcodes-lib', PDSC_PLUGIN_URL . 'assets/js/shortcodes-lib.js', array('jquery', 'jquery-ui-accordion', 'jquery-ui-tabs'), PDSC_VERSION, true );

			// Animations — skip if shortcode is disabled
			if ( ! in_array( 'animate', $disabled ) ) {
				wp_enqueue_style( 'animate', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', array(), '4.1.1' );
				wp_enqueue_style( 'wow-animate', PDSC_PLUGIN_URL . 'assets/css/animate.css', array(), '1.1.2' );
				wp_enqueue_script( 'wow', 'https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js', array(), '1.1.2', true );
			}

			// Count Up — skip if shortcode is disabled
			if ( ! in_array( 'count_up', $disabled ) ) {
				wp_enqueue_script( 'waypoints', PDSC_PLUGIN_URL . 'assets/js/jquery.waypoints.min.js', array(), '4.0.1', true );
				wp_enqueue_script( 'countup', PDSC_PLUGIN_URL . 'assets/js/jquery.countup.min.js', array( 'waypoints' ), '2.0.0', true );
				wp_enqueue_script( 'call-countup', PDSC_PLUGIN_URL . 'assets/js/count-up.js', array( 'countup' ), '1.0.0', true );

				$duration = absint( get_option( 'pdsc_countup_duration', 2000 ) );
				if ( $duration < 500 ) {
					$duration = 2000;
				}
				wp_localize_script( 'call-countup', 'pdscCountUp', array( 'time' => $duration ) );
			}
		}
	}

	/**
	 * Build dynamic CSS from plugin settings to inject on the front end.
	 *
	 * @return string
	 */
	private function get_dynamic_css() {
		$css = '';

		// --- Disable WOW.js animations on mobile ---
		if ( get_option( 'pdsc_disable_animations_mobile', '0' ) === '1' ) {
			$css .= '@media (max-width: 767px) { .wow { animation: none !important; opacity: 1 !important; visibility: visible !important; } }';
		}

		// --- Button border radius ---
		$radius_key = get_option( 'pdsc_button_border_radius', 'sharp' );
		$radius_map = array( 'sharp' => '0', 'rounded' => '4px', 'pill' => '50px' );
		$radius     = isset( $radius_map[ $radius_key ] ) ? $radius_map[ $radius_key ] : '0';
		if ( $radius !== '0' ) {
			$css .= '.primary-button, .ghost-button { border-radius: ' . $radius . '; }';
		}

		// --- Button colors (only output when options have been saved) ---
		$primary_bg    = get_option( 'pdsc_button_primary_bg', '' );
		$primary_text  = get_option( 'pdsc_button_primary_text', '' );
		$primary_hover = get_option( 'pdsc_button_primary_hover_bg', '' );
		$ghost_color   = get_option( 'pdsc_button_ghost_color', '' );
		$ghost_hover   = get_option( 'pdsc_button_ghost_hover_color', '' );

		if ( $primary_bg || $primary_text ) {
			$css .= '.primary-button {'
				. ( $primary_bg   ? ' background-color: ' . esc_attr( $primary_bg ) . '; border-color: ' . esc_attr( $primary_bg ) . ';' : '' )
				. ( $primary_text ? ' color: ' . esc_attr( $primary_text ) . ';' : '' )
				. ' }';
		}
		if ( $primary_hover ) {
			$css .= '.primary-button:hover, .primary-button:focus { background-color: ' . esc_attr( $primary_hover ) . '; border-color: ' . esc_attr( $primary_hover ) . '; }';
		}
		if ( $ghost_color ) {
			$css .= '.ghost-button { color: ' . esc_attr( $ghost_color ) . '; border-color: ' . esc_attr( $ghost_color ) . '; }';
		}
		if ( $ghost_hover ) {
			$css .= '.ghost-button:hover, .ghost-button:focus { color: ' . esc_attr( $ghost_hover ) . '; border-color: ' . esc_attr( $ghost_hover ) . '; }';
		}

		// --- Column gap ---
		$col_gap = absint( get_option( 'pdsc_column_gap', 20 ) );
		if ( $col_gap !== 20 ) {
			$css .= $this->get_column_gap_css( $col_gap );
		}

		// --- Social icons ---
		$social_style = get_option( 'pdsc_social_icon_style', 'plain' );
		$social_size  = get_option( 'pdsc_social_icon_size', 'medium' );
		$font_sizes   = array( 'small' => '0.75em', 'medium' => '1em', 'large' => '1.5em' );
		$box_sizes    = array( 'small' => '32px',   'medium' => '40px', 'large' => '50px' );
		$font_size    = isset( $font_sizes[ $social_size ] ) ? $font_sizes[ $social_size ] : '1em';
		$box_size     = isset( $box_sizes[ $social_size ] )  ? $box_sizes[ $social_size ]  : '40px';

		if ( $social_style === 'circle' || $social_style === 'square' ) {
			$border_radius = ( $social_style === 'circle' ) ? '50%' : '0';
			// Use flexbox on the <ul> to create even gaps without float interference
			$css .= 'ul.social-icons { display: flex; flex-wrap: wrap; gap: 8px; padding: 15px 0; }';
			$css .= '.social-icons li { float: none; margin: 0; padding: 0; }';
			$css .= '.social-icons a {'
				. ' display: inline-flex; align-items: center; justify-content: center;'
				. ' width: ' . $box_size . '; height: ' . $box_size . ';'
				. ' border-radius: ' . $border_radius . ';'
				. ' background-color: #333; color: #fff;'
				. ' text-decoration: none; font-size: ' . $font_size . '; }';
			$css .= '.social-icons a:hover { background-color: #555; color: #fff; }';
		} else {
			// Plain style — explicit font-size for all three sizes
			$css .= '.social-icons a { font-size: ' . $font_size . '; }';
		}

		return $css;
	}

	/**
	 * Generate CSS overrides for column span widths when the gap differs from the default 20px.
	 * Uses the same percentage values as shortcodes.css with a recalculated gap deduction.
	 * Formula: deduction = gap × (100 − pct) / 100  (verified against all original calc values)
	 *
	 * @param int $gap Gap in pixels.
	 * @return string CSS string wrapped in a media query.
	 */
	private function get_column_gap_css( $gap ) {
		// Percentages match shortcodes.css exactly (span-1=10%, span-11=90% by design)
		$span_pcts = array(
			1 => 10, 2 => 16.6, 3 => 25,   4 => 33.3, 5 => 41.6,
			6 => 50, 7 => 58.3, 8 => 66.6, 9 => 75,   10 => 83.3,
			11 => 90, 12 => 100,
		);

		$inner = '';
		foreach ( $span_pcts as $span => $pct ) {
			$deduction = round( $gap * ( 100 - $pct ) / 100, 2 );
			if ( $deduction > 0 ) {
				$inner .= ".grid.grid-12-col .cell.span-{$span} { flex: 0 0 calc({$pct}% - {$deduction}px); }";
			} else {
				$inner .= ".grid.grid-12-col .cell.span-{$span} { flex: 0 0 {$pct}%; }";
			}
		}

		return "@media (min-width: 768px) { {$inner} }";
	}

	/**
	 * Enqueue Scripts and Styles for admin
	 *
	 * @return void
	 */
	function admin_init()
	{
		include_once( PDSC_PLUGIN_DIR . 'includes/class-admin-insert.php' );

		// --- Custom CSS ---
		register_setting( 'pdsc_settings', PDSC_CUSTOM_CSS_OPTION, array( 'sanitize_callback' => array( $this, 'sanitize_css' ) ) );

		// --- Global / Performance ---
		register_setting( 'pdsc_settings', 'pdsc_load_fontawesome',          array( 'sanitize_callback' => array( $this, 'sanitize_checkbox' ) ) );
		register_setting( 'pdsc_settings', 'pdsc_fontawesome_cdn_url',        array( 'sanitize_callback' => 'esc_url_raw' ) );
		register_setting( 'pdsc_settings', 'pdsc_disable_animations_mobile',  array( 'sanitize_callback' => array( $this, 'sanitize_checkbox' ) ) );
		register_setting( 'pdsc_settings', 'pdsc_disabled_shortcodes',        array( 'sanitize_callback' => array( $this, 'sanitize_disabled_shortcodes' ) ) );

		// --- Buttons ---
		register_setting( 'pdsc_settings', 'pdsc_button_default_style',   array( 'sanitize_callback' => array( $this, 'sanitize_button_style' ) ) );
		register_setting( 'pdsc_settings', 'pdsc_button_default_target',  array( 'sanitize_callback' => array( $this, 'sanitize_link_target' ) ) );
		register_setting( 'pdsc_settings', 'pdsc_button_border_radius',   array( 'sanitize_callback' => array( $this, 'sanitize_border_radius' ) ) );

		// --- Toggles ---
		register_setting( 'pdsc_settings', 'pdsc_toggle_default_state', array( 'sanitize_callback' => array( $this, 'sanitize_toggle_state' ) ) );

		// --- Accordions ---
		register_setting( 'pdsc_settings', 'pdsc_accordion_default_open', array( 'sanitize_callback' => array( $this, 'sanitize_accordion_open' ) ) );

		// --- Google Maps ---
		register_setting( 'pdsc_settings', 'pdsc_maps_api_key',       array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'pdsc_settings', 'pdsc_maps_default_height', array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'pdsc_settings', 'pdsc_maps_default_zoom',   array( 'sanitize_callback' => 'absint' ) );

		// --- Count Up ---
		register_setting( 'pdsc_settings', 'pdsc_countup_duration',  array( 'sanitize_callback' => 'absint' ) );
		register_setting( 'pdsc_settings', 'pdsc_countup_separator', array( 'sanitize_callback' => array( $this, 'sanitize_countup_separator' ) ) );
		register_setting( 'pdsc_settings', 'pdsc_countup_prefix',    array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'pdsc_settings', 'pdsc_countup_suffix',    array( 'sanitize_callback' => 'sanitize_text_field' ) );

		// --- Video ---
		register_setting( 'pdsc_settings', 'pdsc_video_default_source', array( 'sanitize_callback' => array( $this, 'sanitize_video_source' ) ) );
		register_setting( 'pdsc_settings', 'pdsc_video_autoplay',        array( 'sanitize_callback' => array( $this, 'sanitize_checkbox' ) ) );
		register_setting( 'pdsc_settings', 'pdsc_video_related',         array( 'sanitize_callback' => array( $this, 'sanitize_checkbox' ) ) );

		// --- Animations ---
		register_setting( 'pdsc_settings', 'pdsc_animation_default_speed', array( 'sanitize_callback' => array( $this, 'sanitize_animation_speed' ) ) );

		// --- Social Icons ---
		register_setting( 'pdsc_settings', 'pdsc_social_icon_style', array( 'sanitize_callback' => array( $this, 'sanitize_social_style' ) ) );
		register_setting( 'pdsc_settings', 'pdsc_social_icon_size',  array( 'sanitize_callback' => array( $this, 'sanitize_social_size' ) ) );

		// --- Alert Boxes ---
		register_setting( 'pdsc_settings', 'pdsc_alert_default_type', array( 'sanitize_callback' => array( $this, 'sanitize_alert_type' ) ) );

		// --- Button Colors ---
		register_setting( 'pdsc_settings', 'pdsc_button_primary_bg',       array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'pdsc_settings', 'pdsc_button_primary_text',      array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'pdsc_settings', 'pdsc_button_primary_hover_bg',  array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'pdsc_settings', 'pdsc_button_ghost_color',       array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting( 'pdsc_settings', 'pdsc_button_ghost_hover_color', array( 'sanitize_callback' => 'sanitize_hex_color' ) );

		// --- Columns ---
		register_setting( 'pdsc_settings', 'pdsc_column_gap', array( 'sanitize_callback' => 'absint' ) );

		// ensure thickbox scripts/styles are available for the popup
		add_thickbox();

		// css
		wp_enqueue_style( 'popup', PDSC_PLUGIN_URL . 'assets/css/admin.css', false, '1.0', 'all' );

		// js
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_localize_script( 'jquery', 'Shortcodes', array('plugin_folder' => WP_PLUGIN_URL .'/shortcodes') );

		// register TinyMCE plugin/button for classic editor blocks and ACF
		add_filter( 'mce_external_plugins', array( $this, 'register_tinymce_plugin' ) );
		add_filter( 'mce_buttons', array( $this, 'register_tinymce_button' ) );
	}

	// -------------------------------------------------------------------------
	// Sanitize callbacks
	// -------------------------------------------------------------------------

	public function sanitize_css( $css ) {
		$css = wp_strip_all_tags( $css );
		return trim( $css );
	}

	public function sanitize_checkbox( $val ) {
		return ( $val === '1' ) ? '1' : '0';
	}

	/**
	 * The disabled shortcodes option is posted as an associative array:
	 *   pdsc_disabled_shortcodes[button] = 1  (active / enabled)
	 *   pdsc_disabled_shortcodes[button] = 0  (inactive / disabled)
	 * We store only the DISABLED keys so the default empty-array means all enabled.
	 */
	public function sanitize_disabled_shortcodes( $val ) {
		$allowed  = array( 'button', 'alert', 'toggle', 'accordion', 'tabs', 'columns', 'animate', 'vsgmap', 'count_up', 'embed-video', 'social' );
		$disabled = array();
		if ( ! is_array( $val ) ) {
			return $disabled;
		}
		foreach ( $allowed as $key ) {
			if ( isset( $val[ $key ] ) && $val[ $key ] === '0' ) {
				$disabled[] = $key;
			}
		}
		return $disabled;
	}

	public function sanitize_button_style( $val ) {
		return in_array( $val, array( 'primary-button', 'ghost-button' ), true ) ? $val : 'primary-button';
	}

	public function sanitize_link_target( $val ) {
		return in_array( $val, array( '_self', '_blank' ), true ) ? $val : '_self';
	}

	public function sanitize_border_radius( $val ) {
		return in_array( $val, array( 'sharp', 'rounded', 'pill' ), true ) ? $val : 'sharp';
	}

	public function sanitize_toggle_state( $val ) {
		return in_array( $val, array( 'open', 'closed' ), true ) ? $val : 'open';
	}

	public function sanitize_accordion_open( $val ) {
		return in_array( $val, array( '0', '-1' ), true ) ? $val : '0';
	}

	public function sanitize_countup_separator( $val ) {
		return in_array( $val, array( '', ',' ), true ) ? $val : '';
	}

	public function sanitize_video_source( $val ) {
		return in_array( $val, array( 'youtube', 'vimeo' ), true ) ? $val : 'youtube';
	}

	public function sanitize_animation_speed( $val ) {
		return in_array( $val, array( '', 'animate__slow', 'animate__fast' ), true ) ? $val : '';
	}

	public function sanitize_social_style( $val ) {
		return in_array( $val, array( 'plain', 'circle', 'square' ), true ) ? $val : 'plain';
	}

	public function sanitize_social_size( $val ) {
		return in_array( $val, array( 'small', 'medium', 'large' ), true ) ? $val : 'medium';
	}

	public function sanitize_alert_type( $val ) {
		return in_array( $val, array( 'info', 'note', 'download', 'warning' ), true ) ? $val : 'info';
	}

	// -------------------------------------------------------------------------

	/**
	 * Add the settings page under the "Settings" menu.
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Shortcodes Generator', 'shortcodes-generator' ),
			__( 'Shortcodes Generator Settings', 'shortcodes-generator' ),
			'manage_options',
			'pdsc_settings',
			array( $this, 'settings_page_html' )
		);
	}

	/**
	 * Output the HTML for the plugin settings page.
	 */
	public function settings_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_GET['settings-updated'] ) ) {
			add_settings_error( 'pdsc_messages', 'pdsc_message', __( 'Settings Saved', 'shortcodes-generator' ), 'updated' );
		}
		settings_errors( 'pdsc_messages' );

		// Fetch all option values
		$custom_css            = get_option( PDSC_CUSTOM_CSS_OPTION, '' );
		$load_fa               = get_option( 'pdsc_load_fontawesome', '1' );
		$fa_url                = get_option( 'pdsc_fontawesome_cdn_url', '' );
		$disable_mobile_anim   = get_option( 'pdsc_disable_animations_mobile', '0' );
		$disabled_shortcodes   = (array) get_option( 'pdsc_disabled_shortcodes', array() );

		$button_style  = get_option( 'pdsc_button_default_style', 'primary-button' );
		$button_target = get_option( 'pdsc_button_default_target', '_self' );
		$button_radius = get_option( 'pdsc_button_border_radius', 'sharp' );

		$toggle_state   = get_option( 'pdsc_toggle_default_state', 'open' );
		$accordion_open = get_option( 'pdsc_accordion_default_open', '0' );

		$maps_api_key = get_option( 'pdsc_maps_api_key', '' );
		$maps_height  = get_option( 'pdsc_maps_default_height', '350' );
		$maps_zoom    = get_option( 'pdsc_maps_default_zoom', '14' );

		$countup_duration  = get_option( 'pdsc_countup_duration', '2000' );
		$countup_separator = get_option( 'pdsc_countup_separator', '' );
		$countup_prefix    = get_option( 'pdsc_countup_prefix', '' );
		$countup_suffix    = get_option( 'pdsc_countup_suffix', '' );

		$video_source   = get_option( 'pdsc_video_default_source', 'youtube' );
		$video_autoplay = get_option( 'pdsc_video_autoplay', '0' );
		$video_related  = get_option( 'pdsc_video_related', '1' );

		$anim_speed = get_option( 'pdsc_animation_default_speed', '' );

		$social_style = get_option( 'pdsc_social_icon_style', 'plain' );
		$social_size  = get_option( 'pdsc_social_icon_size', 'medium' );

		$alert_type = get_option( 'pdsc_alert_default_type', 'info' );

		// Button colors — default to the plugin's built-in values so pickers show correct starting point
		$btn_primary_bg    = get_option( 'pdsc_button_primary_bg',       '#bbbbbb' );
		$btn_primary_text  = get_option( 'pdsc_button_primary_text',      '#ffffff' );
		$btn_primary_hover = get_option( 'pdsc_button_primary_hover_bg',  '#333333' );
		$btn_ghost_color   = get_option( 'pdsc_button_ghost_color',       '#bbbbbb' );
		$btn_ghost_hover   = get_option( 'pdsc_button_ghost_hover_color', '#333333' );

		// Columns
		$column_gap = get_option( 'pdsc_column_gap', '20' );

		$all_shortcodes = array(
			'button'      => __( 'Buttons', 'shortcodes-generator' ),
			'alert'       => __( 'Alert Boxes', 'shortcodes-generator' ),
			'toggle'      => __( 'Toggles', 'shortcodes-generator' ),
			'accordion'   => __( 'Accordions', 'shortcodes-generator' ),
			'tabs'        => __( 'Tabs', 'shortcodes-generator' ),
			'columns'     => __( 'Columns', 'shortcodes-generator' ),
			'animate'     => __( 'Animations', 'shortcodes-generator' ),
			'vsgmap'      => __( 'Google Maps', 'shortcodes-generator' ),
			'count_up'    => __( 'Count Up', 'shortcodes-generator' ),
			'embed-video' => __( 'Video Embeds', 'shortcodes-generator' ),
			'social'      => __( 'Social Icons', 'shortcodes-generator' ),
		);
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Shortcodes Generator Settings', 'shortcodes-generator' ); ?></h1>

			<nav class="nav-tab-wrapper" id="pdsc-tabs">
				<a href="#" class="nav-tab nav-tab-active" data-tab="general"><?php esc_html_e( 'General', 'shortcodes-generator' ); ?></a>
				<a href="#" class="nav-tab" data-tab="buttons"><?php esc_html_e( 'Buttons', 'shortcodes-generator' ); ?></a>
				<a href="#" class="nav-tab" data-tab="toggles"><?php esc_html_e( 'Toggles &amp; Accordions', 'shortcodes-generator' ); ?></a>
				<a href="#" class="nav-tab" data-tab="maps"><?php esc_html_e( 'Google Maps', 'shortcodes-generator' ); ?></a>
				<a href="#" class="nav-tab" data-tab="countup"><?php esc_html_e( 'Count Up', 'shortcodes-generator' ); ?></a>
				<a href="#" class="nav-tab" data-tab="video"><?php esc_html_e( 'Video &amp; Animations', 'shortcodes-generator' ); ?></a>
				<a href="#" class="nav-tab" data-tab="social"><?php esc_html_e( 'Social &amp; Alerts', 'shortcodes-generator' ); ?></a>
			</nav>

			<form action="options.php" method="post">
				<?php settings_fields( 'pdsc_settings' ); ?>

				<!-- ========= General Tab ========= -->
				<div id="pdsc-tab-general" class="pdsc-tab-content">

					<h2><?php esc_html_e( 'Performance', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php esc_html_e( 'Load FontAwesome', 'shortcodes-generator' ); ?></th>
							<td>
								<label>
									<input type="hidden"   name="pdsc_load_fontawesome" value="0">
									<input type="checkbox" name="pdsc_load_fontawesome" value="1" <?php checked( $load_fa, '1' ); ?>>
									<?php esc_html_e( 'Enable', 'shortcodes-generator' ); ?>
								</label>
								<p class="description"><?php esc_html_e( 'Uncheck if your theme already loads FontAwesome to avoid loading it twice.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pdsc_fontawesome_cdn_url"><?php esc_html_e( 'FontAwesome CDN URL', 'shortcodes-generator' ); ?></label></th>
							<td>
								<input type="url" id="pdsc_fontawesome_cdn_url" name="pdsc_fontawesome_cdn_url"
									value="<?php echo esc_attr( $fa_url ); ?>" class="regular-text"
									placeholder="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
								<p class="description"><?php esc_html_e( 'Override the default FontAwesome CDN URL. Leave blank to use the built-in default.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Disable Animations on Mobile', 'shortcodes-generator' ); ?></th>
							<td>
								<label>
									<input type="hidden"   name="pdsc_disable_animations_mobile" value="0">
									<input type="checkbox" name="pdsc_disable_animations_mobile" value="1" <?php checked( $disable_mobile_anim, '1' ); ?>>
									<?php esc_html_e( 'Disable scroll animations on devices narrower than 768px', 'shortcodes-generator' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Active Shortcodes', 'shortcodes-generator' ); ?></th>
							<td>
								<?php foreach ( $all_shortcodes as $key => $label ) :
									$is_disabled = in_array( $key, $disabled_shortcodes, true );
								?>
								<label style="display:block; margin-bottom:6px;">
									<input type="hidden"   name="pdsc_disabled_shortcodes[<?php echo esc_attr( $key ); ?>]" value="0">
									<input type="checkbox" name="pdsc_disabled_shortcodes[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( ! $is_disabled ); ?>>
									<?php echo esc_html( $label ); ?>
								</label>
								<?php endforeach; ?>
								<p class="description"><?php esc_html_e( 'Uncheck any shortcodes you do not use. Disabled shortcodes will not be registered and their scripts will not be loaded.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
					</table>

					<h2><?php esc_html_e( 'Columns', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="pdsc_column_gap"><?php esc_html_e( 'Column Gap (px)', 'shortcodes-generator' ); ?></label></th>
							<td>
								<input type="number" id="pdsc_column_gap" name="pdsc_column_gap"
									value="<?php echo esc_attr( $column_gap ?: '20' ); ?>"
									min="0" max="80" step="4" class="small-text">
								<p class="description"><?php esc_html_e( 'Space between columns. Default is 20px. Applies at desktop widths (768px+).', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
					</table>

					<h2><?php esc_html_e( 'Custom CSS', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( PDSC_CUSTOM_CSS_OPTION ); ?>"><?php esc_html_e( 'Custom CSS', 'shortcodes-generator' ); ?></label></th>
							<td>
								<textarea id="<?php echo esc_attr( PDSC_CUSTOM_CSS_OPTION ); ?>"
									name="<?php echo esc_attr( PDSC_CUSTOM_CSS_OPTION ); ?>"
									rows="10" cols="50" class="large-text code"><?php echo esc_textarea( $custom_css ); ?></textarea>
								<p class="description"><?php esc_html_e( 'Enter CSS that will be output on the front end in addition to the plugin styles. Useful for overrides.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
					</table>

				</div><!-- /general -->

				<!-- ========= Buttons Tab ========= -->
				<div id="pdsc-tab-buttons" class="pdsc-tab-content" style="display:none;">

					<h2><?php esc_html_e( 'Button Defaults', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="pdsc_button_default_style"><?php esc_html_e( 'Default Style', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_button_default_style" name="pdsc_button_default_style">
									<option value="primary-button" <?php selected( $button_style, 'primary-button' ); ?>><?php esc_html_e( 'Primary Button', 'shortcodes-generator' ); ?></option>
									<option value="ghost-button"   <?php selected( $button_style, 'ghost-button' ); ?>><?php esc_html_e( 'Ghost Button', 'shortcodes-generator' ); ?></option>
								</select>
								<p class="description"><?php esc_html_e( 'Default style pre-selected when inserting a button shortcode.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pdsc_button_default_target"><?php esc_html_e( 'Default Link Target', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_button_default_target" name="pdsc_button_default_target">
									<option value="_self"  <?php selected( $button_target, '_self' ); ?>><?php esc_html_e( 'Same Window', 'shortcodes-generator' ); ?></option>
									<option value="_blank" <?php selected( $button_target, '_blank' ); ?>><?php esc_html_e( 'New Window', 'shortcodes-generator' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pdsc_button_border_radius"><?php esc_html_e( 'Border Radius', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_button_border_radius" name="pdsc_button_border_radius">
									<option value="sharp"   <?php selected( $button_radius, 'sharp' ); ?>><?php esc_html_e( 'Sharp (0px)', 'shortcodes-generator' ); ?></option>
									<option value="rounded" <?php selected( $button_radius, 'rounded' ); ?>><?php esc_html_e( 'Rounded (4px)', 'shortcodes-generator' ); ?></option>
									<option value="pill"    <?php selected( $button_radius, 'pill' ); ?>><?php esc_html_e( 'Pill (50px)', 'shortcodes-generator' ); ?></option>
								</select>
								<p class="description"><?php esc_html_e( 'Applied globally to all buttons on the site.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
					</table>

					<h2><?php esc_html_e( 'Button Colors', 'shortcodes-generator' ); ?></h2>
					<p class="description" style="margin: -8px 0 16px;"><?php esc_html_e( 'Override the default button colours. These are applied globally via CSS. The defaults match the plugin\'s built-in styles.', 'shortcodes-generator' ); ?></p>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><?php esc_html_e( 'Primary Button', 'shortcodes-generator' ); ?></th>
							<td>
								<label style="margin-right:20px;">
									<?php esc_html_e( 'Background', 'shortcodes-generator' ); ?>&nbsp;
									<input type="color" name="pdsc_button_primary_bg" value="<?php echo esc_attr( $btn_primary_bg ); ?>">
								</label>
								<label style="margin-right:20px;">
									<?php esc_html_e( 'Label', 'shortcodes-generator' ); ?>&nbsp;
									<input type="color" name="pdsc_button_primary_text" value="<?php echo esc_attr( $btn_primary_text ); ?>">
								</label>
								<label>
									<?php esc_html_e( 'Hover Background', 'shortcodes-generator' ); ?>&nbsp;
									<input type="color" name="pdsc_button_primary_hover_bg" value="<?php echo esc_attr( $btn_primary_hover ); ?>">
								</label>
								<p class="description"><?php esc_html_e( 'The border colour matches the background automatically.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Ghost Button', 'shortcodes-generator' ); ?></th>
							<td>
								<label style="margin-right:20px;">
									<?php esc_html_e( 'Colour (text &amp; border)', 'shortcodes-generator' ); ?>&nbsp;
									<input type="color" name="pdsc_button_ghost_color" value="<?php echo esc_attr( $btn_ghost_color ); ?>">
								</label>
								<label>
									<?php esc_html_e( 'Hover Colour', 'shortcodes-generator' ); ?>&nbsp;
									<input type="color" name="pdsc_button_ghost_hover_color" value="<?php echo esc_attr( $btn_ghost_hover ); ?>">
								</label>
							</td>
						</tr>
					</table>

				</div><!-- /buttons -->

				<!-- ========= Toggles & Accordions Tab ========= -->
				<div id="pdsc-tab-toggles" class="pdsc-tab-content" style="display:none;">

					<h2><?php esc_html_e( 'Toggle Defaults', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="pdsc_toggle_default_state"><?php esc_html_e( 'Default State', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_toggle_default_state" name="pdsc_toggle_default_state">
									<option value="open"   <?php selected( $toggle_state, 'open' ); ?>><?php esc_html_e( 'Open', 'shortcodes-generator' ); ?></option>
									<option value="closed" <?php selected( $toggle_state, 'closed' ); ?>><?php esc_html_e( 'Closed', 'shortcodes-generator' ); ?></option>
								</select>
								<p class="description"><?php esc_html_e( 'Default open/closed state when a toggle is inserted without specifying a state.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
					</table>

					<h2><?php esc_html_e( 'Accordion Defaults', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="pdsc_accordion_default_open"><?php esc_html_e( 'Default Open Section', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_accordion_default_open" name="pdsc_accordion_default_open">
									<option value="0"  <?php selected( $accordion_open, '0' ); ?>><?php esc_html_e( 'First Section', 'shortcodes-generator' ); ?></option>
									<option value="-1" <?php selected( $accordion_open, '-1' ); ?>><?php esc_html_e( 'None (all collapsed)', 'shortcodes-generator' ); ?></option>
								</select>
							</td>
						</tr>
					</table>

				</div><!-- /toggles -->

				<!-- ========= Google Maps Tab ========= -->
				<div id="pdsc-tab-maps" class="pdsc-tab-content" style="display:none;">

					<h2><?php esc_html_e( 'Google Maps Defaults', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="pdsc_maps_api_key"><?php esc_html_e( 'Google Maps API Key', 'shortcodes-generator' ); ?></label></th>
							<td>
								<input type="text" id="pdsc_maps_api_key" name="pdsc_maps_api_key"
									value="<?php echo esc_attr( $maps_api_key ); ?>" class="regular-text">
								<p class="description">
									<?php esc_html_e( 'Optional. Maps work out of the box without a key. Adding a key switches to the Google Maps Embed API v1, which is recommended for production sites.', 'shortcodes-generator' ); ?>
								</p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pdsc_maps_default_height"><?php esc_html_e( 'Default Height (px)', 'shortcodes-generator' ); ?></label></th>
							<td>
								<input type="number" id="pdsc_maps_default_height" name="pdsc_maps_default_height"
									value="<?php echo esc_attr( $maps_height ?: '350' ); ?>"
									min="100" max="2000" step="10" class="small-text">
								<p class="description"><?php esc_html_e( 'Default map height in pixels. Can be overridden per-shortcode with height="400".', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pdsc_maps_default_zoom"><?php esc_html_e( 'Default Zoom Level', 'shortcodes-generator' ); ?></label></th>
							<td>
								<input type="number" id="pdsc_maps_default_zoom" name="pdsc_maps_default_zoom"
									value="<?php echo esc_attr( $maps_zoom ?: '14' ); ?>"
									min="1" max="21" step="1" class="small-text">
								<p class="description"><?php esc_html_e( '1 = world view, 14 = street level (default), 21 = building level. Can be overridden per-shortcode.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
					</table>

				</div><!-- /maps -->

				<!-- ========= Count Up Tab ========= -->
				<div id="pdsc-tab-countup" class="pdsc-tab-content" style="display:none;">

					<h2><?php esc_html_e( 'Count Up Defaults', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="pdsc_countup_duration"><?php esc_html_e( 'Animation Duration (ms)', 'shortcodes-generator' ); ?></label></th>
							<td>
								<input type="number" id="pdsc_countup_duration" name="pdsc_countup_duration"
									value="<?php echo esc_attr( $countup_duration ?: '2000' ); ?>"
									min="500" max="10000" step="100" class="small-text">
								<p class="description"><?php esc_html_e( 'How long the count-up animation runs in milliseconds. Default: 2000 (2 seconds).', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pdsc_countup_separator"><?php esc_html_e( 'Thousands Separator', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_countup_separator" name="pdsc_countup_separator">
									<option value=""  <?php selected( $countup_separator, '' ); ?>><?php esc_html_e( 'None', 'shortcodes-generator' ); ?></option>
									<option value="," <?php selected( $countup_separator, ',' ); ?>><?php esc_html_e( 'Comma (1,000)', 'shortcodes-generator' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pdsc_countup_prefix"><?php esc_html_e( 'Prefix', 'shortcodes-generator' ); ?></label></th>
							<td>
								<input type="text" id="pdsc_countup_prefix" name="pdsc_countup_prefix"
									value="<?php echo esc_attr( $countup_prefix ); ?>" class="small-text" placeholder="e.g. $">
								<p class="description"><?php esc_html_e( 'Static text shown before the number on all count-up shortcodes.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pdsc_countup_suffix"><?php esc_html_e( 'Suffix', 'shortcodes-generator' ); ?></label></th>
							<td>
								<input type="text" id="pdsc_countup_suffix" name="pdsc_countup_suffix"
									value="<?php echo esc_attr( $countup_suffix ); ?>" class="small-text" placeholder="e.g. %">
								<p class="description"><?php esc_html_e( 'Static text shown after the number on all count-up shortcodes.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
					</table>

				</div><!-- /countup -->

				<!-- ========= Video & Animations Tab ========= -->
				<div id="pdsc-tab-video" class="pdsc-tab-content" style="display:none;">

					<h2><?php esc_html_e( 'Video Embed Defaults', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="pdsc_video_default_source"><?php esc_html_e( 'Default Source', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_video_default_source" name="pdsc_video_default_source">
									<option value="youtube" <?php selected( $video_source, 'youtube' ); ?>><?php esc_html_e( 'YouTube', 'shortcodes-generator' ); ?></option>
									<option value="vimeo"   <?php selected( $video_source, 'vimeo' ); ?>><?php esc_html_e( 'Vimeo', 'shortcodes-generator' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Autoplay', 'shortcodes-generator' ); ?></th>
							<td>
								<label>
									<input type="hidden"   name="pdsc_video_autoplay" value="0">
									<input type="checkbox" name="pdsc_video_autoplay" value="1" <?php checked( $video_autoplay, '1' ); ?>>
									<?php esc_html_e( 'Enable autoplay on video embeds', 'shortcodes-generator' ); ?>
								</label>
								<p class="description"><?php esc_html_e( 'Note: browsers may block autoplay for videos that are not muted.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Show Related Videos', 'shortcodes-generator' ); ?></th>
							<td>
								<label>
									<input type="hidden"   name="pdsc_video_related" value="0">
									<input type="checkbox" name="pdsc_video_related" value="1" <?php checked( $video_related, '1' ); ?>>
									<?php esc_html_e( 'Show related videos at the end (YouTube only)', 'shortcodes-generator' ); ?>
								</label>
							</td>
						</tr>
					</table>

					<h2><?php esc_html_e( 'Animation Defaults', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="pdsc_animation_default_speed"><?php esc_html_e( 'Default Speed', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_animation_default_speed" name="pdsc_animation_default_speed">
									<option value=""              <?php selected( $anim_speed, '' ); ?>><?php esc_html_e( 'Normal (1s)', 'shortcodes-generator' ); ?></option>
									<option value="animate__slow" <?php selected( $anim_speed, 'animate__slow' ); ?>><?php esc_html_e( 'Slow (3s)', 'shortcodes-generator' ); ?></option>
									<option value="animate__fast" <?php selected( $anim_speed, 'animate__fast' ); ?>><?php esc_html_e( 'Fast (0.8s)', 'shortcodes-generator' ); ?></option>
								</select>
								<p class="description"><?php esc_html_e( 'Applied globally to all animation shortcodes.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
					</table>

				</div><!-- /video -->

				<!-- ========= Social & Alerts Tab ========= -->
				<div id="pdsc-tab-social" class="pdsc-tab-content" style="display:none;">

					<h2><?php esc_html_e( 'Social Icon Defaults', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="pdsc_social_icon_style"><?php esc_html_e( 'Icon Style', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_social_icon_style" name="pdsc_social_icon_style">
									<option value="plain"  <?php selected( $social_style, 'plain' ); ?>><?php esc_html_e( 'Plain', 'shortcodes-generator' ); ?></option>
									<option value="circle" <?php selected( $social_style, 'circle' ); ?>><?php esc_html_e( 'Circle', 'shortcodes-generator' ); ?></option>
									<option value="square" <?php selected( $social_style, 'square' ); ?>><?php esc_html_e( 'Square', 'shortcodes-generator' ); ?></option>
								</select>
								<p class="description"><?php esc_html_e( 'Circle and Square add a dark background. Override the colour with Custom CSS.', 'shortcodes-generator' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="pdsc_social_icon_size"><?php esc_html_e( 'Icon Size', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_social_icon_size" name="pdsc_social_icon_size">
									<option value="small"  <?php selected( $social_size, 'small' ); ?>><?php esc_html_e( 'Small', 'shortcodes-generator' ); ?></option>
									<option value="medium" <?php selected( $social_size, 'medium' ); ?>><?php esc_html_e( 'Medium (default)', 'shortcodes-generator' ); ?></option>
									<option value="large"  <?php selected( $social_size, 'large' ); ?>><?php esc_html_e( 'Large', 'shortcodes-generator' ); ?></option>
								</select>
							</td>
						</tr>
					</table>

					<h2><?php esc_html_e( 'Alert Box Defaults', 'shortcodes-generator' ); ?></h2>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="pdsc_alert_default_type"><?php esc_html_e( 'Default Alert Type', 'shortcodes-generator' ); ?></label></th>
							<td>
								<select id="pdsc_alert_default_type" name="pdsc_alert_default_type">
									<option value="info"     <?php selected( $alert_type, 'info' ); ?>><?php esc_html_e( 'Info', 'shortcodes-generator' ); ?></option>
									<option value="note"     <?php selected( $alert_type, 'note' ); ?>><?php esc_html_e( 'Note', 'shortcodes-generator' ); ?></option>
									<option value="download" <?php selected( $alert_type, 'download' ); ?>><?php esc_html_e( 'Download', 'shortcodes-generator' ); ?></option>
									<option value="warning"  <?php selected( $alert_type, 'warning' ); ?>><?php esc_html_e( 'Warning', 'shortcodes-generator' ); ?></option>
								</select>
							</td>
						</tr>
					</table>

				</div><!-- /social -->

				<?php submit_button(); ?>
			</form>
		</div>

		<script>
		(function($) {
			var $tabs     = $('#pdsc-tabs .nav-tab');
			var $contents = $('.pdsc-tab-content');

			$tabs.on('click', function(e) {
				e.preventDefault();
				var tab = $(this).data('tab');
				$tabs.removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active');
				$contents.hide();
				$('#pdsc-tab-' + tab).show();
				try { localStorage.setItem('pdsc_active_tab', tab); } catch(err) {}
			});

			// Restore the last active tab after a page load / form save
			try {
				var saved = localStorage.getItem('pdsc_active_tab');
				if (saved && $('[data-tab="' + saved + '"]').length) {
					$('[data-tab="' + saved + '"]').trigger('click');
				}
			} catch(err) {}
		})(jQuery);
		</script>
		<?php
	}


	/**
	 * Add external TinyMCE plugin file path
	 */
	public function register_tinymce_plugin( $plugins ) {
		$plugins['pdsc_shortcodes'] = PDSC_PLUGIN_URL . 'assets/js/tinymce-shortcodes.js';
		return $plugins;
	}

	/**
	 * Add button to TinyMCE toolbar
	 */
	public function register_tinymce_button( $buttons ) {
		array_push( $buttons, 'pdsc_shortcodes' );
		return $buttons;
	}

	/**
	 * Load the tinymce plugin script on every admin page.
	 */
	public function enqueue_admin_assets() {
		wp_enqueue_script(
			'pdsc-tinymce-plugin',
			PDSC_PLUGIN_URL . 'assets/js/tinymce-shortcodes.js',
			array(),
			'1.0',
			false
		);
	}

	/**
	 * Add a "Settings" link on the Plugins page listing.
	 *
	 * @param array $links Existing action links.
	 * @return array Modified links including settings.
	 */
	public function add_settings_link( $links ) {
		$settings_url  = admin_url( 'options-general.php?page=pdsc_settings' );
		$settings_link = '<a href="' . esc_url( $settings_url ) . '">' . esc_html__( 'Settings', 'shortcodes-generator' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

}
new Shortcodes();
