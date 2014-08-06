<?php

add_action('admin_enqueue_scripts', 'admin_page_option_render_scripts');

function admin_page_option_render_scripts($hook) {
    if (themeton_admin_post_type() != 'page') {
        return;
    }

    // Code Mirror
    wp_enqueue_style('codemirror',                 get_template_directory_uri().'/framework/admin-assets/codemirror/lib/codemirror.css');
    wp_enqueue_style('codemirror-theme',           get_template_directory_uri().'/framework/admin-assets/codemirror/theme/monokai.css');
    wp_enqueue_script('codemirror',                get_template_directory_uri().'/framework/admin-assets/codemirror/lib/codemirror.js', false, false, false);
    wp_enqueue_script('codemirror-matchbrackets',  get_template_directory_uri().'/framework/admin-assets/codemirror/addon/edit/matchbrackets.js', false, false, false);
    wp_enqueue_script('codemirror-css',            get_template_directory_uri().'/framework/admin-assets/codemirror/mode/css/css.js', false, false, false);

    // Page Scripts
    wp_enqueue_style('tt_admin_page_option_style', get_template_directory_uri() . '/framework/post-type/page-styles.css');
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('tt_admin_page_option_script', get_template_directory_uri() . '/framework/post-type/page-scripts.js', false, false, true);
}


/* set external sliders
=======================================*/
global $tt_sliders;
$tt_sliders = array("none" => 'No slider');
$tt_sliders = array_merge($tt_sliders, get_external_sliders('layerslider'));
$tt_sliders = array_merge($tt_sliders, get_external_sliders('revslider'));
$tt_sliders = array_merge($tt_sliders, get_external_sliders('masterslider'));




function get_post_type_options_page(){
    global $smof_data, $tt_sidebars, $tt_sliders, $post;


    // init One Page menus
    $onepages_for_navs = array();
    $onepage_with_templates = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-one-page.php'
            ));
    foreach ($onepage_with_templates as $page) {
        $onepage_menu = array();
        $onepage_menu['onepage_nav' . $page->ID] = $page->post_title;
        $onepages_for_navs = array_merge($onepages_for_navs, $onepage_menu);
    }

    // add_action('after_setup_theme', 'onepage_menu_setup');

    // function onepage_menu_setup() {
    //     global $onepages_for_navs;
    //     register_nav_menus($onepages_for_navs);
    // }



    // Google Font Options
    include_once file_require(ADMIN_PATH . 'functions/google-fonts.php');
    $google_fonts = get_google_webfonts();

    $google_webfonts = array();
    $google_webfonts["default"] = "Default (Helvetica, Arial, sans-serif)";
    foreach ($google_fonts as $font) {
        $google_webfonts[$font['family']] = $font['family'];
    }


    // Less Options
    $smof_vars = get_theme_options_less_vars();
    $less_options_orig = get_less_variables();
    $less_options = array();
    foreach ($less_options_orig as $opt) {
        if( $opt['variable']!='less-heading' ){
            $page_vars = get_page_options_less_vars();
            $opt_value = tt_getmeta('less_'.$opt['variable']);

            if( isset($page_vars[$opt['variable']]) && !empty($page_vars[$opt['variable']]) ){
                $opt_value = $page_vars[$opt['variable']];
            }
            elseif( !empty($opt_value) ){
                $opt_value = $opt_value;
            }
            elseif( isset($smof_vars[$opt['variable']]) && !empty($smof_vars[$opt['variable']]) ){
                $opt_value = $smof_vars[$opt['variable']];
            }
            else{
                $opt_value = $opt['value'];
            }

            //$less_options[] = array( $opt['variable']=>$opt_value );
            $less_options = array_merge($less_options, array( $opt['variable']=>$opt_value ));
        }
    }

    global $post;
    $less_content = "";
    if( isset($post->ID) ){
        $less_vars = tt_getmeta("less_page_variables", $post->ID);
        $brand_primary = tt_getmeta("less_brand-primary", $post->ID);
        if( $less_vars=="" && $brand_primary!="" ){
            $less_arr = array();
            foreach ($less_options as $key=>$val){
                if( $key!='less-heading' ){
                    $current_val = tt_getmeta('less_'.$key, $post->ID);
                    $current_val = $current_val!="" ? $current_val : $val;
                    $less_arr = array_merge($less_arr, array( $key=>$current_val ));
                }
            }
            $encoded_arr = base64_encode(serialize($less_arr));
            if (count(get_post_meta($post->ID, "_less_page_variables")) == 0) {
                add_post_meta($post->ID, "_less_page_variables", $encoded_arr, true);
            } else{
                update_post_meta($post->ID, "_less_page_variables", $encoded_arr);
            }
        }

        $encoded_str = tt_getmeta("less_page_variables", $post->ID);
        $less_content = get_less_editor_content( $encoded_str );
        update_post_meta($post->ID, "_up_less_editor", $less_content);
    }



    $url = ADMIN_IMAGES;
    $tmp_arr = array(
        'onepage' => array(
            'label' => 'One Page Options',
            'post_type' => 'page',
            'items' => array(
                array(
                    'name' => 'onepages',
                    'type' => 'onepage',
                    'label' => 'Pages for One Page Template',
                    'desc' => 'Build page with Multi pages',
                    'default' => '',
                ),
                array(
                    'name' => 'onepages_names',
                    'type' => 'text',
                    'label' => 'Pages Names',
                    'default' => '',
                ),
                array(
                    'name' => 'onepages_links',
                    'type' => 'text',
                    'label' => 'Pages Links',
                    'default' => '',
                ),
                array(
                    'name' => 'onepage_menu',
                    'type' => 'checkbox',
                    'label' => 'Use One Page Menu',
                    'desc' => 'If you check this option, current pages menu would be selected pages titles.'
                )
            )
        ),
        'page' => array(
            'label' => 'Page Options',
            'post_type' => 'page',
            'items' => array(
                array(
                    'name' => 'slider',
                    'type' => 'select',
                    'label' => 'Top slider',
                    'option' => $tt_sliders,
                    'desc' => 'Select a slider that you\'ve created on LayerSlider and Revolution Slider. This slider shows up between header and page title.'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'slider_top',
                    'label' => 'Slider at the top of Page',
                    'default' => '0',
                    'desc' => 'If you wanna show fullscreen slider, hope you like this option.'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'hide_topbar',
                    'label' => 'Hide top bar',
                    'default' => '0',
                    'desc' => 'If you wanna hide top bar when Topbar enabled in theme option.'
                ),
                array(
                    'name' => 'page_layout',
                    'type' => 'thumbs',
                    'label' => 'Page Layout',
                    'default' => 'full',
                    'option' => array(
                        'full' => ADMIN_DIR . 'assets/images/1col.png',
                        'right' => ADMIN_DIR . 'assets/images/2cr.png',
                        'left' => ADMIN_DIR . 'assets/images/2cl.png'
                    ),
                    'desc' => 'Select Page Layout (Fullwidth | Right Sidebar | Left Sidebar)'
                ),
                array(
                    'name' => 'sidebar',
                    'type' => 'select',
                    'label' => 'Page Sidebar',
                    'default' => 'page-sidebar',
                    'option' => $tt_sidebars,
                    'desc' => 'You should select a sidebar If you\'ve chosen page layout with sidebar. And if you need an unique sidebar for this page, you have to create new one on Theme Options => <b>Custom Sidebar</b> and then add your Appearence => <b>Widgets</b>. Later on select it here.'
                ),
                

                array(
                    'type' => 'checkbox',
                    'name' => 'title_show',
                    'label' => 'Title (show/hide)',
                    'default' => '1'
                ),
                /* Start title options group
                ===================================*/
                array(
                    'type' => 'start_group',
                    'name' => 'title_options',
                    'visible' => true
                ),
                array(
                    'type' => 'select',
                    'name' => 'title_align',
                    'label' => 'Title align',
                    'default' => 'left',
                    'option' => array(
                        'left' => 'Left',
                        'center' => 'Center',
                        'right' => 'Right'
                    ),
                    'desc' => 'Title alignment'
                ),
                array(
                    'name' => 'title_padding',
                    'type' => 'text',
                    'label' => 'Title Spacing from Top',
                    'default' => '',
                    'desc' => 'Page Title Sections padding-top size (px)'
                ),
                array(
                    'name' => 'title_padding_bottom',
                    'type' => 'text',
                    'label' => 'Title Spacing from Bottom',
                    'default' => '',
                    'desc' => 'Page Title Sections padding-bottom size (px)'
                ),
                array(
                    'name' => 'teaser',
                    'type' => 'textarea',
                    'label' => 'Teaser text',
                    'default' => '',
                    'desc' => 'Add description text which shows up at bottom of Page Title.'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'title_color',
                    'label' => 'Title text invert color ?',
                    'default' => '0',
                    'desc' => 'If this option not active, text color is default color'
                ),
                array(
                    'type' => 'colorpicker',
                    'name' => 'title_bg_color',
                    'label' => 'Title background color',
                    'default' => get_theme_mod('title_bg_color'),
                    'desc' => 'Page Title Section Background Color'
                ),
                array(
                    'type' => 'background',
                    'name' => 'title_bg',
                    'label' => 'Title Background Image',
                    'default' => '',
                    'desc' => 'If you want to show your title area beautiful, this option exactly you need.'
                ),
                array(
                    'type' => 'colorpicker',
                    'name' => 'title_overlay_color',
                    'label' => 'Overlay Color',
                    'default' => '',
                    'desc' => 'It needs when use background image'
                ),
                array(
                    'name' => 'title_overlay_opacity',
                    'type' => 'text',
                    'label' => 'Overlay Opacity',
                    'default' => '',
                    'desc' => 'Overlay opacity value: [0, 0.1, ..1]'
                ),
                array(
                    'name' => 'title_options',
                    'type' => 'end_group'
                )
                /* End title options group
                ===================================*/
            )
        ),
        'ultimate_page' => array(
            'label' => 'Ultimate Page Options',
            'post_type' => 'page',
            'items' => array(
                array(
                    'type' => 'select',
                    'name' => 'up_layout',
                    'label' => 'Container Layout',
                    'default' => 'full',
                    'option' => array(
                        'full' => 'Full (Wide) Layout',
                        'boxed' => 'Boxed Layout'
                    )
                ),
                array(
                    'name' => 'up_margin_top',
                    'type' => 'number',
                    'label' => 'Site Top Spacing',
                    'default' => '0',
                    'desc' => 'Please set site top margin space. Number value in pixels. Note: This style appects only on large screens (>1200px).'
                ),
                array(
                    'name' => 'up_margin_bottom',
                    'type' => 'number',
                    'label' => 'Site Bottom Spacing',
                    'default' => '0',
                    'desc' => 'Please set site bottom margin space. Number value in pixels. Note: This style appects only on large screens (>1200px).'
                ),
                array(
                    'type' => 'background',
                    'name' => 'up_background_img',
                    'label' => 'Background Image',
                    'default' => '',
                    'desc' => 'Add here your body background image. Image and custom pattern are acceptable.'
                ),
                array(
                    'name' => 'up_logo',
                    'type' => 'image',
                    'label' => 'Logo',
                    'default' => '',
                    'desc' => 'If you wanna use custom logo on this page, you should use it.'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'up_header',
                    'label' => 'Enable Header',
                    'default' => '1'
                ),
                /* Start Header Options
                ===================================*/
                array(
                    'type' => 'start_group',
                    'name' => 'up_header_group',
                    'visible' => true
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'up_topbar',
                    'label' => 'Enable Topbar',
                    'default' => '0'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'up_fixed_menu',
                    'label' => 'Navigation Fixed at Top',
                    'default' => '0',
                    'desc' => 'Navigation menu stays fixed at top of your site when you scroll down.'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'up_enable_logo',
                    'label' => 'Enable Logo',
                    'default' => '1',
                    'desc' => 'You can remove logo.'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'up_enable_search',
                    'label' => 'Enable Search Box',
                    'default' => '1',
                    'desc' => 'You can remove search box at next of your main menu.'
                ),
                array(
                    'type' => 'select',
                    'name' => 'up_menu_align',
                    'label' => 'Menu Alignment',
                    'default' => 'center',
                    'option' => array(
                        'left' => 'Left',
                        'center' => 'Center',
                        'right' => 'Right'
                    ),
                    'desc' => 'Menu alignment (left, center, right).'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'up_header_transparent',
                    'label' => 'Enable Header Transparent',
                    'default' => '0',
                    'desc' => 'If you enable this option, header is transparent.'
                ),
                array(
                    'name' => 'up_header_height',
                    'type' => 'number',
                    'label' => 'Header Height',
                    'default' => isset($smof_vars['header-height']) ? (int)$smof_vars['header-height'] : '80',
                    'desc' => 'Default : 80px'
                ),
                array(
                    'name' => 'up_header_group',
                    'type' => 'end_group'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'up_footer',
                    'label' => 'Enable Footer',
                    'default' => '1'
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'up_footer_bar',
                    'label' => 'Enable Footer Bar',
                    'default' => '1'
                ),
                array(
                    'type' => 'select',
                    'name' => 'up_menu_font',
                    'label' => 'Menu Font',
                    'default' => isset($smof_vars['base-font-menu']) ? str_replace("'", '', str_replace('"', '', $smof_vars['base-font-menu'])) : 'default',
                    'option' => $google_webfonts,
                    'desc' => 'Menu link font including sub menu links and description texts.'
                ),
                array(
                    'type' => 'select',
                    'name' => 'up_heading_font',
                    'label' => 'Heading Font',
                    'default' => isset($smof_vars['base-font-heading']) ? str_replace("'", '', str_replace('"', '', $smof_vars['base-font-heading'])) : 'default',
                    'option' => $google_webfonts,
                    'desc' => 'All heading tags (H1 through H6) font incluing Post title and Widget title etc.'
                ),
                array(
                    'type' => 'select',
                    'name' => 'up_body_font',
                    'label' => 'Body Font',
                    'default' => isset($smof_vars['base-font-body']) ? str_replace("'", '', str_replace('"', '', $smof_vars['base-font-body'])) : 'default',
                    'option' => $google_webfonts,
                    'desc' => 'Main body text.'
                )
            )
        ),
        'less_option' => array(
            'label' => 'Less Options (Current Page)',
            'post_type' => 'page',
            'items' => array(
                array(
                    'name' => 'up_less_editor',
                    'type' => 'textarea',
                    'label' => 'LESS Editor',
                    'default' => $less_content
                )
            )
        ),
    );

    return $tmp_arr;
}




add_action('wp_ajax_up_reset_less_options', 'up_reset_less_options_hook');
add_action('wp_ajax_nopriv_up_reset_less_options', 'up_reset_less_options_hook');
function up_reset_less_options_hook(){
    if( isset($_POST['post_id']) && !empty($_POST['post_id']) ){
        delete_post_meta( (int)$_POST['post_id'], '_less_page_variables' );
        delete_post_meta( (int)$_POST['post_id'], '_up_menu_font' );
        delete_post_meta( (int)$_POST['post_id'], '_up_heading_font' );
        delete_post_meta( (int)$_POST['post_id'], '_up_body_font' );
    }
    echo 'success';
    exit;
}
?>