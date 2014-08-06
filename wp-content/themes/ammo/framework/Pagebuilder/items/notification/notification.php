<?php
	

function blox_notification_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'type' => 'default',
		'alignment' => 'left',
		'dismissable' => '0',
        'animation' => '',
        'extra_class' => '',
        'visibility' => ''
	), $atts ) );

	$title = $title!='' ? "<h3>$title</h3>" : '';
	$type = $type!='default' ? $type : '';
	$is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
	$close_button = $dismissable=='1' ? "<a class='close' data-dismiss='alert' href='#' aria-hidden='true'>&times;</a>" : '';
	$dismissable = $dismissable=='1' ? 'alert-dismissable-1' : '';
	$visibility = str_replace(',', ' ', $visibility);

	$content = fix_shortcode_paragraph($content);

	return "<div class='blox-element alert $type $dismissable text-$alignment $is_animate $extra_class $visibility' data-animate='$animation'>$close_button $title ". do_shortcode($content) ."</div>";

}
add_shortcode( 'blox_notification', 'blox_notification_hook' );


?>