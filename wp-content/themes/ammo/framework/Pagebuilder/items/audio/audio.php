<?php

function blox_parse_audio_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'title' => '',
                'color' => '#3a87ad',
                'animation' => '',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));


    $title = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';
    $visibility = str_replace(',', ' ', $visibility);
    $extra_class = $extra_class.' '.$visibility;

    if( validateURL($content) ){
        return get_audio_player( array('url'=>$content, 'title'=>$title, 'color'=>$color, 'extra_class'=>$extra_class) );
    }
    else{
        return "<div class='blox-element audio audio_embed $extra_class'>". $title . $content ."</div>";
    }

    return '';
}

add_shortcode('blox_audio', 'blox_parse_audio_hook');
?>