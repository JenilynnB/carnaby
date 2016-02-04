<?php
/*
 * Plugin API
 */

function wpbdp() {
    global $wpbdp;
    return $wpbdp;
}

function wpbdp_get_version() {
    return WPBDP_VERSION;
}

function wpbdp_get_page_id($name='main') {
    global $wpdb;

    static $shortcodes = array(
        'main' => array('businessdirectory', 'business-directory', 'WPBUSDIRMANUI'),
        'add-listing' => array('businessdirectory-submitlisting', 'WPBUSDIRMANADDLISTING'),
        'manage-listings' => array('businessdirectory-managelistings', 'WPBUSDIRMANMANAGELISTING'),
        'view-listings' => array('businessdirectory-viewlistings', 'businessdirectory-listings', 'WPBUSDIRMANMVIEWLISTINGS'),
        'paypal' => 'WPBUSDIRMANPAYPAL',
        '2checkout' => 'WPBUSDIRMANTWOCHECKOUT',
        'googlecheckout' => 'WPBUSDIRMANGOOGLECHECKOUT'
    );

    if (!array_key_exists($name, $shortcodes))
        return null;

    $where = '1=0';
    $options = is_string($shortcodes[$name]) ? array($shortcodes[$name]) : $shortcodes[$name];
    foreach ($options as $shortcode) {
        $where .= sprintf(" OR post_content LIKE '%%[%s]%%'", $shortcode);
    }

    $id = wp_cache_get( $name, 'wpbdp pages' );

    if ( ! $id )
        $id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE ({$where}) AND post_status = 'publish' AND post_type = 'page' LIMIT 1");

    wp_cache_set( $name, $id, 'wpbdp pages' );

    return $id;
}

function wpbdp_get_page_link($name='main', $arg0=null) {
    if ( $page_id = wpbdp_get_page_id( $name ) ) {
        return _get_page_link( $page_id );
    }

    switch ( $name ) {
        case 'view':
        case 'viewlisting':
        case 'show-listing':
        case 'showlisting':
            $link = get_permalink( intval( $arg0 ) );
            break;
        case 'edit':
        case 'editlisting':
        case 'edit-listing':
        case 'delete':
        case 'deletelisting':
        case 'delete-listing':
        case 'upgrade':
        case 'upgradetostickylisting':
        case 'upgradelisting':
        case 'upgrade-listing':
            $link = add_query_arg( array( 'action' => $name, 'listing_id' => intval( $arg0 ) ), wpbdp_get_page_link( 'main' ) );
            break;
        case 'viewlistings':
        case 'view-listings':
            $link = add_query_arg( array( 'action' => 'viewlistings' ), wpbdp_get_page_link( 'main' ) );
            break;
        case 'add':
        case 'addlisting':
        case 'add-listing':
        case 'submit':
        case 'submitlisting':
        case 'submit-listing':
            $link = add_query_arg( array( 'action' => 'submitlisting' ), wpbdp_get_page_link( 'main' ) );
            break;
        case 'search':
            $link = add_query_arg( array( 'action' => 'search' ), wpbdp_get_page_link( 'main' ) );
            break;
        default:
            if ( !wpbdp_get_page_id( 'main' ) )
                return '';

            $link = wpbdp_get_page_link( 'main' );
            break;
    }

    return $link;
}

/* Admin API */
function wpbdp_admin() {
    return wpbdp()->admin;
}

function wpbdp_admin_notices() {
    wpbdp_admin()->admin_notices();
}

/* Settings API */
function wpbdp_settings_api() {
    global $wpbdp;
    return $wpbdp->settings;
}

function wpbdp_get_option($key, $def=null) {
    global $wpbdp;
    return $wpbdp->settings->get($key, $def);
}

function wpbdp_set_option($key, $value) {
    global $wpbdp;
    return $wpbdp->settings->set($key, $value);
}

/* Form Fields API */
function wpbdp_formfields_api() {
    global $wpbdp;
    return $wpbdp->formfields;
}

function wpbdp_get_formfield($id) {
    
    if (is_numeric($id) && is_string($id))
        return wpbdp_get_formfield(intval($id));
    if (is_string($id))
        return wpbdp_formfields_api()->getFieldsByAssociation($id, true);

    return wpbdp_formfields_api()->get_field($id);
}

/* Fees/Payment API */
function wpbdp_payments_possible() {
    return wpbdp_payments_api()->payments_possible();
}

function wpbdp_fees_api() {
    return wpbdp()->fees;
}

function wpbdp_payments_api() {
    return wpbdp()->payments;
}

/* Listings API */
function wpbdp_listings_api() {
    return wpbdp()->listings;
}

function wpbdp_listing_upgrades_api() {
    return wpbdp()->listings->upgrades;
}

/* Misc. */
function wpbdp_categories_list($parent=0, $hierarchical=true) {
    $terms = get_categories(array(
        'taxonomy' => WPBDP_CATEGORY_TAX,
        'parent' => $parent,
        'orderby' => 'name',
        'hide_empty' => 0,
        'hierarchical' => 0
    ));

    if ($hierarchical) {
        foreach ($terms as &$term) {
            $term->subcategories = wpbdp_categories_list($term->term_id, true);
        }
    }

    return $terms;
}

/*this function returns the exisitng category that you've passed in, plus the parent categories
 * 
 */
function wpbdp_get_parent_categories($catid) {
    $category = get_term(intval($catid), WPBDP_CATEGORY_TAX);

    if ($category->parent) {
        return array_merge(array($category), wpbdp_get_parent_categories($category->parent));
    }

    return array($category);
}

function wpbdp_get_parent_catids($catid) {
    $parent_categories = wpbdp_get_parent_categories($catid);
    array_walk($parent_categories, create_function('&$x', '$x = intval($x->term_id);'));    

    return $parent_categories;
}
/*
function wpbdp_get_child_categories($catid) {
    $category = get_term(intval($catid), WPBDP_CATEGORY_TAX);

    if ($category->child) {
        return array_merge(array($category), wpbdp_get_child_categories($category->child));
    }

    return array($category);
}
*/
function wpbdp_locate_template($template, $allow_override=true, $try_defaults=true) {
    $template_file = '';

    if (!is_array($template))
        $template = array($template);

    if ($allow_override) {
        $search_for = array();

        foreach ($template as $t) {
            $search_for[] = $t . '.tpl.php';
            $search_for[] = $t . '.php';
            $search_for[] = 'single/' . $t . '.tpl.php';
            $search_for[] = 'single/' . $t . '.php';
        }

        $template_file = locate_template($search_for);
    }

    if (!$template_file && $try_defaults) {
        foreach ($template as $t) {
            $template_path = WPBDP_TEMPLATES_PATH . '/' . $t . '.tpl.php'; 
            
            if (file_exists($template_path)) {
                $template_file = $template_path;
                break;
            }elseif(file_exists($t)){
                $template_file = $t;
                break;
            }
            
        }
    }

    return $template_file;
}

function wpbdp_render($template, $vars=array(), $allow_override=true) {
    $vars = wp_parse_args($vars, array(
        '__page__' => array(
            'class' => array(),
            'content_class' => array(),
            'before_content' => '')));
    $template_name = is_array( $template ) ? $template[0] : $template;
    $vars = apply_filters('wpbdp_template_vars', $vars, $template_name);
    return apply_filters( "wpbdp_render_{$template_name}", wpbdp_render_page(wpbdp_locate_template($template, $allow_override), $vars, false) );
}

function wpbdp_render_msg($msg, $type='status') {
    $html = '';
    $html .= sprintf('<div class="wpbdp-msg %s">%s</div>', $type, $msg);
    return $html;
}

/*
 * Template functions
 */

/**
 * Displays a single listing view taking into account all of the theme overrides.
 * @param mixed $listing_id listing object or listing id to display.
 * @param string $view 'single' for single view or 'excerpt' for summary view.
 * @return string HTML output.
 */
function wpbdp_render_listing($listing_id=null, $view='single', $echo=false, $category = "") {
    if (is_object($listing_id)) $listing_id = $listing_id->ID;

    global $post;
    $listings_api = wpbdp_listings_api();

    if ($listing_id)  {
        $args = array( 'post_type' => WPBDP_POST_TYPE, 'p' => $listing_id );

        if ( !isset( $_GET['preview'] ) )
            $args['post_status'] = 'publish';

        query_posts( $args );

        if (have_posts()) the_post();
    }

    if (!$post || $post->post_type != WPBDP_POST_TYPE) {
        return '';
    }

    if ($view == 'excerpt')
        $html = _wpbdp_render_excerpt($category);
    else
        $html = _wpbdp_render_single($category);

    if ($listing_id)
        wp_reset_query();

    if ($echo)
        echo $html;

    return $html;
}

function _wpbdp_render_single($category = "") {
    global $post;

    $html = '';

    $sticky_status = wpbdp_listings_api()->get_sticky_status($post->ID);

    $html .= sprintf( '<div id="wpbdp-listing-%d" class="wpbdp-listing wpbdp-listing-single %s %s %s" itemscope itemtype="http://schema.org/LocalBusiness">',
                      $post->ID,
                      'single',
                      $sticky_status,
                      apply_filters( 'wpbdp_listing_view_css', '', $post->ID ) );
    $html .= apply_filters('wpbdp_listing_view_before', '', $post->ID, 'single');
    $html .= wpbdp_capture_action('wpbdp_before_single_view', $post->ID);

    $sticky_tag = '';
    if ($sticky_status == 'sticky')
        $sticky_tag = sprintf('<div class="stickytag"><img src="%s" alt="%s" border="0" title="%s"></div>',
                        WPBDP_URL . 'core/images/featuredlisting.png',
                        _x('Featured Listing', 'templates', 'WPBDM'),
                        the_title(null, null, false));

    //I changed the parameter below from 'listing' to 'excerpt' -JLB
    $d = WPBDP_ListingFieldDisplayItem::prepare_set( $post->ID, 'excerpt' );
    $listing_fields = implode( '', WPBDP_ListingFieldDisplayItem::walk_set( 'html', $d->fields ) );
    $social_fields = implode( '', WPBDP_ListingFieldDisplayItem::walk_set( 'html', $d->social ) );
   
    
    // images
    //$thumbnail_id = wpbdp_listings_api()->get_thumbnail_id($post->ID);
    $thumbnail_id = get_thumbnail_id($post->ID);
    $images = wpbdp_listings_api()->get_images($post->ID);
    $extra_images = array();

    if ( wpbdp_get_option( 'allow-images' ) ) {
        foreach ($images as $img) {
            // create thumbnail of correct size if needed (only in single view to avoid consuming server resources)
            _wpbdp_resize_image_if_needed( $img->ID );

            if ($img->ID == $thumbnail_id) continue;

            $full_image_data = wp_get_attachment_image_src( $img->ID, 'wpbdp-large', false );
            $full_image_url = $full_image_data[0];

            $extra_images[] = sprintf('<a href="%s" class="thickbox" data-lightbox="wpbdpgal" target="_blank">%s</a>',
                                        $full_image_url,
                                        wp_get_attachment_image( $img->ID, 'wpbdp-thumb', false, array(
                                            'class' => 'wpbdp-thumbnail size-thumbnail',
                                            'alt' => the_title(null, null, false),
                                            'title' => the_title(null, null, false)
                                        ) ));
        }
    }
    
    $main_image = wpbdp_listing_main_image( null, array(
            'link' => 'picture',
            'class' => 'wpbdp-single-thumbnail',
            'thumb_type' => $category), 'large' );
    
    $vars = array(
//        'actions' => wpbdp_render('parts/listing-buttons', array('listing_id' => $post->ID, 'view' => 'single'), false),
        'is_sticky' => $sticky_status == 'sticky',
        'sticky_tag' => $sticky_tag,
        'title' => get_the_title(),
        //'main_image' => wpbdp_get_option( 'allow-images' ) ? wpbdp_listing_thumbnail( null, 'link=picture&class=wpbdp-single-thumbnail' ) : '',
        'main_image' => $main_image,
        'listing_fields' => apply_filters('wpbdp_single_listing_fields', $listing_fields, $post->ID),
        'fields' => $d->fields,
        'listing_id' => $post->ID,
        'extra_images' => $extra_images
    );
    $vars = apply_filters( 'wpbdp_listing_template_vars', $vars, $post->ID );
    $vars = apply_filters( 'wpbdp_single_template_vars', $vars, $post->ID );

    $html .= wpbdp_render('businessdirectory-listing', $vars, true);

    $social_fields = apply_filters('wpbdp_single_social_fields', $social_fields, $post->ID);
    if ($social_fields)
        $html .= '<div class="social-fields cf">' . $social_fields . '</div>';

    $html .= apply_filters('wpbdp_listing_view_after', '', $post->ID, 'single');
    $html .= wpbdp_capture_action('wpbdp_after_single_view', $post->ID);

    $show_contact_form = apply_filters('wpbdp_show_contact_form', wpbdp_get_option('show-contact-form'), $post->ID);
    if ($show_contact_form) {
        $html .= '<div class="contact-form">';
        $html .= wpbdp_listing_contact_form();
        $html .= '</div>';
    }

    if (wpbdp_get_option('show-comment-form')) {
        $html .= '<div class="comments">';

        ob_start();
        comments_template(null, true);
        $html .= ob_get_contents();
        ob_end_clean();

        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}

function _wpbdp_render_excerpt($category = "") {
    global $post;
    static $counter = 0;

    $sticky_status = wpbdp_listings_api()->get_sticky_status($post->ID);

    $html = '';
    $html .= sprintf('<div id="wpbdp-listing-%d" class="wpbdp-listing excerpt wpbdp-listing-excerpt %s %s %s cf">',
                     $post->ID,
                     $sticky_status,
                     ($counter & 1) ? 'odd':  'even',
                     apply_filters( 'wpbdp_excerpt_view_css', '', $post->ID ) );
    $html .= wpbdp_capture_action('wpbdp_before_excerpt_view', $post->ID);

    $d = WPBDP_ListingFieldDisplayItem::prepare_set( $post->ID, 'excerpt' );
    $listing_fields = implode( '', WPBDP_ListingFieldDisplayItem::walk_set( 'html', $d->fields ) );
    $social_fields = implode( '', WPBDP_ListingFieldDisplayItem::walk_set( 'html', $d->social ) );
    
    //$listing_url = wpbdp_render_listing_field('URL');
            
    
   $thumbnail = wpbdp_listing_thumbnail( null, array(
           'link' => 'listing',
           'class' => 'wpbdmthumbs wpbdp-excerpt-thumbnail',
           'thumb_type' => $category));
   
           //'link=listing&class=wpbdmthumbs wpbdp-excerpt-thumbnail'
   
    //$g = WPDP_ListingFieldDisplayItem::

    $vars = array(
        'is_sticky' => $sticky_status == 'sticky',
        //'thumbnail' => listing_thumbnail_screenshot($listing_url),
        'thumbnail' => $thumbnail,
        'title' => get_the_title(),
        'listing_fields' => apply_filters('wpbdp_excerpt_listing_fields', $listing_fields, $post->ID),
        'fields' => $d->fields,
        'listing_id' => $post->ID,
        'category' => $category
    );
    $vars = apply_filters( 'wpbdp_listing_template_vars', $vars, $post->ID );
    $vars = apply_filters( 'wpbdp_excerpt_template_vars', $vars, $post->ID );

    $html .= wpbdp_render('businessdirectory-excerpt', $vars, true);

    $social_fields = apply_filters('wpbdp_excerpt_social_fields', $social_fields, $post->ID);
    if ($social_fields)
        $html .= '<div class="social-fields cf">' . $social_fields . '</div>';

    $html .= wpbdp_capture_action('wpbdp_after_excerpt_view', $post->ID);
    //These are the "view", "edit" and "delete" links for each listing commented out
    //$html .= wpbdp_render('parts/listing-buttons', array('listing_id' => $post->ID, 'view' => 'excerpt'), false);
    $html .= '</div>';

    $counter++;

    return $html;
}

/*
 * This function will return properly formatted HTML for any "listing field". 
 * The Field Name will take either a field ID or a name. Here is a list of labels and IDs (still building this list)
 * "Rating (average)":11
 * "URL":5
 * "Business Name":1
 * "Description":4
 * "Categories":2
 * "Site Type": 9
 * 
 */

function wpbdp_render_listing_field_html($field_name, $post_id='') {
    global $post;
    
    if($post_id==''){
        $post_id = $post->ID;
    }
    
    $html = '';
    
    $d = WPBDP_ListingFieldDisplayItem::prepare_set( $post_id, 'listing' );
    $html .= implode( '', WPBDP_ListingFieldDisplayItem::get_field( 'html', $d->fields, $field_name ));
   return $html;
}


/*
 * Same as above function, returns without formatted HTML
 */
function wpbdp_render_listing_field($field_name,$post_id=null) {
    global $post;
    if(empty($post_id)){
        $post_id = $post->ID;
    }
    
    $html = '';
    
    $d = WPBDP_ListingFieldDisplayItem::prepare_set( $post_id, 'listing' );
    
    if($field_name=="URL"){   
        $fields = WPBDP_ListingFieldDisplayItem::get_field( 'value', $d->fields, $field_name );
        foreach($fields as $field){
            $html = $field;
        }
        
    }else{
        $html .= implode( '', WPBDP_ListingFieldDisplayItem::get_field( 'value', $d->fields, $field_name ));
    }
    return $html;
}

/* Returns all review info for listing
 * 
 */

function get_reviews_and_form($listing_id) {
        global $wpbdp_ratings;    
        if ( apply_filters( 'wpbdp_listing_ratings_enabled', true, $listing_id ) == false ) 
            return;
        
        if(isset($_GET["module"])){	$module = $_GET["module"];	}
        if($module=="reviews"){
            //if we're on the mobile reviews page, show two pages of reviews at
            //  once because the user has already clicked "More Reviews" once
             
            $num_reviews = NUM_REVIEWS_TO_PAGINTE * 2;
        }else{
            $num_reviews = NUM_REVIEWS_TO_PAGINTE;
        }
        
        $vars = array();
        //$vars['review_form'] = $this->can_post_review($listing_id, $reason) ? wpbdp_render_page(plugin_dir_path(__FILE__) . 'templates/form.tpl.php', $this->_form_state) : '';
        $vars['review_form'] = wpbdp_render_page(TEMPLATEPATH . '/review-form.tpl.php', $wpbdp_ratings->_form_state);
        $vars['reason'] = $reason;
        $vars['success'] = $wpbdp_ratings->_form_state['success'];
        $vars['ratings'] = $wpbdp_ratings->get_reviews_paginated($listing_id, $num_reviews);
        $vars['num_reviews'] = $wpbdp_ratings->get_total_reviews($listing_id);

        //return wpbdp_render_page(plugin_dir_path(__FILE__) . 'templates/ratings.tpl.php', $vars);
        return wpbdp_render_page(TEMPLATEPATH.'/ratings.tpl.php', $vars);
    }

/* 
 * Returns information for all categories, formatted for the main listing
 *  
 */
function render_category_info(){
    global $post;
    $html = '';
    
    $listing = WPBDP_Listing::get( $post->ID );
    $wpbdp_categories = $listing->get_categories( 'all' );
    
   
    $womens_categories= array();
    $mens_categories = array();
    $kids_categories = array();
    $girls_categories = array();
    $boys_categories = array();
    $baby_categories = array();
    
    foreach($wpbdp_categories as &$c){
       
       $wp_category = get_term(intval($c->id), WPBDP_CATEGORY_TAX);
       $wp_top_parent_cat = get_top_parent_category($c->id);
       
       $wp_parent = get_term(intval($wp_category->parent), WPBDP_CATEGORY_TAX);
       
       $is_top = ($wp_top_parent_cat == $wp_category);
       
       if($wp_top_parent_cat->name == "Women" && !$is_top):
           $womens_categories[] = $c;
       elseif($wp_top_parent_cat->name == "Men" && !$is_top):
           $mens_categories[]= $c;
       elseif($wp_top_parent_cat->name = "Kids & Baby" && !$is_top):    
           $kids_categories[] = $c;
           if($wp_parent->name == "Girls"){
               $girls_categories[] = $c;
           }elseif($wp_parent->name == "Boys"){
               $boys_categories[] = $c;
           }elseif($wp_parent->name == "Baby"){
               $baby_categories[] = $c;
           }   
       endif;
    }
    
    
    if(!empty($womens_categories)):
         
        $html.= "<h3>Women</h3>
                <table class='listing-cat-info'>";
        
        //$html .= render_good_for("women");        
        $womens_category_links = array();
        $womens_extended_sizes = array();
        
        
        $womens_extended_sizes_values = get_field("womens_extended_sizes");
        $womens_extended_sizes = get_field_labels(array('womens_extended_sizes' => $womens_extended_sizes_values));
        $womens_sizes = get_field('womens_sizes');
        
        
        foreach($womens_categories as $wc){
            $url = get_term_link( $wc->id, WPBDP_CATEGORY_TAX );
            $link = "<a href='" . $url . "'>" . $wc->name . "</a>";
            $womens_category_links[] = $link;
        }
        if(isset($womens_category_links) && sizeof($womens_category_links)>0){
            $html .= '<tr class="listing-tag-row"><td colspan="2"><i class="icon-tag"></i>&nbsp;&nbsp;&nbsp; '.implode(", ", $womens_category_links)."</td></tr>";
        }
        //display all the womens categories for this store
        /*
        if(!empty($womens_category_links)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Categories: </td>';
            $html .= '<td class="listing-category-values">'.implode( ', ', $womens_category_links ).'</td>';
            $html .= '</tr>';
        }
        */
         
        //Display standard womens sizes carried
        if($womens_sizes){
            $womens_sizes_range = get_largest_and_smallest_sizes($womens_sizes);
            
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Sizes: </td> ';
            $html .= '<td class="listing-category-values">'. $womens_sizes_range['smallest'] . " - " . $womens_sizes_range['largest'].'</td>'; 
            $html.='</tr>';
            
            //display womens extended sizes
            if(!empty($womens_extended_sizes)){

                $html.='<tr class="listing-category-row">';
                $html .= '<td style="min-width:120px"></td><td class="listing-category-values">'.implode (", ", $womens_extended_sizes).'</td>';
                $html.='</tr>';
            }
            
            
        }
        
        
 
        if(get_field('have_womens_dress_sizes')){
            $womens_dress_sizes = get_field('womens_dress_sizes');
            if(!empty($womens_dress_sizes)) {
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Dress Sizes: </td>';
                $html .= '<td class="listing-category-values">'.min($womens_dress_sizes) . " - " . max($womens_dress_sizes).'</td>';
                $html .= '</tr>';
            }
        }
          
        if(get_field('have_womens_shoe_sizes')){
            $womens_shoe_sizes = get_field('womens_shoe_sizes');
            if(!empty($womens_shoe_sizes)){
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Shoe Sizes: </td>';
                $html .= '<td class="listing-category-values">'. min($womens_shoe_sizes) . " - " . max($womens_shoe_sizes).'</td>';
                $html .= '</tr>';
            }
        }
 
        
        if(get_field('have_womens_pant_sizes')){
            $womens_pant_sizes = get_field('womens_pant_sizes');
            if(!empty($womens_pant_sizes)){
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Pant Sizes: </td>';
                $html .= '<td class="listing-category-values">'. min($womens_pant_sizes) . " - " . max($womens_pant_sizes).'</td>'; 
                $html .= '</tr>';
            }
        }
       $html.='</table>';  
        
    endif;
    
    if(!empty($mens_categories)):
        
        
        $html.= "<h3>Men</h3>
                <table class='listing-cat-info'>";
        //$html .= render_good_for("men");
        $mens_category_links = array();
        $mens_extended_sizes = array();
        
        $mens_extended_sizes_values = get_field("mens_extended_sizes");
        $mens_extended_sizes = get_field_labels(array('mens_extended_sizes' =>$mens_extended_sizes_values));
        
        $mens_sizes = get_field("mens_sizes");
        
        
        foreach($mens_categories as $mc){
            $url = get_term_link( $mc->id, WPBDP_CATEGORY_TAX );
            $link = "<a href='" . $url . "'>" . $mc->name . "</a>";
            $mens_category_links[] = $link;
        }
        if(isset($mens_category_links) && sizeof($mens_category_links)>0){
            $html .= '<tr class="listing-tag-row"><td colspan="2"><i class="icon-tag"></i>&nbsp;&nbsp;&nbsp; '.implode(", ", $mens_category_links)."</td></tr>";
        }
        //display all the womens categories for this store
        /*
        if(!empty($mens_category_links)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Categories: </td>';
            $html .= '<td class="listing-category-values">'.implode( ', ', $mens_category_links ).'</td>';
            $html .= '</tr>';
        }
         * *
         */
        
        //Display standard womens sizes carried
        if($mens_sizes){
            $mens_sizes_range = get_largest_and_smallest_sizes($mens_sizes);
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Sizes: </td> ';
            $html .= '<td class="listing-category-values">'. $mens_sizes_range['smallest'] . " - " . $mens_sizes_range['largest'].'</td>'; 
            $html .= '</tr>';
            
            //display womens extended sizes
            if(!empty($mens_extended_sizes)){
                $html.='<tr class="listing-category-row">';
                $html .= '<td style="min-width:120px;"></td><td class="listing-category-values">'.implode (", ", $mens_extended_sizes).'</td>';
                $html.='</tr>';
            }
         
        }
        
        if(get_field('have_mens_pant_sizes')){
            $mens_pant_sizes = get_field('mens_pant_sizes');
            if(!empty($mens_pant_sizes)){
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Pant Sizes: </td>';
                $html .= '<td class="listing-category-values">'. min($mens_pant_sizes) . " - " . max($mens_pant_sizes).'</td>'; 
                $html .= '</tr>';
            }
        }
        
        if(get_field('have_mens_suit_sizes')){
            $mens_suit_sizes = get_field('mens_suit_sizes');
            if(!empty($mens_suit_sizes)) {
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Suit Sizes: </td>';
                $html .= '<td class="listing-category-values">'.min($mens_suit_sizes) . " - " . max($mens_suit_sizes).'</td>';
                $html .= '</tr>';
            }
        }
        
        if(get_field('have_mens_dress_shirt_sizes')){
            $mens_shirt_sizes = get_field('mens_dress_shirt_sizes');
            if(!empty($mens_shirt_sizes)) {
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Dress Shirt Sizes: </td>';
                $html .= '<td class="listing-category-values">'.min($mens_shirt_sizes) . " - " . max($mens_shirt_sizes).'</td>';
                $html .= '</tr>';
            }
        }
        
        
        if(get_field('have_mens_shoe_sizes')){
            $mens_shoe_sizes = get_field('mens_shoe_sizes');
            if(!empty($mens_shoe_sizes)){
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Shoe Sizes: </td>';
                $html .= '<td class="listing-category-values">'. min($mens_shoe_sizes) . " - " . max($mens_shoe_sizes).'</td>';
                $html .= '</tr>';
            }
        }
        
        $html.='</table>'; 
         
    endif;
    
    if(!empty($kids_categories)):
        $html.= "<h3>Kids & Baby</h3>
                <table class='listing-cat-info'>";
        //$html .= render_good_for("kids");
        //Need to make label disappear if there are no values
        $kids_category_links = array();
        
        
        foreach($girls_categories as $gc){
            $url = get_term_link( $gc->id, WPBDP_CATEGORY_TAX );
            $link = "<a href='" . $url . "'>" . $gc->name . "</a>";
            $girls_category_links[] = $link;
        }
        
        //display all the womens categories for this store
        
        if(!empty($girls_category_links)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Girls: </td>';
            $html .= '<td class="listing-tag-row"><i class="icon-tag"></i>&nbsp;&nbsp;&nbsp; '.implode(", ", $girls_category_links).'</td>';
            $html .= '</tr>';
        }
        
        foreach($boys_categories as $bc){
            $url = get_term_link( $bc->id, WPBDP_CATEGORY_TAX );
            $link = "<a href='" . $url . "'>" . $bc->name . "</a>";
            $boys_category_links[] = $link;
        }
        
        //display all the womens categories for this store
        
        if(!empty($boys_category_links)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Boys: </td>';
            $html .= '<td class="listing-tag-row"><i class="icon-tag"></i>&nbsp;&nbsp;&nbsp; '.implode(", ", $boys_category_links).'</td>';
            $html .= '</tr>';
        }

        foreach($baby_categories as $bbc){
            $url = get_term_link( $bbc->id, WPBDP_CATEGORY_TAX );
            $link = "<a href='" . $url . "'>" . $bbc->name . "</a>";
            $baby_category_links[] = $link;
        }
        
        //display all the womens categories for this store
        if(!empty($baby_category_links)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Baby: </td>';
            $html .= '<td class="listing-tag-row"><i class="icon-tag"></i>&nbsp;&nbsp;&nbsp; '.implode(", ", $baby_category_links).'</td>';
            $html .= '</tr>';
        }

        
        
        if(get_field('have_girls_sizes')){
            $girls_sizes = get_field('girls_sizes');
            if(!empty($girls_sizes)){
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Girls Sizes: </td>';
                $html .= '<td class="listing-category-values">'. min($girls_sizes) . " - " . max($girls_sizes).'</td>'; 
                $html .= '</tr>';
            }
        }
        
        if(get_field('have_boys_sizes')){
            $boys_sizes = get_field('boys_sizes');
            if(!empty($boys_sizes)) {
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Boys Sizes: </td>';
                $html .= '<td class="listing-category-values">'.min($boys_sizes) . " - " . max($boys_sizes).'</td>';
                $html .= '</tr>';
            }
        }
        
        if(get_field('have_baby_sizes')){
            $baby_sizes = get_field('baby_sizes');
            if(!empty($baby_sizes)){
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Baby Sizes: </td>';
                $html .= '<td class="listing-category-values">'. min($baby_sizes) . " - " . max($baby_sizes).' months</td>';
                $html .= '</tr>';
            }
        }
        
        if(get_field('have_kids-baby_shoe_sizes')){
            $kids_shoe_sizes = get_field('kids-baby_shoe_sizes');
            if(!empty($kids_shoe_sizes)){
                $html.='<tr class="listing-category-row">';
                $html .= '<td class="listing-category-label">Kids Shoe Sizes: </td>';
                $html .= '<td class="listing-category-values">'. min($kids_shoe_sizes) . " - " . max($kids_shoe_sizes).'</td>';
                $html .= '</tr>';
            }
        }
        
        $html.='</table>'; 

        
    endif;
    
    return $html;
    
}
function get_hierarchical_categories($parent_id, $categories){
    $return = array();
    foreach($categories as $c){
        $p_id = $c->parent;
        if($p_id==$parent_id){
             //This is the top level cat we're looking for
            
            unset($categories[key($categories)]);
            
            $c->children = get_hierarchical_categories($c->term_id, $categories);
            $return[] = $c;
        }    
    }
    return $return;
}

function get_top_parent_category($catid) {
    
    $wp_category = get_term(intval($catid), WPBDP_CATEGORY_TAX);
    
    if ($wp_category->parent):
        return get_top_parent_category($wp_category->parent);
    else:
        return $wp_category;
    endif;
    
}

function get_listing_name($listing_id){
    $listing = WPBDP_Listing::get( $listing_id );
    
}

function get_listing_image($listing_id, $size='thumbnail'){
    
    $image = wpbdp_listing_thumbnail( $listing_id, 'link=listing&class=wpbdmthumbs wpbdp-excerpt-thumbnail' );
    return $image;
}

/*
 * Gets the top apparel categories for a given listing. Returns as WPBDP categories.
 * If the listing includes Kids & Baby, subcategories will NOT be returned
 */

function get_top_apparel_categories($listing_id){
    
    $listing = WPBDP_Listing::get( $listing_id );
    if(empty($listing)){
        return;
    }
    $wpbdp_categories = $listing->get_categories( 'all' );
    $top_categories = array();
    $i = 0;
    
    foreach($wpbdp_categories as $wpbdp_c){
        $wp_cats = get_term($wpbdp_c->id, WPBDP_CATEGORY_TAX);
        
        if($wp_cats->parent == 0){
            $top_categories[$i] = $wp_cats;
            $i++;
        }
    }
    
    
    return $top_categories;
}

/*
 * Gets the top apparel categories for a given listing. Returns as WPBDP categories.
 * If the listing includes Kids & Baby, the next level (Boys, Girls, Baby) WILL be returned
 */

function get_top_apparel_categories_with_kids($listing_id){
    
    $listing = WPBDP_Listing::get( $listing_id );
    if(empty($listing)){
        return;
    }
    $wpbdp_categories = $listing->get_categories( 'all' );
    $top_categories = array();
    $i = 0;
    
    foreach($wpbdp_categories as $wpbdp_c){
        $wp_cats = get_term($wpbdp_c->id, WPBDP_CATEGORY_TAX);
        
        //includes top-level and any categories with "Kids & Baby" as the parent
        if($wp_cats->parent == 0 || $wp_cats->parent == 4){
            $top_categories[$i] = $wp_cats;
            $i++;
        }
    }
    
    /*
    foreach($wpbdp_categories as $cat){
        if($cat->name=="Women" || $cat->name =="Men" || $cat->name == "Girls" || $cat->name == "Boys" || $cat->name=="Baby"):
            $top_categories[] = $cat;
        endif;
    }
     * 
     */
    
    return $top_categories;
}


/*
 * 
 * */
function get_top_apparel_categories_html($listing_id=0){
    if($listing_id==0) $listing_id = get_the_ID ();
    
    $categories = get_top_apparel_categories($listing_id);
    $top_categories = array();
    
    //echo print_r($categories);
    foreach($categories as $c){
        $wp_category = get_term(intval($c->id), WPBDP_CATEGORY_TAX);
        if(!is_wp_error($wp_category)){
            $top_categories[] = '<a href="'.get_term_link($wp_category).'" class="btn btn-default btn-md">'.$wp_category->name.'</a>';
        }
    }
    
    return '<div class="listing-top-categories">'.implode("", $top_categories).'</div>';
}


function get_largest_and_smallest_sizes($sizes){
    $res = array("largest","smallest");
    
    //This really should be abstracted to pull from the ACF plugin
    $letter_sizes = array('XXS','XS','S','M','L','XL','XXL','XXXL');
    
    //$largest = max($sizes);
    //$smallest = min($sizes);
    
    if(!$largest && !$smallest):
        
        foreach($letter_sizes as &$ls){
            
            if(in_array($ls, $sizes) && !$smallest): 
                $smallest = $ls;
                $largest = $ls;
            elseif(in_array($ls, $sizes)):
                $largest = $ls;
            endif;
        }
    endif;
    
    $res["largest"] = $largest;    
    $res["smallest"] = $smallest;
   
    return $res;
}

function has_written_review($listing_id=0){
    
    if ($listing_id==0){$listing_id = get_the_ID();}
    if (!wpbdp_get_option('ratings-allow-unregistered') && !is_user_logged_in()) {
        return false;
    }

    global $wpdb;

    $user_id = get_current_user_id();

    if ($user_id) {

        return intval(!$wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_ratings WHERE (user_id = %d OR ip_address = %s) AND listing_id = %d", $user_id, $ip_address, $listing_id) )) == 0;

    } else {
        return intval(!$wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_ratings WHERE ip_address = %s AND listing_id = %d", $ip_address, $listing_id) )) == 0;
    }
}
function get_shopstyle_retailer_id($listing_id=0)
{
    global $post;
    $retailer_id = '';
    
    if ($listing_id == 0){
        $listing_id = $post->id;
    }
    
    $retailer_id = get_field('shopstyle_retailer_id', $listing_id);
    
    if(!empty($retailer_id)){
        $retailer_id = 'r'.$retailer_id;
    }

    return $retailer_id;
}

function render_products_slick_slider($listing_id=null, $category = ""){
    global $post;
    if ($listing_id=null){
        $listing_id = $post->id;
    }
    
    $retailer_id = 'r'.get_field('shopstyle_retailer_id', $listing_id);
    
    if($retailer_id=='r'){
        return "";
    }
    
    if($category!=""){
        
        if($category == "men"){
            $shopstyle_category = "mens-clothes";
        }else if ($category=="women"){
            $shopstyle_category = "womens-clothes";
        }else if ($category=="kids-baby"){
            $shopstyle_category = "kids-and-baby-clothes";
        }else if ($category == "girls"){
            $shopstyle_category = "girls";
        }else if ($category == "boys"){
            $shopstyle_category = "boys";
        }else if ($category == "baby"){
            $shopstyle_category = "kids-and-baby-clothes";
        }
        
        $product_params = array(
                'fl' => $retailer_id,
                'sort'  => 'popular',
                'cat'   => $shopstyle_category
            );
    }else{
        $product_params = array(
                'fl' => $retailer_id,
                'sort'  => 'popular'
            );
    }
        
    $shopstyle = new ShopStyle();
    
    $products_response = $shopstyle->getProducts(12, 0, $product_params);
    
    $products = $products_response["products"];
    if(sizeof($products)==0){
        return "";
    }    
    /*Put the slick slider initial code here*/
    $response .= '<div class="products-slider-container">';
    $response .= '<div class="slick-slider-products listing-products">';
    
    foreach($products as $p){
        
        $product_name = $p["name"];
        if(strlen($product_name)>40){
            $product_name = substr($product_name, 0, 40)."...";
        }
        $product_price = $p["price"];
        $product_url = $p["clickUrl"];
        $product_id = $p["id"];
        
        $product_images = $p["image"];
        $image_sizes = $product_images["sizes"];
        
        $image = $image_sizes["Large"];
        $image_url = $image["url"];
        $image_height = $image["height"];
        $image_width = $image["width"];
        
        
        //Format more swiper code for each product here
        $response .= '<div class="slick-slider-product">';
        $response .=    '<a href="'.$product_url.'" target="_blank">';
        $response .=        '<div class="product-image" id="product-image-'.$product_id.'" rel="product-overlay-'.$product_id.'" style="background-image:url('.$image_url.'); '
                            . 'height:'.$image_height.'px; '
                            . 'width: '.$image_width.'px;">';
        
        $response .=            '<div class="product-overlay" id="product-overlay-'.$product_id.'" >';
        $response .=            '<div class="title">'.$product_name.'</div>';
        $response .=            '<div class="price">$'.number_format($product_price, 2).'</div>';
        $response .=            '<div class="link">View</div>';
        $response .=            '</div>';
        
        $response .= '      </div>';
        $response .=    '</a>';
        $response .= '</div>';
    }
    
    
    //closing swiper code
    $response .= '</div></div>';
    
    return $response;
}

function render_popshop_products($listing_id=null, $category = ""){
    global $post;
    $popshops = new PopShops();
    
    if ($listing_id=null){
        $listing_id = $post->id;
    }
    
    $merchant = $popshops->getMerchants($post->post_title);

    if(!empty($merchant['results'])){
        $merchant_results = $merchant['results']['merchants']['merchant'];
        
        foreach($merchant_results as $m){
            
            $url = wpbdp_render_listing_field('URL', $post->ID);
            $url = $url[1];
            //strip urls of http:// and/or www
            
            $base_url = preg_replace('/(http:\/\/)?(www\.)?+/i', '', $url);
            
            //compare the urls of the listing and the returned merchant
            if (strstr($m['site_url'], $base_url)){
                $merchant_id = $m['id'];
            }
        }
    }

    if($merchant_id==''){
        return;
    }
 
    if($category!=""){
        //Category list: http://popshops.com/v3/categories.xml?account=73q0rijkutz169vyq4w39zbas&catalog=8u5tah1dl5lf35d4kbmid46wj
        if($category == "men"){
            $popshop_category = "3001";
        }else if ($category=="women"){
            $popshop_category = "3300";
        }else if ($category=="kids-baby"){
            $popshop_category = "3800";
        }else if ($category == "girls"){
            $popshop_category = "3950";
        }else if ($category == "boys"){
            $popshop_category = "3801";
        }else if ($category == "baby"){
            $popshop_category = "32217";
        }
        
        $product_params = array(
            'merchant' => $merchant_id,
            'category'   => $popshop_category
        );
    
    }else{
      $product_params = array(
            'merchant' => $merchant_id
        );  
    }
    
    $products_response = $popshops->getProducts(12, 0, $product_params);
    $products = $products_response["results"]["products"];
    
    // If products response came back with no results and there was a category 
    // applied, remove the category and tr again
    if(empty($products) && sizeof($products)==0 && $popshop_category != ""){
        $product_params = array(
            'merchant' => $merchant_id
        );
        $products_response = $popshops->getProducts(12, 0, $product_params);
        $products = $products_response["results"]["products"];
    }
    
    if(empty($products) && sizeof($products)==0){
        return;
    }
    
    
    /*Put the slick slider initial code here*/
    $response .= '<div class="products-slider-container">';
    $response .= '<div class="slick-slider-products listing-products">';
    
    $num_products = 0;
    
    foreach($products['product'] as $p){
        //echo print_r($p);   
        $product_name = $p["name"];
        if(strlen($product_name)>40){
            $product_name = substr($product_name, 0, 40)."...";
        }
        $product_price = $p["price_min"];
        $product_id = $p["id"];
        $product_url = $p["offers"]['offer'][0]["url"];
        
        $image_url = $p["image_url_large"];
        list($large_image_width, $large_image_height) = getimagesize($image_url);
        
        if($large_image_height >0 && $large_image_width>0){
            $image_height = 205;
            $image_width = (205/$large_image_height)*$large_image_width;
            if($image_width<150){
                $image_width = 150;
            }
            $num_products++;
        }else{
            continue;
        }
            
        
        
        //Format more swiper code for each product here
        $response .= '<div class="slick-slider-product">';
        $response .=    '<a href="'.$product_url.'" target="_blank">';
        $response .=        '<div class="product-image" id="product-image-'.$product_id.'" rel="product-overlay-'.$product_id.'" style="background-image:url('.$image_url.');'
                            . 'height:'.$image_height.'px; '
                            . 'width: '.$image_width.'px;">';
        
        $response .=            '<div class="product-overlay" id="product-overlay-'.$product_id.'" >';
        $response .=            '<div class="title">'.$product_name.'</div>';
        $response .=            '<div class="price">$'.number_format($product_price, 2).'</div>';
        $response .=            '<div class="link">View</div>';
        $response .=            '</div>';
        
        $response .= '      </div>';
        $response .=    '</a>';
        $response .= '</div>';
    }
    
    
    //closing swiper code
    $response .= '</div></div>';
    
    //Product images could all be corrupt and no products should be returned
    if($num_products > 5){
        return $response;
    }else{
        return;
    }
    
   
}



function render_products(){
    global $post;
    
    $retailer_id = 'r'.get_field('shopstyle_retailer_id', $post->id);

    if($retailer_id=='r'){
        return;
    }
    
    $product_params = array(
            'fl' => $retailer_id,
            'sort'  => 'popular'
        );
    $shopstyle = new ShopStyle();
    
    $products_response = $shopstyle->getProducts(12, 0, $product_params);

    $products = $products_response["products"];
    
    
    $response .= '<div class="section-normal ">';
    $response .= '<div class="row blox-row">';       
    $response .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">';
    $response .= '<div class="blox-column-content">';
    $response .= '<div class="blox-element">';
    $response .= '<h3 class="element-title">Popular Products</h3>';
    $response .= '<div class="blox-element blox-carousel swiper-container">';
    $response .= '<div class="blox-element grid-loop portfolio swiper-wrapper boxed" >';
    

    
    foreach($products as $p){
        
        $product_name = $p["name"];
        $product_price = $p["$price"];
        $product_url = $p["clickUrl"];
    
        $product_images = $p["image"];
        $image_sizes = $product_images["sizes"];
        
        
        $image = $image_sizes["Large"];
        $image_url = $image["url"];
        $image_height = $image["height"];
        $image_width = $image["width"];
        
        $response .= '<div class="post_filter_item col-md-3 col-sm-6 col-xs-12 swiper-slide swiper-slide-visible swiper-slide-active">';
        $response .=    '<article class="entry">';
        $response .=        '<div class="entry-media"">';
        $response .=            '<a href="'.$product_url.'" target="_blank"><img src="'.$image_url.'" class="carousel-image" style="max-height:150px; width:auto; margin:auto; "/></a>';
        /*
        if ($image_height>$image_width){
            $response .=            '<a href="'.$product_url.' target="_blank""><img src="'.$image_url.'" class="carousel-image" style="height:150px; width:auto; margin-left: auto; margin-right:auto;"/></a>';
        }else{
            $response .=            '<a href="'.$product_url.' target="_blank""><img src="'.$image_url.'" class="carousel-image" style="width:150px; height:auto; margin-left: auto; margin-right:auto;"/></a>';
        }*/
        $response .=        '</div>';
        $response .=    '</article>';
        $response .= '</div>';
        
    }
    
    $response .= '</div>';
    $response .= '<div class="carousel-control-prev"><i class="fa-angle-left"></i></div>';
    $response .= '<div class="carousel-control-next"><i class="fa-angle-right"></i></div>';
    $response .= '</div>';
    $response .= '</div>';
    $response .= '</div>';
    $response .= '</div>';
    $response .= '</div>';
    $response .= '</div>';
    
    return $response;
}


function render_price_field($post_id=''){
    global $post;
    if($post_id==''){
        $post_id=$post->ID;
    }
    
    /*Price info*/
    $prices = get_field_object('price', $post_id);  //returns the array of all key-value pairs, along with the selected value
    $price = get_field('price', $post_id);    //returns an array of all selected values

    $n = count($price);
    $result = $prices['choices'][$price[0]];

    if($n > 1):
        $result .= " - ";
        $result .= $prices['choices'][$price[$n-1]];

    endif;

    return $result;   
}

function render_listing_highlights(){
    global $post;
    
    $shipping = get_shipping_info($post->ID, 'highlight');
    $returns = get_return_shipping_info();
    $prices = render_price_field();
    
    $return .= '<div class="listing-highlights flex">';
    $return .= '<div class="listing-highlight "><div class="listing-highlight-item flex" >'.$prices.'</div></div>';
    $return .= '<div class="listing-highlight-spacer flex" ><i class="spacer">•</i></div>';
    $return .= '<div class="listing-highlight "><div class="listing-highlight-item flex"><i class="icon-plane"></i>'.$shipping.'</div></div>';
    $return .= '<div class="listing-highlight-spacer flex" ><i class="spacer">•</i></div>';
    $return .= '<div class="listing-highlight "><div class="listing-highlight-item flex"><i class="icon-refresh"></i>'.$returns.'</div></div>';
    $return .= '</div>';
    
    return $return;
    
    
}
function render_shipping_info($listing_id='', $context=''){

    $return = '';
    $shipping_info = get_shipping_info($listing_id, $context); 
    $return .= '<div class="shipping_info">' . $shipping_info . '</div>';
    
    echo $return;
}

function get_shipping_info($post_id='', $context=''){
    global $post;
    if($post_id==''){
        $post_id = $post->ID;
    }
    
    $shipping = get_field('shipping', $post_id);
    $shipping_cost = get_field('shipping_cost', $post_id);
    $shipping_time = get_field('standard_delivery_time', $post_id);    
     
    if(!$shipping){
        return;
    }
    
    if ( $shipping == "ship_free" ):
            $shipping_info = 'Free Shipping';
    elseif ( $shipping == "ship_min" ):
            $shipping_info = 'Free Shipping, $' . get_field('free_shipping_minimum_amount', $post_id) . '+ orders';
            if($context!='highlight'): $shipping_info.='<br/>Standard Shipping: $' . $shipping_cost ;endif;
    elseif ( $shipping == "ship_flat" ):
            $shipping_info = 'Standard shipping: $' . $shipping_cost ;
    else:
            $shipping_info = 'Shipping cost increases with order size';
    endif; 
    
    if($context!='highlight'){
        if(get_field('have_standard_delivery_time')){
            $shipping_min = min($shipping_time);
            $shipping_max = max($shipping_time);
            if($shipping_min != $shipping_max){
                $shipping_info .= '<br/>Arrives in '.$shipping_min . " - " . $shipping_max." business days";
            }else{
                $shipping_info .= '<br/>Arrives in '.$shipping_min. " business days";
            }
                
        }
        $shipping_notes = get_field('shipping_notes');
        if($shipping_notes!=''){
            $shipping_info .= '<br/>'.$shipping_notes;
        }
    }
    
    return $shipping_info;
}

function render_return_shipping_info($listing_id=''){
    
     /*Return Shipping Info*/
    
    $return_shipping_info = get_return_shipping_info($listing_id);
    $return .= '<div class="shipping_info" >' . $return_shipping_info . '</div>';
    
    echo $return;
}

function get_return_shipping_info($post_id=''){
    global $post;
    if($post_id==''){
        $post_id=$post->ID;
    }
    
    $return_shipping = get_field('return_shipping', $post_id);
    
    
    if(!$return_shipping){
        return;
    }
    if ( $return_shipping == "return_free" ):
        $return_shipping_info = 'Free Returns';
    elseif ( $return_shipping =="return_flat" ):
        $return_shipping_info =  'Flat rate return fee $' . get_field('return_shipping_cost', $post_id);
    else:
        $return_shipping_info = 'Buyer handles return shipping';
    endif;
    
    $return_policy = get_field('return_policy', $post_id);
    if ($return_policy>0){
        $return_shipping_info .= "<br/>".$return_policy." Days to Return";
    }
    
    
    
    return $return_shipping_info;
}

function render_canada_shipping(){
    $can_shipping = '';
    $can_shipping .= '<div class="wpbdp-listing-can-shipping-info">';
    $can_shipping .= '<label class="element-title"><i class="icon-globe"></i> Shipping to Canada:</label>';
    $can_shipping .= '<div class="shipping_info" >';
    
    $can_shipping_cost = get_field('shipping_cost_to_canada');
    if(get_field('ships_to_canada')){
        $can_taxes_duties = get_field('taxes_and_duties');
        
        if(strlen($can_shipping_cost) !=0){
            if($can_shipping_cost==0){
                $can_shipping .= 'Free Shipping';
            }else{
                $can_shipping .= '$';
                $can_shipping .= number_format($can_shipping_cost, 2);
                $can_shipping .= ' shipping fee';
            }
            if($can_taxes_duties){
                $can_shipping .= '. Customs & import duties extra.';
            }else{
                $can_shipping .= '. Customs & import duties included.';
            }
        }else{
            //We know that they ship to canada, but don't have costs
            $can_shipping .= 'Ships to Canada.';
        }
        
        $can_shipping_notes = get_field('canada_shipping_notes');
        if($can_shipping_notes!=''){
            $can_shipping .= "<br/>".$can_shipping_notes;
        }
    }else{
        $can_shipping .= 'Does not ship to Canada.';
    }
    
    $can_shipping .= '</div></div>';
    return $can_shipping;
}

function render_international_shipping(){
    $int_shipping = '';
    $int_shipping .= '<div class="wpbdp-listing-int-shipping-info">';
    $int_shipping .= '<label class="element-title"><i class="icon-globe"></i> International Shipping:</label>';
    $int_shipping .= '<div class="shipping_info" >';
    
    if(get_field('international_shipping')){
        $int_shipping .= 'Ships Internationally';
        $int_shipping .= $can_shipping_cost;
        $int_shipping_notes = get_field('international_shipping_notes');
        if($int_shipping_notes!=''){
            $int_shipping .= "<br/>".$int_shipping_notes;
        }
    }else{
        $int_shipping .= 'Does not ship Internationally';
    }
    
    $int_shipping .= '</div></div>';
    return $int_shipping;
}

/*
 * @param string $type accepts values of "men", "women" or "kids" to specify which
 * version of the "good for" tag to return;
 */
function render_good_for($type){
    $good_for_object = get_field_object('good_for_'.$type);
    $good_for_values = $good_for_object['value'];
    
    if(!empty($good_for_values)){
        foreach($good_for_values as $g){
            $values[] = $good_for_object['choices'][$g];
        }
        $return = '<tr class="listing-tag-row"><td colspan="2"><i class="icon-tag"></i>&nbsp;&nbsp;&nbsp; '.implode(", ", $values)."</td></tr>";
    }

    return $return;
}
function render_category_size_info(){
    
}

function render_customer_support_phone(){
   
    $support_phone = get_field('support_phone');
    if(!empty($support_phone)) $html.= "<div class='listing-category-label'><i class='fa fa-phone'></i> Customer Service Phone Number: </div><div class='listing-category-values'>".$support_phone."</div>";
    return $html;
}

function render_customer_support_email(){
    $support_email = get_field('support_email');
    if(!empty($support_email)) {
        $html.= "<div class='listing-category-label'>"
               . "  <i class='icon-envelope-open'></i> Customer Service Email: "
               . "</div>";
    
        
        if(stristr($support_email, "http://") || stristr($support_email, "https://")){
            $html.= "<div class='listing-category-values'>"
    . "<a href='".$support_email."' target=_blank>Contact Form</a></div>";
        }else{
            $html.= "<div class='listing-category-values'>"
                   . "<a href='mailto:".$support_email."'>".$support_email."</a></div>";

        }
    }
    return $html;
}

function render_delivery_time(){
    if(get_field('have_standard_delivery_time')){
        
        $delivery_time = get_field('standard_delivery_time');
        $html = '<div class="listing-category-label"><i class="fa fa-truck"></i> US Delivery Time</div>';
        $html .= $delivery_time . " Days";
    }else{
        $html = "";
    }
    
    return $html;
}

function render_shipping_notes(){
    
}

/*
 * Returns the URL for the listing specified, with the Shopstyle tracking attached.
 * If you need to add tracking to a specific category URL, it can be passed in as a parameter.
 */

function get_shopstyle_retailer_url($listing_id, $category_url = ""){
    
    $shopstyle = new ShopStyle();
    $host = $shopstyle->getHost();
    $API_key = $shopstyle->getApiKey();
    
    if($category_url==""){
        $listing_url = wpbdp_render_listing_field('URL', $listing_id);
        $listing_url = esc_url($listing_url[0]);
    }else{
        $listing_url = $category_url;
    }
    $return = esc_url("http://".$host."/action/apiVisitRetailer?url=".$listing_url."&pid=".$API_key);
    
    return $return;
    
}

function set_thumbnail_id( $listing_id, $image_id, $thumb_type = FALSE) {
    if(!$listing_id){
        return;
    }
    //echo $thumb_type."farts";
    if ( ! $image_id )
        return delete_post_meta( $listing_id, '_wpbdp[thumbnail_id]' );
    
    if(! $thumb_type){
        return update_post_meta( $listing_id, '_wpbdp[thumbnail_id]', $image_id );
    }else if($thumb_type == "women"){
        return update_post_meta( $listing_id, '_wpbdp[womens_thumb_id]', $image_id );
    }else if($thumb_type == "men"){
        return update_post_meta( $listing_id, '_wpbdp[mens_thumb_id]', $image_id );
    }else if($thumb_type == "kids"||$thumb_type == "kids-baby"){
        return update_post_meta( $listing_id, '_wpbdp[kids_thumb_id]', $image_id );
    }else if($thumb_type == "girls"){
        return update_post_meta( $listing_id, '_wpbdp[girls_thumb_id]', $image_id );
    }else if($thumb_type == "boys"){
        return update_post_meta( $listing_id, '_wpbdp[boys_thumb_id]', $image_id );
    }else if($thumb_type == "baby"){
        return update_post_meta( $listing_id, '_wpbdp[baby_thumb_id]', $image_id );
    }else{
        return;
    }
    
}

function get_thumbnail_id($listing_id, $thumb_type = "") {
    if($thumb_type =="women"){
        $thumbnail_id = get_post_meta( $listing_id, '_wpbdp[womens_thumb_id]', true );
    }else if ($thumb_type == "men"){
        $thumbnail_id = get_post_meta( $listing_id, '_wpbdp[mens_thumb_id]', true );
    }else if ($thumb_type == "kids-baby"){
        $thumbnail_id = get_post_meta( $listing_id, '_wpbdp[kids_thumb_id]', true );
    }else if ($thumb_type == "girls"){
        $thumbnail_id = get_post_meta( $listing_id, '_wpbdp[girls_thumb_id]', true );
    }else if ($thumb_type == "boys"){
        $thumbnail_id = get_post_meta( $listing_id, '_wpbdp[boys_thumb_id]', true );
    }else if ($thumb_type == "baby"){
        $thumbnail_id = get_post_meta( $listing_id, '_wpbdp[baby_thumb_id]', true );
    }else if($thumb_type == ""){
        $thumbnail_id = get_post_meta($listing_id, '_wpbdp[thumbnail_id]', true );
    }

    if ( $thumbnail_id ) {
        if ( false !== get_post_status( $thumbnail_id ) )
            return intval( $thumbnail_id );
    }

    if ( $thumb_type=="" && $images = get_attached_media('image/jpeg', $listing_id) ) {
        update_post_meta( $listing_id, '_wpbdp[thumbnail_id]', $images[0]->ID );
        $num_images = sizeof($images);
        return $images[num_images-1]->ID;
    }
 

    return 0;
}


function wpbdp_latest_listings($n=10, $before='<ul>', $after='</ul>', $before_item='<li>', $after_item = '</li>') {
    $n = max(intval($n), 0);

    $posts = get_posts(array(
        'post_type' => WPBDP_POST_TYPE,
        'post_status' => 'publish',
        'numberposts' => $n,
        'orderby' => 'date'
    ));

    $html = '';

    $html .= $before;

    foreach ($posts as $post) {
        $html .= $before_item;
        $html .= sprintf('<a href="%s">%s</a>', get_permalink($post->ID), get_the_title($post->ID));
        $html .= $after_item;
    }

    $html .= $after;

    return $html;
}

function _wpbdp_template_mode($template) {
    if ( wpbdp_locate_template(array('businessdirectory-' . $template, 'wpbusdirman-' . $template), true, false) )
        return 'template';
    return 'page';
}

/**
 * Checks if permalinks are enabled.
 * @return boolean
 * @since 2.1
 */
function wpbdp_rewrite_on() {
    global $wp_rewrite;
    return $wp_rewrite->permalink_structure ? true : false;
}

/**
 * Checks if a given user can perform some action to a listing.
 * @param string $action the action to be checked. available actions are 'view', 'edit', 'delete' and 'upgrade-to-sticky'
 * @param (object|int) $listing_id the listing ID. if null, the current post ID will be used
 * @param int $user_id the user ID. if null, the current user will be used
 * @return boolean
 * @since 2.1
 */
function wpbdp_user_can($action, $listing_id=null, $user_id=null) {
    $listing_id = $listing_id ? ( is_object($listing_id) ? $listing_id->ID : intval($listing_id) ) : get_the_ID();
    $user_id = $user_id ? $user_id : wp_get_current_user()->ID;
    $post = get_post($listing_id);

    if ($post->post_type != WPBDP_POST_TYPE)
        return false;

    if ( isset($_GET['preview']) )
        return false;

    switch ($action) {
        case 'view':
            return true;
            break;
        case 'edit':
        case 'delete':
            return user_can($user_id, 'administrator') || ($post->post_author == $user_id);
            break;
        case 'upgrade-to-sticky':
            if ( !wpbdp_get_option('featured-on') || !wpbdp_get_option('payments-on') )
                return false;

            if ( !wpbdp_payments_possible() )
                return false;

            $sticky_info = wpbdp_listing_upgrades_api()->get_info( $listing_id );
            return $sticky_info->upgradeable && (user_can($user_id, 'administrator') || ($post->post_author == $user_id));
            break;
    }

    return false;
}

function wpbdp_get_post_by_slug($slug, $post_type=null) {
    $post_type = $post_type ? $post_type : WPBDP_POST_TYPE;

    $posts = get_posts(array(
        'name' => $slug,
        'post_type' => $post_type,
        'post_status' => 'publish',
        'numberposts' => 1
    ));

    if ($posts)
        return $posts[0];
    else
        return 0;
}

function wpbdp_get_current_sort_option() {
    $obj = new StdClass();
    if ($sort = trim(wpbdp_getv($_GET, 'wpbdp_sort', null))) {
        $order = substr($sort, 0, 1) == '-' ? 'DESC' : 'ASC';
        $sort = ltrim($sort, '-');

        
        $obj->option = $sort;
        $obj->order = $order;

        return $obj;
    }else{
        $sort = 'rating';
        $order = 'DESC';
        
        $obj->option = $sort;
        $obj->order = $order;
        return $obj;
    }

    return null;
}

/*
 * @since 2.1.6
 */
function _wpbdp_resize_image_if_needed($id) {
    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    if ( $metadata = wp_get_attachment_metadata( $id ) ) {
        if ( !isset( $metadata['sizes']['wpbdp-thumb'] ) || !isset( $metadata['sizes']['wpbdp-thumb'] ) || 
            (isset($metadata['sizes']['wpbdp-thumb']) && (abs( intval($metadata['sizes']['wpbdp-thumb']['width']) - intval( wpbdp_get_option( 'thumbnail-width' ) ) ) >= 15) ) ) {
            wpbdp_log( sprintf( 'Re-creating thumbnails for attachment %d', $id ) );
            $filename = get_attached_file($id, true);
            $attach_data = wp_generate_attachment_metadata( $id, $filename );
            wp_update_attachment_metadata( $id,  $attach_data );
        }
    }
}

/*
 * @since 2.1.7
 */
function wpbdp_format_currency($amount, $decimals = 2, $currency = null) {
    if ( $amount == 0.0 )
        return '—';
    
    return ( ! $currency ? wpbdp_get_option( 'currency-symbol' ) : $currency ) . ' ' . number_format( $amount, $decimals );
}


/**
 * @since 2.3
 */
function wpbdp_has_module( $module ) {
    global $wpbdp;
    return $wpbdp->has_module( $module );
}

/*
 * Get the URL to the store, attaching any tracking if necessary and limiting
 * the length of the URL, if necessary (given context).
*/

function get_listing_outbound_link($id, $max_length = 0, $category = ""){
    if($category == "Women"){
        $cat_url = get_field('womens_url', $id);
    }else if($category == "Men"){
        $cat_url = get_field('mens_url', $id);
    }else if($category == "Kids & Baby"){
        $cat_url = get_field('kids_url', $id);
    }else if($category == "Girls"){
        $cat_url = get_field('girls_url', $id);
    }else if($category == "Boys"){
        $cat_url = get_field('boys_url', $id);
    }else if($category == "baby"){
        $cat_url = get_field('Baby_url', $id);
    }
    
    $base_url = wpbdp_render_listing_field('URL', $id);
    
    if(is_array($base_url)){
        $link_text = $base_url[1];
        $base_url = $base_url[0];
    }
    
    $dubdub = strpos($link_text, "www.");
    
    if ( $dubdub ===FALSE){
        $no_dub_url = $link_text;
        
    }else{
       $no_dub_url = substr($link_text,$dubdub+4); 
    }
    if($max_length == 0){
        if(strlen($no_dub_url)>17){
            $no_dub_url = substr($no_dub_url, 0, 17)."...";
        }
    }else{
        if(strlen($no_dub_url)>50){
            $no_dub_url = substr($no_dub_url, 0, $max_length)."...";
        }
    }
    /*
    if((get_shopstyle_retailer_id($id))!=''){
        if($cat_url!=""){
            $listing_url = '<a href="'.get_shopstyle_retailer_url($id, $cat_url).'" target="_blank"><i class="fa fa-external-link"></i>&nbsp;&nbsp;&nbsp;Visit '.$no_dub_url.'</a>';
        }else{
            $listing_url = '<a href="'.get_shopstyle_retailer_url($id).'" target="_blank"><i class="fa fa-external-link"></i>&nbsp;&nbsp;&nbsp;Visit '.$no_dub_url.'</a>';
        }
    }else{
     * 
     */
        if($cat_url!=""){
            if (strcmp(substr($cat_url, 0, 7), "http://") !=0){
                $url = "http://" . $cat_url;
            }else{
                $url = $cat_url;
            }
        }else{
            if (strcmp(substr($base_url, 0, 7), "http://") !=0){
                $url = "http://" . $base_url;
            }else{
                $url = $base_url;
            }
        }
        $listing_url = '<a href="'.$url.'" target="_blank"><i class="fa fa-external-link"></i>&nbsp;&nbsp;&nbsp;&nbsp;Visit '.$no_dub_url.'</a>';
    /*
     * }
     * 
     */
    
    

    return $listing_url;
}

function get_listing_outbound_url($id){
    $base_url = wpbdp_render_listing_field('URL', $id);
    $link_text = $base_url[1];
    $base_url = $base_url[0];
    $dubdub = strpos($base_url, "www.");
    
    if ( $dubdub ===FALSE){
        $no_dub_url = $base_url;
        
    }else{
       $no_dub_url = substr($base_url,$dubdub+4); 
    }
    
    if((get_shopstyle_retailer_id($id))!=''){
        $listing_url = get_shopstyle_retailer_url($id);
    }else{
        
        if (strcmp(substr($base_url, 0, 7), "http://") !=0){
            $url = "http://" . $base_url;
        }else{
            $url = $base_url;
        }
        $listing_url = $url;
    }
    return $listing_url;
}


function carnaby_display_listing_excerpt($title, $posts){
    if ( ! empty( $title ) ) echo $title;

    echo '<div class="box-listing-excerpt">';
    foreach ($posts as $post) {
        $thumbnail = wpbdp_listing_thumbnail( $post->ID, 'link=listing' );
        $rating = wpbdp_render_listing_field_html('Rating (average)', $post->ID);

        $listing_url = get_listing_outbound_url($post->ID);
        if (function_exists('wpfp_link')) { 
            $favorite_link = wpfp_link(1, "", 0, array(), $post->ID); 
        }

        echo '<div>';
        if ( $thumbnail )
            echo $thumbnail;
        echo sprintf( '<div><a href="%s">%s</a></div>', get_permalink( $post->ID ), get_the_title( $post->ID ) );
        echo '<div>'.$listing_url.'</div>';
        echo '<div class="favorite-icon">'.$favorite_link.'</div>';
        echo '<div class="listing-rating">'.$rating.'</div>';
        echo '<div class="listing-price">'. render_price_field($post->ID).'</div>';
        echo '<div class="listing-shipping">'.get_shipping_info('highlight', $post->ID).'</div>';
        echo '<div class="listing-return-shipping">'.get_return_shipping_info($post->ID).'</div>';

        echo '</div>';
    }

    echo '</div>';

}

function support_email_address(){
    return "support@carnabywest.com";
}

/* Takes an array of key-value pairs from URL parameters and returns an array of
 * key-value pairs with the user-readable labels  */

function get_field_labels($url_parameters){
    $readable = array();
    
    if (!empty($url_parameters)){
        
        foreach($url_parameters as $key => $values_string){
            
            if (strpos($key, SF_TAX_PRE) === 0){
                //$field_object = get_field_object(substr($key, strlen(SF_TAX_PRE)));
                //$field = get_field(substr($key, strlen(SF_TAX_PRE)));
                $key = substr($key, strlen(SF_TAX_PRE));
            }else if(strpos($key, SF_META_PRE) === 0){
                $key = substr($key, strlen(SF_META_PRE));
                //$field_object = get_field_object(substr($key, strlen(SF_META_PRE)));
                //$field = get_field(substr($key, strlen(SF_META_PRE)));
            }else{
                //not a filter field
            }
            if(is_array($values_string)){
                $values_array = $values_string;
            }else{
                $values_array = (preg_split("/[,\+\- ]+/", esc_attr(($values_string)))); //explode with 2 delims
            }
            
            $readable_values = array();
            if($key=="price"){
                
                foreach($values_array as $value){
                    if($value=="1$"){
                        $readable_values[] = "$"; 
                    }elseif($value=="2$"){
                        $readable_values[] = "$$";
                    }elseif($value=="3$"){
                        $readable_values[] = "$$$";
                    }elseif($value=="4$"){
                        $readable_values[] = "$$$$";
                    }
                }
                if(!empty($readable_values)){
                    $readable['Price'] .= implode(", ", $readable_values);
                }
            }elseif($key=="shipping"){
                foreach($values_array as $value){
                    if($value=="ship_free"){
                        $readable_values[] = "Free Shipping"; 
                    }elseif($value=="ship_flat"){
                        $readable_values[] = "Flat Rate Shipping";
                    }elseif($value=="ship_min"){
                        $readable_values[] = "Free Shipping with Min. Order";
                    }elseif($value=="ship_increase"){
                        $readable_values[] = "Shipping Increases with Order";
                    }
                }
                if(!empty($readable_values)){
                    $readable['Shipping'] .= implode(", ", $readable_values);
                }
            }elseif($key=="good_for_women" || $key=="good_for_men" || $key=="good_for_kids"){
                foreach($values_array as $value){
                    if($value=="jewelry_and_accessories"){
                        $readable_values[] = "Accessories & Jewelry";
                    }elseif($value=="bags_and_handbags"){
                        $readable_values[] = "Bags & Handbags";
                    }elseif($value=="bags"){
                        $readable_values[] = "Bags";     
                    }elseif($value=="basics"){
                        $readable_values[] = "Basics"; 
                    }elseif($value=="beach_vacation"){
                        $readable_values[] = "Swim";
                    }elseif($value=="casual_weekend"){
                        $readable_values[] = "Casual Weekend";
                    }elseif($value=="denim"){
                        $readable_values[] = "Denim";
                    }elseif($value=="discount_shopping"){
                        $readable_values[] = "Discount Shopping";
                    }elseif($value=="everyday_dressing"){
                        $readable_values[] = "Everyday Apparel";
                    }elseif($value=="formal_and_semiformal_wear"){
                        $readable_values[] = "Formal & Semiformal Wear";
                    }elseif($value=="fast_fashion"){
                        $readable_values[] = "Fast Fashion";
                    }elseif($value=="lingerie"){
                        $readable_values[] = "Lingerie";
                    }elseif($value=="maternity"){
                        $readable_values[] = "Maternity";
                    }elseif($value=="outdoor_adventures"){
                        $readable_values[] = "Outdoor Adventures";
                    }elseif($value=="outerwear"){
                        $readable_values[] = "Outerwear";
                    }elseif($value=="shoes"){
                        $readable_values[] = "Shoes";
                    }elseif($value=="socks_and_hosiery"){
                        $readable_values[] = "Socks & Hosiery";
                    }elseif($value=="socks"){
                        $readable_values[] = "Socks";    
                    }elseif($value=="swim"){
                        $readable_values[] = "Swim";
                    }elseif($value=="underwear"){
                        $readable_values[] = "Underwear";    
                    }elseif($value=="wear_to_work"){
                        $readable_values[] = "Wear to Work";
                    }elseif($value=="weddings"){
                        $readable_values[] = "Weddings";
                    }elseif($value=="workout_wear"){
                        $readable_values[] = "Workout Wear";
                    }
                }
                if(!empty($readable_values)){
                    $readable['Store Features'] .= implode(", ", $readable_values);
                }
            }elseif($key=="return_shipping"){
                foreach($values_array as $value){
                    if($value=="return_free"){
                        $readable_values[] = "Free Returns"; 
                    }elseif($value=="return_flat"){
                        $readable_values[] = "Flat Rate Returns";
                    }elseif($value=="return_customer"){
                        $readable_values[] = "Customer Handles Return";
                    }
                }
                if(!empty($readable_values)){
                    $readable['Return Shipping'] .= implode(", ", $readable_values);
                }
            }elseif($key=="international_shipping"){
                foreach($values_array as $value){
                    if($value==true){
                        $readable_values[] = "Ships Overseas"; 
                    }
                }
                if(!empty($readable_values)){
                    $readable['International Shipping'] .= implode(", ", $readable_values);
                }
            }elseif($key=="ships_to_canada"){
                foreach($values_array as $value){
                    if($value==true){
                        $readable_values[] = "Ships To Canada"; 
                    }
                }
                if(!empty($readable_values)){
                    $readable['Canada Shipping'] .= implode(", ", $readable_values);
                }
            }elseif($key=="shipping_cost_to_canada"){
                foreach ($values_array as &$value){
                    $value = '$'.$value;
                }
                if(!empty($values_array)){
                    $readable['Canada Shipping Cost'] .= implode(" - ", $values_array);
                }
                
            }elseif($key=="womens_extended_sizes"){
                foreach($values_array as $value){
                    if($value=="plus_size"){
                        $readable_values[] = "Plus Size"; 
                    }elseif($value=="petites"){
                        $readable_values[] = "Petites";
                    }elseif($value=="tall"){
                        $readable_values[] = "Tall";
                    }elseif($value=="maternity"){
                        $readable_values[] = "Maternity";
                    }
                }
                if(!empty($readable_values)){
                    $readable['Women\'s Extended Sizes'] .= implode(", ", $readable_values);
                }
            }elseif($key=="mens_extended_sizes"){
                foreach($values_array as $value){
                    if($value=="slim"){
                        $readable_values[] = "Slim"; 
                    }elseif($value=="tall"){
                        $readable_values[] = "Tall";
                    }elseif($value=="big_and_tall"){
                        $readable_values[] = "Big & Tall";
                    }
                }
                if(!empty($readable_values)){
                    $readable['Men\'s Extended Sizes'] .= implode(", ", $readable_values);
                }
            }
     
        }
    }
    return $readable;
}


/*
 * Displays the registration modal with additional specifications
 * 
 * @param string $message: message to display to user about why they need register
 * @param string $modal_class: additional classes to add the the containing modal
 * @param string $modal_hidden: whether to hide the modal initially
 * @param string $modal_style: additional styles to add to modal
 * @param string $display_form: takes values of "reg" or "login" depending on which form to display 
 * 
 */

function get_registration_modal($message = '', $modal_class='', $modal_hidden='false', $modal_style='', $display_form='reg'){
    
    if($display_form=='login'){
        $reg_modal = '';
        $login_modal = 'active';
    }else{
        $reg_modal = 'active';
        $login_modal = '';
    }
    

    $html .= '<div class="modal fade '.$modal_class.'" '.$modal_style.' id="registrationModal" tabindex="-1" role="dialog" aria-labelledby="registrationModalLabel" aria-hidden="'.$modal_hidden.'">
                <div class="modal-dialog" id="reg-modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="reg_form_wrapper" id="reg_form_wrapper">
                            <div class="flip-form registration-form registration-form-embed review-action  '.$reg_modal.'">';
    if($message!=''){
    $html .= '                   <div class="alert alert-warning text-center" id="reg-alert-warning">'.
                                    $message.
                                '</div>';
    }
    
    $html .= do_shortcode("[usersultra_registration]");
    $html .= '              </div>
                            <div class="flip-form login-form login-form-embed'.$login_modal.'" id="login-alert-warning">';

    if($message!=''){
        $html .= '              <div class="alert alert-warning text-center" id="reg-alert-warning">'.
                                    $message.
                                '</div>';
    }
    $html .= do_shortcode("[usersultra_login]"); 
    $html .= '              </div>
                        </div>            
                    </div>
                </div> 
            </div>';
    
    return $html;
}