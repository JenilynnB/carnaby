<?php

if ( ! class_exists('WeDevs_Settings_API' ) )
    require_once ( 'class-settings-api.php' );

/**
 * MasterSlider Setting page
 *
 * @author Tareq Hasan
 */
if ( !class_exists('MSP_Settings' ) ):

class MSP_Settings {

    private $settings_api;

    function __construct() {

        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 11 );
        add_action( 'admin_action_msp_envato_license', array( $this, 'envato_license_updated' ) );
        
        add_action( 'admin_footer-master-slider_page_masterslider-setting', array( $this, 'print_setting_script' ) );
        add_filter( 'axiom_wedev_setting_section_submit_button', array( $this, 'section_submit_button' ), 10, 2 );
    }


    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields  ( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }


    function section_submit_button( $button_markup, $section ){
        if( isset( $section['id'] ) && 'msp_envato_license' == $section['id'] ){
            $is_license_actived = get_option( MSWP_SLUG . '_is_license_actived', 0 );
            return sprintf( '<a id="validate_envato_license" class="button button-primary button-large" data-activate="%1$s" data-isactive="%3$d" data-deactivate="%2$s" data-validation="%4$s" >%1$s</a>%5$s', 
                            __( 'Activate License', MSWP_TEXT_DOMAIN ), __( 'Deactivate License', MSWP_TEXT_DOMAIN ), (int)$is_license_actived,
                            __( 'Validating ..', MSWP_TEXT_DOMAIN ), '<div class="msp-msg-nag">is not actived</div>' );
        }
        return $button_markup;
    }


    function admin_menu() {
        
        add_submenu_page(
            MSWP_SLUG,
            __( 'Settings' , MSWP_TEXT_DOMAIN ),
            __( 'Settings' , MSWP_TEXT_DOMAIN ),
            apply_filters( 'masterslider_setting_capability', 'manage_options' ),
            MSWP_SLUG . '-setting',
            array( $this, 'render_setting_page' )
        );
    }

    function get_settings_sections() {
        $sections = array(
            
            array(
                'id' => 'msp_general_setting',
                'title' => __( 'General Settings', MSWP_TEXT_DOMAIN )
            )
        );

        if( ! apply_filters( MSWP_SLUG.'_disable_auto_update', 0 ) ) {
            $sections[] = array(
                'id' => 'msp_envato_license',
                'title' => __( 'License Activation', MSWP_TEXT_DOMAIN ),
                'desc'  => __( 'To activate automatic update for master slider a valid purchase code is required.', MSWP_TEXT_DOMAIN )
            );
        }

        $woo_enabled = msp_is_plugin_active( 'woocommerce/woocommerce.php' );
        $woo_section_desc = $woo_enabled ? '': __( 'You need to install and activate WooCommerce plugin to use following options.', MSWP_TEXT_DOMAIN );

        $sections[] = array(
            'id' => 'msp_woocommerce',
            'title' => __( 'WooCommerce Setting', MSWP_TEXT_DOMAIN ),
            'desc'  => $woo_section_desc
        );

        $sections[] = array(
            'id' => 'msp_advanced',
            'title' => __( 'Advanced Setting', MSWP_TEXT_DOMAIN )
        );

        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        
        $settings_fields = array();
            
        $settings_fields['msp_general_setting'] = array(
            array(
                'name'  => 'hide_info_table',
                'label' => __( 'Hide info table', MSWP_TEXT_DOMAIN ),
                'desc'  => __( 'If you want to hide "Latest video tutorials" table on master slider admin panel check this field.', MSWP_TEXT_DOMAIN ),
                'type'  => 'checkbox'
            ),
            array(
                'name'  => '_enable_cache',
                'label' => __( 'Enable cache?', MSWP_TEXT_DOMAIN ),
                'desc'  => __( 'Enable cache to make Masterslider even more faster!', MSWP_TEXT_DOMAIN ),
                'type'  => 'checkbox'
            ),
            array(
                'name'  => '_cache_period',
                'label' => __( 'Cache period time', MSWP_TEXT_DOMAIN ),
                'desc'  => __( 'The cache refresh time in hours. Cache is also cleared when you click on "Save Changes" in slider panel.', MSWP_TEXT_DOMAIN ),
                'type'  => 'text',
                'default' => '12',
                'sanitize_callback' => 'floatval'
            )
        );
            /*
            'msp_general_setting' => array(
                array(
                    'name' => 'text_val',
                    'label' => __( 'Text Input (integer validation)', 'wedevs' ),
                    'desc' => __( 'Text input description', 'wedevs' ),
                    'type' => 'text',
                    'default' => 'Title',
                    'sanitize_callback' => 'intval'
                ),
                array(
                    'name' => 'textarea',
                    'label' => __( 'Textarea Input', 'wedevs' ),
                    'desc' => __( 'Textarea description', 'wedevs' ),
                    'type' => 'textarea'
                ),
                array(
                    'name' => 'checkbox',
                    'label' => __( 'Checkbox', 'wedevs' ),
                    'desc' => __( 'Checkbox Label', 'wedevs' ),
                    'type' => 'checkbox'
                ),
                array(
                    'name' => 'radio',
                    'label' => __( 'Radio Button', 'wedevs' ),
                    'desc' => __( 'A radio button', 'wedevs' ),
                    'type' => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no' => 'No'
                    )
                ),
                array(
                    'name' => 'multicheck',
                    'label' => __( 'Multile checkbox', 'wedevs' ),
                    'desc' => __( 'Multi checkbox description', 'wedevs' ),
                    'type' => 'multicheck',
                    'options' => array(
                        'one' => 'One',
                        'two' => 'Two',
                        'three' => 'Three',
                        'four' => 'Four'
                    )
                ),
                array(
                    'name' => 'selectbox',
                    'label' => __( 'A Dropdown', 'wedevs' ),
                    'desc' => __( 'Dropdown description', 'wedevs' ),
                    'type' => 'select',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no' => 'No'
                    )
                ),
                array(
                    'name' => 'password',
                    'label' => __( 'Password', 'wedevs' ),
                    'desc' => __( 'Password description', 'wedevs' ),
                    'type' => 'password',
                    'default' => ''
                ),
                array(
                    'name' => 'file',
                    'label' => __( 'File', 'wedevs' ),
                    'desc' => __( 'File description', 'wedevs' ),
                    'type' => 'file',
                    'default' => ''
                )
            ),*/

        if( ! apply_filters( MSWP_SLUG.'_disable_auto_update', 0 ) ) {
            
            $settings_fields['msp_envato_license'] = array(

                    array(
                        'name'      => 'username',
                        'label'     => __( 'Your Envato Username'     , MSWP_TEXT_DOMAIN ),
                        'desc'      => '',
                        'type'      => 'text',
                        'default'   => ''
                    ),
                    array(
                        'name'      => 'api_key',
                        'label'     => __( 'Your Secret API Key' , MSWP_TEXT_DOMAIN ),
                        'desc'      => __( 'To find your API key, navigate to your envato account, select settings from the account dropdown, then navigate to the API Keys tab. <a href="http://codecanyon.net/help/api" target="_blank">More info ..</a>.', MSWP_TEXT_DOMAIN ),
                        'type'      => 'password',
                        'default'   => ''
                        ),
                    array(
                        'name'      => 'purchase_code',
                        'label'     => __( 'Master Slider Purchase Code' , MSWP_TEXT_DOMAIN ),
                        'desc'      => __( 'Please enter purchase code for your Master Slider', MSWP_TEXT_DOMAIN ) . sprintf( ' (<a href="http://support.averta.net/envato/knowledgebase/find-item-purchase-code/" target="_blank" >%s</a>)',
                                                                                                                              __( "How to find your Item's Purchase Code", MSWP_TEXT_DOMAIN ) ),
                        'type'      => 'text',
                        'default'   => ''
                    )
            );
        }

        $settings_fields['msp_woocommerce'] = array(

                array(
                    'name' => 'enable_single_product_slider',
                    'label' => __( 'Enable slider in product single page', 'wedevs' ),
                    'desc' => __( 'Replace woocommerce default product slider in product single page with Masterslider', MSWP_TEXT_DOMAIN ),
                    'type' => 'checkbox'
                )
        );

        $settings_fields['msp_advanced'] = array(
            array(
                'name'  => 'allways_load_ms_assets',
                'label' => __( 'Load assets on all pages?', MSWP_TEXT_DOMAIN ),
                'desc'  => __( 'By default, Master Slider will load corresponding JavaScript files on demand. but if you need to load assets on all pages, check this option. ( For example, if you plan to load Master Slider via Ajax, you need to check this option ) ', MSWP_TEXT_DOMAIN ),
                'type'  => 'checkbox'
            )
        );

        return $settings_fields;
    }

    function render_setting_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }


    /**
     * This code uses localstorage for displaying active tabs
     * 
     */
    function print_setting_script() {
        ?>
        <script>
        (function($) {
        $(function() {

            var $username       = $("#msp_envato_license\\[username\\]"),
                $api_key        = $("#msp_envato_license\\[api_key\\]"),
                $purchase_code  = $("#msp_envato_license\\[purchase_code\\]"),
                $activate_btn   = $('#validate_envato_license');

            var _is_license_active = $activate_btn.data('isactive');

            function msp_enable_activation_form( activate ){
                if( activate ){
                    $activate_btn.text( $activate_btn.data('deactivate') );
                    $username.prop( 'disabled', true );
                    $api_key.prop( 'disabled', true );
                    $purchase_code.prop( 'disabled', true );
                    $activate_btn.siblings('.msp-msg-nag').html('Your license is active');

                } else {
                    $activate_btn.text( $activate_btn.data('activate') );
                    $username.prop( 'disabled', false );
                    $api_key.prop( 'disabled', false );
                    $purchase_code.prop( 'disabled', false );
                    $activate_btn.siblings('.msp-msg-nag').html('Your license is NOT active');
                }
            }

            msp_enable_activation_form( _is_license_active );


            $activate_btn.on('click', function(event){
                event.preventDefault();
                $this= $(this);

                $this.text( $this.data('validation') );
                var do_activation = _is_license_active ? 0 : 1;

                jQuery.post(
                    ajaxurl,
                    {
                        nonce:   $this.data( 'nonce' ),
                        action:  'msp_license_activation',
                        doActivation: do_activation,
                        username: $username.val(),
                        api_key : $api_key.val(),
                        purchase_code : $purchase_code.val()
                    },
                    function( res ){
                        res = JSON.parse(res);

                        msp_enable_activation_form( res.success );
                        $this.siblings('.msp-msg-nag').html( res.message );
                        $this.data('isactive', String(res.success) );
                        _is_license_active = res.success;
                    }
                );
                    
            });

            

        });
        })(jQuery);
        </script>
        <style>
            .master-slider_page_masterslider-setting .wrap input[disabled] { background-color:#e0e0e0; }
            .msp-msg-nag {
                display: inline-block;
                line-height: 14px;
                padding: 8px 15px;
                font-size: 14px;
                text-align: left;
                margin: 0 20px;
                background-color: #fff;
                border-left: 4px solid #ffba00;
                -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
                box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            }
        </style>
        <?php
    }

}

endif;

$settings = new MSP_Settings();