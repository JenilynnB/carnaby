<?php

add_action('wp_ajax_get_blox_element_galleryimg', 'get_blox_element_galleryimg_hook');
add_action('wp_ajax_nopriv_get_blox_element_galleryimg', 'get_blox_element_galleryimg_hook');
function get_blox_element_galleryimg_hook() {
    try {
		if( isset($_POST['images']) && $_POST['images']!='' ){
			$arr = explode(',', trim($_POST['images']));
			$counter = 0;
			$images = '';
			foreach ($arr as $value) {
				if( $value!='' ){
					$attach_url = wp_get_attachment_url($value);
					$attach_url = $attach_url!==false ? $attach_url : THEME_NOIMAGE;
					$images .= ($counter==0 ? '' : '^') . $attach_url;
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



function blox_parse_gallery_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title' => '',
		'images' => '',
		'layout' => 'default',
		'animation' => '',
		'extra_class' => '',
		'visibility' => ''
	), $atts ) );
	
	$title = isset($title) && $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';
	$is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
	$visibility = str_replace(',', ' ', $visibility);
	$extra_class .= " $visibility $is_animate";
	
	if( $images!='' ){
		$img_array = explode(',', $images);
		$html = '';

		foreach ($img_array as $img_id) {
			$attach_url = wp_get_attachment_url($img_id);
			$attach_url = $attach_url!==false ? $attach_url : THEME_NOIMAGE;
			$img = $attach_url;
			$img = $layout=='imac' ? blox_aq_resize($attach_url, 800, 450, true) : $img;
			$img = $layout=='laptop' ? blox_aq_resize($attach_url, 800, 500, true) : $img;
			$img = $layout=='iphone' ? blox_aq_resize($attach_url, 640, 1130, true) : $img;
			$img = $layout=='' || $layout=='default' ? blox_aq_resize($attach_url, 960, 540, true) : $img;

			$html .= '<div class="swiper-slide">
						<img src="'.$img.'" style="width:100%;" alt="Image" class="img-responsive" />
                      </div>';
		}

		$result = '<div class="swiper-container swipy-slider">
	                    <div class="swiper-wrapper">'. $html .'</div>
	                    <div class="swiper-control-prev"><i class="fa-angle-left"></i></div>
	                    <div class="swiper-control-next"><i class="fa-angle-right"></i></div>
	                    <div class="swiper-pagination"></div>
	               </div>';

		
		if( in_array($layout, array('imac', 'laptop', 'iphone')) ){
			$device = $layout;
			$device = $layout=='laptop' ? 'macbook' : $layout;
			$device = $layout=='iphone' ? 'iphone5 portrait white' : $layout;
			$result = '<div class="device-mockup '. $device .'">
			                <div class="device">
			                    <div class="screen">
			                        <div class="gallery_viewport">'.$result.'</div>
			                    </div>
			                    <div class="button"></div>
			                </div>
			           </div>';
		}

		return '<div class="blox-element blox-gallery '. $extra_class .'" data-animate="'. $animation .'">
					'. $title . $result .'
		        </div>';
	}
	
	return '';
}
add_shortcode( 'blox_gallery', 'blox_parse_gallery_hook' );


?>