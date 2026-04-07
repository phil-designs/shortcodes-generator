<?php
/**
SHORTCODE FUNCTIONS
 */

// Shared helper: returns true if a shortcode key is in the disabled list.
function pdsc_is_disabled( $key ) {
	return in_array( $key, (array) get_option( 'pdsc_disabled_shortcodes', array() ), true );
}

/*	Column Shortcodes --- */

if ( ! function_exists('column') ) {
	function column( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'column' => 'one-third',
			'last'   => false
		), $atts));

		$last_class = '';
		$last_div   = '';
		if ( $last ) {
			$last_class = ' column-last';
			$last_div   = '<div class="clear"></div>';
		}

		return '<div class="flex-col cell ' . esc_attr( $column ) . $last_class . '">' . do_shortcode( $content ) . '</div>' . $last_div;
	}
}

function aa_columns_shortcode( $atts, $content ) {
	return "<div class='grid grid-12-col'>" . do_shortcode( $content ) . "</div>";
}

if ( ! pdsc_is_disabled( 'columns' ) ) {
	add_shortcode( 'column', 'column' );
	add_shortcode( 'columns', 'aa_columns_shortcode' );
}

/* Buttons --- */

if ( ! function_exists('button') ) {
	function button( $atts, $content = null ) {
		$default_style  = get_option( 'pdsc_button_default_style', 'primary-button' );
		$default_target = get_option( 'pdsc_button_default_target', '_self' );

		extract( shortcode_atts( array(
			'url'    => '#',
			'target' => $default_target,
			'style'  => $default_style,
		), $atts ) );

		return '<a href="' . esc_url( $url ) . '" target="' . esc_attr( $target ) . '" class="' . esc_attr( $style ) . '">' . do_shortcode( $content ) . '</a>';
	}
}

if ( ! pdsc_is_disabled( 'button' ) ) {
	add_shortcode( 'button', 'button' );
}

/* Animation --- */

if ( ! function_exists('animations') ) {
	function animations( $atts, $content = null ) {
		$default_speed = get_option( 'pdsc_animation_default_speed', '' );

		extract( shortcode_atts( array(
			'style' => '',
			'delay' => '',
		), $atts ) );

		$classes = trim( implode( ' ', array_filter( array( 'wow', $style, $delay, $default_speed ) ) ) );

		return '<span style="display:inline-block;" class="' . esc_attr( $classes ) . '">' . do_shortcode( $content ) . '</span>';
	}
}

if ( ! pdsc_is_disabled( 'animate' ) ) {
	add_shortcode( 'animate', 'animations' );
}

/* Alerts --- */

if ( ! function_exists('alert') ) {
	function alert( $atts, $content = null ) {
		$default_type = get_option( 'pdsc_alert_default_type', 'info' );

		extract( shortcode_atts( array(
			'style' => $default_type,
			'close' => 'no',
		), $atts ) );

		$close_btn = ( $close === 'yes' ) ? '<div class="alert-close"></div>' : '';

		return '<div class="alertBox ' . esc_attr( $style ) . '-box">' . do_shortcode( $content ) . $close_btn . '</div>';
	}
}

if ( ! pdsc_is_disabled( 'alert' ) ) {
	add_shortcode( 'alert', 'alert' );
}

/* Toggle Shortcodes --- */

if ( ! function_exists('toggle') ) {
	function toggle( $atts, $content = null ) {
		$default_state = get_option( 'pdsc_toggle_default_state', 'open' );

		extract( shortcode_atts( array(
			'title' => 'Title goes here',
			'state' => $default_state,
		), $atts ) );

		return '<div data-id="' . esc_attr( $state ) . '" class="toggle">'
			. '<span class="toggle-title">' . esc_html( $title ) . '</span>'
			. '<div class="toggle-inner">' . do_shortcode( $content ) . '</div>'
			. '</div>';
	}
}

if ( ! pdsc_is_disabled( 'toggle' ) ) {
	add_shortcode( 'toggle', 'toggle' );
}

/* Accordion Shortcodes --- */

if ( ! function_exists('accordion_function') ) {
	function accordion_function( $atts, $content ) {
		$default_open = get_option( 'pdsc_accordion_default_open', '0' );

		extract( shortcode_atts( array(
			'el_class' => '',
			'type'     => 'accordion',
			'opened'   => $default_open,
		), $atts ) );

		$extra_class = ( $el_class !== '' ) ? ' ' . esc_attr( $el_class ) : '';

		$html  = '<div data-opened="' . esc_attr( $opened ) . '" class="accordion ' . esc_attr( $type ) . $extra_class . ' clearfix">';
		$html .= do_shortcode( $content );
		$html .= '</div>';
		return $html;
	}
}

if ( ! function_exists('accordion_section_function') ) {
	function accordion_section_function( $atts, $content ) {
		extract( shortcode_atts( array(
			'title' => 'Section',
		), $atts ) );

		return '<section>
	<div class="accordion-title">' . esc_html( $title ) . '</div>
	<div class="accordion-content">' . do_shortcode( $content ) . '</div>
</section>';
	}
}

if ( ! pdsc_is_disabled( 'accordion' ) ) {
	add_shortcode( 'accordion', 'accordion_function' );
	add_shortcode( 'accordion_section', 'accordion_section_function' );
}

/* Tabs Shortcodes --- */

if ( ! function_exists('tabs') ) {
	function tabs( $atts, $content = null ) {
		extract( shortcode_atts( array(), $atts ) );

		STATIC $i = 0;
		$i++;

		preg_match_all( '/tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );

		$tab_titles = isset( $matches[1] ) ? $matches[1] : array();
		$output     = '';

		if ( count( $tab_titles ) ) {
			$output .= '<div id="tabs-' . $i . '" class="tabs"><div class="tab-inner">';
			$output .= '<ul class="nav clearfix">';
			foreach ( $tab_titles as $tab ) {
				$output .= '<li><a href="#tab-' . sanitize_title( $tab[0] ) . '">' . esc_html( $tab[0] ) . '</a></li>';
			}
			$output .= '</ul>';
			$output .= do_shortcode( $content );
			$output .= '</div></div>';
		} else {
			$output .= do_shortcode( $content );
		}

		return $output;
	}
}

if ( ! function_exists('tab') ) {
	function tab( $atts, $content = null ) {
		extract( shortcode_atts( array( 'title' => 'Tab' ), $atts ) );
		return '<div id="tab-' . sanitize_title( $title ) . '" class="tab">' . do_shortcode( $content ) . '</div>';
	}
}

if ( ! pdsc_is_disabled( 'tabs' ) ) {
	add_shortcode( 'tabs', 'tabs' );
	add_shortcode( 'tab', 'tab' );
}

/* ------------------------
-----   Google Map    -----
------------------------------*/
/* Forked from "Very Simple Google Maps" Plugin */
function vsg_maps_shortcode( $atts, $content = null ) {
	$api_key        = get_option( 'pdsc_maps_api_key', '' );
	$default_height = absint( get_option( 'pdsc_maps_default_height', 350 ) );
	$default_zoom   = absint( get_option( 'pdsc_maps_default_zoom', 14 ) );

	// Fall back to built-in defaults if saved values are out of range
	if ( $default_height < 100 )             { $default_height = 350; }
	if ( $default_zoom < 1 || $default_zoom > 21 ) { $default_zoom = 14; }

	extract( shortcode_atts( array(
		'align'       => 'left',
		'width'       => '100%',
		'height'      => $default_height,
		'address'     => '',
		'info_window' => 'A',
		'zoom'        => $default_zoom,
		'companycode' => '',
	), $atts ) );

	if ( ! empty( $api_key ) ) {
		// Maps Embed API v1 — requires an API key
		$src = 'https://www.google.com/maps/embed/v1/place?' . http_build_query( array(
			'key'  => $api_key,
			'q'    => $address,
			'zoom' => intval( $zoom ),
		) );
	} else {
		// Basic embed — works without an API key (default / out-of-the-box behaviour)
		$query_string = 'q=' . urlencode( $address )
			. '&cid=' . urlencode( $companycode )
			. '&center=' . urlencode( $address );
		$src = 'https://maps.google.com/maps?&' . $query_string
			. '&output=embed&z=' . intval( $zoom )
			. '&iwloc=' . urlencode( $info_window )
			. '&visual_refresh=true';
	}

	return '<div class="vsg-map"><iframe class="google-map"'
		. ' align="' . esc_attr( $align ) . '"'
		. ' width="' . esc_attr( $width ) . '"'
		. ' height="' . esc_attr( $height ) . '"'
		. ' frameborder="0" scrolling="no" marginheight="0" marginwidth="0"'
		. ' src="' . esc_url( $src ) . '"></iframe></div>';
}

if ( ! pdsc_is_disabled( 'vsgmap' ) ) {
	add_shortcode( 'vsgmap', 'vsg_maps_shortcode' );
}

/*---------------------------------
	Count Up Shortcode
------------------------------------*/
function aa_count_up_shortcode( $atts, $content ) {
	$prefix    = get_option( 'pdsc_countup_prefix', '' );
	$suffix    = get_option( 'pdsc_countup_suffix', '' );
	$separator = get_option( 'pdsc_countup_separator', '' );

	$number = trim( $content );

	// Apply thousands separator: number_format produces commas (e.g. 1,000)
	// which the jQuery CountUp plugin natively detects and preserves during animation.
	if ( $separator === ',' && is_numeric( str_replace( ',', '', $number ) ) ) {
		$number = number_format( intval( str_replace( ',', '', $number ) ) );
	}

	$html = '';
	if ( $prefix !== '' ) {
		$html .= '<span class="counter-prefix">' . esc_html( $prefix ) . '</span>';
	}
	$html .= '<span class="counter">' . esc_html( $number ) . '</span>';
	if ( $suffix !== '' ) {
		$html .= '<span class="counter-suffix">' . esc_html( $suffix ) . '</span>';
	}

	return '<span class="counter-wrap">' . $html . '</span>';
}

if ( ! pdsc_is_disabled( 'count_up' ) ) {
	add_shortcode( 'count_up', 'aa_count_up_shortcode' );
}

/*---------------------------------
	Social Icons Shortcode
------------------------------------*/
function aa_social_shortcode( $atts, $content ) {
	return '<ul class="social-icons fa-ul">' . do_shortcode( $content ) . '</ul>';
}

function aa_link_shortcode( $atts, $content ) {
	$url    = isset( $atts['url'] )    ? esc_url( $atts['url'] )         : '#';
	$target = isset( $atts['target'] ) ? esc_attr( $atts['target'] )     : '_self';
	$type   = isset( $atts['type'] )   ? esc_attr( $atts['type'] )       : '';

	return '<li class="fa-li"><a href="' . $url . '" target="' . $target . '"><i class="fa ' . $type . '"></i></a></li>';
}

if ( ! pdsc_is_disabled( 'social' ) ) {
	add_shortcode( 'social', 'aa_social_shortcode' );
	add_shortcode( 'link', 'aa_link_shortcode' );
}

/*---------------------------------
-----   Video Shortcode    -----
------------------------------------*/
function aa_video_shortcode( $atts, $content ) {
	$default_source = get_option( 'pdsc_video_default_source', 'youtube' );
	$global_autoplay = get_option( 'pdsc_video_autoplay', '0' ) === '1';
	$global_related  = get_option( 'pdsc_video_related', '1' ) === '1';

	extract( shortcode_atts( array(
		'source' => $default_source,
		'id'     => '',
	), $atts ) );

	$video_id = esc_attr( $id );

	if ( $source === 'vimeo' ) {
		$params = $global_autoplay ? '?autoplay=1' : '';
		$html = '<div class="video-container">'
			. '<iframe src="https://player.vimeo.com/video/' . $video_id . $params . '"'
			. ' width="960" height="540" frameborder="0"'
			. ' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>'
			. '</div>';
	} else {
		$yt_params = array(
			'rel'      => $global_related ? '1' : '0',
			'vq'       => 'hd1080',
			'autohide' => '1',
			'showinfo' => '0',
		);
		if ( $global_autoplay ) {
			$yt_params['autoplay'] = '1';
		}
		$query = implode( '&amp;', array_map(
			function( $k, $v ) { return $k . '=' . $v; },
			array_keys( $yt_params ),
			$yt_params
		) );
		$html = '<div class="video-container">'
			. '<iframe width="960" height="540"'
			. ' src="https://www.youtube.com/embed/' . $video_id . '?' . $query . '"'
			. ' frameborder="0" allowfullscreen></iframe>'
			. '</div>';
	}

	return $html;
}

if ( ! pdsc_is_disabled( 'embed-video' ) ) {
	add_shortcode( 'embed-video', 'aa_video_shortcode' );
}
