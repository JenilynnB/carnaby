<?php
/*
 Plugin Name: Business Directory Plugin - Ratings Module
 Plugin URI: http://www.businessdirectoryplugin.com
 Version: 3.4
 Author: D. Rodenbaugh
 Description: Business Directory Ratings.  Allows your users to rate businesses, search by rating, and enter comments about listings.
 Author URI: http://www.skylineconsult.com
 */

require_once(plugin_dir_path(__FILE__) . 'admin.php');

class BusinessDirectory_RatingsModule {

    const VERSION = '3.4';
    const REQUIRED_BD_VERSION = '3.4';
    const DB_VERSION = '0.6';

    public function __construct() {
        add_action( 'plugins_loaded', array( $this, '_initialize' ) );
    }

    /*
     * Activation
     */
    public function _initialize() {
        if (is_admin())
            $this->admin = new BusinessDirectory_RatingsModuleAdmin();

        if ( ! defined( 'WPBDP_VERSION' ) || version_compare( WPBDP_VERSION, self::REQUIRED_BD_VERSION, '<' ) )
            return;

        $this->DEFAULT_TOOLTIPS = array(null, 'Awful', 'Bad', 'Average', 'Good', 'Awesome');

        // Load i18n.
        load_plugin_textdomain( 'wpbdp-ratings', false, trailingslashit( basename( dirname( __FILE__ ) ) ) . 'translations/' );


        add_action('init', array($this, '_install_or_update'));

        add_action( 'admin_enqueue_scripts', array( &$this, '_enqueue_scripts' ) );
        add_action( 'wpbdp_enqueue_scripts', array( &$this, '_enqueue_scripts' ) );
        add_action('wp_print_scripts', array($this, '_configure_js'));
        
        add_action('wpbdp_register_settings', array($this, '_register_settings'));

        add_action( 'wpbdp_register_fields', array( $this, 'register_fields' ) );
        add_action('wpbdp_before_single_view', array($this, '_process_form')); // single view
        add_action('wpbdp_after_single_view', array($this, '_reviews_and_form')); // single view

        add_filter('wpbdp_search_where', array($this, '_search_where'), 10, 2);

        // sort options
        add_filter('wpbdp_listing_sort_options', array($this, '_sort_options'));
        add_filter('wpbdp_query_fields', array($this, '_query_fields'));
        add_filter('wpbdp_query_orderby', array($this, '_query_orderby'));

        add_action('wp_ajax_wpbdp-ratings', array($this, '_handle_ajax'));  // ajax

        // Notifications.
        add_action( 'wpbdp_ratings_rating_submitted', array( &$this, 'send_rating_for_review_notification' ) );
        add_action( 'wpbdp_ratings_rating_approved', array( &$this, 'send_new_rating_notification' ) );
    }

    public function &get_ratings_field() {
        $ratings_field = wpbdp_get_form_fields( 'field_type=ratings&unique=1' );
        return $ratings_field;
    }

    public function register_fields( $api ) {
        require_once( plugin_dir_path( __FILE__ ) . 'class-ratings-field.php' );
        $api->register_field_type( 'WPBDP_Ratings_Field', 'ratings' );

        // Create field (if needed).
        $ratings_field = $this->get_ratings_field(); 
        if ( ! $ratings_field ) {
            $display = array( 'listing' );
            if ( get_option( 'wpbdp-ratings-display-in-excerpt', true ) )
                $display[] = 'excerpt';

            if ( get_option( 'wpbdp-ratings-display-in-search', true ) )
                $display[] = 'search';

            $f = new WPBDP_FormField( array( 'label' => __( 'Average', 'wpbdp-ratings' ),
                                             'field_type' => 'ratings',
                                             'association' => 'custom',
                                             'display_flags' => $display,
                                             'weight' => 20 ) );
            $f->save();
        }
    }

    public function _install_or_update() {
        global $wpdb;

        $db_version = get_option('wpbdp-ratings-db-version', '0.0');

        if ($db_version != self::DB_VERSION) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $sql = "CREATE TABLE {$wpdb->prefix}wpbdp_ratings (
                id bigint(20) PRIMARY KEY  AUTO_INCREMENT,
                listing_id bigint(20) NOT NULL,
                rating tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
                user_id bigint(20) NOT NULL DEFAULT 0,
                user_name varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
                ip_address varchar(255) NOT NULL,
                comment text CHARACTER SET utf8 COLLATE utf8_general_ci,
                created_on datetime NOT NULL,
                approved tinyint(1) UNSIGNED NOT NULL DEFAULT 1
            ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

            dbDelta($sql);
        }

        // Upgrade an option in >= 1.6
        if ( version_compare( $db_version, '0.5', '<' ) ) {
            if ( wpbdp_get_option( 'ratings-require-comment' ) ) {
                wpbdp_set_option( 'ratings-comments', 'required' );
            } else {
                wpbdp_set_option( 'ratings-comments', 'optional' );
            }
        }

        update_option('wpbdp-ratings-db-version', self::DB_VERSION);
    }

    public function _enqueue_scripts() {
        if (!is_admin() && !$this->enabled()) return;

        //wp_enqueue_style('wpbdp-ratings', plugins_url('/resources/wpbdp-ratings.min.css', __FILE__));
        wp_enqueue_style('wpbdp-ratings', plugins_url('/resources/wpbdp-ratings.css', __FILE__));

        wp_register_script('jquery-raty',
                           plugins_url('/resources/jquery.raty-2.4.5/js/jquery.raty.min.js', __FILE__),
                           array('jquery'));

        if (is_admin()) {
            wp_enqueue_script('wpbdp-ratings-admin',
                              plugins_url('/resources/wpbdp-ratings-admin.min.js', __FILE__),
                              array('jquery-raty'));
        } else {
            /*wp_enqueue_script('wpbdp-ratings',
                              plugins_url('/resources/wpbdp-ratings.min.js', __FILE__),
                              array('jquery-raty'));  */          
            wp_enqueue_script('wpbdp-ratings',
                              plugins_url('/resources/wpbdp-ratings.js', __FILE__),
                              array('jquery-raty'));            
        }
    }

    public function _change_approval_setting($setting, $newvalue, $oldvalue=null) {
        if (!$newvalue) {
            global $wpdb;
            $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->prefix}wpbdp_ratings SET approved = %d", 1) );
        }
        return $newvalue;
    }

    public function _register_settings($api) {
        $g = $api->add_group('ratings', __('Ratings', 'wpbdp-ratings'));

        $s = $api->add_section($g, 'ratings-general', __('General Settings', 'wpbdp-ratings'));
        $api->add_setting($s, 'ratings-enable', __('Enable ratings', 'wpbdp-ratings'), 'boolean', true);
        $api->add_setting($s, 'ratings-min-ratings', __('Ratings threshold', 'wpbdp-ratings'), 'text', 0, __('Minimum number of reviews before ratings are displayed on a listing', 'wpbdp-ratings'));
        $api->add_setting($s, 'ratings-allow-unregistered', __('Allow unregistered users to post reviews?', 'wpbdp-ratings'), 'boolean', false);
        // $api->add_setting($s, 'ratings-require-comment', __('Require rating comment?', 'wpbdp-ratings'), 'boolean', true);
        $api->add_setting( $s,
                           'ratings-comments',
                           __( 'Rating comments', 'wpbdp-ratings' ),
                           'choice',
                           'required',
                           __( 'Decide whether rating comments should be required, optional or not used at all.', 'wpbdp-ratings' ),
                           array( 'choices' => array(
                                array( 'required', __( 'Required', 'wpbdp-ratings' ) ),
                                array( 'optional', __( 'Optional', 'wpbdp-ratings' ) ),
                                array( 'disabled', __( 'Disabled', 'wpbdp-ratings' ) )
                                ) )
                         );
        $api->add_setting($s, 'ratings-require-approval', __('Admin must approve reviews?', 'wpbdp-ratings'), 'boolean', false, null, null, array($this, '_change_approval_setting'));

        $s = $api->add_section( $g, 'ratings-email-settings', __( 'E-mail Settings', 'wpbdp-ratings' ) );
        $api->add_setting( $s,
                           'ratings-notify-owner',
                           __( 'Notify listing owner of new ratings?', 'wpbdp-ratings' ),
                           'boolean',
                           false );
        $api->add_setting( $s,
                           'ratings-notify-admin',
                           __( 'Notify site admin of approved/submitted ratings?', 'wpbdp-ratings' ),
                           'boolean',
                           false );
        $api->add_setting( $s,
                           'ratings-notification-email',
                           __( 'Notification E-Mail', 'wpbdp-ratings' ),
                           'text',
                           'A new rating has been posted to the listing [listing]. The rating details are below.' . str_repeat( PHP_EOL, 2 ) .
                           'Posted on: [date]' . PHP_EOL .                
                           'Posted by: [rating_author]' . PHP_EOL .
                           'Rating: [rating_rating]' . PHP_EOL .
                           'Comments: [rating_comment]',
                           __( 'You can use the placeholders [listing] for the listing title, [rating_author] for the author of the rating, [rating_comment] for the rating comment, [rating_rating] for the actual rating and [date] for the date the rating was posted.', 'wpbdp-ratings' ),
                           array( 'use_textarea' => true )
                         );

/*        $s = $api->add_section($g, 'ratings-display', __('Display Settings', 'wpbdp-ratings'));
        $api->add_setting($s, 'ratings-display-in-excerpt', __('Display ratings in excerpt view', 'wpbdp-ratings'), 'boolean', true);
        $api->add_setting( $s,
                           'ratings-display-in-search',
                           __( 'Display ratings search option in Advanced Search?', 'wpbdp-ratings' ),
                           'boolean',
                           true );*/

        $s = $api->add_section($g, 'ratings-tooltips', __('Rating Tooltips', 'wpbdp-ratings'));
        for ( $i = 1; $i <= 5; $i++ )
            $api->add_setting($s,
                              'ratings-tooltip-' . $i,
                              sprintf(__('%d stars', 'wpbdp-ratings'), $i),
                              'text',
                              $this->DEFAULT_TOOLTIPS[$i]);
    }

    public function _configure_js() {
        if (!is_admin() && !$this->enabled()) return;

        // TODO: maybe use wp_localize_script?

        echo '<script type="text/javascript">';
        echo 'if (typeof(window.WPBDP) == "undefined") WPBDP = {};';
        echo 'if (typeof(WPBDP.ratings) == "undefined") WPBDP.ratings = {};';
        echo sprintf('WPBDP.ratings._config = {number: 5, path: "%s", ajaxurl: "%s", hints:[]};',
                     plugin_dir_url(__FILE__) . 'resources/jquery.raty-2.4.5/img/',
                     admin_url('admin-ajax.php')
                     );
        
        for ( $i = 1; $i <=5; $i++ ) {
            echo sprintf('WPBDP.ratings._config.hints.push("%s");', esc_attr(wpbdp_get_option('ratings-tooltip-' . $i)));
        }

        echo '</script>';
    }

    /*
     * Sort by ratings
     */

    public function _sort_options($options) {
        $options['rating'] = array( __( 'Rating', 'wpbdp-ratings' ), '', 'DESC' );
        $options['rating_count'] = array( __( 'Rating Count', 'wpbdp-ratings' ), '', 'DESC' );
        return $options;
    }

    public function _query_fields($fields) {
        global $wpdb;

        $sort = wpbdp_get_current_sort_option();

        if (!$sort)
            return $fields;

        if ($sort->option == 'rating') {
            $rating_query = "(SELECT AVG(rating) FROM {$wpdb->prefix}wpbdp_ratings WHERE listing_id = {$wpdb->posts}.ID) AS wpbdp_rating";
            return $fields . ', ' . $rating_query;
        } elseif ($sort->option == 'rating_count') {
            $rating_query = "(SELECT COUNT(rating) FROM {$wpdb->prefix}wpbdp_ratings WHERE listing_id = {$wpdb->posts}.ID) AS wpbdp_rating_count";
            return $fields . ', ' . $rating_query;
        }

        return $fields;
    }

    public function _query_orderby($orderby) {
        $sort = wpbdp_get_current_sort_option();

        if (!$sort)
            return $orderby;

        if ($sort->option == 'rating') {
            return $orderby . ', wpbdp_rating ' . $sort->order;
        } elseif ($sort->option == 'rating_count') {
            return $orderby . ', wpbdp_rating_count ' . $sort->order;
        }

        return $orderby;
    }

    // FIXME: use the wpbdp_search_where filter while we work this into the actual FormField class.
    public function _search_where($where, $search_args) {
        $field = $this->get_ratings_field();

        if ( ! $field->has_display_flag( 'search' ) )
            return $where;

        $min_rating = 0;
        foreach ( $search_args['fields'] as $a ) {
            if ( $field->get_id() == $a['field_id'] ) {
                $min_rating = intval( $a['q'] );
                break;
            }
        }

        if ( $min_rating > 0 ) {
            global $wpdb;

            $subquery = $wpdb->prepare( "SELECT listing_id FROM {$wpdb->prefix}wpbdp_ratings GROUP BY listing_id HAVING AVG(rating) >= %d", $min_rating );
            $where .= " AND {$wpdb->posts}.ID IN ({$subquery})";
        }

        return $where;
    }


    /*
     * Ratings
     */
    public function enabled() {
        return function_exists( 'wpbdp_get_option' ) && wpbdp_get_option('ratings-enable') ? true : false;
    }

    public function get_rating_info($listing_id) {
        global $wpdb;

        $info = $wpdb->get_row( $wpdb->prepare("SELECT COUNT(*) AS count, AVG(rating) AS average FROM {$wpdb->prefix}wpbdp_ratings WHERE listing_id = %d AND approved = %d", $listing_id, 1) );
        $info->average = round($info->average, 2);
        $info->count = intval($info->count);

        return $info;
    }

    public function get_reviews($listing_id, $only_approved=true) {
        global $wpdb;

        if ($only_approved) {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE listing_id = %d AND approved = %d ORDER BY id DESC", $listing_id, 1);
        } else {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE listing_id = %d ORDER BY id DESC", $listing_id);
        }

        $reviews = $wpdb->get_results($query);
        return $reviews;
    }
    
    public function get_reviews_by_user($user_id, $only_approved=true){
        global $wpdb;

        if ($only_approved) {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE user_id = %d AND approved = %d ORDER BY id DESC", $user_id, 1);
        } else {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE user_id = %d ORDER BY id DESC", $user_id);
        }

        $reviews = $wpdb->get_results($query);
        return $reviews;
    }
    
    public function get_this_review_by_user($user_id, $listing_id, $only_approved=true){
        global $wpdb;

        if ($only_approved) {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE user_id = %d AND listing_id = %d AND approved = %d ORDER BY id DESC", $user_id, $listing_id, 1);
        } else {
            $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE user_id = %d AND listing_id = %d ORDER BY id DESC", $user_id, $listing_id);
        }

        $review = $wpdb->get_row($query);
       
        return $review;
    }

    private function get_client_ip_address() {
        $ip = '0.0.0.0';

        $check_vars = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');

        foreach ($check_vars as $varname) {
            if (isset($_SERVER[$varname]) && !empty($_SERVER[$varname]))
                return $_SERVER[$varname];
        }

        return $ip;
    }

    function can_post_review($listing_id, &$reason=null) {
        //$reason = 'already-rated';

        if (!wpbdp_get_option('ratings-allow-unregistered') && !is_user_logged_in()) {
            
            $reason = 'not-logged-in';
            return false;
        }
        
        global $wpdb;
        
        $user_id = get_current_user_id();
        //$ip_address = $this->get_client_ip_address();
        
        if ($user_id) {
            
            //return intval($wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_ratings WHERE (user_id = %d OR ip_address = %s) AND listing_id = %d", $user_id, $ip_address, $listing_id) )) == 0;
            if(!intval($wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_ratings WHERE (user_id = %d OR ip_address = %s) AND listing_id = %d", $user_id, $ip_address, $listing_id) )) == 0){
                $reason='already-rated';
            }
            return true;
        } else {
            return intval($wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_ratings WHERE ip_address = %s AND listing_id = %d", $ip_address, $listing_id) )) == 0;
        }
    }
    
    function has_written_review($listing_id){
        
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

    public function _process_form($listing_id) {
        global $wpdb;
        $review = array();
        
        $this->_form_state = array();
        $this->_form_state['success'] = false;
        $this->_form_state['validation_errors'] = $this->validate_form();
        $this->_form_state['edit_review'] = $this->has_written_review($listing_id);
        
        if(get_current_user_id()){
            $this->_form_state['review_to_edit'] = $this->get_this_review_by_user(get_current_user_id(), $listing_id);
            $review_id = $this->_form_state['review_to_edit']->id;
        }
        
        if (!$this->can_post_review($listing_id))
            return;
        
        if (isset($_POST['rate_listing']) && !$this->_form_state['validation_errors']) {
            $review = stripslashes_deep( array(
                'user_id' => get_current_user_id(),
                'user_name' => wpbdp_getv($_POST, 'user_name', ''),
                'ip_address' => $this->get_client_ip_address(),
                'listing_id' => intval($_POST['listing_id']),
                'rating' => intval($_POST['score']),
                'comment' => trim($_POST['comment']),
                'created_on' => current_time('mysql'),
                'approved' => wpbdp_get_option('ratings-require-approval') ? 0 : 1
                ) );
            
            if ($this->has_written_review($listing_id)>0) {

                if ( ( $review['user_id'] && $review['user_id'] == get_current_user_id() ) || current_user_can('administrator')) {
                    if ($wpdb->update("{$wpdb->prefix}wpbdp_ratings", $review, array('id' => $review_id))) {
                        $this->_form_state['success'] = true;
                    }
                }
            }else{
               if ($wpdb->insert("{$wpdb->prefix}wpbdp_ratings", $review)) {
                    $this->_form_state['success'] = true;
                }
            }    
                
                
                
            if ( $review['approved'] == 1 )
                    do_action( 'wpbdp_ratings_rating_approved', (object) $review );
            else
                    do_action( 'wpbdp_ratings_rating_submitted', (object) $review );
        }
    }

    private function validate_form() {
        $errors = array();

        if (isset($_POST['rate_listing'])) {
            if (!is_user_logged_in() && !trim($_POST['user_name']))
                $errors[] = __('Please enter your name.', 'wpbdp-ratings');

            $rating = intval(wpbdp_getv($_POST, 'score', 0));
            if ($rating <= 0 || $rating > 5)
                $errors[] = __('Please select a valid rating.', 'wpbdp-ratings');


            if ( wpbdp_get_option( 'ratings-comments' ) == 'required' && !trim($_POST['comment']))
                $errors[] = __('Please enter a comment.', 'wpbdp-ratings');
        }
        return $errors;
    }


    /*
     * Views.
     */
    public function _reviews_and_form($listing_id) {
        if (!$this->enabled() || ( apply_filters( 'wpbdp_listing_ratings_enabled', true, $listing_id ) == false ) )
            return;
        
        
        $vars = array();
        $vars['review_form'] = $this->can_post_review($listing_id, $reason) ? wpbdp_render_page(plugin_dir_path(__FILE__) . 'templates/form.tpl.php', $this->_form_state) : '';
        $vars['reason'] = $reason;
        $vars['success'] = $this->_form_state['success'];
        $vars['ratings'] = $this->get_reviews($listing_id);

        echo wpbdp_render_page(plugin_dir_path(__FILE__) . 'templates/ratings.tpl.php', $vars);
    }


    /*
     * AJAX
     */

    public function _handle_ajax() {
        global $wpdb;

        $res = array('success' => false, 'msg' => __('An unknown error occurred', 'wpbdp-ratings'));

        switch (wpbdp_getv($_POST, 'a')) {
            case 'info':
                $res['info'] = (array) $this->get_rating_info($_POST['listing_id']);
                $res['success'] = true;                
                break;
            case 'edit':
                if ($review = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE id = %d", $_POST['id']) )) {
                    if ( ( $review->user_id && $review->user_id == get_current_user_id() ) || current_user_can('administrator')) {
                        $review->comment = stripslashes( trim( $_POST['comment'] ) );

                        if ($wpdb->update("{$wpdb->prefix}wpbdp_ratings", (array) $review, array('id' => $review->id))) {
                            $res['comment'] = $review->comment;
                            $res['success'] = true;
                        }
                    }
                }
                break;

            case 'delete':
                if ($review = $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE id = %d", $_POST['id']) )) {
                    if ( ( $review->user_id && $review->user_id == get_current_user_id() ) || current_user_can('administrator')) {
                        if ($wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}wpbdp_ratings WHERE id = %d", $_POST['id']) ) !== FALSE) {
                            $res['success'] = true;
                        }
                    }
                }
                break;
            default:
                break;
        }

        if ($res['success'] == true)
            $res['msg'] = '';

        print json_encode($res);
        exit;
    }

    public function send_new_rating_notification( $review ) {
        if ( !wpbdp_get_option( 'ratings-notify-owner' ) && !wpbdp_get_option( 'ratings-notify-admin' ) )
            return;

        $email = new WPBDP_Email();
        $email->subject = get_bloginfo( 'name' );

        if ( wpbdp_get_option( 'ratings-notify-owner' ) )
            $email->to[] = wpbusdirman_get_the_business_email( $review->listing_id );

        if ( wpbdp_get_option( 'ratings-notify-admin' ) )
            $email->to[] = get_bloginfo( 'admin_email' );

        $replacements = array(
            '[listing]' => sprintf( '<a href="%s">%s</a>', get_permalink( $review->listing_id ), get_the_title( $review->listing_id ) ),
            '[rating_author]' => $review->user_id ? ( get_the_author_meta( 'display_name', $review->user_id ) . ' (' . get_the_author_meta( 'user_login', $review->user_id )  . ')' ): 'IP ' . $review->ip_address,
            '[rating_comment]' => $review->comment,
            '[rating_rating]' => $review->rating . ' / 5',
            '[date]' => date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $review->created_on ) )
        );
        $email->body = str_replace( array_keys( $replacements ), array_values( $replacements ), wpbdp_get_option( 'ratings-notification-email' ) );
        $email->send();
    }

    public function send_rating_for_review_notification( $review ) {
        if ( ! wpbdp_get_option( 'ratings-notify-admin' ) )
            return;

        $email = new WPBDP_Email();
        $email->subject = sprintf( __( '[%s] Rating pending approval', 'wpbdp-ratings' ), get_bloginfo( 'name' ) );
        $email->to[] = get_bloginfo( 'admin_email' );

        $url = admin_url( 'admin.php?page=wpbdp-ratings-pending#review-' . $review->id );
        $replacements = array(
            '[listing]' => sprintf( '<a href="%s">%s</a>', get_permalink( $review->listing_id ), get_the_title( $review->listing_id ) ),
            '[rating_author]' => $review->user_id ? ( get_the_author_meta( 'display_name', $review->user_id ) . ' (' . get_the_author_meta( 'user_login', $review->user_id )  . ')' ): 'IP ' . $review->ip_address,
            '[rating_comment]' => $review->comment,
            '[rating_rating]' => $review->rating . ' / 5',
            '[date]' => date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $review->created_on ) ),
            '[url]' => '<a href="' . esc_url( $url ) . '">' . $url . '</a>'
        );

        $msg = __('Dear admin,

A new rating has been submitted to the listing [listing] and is pending approval. You can see the listing and take care of approving it or rejecting it by visiting [url].
Rating details are below:

Posted on: [date]
Posted by: [rating_author]
Rating: [rating_rating]
Comments: [rating_comment]
', 'wpbdp-ratings' );
        $msg = str_replace( array_keys( $replacements ), array_values( $replacements ), $msg );
        $email->body = $msg;
        $email->send();
    }

}

global $wpbdp_ratings;
$wpbdp_ratings = new BusinessDirectory_RatingsModule();

function wpbdp_ratings() {
    global $wpbdp_ratings;
    return $wpbdp_ratings;
}
