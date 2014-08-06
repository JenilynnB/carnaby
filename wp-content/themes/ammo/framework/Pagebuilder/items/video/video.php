<?php

function blox_parse_video_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'title' => '',
                'image' => '',
                'color' => '#3a87ad',
                'animation' => '',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));

    $title = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';
    $visibility = str_replace(',', ' ', $visibility);
    $extra_class = $extra_class.' '.$visibility;
    $result = '';

    if( validateURL($content) ){
        if( wp_oembed_get($content) !== false ){
            return "<div class='blox-element video video_embed $extra_class'>". $title . wp_oembed_get($content) ."</div>";
        }
        else{
            return get_video_player( array('url'=>$content, 'title'=>$title, 'poster'=>$image, 'color'=>$color, 'extra_class'=>$extra_class) );
        }
    }
    else{
        return "<div class='blox-element video video_embed $extra_class'>". $title . $content ."</div>";
    }
    
    return $result;
}

add_shortcode('blox_video', 'blox_parse_video_hook');
?>