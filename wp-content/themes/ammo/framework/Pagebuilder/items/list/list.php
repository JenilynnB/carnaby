<?php
	
function blox_parse_list_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title' => '',
        'icon' => 'fa-user',
		'color' => '',
        'animation' => '',
        'extra_class' => '',
        'visibility' => ''
	), $atts ) );

	$result = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';
	$is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
	$visibility = str_replace(',', ' ', $visibility);

	$lis = str_replace('<p>', '', $content);
	$lis = str_replace('</p>', '', $lis);
	$lis = str_replace('<li>', '', $lis);
	$lis = str_replace('<ul>', '', $lis);
	$lis = str_replace('</ul>', '', $lis);

	$result .= "<div class='blox-element list-icons $is_animate $extra_class $visibility' data-animate='$animation'><ul>";

	$list = explode('</li>', $lis);
	if( count($list)-1 > 0 ){
		for($i=0; $i<count($list)-1; $i++){
			$result .= "<li><span class='$icon' style='color:$color;'></span> $list[$i]</li>";
		}
	}
	$result .= '</ul></div>';

	return $result;
}
add_shortcode( 'blox_list', 'blox_parse_list_hook' );

?>