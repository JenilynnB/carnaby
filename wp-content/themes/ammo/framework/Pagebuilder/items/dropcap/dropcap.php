<?php
	
function blox_parse_dropcap_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'text' => 'D',
		'style' => 'style1',
		'color' => '#000',//????
                'size' => '20',//????
                'class' => ''
	), $atts ) );
	return "<span class='blox_dropcap $style' style='color:$color;padding:$size;'>$text</span></a>";
}
add_shortcode( 'blox_dropcap', 'blox_parse_dropcap_hook' );

?>