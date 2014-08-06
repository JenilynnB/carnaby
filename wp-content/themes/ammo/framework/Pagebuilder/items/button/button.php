<?php

function blox_parse_button_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'text' => 'Button',
                'link' => '#',
                'target' => '_blank',
                'button_type' => 'btn-default',
                'size' => 'btn-md',
                'icon' => '',
                'align' => '',
                'animation' => '',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));

    $icon = $icon != '' ? "<span class='$icon'></span>" : '';
    if($target == 'lightbox') {
        $target = '_blank';
        $extra_class .= ' lightbox';
    }
    $is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
    $visibility = str_replace(',', ' ', $visibility);

    $before = $after = '';
    if($align == 'center') {
        $before = '<div class="blox_element_center">';
        $after = '</div>';
    } else {
        $align = $align == 'right' ? 'pull-right' : '';
    }

    return "$before<a href='$link' class='btn $button_type $size $is_animate $align $extra_class $visibility' target='$target' data-animate='$animation'>$icon $text</a>$after";

}

add_shortcode('blox_button', 'blox_parse_button_hook');

?>