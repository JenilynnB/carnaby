<?php


/* Element Title
=================================*/
function element_title_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'tag' => 'h3'
	), $atts ) );

	return "<$tag class='element-title'>$content</$tag>";

}
add_shortcode( 'element_title', 'element_title_hook' );


/* Dropcap
=================================*/
function blox_dropcap_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title' => ''
	), $atts ) );

	return "<span class='dropcap'>$content</span>";

}
add_shortcode( 'blox_dropcap', 'blox_dropcap_hook' );




/* Highlight
=================================*/
function blox_highlight_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'type' => ''
	), $atts ) );

	return "<span class='label label-$type'>$content</span>";

}
add_shortcode( 'blox_highlight', 'blox_highlight_hook' );



/* Icon
=================================*/
function blox_icon_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'icon' => '',
		'color' => ''
	), $atts ) );

	$style = $color!='' ? "color:$color;" : '';
	return "<i class='$icon' style='$style'></i>";

}
add_shortcode( 'blox_icon', 'blox_icon_hook' );



/* Tooltip
=================================*/
function blox_tooltip_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'tooltip' => ''
	), $atts ) );

	return "<a href='javascript:;' data-toggle='tooltip' class='blox-tooltip' title='$tooltip'>$content</a>";

}
add_shortcode( 'blox_tooltip', 'blox_tooltip_hook' );



?>