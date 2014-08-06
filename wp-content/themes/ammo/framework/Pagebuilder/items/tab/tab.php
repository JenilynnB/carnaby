<?php

function blox_parse_tab_hook($atts, $content = null) {
    extract(shortcode_atts(array(
        'title' => '',
        'nav_style' => 'nav-tabs',
        'animation' => '',
        'extra_class' => '',
        'visibility' => '',
        'skin' => ''
    ), $atts));

    $title = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';
    $is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
    $skin = $skin=='default' ? '' : $skin;
    $visibility = str_replace(',', ' ', $visibility);
    $extra_class .= ' '.$visibility;
    $extra_class .= ' '.$skin;
    $extra_class .= ' '.$is_animate;

    return $title.'<div class="blox-element tabs '.$extra_class.'" data-animate="'.$animation.'">
                <ul class="nav '. $nav_style .'"></ul>
                <div class="tab-content">'. do_shortcode($content) .'</div>
            </div>';

}

add_shortcode('blox_tab', 'blox_parse_tab_hook');

function blox_parse_tab_item_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'title' => 'Tab item',
                'icon' => ''
                    ), $atts));

    $tab_id = uniqid();
    return '<div class="tab-pane fade" id="tab-'. $tab_id .'" title="' . ($icon != '' ? "<i class='$icon'></i>" : '') . $title . '">'. do_shortcode($content) .'</div>';
}

add_shortcode('blox_tab_item', 'blox_parse_tab_item_hook');

?>