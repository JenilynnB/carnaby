<?php

function blox_parse_heading_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'size' => 'h3',
		'icon' => '',
		'style' => '',
		'text_align' => 'left',
		'animation' => '',
		'extra_class' => ''
	), $atts ) );

	$is_animate = $animation!='none' && $animation!='' ? 'animate' : '';

	$extra_class .= ' text-'.$text_align;
	$icon = !empty($icon) ? '<span class="heading-icon"><i class="'.$icon.'"></i></span>' : '';
	$content = !empty($content) ? '<p class="lead">'.do_shortcode($content).'</p>' : '';

	
	switch($style){
		case 'style1':
			return '<div class="blox-element blox-heading '.$style . ' '. $is_animate.' '. $extra_class .'" data-animate="'.$animation.'">
						<'.$size.' class="heading-title">'.$title.'</'.$size.'>
						'.$content.'
					</div>';
			break;
		case 'style2':
			return '<div class="blox-element blox-heading '.$style . ' '. $is_animate.' '. $extra_class .'" data-animate="'.$animation.'">
						<'.$size.' class="heading-title">'.$title.'</'.$size.'>
						'.$icon.'
						'.$content.'
					</div>';
			break;
		case 'style3':
			return '<div class="blox-element blox-heading '.$style . ' '. $is_animate.' '. $extra_class .'" data-animate="'.$animation.'">
						<'.$size.' class="heading-title">'.$icon.$title.'</'.$size.'>
						'.$content.'
						<span class="heading-line"></span>
					</div>';
			break;
		case 'style4':
			return '<div class="blox-element blox-heading '.$style . ' '. $is_animate.' '. $extra_class .'" data-animate="'.$animation.'">
						<'.$size.'>'.$icon.$title.'</'.$size.'>
						'.$content.'
					</div>';
			break;
		case 'style5':
			return '<div class="blox-element blox-heading '.$style . ' '. $is_animate.' '. $extra_class .'" data-animate="'.$animation.'">
						<'.$size.' class="heading-title">'.$title.'</'.$size.'>
						<span class="heading-line"></span>
						'.$content.'
					</div>';
			break;
		default:
			return '<div class="blox-element  blox-heading '.$style . ' '. $is_animate.' '. $extra_class .'" data-animate="'.$animation.'">
						<'.$size.' class="heading-title">'.$title.'</'.$size.'>
						'.$content.'
					</div>';
			break;
	}

	return '';
}
add_shortcode( 'blox_heading', 'blox_parse_heading_hook' );

?>