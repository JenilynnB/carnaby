<?php


/**
 * Globals
 */
define('THEMEDOC', 'http://docs.themeton.com/ammo');
define('SUPPORFORUM', 'http://themeton.com/support');
define('THEME_NOIMAGE', get_template_directory_uri().'/assets/images/noimage.png');

if (!function_exists('file_require')) {

    /*
     * The function allows us to include deep directory PHP files if they exist in child theme path.
     * Otherwise it works just regularly include main theme files.
     */

    function file_require($file, $uri = false) {
        if (is_child_theme()) {
            if (!$uri) {
                $dir = get_template_directory();
                $replace = get_stylesheet_directory();
                $file_exist = str_replace($dir, $replace, $file);
            } else {
                $dir = get_template_directory_uri();
                $replace = get_stylesheet_directory_uri();
                $file_exist = str_replace(get_template_directory_uri(), get_stylesheet_directory(), $file);
            }

            if (file_exists($file_exist)) {
                $file_child = str_replace($dir, $replace, $file);
                return $file_child;
            } else {
                return $file;
            }
        } else {
            return $file;
        }
    }
}


/* Theme Setup
====================================*/
function themeton_theme_setup(){
    add_editor_style('editor-style.css');
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');

    register_nav_menus(array(
        'primary' => __('Primary Navigation', 'themeton'),
        'top_bar-menu' => __('Top Bar Navigation', 'themeton'),
        'mobile-menu' => __('Mobile Navigation (optional)', 'themeton'),
        'footer_bar-menu' => __('Footer Navigation', 'themeton')
    ));
    load_theme_textdomain('themeton', get_template_directory() . '/languages/');

}
add_action( 'after_setup_theme', 'themeton_theme_setup' );


if (!isset($content_width)){
	$content_width = 960;
}



/* Import Dummy Data
====================================*/
require_once file_require(get_template_directory() . '/framework/addons/wordpress-importer/config.php');
/*
=================================================================
    Admin Panel - Options Framework
=================================================================
*/
require_once file_require(get_template_directory() . '/framework/addons/lessphp/index.php');
require_once file_require(get_template_directory() . '/framework/admin-panel/index.php');


// Output Google Fonts
function import_google_fonts(){
    global $post;
    global $smof_data;
    $fonts = array();

    // Load them option fonts
    $font_options = array('base-font-body', 'base-font-menu', 'base-font-heading');
    $theme_less_vars = get_theme_options_less_vars();
    foreach ($font_options as $option){
        $font = isset($theme_less_vars[$option]) ? trim($theme_less_vars[$option]) : '';
        if( $font!='' && $font!='default' ){
            $fonts[] = $font;
        }
    }

    // load ultimate page fonts
    if( isset($post->ID) && get_post_meta( $post->ID, '_wp_page_template', true )=='page-ultimate.php' ){
        $up_fonts = array('up_menu_font', 'up_heading_font', 'up_body_font');
        foreach ($up_fonts as $upfont) {
            $font = trim(tt_getmeta($upfont, $post->ID));
            if( $font!='' && $font!='default' ){
                $fonts[] = $font;
            }
        }
    }

    $protocol = is_ssl() ? 'https' : 'http';
    include_once file_require(ADMIN_PATH . 'functions/google-fonts.php');
    $google_fonts = get_google_webfonts();

    foreach ($fonts as $font) {
        $var = str_replace("'", '', trim($font));
        $var = str_replace('"', '', $var);
        $var = str_replace(' ', '-', $var);

        $val = str_replace("'", '', trim($font));
        $val = str_replace('"', '', $val);
        $val = str_replace(' ', '+', $val);

        $variants = $subsets = '';
        foreach($google_fonts as $key=>$current) {
            if( trim($current['family']) == trim($font) ) {
                $variants = implode(',',$current['variants']);
                $subsets = '&subset='. implode(',',$current['subsets']);
                break;
            }
        }
        wp_enqueue_style("themeton-google-font-".strtolower($var), "$protocol://fonts.googleapis.com/css?family=" . $val . ":".$variants . $subsets);

    }

}

/*
=================================================================
    Start Registering Scripts
=================================================================
*/
add_action('wp_enqueue_scripts', 'themeton_enqueue_scripts');
function themeton_enqueue_scripts() {

    /* jQuery */
    wp_enqueue_script('jquery');
    

    global $smof_data;

    /* Google Fonts */
    import_google_fonts();
    
    /* Fontawesome */
    wp_enqueue_style('font-awesome',            get_template_directory_uri().'/assets/plugins/font-awesome/css/font-awesome.min.css');

    /* Simple Line Icons */
    wp_enqueue_style('simple-line-icons',       get_template_directory_uri().'/assets/plugins/simple-line-icons/simple-line-icons.css');
    
    /* Bootstrap */
    wp_enqueue_style('bootstrap',  get_template_directory_uri().'/assets/plugins/bootstrap/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/assets/plugins/bootstrap/js/bootstrap.min.js', false, false, true);

    /* Device-Mockups */
    wp_enqueue_style( 'device-mockups', get_template_directory_uri() . '/assets/plugins/device-mockups/device-mockups.css' );

    /* Pretty Photo */
    wp_enqueue_style( 'prettyPhoto', get_template_directory_uri() . '/assets/plugins/prettyPhoto/css/prettyPhoto.css' );
    wp_enqueue_script('prettyPhoto', get_template_directory_uri() . '/assets/plugins/prettyPhoto/js/jquery.prettyPhoto.js', false, false, true);

    /* Swiper */
    wp_enqueue_style( 'swiper', get_template_directory_uri() . '/assets/plugins/swiper/idangerous.swiper.css' );
    wp_enqueue_script('swiper', get_template_directory_uri() . '/assets/plugins/swiper/idangerous.swiper.js', false, false, true);

    /* Theme CSS */
    wp_enqueue_style( 'animate', get_template_directory_uri() . '/assets/css/animate.css' );
    wp_enqueue_style( 'theme-default-styles', get_stylesheet_uri() );
    
    wp_enqueue_style( 'theme-style', get_less_to_css_path() );
    
    /* Ultimate Page Custom LESS */
    global $post;
    if( isset($post->ID) && get_post_meta( $post->ID, '_wp_page_template', true )=='page-ultimate.php' ){
        $ultimate_css = get_ultimate_css_url();
        if( $ultimate_css!==false ){
            wp_enqueue_style( 'theme-ultimate', $ultimate_css );
        }
    }
    
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script( 'comment-reply' );
    }

    /* Theme JS */
    wp_enqueue_script('waypoint', get_template_directory_uri() . '/assets/plugins/waypoints.min.js', false, false, true);
    wp_enqueue_script('stellar', get_template_directory_uri() . '/assets/plugins/stellar.js/jquery.stellar.min.js', false, false, true);
    wp_enqueue_script('scrollto', get_template_directory_uri() . '/assets/plugins/jquery.scrollto.min.js', false, false, true);
    wp_enqueue_script('localscroll', get_template_directory_uri() . '/assets/plugins/jquery.localscroll.min.js', false, false, true);
    wp_enqueue_script('isotope', get_template_directory_uri() . '/assets/plugins/isotope.pkgd.min.js', false, false, true);
    wp_enqueue_script('fitvideo', get_template_directory_uri() . '/assets/plugins/jquery.fitvids.js', false, false, true);
    wp_enqueue_script('jcycle2', get_template_directory_uri() . '/assets/plugins/jquery.cycle2.min.js', false, false, true);
    wp_enqueue_script('jplayer', get_template_directory_uri() . '/assets/plugins/jplayer/jquery.jplayer.min.js', false, false, true);
    wp_enqueue_script('counterup', get_template_directory_uri() . '/assets/plugins/jquery.counterup.min.js', false, false, true);
    wp_enqueue_script('themeton-menu', get_template_directory_uri() . '/assets/js/themeton.menu.js', false, false, true);
    wp_enqueue_script('theme-script', get_template_directory_uri() . '/assets/js/scripts.js', false, false, true);

}



/*
=================================================================
    Start Defining Sidebars
=================================================================
*/
global $smof_data;
$tt_sidebars = array();
if (isset($smof_data['custom_sidebar']) && is_array($smof_data['custom_sidebar'])) {
    foreach ($smof_data['custom_sidebar'] as $sidebar) {
        if ($sidebar['title'] != '') {
            $tt_sidebars[$sidebar['title']] = $sidebar['title'];
        }
    }
}
$tt_sidebars = array_merge(array(
    'post-sidebar'=> 'Post Sidebar Area',
    'page-sidebar'=> 'Page Sidebar Area',
    'blog-sidebar'=> 'Blog Sidebar Area',
    'portfolio-sidebar'=> 'Portfolio Sidebar Area',
    'businessdirectory-sidebar'=> 'Business Directory Sidebar Area',
    'woocommerce-sidebar'=> 'Woocommerce Sidebar Area'
        ), $tt_sidebars);

function theme_widgets_init() {
    /**
     * Registering custom sidebars 
     */
    global $tt_sidebars;
    if(isset($tt_sidebars)) {
        foreach ($tt_sidebars as $id => $sidebar) {
            if ($id != '') {
                register_sidebar(array(
                    'name' => $sidebar,
                    'id' => $id,
                    'description' => $sidebar,
                    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
                    'after_widget' => '</aside>',
                    'before_title' => '<h3 class="widget-title">',
                    'after_title' => '</h3>'
                ));                
            }
        }
    }

    /**
     * Registering Footer sidebars 
     */
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name' => 'Footer Sidebar Area ' . $i,
            'id' => 'sidebar_metro_footer' . $i,
            'description' => 'Footer Sidebar Area ' . $i,
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget' => '</aside>',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>'
        ));
    }
}

add_action('widgets_init', 'theme_widgets_init');

/* Allowing shortcode execution for text widget */
add_filter('widget_text', 'do_shortcode');


/*
=================================================================
    End of Defining Sidebars
=================================================================
*/




/* * ***********************     */
/* Theme part file including     */
/* * ***********************     */
require_once file_require(get_template_directory() . '/framework/common-functions.php');
require_once file_require(get_template_directory() . '/framework/Pagebuilder/index.php');
require_once file_require(get_template_directory() . '/framework/mega-menu/mega-menu.php');
require_once file_require(get_template_directory() . '/framework/widgets/init_widget.php');
require_once file_require(get_template_directory() . '/framework/color-options.php');

require_once file_require(get_template_directory() . '/framework/addons/woocommerce/config.php');


/* TGM Plugin Activation */
require_once file_require(get_template_directory() . '/framework/addons/tgmplugin/plugin-install.php');


/* * ***********************       */
/* Metaboxes of page and post      */
/* * ***********************       */
$tt_post_meta = array();
require_once file_require(get_template_directory() . '/framework/post-type/post-options.php');
require_once file_require(get_template_directory() . '/framework/post-type/page-options.php');
require_once file_require(get_template_directory() . '/framework/post-type/portfolio-options.php');
require_once file_require(get_template_directory() . '/framework/post-type/renderer.php');




/* Check Slider Selected
==============================*/
function getPageSlider($onTop){
    global $post;
    if( tt_getmeta('slider_top')!='1' && $onTop ){ return false; }
    if( tt_getmeta('slider_top')=='1' && !$onTop ){ return false; }

    $slider_class = $onTop ? 'slider-fullscreen' : '';

    if (tt_getmeta('slider') != '' && tt_getmeta('slider') != 'none'):
        echo '<div id="tt-slider" class="tt-slider '.$slider_class.'">';
            $slider_name = tt_getmeta("slider");
            $slider = explode("_", $slider_name);
            $shortcode = '';
            if (strpos($slider_name, "layerslider") !== false)
                $shortcode = "[" . $slider[0] . " id='" . $slider[1] . "']";
            elseif (strpos($slider_name, "revslider") !== false)
                $shortcode = "[rev_slider " . $slider[1] . "]";
            echo do_shortcode($shortcode);
        echo '</div>';
    endif;
}



?>