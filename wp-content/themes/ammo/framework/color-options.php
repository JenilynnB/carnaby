<?php

global $color_options;
global $theme_less_vars;

function getLessValue($variable){
     global $theme_less_vars;
     $result = "";
     if( isset( $theme_less_vars[$variable] ) && !empty($theme_less_vars[$variable]) ){
          $result = $theme_less_vars[$variable];
     }
     $result = str_replace('"', '', $result);
     $result = str_replace("'", '', $result);
     $result = str_replace("\"", "", $result);
     return $result;
}


class ThemetonTheme_Customize {

     public static function init_data(){
        global $color_options, $theme_less_vars;

        $theme_less_vars = get_theme_options_less_vars();

        // Theme path
        $theme_path = get_stylesheet_directory_uri();

        // Google Font Options
        include_once file_require(ADMIN_PATH . 'functions/google-fonts.php');
        $google_fonts = get_google_webfonts();

        $google_webfonts = array();
        $google_webfonts["default"] = "Default (Helvetica, Arial, sans-serif)";
        foreach ($google_fonts as $font) {
          $google_webfonts[$font['family']] = $font['family'];
        }

        $font_sizes = array();
        for($i=8; $i<73; $i++){
           $font_sizes = array_merge( $font_sizes, array($i.'px'=>$i.'px') );
        }

        $transparent_percent = array('10'=>'10%', '20'=>'20%', '30'=>'30%', '40'=>'40%', '50'=>'50%', '60'=>'60%', '70'=>'70%', '80'=>'80%', '90'=>'90%', '100'=>'100%');

        $header_heights = array();
        for($i=0; $i<501; $i++){
           $header_heights = array_merge($header_heights, array( $i.'px'=>$i.'px' ));
        }

        $topbar_heights = array();
        for($i=0; $i<100; $i++){
           $topbar_heights = array_merge($topbar_heights, array( $i.'px'=>$i.'px' ));
        }

        $color_options = array(
               array(
                    'id'           => 'general_options',
                    'title'        => 'Theme: General Option',
                    'description'  => 'You can set site general layouts, options.',
                    'items'        => array(
                         array(
                              'id'      => 'general-layout',
                              'label'   => 'Container Layout',
                              'default' => 'full',
                              'type'    => 'select',
                              'choices' => array(
                                   'full' => 'Fullwidth Layout',
                                   'boxed' => 'Boxed Layout'
                               )
                         ),
                         array(
                              'id'      => 'general-top-space',
                              'label'   => 'Site Top Space',
                              'default' => '0px',
                              'type'    => 'text'
                         ),
                         array(
                              'id'      => 'general-bottom-space',
                              'label'   => 'Site Bottom Space',
                              'default' => '0px',
                              'type'    => 'text'
                         ),
                         array(
                              'id'      => 'general-bg-image',
                              'label'   => 'Background Image',
                              'default' => '',
                              'type'    => 'image'
                         ),
                         array(
                              'id'      => 'general-bg-repeat',
                              'label'   => 'Background Repeat',
                              'default' => 'no-repeat',
                              'type'    => 'select',
                              'choices' => array(
                                   'no-repeat' => 'No Repeat',
                                   'repeat' => 'Tile',
                                   'repeat-x' => 'Tile Horizontally',
                                   'repeat-y' => 'Tile Vertically'
                               )
                         ),
                         array(
                              'id'      => 'general-bg-position',
                              'label'   => 'Background Position (x,y)',
                              'default' => 'left top',
                              'type'    => 'select',
                              'choices' => array(
                                   'left top' => 'Left Top',
                                   'left center' => 'Left Middle',
                                   'left bottom' => 'Left Bottom',
                                   'center top' => 'Center Top',
                                   'center center' => 'Center Middle',
                                   'center bottom' => 'Center Bottom',
                                   'right top' => 'Right Top',
                                   'right center' => 'Right Middle',
                                   'right bottom' => 'Right Bottom'
                               )
                         ),
                         array(
                              'id'      => 'general-bg-attach',
                              'label'   => 'Background Attachment',
                              'default' => 'scroll',
                              'type'    => 'select',
                              'choices' => array(
                                   'scroll' => 'Scroll',
                                   'fixed' => 'Fixed'
                               )
                         )
                    )
               ),
               array(
                    'id'           => 'header_options',
                    'title'        => 'Theme: Header Option',
                    'description'  => '',
                    'items'        => array(
                         array(
                              'id'      => 'top-bar-height',
                              'label'   => 'Topbar Height',
                              'default' => getLessValue('top-bar-height'),
                              'type'    => 'select',
                              'choices' => $topbar_heights
                         ),
                         array(
                              'id'      => 'logo',
                              'label'   => 'Logo Image',
                              'default' => $theme_path . "/assets/images/logo.png",
                              'type'    => 'image'
                         ),
                         array(
                              'id'      => 'logo-height',
                              'label'   => 'Logo Height',
                              'default' => getLessValue('logo-height'),
                              'type'    => 'text'
                         ),
                         array(
                              'id'      => 'logo-width',
                              'label'   => 'Logo Width',
                              'default' => getLessValue('logo-width'),
                              'type'    => 'text'
                         ),
                         array(
                              'id'      => 'logo_retina',
                              'label'   => 'Retina Logo Image',
                              'default' => $theme_path . "/assets/images/logo@2x.png",
                              'type'    => 'image'
                         ),
                         array(
                              'id'      => 'icon_favicon',
                              'label'   => 'Favicon ( 16x16, PNG/ICO/JPG )',
                              'default' => $theme_path . "/assets/images/favicon.png",
                              'type'    => 'image'
                         ),
                         array(
                              'id'      => 'logo_admin',
                              'label'   => 'Login Page Logo (up to 274x95px)',
                              'default' => $theme_path . "/assets/images/logo-admin.png",
                              'type'    => 'image'
                         ),
                         array(
                              'id'      => 'header-height',
                              'label'   => 'Header Height (higher than logo!)',
                              'default' => getLessValue('header-height'),
                              'type'    => 'select',
                              'choices' => $header_heights
                         ),
                         array(
                              'id'      => 'fixed_menu',
                              'label'   => 'Header Fixed at Top',
                              'default' => 1,
                              'type'    => 'checkbox'
                         ),
                         array(
                              'id'      => 'search_box',
                              'label'   => 'Enable Search Box',
                              'default' => 1,
                              'type'    => 'checkbox'
                         ),
                         array(
                              'id'      => 'menu_alignment',
                              'label'   => 'Menu Alignment',
                              'default' => 'right',
                              'type'    => 'select',
                              'choices' => array('right'=>'Right', 'center'=>'Center', 'left'=>'Left')
                         ),
                         array(
                              'id'      => 'header_transparent',
                              'label'   => 'Header Transparent (removes top bar)',
                              'default' => 0,
                              'type'    => 'checkbox',
                         ),
                         array(
                              'id'      => 'header-transparent-opacity',
                              'label'   => 'Header Opacity',
                              'default' => (int)getLessValue('header-transparent-opacity').'%',
                              'type'    => 'select',
                              'choices' => $transparent_percent
                         )
                    )
               ),
               array(
                    'id'           => 'title_options',
                    'title'        => 'Theme: Title Area Option',
                    'description'  => 'You can control page title section here',
                    'items'        => array(
                         array(
                              'id'      => 'title-padding',
                              'label'   => 'Title Area Space',
                              'default' => getLessValue('title-padding'),
                              'type'    => 'text'
                         ),
                         array(
                              'id'      => 'title-bg-image',
                              'label'   => 'Background Image',
                              'default' => '',
                              'type'    => 'image'
                         ),
                         array(
                              'id'      => 'title-bg-repeat',
                              'label'   => 'Background Repeat',
                              'default' => 'no-repeat',
                              'type'    => 'select',
                              'choices' => array(
                                   'no-repeat' => 'No Repeat',
                                   'repeat' => 'Tile',
                                   'repeat-x' => 'Tile Horizontally',
                                   'repeat-y' => 'Tile Vertically'
                               )
                         ),
                         array(
                              'id'      => 'title-bg-position',
                              'label'   => 'Background Position (x,y)',
                              'default' => 'left top',
                              'type'    => 'select',
                              'choices' => array(
                                   'left top' => 'Left Top',
                                   'left center' => 'Left Middle',
                                   'left bottom' => 'Left Bottom',
                                   'center top' => 'Center Top',
                                   'center center' => 'Center Middle',
                                   'center bottom' => 'Center Bottom',
                                   'right top' => 'Right Top',
                                   'right center' => 'Right Middle',
                                   'right bottom' => 'Right Bottom'
                               )
                         ),
                         array(
                              'id'      => 'title-bg-attach',
                              'label'   => 'Background Attachment',
                              'default' => 'scroll',
                              'type'    => 'select',
                              'choices' => array(
                                   'scroll' => 'Scroll',
                                   'fixed' => 'Fixed'
                               )
                         )
                    )
               ),
               array(
                    'id'           => 'color_general',
                    'title'        => 'Theme: Color Options',
                    'description'  => '',
                    'items'        => array(
                         array(
                              'id'      => 'brand-primary',
                              'label'   => 'Primary Color',
                              'default' => getLessValue('brand-primary')
                         ),
                         array(
                              'id'      => 'text-color',
                              'label'   => 'Text Color',
                              'default' => getLessValue('text-color')
                         ),
                         array(
                              'id'      => 'body-bg',
                              'label'   => 'Body Background Color (for boxed)',
                              'default' => getLessValue('body-bg')
                         ),
                         array(
                              'id'      => 'title-background',
                              'label'   => 'Title Background Color',
                              'default' => getLessValue('title-background')
                         ),
                         array(
                              'id'      => 'primary-background',
                              'label'   => 'Content Background Color',
                              'default' => getLessValue('primary-background')
                         ),
                         array(
                              'id'      => 'top-bar-background',
                              'label'   => 'Top Bar Background Color',
                              'default' => getLessValue('top-bar-background')
                         ),
                         array(
                              'id'      => 'header-background',
                              'label'   => 'Header Background Color',
                              'default' => getLessValue('header-background')
                         ),
                         array(
                              'id'      => 'footer-background',
                              'label'   => 'Footer Background Color',
                              'default' => getLessValue('footer-background')
                         ),

                         array(
                              'id'      => 'brand-success',
                              'label'   => 'Brand Success',
                              'default' => getLessValue('brand-success')
                         ),
                         array(
                              'id'      => 'brand-info',
                              'label'   => 'Brand Info',
                              'default' => getLessValue('brand-info')
                         ),
                         array(
                              'id'      => 'brand-warning',
                              'label'   => 'Brand Warning',
                              'default' => getLessValue('brand-warning')
                         ),
                         array(
                              'id'      => 'brand-danger',
                              'label'   => 'Brand Danger',
                              'default' => getLessValue('brand-danger')
                         )
                    )
               ),
               array(
                    'id'           => 'font_options',
                    'title'        => 'Theme : Font Options',
                    'description'  => '',
                    'items'        => array(
                         array(
                              'id'      => 'base-font-body',
                              'label'   => 'General Font Family',
                              'default' => getLessValue('base-font-body'),
                              'type'    => 'select',
                              'choices' => $google_webfonts
                         ),
                         array(
                              'id'      => 'font-size-base',
                              'label'   => 'General Font Size',
                              'default' => getLessValue('font-size-base'),
                              'type'    => 'select',
                              'choices' => $font_sizes
                         ),
                         array(
                              'id'      => 'font-weight-base',
                              'label'   => 'General Font Weight',
                              'default' => getLessValue('font-weight-base'),
                              'type'    => 'select',
                              'choices' => array(
                                   '100' => '100',
                                   '200' => '200',
                                   '300' => '300',
                                   '400' => '400',
                                   '500' => '500',
                                   '600' => '600',
                                   '700' => '700'
                              )
                         ),
                         // Menu Font Options
                         array(
                              'id'      => 'base-font-menu',
                              'label'   => 'Menu Font Family',
                              'default' => getLessValue('base-font-menu'),
                              'type'    => 'select',
                              'choices' => $google_webfonts
                         ),
                         array(
                              'id'      => 'menu-font-size',
                              'label'   => 'Menu Font Size',
                              'default' => getLessValue('menu-font-size'),
                              'type'    => 'select',
                              'choices' => $font_sizes
                         ),

                         // Topbar font size
                         array(
                              'id'      => 'top-bar-font-size',
                              'label'   => 'Topbar Font Size',
                              'default' => getLessValue('top-bar-font-size'),
                              'type'    => 'select',
                              'choices' => $font_sizes
                         ),
                         // Heading Font Options
                         array(
                              'id'      => 'base-font-heading',
                              'label'   => 'Heading Font Family',
                              'default' => getLessValue('base-font-heading'),
                              'type'    => 'select',
                              'choices' => $google_webfonts
                         ),
                         array(
                              'id'      => 'font-size-h1',
                              'label'   => 'H1 Size',
                              'default' => (int)((int)getLessValue('font-size-base')*2.25+1) . 'px',
                              'type'    => 'select',
                              'choices' => $font_sizes
                         ),
                         array(
                              'id'      => 'font-size-h2',
                              'label'   => 'H2 Size',
                              'default' => (int)((int)getLessValue('font-size-base')*1.75+1) . 'px',
                              'type'    => 'select',
                              'choices' => $font_sizes
                         ),
                         array(
                              'id'      => 'font-size-h3',
                              'label'   => 'H3 Size',
                              'default' => (int)((int)getLessValue('font-size-base')*1.5+1) . 'px',
                              'type'    => 'select',
                              'choices' => $font_sizes
                         ),
                         array(
                              'id'      => 'font-size-h4',
                              'label'   => 'H4 Size',
                              'default' => (int)((int)getLessValue('font-size-base')*1.25+1) . 'px',
                              'type'    => 'select',
                              'choices' => $font_sizes
                         ),
                         array(
                              'id'      => 'font-size-h5',
                              'label'   => 'H5 Size',
                              'default' => getLessValue('font-size-base'),
                              'type'    => 'select',
                              'choices' => $font_sizes
                         ),
                         array(
                              'id'      => 'font-size-h6',
                              'label'   => 'H6 Size',
                              'default' => (int)((int)getLessValue('font-size-base')*0.85+1) . 'px',
                              'type'    => 'select',
                              'choices' => $font_sizes
                         )
                    )
               )
        );
     }

     public static function register($wp_customize) {

        ThemetonTheme_Customize::init_data();
        global $color_options;

        $priority = 300;
        foreach ($color_options as $color_option) {
            
            /* Create Section */
            $wp_customize->add_section( $color_option['id'], array(
                'title' => $color_option['title'],
                'priority' => $priority,
                'description' => isset($color_option['description'])?$color_option['description']:''
            ));

            /* Create Items */
            $items = $color_option['items'];
            $order = 0;
            foreach ($items as $item) {
                $wp_customize->add_setting($item['id'], array(
                    'default' => $item['default'],
                    'type' => 'theme_mod',
                    'transport' => 'postMessage',
                    'capability' => 'edit_theme_options'
                ));

                if( isset($item['type']) ){
                    if( $item['type']=='image' ){
                        $wp_customize->add_control(
                            new WP_Customize_Image_Control( $wp_customize, $item['id'],
                               array(
                                   'label'      => $item['label'],
                                   'section'    => $color_option['id'],
                                   'settings'   => $item['id'],
                                   'priority' => $order
                               )
                            )
                       );
                    }
                    else{
                        $choices = isset($item['choices']) ? $item['choices'] : array();
                        $wp_customize->add_control( $item['id'], array(
                            'label'    => $item['label'],
                            'section'  => $color_option['id'],
                            'settings' => $item['id'],
                            'type'     => $item['type'],
                            'choices'    => $choices,
                            'priority' => $order
                       ));
                    }
                }
                else{
                    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $item['id'], array(
                         'label' => $item['label'],
                         'section' => $color_option['id'],
                         'settings' => $item['id'],
                         'priority' => $order
                     )));
                }

                $order++;
            }

            unset($order);
            $priority++;
        }
    }

    public static function header_output() {

    global $smof_data;

    echo '<!--Customizer CSS--> 
        <style type="text/css">';


    /* Body margin on Attached layout */
    $general_margin_top = get_theme_mod('general-layout')=='boxed' ? (int)get_theme_mod('general-top-space') : 0;
    $general_margin_bottom = get_theme_mod('general-layout')=='boxed' ? (int)get_theme_mod('general-bottom-space') : 0;
    if( $general_margin_top>0 ){
     echo "@media only screen and (min-width: 1200px) {.layout-wrapper{margin-top:" . $general_margin_top . "px}";
     echo ".admin-bar .layout-wrapper{margin-top:" . ($general_margin_top+32) . "px}\n";
    }
    if( $general_margin_bottom>0 ){
     echo "@media only screen and (min-width: 1200px) {.layout-wrapper{margin-bottom:" . $general_margin_bottom . "px}";
    }

    // Body Background Image Option
    $style = '';
    $bg_image = get_theme_mod('general-bg-image');
    if ( $bg_image != "") {
        $bg_image = str_replace('[site_url]', site_url(), $bg_image);
        $style .= "background-image:url('" . $bg_image . "');";
        $style .= "background-repeat:" . get_theme_mod('general-bg-repeat') . ";";
        $style .= "background-position:" . get_theme_mod('general-bg-position') . ";";
        $style .= "background-attachment:" . get_theme_mod('general-bg-attach') . ";";
     }
    echo "body{ $style }\n";


    /* STYLES OPTIONS */
    $title_bg = '';

    $title_bg_image = get_theme_mod('title-bg-image');
    if ($title_bg_image != '') {
    //if (isset($smof_data['title_bg_image']) && $smof_data['title_bg_image'] != "") {
        $title_bg_image = str_replace('[site_url]', site_url(), $title_bg_image);
        $title_bg = "background-image:url('" . $title_bg_image . "');";
        $title_bg .= "background-repeat:" . get_theme_mod('title-bg-repeat') . ";";
        $title_bg .= "background-position:" . get_theme_mod('title-bg-position') . ";";
        $title_bg .= "background-attachment:" . get_theme_mod('title-bg-attach') . ";";
    }
    echo ".page-title.section{ $title_bg }";

     /* Retina logo*/
     $retina_logo = get_theme_mod('logo_retina');
     if( !empty($retina_logo) ){
          echo '@media only screen and (-webkit-min-device-pixel-ratio: 1.3), only screen and (-o-min-device-pixel-ratio: 13/10), only screen and (min-resolution: 120dpi) {
               .logo .normal{display:none !important;}
               .logo .retina{display:inline !important;}
          }';
     }

    /* Footer */
    if (isset($smof_data['footer_bg_image']) && $smof_data['footer_bg_image'] != "") {
        echo "#footer{background-image:url('" . $smof_data['footer_bg_image'] . "');";
        echo "background-repeat:" . $smof_data['footer_bg_repeat'] . ";";
        echo "background-position:" . $smof_data['footer_bg_position'] . ";";
        echo "background-attachment:" . $smof_data['footer_bg_fixed'] . ";}\n";
    }

    
    /* Hides heart/like from blog post and widget */
    if(isset($smof_data['remove_heart']) && $smof_data['remove_heart'] == 1) {
        echo ".meta_like, .meta-like {display:none!important}";
    }
    /* CUSTOM STYLES */
    if (isset($smof_data['custom_css']) && $smof_data['custom_css'] != '')
        echo $smof_data['custom_css'] . "\n";
    if (isset($smof_data['tablet_css']) && $smof_data['tablet_css'] != '') {
        echo "@media (min-width: 768px) and (max-width: 985px) {";
        echo $smof_data['tablet_css'];
        echo "}\n";
    }
    if (isset($smof_data['wide_phone_css']) && $smof_data['wide_phone_css'] != '') {
        echo "@media (min-width: 480px) and (max-width: 767px) {";
        echo $smof_data['wide_phone_css'];
        echo "}\n";
    }
    if (isset($smof_data['phone_css']) && $smof_data['phone_css'] != '') {
        echo "@media (max-width: 479px) {";
        echo $smof_data['phone_css'];
        echo "}\n";
    }

    ?>

          </style>
        <!-- /Theme Options Panel -->
        <?php
    }

    public static function live_preview() {
        wp_enqueue_script(
                'themeton-themecustomizer', // Give the script a unique ID
                get_template_directory_uri() . '/framework/admin-assets/color-options.js', array('jquery', 'customize-preview'), // Define dependencies
                '', // Define a version (optional) 
                true // Specify whether to put in footer (leave this true)
        );

        add_action('wp_head', array('ThemetonTheme_Customize', 'customizer_custom_admin_head'));
    }

    public static function customizer_custom_admin_head(){
          echo '<link rel="stylesheet/less" type="text/css" href="'.get_stylesheet_directory_uri().'/assets/less/style.less" />
               <script>
               less = {
               env: "development",
               async: false,
               fileAsync: false,
               poll: 1000,
               functions: {},
               dumpLineNumbers: "comments",
               relativeUrls: false,
               rootpath: ":/a.com/"
               };
               </script>
               <script src="'.get_template_directory_uri().'/assets/plugins/less-1.7.0.min.js"></script>';
    }

    public static function generate_css($selector, $style, $mod_name, $prefix = '', $postfix = '', $echo = true) {
        $return = '';
        $mod = get_theme_mod($mod_name);
        if (!empty($mod)) {
            $return = sprintf("%s{%s:%s;}", $selector, $style, $prefix . $mod . $postfix);
            if ($echo) {
                echo $return;
            }
        }
        return $return;
    }

}

// Setup the Theme Customizer settings and controls...
add_action('customize_register', array('ThemetonTheme_Customize', 'register'));

// Output custom CSS to live site
add_action('wp_head', array('ThemetonTheme_Customize', 'header_output'));

// Enqueue live preview javascript in Theme Customizer admin screen
add_action('customize_preview_init', array('ThemetonTheme_Customize', 'live_preview'));




add_action('customize_save_after', 'themeton_customize_save_after');
function themeton_customize_save_after(){

    ThemetonTheme_Customize::init_data();
    global $color_options;
    global $smof_data;
    
    $less_vals = array();
    foreach ($color_options as $color_option){
        $items = $color_option['items'];
        foreach ($items as $item){
            $less_vals = array_merge($less_vals, array( $item['id']=>get_theme_mod($item['id']) ));
        }
    }

    $theme_less_vars = get_theme_options_less_vars();
    foreach ($less_vals as $key => $value) {
        if( isset($theme_less_vars[$key]) && $value!='' ){
            $theme_less_vars[$key] = $value;
        }
    }
    
    // Save LESS Variables
    $encoded_str = base64_encode(serialize($theme_less_vars));
    set_theme_mod( "less_theme_variables", $encoded_str );

    /* Build CSS */
    build_main_less_to_css($theme_less_vars);
}


function reset_theme_color_options_handler(){
    $less_vars = get_less_variables_primary();
    
    ThemetonTheme_Customize::init_data();
    global $color_options;
    foreach ($color_options as $color_option){
        $items = $color_option['items'];
        foreach ($items as $item){
            if( isset($less_vars[$item['id']]) ){
                set_theme_mod($item['id'], $less_vars[$item['id']]);
            }
        }
    }
    
    $encoded_str = base64_encode(serialize($less_vars));
    set_theme_mod( "less_theme_variables", $encoded_str );
    set_theme_mod( "less_editor", get_less_editor_content(array()) );
}



?>