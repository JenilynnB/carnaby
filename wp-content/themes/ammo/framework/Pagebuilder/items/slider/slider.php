<?php

add_action('wp_ajax_get_blox_element_sliders', 'get_blox_element_sliders_hook');
add_action('wp_ajax_nopriv_get_blox_element_sliders', 'get_blox_element_sliders_hook');

function get_blox_element_sliders_hook() {
    try {

        global $tt_sliders;
        $selected_slider = isset($_POST['slider']) ? $_POST['slider'] : '';

        echo '<p>';
        echo '<label>Defined Sliders</label>';
        echo '<select id="blox_elem_option_slider" name="slider">';
        foreach ($tt_sliders as $id => $slider) {
            echo '<option value="' . $id . '" ' . (trim($selected_slider) == trim($id) ? 'selected="selected"' : '') . '>' . $slider . '</option>';
        }
        echo '</select>';
        echo '</p>';

        echo '<p>
                <label>Extra Classes</label>
                <input type="text" id="blox_el_option_class" value="' . (isset($_POST['extra_class']) ? $_POST['extra_class'] : '') . '" />
            </p>';
    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}

function blox_parse_slider_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'slider' => '',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));

    $visibility = str_replace(',', ' ', $visibility);

    $result = '';
    if ($slider != '') {        
        $result .= "<div class='blox_element blox_slider $extra_class $visibility'>";
        
        $slider_name = $slider;
        $slider = explode("_", $slider_name);
        $shortcode = '';
        if (strpos($slider_name, "layerslider") !== false)
            $shortcode = "[" . $slider[0] . " id='" . $slider[1] . "']";
        elseif (strpos($slider_name, "revslider") !== false)
            $shortcode = "[rev_slider " . $slider[1] . "]";
        elseif (strpos($slider_name, "masterslider") !== false)
            $shortcode = "[masterslider id='" . $slider[1] . "']";

        $result .= do_shortcode($shortcode);
        $result .= '</div>';
    }
    return $result;
}

add_shortcode('blox_slider', 'blox_parse_slider_hook');
?>