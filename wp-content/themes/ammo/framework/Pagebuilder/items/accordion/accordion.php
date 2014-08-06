<?php


function blox_parse_accordion_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'animation' => '',
		'border' => '',
        'extra_class' => '',
        'visibility' => ''
	), $atts ) );

	$title = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';
	$animate_class = get_blox_animate_class($animation);
	$visibility = str_replace(',', ' ', $visibility);

	$acc_id = uniqid();

	return '<div class="blox-element blox-accordion '. $visibility .' '.$extra_class.'">
				'. $title .'
				<div id="accordion_'. $acc_id .'" class="accordion panel-group">'. do_shortcode($content) .'</div>
			</div>';
}
add_shortcode( 'blox_accordion', 'blox_parse_accordion_hook' );


function blox_parse_accordion_item_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title' => 'Accordion Section',
		'icon' => '',
		'collapse' => ''
	), $atts ) );

	$acc_id = uniqid();
	$icon = $icon!='' ? "<i class='$icon'></i> " : '';
	$collapse = $collapse=='1' ? 'in' : '';

	return '<div class="acc-panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" href="#acc-'. $acc_id .'">'. $icon . do_shortcode($title) .'</a>
					</h4>
				</div>
				<div id="acc-'. $acc_id .'" class="panel-collapse collapse '. $collapse .'">
					<div class="panel-body">'. do_shortcode($content) .'</div>
				</div>
			</div>';

}
add_shortcode( 'blox_accordion_item', 'blox_parse_accordion_item_hook' );



?>