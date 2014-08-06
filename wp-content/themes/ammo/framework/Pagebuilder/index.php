<?php

/*
  Plugin Name: Blox Page Builder
  Plugin URI: http://www.themeton.com/blox-pagebuilder
  Description: Play Web Elements
  Version: 1.0
  Author: Themeton
  Author URI: http://themeton.com
 */

define('BLOX_PATH', trailingslashit(get_template_directory_uri()).'framework/Pagebuilder/');
define('BLOX_DIR', trailingslashit(get_template_directory()).'framework/Pagebuilder/');

require_once file_require(BLOX_DIR . 'inc/common_functions.php');
require_once file_require(BLOX_DIR . 'inc/loop_layouts.php');
require_once file_require(BLOX_DIR . 'inc/blox_aq_resizer.php');
require_once file_require(BLOX_DIR . 'inc/shortcodes.php');
require_once file_require(BLOX_DIR . 'render.php');



add_action('wp_enqueue_scripts', 'blox_frontend_scripts');
function blox_frontend_scripts() {
    
    wp_enqueue_script('jquery');

    /* Blox Style and Scripts
    ==========================================*/
    wp_enqueue_style('blox-style', file_require(BLOX_PATH.'css/blox-frontend.css', true));
    wp_enqueue_script('blox-script', file_require(BLOX_PATH.'js/blox-frontend.js', true), false, false, true);

}

function blox_wp_head() {
    echo '<script>
                var blox_plugin_path = "' . BLOX_PATH . '";
                var blox_ajax_url = "' . site_url() . '/wp-admin/admin-ajax.php";
          </script>';
}
add_action('wp_head', 'blox_wp_head');



add_action('wp_ajax_blox_template_data', 'blox_template_data_hook');
add_action('wp_ajax_nopriv_blox_template_data', 'blox_template_data_hook');
function blox_template_data_hook(){
    echo get_theme_mod('blox_templates');
    exit;
}


/* get blox templates */
function blox_get_template() {
    
    $blox_templates = get_theme_mod('blox_templates');
    if( !empty($blox_templates) ){
        $template_array = unserialize(base64_decode($blox_templates));
        return is_array($template_array) ? $template_array : array();
    }
    return array();
}


/*
 * BLOX TEMPLATE SAVE
 */
add_action('wp_ajax_blox_template_save', 'blox_template_save_hook');
add_action('wp_ajax_nopriv_blox_template_save', 'blox_template_save_hook');
function blox_template_save_hook() {
    try {
        $uid = uniqid();
        $template = array(
            array(
                'id' => $uid,
                'title' => $_POST['title'],
                'content' => $_POST['content']
            )
        );

        $templates = blox_get_template();
        $templates = array_merge($templates, $template);
        $encoded_templates = base64_encode(serialize($templates));
        set_theme_mod( 'blox_templates', $encoded_templates );


        $arr = array();
        foreach ($templates as $tmp) {
            $new_temp = $tmp;
            $new_temp['content'] = '';
            $arr[] = $new_temp;
        }

        echo json_encode($arr);
    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}

add_action('wp_ajax_blox_template_remove', 'blox_template_remove_hook');
add_action('wp_ajax_nopriv_blox_template_remove', 'blox_template_remove_hook');

function blox_template_remove_hook() {
    try {
        $templates = blox_get_template();
        $new_template = array();
        foreach ($templates as $template) {
            if ($template['id'] != $_POST['id']) {
                $new_template[] = $template;
            }
        }

        $encoded_templates = base64_encode(serialize($new_template));
        set_theme_mod( 'blox_templates', $encoded_templates );

        $arr = array();
        foreach ($new_template as $tmp) {
            $new_temp = $tmp;
            $new_temp['content'] = '';
            $arr[] = $new_temp;
        }

        echo json_encode($arr);
    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}

add_action('wp_ajax_blox_template_load', 'blox_template_load_hook');
add_action('wp_ajax_nopriv_blox_template_load', 'blox_template_load_hook');

function blox_template_load_hook() {
    try {
        $content = '';
        $templates = blox_get_template();
        foreach ($templates as $template) {
            if ($template['id'] == $_POST['id']) {
                $content = stripslashes($template['content']);
            }
        }
        echo $content;
    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}

?>