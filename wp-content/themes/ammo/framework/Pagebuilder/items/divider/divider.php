<?php

function blox_parse_divider_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'type' => 'default',
                'space' => '5',
                'fullwidth' => '',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));

    $type = $type!='default' ? $type : '';
    $spacer = $type=='space' ? 'height:'. $space .'px;' : '';
    $visibility = str_replace(',', ' ', $visibility);
    $extra_class = $extra_class.' '.$visibility;
    $extra_class .= $fullwidth=='1' ? ' divider-fullwidth' : '';
    
    return "<div class='blox-element divider $type $extra_class' style='$spacer'></div>";
}

add_shortcode('blox_divider', 'blox_parse_divider_hook');
?>