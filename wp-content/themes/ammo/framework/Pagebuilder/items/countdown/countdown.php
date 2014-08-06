<?php
	
function blox_parse_countdown_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'style' => 'style1',
        'date' => 'date',
        'bgcolor' => '#ededed',
        'fgcolor' => '#d4d4d4',
        'textcolor' => '#000000',
        'circle_width' => '170',
        'animation' => '',
        'extra_class' => ''
	), $atts ) );

	$animate_class = get_blox_animate_class($animation);

	$params = "data-bgColor='$bgcolor' data-fgColor='$fgcolor' data-inputColor='$textcolor'";

	return "<div class='blox_element blox_countdown $extra_class' data-date='$date'>
				<div class='blox_counter_wrapper'>
					<div class='cditem $animate_class'>
						<input type='text' data-width='$circle_width' data-min='0' data-max='99' $params class='countdown_dial countdown_day' />
						<div class='labels'>Days</div>
					</div>
					<div class='cditem $animate_class'>
						<input type='text' data-width='$circle_width' data-min='0' data-max='23' $params class='countdown_dial countdown_hour' />
						<div class='labels'>Hours</div>
					</div>
					<div class='cditem $animate_class'>
						<input type='text' data-width='$circle_width' data-min='0' data-max='59' $params class='countdown_dial countdown_minute' />
						<div class='labels'>Minutes</div>
					</div>
					<div class='cditem $animate_class'>
						<input type='text' data-width='$circle_width' data-min='0' data-max='59' $params class='countdown_dial countdown_second' />
						<div class='labels'>Seconds</div>
					</div>
				</div>
				<div class='clearfix'></div>
			</div>";
}
add_shortcode( 'blox_countdown', 'blox_parse_countdown_hook' );

?>
