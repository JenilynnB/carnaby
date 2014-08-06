<?php


function blox_parse_contentbox_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'widget_title' => '',
		'title' => '',
		'color' => 'transparent',
		'style' => '',
		'icon' => 'fa-user',
		'animation' => '',
		'extra_class' => ''
	), $atts ) );

	$widget_title = $widget_title!='' ? "<h3 class='element_title'>$widget_title</h3>" : '';
	$animate_class = get_blox_animate_class($animation);
	
	$content_title = $title!='' ? "<h3><i class='$icon'></i> $title</h3>" : '';
	return "$widget_title
			<div class='blox_element blox_elem_content_box $animate_class $style ".($style=='blox_elem_content_box_colored' ? blox_light_dark($color) : '')." $extra_class' style='".($style=='blox_elem_content_box_colored' ? "background-color: $color; color: #FFF;" : '')."'>
				$content_title
				<div class='blox_elem_content_box_content'>
					".do_shortcode($content)."
				</div>
			</div>";
}
add_shortcode( 'blox_contentbox', 'blox_parse_contentbox_hook' );



?>