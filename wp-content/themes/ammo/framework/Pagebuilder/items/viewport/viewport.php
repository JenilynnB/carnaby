<?php


function blox_parse_viewport_hook( $atts, $content=null ) {
    extract( shortcode_atts( array(
        'title' => '',
        'image' => '',
        'layout' => 'default',
        'viewport_height' => '200',
        'link' => '#',
        'animation' => '',
        'extra_class' => '',
        'visibility' => ''
    ), $atts ) );
    
    $title_text = $title;
    $title = isset($title) && $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';
    $is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
    $visibility = str_replace(',', ' ', $visibility);
    $extra_class .= " $visibility $is_animate";

    $result = '<div class="viewport-screen" style="background-image:url('.$image.');"><a href="'.$link.'" target="_blank"></a></div>';

    

    if( in_array($layout, array('imac', 'laptop', 'iphone')) ){
        $device = $layout;
        $device = $layout=='laptop' ? 'macbook' : $layout;
        $device = $layout=='iphone' ? 'iphone5 portrait white' : $layout;
        $result = '<div class="device-mockup '. $device .'">
                        <div class="device">
                            <div class="screen">'.$result.'</div>
                            <div class="button"></div>
                        </div>
                   </div>';
    }
    else{
        $result = '<div class="browser">
                        <div class="browser-top-container">
                            <p class="browser-title">'.$title_text.'</p>
                            <ul class="browser-right-buttons"><li></li><li></li><li class="bb-last"></li></ul>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                        <div class="browser-middle-container">'.$result.'</div>
                        <div class="browser-bottom-container"></div>
                    </div>';
    }

    $style_attr = '';
    $style_attr = $layout=='default' ? 'height:'.$viewport_height.'px;' : $style_attr;

    return '<div class="blox-element blox-viewport '. $extra_class .'" style="'.$style_attr.'" data-animate="'. $animation .'">
                '. $title . $result .'
            </div>';
}
add_shortcode( 'blox_viewport', 'blox_parse_viewport_hook' );


?>