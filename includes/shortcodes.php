<?php
/**
SHORTCODE FUNCTIONS
 */

/*	Column Shortcodes --- */

if ( !function_exists('column') ) {
	function column( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'column' => 'one-third',
			'last' => false
		), $atts));

		$last_class = '';
		$last_div = '';
		if( $last ) {
			$last_class = ' column-last';
			$last_div = '<div class="clear"></div>';
		}

		return '<div class="flex-col cell ' . $column . $last_class . '">' . do_shortcode($content) . '</div>' . $last_div;
	}
	add_shortcode('column', 'column');
}

function aa_columns_shortcode( $atts, $content) {
	return "<div class='grid grid-12-col'>" . do_shortcode( $content ) . "</div>";
}
add_shortcode('columns', 'aa_columns_shortcode');

/* Buttons --- */

if (!function_exists('button')) {
	function button( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'url' => '#',
			'target' => '_self',
			'style' => 'primary-button'
	    ), $atts));

	   return '<a href="'.$url.'" target="'.$target.'" class="'.$style.'">' . do_shortcode($content) . '</a>';
	}
	add_shortcode('button', 'button');
}

/* Animation --- */

if (!function_exists('animations')) {
	function animations( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'style' => '',
			'delay' => ''
	    ), $atts));

	   return '<span style="display:inline-block;" class="wow '.$style.' '.$delay.'">' . do_shortcode($content) . '</span>';
	}
	add_shortcode('animate', 'animations');
}

/* Alerts --- */

if (!function_exists('alert')) {
	function alert( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'style'   => 'info'
	    ), $atts));
		
	if ($atts['close'] !="yes") {;
	$html = '<div class="alertBox '.$style.'-box">' . do_shortcode($content) . '</div>';
	}
	else
	$html = '<div class="alertBox '.$style.'-box">' . do_shortcode($content) . '<div class="alert-close"></div></div>';
	
	//$html .='</div>';
	return $html;

	}
	add_shortcode('alert', 'alert');
}

/* Toggle Shortcodes --- */

if (!function_exists('toggle')) {
	function toggle( $atts, $content = null ) {
	    extract(shortcode_atts(array(
			'title'    	 => 'Title goes here',
			'state'		 => 'open'
	    ), $atts));

		return "<div data-id='".$state."' class=\"toggle\"><span class=\"toggle-title\">". $title ."</span><div class=\"toggle-inner\">". do_shortcode($content) ."</div></div>";
	}
	add_shortcode('toggle', 'toggle');
}

/* Accordion Shortcodes --- */
if ( ! function_exists( 'accordion_function' ) ) {
	function accordion_function( $atts, $content ){
	    extract( shortcode_atts( array(
	        'el_class'  => '',
	        'type'		=> 'accordion',
	        'opened' 	=> '0'
	    ), $atts ) );
	    $html = '<div data-opened="' . $opened . '" class="accordion ' . $type . ' ' . ( $el_class != '' ? ' ' . $el_class : '' ) . ' clearfix">';
	    $html .= do_shortcode( $content );
	    $html .= '</div>';
	    return $html;
	}
	add_shortcode( 'accordion', 'accordion_function' );
}

if ( ! function_exists( 'accordion_section_function' ) ) {
	function accordion_section_function( $atts, $content ){
	    extract( shortcode_atts( array(
	        'title' => 'Section',
	    ), $atts ) );
	    $html = '<section>
	    	<div class="accordion-title">' . $title . '</div>
	    	<div class="accordion-content">' . do_shortcode( $content ) . '</div>
	    </section>';
	    return $html;
	}	
	add_shortcode( 'accordion_section', 'accordion_section_function' );
}

/* Tabs Shortcodes --- */

if (!function_exists('tabs')) {
	function tabs( $atts, $content = null ) {
		$defaults = array();
		extract( shortcode_atts( $defaults, $atts ) );

		STATIC $i = 0;
		$i++;

		// Extract the tab titles for use in the tab widget.
		preg_match_all( '/tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );

		$tab_titles = array();
		if( isset($matches[1]) ){ $tab_titles = $matches[1]; }

		$output = '';

		if( count($tab_titles) ){
		    $output .= '<div id="tabs-'. $i .'" class="tabs"><div class="tab-inner">';
			$output .= '<ul class="nav clearfix">';

			foreach( $tab_titles as $tab ){
				$output .= '<li><a href="#tab-'. sanitize_title( $tab[0] ) .'">' . $tab[0] . '</a></li>';
			}

		    $output .= '</ul>';
		    $output .= do_shortcode( $content );
		    $output .= '</div></div>';
		} else {
			$output .= do_shortcode( $content );
		}

		return $output;
	}
	add_shortcode( 'tabs', 'tabs' );
}

if (!function_exists('tab')) {
	function tab( $atts, $content = null ) {
		$defaults = array( 'title' => 'Tab' );
		extract( shortcode_atts( $defaults, $atts ) );

		return '<div id="tab-'. sanitize_title( $title ) .'" class="tab">'. do_shortcode( $content ) .'</div>';
	}
	add_shortcode( 'tab', 'tab' );
}

/* ------------------------
-----   Google Map    -----
------------------------------*/
/*Forked from "Very Simple Google Maps" Plugin*/
/* This section enables adding an very simple embeded Google Map with only a simple shortcode */
    function vsg_maps_shortcode($atts, $content = null) {
    extract(shortcode_atts(array(
    "align" => 'left',
    "width" => '100%',
    "height" => '350',
    "address" => '',
	"info_window" => 'A',
	"zoom" => '14',
	"companycode" => ''
    ), $atts));
	$query_string = 'q=' . urlencode($address) . '&cid=' . urlencode($companycode) . '&center=' . urlencode($address);
    return '<div class="vsg-map"><iframe class="google-map" align="'.$align.'" width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?&'.htmlentities($query_string).'&output=embed&z='.$zoom.'&iwloc='.$info_window.'&visual_refresh=true"></iframe></div>';
    }
    add_shortcode("vsgmap", "vsg_maps_shortcode");

/*---------------------------------
	Social Icons Shortcode
------------------------------------*/
function aa_social_shortcode($atts, $content){
	$html = '<ul class="social-icons fa-ul">' . do_shortcode($content) . '</ul>';
	return $html;
}
function aa_link_shortcode($atts, $content){	
	$html = '<li class="fa-li"><a href="' . $atts['url'] . '" target="' . $atts['target'] . '"><i class="fa ' . $atts['type'] . '"></i>
</a></li>';
	return $html;
}	
add_shortcode('social', 'aa_social_shortcode');
add_shortcode('link', 'aa_link_shortcode');

/*---------------------------------
-----   Video Shortcode    -----
------------------------------------*/
function aa_video_shortcode($atts, $content){
	if ($atts['source'] =="vimeo") {;
	$html='<div class="video-container"><iframe src="http://player.vimeo.com/video/'. $atts['id'] .'" width="960" height="540" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>';
	} else if ($atts['source'] =="youtube") {;
	$html='<div class="video-container"><iframe width="960" height="540" src="http://www.youtube.com/embed/'. $atts['id'] .'?rel=0&vq=hd1080;3&amp;autohide=1&amp;&amp;showinfo=0" frameborder="0" allowfullscreen></iframe></div>';
	}
	return $html;
}
add_shortcode('embed-video', 'aa_video_shortcode');

?>