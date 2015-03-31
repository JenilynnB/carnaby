<?php
/**
 * You can extend it with new icons. 
 * Please see the icon list from here, http://fortawesome.github.io/Font-Awesome/cheatsheet/
 * And extend following array with name and hex code.
 */
global $tt_social_icons;
$tt_social_icons = array(
    'facebook' => 'fa fa-facebook',
    'twitter' => 'fa fa-twitter',
    'googleplus' => 'fa fa-google-plus',
    'email' => 'fa fa-envelope',
    'pinterest' => 'fa fa-pinterest',
    'linkedin' => 'fa fa-linkedin',
    'youtube' => 'fa fa-youtube',
    'vimeo' => 'fa fa-vimeo-square',
    'dribbble' => 'fa fa-dribbble',
    'instagram' => 'fa fa-instagram',
    'flickr' => 'fa fa-flickr',
    'skype' => 'fa fa-skype'
);


add_action('admin_enqueue_scripts', 'admin_common_render_scripts');

function admin_common_render_scripts() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('themeton-admin-common-style', get_template_directory_uri() . '/framework/admin-assets/common.css');

    wp_enqueue_script('jquery');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('themeton-admin-common-js', get_template_directory_uri() . '/framework/admin-assets/common.js', false, false, true);
}


/* Validate URL
========================================================*/
function validateURL($url){
    return filter_var($url, FILTER_VALIDATE_URL);

    if(!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)){
        return false;
    }
    return true;
}


/**
 * The function returns brightness value from 0 to 255
 */
function get_brightness($hex) {
    $hex = str_replace('#', '', $hex);

    if (strlen($hex) < 6) {
        $hex = substr($hex, 0, 1) . substr($hex, 0, 1) .
                substr($hex, 1, 2) . substr($hex, 1, 2) .
                substr($hex, 2, 3) . substr($hex, 2, 3);
    }

    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}


function themeton_admin_post_type() {
    global $post, $typenow, $current_screen;

    // Check to see if a post object exists
    if ($post && $post->post_type)
        return $post->post_type;

    // Check if the current type is set
    elseif ($typenow)
        return $typenow;

    // Check to see if the current screen is set
    elseif ($current_screen && $current_screen->post_type)
        return $current_screen->post_type;

    // Finally make a last ditch effort to check the URL query for type
    elseif (isset($_REQUEST['post_type']))
        return sanitize_key($_REQUEST['post_type']);
 
    return '-1';
}

function tt_getmeta($meta, $post_id = NULL) {
    global $post;
    if ($post_id != NULL && (int) $post_id > 0) {
        return get_post_meta($post_id, '_' . $meta, true);
    } else if (isset($post->ID)) {
        return get_post_meta($post->ID, '_' . $meta, true);
    }
    return '';
}


function get_post_like($post_id){
    return '<a href="javascript:;" data-pid="'. $post_id .'" class="'. blox_post_liked($post_id) .'"><i class="fa fa-heart"></i> <span>'. (int)blox_getmeta($post_id, 'post_like') .'</span></a>';
}


function get_external_sliders($type){
    global $wpdb;
    $sliders = array();

    if( $type == 'layerslider' ){
        /* SLIDER VALUES */

        if( class_exists('LS_Sliders') ){
            $layer_sliders = LS_Sliders::find(array('data'=>false));
            foreach ($layer_sliders as $item) {
                $sliders = array_merge($sliders, array("layerslider_" . $item['id'] => "LayerSlider - " . $item['name']));
            }
        }
    }
    else if( $type == 'revslider' ){
        // Revolution slider list
        $table_name_rev = $wpdb->prefix . "revslider_sliders";
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name_rev'") != $table_name_rev) {
            
        } else {
            $rev_sliders = $wpdb->get_results("SELECT id, title, alias FROM $table_name_rev");
            if (!empty($rev_sliders)) {
                foreach ($rev_sliders as $key => $item) {
                    $name = empty($item->title) ? ('Unnamed(' . $item->id . ')') : $item->title;
                    $sliders = array_merge($sliders, array("revslider_" . $item->alias => "Revolution Slider - " . $name));
                }
            }
        }
    }
    else if( $type == 'masterslider' ){
        if( function_exists('get_mastersliders') ){
            $master_sliders = get_mastersliders();
            foreach ($master_sliders as $slider) {
                $sliders = array_merge($sliders, array("masterslider_" . $slider['ID'] => "Master Slider - " . $slider['title']));
            }

        }
    }

    return $sliders;
}


/* Get Site Logo */
function tt_site_logo() {
    global $smof_data;
    $hide = '';
    echo '<div class="logo site-brand">';

    //Normal logo
    $logo = get_theme_mod('logo');
    if ( !empty($logo) ) {
        
        $logo = str_replace('[site_url]', site_url(), $logo);        
        echo "<a href=" . home_url() . "><img src='" . $logo . "' alt='" . get_bloginfo('name') . "' class='normal'/>";

        // Retina logo
        $retina_logo = get_theme_mod('logo_retina');
        if( !empty($retina_logo) ){
            $logo_width = get_theme_mod('logo-width');
            $logo_height = get_theme_mod('logo-height');
            if( !empty($logo_width) && !empty($logo_width) ) {
                $pixels ="";
                if(is_numeric($logo_width) && is_numeric($logo_height) ){
                    $pixels ="px";
                }
                $retina_logo = str_replace('[site_url]', site_url(), $retina_logo);
                echo '<img src="'. $retina_logo.'" alt="'.get_bloginfo('name').'" style="width:'. $logo_width.$pixels.';max-height:'. $logo_height.$pixels.'; height: auto !important" class="retina" />';
            }
        }
        echo "</a>";

        // Hide site title text if logo image is defined
        $hide = "style='display:none'";
    }
    echo "<h1 $hide class='navbar-brand'><a href=" . home_url() . ">" . get_bloginfo('name') . "</a></h1>";
    echo '</div>';
}

/* Get Ultimate Page Logo */
function tt_up_site_logo() {
    global $smof_data;
    $up_logo = tt_getmeta('up_logo');

    if( !empty($up_logo) ){
        echo '<div class="logo site-brand">';
            echo "<a href=" . home_url() . "><img src='$up_logo' alt='" . get_bloginfo('name') . "' class='normal'/></a>";
        echo '</div>';
        return;
    }
    $hide = '';
    echo '<div class="logo site-brand">';
    if ( isset($smof_data['logo']) && $smof_data['logo'] != '') {
        echo "<a href=" . home_url() . "><img src='" . $smof_data['logo'] . "' alt='" . get_bloginfo('name') . "' class='normal'/>";

        // Retina logo
        if(isset($smof_data['logo_retina']) && $smof_data['logo_retina'] !='' ) {
            if(isset($smof_data['logo_retina_width']) && isset($smof_data['logo_retina_height'])) {
                        $pixels ="";
                if(is_numeric($smof_data['logo_retina_width']) && is_numeric($smof_data['logo_retina_height'])){
                    $pixels ="px";
                }
                echo '<img src="'. $smof_data["logo_retina"].'" alt="'.get_bloginfo('name').'" style="width:'. $smof_data["logo_retina_width"].$pixels.';max-height:'. $smof_data["logo_retina_height"].$pixels.'; height: auto !important" class="retina" />';
            }
        }
        echo "</a>";

        // Hide site title text if logo image is defined
        $hide = "style='display:none'";
    }
    echo "<h1 $hide class='navbar-brand'><a href=" . home_url() . ">" . get_bloginfo('name') . "</a></h1>";
    echo '</div>';
}

/*
 * Favicon and Touch Icons
 */

function tt_icons() {
    global $smof_data;

    /*
     * Favicon
     */
    $url = get_template_directory_uri() . "/images/favicon.png";
    $url_custom = get_theme_mod('icon_favicon');
    if ( !empty($url_custom) ) {
        $url = $url_custom;
    }
    $url = str_replace('[site_url]', site_url(), $url);
    echo "<link rel='shortcut icon' href='$url'/>";

    /*
     * Apple Devices Touch Icons
     */
    if (isset($smof_data['icon_iphone']) && $smof_data['icon_iphone'])
        echo '<link rel="apple-touch-icon" href="' . $smof_data['icon_iphone'] . '">';
    if (isset($smof_data['icon_iphone_retina']) && $smof_data['icon_iphone_retina'])
        echo '<link rel="apple-touch-icon" sizes="114x114" href="' . $smof_data['icon_iphone_retina'] . '">';
    if (isset($smof_data['icon_ipad']) && $smof_data['icon_ipad'])
        echo '<link rel="apple-touch-icon" sizes="72x72" href="' . $smof_data['icon_ipad'] . '">';
    if (isset($smof_data['icon_ipad_retina']) && $smof_data['icon_ipad_retina'])
        echo '<link rel="apple-touch-icon" sizes="144x144" href="' . $smof_data['icon_ipad_retina'] . '">';
}

/*
 * Site Tracking Code
 */

function tt_trackingcode() {
    global $smof_data;
    if ( isset($smof_data['site_analytics']) && $smof_data['site_analytics']!='') {
        echo $smof_data['site_analytics'];
    }
}

function add_video_radio($embed) {
    if (strstr($embed, 'http://www.youtube.com/embed/')) {
        return str_replace('?fs=1', '?fs=1&rel=0', $embed);
    } else {
        return $embed;
    }
}

add_filter('oembed_result', 'add_video_radio', 1, true);

if (!function_exists('custom_upload_mimes')) {
    add_filter('upload_mimes', 'custom_upload_mimes');

    function custom_upload_mimes($existing_mimes = array()) {
        $existing_mimes['ico'] = "image/x-icon";
        return $existing_mimes;
    }

}


if (!function_exists('format_class')) {

    // Returns post format class by string
    function format_class($post_id) {
        $format = get_post_format($post_id);
        if ($format === false)
            $format = 'standard';
        return 'format_' . $format;
    }
}


/**
 * Comment Count Number
 * @return html 
 */
function comment_count_text() {
    $comment_count = get_comments_number('0', '1', '%');
    $comment_text = $comment_count . ' ' . __('Comments', 'themeton');
    if( (int)$comment_count == 1 ){
        $comment_text = $comment_count . ' ' . __('Comment', 'themeton');
    }
    else if( (int)$comment_count < 1 ){
        $comment_text = __('No Comment', 'themeton');
    }
    return "<a href='" . get_comments_link() . "' title='" . $comment_text . "'> " . $comment_text . "</a>";
}

function comment_count() {
    $comment_count = get_comments_number('0', '1', '%');
    $comment_trans = '<i class="fa fa-comment"></i> ' . $comment_count;
    return "<a href='" . get_comments_link() . "' title='" . $comment_trans . "'> " . $comment_trans . "</a>";
}

/**
 * Returns Author link
 * @return html
 */
function get_author_posts_link() {
    $output = '';
    ob_start();
    the_author_posts_link();
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}






/**
 * This code filters the Categories archive widget to include the post count inside the link
 */
add_filter('wp_list_categories', 'cat_count_span');

function cat_count_span($links) {
    $links = str_replace('</a> (', ' <span>', $links);
    $links = str_replace('<span class="count">(', '<span>', $links);
    $links = str_replace(')', '</span></a>', $links);
    return $links;
}

/**
 * This code filters the Archive widget to include the post count inside the link
 */
add_filter('get_archives_link', 'archive_count_span');

function archive_count_span($links) {
    $links = str_replace('</a>&nbsp;(', ' <span>', $links);
    $links = str_replace(')</li>', '</span></a></li>', $links);
    return $links;
}

/**
 * Prints social links on top bar & sub footer area
 * @global array $tt_social_icons
 * @param type $footer : Sign of footer layout
 */
function social_links_by_icon($footer = false) {
    global $tt_social_icons, $smof_data;
    $sign = false;
    $pref = 'social_';
    if ($footer)
        $pref = 'footer_' . $pref;
    $result = '<ul class="top-bar-list list-inline">';
    foreach ($tt_social_icons as $key => $hex) {
        if (isset($smof_data[$pref . $key]) && $smof_data[$pref . $key] != '') {
            $url = $smof_data[$pref . $key];
            if ($key != 'email') {
                if (!preg_match_all('!https?://[\S]+!', $url, $matches))
                    $url = "http://" . $url;
            } else {
                $url = 'mailto:' . $url . '?subject=' . get_bloginfo('name') . '&amp;body='.__('Your%20message%20here!', 'themeton');
            }
            $result .= '<li><a class="social-link ' . $key . '" href="' . $url . '" target="_blank"><i class="fa ' . $hex . '"></i></a></li>';
            $sign = true;
        }
    }
    $result .= '</ul>';
    echo $sign ? $result : __('Please add your socials.', 'themeton');
}

/**
 * Prints Top Bar content
 * @param type $type : Menu type
 * @param type $position : Right or Left
 */
function tt_bar_content($bar_content = 'text1', $footer = false) {
    global $smof_data;

    $splitedValues = explode(',', trim($bar_content));

    foreach ($splitedValues as $value) {

        $type = trim($value);

        $pref = 'top_';
        if($footer) {
            $pref = 'footer_';
        }
        
        if ($type == 'social') {
            ob_start();
            social_links_by_icon($footer);
            $result = ob_get_clean();
            echo '<div class="topbar-item">'. $result .'</div>';
        }
        elseif ($type == 'shop') {
            global $woocommerce;
            if (isset($woocommerce->cart)) {
                $cart = $woocommerce->cart;

                // Get mini cart
                ob_start();
                woocommerce_mini_cart();
                $mini_cart = ob_get_clean();

                echo '<div class="woocommerce-shcart woocommerce topbar-item hidden-sm hidden-xs">
                        <div class="shcart-display">
                            <i class="fa fa-shopping-cart"></i>'. __('Cart', 'themeton') .'
                            <span class="total-cart">'. $cart->cart_contents_count .'</span>
                        </div>
                        <div class="shcart-content">
                            <div class="widget_shopping_cart_content">' . $mini_cart . '</div>
                        </div>
                      </div>';
            }
            else{
                echo '<div class="topbar-item">'. __('Please install Woocommerce.', 'themeton') .'</div>';;
            }
        }
        elseif ($type == 'lang') {
            global $wp_filter;
            if( isset($wp_filter['icl_language_selector']) ){
                ob_start();
                do_action('icl_language_selector');
                $result = ob_get_clean();
                echo '<div class="topbar-item">'. $result .'</div>';
            }
            else{
                echo '<div class="topbar-item">'. __('Please install WPML.', 'themeton') .'</div>';;
            }
        }
        elseif ($type == 'menu') {
            ob_start();
            wp_nav_menu(array('theme_location' => $pref.'bar-menu', 'fallback_cb' => '', 'depth'=>1, 'menu_class'=>'list-inline'));
            $result = ob_get_clean();
            echo '<div class="topbar-item">'. $result .'</div>';
        }
        elseif ($type == 'text1' || $type == 'text2') {
            if (isset($smof_data[$pref.'bar_'.$type])) {
                $result = '<span class="bar-text">'. do_shortcode($smof_data[$pref.'bar_'.$type]) .'</span>';
                echo '<div class="topbar-item">'. $result .'</div>';
            }
        }
        else if( $type=='login' ){
            $link = get_edit_user_link();
            $text = __('Login / Register', 'themeton');

            if( function_exists('is_shop') ){
                $link = get_permalink( get_option('woocommerce_myaccount_page_id') );
            }
            else if( !is_user_logged_in() ){
                $link = wp_login_url();
            }

            if( is_user_logged_in() ){
                $text = __('My Account','themeton');
            }
            $result = '<div class="topbar-item login-item">
                            <a href="'. $link .'">'. $text .'</a>
                       </div>';
            echo $result;
        }
    }

}



if (!function_exists('tt_comment_form')) :

    function tt_comment_form($fields) {
        global $id, $post_id;
        if (null === $post_id)
            $post_id = $id;
        else
            $id = $post_id;

        $commenter = wp_get_current_commenter();

        $req = get_option('require_name_email');
        $aria_req = ( $req ? " aria-required='true'" : '' );
        $fields = array(
            'author' => '<p class="comment-form-author">' . '<label for="author">' . __('Name', 'themeton') . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
            '<input placeholder="' . __('Name', 'themeton') . '" id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' /></p>',
            'email' => '<p class="comment-form-email"><label for="email">' . __('Email', 'themeton') . ( $req ? ' <span class="required">*</span>' : '' ) . '</label> ' .
            '<input placeholder="' . __('Email', 'themeton') . '" id="email" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) . '" size="30"' . $aria_req . ' /></p>',
            'url' => '<p class="comment-form-url"><label for="url">' . __('Website', 'themeton') . '</label>' .
            '<input placeholder="' . __('Website', 'themeton') . '" id="url" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" /></p>',
        );
        return $fields;
    }
    add_filter('comment_form_default_fields', 'tt_comment_form');
endif;



if (!function_exists('about_author')) {
    
    function about_author() {
        ?>
        <div class="item-author clearfix">
            <?php
            $author_email = get_the_author_meta('email');
            echo get_avatar($author_email, $size = '60');
            ?>
            <h3><?php _e("Written by ", "themeton"); ?><?php if (is_author()) the_author(); else the_author_posts_link(); ?></h3>
            <div class="author-title-line"></div>
            <p>
                <?php
                $description = get_the_author_meta('description');
                if ($description != '')
                    echo $description;
                else
                    _e('The author didnt add any Information to his profile yet', 'themeton');
                ?>
            </p>
        </div>
        <?php
    }

}

if (!function_exists('social_share')) {

    /**
     * Prints Social Share Options
     * @global array $tt_social_icons
     * @global type $post : Current post
     */
    function social_share() {
        global $smof_data, $tt_social_icons, $post;
        
        echo '<span class="sf_text">' . __('Share', 'themeton') . ': </span>';
        echo '<ul class="post_share list-inline">';
        if (isset($smof_data['share_buttons']['facebook']) && $smof_data['share_buttons']['facebook'] == 1) {
            echo '<li><a href="https://www.facebook.com/sharer/sharer.php?u=' . get_permalink() . '" title="Facebook" target="_blank"><i class="fa ' . $tt_social_icons['facebook'] . '"></i></a></li>';
        }
        if (isset($smof_data['share_buttons']['twitter']) && $smof_data['share_buttons']['twitter'] == 1) {
            echo '<li><a href="https://twitter.com/share?url=' . get_permalink() . '" title="Twitter" target="_blank"><i class="fa ' . $tt_social_icons['twitter'] . '"></i></a></li>';
        }
        if (isset($smof_data['share_buttons']['googleplus']) && $smof_data['share_buttons']['googleplus'] == 1) {
            echo '<li><a href="https://plus.google.com/share?url='.get_permalink().'" title="GooglePlus" target="_blank"><i class="fa ' . $tt_social_icons['googleplus'] . '"></i></a></li>';
        }
        if (isset($smof_data['share_buttons']['pinterest']) && $smof_data['share_buttons']['pinterest'] == 1) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
            echo '<li><a href="//pinterest.com/pin/create/button/?url=' . get_permalink() . '&media=' . $image[0] . '&description=' . get_the_title() . '" title="Pinterest" target="_blank"><i class="fa ' . $tt_social_icons['pinterest'] . '"></i></a></li>';
        }
        if (isset($smof_data['share_buttons']['email']) && $smof_data['share_buttons']['email'] == 1) {
            echo '<li><a href="mailto:?subject=' . get_the_title() . '&body=' . strip_tags(get_the_excerpt()) . get_permalink() . '" title="Email" target="_blank"><i class="fa ' . $tt_social_icons['email'] . '"></i></a></li>';
        }
        echo '</ul>';

    }

}

/**
 * Prints Related Posts
 * @global type $post : Current post
 */
function tt_related_posts( $options=array() ) {
    
    $options = array_merge(array(
                    'per_page'=>'3'
                    ),
                    $options);

    global $post, $smof_data;

    $args = array(
        'post__not_in' => array($post->ID),
        'posts_per_page' => $options['per_page']
    );
    $grid_class = 'col-md-4 col-sm-6 col-xs-12';
    $post_type_class = 'blog';

    $categories = get_the_category($post->ID);
    if ($categories) {
        $category_ids = array();
        foreach ($categories as $individual_category) {
            $category_ids[] = $individual_category->term_id;
        }
        $args['category__in'] = $category_ids;
    }

    // For portfolio post and another than Post
    if($post->post_type != 'post') {
        $tax_name = 'portfolio_entries'; //should change it to dynamic and for any custom post types
        $args['post_type'] =  get_post_type(get_the_ID());
        $args['tax_query'] = array(
            array(
                'taxonomy' => $tax_name,
                'field' => 'id',
                'terms' => wp_get_post_terms($post->ID, $tax_name, array('fields'=>'ids'))
            )
        );
        if( $options['per_page']=='4' ) {
            $grid_class = 'col-md-3 col-sm-6 col-xs-12';
        }
        $post_type_class = 'portfolio';
    }

    if(isset($args)) {
        $my_query = new wp_query($args);
        if ($my_query->have_posts()) {

            $html = '';
            while ($my_query->have_posts()) {
                $my_query->the_post();

                $html .= '<div class="'.$grid_class.' loop-item">
                                <article itemscope="" itemtype="http://schema.org/BlogPosting" class="entry">
                                    '. hover_featured_image(array('overlay'=>'permalink')) .'

                                    <div class="relative">
                                        <div class="entry-title">
                                            <h2 itemprop="headline">
                                                <a itemprop="url" href="'. get_permalink() .'">'.get_the_title().'</a>
                                            </h2>
                                        </div>
                                        <ul class="entry-meta list-inline">
                                            <li itemprop="datePublished" class="meta-date">'. date_i18n(get_option('date_format'), strtotime(get_the_date())) .'</li>
                                            <li class="meta-like">'. get_post_like(get_the_ID()) .'</li>
                                        </ul>
                                    </div>
                                </article>
                            </div>';
            }

            echo '<div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <h3 class="related-posts">' . __('Related Posts', 'themeton') . '</h3>
                        <div class="blox-element related-posts '.$post_type_class.' grid-loop">
                            <div class="row">
                                <div class="loop-container">'. $html .'</div>
                            </div>
                        </div>
                    </div>
                  </div>';
        }
    }
    wp_reset_query();
}

// ADDING ADMIN BAR MENU
if (!function_exists('tt_admin_bar_menu')) {
    add_action('admin_bar_menu', 'tt_admin_bar_menu', 90);

    function tt_admin_bar_menu() {

        if (!current_user_can('manage_options'))
            return;

        global $wp_admin_bar;

        $admin_url = admin_url('admin.php');

        $options = array(
            'id' => 'theme-options',
            'title' => __('Theme Options', 'themeton'),
            'href' => $admin_url . "?page=theme-options",
        );
        $wp_admin_bar->add_menu($options);

        $color = array(
            'id' => 'color-options',
            'title' => __('Site Customize', 'themeton'),
            'href' => admin_url() . "customize.php",
        );
        $wp_admin_bar->add_menu($color);
    }

}


/**
 * Prints Custom Logo Image for Login Page
 */
function custom_login_logo() {
    global $smof_data;
    $logo = get_theme_mod('logo_admin');
    if (!empty($logo)) {
        $logo = str_replace('[site_url]', site_url(), $logo);
        echo '<style type="text/css">.login h1 a { background: url(' . $logo . ') center center no-repeat !important;width: auto !important;}</style>';
    }
}

add_action('login_head', 'custom_login_logo');


/*
 * Random order
 * Preventing duplication of post on paged
 */

function register_tt_session(){
    if( !session_id() ){
        session_start();
    }
}

if(!is_admin() && true) {
    add_action('init', 'register_tt_session');
    //add_filter('posts_orderby', 'edit_posts_orderby');

    function edit_posts_orderby($orderby_statement) {
        if (isset($_SESSION['expiretime'])) {
            if ($_SESSION['expiretime'] < time()) {
                session_unset();
            }
        } else {
            $_SESSION['expiretime'] = time() + 300;
        }

        $seed = rand();
        if (isset($_SESSION['seed'])) {
            $seed = $_SESSION['seed'];
        } else {
            $_SESSION['seed'] = $seed;
        }
        $orderby_statement = 'RAND(' . $seed . ')';
        return $orderby_statement;
    }
}



/* Pager functions
====================================================*/
if (!function_exists('themeton_pager')) :

    function themeton_pager($query = null) {
        global $wp_query;
        $current_query = $query!=null ? $query : $wp_query;
        $pages = (int)$current_query->max_num_pages;
        $paged = get_query_var('paged') ? (int)get_query_var('paged') : 1;
        if (is_front_page()){
            $paged = get_query_var('page') ? (int)get_query_var('page') : $paged;
        }

        if (empty($pages)) {
            $pages = 1;
        }

        if ( $pages!=1 ) {
            if ($paged > 1) {
                $prevlink = get_pagenum_link($paged - 1);
            }
            if ($paged < $pages) {
                $nextlink = get_pagenum_link($paged + 1);
            }


            $big = 9999; // need an unlikely integer
            echo "<div class='row'><div class='col-xs-12 col-sm-12 col-md-12 col-lg-12'><div class='pagination-container clearfix'>";

            $args = array(
                'current' => 0,
                'show_all' => false,
                'prev_next' => true,
                'add_args' => false, // array of query args to add
                'add_fragment' => '',
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'end_size' => 3,
                'mid_size' => 1,
                'format' => '?paged=%#%',
                'current' => max(1, $paged),
                'total' => $current_query->max_num_pages,
                'type' => 'list',
                'prev_text' => '<i class="icon-arrow-left"></i>',
                'next_text' => '<i class="icon-arrow-right"></i>',
            );

            extract($args, EXTR_SKIP);

            // Who knows what else people pass in $args
            $total = (int) $total;
            if ($total < 2)
                return;
            $current = (int) $current;
            $end_size = 0 < (int) $end_size ? (int) $end_size : 1; // Out of bounds?  Make it the default.
            $mid_size = 0 <= (int) $mid_size ? (int) $mid_size : 2;
            $add_args = is_array($add_args) ? $add_args : false;
            $r = '';
            $page_links = array();
            $next_link = '<li class="disabled"><a href="#">'. __('Prev', 'themeton') .'</a></li>';
            $prev_link = '<li class="disabled"><a href="#">'. __('Next', 'themeton') .'</a></li>';
            $n = 0;
            $dots = false;

            // Next link
            if ($prev_next && $current && 1 < $current) :
                $link = str_replace('%_%', 2 == $current ? '' : $format, $base);
                $link = str_replace('%#%', $current - 1, $link);
                if ($add_args)
                    $link = add_query_arg($add_args, $link);
                $link .= $add_fragment;
                $next_link = '<li><a href="'. esc_url(apply_filters('paginate_links', $link)) .'">'. __('Prev', 'themeton') .'</a></li>';
            endif;

            // Pager links
            for ($n = 1; $n <= $total; $n++) :
                $n_display = number_format_i18n($n);
                if ($n == $current) :
                    $page_links[] = "<li class='active'><a href='#'>$n_display <span class='sr-only'>(current)</span></a></li>";
                    $dots = true;
                else :
                    if ($show_all || ( $n <= $end_size || ( $current && $n >= $current - $mid_size && $n <= $current + $mid_size ) || $n > $total - $end_size )) :
                        $link = str_replace('%_%', 1 == $n ? '' : $format, $base);
                        $link = str_replace('%#%', $n, $link);
                        if ($add_args)
                            $link = add_query_arg($add_args, $link);
                        $link .= $add_fragment;
                        $page_links[] = "<li><a href='" . esc_url(apply_filters('paginate_links', $link)) . "'>$n_display</a></li>";
                        $dots = true;
                    elseif ($dots && !$show_all) :
                        $page_links[] = '<li><span class="page-numbers dots">&hellip;</span></li>';
                        $dots = false;
                    endif;
                endif;
            endfor;

            // Prev links
            if ($prev_next && $current && ( $current < $total || -1 == $total )) :
                $link = str_replace('%_%', $format, $base);
                $link = str_replace('%#%', $current + 1, $link);
                if ($add_args)
                    $link = add_query_arg($add_args, $link);
                $link .= $add_fragment;
                $prev_link = '<li><a href="'. esc_url(apply_filters('paginate_links', $link)) .'">'. __('Next', 'themeton') .'</a></li>';
            endif;

            $r .= "<ul class='pagination pull-left'>";
            $r .= join("\n\t", $page_links);
            $r .= "</ul>\n";
            $r .= '<ul class="pagination pull-right">
                    '. $next_link .'
                    '. $prev_link .'
                 </ul>
                 <div class="clearfix"></div>';
            echo $r;
            echo "</div></div></div>";
        }
    }

endif;



if ( ! function_exists( 'themeton_theme_comment' ) ) :
	
function themeton_theme_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'themeton' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'themeton' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'themeton' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'themeton' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'themeton' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'themeton' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'themeton' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif;

// Search form customizing

function tt_search_form( $form ) {
    $form = '<div class="search-form">
                <form method="get" id="searchform" action="'.esc_url( home_url( '/' ) ).'">
                    <div class="input-group">
                        <input type="text" class="form-control" name="s" placeholder="'. __('What are you shopping for?', 'themeton'). '">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">'. __('Search!', 'themeton'). '</button>
                        </span>
                    </div>
                </form>
            </div>';

    return $form;
}
function tt_product_search_form( $form ) {
    $form = '<div class="search-form">
                <form method="get" id="searchform" action="'.esc_url( home_url( '/' ) ).'">
                    <div class="input-group">
                        <input type="text" class="form-control" name="s" placeholder="'. __('Search for products ...', 'themeton'). '">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">'. __('Go!', 'themeton'). '</button>
                        </span>
                    </div>
                    <input type="hidden" name="post_type" value="product" />
                </form>
            </div>';

    return $form;
}

add_filter( 'get_search_form', 'tt_search_form' );
add_filter( 'get_product_search_form', 'tt_product_search_form' );


add_filter( 'wp_nav_menu_items', 'add_user_menu', 10, 2);
function add_user_menu($items, $args)
{
    
    if($args->theme_location == 'primary'){
        $items  = '';
        
        $blog_url = site_url('/your-shopping-guide');
        $items .= '<li class="menu-item">';
        $items .= '<a href="'.$blog_url.'" class="highlight"><span class="menu-text" >Shopping Guide</span></a>';
        $items .= '</li>';
            
        
        if(is_user_logged_in())
        {
            global $xoouserultra;   
            $user=wp_get_current_user();
            //$name=$user->display_name; // or user_login , user_firstname, user_lastname

            $user_last_name = $user->last_name;
            $user_last_initial = substr($user_last_name,0,1);
            $name=$user->first_name." ".$user_last_initial.".";

            $user_dashboard_url = site_url("/dashboard");
            $user_profile_url = site_url("/profile");
            $user_account_url = site_url("/myaccount");
            $user_friends_url = site_url("dashboard/?module=friends");
            $user_messages_url = site_url("dashboard/?module=messages");
            $logout_url = site_url("/logout");

            //$user_photo = get_avatar($user->ID, 40);
            $user_photo = $xoouserultra->userpanel->get_user_pic_url( $user->ID, 40, "avatar");
            $unread_messages_count = $xoouserultra->mymessage->get_unread_messages_amount($user->ID);
            
            $items .= '<li class="menu-item has-children" id="menu-item-user-name">';
            
            $items .=    '<a class="menu-item-user-name-link">';
            $items .=    '<div class="menu-user-photo"><img class="user-avatar-rounded user-avatar-mini" src="'.$user_photo.'"></div>';
            $items .=    '<span class="menu-text" id="user-name-menu">';
            $items .=    $name.'</span></a>';
            $items .=       '<ul class="dropdown-menu" style="display:none;">';
            $items .=           '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1140">';
            $items .=               '<a href="'.$user_dashboard_url.'"><span class="menu-text">Dashboard</span></a>';
            $items .=           '</li>';
            $items .=           '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1140">';
            $items .=               '<a href="'.$user_profile_url.'"><span class="menu-text">My Profile</span></a>';
            $items .=           '</li>';
            $items .=           '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1140">';
            $items .=               '<a href="'.$user_messages_url.'"><span class="menu-text">My Messages</span>';
            if($unread_messages_count>0){
            $items .=               '<div class="uultra-noti-bubble" title="'.__('Unread Messages', 'xoousers').'">'.$unread_messages_count.'</div></a>';
            }
            $items .=           '</li>';
            $items .=           '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1140">';
            $items .=               '<a href="'.$user_friends_url.'"><span class="menu-text">My Friends</span></a>';
            $items .=           '</li>';
            $items .=           '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1140">';
            $items .=               '<a href="'.$user_account_url.'"><span class="menu-text">My Account</span></a>';
            $items .=           '</li>';
            $items .=           '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1140">';
            $items .=               '<a href="'.$logout_url.'"><span class="menu-text">Logout</span></a>';
            $items .=           '</li>';        
            $items .=       '</ul>';

        }else{
            $login_url = site_url('/login');
            $register_url = site_url('/registration');
            $items .= '<li class="menu-item" id="menu-item-register">';
            $items .= '<div class="menu-btn-container">';
            $items .= '<a href="'.$register_url.'" class="btn btn-secondary" id="user-name-menu"><span class="menu-btn-text" >Sign up</span></a>';
            $items .= '</div>';
            $items .= '</li>';
            $items .= '<li class="menu-item" id="menu-item-login">';
            $items .= '<div class="menu-btn-container">';
            $items .= '<a href="'.$login_url.'" class="btn btn-secondary" id="user-name-menu"><span class="menu-btn-text" >Login</span></a>';
            $items .= '</div>';
            $items .= '</li>';
            
            /*
            $items .= '<li class="menu-item menu-item-user-name">';
            $items .= '<div class="">';
            $items .= '<button href="'.$register_url.'" class="btn btn-secondary" id="user-name-menu"><span class="menu-icon icon-user"></span><span class="menu-text" >Sign up</span></a>';
            $items .= '</div>';
            $items .= '</li>';
            $items .= '<li class="menu-item menu-item-user-name">';
            $items .= '<a href="'.$login_url.'" id="user-name-menu"><span class="menu-icon icon-user"></span><span class="menu-text" >Login</span></a>';
            $items .= '</li>';
            */
        }
        
        
    }
    return $items;
}

add_action( 'template_redirect', 'redirect_to_specific_page' );

function redirect_to_specific_page() {

    if ( is_page('myaccount') && ! is_user_logged_in() ) {

    wp_redirect(site_url()); 
    exit;
    }
    
    if ( is_page('login') && is_user_logged_in() ) {

    wp_redirect(site_url('/dashboard')); 
    exit;
    }
    
    if ( is_page('registration') && is_user_logged_in() ) {

    wp_redirect(site_url('/dashboard')); 
    exit;
    }
    
    if ( is_page('logout') && ! is_user_logged_in() ) {

    wp_redirect(site_url()); 
    exit;
    }
    
}


/** Filters the results returned on the category page, based on the original
 * category selected ***/
function filter_results( $query )
{
    global $sf_form_data;
    global $wp_query;
    if ( $sf_form_data->is_valid_form() && $query->is_main_query() && !is_admin() )
    {
        $taxquery = $query->get('tax_query');
        //If the search & filter form is for Women
        if($sf_form_data->form_id()=="268"){    
            //Figure out how to filter by wpbdp_category
            //$query->set('_sft_wpbdp_cat', '1'); //you can use any query modifications from here - http://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts
            
            $taxquery[] =
                    array(
                        'taxonomy' => 'wpbdp_category',
                        'field' => 'name',
                        'terms' => 'women',
                        'operator'=> 'IN'
                );
            
            $query->set( 'tax_query', $taxquery ); 
            
        }else if($sf_form_data->form_id()=="1065"){    
            //Figure out how to filter by wpbdp_category
            //$query->set('_sft_wpbdp_cat', '2'); //you can use any query modifications from here - http://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts
        
            $taxquery[] =
                    array(
                        'taxonomy' => 'wpbdp_category',
                        'field' => 'name',
                        'terms' => "men",
                        'operator'=> 'IN'
                );
            $query->set( 'tax_query', $taxquery ); 
        }else if($sf_form_data->form_id()=="1143"){    
            //Figure out how to filter by wpbdp_category
            //$query->set('_sft_wpbdp_cat', '2'); //you can use any query modifications from here - http://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts    
            
           

            $taxquery[] = 
                    array(
                        'taxonomy' => 'wpbdp_category',
                        'field' => 'slug',
                        'terms' => 'kids-baby',
                        'operator'=> 'IN'
                );
            $query->set( 'tax_query', $taxquery ); 
        }else if($sf_form_data->form_id()=="1147"){    
            //Figure out how to filter by wpbdp_category
            //$query->set('_sft_wpbdp_cat', '2'); //you can use any query modifications from here - http://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts    

            $taxquery[] = array(
                        'taxonomy' => 'wpbdp_category',
                        'field' => 'slug',
                        'terms' => 'girls',
                        'operator'=> 'IN'
                );
            $query->set( 'tax_query', $taxquery ); 
        }else if($sf_form_data->form_id()=="1148"){    
            //Figure out how to filter by wpbdp_category
            //$query->set('_sft_wpbdp_cat', '2'); //you can use any query modifications from here - http://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts    

            $taxquery[] = 
                    array(
                        'taxonomy' => 'wpbdp_category',
                        'field' => 'slug',
                        'terms' => 'boys',
                        'operator'=> 'IN'
                );
            $query->set( 'tax_query', $taxquery ); 
        }else if($sf_form_data->form_id()=="1149"){    
            //Figure out how to filter by wpbdp_category
            //$query->set('_sft_wpbdp_cat', '2'); //you can use any query modifications from here - http://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts    

            $taxquery[] = 
                    array(
                        'taxonomy' => 'wpbdp_category',
                        'field' => 'slug',
                        'terms' => 'baby',
                );
            $query->set( 'tax_query', $taxquery ); 
        }
        
    }
}
add_action( 'pre_get_posts', 'filter_results', 21 );    

function get_user_profile_thumb_circle($size, $user_id){
    
    global $xoouserultra;   
    $user=wp_get_current_user();
    //$name=$user->display_name; // or user_login , user_firstname, user_lastname

    $user_profile_url = site_url("/profile");
    //$user_photo = get_avatar($user->ID, 40);
    $user_photo = $xoouserultra->userpanel->get_user_pic_url( $user_id, $size, "avatar");

    $items .= '<div class="user-thumb-photo user-avatar-rounded" style="width:'.$size.'px; height:'.$size.'px; background-image: url('.$user_photo.'); "></div>';

    return $items;
}

function get_current_filters(){
    global $sf_form_data;
    global $wp_query;

    //$this->frmqreserved = array(SF_FPRE."category_name", SF_FPRE."s", SF_FPRE."tag", SF_FPRE."submitted", SF_FPRE."post_date", SF_FPRE."post_types", SF_FPRE."sort_order", SF_FPRE."author"); //same as reserved

    $categories = array();
    $defaults = array();

    if(isset($wp_query->query['category_name']))
    {
            $category_params = (preg_split("/[,\+ ]/", esc_attr($wp_query->query['category_name']))); //explode with 2 delims

            //$category_params = explode("+",esc_attr($wp_query->query['category_name']));

            foreach($category_params as $category_param)
            {
                    $category = get_category_by_slug( $category_param );
                    if(isset($category->cat_ID))
                    {
                            $categories[] = $category->cat_ID;
                    }
            }
    }

    //$this->defaults[SF_FPRE.'category'] = $categories;
    $defaults['category'] = $categories;
    
    //grab search term for prefilling search input
    if(isset($wp_query->query['s']))
    {//!"�$%^&*()
            $this->searchterm = trim(get_search_query());
    }

    //check to see if tag is set

    $tags = array();

    if(isset($wp_query->query['tag']))
    {
            $tag_params = (preg_split("/[,\+ ]/", esc_attr($wp_query->query['tag']))); //explode with 2 delims

            foreach($tag_params as $tag_param)
            {
                    $tag = get_term_by("slug",$tag_param, "post_tag");
                    if(isset($tag->term_id))
                    {
                            $tags[] = $tag->term_id;
                    }
            }
    }

    //$this->defaults[SF_FPRE.'post_tag'] = $tags;
    $defaults['post_tag'] = $tags;

    $taxonomies_list = get_taxonomies('','names');


    $taxs = array();

    //loop through all the query vars
    if(isset($wp_query->query))
    {
            foreach($wp_query->query as $key=>$val)
            {

                    if (strpos($key, SF_TAX_PRE) === 0)
                    {
                            $key = substr($key, strlen(SF_TAX_PRE));

                            $taxslug = ($val);
                            //$tax_params = explode("+",esc_attr($taxslug));

                            $tax_params = (preg_split("/[,\+ ]/", esc_attr($taxslug))); //explode with 2 delims

                            foreach($tax_params as $tax_param)
                            {
                                    $tax = get_term_by("slug",$tax_param, $key);

                                    if(isset($tax->term_id))
                                    {
                                            $taxs[] = $tax->term_id;
                                    }
                            }

                            //$this->defaults[SF_TAX_PRE.$key] = $taxs;
                            $defaults[$key] = $taxs;


                    }
                    else if (strpos($key, SF_META_PRE) === 0)
                    {
                            $key = substr($key, strlen(SF_META_PRE));

                            $meta_data = array("","");

                            if(isset($wp_query->query[SF_META_PRE.$key]))
                            {
                                    //get meta field options
                                    $meta_field_data = $sf_form_data->get_field_by_key(SF_META_PRE.$key);


                                    if($meta_field_data['meta_type']=="number")
                                    {
                                            $meta_data = array("","");
                                            if(isset($wp_query->query[$key]))
                                            {

                                                    $meta_data = (preg_split("/[,\+ ]/", esc_attr(($wp_query->query[SF_META_PRE.$key])))); //explode with 2 delims

                                                    if(count($meta_data)==1)
                                                    {
                                                            $meta_data[1] = "";
                                                    }
                                            }

                                            //$this->defaults[SF_FPRE.$key] = $meta_data;	
                                            $defaults[$key] = $meta_data;
                                    }
                                    else if($meta_field_data['meta_type']=="choice")
                                    {
                                            if($meta_field_data["operator"]=="or")
                                            {
                                                    $ochar = "-,-";
                                                    $meta_data = explode($ochar, esc_attr($wp_query->query[SF_META_PRE.$key]));
                                            }
                                            else
                                            {
                                                    $ochar = "-+-";
                                                    $meta_data = explode($ochar, esc_attr(urlencode($wp_query->query[SF_META_PRE.$key])));
                                                    $meta_data = array_map( 'urldecode', ($meta_data) );
                                            }

                                            if(count($meta_data)==1)
                                            {
                                                    $meta_data[1] = "";
                                            }
                                    }
                                    else if($meta_field_data['meta_type']=="date")
                                    {
                                            $meta_data = array("","");
                                            if(isset($wp_query->query[$key]))
                                            {
                                                    $meta_data = array_map('urldecode', explode("+", esc_attr(urlencode($wp_query->query[SF_META_PRE.$key]))));
                                                    if(count($meta_data)==1)
                                                    {
                                                            $meta_data[1] = "";
                                                    }
                                            }
                                    }
                            }

                            //$this->defaults[SF_META_PRE.$key] = $meta_data;					
                            $defaults[$key] = $meta_data;					

                    }
            }
        }

        $post_date = array("","");
        if(isset($wp_query->query['post_date']))
        {
                $post_date = array_map('urldecode', explode("+", esc_attr(urlencode($wp_query->query['post_date']))));
                if(count($post_date)==1)
                {
                        $post_date[1] = "";
                }
        }
        //$this->defaults[SF_FPRE.'post_date'] = $post_date;
        $defaults['post_date'] = $post_date;

        $post_types = array();
        if(isset($wp_query->query['post_types']))
        {
                $post_types = explode(",",esc_attr($wp_query->query['post_types']));
        }
        //$this->defaults[SF_FPRE.'post_type'] = $post_types;
        $defaults['post_type'] = $post_types;

        $sort_order = array();
        if(isset($wp_query->query['sort_order']))
        {
                $sort_order = explode(",",esc_attr(urlencode($wp_query->query['sort_order'])));
        }
        //$this->defaults[SF_FPRE.'sort_order'] = $sort_order;
        $defaults['sort_order'] = $sort_order;

        $authors = array();
        if(isset($wp_query->query['authors']))
        {
                $authors = explode(",",esc_attr($wp_query->query['authors']));
        }

        //$this->defaults[SF_FPRE.'author'] = $authors;
        $defaults['author'] = $authors;

        /*echo "<pre>";
        //var_dump($this->defaults);
        global $sf_form_data;
        var_dump($sf_form_data->get_count_table());
        echo "</pre>";*/

        return $defaults;
}

function get_current_filters_by_field($field_name){
    global $sf_form_data;
    global $wp_query;

    //$this->frmqreserved = array(SF_FPRE."category_name", SF_FPRE."s", SF_FPRE."tag", SF_FPRE."submitted", SF_FPRE."post_date", SF_FPRE."post_types", SF_FPRE."sort_order", SF_FPRE."author"); //same as reserved
    $categories = array();
    $defaults = array();

        
    if($field_name=='category'){
        if(isset($wp_query->query['category_name']))
        {
                $category_params = (preg_split("/[,\+ ]/", esc_attr($wp_query->query['category_name']))); //explode with 2 delims

                //$category_params = explode("+",esc_attr($wp_query->query['category_name']));

                foreach($category_params as $category_param)
                {
                    $category = get_category_by_slug( $category_param );
                        if(isset($category->cat_ID))
                        {
                                $categories[] = $category->cat_ID;
                        }
                }
        }

        //$this->defaults[SF_FPRE.'category'] = $categories;
        return $categories;
    }elseif($field_name=='tag'){
    
        //check to see if tag is set
        $tags = array();

        if(isset($wp_query->query['tag']))
        {
                $tag_params = (preg_split("/[,\+ ]/", esc_attr($wp_query->query['tag']))); //explode with 2 delims

                foreach($tag_params as $tag_param)
                {
                        
                    $tag = get_term_by("slug",$tag_param, "post_tag");
                        if(isset($tag->term_id))
                        {
                                $tags[] = $tag->term_id;
                        }
                }
        }

        //$this->defaults[SF_FPRE.'post_tag'] = $tags;
        return $tags;
    }elseif($field_name=='post_date'){
        
        $post_date = array("","");
        if(isset($wp_query->query['post_date']))
        {
                $post_date = array_map('urldecode', explode("+", esc_attr(urlencode($wp_query->query['post_date']))));
                if(count($post_date)==1)
                {
                        $post_date[1] = "";
                }
        }
        //$this->defaults[SF_FPRE.'post_date'] = $post_date;
        return $post_date;
    }elseif($field_name=='post_types'){

        $post_types = array();
        if(isset($wp_query->query['post_types']))
        {
                $post_types = explode(",",esc_attr($wp_query->query['post_types']));
        }
        //$this->defaults[SF_FPRE.'post_type'] = $post_types;
        return $post_types;
    }elseif($field_name=='sort_order'){

        $sort_order = array();
        if(isset($wp_query->query['sort_order']))
        {
                $sort_order = explode(",",esc_attr(urlencode($wp_query->query['sort_order'])));
        }
        //$this->defaults[SF_FPRE.'sort_order'] = $sort_order;
        return $sort_order;
    }elseif($field_name=='authors'){

        $authors = array();
        if(isset($wp_query->query['authors']))
        {
                $authors = explode(",",esc_attr($wp_query->query['authors']));
        }

        //$this->defaults[SF_FPRE.'author'] = $authors;
        return $authors;
    
    }else{

        $taxs = array();
        //loop through all the query vars
        if(isset($wp_query->query))
        {
                foreach($wp_query->query as $key=>$val)
                {
                        if (strpos($key, SF_TAX_PRE) === 0 && $field_name == 'taxonomy')
                        {
                                $key = substr($key, strlen(SF_TAX_PRE));

                                $taxslug = ($val);
                                //$tax_params = explode("+",esc_attr($taxslug));

                                $tax_params = (preg_split("/[,\+ ]/", esc_attr($taxslug))); //explode with 2 delims

                                foreach($tax_params as $tax_param)
                                {
                                    
                                    $tax = get_term_by("slug",$tax_param, $key);

                                        if(isset($tax->term_id))
                                        {
                                                $taxs[] = $tax->term_id;
                                        }
                                }

                                //$this->defaults[SF_TAX_PRE.$key] = $taxs;
                                $defaults[$key] = $taxs;


                        }
                        else if (strpos($key, SF_META_PRE) === 0 && ($field_name == substr($key, strlen(SF_META_PRE))||$field_name==$key)) 
                       {
                                if($field_name!=$key){
                                    $key = substr($key, strlen(SF_META_PRE));
                                }    
                                $meta_data = array("","");

                                if(isset($wp_query->query[SF_META_PRE.$key]))
                                {
                                        //get meta field options
                                        $meta_field_data = $sf_form_data->get_field_by_key(SF_META_PRE.$key);
                                        if($meta_field_data['meta_type']=="number")
                                        {
                                                $meta_data = array("","");
                                                if(isset($wp_query->query[$key]))
                                                {

                                                        $meta_data = (preg_split("/[,\+ ]/", esc_attr(($wp_query->query[SF_META_PRE.$key])))); //explode with 2 delims

                                                        if(count($meta_data)==1)
                                                        {
                                                                $meta_data[1] = "";
                                                        }
                                                }

                                                //$this->defaults[SF_FPRE.$key] = $meta_data;	
                                                $defaults[$key] = $meta_data;
                                        }
                                        else if($meta_field_data['meta_type']=="choice")
                                        {
                                                if($meta_field_data["operator"]=="or")
                                                {
                                                        $ochar = "-,-";
                                                        $meta_data = explode($ochar, esc_attr($wp_query->query[SF_META_PRE.$key]));
                                                }
                                                else
                                                {
                                                        $ochar = "-+-";
                                                        $meta_data = explode($ochar, esc_attr(urlencode($wp_query->query[SF_META_PRE.$key])));
                                                        $meta_data = array_map( 'urldecode', ($meta_data) );
                                                }
                                                /*
                                                if(count($meta_data)==1)
                                                {
                                                        $meta_data[1] = "";
                                                }*/
                                        }
                                        else if($meta_field_data['meta_type']=="date")
                                        {
                                                $meta_data = array("","");
                                                if(isset($wp_query->query[$key]))
                                                {
                                                        $meta_data = array_map('urldecode', explode("+", esc_attr(urlencode($wp_query->query[SF_META_PRE.$key]))));
                                                        if(count($meta_data)==1)
                                                        {
                                                                $meta_data[1] = "";
                                                        }
                                                }
                                        }
                                }

                                //$this->defaults[SF_META_PRE.$key] = $meta_data;					
                                //$defaults[$key] = $meta_data;					
                        }
                }
            }
    }


        return $meta_data;
}

if( !defined( 'ABSPATH' ) )
	exit;
	
function modify_wp_search_where( $where ) {
	if( is_search() ) {
		
		global $wpdb, $wp;

		$search_var = $wp->query_vars['s'];
		$search_bool = stripos($search_var, " ");

		if($search_bool == false){

			$where = preg_replace(
			"/($wpdb->posts.post_title (LIKE '%{$wp->query_vars['s']}%'))/i",
                        //"$0 OR ( $wpdb->postmeta.meta_value LIKE '%{$wp->query_vars['s']}%' )",
			"$0 OR ( $wpdb->postmeta.meta_value LIKE '%{$wp->query_vars['s']}%' ) OR (t.name LIKE '%{$wp->query_vars['s']}%') ",
			$where
			);

		}else{

			$search_var = explode(" ", $search_var);
			for($i=0; $i<count($search_var); $i++){
				$where = preg_replace(
					"/($wpdb->posts.post_title (LIKE '%{$search_var[$i]}%'))/i",
                                        //"$0 OR ( $wpdb->postmeta.meta_value LIKE '%{$wp->query_vars['s']}%' )",
					"$0) OR ( $wpdb->postmeta.meta_value LIKE '%{$search_var[$i]}%' ) OR (t.name LIKE '%{$search_var[$i]}%' ",
					$where
					);
			}
                        /*
                        $where .= " OR ("
                                . " ($wpdb->posts.post_title LIKE '%{$wp->query_vars['s']}%' ) "
                                . " OR ($wpdb->postmeta.meta_value LIKE '%{$wp->query_vars['s']}%' )"
                                . " OR (t.name LIKE '%{$wp->query_vars['s']}%' ) "
                                . " OR ($wpdb->post_content LIKE %{$wp->query_vars['s']}%' )"
                                . ")"; 
			*/

                }
                
		add_filter( 'posts_join_request', 'modify_wp_search_join' );
                add_filter( 'posts_groupby', 'modify_wp_search_groupby');
		add_filter( 'posts_distinct_request', 'modify_wp_search_distinct' );
	}
	
	return $where;
	
}
add_action( 'posts_where_request', 'modify_wp_search_where' );

function modify_wp_search_join( $join ) {

	global $wpdb;
	
	if(strpos($join, "JOIN $wpdb->post_meta")){
            $join = preg_replace("/[LEFT |INNER ] JOIN $wpdb->postmeta ON ([a-z0-9_-])/i", 
                " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ", $join);
        }else{
            $join .= " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
        }
        
	$join .= " LEFT JOIN $wpdb->term_relationships tr ON ($wpdb->posts.ID = tr.object_id)";
        $join .= " LEFT JOIN $wpdb->term_taxonomy tt ON (tt.term_taxonomy_id=tr.term_taxonomy_id)";
        $join .= " LEFT JOIN $wpdb->terms t ON (t.term_id = tt.term_id)";
        /*$join .= " LEFT JOIN ($wpdb->term_relationships tr, $wpdb->term_taxonomy tt, $wpdb->terms t) "
                . "ON ($wpdb->posts.ID = tr.object_id, "
                . "tt.term_taxonomy_id = tr.term_taxonomy_id, "
                . "t.term_id = tt.term_id)";
        */
        return $join;
        
}

function modify_wp_search_groupby($groupby){
  global $wpdb;

  // we need to group on post ID
  $groupby_id = "{$wpdb->posts}.ID";
  if(!is_search() || strpos($groupby, $groupby_id) !== false) return $groupby;

  // groupby was empty, use ours
  if(!strlen(trim($groupby))) return $groupby_id;

  // wasn't empty, append ours
  return $groupby.", ".$groupby_id;
}


function modify_wp_search_distinct( $distinct ) {

	return 'DISTINCT';
	
}

function edit_page_title($title, $sep){
        if(isset($_GET["module"])){	$module = $_GET["module"];	}
            
        if($module=='messages' || $module=='messages_sent'){
            $title = "My Messages | Carnaby West";
        }elseif($module=='friends'){
            $title = "Friends | Carnaby West";
        }elseif($module=='profile'){
            $title = "Edit My Profile | Carnaby West";
        }
        //fix for profile as well
        return $title;
    }
    
add_filter('wp_title', 'edit_page_title', 10, 2);

function edit_the_title($title, $sep){
        if(isset($_GET["module"])){	$module = $_GET["module"];	}
            
        if($module=='messages' || $module=='messages_sent'){
            $title = "My Messages";
        }elseif($module=='friends'){
            $title = "My Friends";
        }elseif($module=='profile'){
            $title = "Edit My Profile";
        }
        //fix for profile as well
        return $title;
    }
    
add_filter('the_title', 'edit_the_title', 10, 2);
