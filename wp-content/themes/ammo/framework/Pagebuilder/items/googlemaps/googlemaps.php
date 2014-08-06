<?php
	
function blox_parse_gmap_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'title' => '',
        'type' => 'embed',
		'lat' => '0',
        'long' => '0',
        'address' => '',
        'zoom' => '14',
        'pin' => '',
		'viewtype' => 'ROADMAP',
        'map_color' => '',
        'map_height' => '400',
        'animation' => '',
        'extra_class' => '',
        'visibility' => ''
	), $atts ) );

    $result = '';

    $title = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';
    $visibility = str_replace(',', ' ', $visibility);

    if($type == 'embed') {
        $result = $title.$content;
    }
    else{
        wp_register_script('googlemap_api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAQqYj4InzdbPwz-Kplwf9kltH5FrE5t5M&sensor=false');
        wp_enqueue_script('googlemap_api');
        
        $id = 'gmaps'.uniqid();
        $result = $title.'<div class="google-map '.$visibility.'" id="'.$id.'" data-lat="'.$lat.'" data-long="'.$long.'" data-zoom="'.$zoom.'" data-view-type="'.$viewtype.'" data-map-color="'.$map_color.'" data-pin="'.$pin.'" style="height: '.$map_height.'px; width: 100%;"></div>';
    }

    return '<div class="blox-element blox-gmap '.$extra_class.' '.$visibility.'">'.do_shortcode($result).'</div>';
}
add_shortcode( 'blox_gmap', 'blox_parse_gmap_hook' );

?>