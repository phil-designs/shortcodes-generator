<?php
/** Shortcode Config */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ----------------------
----- Button Config -----
-------------------------*/
$pdsc_shortcodes['button'] = array(
	'title' => __( 'Button', 'shortcodes-generator' ),
	'id' => 'button-shortcode',
	'template' => '[button {{attributes}}] {{content}} [/button]',
	'params' => array(
		'url' => array(
			'std' => 'http://example.com',
			'type' => 'text',
			'label' => __( 'Link', 'shortcodes-generator' ),
			'desc' => __( "Add the button's url eg http://example.com", 'shortcodes-generator' ),
		),
		'style' => array(
			'type' => 'select',
			'label' => __( 'Style', 'shortcodes-generator' ),
			'desc' => __( "Select the button's style, ie the button's colour", 'shortcodes-generator' ),
			'options' => array(
				'primary-button' => 'Button',
				'ghost-button' => 'Ghost Button',
			),
		),
		'target' => array(
			'type' => 'select',
			'label' => __( 'Target', 'shortcodes-generator' ),
			'desc' => __( 'Set the browser behavior for the click action.', 'shortcodes-generator' ),
			'options' => array(
				'_self' => 'Same window',
				'_blank' => 'New window',
			),
		),
		'content' => array(
			'std' => 'Button Label',
			'type' => 'text',
			'label' => __( 'Label', 'shortcodes-generator' ),
			'desc' => __( "Add the button's text", 'shortcodes-generator' ),
		),
	),
);

/* ---------------------
----- Alert Config -----
------------------------*/
$pdsc_shortcodes['alert'] = array(
	'title' => __( 'Alert Box', 'shortcodes-generator' ),
	'id' => 'alert-shortcode',
	'template' => '[alert {{attributes}}] {{content}} [/alert]',
	'params' => array(
		'style' => array(
			'type' => 'select',
			'label' => __( 'Type', 'shortcodes-generator' ),
			'desc' => __( "Select the alert's type.", 'shortcodes-generator' ),
			'options' => array(
				'info' => 'Info',
				'note' => 'Note',
				'download' => 'Download',
				'warning' => 'Warning',
			),
		),
		'close' => array(
			'type' => 'select',
			'std' => '',
			'label' => __( 'Closable', 'shortcodes-generator' ),
			'desc' => __( 'Check this if you want this alert box to be closeable.', 'shortcodes-generator' ),
			'options' => array(
				'no' => 'No',
				'yes' => 'Yes',
			),
		),
		'content' => array(
			'std' => 'Your Alert!',
			'type' => 'textarea',
			'label' => __( 'Content', 'shortcodes-generator' ),
			'desc' => __( "Add the alert's text", 'shortcodes-generator' ),
		),
	),
);

/* ----------------------
----- Toggle Config -----
-------------------------*/
$pdsc_shortcodes['toggle'] = array(
	'title' => __( 'Toggle', 'shortcodes-generator' ),
	'id' => 'toggle-shortcode',
	'template' => ' {{child_shortcode}} ', // There is no wrapper shortcode
	'notes' => __( "Click 'Add Toggle' to add a new toggle. Drag and drop to reorder toggles.", 'shortcodes-generator' ),
	'params' => array(),
	'child_shortcode' => array(
		'params' => array(
			'title' => array(
				'type' => 'text',
				'label' => __( 'Toggle Content Title', 'shortcodes-generator' ),
				'desc' => __( 'Add the title that will go above the toggle content', 'shortcodes-generator' ),
				'std' => 'Title',
			),
			'content' => array(
				'std' => 'Content',
				'type' => 'textarea',
				'label' => __( 'Toggle Content', 'shortcodes-generator' ),
				'desc' => __( 'Add the toggle content. Will accept HTML', 'shortcodes-generator' ),
			),
			'state' => array(
				'type' => 'select',
				'label' => __( 'Toggle State', 'shortcodes-generator' ),
				'desc' => __( 'Select the state of the toggle on page load', 'shortcodes-generator' ),
				'options' => array(
					'open' => 'Open',
					'closed' => 'Closed',
				),
			),
		),
		'template' => '[toggle {{attributes}}] {{content}} [/toggle]',
		'clone_button' => __( 'Add Toggle', 'shortcodes-generator' ),
	),
);


/* ---------------------------
-----   Accordion Config -----
------------------------------*/
$pdsc_shortcodes['accordion'] = array(
	'title' => __( 'Accordion', 'shortcodes-generator' ),
	'id' => 'accordion-shortcode',
	'template' => '[accordion] {{child_shortcode}} [/accordion]', // There is no wrapper shortcode
	'notes' => __( "Click 'Add Accordion' to add a new accordion. Drag and drop to reorder accordions.", 'shortcodes-generator' ),
	'params' => array(),
	'child_shortcode' => array(
		'params' => array(
			'title' => array(
				'type' => 'text',
				'label' => __( 'Accordion Content Title', 'shortcodes-generator' ),
				'desc' => __( 'Add the title that will go above the accordion content', 'shortcodes-generator' ),
				'std' => 'Title',
			),
			'content' => array(
				'std' => 'Content',
				'type' => 'textarea',
				'label' => __( 'Accordion Content', 'shortcodes-generator' ),
				'desc' => __( 'Add the accordion content. Will accept HTML', 'shortcodes-generator' ),
			),
		),
		'template' => '[accordion_section {{attributes}}] {{content}} [/accordion_section]',
		'clone_button' => __( 'Add Accordion', 'shortcodes-generator' ),
	),
);

/* --------------------
----- Tabs Config -----
-----------------------*/
$pdsc_shortcodes['tabs'] = array(
	'title' => __( 'Tab', 'shortcodes-generator' ),
	'id' => 'tabs-shortcode',
	'template' => '[tabs] {{child_shortcode}} [/tabs]',
	'notes' => __( "Click 'Add Tab' to add a new tab. Drag and drop to reorder tabs.", 'shortcodes-generator' ),
	'params' => array(),
	'child_shortcode' => array(
		'params' => array(
			'title' => array(
				'std' => 'Title',
				'type' => 'text',
				'label' => __( 'Tab Title', 'shortcodes-generator' ),
				'desc' => __( 'Title of the tab.', 'shortcodes-generator' ),
			),
			'content' => array(
				'std' => 'Tab Content',
				'type' => 'textarea',
				'label' => __( 'Tab Content', 'shortcodes-generator' ),
				'desc' => __( 'Add the tabs content.', 'shortcodes-generator' ),
			),
		),
		'template' => '[tab {{attributes}}] {{content}} [/tab]',
		'clone_button' => __( 'Add Tab', 'shortcodes-generator' ),
	),
);

/* ----------------------
----- Column Config -----
-------------------------*/
$pdsc_shortcodes['columns'] = array(
	'title' => __( 'Columns', 'shortcodes-generator' ),
	'id' => 'columns-shortcode',
	'template' => '[columns] {{child_shortcode}} [/columns]', // There is no wrapper shortcode
	'notes' => __( "Click 'Add Column' to add a new column. Drag and drop to reorder columns.", 'shortcodes-generator' ),
	'params' => array(),
	'child_shortcode' => array(
		'params' => array(
			'column' => array(
				'type' => 'select',
				'label' => __( 'Column Type', 'shortcodes-generator' ),
				'desc' => __( 'Select the columns span here. Combined span of all columns should not exceed 12.', 'shortcodes-generator' ),
				'options' => array(
					'span-1'  => '1',
					'span-2'  => '2',
					'span-3'  => '3',
					'span-4'  => '4',
					'span-5'  => '5',
					'span-6'  => '6',
					'span-7'  => '7',
					'span-8'  => '8',
					'span-9'  => '9',
					'span-10' => '10',
					'span-11' => '11',
					'span-12' => '12',
				),
			),
			'content' => array(
				'std' => __( 'Column content...', 'shortcodes-generator' ),
				'type' => 'textarea',
				'label' => __( 'Column Content', 'shortcodes-generator' ),
				'desc' => __( 'Add the column content.', 'shortcodes-generator' ),
			),
		),
		'template' => '[column {{attributes}}] {{content}} [/column]',
		'clone_button' => __( 'Add Column', 'shortcodes-generator' ),
	),
);

/* ----------------------
----- Animation Config -----
-------------------------*/
$pdsc_shortcodes['animations'] = array(
	'title' => __( 'Animations', 'shortcodes-generator' ),
	'id' => 'animation-shortcode',
	'template' => '[animate {{attributes}}] {{content}} [/animate]',
	'params' => array(
		'style' => array(
			'type' => 'select',
			'label' => __( 'Animation Style', 'shortcodes-generator' ),
			'desc' => __( 'Select the animation style for this content.', 'shortcodes-generator' ),
			'options' => array(
				'_attention_seeker_'     => '---Attention Seekers---',
				'animate__bounce'        => 'Bounce',
				'animate__flash'         => 'Flash',
				'animate__pulse'         => 'Pulse',
				'animate__rubberBand'    => 'Rubber Band',
				'animate__shakeX'        => 'Shake Side-to-side',
				'animate__shakeY'        => 'Shake Up-and-down',
				'animate__headShake'     => 'Head Shake',
				'animate__swing'         => 'Swing',
				'animate__tada'          => 'Tada',
				'animate__wobble'        => 'Wobble',
				'animate__jello'         => 'Jello',
				'animate__heartBeat'     => 'Heart Beat',
				'_back_entrances_'       => '---Back Entrances---',
				'animate__backInDown'    => 'Back In Down',
				'animate__backInLeft'    => 'Back In Left',
				'animate__backInRight'   => 'Back In Right',
				'animate__backInUp'      => 'Back In Up',
				'_back_exits_'           => '---Back Exits---',
				'animate__backOutDown'   => 'Back Out Down',
				'animate__backOutLeft'   => 'Back Out Left',
				'animate__backOutRight'  => 'Back Out Right',
				'animate__backOutUp'     => 'Back Out Up',
				'_bouncing_entrances_'   => '---Bouncing Entrances---',
				'animate__bounceIn'      => 'Bounce In',
				'animate__bounceInDown'  => 'Bounce In Down',
				'animate__bounceInLeft'  => 'Bounce In Left',
				'animate__bounceInRight' => 'Bounce In Right',
				'animate__bounceInUp'    => 'Bounce In Up',
				'_bouncing_exits_'       => '---Bouncing Exits---',
				'animate__bounceOut'     => 'Bounce Out',
				'animate__bounceOutDown' => 'Bounce Out Down',
				'animate__bounceOutLeft' => 'Bounce Out Left',
				'animate__bounceOutRight' => 'Bounce Out Right',
				'animate__bounceOutUp'   => 'Bounce Out Up',
				'_fading_entrances_'     => '---Fading Entrances---',
				'animate__fadeIn'        => 'Fade In',
				'animate__fadeInDown'    => 'Fade In Down',
				'animate__fadeInDownBig' => 'Fade In Down Big',
				'animate__fadeInLeft'    => 'Fade In Left',
				'animate__fadeInLeftBig' => 'Fade In Left Big',
				'animate__fadeInRight'   => 'Fade In Right',
				'animate__fadeInRightBig' => 'Fade In Right Big',
				'animate__fadeInUp'      => 'Fade In Up',
				'animate__fadeInUpBig'   => 'Fade In Up Big',
				'animate__fadeInTopLeft' => 'Fade In Top Left',
				'animate__fadeInTopRight' => 'Fade In Top Right',
				'animate__fadeInBottomLeft' => 'Fade In Bottom Left',
				'animate__fadeInBottomRight' => 'Fade In Bottom Right',
				'_fading_exits_'         => '---Fading Exits---',
				'animate__fadeOut'       => 'Fade Out',
				'animate__fadeOutDown'   => 'Fade Out Down',
				'animate__fadeOutDownBig' => 'Fade Out Down Big',
				'animate__fadeOutLeft'   => 'Fade Out Left',
				'animate__fadeOutLeftBig' => 'Fade Out Left Big',
				'animate__fadeOutRight'  => 'Fade Out Right',
				'animate__fadeOutRightBig' => 'Fade Out Right Big',
				'animate__fadeOutUp'     => 'Fade Out Up',
				'animate__fadeOutUpBig'  => 'Fade Out Up Big',
				'animate__fadeOutTopLeft' => 'Fade Out Top Left',
				'animate__fadeOutTopRight' => 'Fade Out Top Right',
				'animate__fadeOutBottomRight' => 'Fade Out Bottom Right',
				'animate__fadeOutBottomLeft' => 'Fade Out Bottom Left',
				'_flippers_'             => '---Flippers---',
				'animate__flip'          => 'Flip',
				'animate__flipInX'       => 'Flip In Horizontal',
				'animate__flipInY'       => 'Flip In Vertical',
				'animate__flipOutX'      => 'Flip Out Horizontal',
				'animate__flipOutY'      => 'Flip Out Vertical',
				'_lightspeed_'           => '---Lightspeed---',
				'animate__lightSpeedInRight'  => 'Lightspeed In Right',
				'animate__lightSpeedInLeft'   => 'Lightspeed In Left',
				'animate__lightSpeedOutRight' => 'Lightspeed Out Right',
				'animate__lightSpeedOutLeft'  => 'Lightspeed Out Left',
				'_rotating_entrances_'        => '---Rotating Entrances---',
				'animate__rotateIn'           => 'Rotate In',
				'animate__rotateInDownLeft'   => 'Rotate In Down Left',
				'animate__rotateInDownRight'  => 'Rotate In Down Right',
				'animate__rotateInUpLeft'     => 'Rotate In Up Left',
				'animate__rotateInUpRight'    => 'Rotate In Up Right',
				'_rotating_exits_'            => '---Rotating Exits---',
				'animate__rotateOut'          => 'Rotate Out',
				'animate__rotateOutDownLeft'  => 'Rotate Out Down Left',
				'animate__rotateOutDownRight' => 'Rotate Out Down Right',
				'animate__rotateOutUpLeft'    => 'Rotate Out Up Left',
				'animate__rotateOutUpRight'   => 'Rotate Out Up Right',
				'_specials_'                  => '---Specials---',
				'animate__hinge'              => 'Hinge',
				'animate__jackInTheBox'       => 'Jack In The Box',
				'animate__rollIn'             => 'Roll In',
				'animate__rollOut'            => 'Roll Out',
				'_zooming_entrances_'         => '---Zooming Entrances---',
				'animate__zoomIn'             => 'Zoom In',
				'animate__zoomInDown'         => 'Zoom In Down',
				'animate__zoomInLeft'         => 'Zoom In Left',
				'animate__zoomInRight'        => 'Zoom In Right',
				'animate__zoomInUp'           => 'Zoom In Up',
				'_zooming_exits_'             => '---Zooming Exits---',
				'animate__zoomOut'            => 'Zoom Out',
				'animate__zoomOutDown'        => 'Zoom Out Down',
				'animate__zoomOutLeft'        => 'Zoom Out Left',
				'animate__zoomOutRight'       => 'Zoom Out Right',
				'animate__zoomOutUp'          => 'Zoom Out Up',
				'_sliding_entrances_'         => '---Sliding Entrances---',
				'animate__slideInDown'        => 'Slide In Down',
				'animate__slideInLeft'        => 'Slide In Left',
				'animate__slideInRight'       => 'Slide In Right',
				'animate__slideInUp'          => 'Slide In Up',
				'_sliding_exits_'             => '---Sliding Exits---',
				'animate__slideOutDown'       => 'Slide Out Down',
				'animate__slideOutLeft'       => 'Slide Out Left',
				'animate__slideOutRight'      => 'Slide Out Right',
				'animate__slideOutUp'         => 'Slide Out Up',
			),
		),
		'delay' => array(
			'type' => 'select',
			'label' => __( 'Animation Delay', 'shortcodes-generator' ),
			'desc' => __( 'Set the delay time for the animation when it appears on the screen.', 'shortcodes-generator' ),
			'options' => array(
				'animate__delay-2s' => '2 Seconds',
				'animate__delay-3s' => '3 Seconds',
				'animate__delay-4s' => '4 Seconds',
				'animate__delay-5s' => '5 Seconds',
			),
		),
		'content' => array(
			'type' => 'textarea',
			'label' => __( 'Content', 'shortcodes-generator' ),
			'desc' => __( 'Add the content you wish to animate here. You can restyle it after you exit the shortcode interface.', 'shortcodes-generator' ),
		),
	),
);

/* ------------------------------
-----  Google Map Config -------
---------------------------------*/
$pdsc_shortcodes['google-map'] = array(
	'title' => __( 'Google Map', 'shortcodes-generator' ),
	'id' => 'map-shortcode',
	'template' => '[vsgmap {{attributes}}]',
	'params' => array(
		'address' => array(
			'std' => '',
			'type' => 'text',
			'label' => __( 'Address', 'shortcodes-generator' ),
			'desc' => __( 'Add an address eg 123 Fifth Avenue, New York, NY 10003.', 'shortcodes-generator' ),
		),
	),
);

/* ------------------------------
-----  Count Up Config -------
---------------------------------*/
$pdsc_shortcodes['count_up'] = array(
	'title' => __( 'Count Up', 'shortcodes-generator' ),
	'id' => 'count_up-shortcode',
	'template' => '[count_up]{{content}}[/count_up]',
	'params' => array(
		'content' => array(
			'type' => 'text',
			'label' => __( 'Number', 'shortcodes-generator' ),
			'desc' => __( 'Add the number you wish to count up to here.', 'shortcodes-generator' ),
		),
	),
);

/* ----------------------
----- Video Config -----
-------------------------*/
$pdsc_shortcodes['video'] = array(
	'title' => __( 'Video Embed', 'shortcodes-generator' ),
	'id' => 'video-shortcode',
	'template' => '[embed-video {{attributes}} ]',
	'params' => array(
		'source' => array(
			'type' => 'select',
			'label' => __( 'Video Source', 'shortcodes-generator' ),
			'desc' => __( 'Select the video source, either YouTube or Vimeo.', 'shortcodes-generator' ),
			'options' => array(
				'vimeo'   => 'Vimeo',
				'youtube' => 'YouTube',
			),
		),
		'id' => array(
			'std' => '',
			'type' => 'text',
			'label' => __( 'Video ID', 'shortcodes-generator' ),
			'desc' => __( 'Enter the video ID.', 'shortcodes-generator' ),
		),
	),
);

/* ----------------------------
----- Social Icons Config -----
-------------------------------*/
$pdsc_shortcodes['social'] = array(
	'title' => __( 'Social Icons', 'shortcodes-generator' ),
	'id' => 'social-shortcode',
	'template' => '[social] {{child_shortcode}} [/social]', // There is no wrapper shortcode
	'notes' => __( "Click 'Add Icon' to add a new icon. Drag and drop to reorder icons.", 'shortcodes-generator' ),
	'params' => array(),
	'child_shortcode' => array(
		'params' => array(
			'type' => array(
				'type' => 'select',
				'label' => __( 'Icon type', 'shortcodes-generator' ),
				'desc' => __( "Choose the icon's type (social network).", 'shortcodes-generator' ),
				'options' => array(
					'fa-envelope-o'         => 'Email',
					'fa-brands fa-facebook-f' => 'Facebook',
					'fa-brands fa-youtube'  => 'YouTube',
					'fa-brands fa-vimeo-v'  => 'Vimeo',
					'fa-brands fa-instagram' => 'Instagram',
					'fa-brands fa-tiktok'   => 'TikTok',
					'fa-brands fa-linkedin-in' => 'LinkedIn',
					'fa-brands fa-pinterest-p' => 'Pinterest',
					'fa-brands fa-x-twitter' => 'Twitter',
					'add-fontawesome-classes' => 'Custom Icon',
				),
			),
			'target' => array(
				'type' => 'select',
				'label' => __( 'Icon Target', 'shortcodes-generator' ),
				'desc' => __( '_self = open in same window. _blank = open in new window.', 'shortcodes-generator' ),
				'options' => array(
					'_self'  => '_self',
					'_blank' => '_blank',
				),
			),
			'url' => array(
				'std' => 'https://www.example.com',
				'type' => 'text',
				'label' => __( 'Icon Url', 'shortcodes-generator' ),
				'desc' => __( 'Add the url to your social profile related with the chosen icon.', 'shortcodes-generator' ),
			),
		),
		'template' => '[link {{attributes}} ][/link]',
		'clone_button' => __( 'Add Icon', 'shortcodes-generator' ),
	),
);
