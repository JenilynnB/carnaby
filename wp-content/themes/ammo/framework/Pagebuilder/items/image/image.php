<?php
	
function blox_parse_image_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'image' => '',
		'img_width' => '100%',
		'alignment' => 'left',
		'link' => '',
        'target' => '_blank',
        'animation' => '',
        'extra_class' => '',
        'visibility' => ''
	), $atts ) );
        
	$is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
	$visibility = str_replace(',', ' ', $visibility);
	
	$a_start = '';
	$a_end = '';
	if($target == 'lightbox'){
		$target == '_blank';
		$extra_class .= ' lightbox';
		$a_start = "<a href='$image' target='$target'>";
		$a_end = "</a>";
	}
	if( $link ){
		$a_start = "<a href='$link' target='$target'>";
		$a_end = "</a>";
	}

	$img_width = $img_width!='100%' ? $img_width .= 'px' : $img_width;
	$img_width = $img_width=='0px' ? '100%' : $img_width;

	return "<div class='blox-element image $is_animate $extra_class text-$alignment $visibility' data-animate='$animation'>
				$a_start
					<img src='$image' alt='Image' style='width:$img_width;' />
				$a_end
			</div>";
}
add_shortcode( 'blox_image', 'blox_parse_image_hook' );

?>