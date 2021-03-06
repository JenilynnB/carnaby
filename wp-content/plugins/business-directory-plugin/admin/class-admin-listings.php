<?php

class WPBDP_Admin_Listings {

    function __construct() {
        add_action( 'manage_' . WPBDP_POST_TYPE . '_posts_columns', array( &$this, 'add_columns' ) );
        add_action( 'manage_' . WPBDP_POST_TYPE . '_posts_custom_column', array( &$this, 'listing_column' ), 10, 2 );
        add_action( 'restrict_manage_posts', array(&$this, 'my_restrict_manage_posts_category'));
        add_action( 'restrict_manage_posts', array(&$this, 'my_restrict_manage_posts_meta'));
        add_action( 'parse_query', array(&$this, 'convert_directory_id_to_taxonomy_term'));
        add_action( 'parse_query', array(&$this, 'add_meta_id_to_query'));
        
        add_filter( 'views_edit-' . WPBDP_POST_TYPE, array( &$this, 'listing_views' ) );
        add_filter( 'posts_clauses', array( &$this, 'listings_admin_filters' ) );
    }

    // {{{ Custom columns.

    
    function my_restrict_manage_posts_category() {
        global $typenow;
        
        
        if ($typenow==WPBDP_POST_TYPE){
            $selected = $_REQUEST[WPBDP_CATEGORY_TAX];
            
            $args = array(
                'show_option_all' => "Show All Categories",
                'taxonomy'        => WPBDP_CATEGORY_TAX,
                'name'               => WPBDP_CATEGORY_TAX, 
                'hierarchical'      => true,
                'selected'          => $selected,
                'orderby'           => 'NAME'

            );
            wp_dropdown_categories($args);
        }
    }
    
    function my_restrict_manage_posts_meta(){
        global $typenow;
        $meta_key_list = array(
                "good_for_women",
                "good_for_men",
                "good_for_kids");
        
        
        foreach($meta_key_list as $meta_key){
            if ($typenow==WPBDP_POST_TYPE){
                $field = get_standard_field($meta_key);
                $meta_value = $_REQUEST[$meta_key];
                $field_options = $field['choices'];
                if(is_array($field_options) && !empty($field_options)){
                    $dropdown = '<select name="'.$field["name"].'" id = "'.$field["name"].'" class="postform" >';
                    $dropdown .= '<option value="none" selected="selected">Show all '.$meta_key.'</option> ';
                    foreach($field_options as $option => $display_name){
                        if($option==$meta_value){
                            $selected = "selected";
                        }else{
                            $selected = "";
                        }
                        $dropdown .= '<option value="'.$option.'"  '.$selected.'>'.$display_name.'</option>';
                    }

                    $dropdown .= '</select>';

                    echo $dropdown;
                }
            }
        }
        
    }
    

    
    function add_columns( $columns_ ) {
        $custom_columns = array();
        $custom_columns['category'] = _x( 'Categories', 'admin', 'WPBDM' );
        $custom_columns['good_for_women'] = _x( 'Good For - Women', 'admin', 'WPBDM' );
        $custom_columns['good_for_men'] = _x( 'Good For - Men', 'admin', 'WPBDM' );
        $custom_columns['good_for_kids'] = _x( 'Good For - Kids', 'admin', 'WPBDM' );
        //$custom_columns['payment_status'] = __( 'Payment Status', 'WPBDM' );
        //$custom_columns['sticky_status'] = __( 'Featured (Sticky) Status', 'WPBDM' );

        $columns = array();

        foreach ( $columns_ as $k => $v ) {
            $columns[ $k ] = $v;

            if ( 'title' == $k )
                $columns = array_merge( $columns, $custom_columns );
        }

        return $columns;
    }


    function listing_column( $column, $post_id ) {
        if ( ! method_exists( $this, 'listing_column_' . $column ) )
            return;

        call_user_func( array( &$this, 'listing_column_' . $column ), $post_id );
    }

    function listing_column_category( $post_id ) {
        $listing = WPBDP_Listing::get( $post_id );
        $categories = $listing->get_categories( 'all' );

        $i = 0;
        foreach ( $categories as &$category ) {
            print $category->expired ? '<s>' : '';
            printf( '<a href="%s" title="%s">%s</a>',
                    get_term_link( $category->id, WPBDP_CATEGORY_TAX ),
                    $category->expired ? _x( '(Listing expired in this category)', 'admin', 'WPBDM' ) : '',
                    $category->name );
            print $category->expired ? '</s>' : '';
            print ( ( $i + 1 ) != count( $categories ) ? ', ' : '' );

            $i++;
        }
    }
    
    function listing_column_good_for_women( $post_id ) {
        $listing = WPBDP_Listing::get( $post_id );
        $meta_values = get_field('good_for_women', $post_id);
        
        if($meta_values!=null){
            print(implode(", ", $meta_values));
            
        }
    }
    
    function listing_column_good_for_men( $post_id ) {
        $listing = WPBDP_Listing::get( $post_id );
        $meta_values = get_field('good_for_men', $post_id);
        
        if($meta_values!=null){
            print(implode(", ", $meta_values));
            
        }
    }
    
    function listing_column_good_for_kids( $post_id ) {
        $listing = WPBDP_Listing::get( $post_id );
        $meta_values = get_field('good_for_kids', $post_id);
        
        if($meta_values!=null){
            print(implode(", ", $meta_values));
            
        }
    }
    
    

    function listing_column_payment_status( $post_id ) {
        $listing = WPBDP_Listing::get( $post_id );
        $paid_status = $listing->get_payment_status();

        $status_links = '';

        if ( $paid_status != 'ok' )
            $status_links .= sprintf('<span><a href="%s">%s</a></span>',
                                    add_query_arg( array( 'wpbdmaction' => 'setaspaid', 'post' => $post_id ) ),
                                    __('Paid', 'WPBDM'));

        printf( '<span class="tag paymentstatus %s">%s</span>', $paid_status, strtoupper( $paid_status ) );

        if ( $status_links && current_user_can( 'administrator' ) )
            printf( '<div class="row-actions"><b>%s:</b> %s</div>', __( 'Mark as', 'WPBDM' ), $status_links );
    }

    function listing_column_sticky_status( $post_id ) {
        $upgrades_api = wpbdp_listing_upgrades_api();
        $sticky_info = $upgrades_api->get_info( $post_id );

        echo sprintf('<span class="tag status %s">%s</span><br />',
                    str_replace(' ', '', $sticky_info->status),
                    $sticky_info->pending ? __('Pending Upgrade', 'WPBDM') : esc_attr($sticky_info->level->name) );

        echo '<div class="row-actions">';

        if ( current_user_can('administrator') ) {
            if ( $sticky_info->upgradeable ) {
                echo sprintf('<span><a href="%s">%s</a></span>',
                             add_query_arg(array('wpbdmaction' => 'changesticky', 'u' => $sticky_info->upgrade->id, 'post' => $post_id)),
                             '<b>↑</b> ' . sprintf(__('Upgrade to %s', 'WPBDM'), esc_attr($sticky_info->upgrade->name)) );
                echo '<br />';
            }

            if ( $sticky_info->downgradeable ) {
                echo sprintf('<span><a href="%s">%s</a></span>',
                             add_query_arg(array('wpbdmaction' => 'changesticky', 'u' => $sticky_info->downgrade->id, 'post' => $post_id)),
                             '<b>↓</b> ' . sprintf(__('Downgrade to %s', 'WPBDM'), esc_attr($sticky_info->downgrade->name)) );                
            }
        } elseif ( current_user_can('contributor') && wpbdp_user_can( 'upgrade-to-sticky', $post_id ) ) {
                echo sprintf('<span><a href="%s"><b>↑</b> %s</a></span>', wpbdp_get_page_link('upgradetostickylisting', $post_id), _x('Upgrade to Featured', 'admin actions', 'WPBDM'));            
        }

        echo '</div>';

    }

    // }}}

    // {{{ List views.
    
    function listing_views( $views ) {
        global $wpdb;

        if ( ! current_user_can( 'administrator' ) ) {
            if ( current_user_can( 'contributor' ) && isset( $views['mine'] ) )
                return array( $views['mine'] );

            return array();
        }

        $post_statuses = '\'' . join('\',\'', isset($_GET['post_status']) ? array($_GET['post_status']) : array('publish', 'draft', 'pending')) . '\'';

        $paid = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->posts} p WHERE p.post_type = %s AND p.post_status IN ({$post_statuses})
            AND NOT EXISTS ( SELECT 1 FROM {$wpdb->prefix}wpbdp_payments ps WHERE ps.listing_id = p.ID AND ps.status = %s )",
            WPBDP_POST_TYPE,
            'pending'
        ) );

        $unpaid = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p LEFT JOIN {$wpdb->prefix}wpbdp_payments ps ON p.ID = ps.listing_id
             WHERE p.post_type = %s AND p.post_status IN ({$post_statuses}) AND ps.status = %s",
             WPBDP_POST_TYPE,
             'pending'
        ) );
        $pending_upgrade = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id)
                                                           WHERE p.post_type = %s AND p.post_status IN ({$post_statuses}) AND ( (pm.meta_key = %s AND pm.meta_value = %s) )",
                                                           WPBDP_POST_TYPE,
                                                           '_wpbdp[sticky]',
                                                           'pending') );
        $expired = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p INNER JOIN {$wpdb->prefix}wpbdp_listing_fees lf ON lf.listing_id = p.ID WHERE lf.expires_on < %s",
                                                   current_time( 'mysql' ) ) );

        $views['paid'] = sprintf('<a href="%s" class="%s">%s <span class="count">(%s)</span></a>',
                                 add_query_arg('wpbdmfilter', 'paid', remove_query_arg('post')),
                                 wpbdp_getv($_REQUEST, 'wpbdmfilter') == 'paid' ? 'current' : '',
                                 __('Paid', 'WPBDM'),
                                 number_format_i18n($paid));
        $views['unpaid'] = sprintf('<a href="%s" class="%s">%s <span class="count">(%s)</span></a>',
                                   add_query_arg('wpbdmfilter', 'unpaid', remove_query_arg('post')),
                                   wpbdp_getv($_REQUEST, 'wpbdmfilter') == 'unpaid' ? 'current' : '',
                                   __('Unpaid', 'WPBDM'),
                                   number_format_i18n($unpaid));
        $views['featured'] = sprintf('<a href="%s" class="%s">%s <span class="count">(%s)</span></a>',
                                   add_query_arg('wpbdmfilter', 'pendingupgrade', remove_query_arg('post')),
                                   wpbdp_getv($_REQUEST, 'wpbdmfilter') == 'pendingupgrade' ? 'current' : '',
                                   __('Pending Upgrade', 'WPBDM'),
                                   number_format_i18n($pending_upgrade));
        $views['expired'] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%s)</span></a>',
                                     add_query_arg( 'wpbdmfilter', 'expired', remove_query_arg( 'post' ) ),
                                     wpbdp_getv( $_REQUEST, 'wpbdmfilter' ) == 'expired' ? 'current' : '' ,
                                     _x( 'Expired', 'admin', 'WPBDM' ),
                                     number_format_i18n( $expired )
                                    );
        return $views;
    }

    function listings_admin_filters( $pieces  ) {
        global $current_screen;
        global $wpdb;

        if ( ! is_admin() || ! isset( $_REQUEST['wpbdmfilter'] ) ||  'edit-' . WPBDP_POST_TYPE !=  $current_screen->id )
            return $pieces;

        switch ( $_REQUEST['wpbdmfilter'] ) {
            case 'expired':
                $pieces['join'] = " LEFT JOIN {$wpdb->prefix}wpbdp_listing_fees ON {$wpdb->prefix}wpbdp_listing_fees.listing_id = {$wpdb->posts}.ID ";
                $pieces['where'] = $wpdb->prepare( " AND {$wpdb->prefix}wpbdp_listing_fees.expires_on IS NOT NULL AND {$wpdb->prefix}wpbdp_listing_fees.expires_on < %s ", current_time( 'mysql' ) );
                $pieces['groupby'] = " {$wpdb->posts}.ID ";
                break;
            case 'pendingupgrade':
                $pieces['join'] = " LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = {$wpdb->posts}.ID ";
                $pieces['where'] = $wpdb->prepare( " AND pm.meta_key = %s AND pm.meta_value = %s ", '_wpbdp[sticky]', 'pending' );
                break;
            case 'paid':
                $pieces['where'] .= $wpdb->prepare( " AND NOT EXISTS ( SELECT 1 FROM {$wpdb->prefix}wpbdp_payments WHERE {$wpdb->posts}.ID = {$wpdb->prefix}wpbdp_payments.listing_id AND ( {$wpdb->prefix}wpbdp_payments.status IS NULL OR {$wpdb->prefix}wpbdp_payments.status != %s ) )", 'pending' );
                break;
            case 'unpaid':
                $pieces['join'] .= " LEFT JOIN {$wpdb->prefix}wpbdp_payments ON {$wpdb->posts}.ID = {$wpdb->prefix}wpbdp_payments.listing_id ";
                $pieces['where'] .= $wpdb->prepare( " AND {$wpdb->prefix}wpbdp_payments.status = %s ", 'pending' );
                $pieces['groupby'] .= " {$wpdb->posts}.ID ";
                break;
            default:
                break;
        }
        
        
        return $pieces;
    }

    function convert_directory_id_to_taxonomy_term($query) {
        
        global $pagenow;
        $qv = &$query->query_vars;
        $taxquery = $query->get('tax_query');
        if ($pagenow=='edit.php' &&
            isset($qv['post_type']) && $qv['post_type']==WPBDP_POST_TYPE &&
            isset($qv[WPBDP_CATEGORY_TAX]) && is_numeric($qv[WPBDP_CATEGORY_TAX]) && $qv[WPBDP_CATEGORY_TAX]!=0) {

                $term = get_term_by('id',$qv[WPBDP_CATEGORY_TAX],WPBDP_CATEGORY_TAX);
                $taxquery[] = 
                    array(
                        'taxonomy' => WPBDP_CATEGORY_TAX,
                        'field' => 'slug',
                        'terms' => $term->slug,
                        'operator' => 'IN'
                    );

                $query->set( 'tax_query', $taxquery );

                //At some point this gets set in the main wordpress query and it is not
                //a valid query var. It needs to be reset to work properly.
                $query->set(WPBDP_CATEGORY_TAX, '');
                //echo print_r($query);

        }
    }
    
    function add_meta_id_to_query($query){
        //echo $GLOBALS['wp_query']->request;
        global $pagenow;
        $meta_key_list = array(
                "good_for_men",
                "good_for_women",
                "good_for_kids");
        $qv = &$query->query_vars;
        
        foreach($meta_key_list as $meta_key){
            $meta_value = $_REQUEST[$meta_key];
            if ($pagenow=='edit.php' &&
                    isset($qv['post_type']) && $qv['post_type']==WPBDP_POST_TYPE &&
                    isset($meta_value) && $meta_value!="none") {

                //$query->set($meta_key, $meta_value);
                //$qv[$meta_key] = $meta_value;

                $query->set('meta_query', array(

                        array(
                                'key'     => $meta_key,
                                'value'   => $meta_value,
                                'compare' => 'LIKE'
                        )
                ));


            }
        }
        
    }
        
        
    // }}}
    
}
