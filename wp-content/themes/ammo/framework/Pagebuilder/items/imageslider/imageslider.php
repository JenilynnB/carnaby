<?php

add_action('wp_ajax_get_blox_element_imagesliderimg', 'get_blox_element_imagesliderimg_hook');
add_action('wp_ajax_nopriv_get_blox_element_imagesliderimg', 'get_blox_element_imagesliderimg_hook');
function get_blox_element_imagesliderimg_hook() {
    try {
		if( isset($_POST['images']) && $_POST['images']!='' ){
			$arr = explode(',', trim($_POST['images']));
			$counter = 0;
			$images = '';
			foreach ($arr as $value) {
				if( $value!='' ){
					$attach_url = wp_get_attachment_url($value);
					$attach_url = $attach_url!==false ? $attach_url : THEME_NOIMAGE;
					$images .= ($counter==0 ? '' : '^').$attach_url;
					$counter++;
				}
			}
			echo $images;
		}
		else{
			echo "-1";
		}
    }
    catch (Exception $e) {
    	echo "-1";
    }
    exit;
}



function blox_parse_imageslider_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'images' => '',
		'layout' => '1',
                'lightbox' => '1',
		'extra_class' => ''
	), $atts ) );
	
	$uid = uniqid();
	
	if( $images!='' ){
		$img_array = explode(',', $images);
		$html = '';
		foreach ($img_array as $img_id) {
			$attach_url = wp_get_attachment_url($img_id);
			$img = blox_aq_resize($attach_url, 80, 80, true);
			
			$prev = '';
			if( $layout=='2' ){
				$prev = blox_aq_resize($attach_url, 800, 640, true);
				$prev = $prev!='' ? $prev : $attach_url;
			}
			else if( $layout=='4' ){
				$prev = blox_aq_resize($attach_url, 800, 480, true);
				$prev = $prev!='' ? $prev : $attach_url;
			}
			
			$html .= '<a href="'.$attach_url.'" data-preview="'.$prev.'" rel="blox_imageslider['.$uid.']"><img src="'.$img.'" /></a>';
		}
		$html = "<div class='blox_element blox_imageslider gallery_layout$layout $extra_class'><span class='gallery_preview'></span><span class='gallery_thumbs'>$html</span></div>";
		return $html;
	}
	
	return 'No slider images!';
}
add_shortcode( 'blox_imageslider', 'blox_parse_imageslider_hook' );


?>