<?php
if (!class_exists('WP_List_Table')) require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class BusinessDirectory_RatingsReviewTable extends WP_List_Table {

    public function __construct() {
        parent::__construct(array(
            'singular' => __('review pending', 'wpbdp-ratings'),
            'plural' => __('reviews pending', 'wpbdp-ratings'),
            'ajax' => false
        ));
    }

    public function get_columns() {
        return array(
            'user_ip' => __('User/IP', 'wpbdp-ratings'),
            'rating' => __('Rating', 'wpbdp-ratings'),
            'comment' => __('Comment', 'wpbdp-ratings'),
            'listing' => __('Listing', 'wpbdp-ratings')
        );
    }

    public function prepare_items() {
        
        global $wpdb;
        
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $offset = ($current_page-1)*$per_page;
        
        $this->_column_headers = array($this->get_columns(), array(), $this->get_sortable_columns());

        //Commenting this line out to show all ratings (not only those needing to be approved
        //$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE approved = %d ORDER BY id DESC", 0);
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpbdp_ratings ORDER BY id DESC LIMIT %d OFFSET %d", $per_page, $offset);
        $this->items = $wpdb->get_results($query);
        
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_ratings");
        
        $this->set_pagination_args(array(
           'total_items' => $total_items,
           'per_page' => $per_page
        ));
        
        
    }

    /* Rows */

    public function column_user_ip($row) {
        $html  = '';

        if ($row->user_id == 0) {
            $html .= '<b>' . esc_attr($row->user_name) . '</b>';
        } else {
            $html .= '<b>' . get_the_author_meta('display_name', $row->user_id) . '</b>';
        }
        $html .= '<br />' . $row->ip_address;

        return $html;
    }

    public function column_rating($row) {
        return sprintf('<span class="wpbdp-ratings-stars" data-readonly="readonly" data-value="%s"></span>', $row->rating);
    }

    public function column_comment($row) {
        $html  = '';

        $html .= '<div class="submitted-on">';
        $html .= sprintf(__('Submitted on <i>%s</i>', 'wpbdp-ratings'), date_i18n(get_option('date_format'), strtotime($row->created_on)));
        $html .= '</div>';

        $html .= '<p>' . substr(esc_attr($row->comment), 0, 100) . '</p>';
        $html .= '<a name="review-' . $row->id . '"></a>';

        $actions = array();
        $actions['approve_rating'] = sprintf('<a href="%s">%s</a>',
                                      esc_url(add_query_arg(array('action' => 'approve', 'id' => $row->id))),
                                      __('Approve', 'wpbdp-ratings'));
        $actions['delete'] = sprintf('<a href="%s">%s</a>',
                                      esc_url(add_query_arg(array('action' => 'delete', 'id' => $row->id))),
                                      __('Delete', 'wpbdp-ratings'));        
        $html .= $this->row_actions($actions);

        return $html;
    }

    public function column_listing($row) {
        return sprintf('<a href="%s">%s</a>', get_permalink($row->listing_id), get_the_title($row->listing_id));
    }

}


class BusinessDirectory_RatingsModuleAdmin {

    public function __construct() {
        add_action('admin_notices', array($this, '_admin_notices'));

        if ( ! $this->check_requirements() )
            return;

        add_action('admin_init', array($this, '_add_admin_metabox'));
        add_action('wpbdp_admin_menu', array($this, '_admin_menu'));

        add_action( 'wp_ajax_wpbdp-ratings-add', array( &$this, 'ajax_add_rating' ) );
    }

    /*
     * Requirements check.
     */

    private function check_requirements() {
        return function_exists('wpbdp_get_version') && version_compare(wpbdp_get_version(), BusinessDirectory_RatingsModule::REQUIRED_BD_VERSION, '>=');
    }

    public function _admin_notices() {
        if (!$this->check_requirements())
            echo sprintf('<div class="error"><p>Business Directory - Ratings Module requires Business Directory Plugin >= %s.</p></div>', BusinessDirectory_RatingsModule::REQUIRED_BD_VERSION);
    }

    /*
     * Admin.
     */

    public function _admin_menu($menu) {
        /* Commenting this section out so that this link always appears
        if (!wpbdp_get_option('ratings-require-approval'))
            return;
        */
        /*
        add_submenu_page($menu,
                         __('Listing ratings pending review', 'wpbdp-ratings'),
                         __('Ratings for review', 'wpbdp-ratings'),
                         'activate_plugins',
                         'wpbdp-ratings-pending-review',
                         array($this, '_admin_ratings_review'));
         * 
         */
        add_submenu_page($menu,
                         __('All Reviews', 'wpbdp-ratings'),
                         __('All Reviews', 'wpbdp-ratings'),
                         'activate_plugins',
                         'wpbdp-ratings-pending-review',
                         array($this, '_admin_ratings_review'));
        
        
        
    }

    public function _admin_ratings_review() {
        if ( isset($_GET['action']) && isset($_GET['id']) ) {
            global $wpdb;

            switch ($_GET['action']) {
                case 'approve':
                    $rating_id = intval( $_GET['id'] );
                    $review = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE id = %d", $rating_id ) );

                    if ( $review ) {
                        $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpbdp_ratings SET approved = %d WHERE id = %d", 1, $rating_id ) );
                        do_action( 'wpbdp_ratings_rating_approved', $review );
                    }

                    wpbdp()->admin->messages[] = __('The rating was approved.', 'wpbdp-ratings');
                    break;
                case 'delete':
                    $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}wpbdp_ratings WHERE id = %d", intval($_GET['id'])) );
                    wpbdp()->admin->messages[] = __('The rating was deleted.', 'wpbdp-ratings');
                    break;
                default:
                    break;
            }
        }

        echo wpbdp_admin_header();
        echo wpbdp_admin_notices();

        echo '<div id="wpbdp-ratings-pending-review">';

        $table = new BusinessDirectory_RatingsReviewTable();
        $table->prepare_items();
        $table->display();

        echo '</div>';

        echo wpbdp_admin_footer();
    }

    public function _add_admin_metabox() {
        add_meta_box('wpbdp-ratings',
                    __('Listing Ratings', 'wpbdp-ratings'),
                    array($this, '_ratings_metabox'),
                    'wpbdp_listing',
                    'normal',
                    'low');
    }

    public function _ratings_metabox($listing) {
        $reviews = wpbdp_ratings()->get_reviews($listing->ID);

        echo wpbdp_render_page( plugin_dir_path( __FILE__ ) . 'templates/admin-post-review.tpl.php', array(
            'listing_id' => $listing->ID
        ) );

        echo wpbdp_render_page(plugin_dir_path(__FILE__) . 'templates/admin-ratings.tpl.php', array(
            'reviews' => $reviews
        ));
    }

    public function ajax_add_rating() {
        if ( !current_user_can( 'administrator' ) )
            die();

        global $wpdb;

        if ( $rating = wpbdp_getv( $_POST, 'rating', null ) ) {
            $rating['user_id'] = 0;

            if ( $user = get_user_by( 'login', $rating['user_name'] ) ) {
                $rating['user_id'] = $user->ID;
                $rating['user_name'] = '';
            }

            $rating['rating'] = intval( $rating['rating'] );
            $rating['approved'] = 1;
            $rating['created_on'] = current_time( 'mysql' );
            $rating = stripslashes_deep( $rating );

            if ( $wpdb->insert( $wpdb->prefix . 'wpbdp_ratings', $rating ) ) {
                $review = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_ratings WHERE id = %d", $wpdb->insert_id ) ) or die();

                $response = array();
                $response['ok'] = true;
                $response['html'] = wpbdp_render_page( plugin_dir_path( __FILE__ ) .'templates/admin-rating-row.tpl.php',
                                                       array( 'review' => $review )
                                                     );
            }

            echo json_encode( $response );
        }

        die();
    }

}
