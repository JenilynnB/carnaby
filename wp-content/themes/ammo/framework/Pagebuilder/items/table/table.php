<?php

function blox_parse_table_hook($atts, $content = null) {
    extract(shortcode_atts(array(
        'title' => '',
        'button_icon' => '',
        'animation' => '',
        'extra_class' => '',
        'skin' => '',
        'visibility' => ''
    ), $atts));

    $is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
    $visibility = str_replace(',', ' ', $visibility);
    $extra_class .= ' '.$is_animate;
    $extra_class .= ' '.$visibility;

    return '<div class="blox-element pricing-row ' . $extra_class.'" data-animate="'. $animation .'" data-skin="'. $skin .'" data-button-icon="'.$button_icon.'">' . do_shortcode($content) . '</div>';
}

add_shortcode('blox_table', 'blox_parse_table_hook');

function blox_parse_table_row_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'type' => ''
                    ), $atts));

    return '<div class="blox_table_row" type="' . $type . '">' . do_shortcode($content) . '</div>';
}

add_shortcode('blox_table_row', 'blox_parse_table_row_hook');

function blox_parse_table_cell_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'type' => ''
                    ), $atts));

    return '<div class="blox_table_cell" type="' . $type . '">' . do_shortcode($content) . '</div>';
}

add_shortcode('blox_table_cell', 'blox_parse_table_cell_hook');
?>