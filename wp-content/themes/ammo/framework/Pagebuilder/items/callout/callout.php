<?php

function blox_parse_callout_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'title' => '',
                'alignment' => 'left',
                'skin' => 'bordered',
                'animation' => '',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));


    $is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
    $title = $title!='' ? "<h1>$title</h1>" : '';
    $visibility = str_replace(',', ' ', $visibility);

    $content = fix_shortcode_paragraph($content);
    
    $html = "<div class='blox-element callout text-$alignment $skin $is_animate $extra_class $visibility' data-animate='$animation'>$title<p class='lead'>". do_shortcode($content) ."</p></div>";
    return $html;
}

add_shortcode('blox_callout', 'blox_parse_callout_hook');
?>