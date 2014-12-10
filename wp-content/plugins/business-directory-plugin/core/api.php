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
function wpbdp_render_listing($listing_id=null, $view='single', $echo=false) {
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
        $html = _wpbdp_render_excerpt();
    else
        $html = _wpbdp_render_single();

    if ($listing_id)
        wp_reset_query();

    if ($echo)
        echo $html;

    return $html;
}

function _wpbdp_render_single() {
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
    $thumbnail_id = wpbdp_listings_api()->get_thumbnail_id($post->ID);
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

    $vars = array(
//        'actions' => wpbdp_render('parts/listing-buttons', array('listing_id' => $post->ID, 'view' => 'single'), false),
        'is_sticky' => $sticky_status == 'sticky',
        'sticky_tag' => $sticky_tag,
        'title' => get_the_title(),
        //'main_image' => wpbdp_get_option( 'allow-images' ) ? wpbdp_listing_thumbnail( null, 'link=picture&class=wpbdp-single-thumbnail' ) : '',
        'main_image' => wpbdp_get_option( 'allow-images' ) ? wpbdp_listing_main_image( null, 'link=picture&class=wpbdp-single-thumbnail', 'large' ) : '',
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

function _wpbdp_render_excerpt() {
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
    $listing_url = wpbdp_render_listing_field('URL');
    
    //$g = WPDP_ListingFieldDisplayItem::

    $vars = array(
        'is_sticky' => $sticky_status == 'sticky',
        //'thumbnail' => listing_thumbnail_screenshot($listing_url),
        'thumbnail' => ( wpbdp_get_option( 'allow-images' ) && wpbdp_get_option( 'show-thumbnail' ) ) ? wpbdp_listing_thumbnail( null, 'link=listing&class=wpbdmthumbs wpbdp-excerpt-thumbnail' ) : '',
        'title' => get_the_title(),
        'listing_fields' => apply_filters('wpbdp_excerpt_listing_fields', $listing_fields, $post->ID),
        'fields' => $d->fields,
        'listing_id' => $post->ID
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

function wpbdp_render_listing_field_html($field_name) {
    global $post;
    
    $html = '';
    
    $d = WPBDP_ListingFieldDisplayItem::prepare_set( $post->ID, 'listing' );
    $html .= implode( '', WPBDP_ListingFieldDisplayItem::get_field( 'html', $d->fields, $field_name ));
   return $html;
}


/*
 * Same as above function, returns without formatted HTML
 */
function wpbdp_render_listing_field($field_name) {
    global $post;
    
    $html = '';
    
    $d = WPBDP_ListingFieldDisplayItem::prepare_set( $post->ID, 'listing' );
    $html .= implode( '', WPBDP_ListingFieldDisplayItem::get_field( 'value', $d->fields, $field_name ));
    return $html;
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
    
    $html.="<div class = 'side-panel-group'>";
    
    if(!empty($womens_categories)):
         
        $html.= "<div class='listing-side-container'> 

                    <div class='listing-side-container-circle-heading col-md-3 col-xs-3'  data-animate='flipInY' style='-webkit-animation: 0.1s; -webkit-animation-name:flipInY'>
                        Women
                    </div>
                    
                    <div class='listing-side-container-info col-md-9 col-xs-9'>
                        <div class='panel-body'>
                            <table class='listing-cat-info'>";
        
        $womens_style = array();
        $womens_category_links = array();
        $womens_extended_sizes = array();
        
        $women_style = get_field('womens_style');
        
        $womens_extended_sizes = get_field("womens_extended_sizes");
        $womens_sizes = get_field('womens_sizes');
        
        
        foreach($womens_categories as $wc){
            $url = '/business-directory/site_categories/' . $wc->slug . '/';
            $link = "<a href='" . $url . "'>" . $wc->name . "</a>";
            $womens_category_links[] = $link;
        }
        
        //display all the womens categories for this store
        if(!empty($womens_category_links)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Categories: </td>';
            $html .= '<td class="listing-category-values">'.implode( ', ', $womens_category_links ).'</td>';
            $html .= '</tr>';
        }

         
        //display all the style categories for this store
        if(!empty($women_style)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Style: </td>';
            $html .= '<td class="listing-category-values">'.implode(', ', $women_style).'</td>';
            $html .= '</tr>';
        }

         
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
        
        $html.=         "</div>
                    </div>     
                </div>";
        //$html .= "<br /><br />";
        
    endif;
    
    if(!empty($mens_categories)):
        
        
        $html.= "<div class='listing-side-container'> 
                    <div class='listing-side-container-circle-heading  col-md-3 col-xs-3'>
                        Men
                    </div>
                    
                    <div class='listing-side-container-info col-md-9 col-xs-9'>
                        <div class='panel-body'>
                            <table class='listing-cat-info'>";
    
        $mens_style = array();
        $mens_category_links = array();
        $mens_extended_sizes = array();
        
        $men_style = get_field('mens_style');
        $mens_extended_sizes = get_field("mens_extended_sizes");
        $mens_sizes = get_field("mens_sizes");
        //if(!empty($mens_style)) $html .= implode(', ', $men_style);
        
        
        foreach($mens_categories as $mc){
            $url = '/business-directory/site_categories/' . $mc->slug . '/';
            $link = "<a href='" . $url . "'>" . $mc->name . "</a>";
            $mens_category_links[] = $link;
        }
        
        
        //display all the womens categories for this store
        if(!empty($mens_category_links)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Categories: </td>';
            $html .= '<td class="listing-category-values">'.implode( ', ', $mens_category_links ).'</td>';
            $html .= '</tr>';
        }
        //display all the style categories for this store
        if(!empty($men_style)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Style: </td>';
            $html .= '<td class="listing-category-values">'.implode(', ', $men_style).'</td>';
            $html .= '</tr>';
        }
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
        $html.=         "</div>
                    </div>     
                </div>";
         
    endif;
    
    if(!empty($kids_categories)):
        $html.= "<div class='listing-side-container'> 
                    <div class='listing-side-container-circle-heading  col-md-3 col-xs-3'>
                        Kids & Baby
                    </div>
                    
                    <div class='listing-side-container-info col-md-9 col-xs-9'>
                        <div class='panel-body'>
                            <table class='listing-cat-info'>";
        
        //Need to make label disappear if there are no values
        $kids_style = array();
        $kids_category_links = array();
        $kids_style = get_field('kids-baby_style');
        
        
        foreach($girls_categories as $gc){
            $url = '/business-directory/site_categories/' . $gc->slug . '/';
            $link = "<a href='" . $url . "'>" . $gc->name . "</a>";
            $girls_category_links[] = $link;
        }
        //display all the womens categories for this store
        if(!empty($girls_category_links)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Girls: </td>';
            $html .= '<td class="listing-category-values">'.implode( ', ', $girls_category_links ).'</td>';
            $html .= '</tr>';
        }
        
        foreach($boys_categories as $bc){
            $url = '/business-directory/site_categories/' . $bc->slug . '/';
            $link = "<a href='" . $url . "'>" . $bc->name . "</a>";
            $boys_category_links[] = $link;
        }
        
        //display all the womens categories for this store
        if(!empty($boys_category_links)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Boys: </td>';
            $html .= '<td class="listing-category-values">'.implode( ', ', $boys_category_links ).'</td>';
            $html .= '</tr>';
        }
        
        foreach($baby_categories as $bbc){
            $url = '/business-directory/site_categories/' . $bbc->slug . '/';
            $link = "<a href='" . $url . "'>" . $bbc->name . "</a>";
            $baby_category_links[] = $link;
        }
        
        //display all the womens categories for this store
        if(!empty($baby_category_links)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Baby: </td>';
            $html .= '<td class="listing-category-values">'.implode( ', ', $baby_category_links ).'</td>';
            $html .= '</tr>';
        }
        
        //display all the style categories for this store
        if(!empty($kids_style)) {
            $html.='<tr class="listing-category-row">';
            $html .= '<td class="listing-category-label">Style: </td>';
            $html .= '<td class="listing-category-values">'.implode(', ', $kids_style).'</td>';
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
                $html .= '<td class="listing-category-values">'. min($baby_sizes) . " - " . max($baby_sizes).'</td>';
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
        $html.=         "</div>
                    </div>     
                </div>";
        
    endif;
    $html .= "</div>";
    
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
 * If the listing includes Kids & Baby, only the relevant sub-categories will be returned
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
        $top_categories[] = '<a href="'.get_term_link($wp_category).'" class="btn btn-default btn-md">'.$wp_category->name.'</a>';
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
    $retailer_id = 'r'.get_field('shopstyle_retailer_id', $listing_id);

    return $retailer_id;
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
        $response .=            '<a href="'.$product_url.' target="_blank""><img src="'.$image_url.'" class="carousel-image" style="max-height:150px; width:auto; margin:auto; "/></a>';
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


function render_price_field(){
    /*Price info*/
    $prices = get_field_object('price');  //returns the array of all key-value pairs, along with the selected value
    $price = get_field('price');    //returns an array of all selected values

    $n = count($price);
    $result = $prices['choices'][$price[0]];

    if($n > 1):
        $result .= " - ";
        $result .= $prices['choices'][$price[$n-1]];

    endif;

    return $result;   
}

function render_listing_highlights(){
    
    $shipping = get_shipping_info('highlight');
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
function render_shipping_info(){

    $return = '';

    $shipping_info = get_shipping_info($context);
    $return .= '<div class="wpbdp-listing-shipping-info ">';
    $return .= '<label class="element-title"><i class="fa fa-truck"></i> US Shipping</label>';
    $return .= '<div class="shipping_info"><p>' . $shipping_info . '</p></div>';
    $return .= '</div>';
    
    return $return;
}

function get_shipping_info($context=''){
    $shipping = get_field('shipping');
    $shipping_cost = get_field('shipping_cost');
    $shipping_time = get_field('standard_delivery_time');    
     
    if(!$shipping){
        return;
    }
    
    if ( $shipping == "ship_free" ):
            $shipping_info = 'Free Shipping';
    elseif ( $shipping == "ship_min" ):
            $shipping_info = 'Free Shipping, $' . get_field('free_shipping_minimum_amount') . '+ orders';
            if($context!='highlight'): $shipping_info.='<br/>Standard Shipping: $' . $shipping_cost ;endif;
    elseif ( $shipping == "ship_flat" ):
            $shipping_info = 'Standard shipping: $' . $shipping_cost ;
    else:
            $shipping_info = 'Shipping costs increase with order size';
    endif; 
    
    if($context!='highlight'){
        if(get_field('have_standard_delivery_time')){
            $shipping_info .=  '<br/>Arrives in '.min($shipping_time) . " - " . max($shipping_time)." business days";
        }
        $shipping_notes = get_field('shipping_notes');
        if($shipping_notes!=''){
            $shipping_info .= '<br/>'.$shipping_notes;
        }
    }
    
    
    
    return $shipping_info;
}

function render_return_shipping_info(){
    
     /*Return Shipping Info*/
    
    $return_shipping_info = get_return_shipping_info();
    $return .= '<div class="wpbdp-listing-shipping-info">';
    $return .= '<label class="element-title"><i class="fa fa-mail-reply"></i> US Return Shipping:</label>';
    $return .= '<div class="shipping_info" >' . $return_shipping_info . '</div>';
    $return .= '</div>';
    
    return $return;
}

function get_return_shipping_info(){
    
    $return_shipping = get_field('return_shipping');

    if(!$return_shipping){
        return;
    }
    if ( $return_shipping == "return_free" ):
        $return_shipping_info = 'Free Returns';
    elseif ( $return_shipping =="return_flat" ):
        $return_shipping_info =  'Flat rate return fee $' . get_field('return_shipping_cost');
    else:
        $return_shipping_info = 'Buyer handles return shipping';
    endif;
    
    return $return_shipping_info;
}

function render_canada_shipping(){
    $can_shipping = '';
    $can_shipping .= '<div class="wpbdp-listing-can-shipping-info">';
    $can_shipping .= '<label class="element-title"><i class="icon-globe"></i> Shipping to Canada:</label>';
    $can_shipping .= '<div class="shipping_info" >';
    
    if(get_field('ships_to_canada')){
        $can_shipping_cost = get_field('shipping_cost_to_canada');
        $can_taxes_duties = get_field('taxes_and_duties');
        $can_shipping .= 'Ships to Canada, $';
        $can_shipping .= $can_shipping_cost;
        if($can_taxes_duties){
            $can_shipping .= ' plus taxes & import duties';
        }
        $can_shipping_notes = get_field('canada_shipping_notes');
        if($can_shipping_notes!=''){
            $can_shipping .= "<br/>".$can_shipping_notes;
        }
    }else{
        $can_shipping .= 'Does not ship to Canada';
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

function render_good_for(){
    $good_for_object = get_field_object('good_for');
    $good_for_values = $good_for_object['value'];
    
    if(!empty($good_for_values)){
        foreach($good_for_values as $g){
            $values[] = $good_for_object['choices'][$g];
        }
        $return = '<i class="icon-tag"></i> '.implode(", ", $values);
    }

    return $return;
}
function render_category_size_info(){
    
}

function render_customer_support_info(){
   
    $support_phone = get_field('support_phone');
    $support_email = get_field('support_email');
    
    if(!empty($support_phone)) $html.= "<div class='listing-category-label'><i class='fa fa-phone'></i> Support Phone: </div><div class='listing-category-values'>".$support_phone."</div>";
    if(!empty($support_email)) $html.= "<div class='listing-category-label'><i class='icon-envelope-open'></i> Support Email: </div><div class='listing-category-values'>".$support_email."</div>";

    return $html;
}

function get_shopstyle_retailer_url($listing_id){
    
    $shopstyle = new ShopStyle();
    $host = $shopstyle->getHost();
    $API_key = $shopstyle->getApiKey();
    
    $listing_url = esc_url(wpbdp_render_listing_field('URL'));
    
    $return = esc_url("http://".$host."/action/apiVisitRetailer?url=".$listing_url."&pid=".$API_key);
    
    return $return;
    
}

function set_thumbnail_id( $listing_id, $image_id ) {
    if(!$listing_id){
        return;
    }
    
    if ( ! $image_id )
        return delete_post_meta( $listing_id, '_wpbdp[thumbnail_id]' );

    return update_post_meta( $listing_id, '_wpbdp[thumbnail_id]', $image_id );
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
    if ($sort = trim(wpbdp_getv($_GET, 'wpbdp_sort', null))) {
        $order = substr($sort, 0, 1) == '-' ? 'DESC' : 'ASC';
        $sort = ltrim($sort, '-');

        $obj = new StdClass();
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
