<?php

function blox_parse_counter_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'number' => '0',
                'text' => '',
                'icon' => '',
                'count_type' => 'scroll',
                'animation' => '',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));

    $icon = $icon != '' ? "<span class='counter-icon'><span class='$icon'></span></span>" : '';
    $is_animate = $animation!='none' && $animation!='' ? 'animate' : '';
    $visibility = str_replace(',', ' ', $visibility);
    $text = $text!='' ? "<span class='counter-text'>$text</span>" : '';

    if( $count_type=='scroll' ){
        $numeric_format = "";
        $char_nums = str_split($number);
        foreach ($char_nums as $char) {
            if( preg_match("/([0-9])/", $char) ){
                $numeric_char = "";
                for( $i=0; $i<=(int)$char; $i++ ){
                    $numeric_char .= "<li>$i</li>";
                }
                $numeric_char = $numeric_char!='' ? "<ul>$numeric_char</ul>" : "";
                $numeric_format .= "<div class='numeric'><span>$char</span> $numeric_char</div>";
            }
            else{
                $numeric_format .= "<span class='none-numeric'>$char</span>";
            }
        }
        return "<div class='blox-element blox-counter counter-scroll $is_animate $extra_class $visibility' data-animate='$animation'><div class='counter-top'>$icon <div class='cl'>$numeric_format</div></div> $text</div>";
    }

    return "<div class='blox-element blox-counter counter-count $is_animate $extra_class $visibility' data-animate='$animation'><span class='counter-top'>$icon <span class='counter-number'>$number</span></span> $text</div>";

}

add_shortcode('blox_counter', 'blox_parse_counter_hook');

?>