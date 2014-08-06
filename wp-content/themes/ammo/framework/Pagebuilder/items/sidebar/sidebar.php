<?php

add_action('wp_ajax_get_blox_element_sidebars', 'get_blox_element_sidebars_hook');
add_action('wp_ajax_nopriv_get_blox_element_sidebars', 'get_blox_element_sidebars_hook');

function get_blox_element_sidebars_hook() {
    try {

        global $wp_registered_sidebars;
        $selected_sidebar = isset($_POST['sidebar']) ? $_POST['sidebar'] : '';

        echo '<p>';
        echo '<label>Title</label>';
        echo '<input type="text" id="blox_el_option_title" value="' . (isset($_POST['title']) ? $_POST['title'] : '') . '" />';
        echo '</p>';

        echo '<p class="blox_field_with_desc">';
        echo '<label>Defined Sidebar</label>';
        echo '<select id="blox_elem_option_sidebar">';
        foreach ($wp_registered_sidebars as $key => $value) {
            echo '<option value="' . $key . '" ' . (trim($selected_sidebar) == trim($key) ? 'selected' : '') . '>' . $value['name'] . '</option>';
        }
        echo '</select>';
        echo '<span class="item_desc">If you need an unique sidebar here, you have to create new one on Theme Options =&gt; <b>Custom Sidebar</b> and then add your Appearence =&gt; <b>Widgets</b>. Later on select it here.</span>';
        echo '</p>';

    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}

function blox_parse_sidebar_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'title' => '',
                'sidebar' => ''
                    ), $atts));

    $result = '<div class="blox-element blox-sidebar">';
    $result .= $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';
    
    if ($sidebar != '' && is_active_sidebar($sidebar)) {
        ob_start();
        dynamic_sidebar($sidebar);
        $result .= ob_get_contents();
        ob_end_clean();
    }
    $result .= '</div>';
    
    return $result;
}

add_shortcode('blox_sidebar', 'blox_parse_sidebar_hook');



?>