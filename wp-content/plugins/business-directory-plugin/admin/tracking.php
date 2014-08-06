<?php
if ( !defined( 'WPBDP_VERSION' ) ) die; // This page should not be called directly.

/**
 * @package admin
 */

if ( !class_exists( 'WPBDP_SiteTracking' ) ) {

/**
 * Class used for anonymously tracking of users setups.
 * @since 3.2
 */
class WPBDP_SiteTracking {

    const TRACKING_URL = 'http://data.businessdirectoryplugin.com/tr/';

    public function __construct() {
        if ( !wp_next_scheduled( 'wpbdp_site_tracking' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), 'daily', 'wpbdp_site_tracking' );
        }

        add_action( 'wpbdp_site_tracking', array( $this, 'tracking' ) );
        // do_action( 'wpbdp_site_tracking' );
    }

    public function site_hash() {
        $hash = get_option( 'wpbdp-site_tracking_hash', '' );

        if ( !$hash ) {
            $hash = sha1( site_url() );
            update_option( 'wpbdp-site_tracking_hash', $hash );
        }

        return $hash;
    }

    public function tracking() {
        global $wpdb;

        wpbdp_log( 'Performing (scheduled) site tracking.' );

        $site_hash = $this->site_hash();
        $data = get_transient( 'wpbdp-site_tracking_data' );

        if ( !$data ) {
            wpbdp_log( 'Gathering site tracking metrics.' );

            $data = array();

            // General site info.
            $data['hash'] = $site_hash;
            $data['site-info'] = array(
                'title' => get_bloginfo( 'name' ),
                'wp-version' => get_bloginfo( 'version' ),
                'bd-version' => WPBDP_VERSION,
                /*'url' => site_url()*/
                'lang' => get_locale(),
                'users' => count( get_users() )
            );

            // Plugins info.
            if ( !function_exists( 'get_plugin_data' ) )
                require_once ABSPATH . 'wp-admin/includes/admin.php';

            $data['plugins'] = array();
            foreach ( get_option( 'active_plugins' ) as $path ) {
                $plugin = get_plugin_data( WP_PLUGIN_DIR . '/' . $path );
                
                $data['plugins'][] = array(
                    'id' => str_replace( '/' . basename( $path ),  '', $path ),
                    'name' => wpbdp_getv( $plugin, 'Name', '' ),                    
                    'version' => wpbdp_getv( $plugin, 'Version', '' ),
                    'plugin_uri' => wpbdp_getv( $plugin, 'PluginURI', '' ),
                    'author' => wpbdp_getv( $plugin, 'AuthorName', '' ),
                    'author_uri' => wpbdp_getv( $plugin, 'AuthorURI', '' )
                );
            }

            // Theme info.
            $data['theme'] = array();

            if ( function_exists( 'wp_get_theme' ) ) {
                $theme = wp_get_theme();

                foreach ( array( 'Name', 'ThemeURI', 'Version', 'Author', 'AuthorURI' ) as $k ) {
                    $data['theme'][ strtolower( $k ) ] = $theme->display( $k, false, false );
                }

                $data['theme']['parent'] = array();
                if ( $theme_parent = $theme->parent() ) {
                    foreach ( array( 'Name', 'ThemeURI', 'Version', 'Author', 'AuthorURI' ) as $k ) {
                        $data['theme']['parent'][ strtolower( $k ) ] = $theme_parent->display( $k, false, false );
                    }
                } else {
                    $data['theme']['parent'] = null;
                }
            } else {
                $theme = (object) get_theme_data( get_stylesheet_directory() . '/style.css' );

                foreach ( array( 'Name', 'Version', 'Author' ) as $k ) {
                    $data['theme'][ strtolower( $k ) ] = wpbdp_getv( $theme, $k, '' );
                }
            }

            // Posts.
            $data['posts'] = array();

            foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
                $count = wp_count_posts( $post_type );
                $data['posts'][ $post_type ] = intval( $count->publish );
            }

            // Taxonomies.
            $data['taxonomies']  = array();

            foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $tax ) {
                $data['taxonomies'][ $tax->name ] = array(
                    'name' => $tax->name,
                    'label' => $tax->label,
                    'terms' => intval( wp_count_terms( $tax->name, array( 'hide_empty' => 0 ) ) )
                );
            }

            // Environment.
            $data['environment'] = array();
            $data['environment']['os'] = php_uname( 's' ) . ' ' . php_uname( 'r' ) . ' ' . php_uname( 'm' );
            $data['environment']['php'] = phpversion();
            $data['environment']['mysql'] = $wpdb->get_var( 'SELECT @@version' );
            $data['environment']['server-software'] = $_SERVER['SERVER_SOFTWARE'];

            wp_remote_post( self::TRACKING_URL, array(
                'method' => 'POST',
                'blocking' => false,
                'body' => $data
            ) );

            set_transient( 'wpbdp-site_tracking_data', true, 7 * 60 * 60 * 24 );
        }
        // delete_transient( 'wpbdp-site_tracking_data' );


    }

    public static function handle_ajax_response() {
        if ( !wp_verify_nonce( $_POST['nonce'], 'wpbdp-set_site_tracking' ) )
            exit();

        if ( isset( $_POST['enable_tracking'] ) ) {
            update_option( 'wpbdp-tracking-dismissed', true );

            if ( intval( $_POST['enable_tracking'] ) ) {
                wpbdp_set_option( 'tracking-on', true );
            }
        }
    }

    public static function request_js() {
        $content  = '';
        $content .= '<h3>' . _x( 'Help Improve Business Directory', 'tracking', 'WPBDM' ) . '</h3>';
        $content .= '<p>';
        $content .= _x( 'Can Business Directory keep track of your theme, plugins, and other non-personal, non-identifying information to help us in testing the plugin for future releases?', 'tracking', 'WPBDM' );
        $content .= '<br />';
        $content .= '&#149; ' . sprintf( '<a href="%s" target="_blank">%s</a>', 'http://businessdirectoryplugin.com/what-we-track', _x( 'What do you track?', 'tracking', 'WPBDM' ) );
        $content .= '</p>';
?>
    <script type="text/javascript">
        //<![CDATA[
        jQuery(function($){
            function WPBDP_SiteTracking_answer(enable) {
                var args = {
                    action: "wpbdp-set_site_tracking",
                    enable_tracking: enable ? 1 : 0,
                    nonce: "<?php echo wp_create_nonce( 'wpbdp-set_site_tracking' ); ?>"
                };

                $.post(ajaxurl, args, function() {
                    $('#wp-pointer-0').remove();
                });
            }

            $('#wpadminbar').pointer({
                'content': <?php echo json_encode( $content ); ?>,
                'position': { 'edge': 'top', 'align': 'center' },
                'buttons': function(event, t) {
                    var do_not_track = $('<a id="wpbdp-pointer-b2" class="button-secondary" style="margin-right: 5px;"><?php _ex("No, Thanks.", "tracking", "WPBDM") ?></a>');
                    do_not_track.bind('click.pointer', function() { t.element.pointer('close'); });

                    return do_not_track;
                }
            }).pointer('open');

            $('#wpbdp-pointer-b2').before('<a id="wpbdp-pointer-b1" class="button-primary"><?php _ex("Allow Tracking", "tracking", "WPBDM"); ?></a>');

            $('#wpbdp-pointer-b1').click(function(){
                WPBDP_SiteTracking_answer( true );
            });

            $('#wpbdp-pointer-b2').click(function(){
                WPBDP_SiteTracking_answer( false );
            });
        });
        //]]>
    </script>
<?php 
    }

}

}
