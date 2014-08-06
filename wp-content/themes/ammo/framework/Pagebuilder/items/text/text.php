<?php

function blox_parse_text_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'visibility' => '',
		'animation' => '',
		'extra_class' => ''
	), $atts ) );

	$title = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';

	$is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
	$animation = "data-animate='$animation'";
	$visibility = str_replace(',', ' ', $visibility);

	$content = fix_shortcode_paragraph($content);

	return "<div class='blox-element blox-element-text $is_animate $extra_class $visibility' $animation>".$title.do_shortcode($content)."</div>";
}
add_shortcode( 'blox_text', 'blox_parse_text_hook' );



?>