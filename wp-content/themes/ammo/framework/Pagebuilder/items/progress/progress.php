<?php

function blox_parse_progress_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'title' => '',
                'percent' => '60',
                'type' => 'default',
                'striped' => '',
                'active' => '',
                'animation' => '',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));

    $type = $type!='default' ? $type : '';
    $is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
    $striped = $striped=='1' ? 'progress-striped' : '';
    $active = $active=='1' ? 'active' : '';
    $visibility = str_replace(',', ' ', $visibility);

    $result = "<div class='blox-element progress-wrap $is_animate $extra_class $visibility' data-animate='$animation'>
                    <div class='bar-text'>
                        <span>$title:</span>
                        <span class='pull-right'>$percent%</span></div>
                    <div class='progress $striped $active'>
                        <div class='progress-bar $type' role='progressbar' aria-valuenow='".(int)$percent."' aria-valuemin='0' aria-valuemax='100' style='width: ".(int)$percent."%;'>
                            <span class='sr-only'>".(int)$percent."% ".__('Complete', 'themeton')."</span>
                        </div>
                    </div>
                </div>";

    return $result;
}

add_shortcode('blox_progress', 'blox_parse_progress_hook');
?>