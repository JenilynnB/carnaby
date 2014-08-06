<?php

function blox_parse_toggle_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'title' => '',
                'toggle_state' => '0',
                'animation' => '',
                'extra_class' => ''
            ), $atts));

    $animate_class = get_blox_animate_class($animation);
    
    return  '<div class="blox_element tt_toggle ' . ($toggle_state == '1' ? 'tt_toggle_open' : '') . ' ' . $extra_class . ' '.$animate_class.'">
                <div class="tt_toggle_title">
                    <div><a href="#">' . do_shortcode($title) . '<span class="tt_icon fa-plus"></span></a></div>
                </div>
                <div class="tt_toggle_inner">' . do_shortcode($content) . '</div>
            </div>';
}

add_shortcode('blox_toggle', 'blox_parse_toggle_hook');
?>