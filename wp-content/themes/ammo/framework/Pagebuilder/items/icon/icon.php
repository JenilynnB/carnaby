<?php

function blox_parse_icon_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'link' => '',
                'target' => '0',
                'icon' => 'fa-smile-o',
                'style' => 'blox_elem_icon_no_bordered',
                'color' => '#000',
                'size' => '48',
                'align' => 'left',
                'animation' => '',
                'extra_class' => ''
                    ), $atts));
    $bgcolor = blox_light_dark($color);
    $color = $style == 'blox_elem_icon_filled' ? 'background-color:' . $color : 'color:' . $color;
    $animate_class = get_blox_animate_class($animation);
    $target = $target == '1' ? '_blank' : '_self';

    $before = $after = '';
    if ($align == 'center') {
        $before = '<div class="blox_element_center">';
        $after = '</div>';
    } else {
        $align = $align == 'right' ? 'pull-right' : '';
    }

    if ($link != '')
        return $before."<a href='$link' target='$target'><span class='blox_elem_icon $style $bgcolor $align $animate_class $extra_class' style='font-size:$size" . "px;$color'><i class='$icon'></i></span></a>".$after;
    else
        return $before."<span class='blox_elem_icon $style $bgcolor $align $animate_class $extra_class' style='font-size:$size" . "px;$color'><i class='$icon'></i></span>".$after;
}

add_shortcode('blox_icon', 'blox_parse_icon_hook');
?>