<?php
	
function blox_parse_placeholder_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'icon' => 'fa-smile-o',
        'size' => '300',//height
        'animation' => '',
        'extra_class' => ''
	), $atts ) );

	$animate_class = get_blox_animate_class($animation);

	return "<div class='blox_element blox_elem_placeholder $animate_class $extra_class' style='height:$size"."px'><span class='$icon'></span></div>";
}
add_shortcode( 'blox_placeholder', 'blox_parse_placeholder_hook' );

?>