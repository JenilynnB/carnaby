<?php
class XooUserAdmin extends XooUserUltraCommon 
{

	var $options;
	var $wp_all_pages = false;
	var $userultra_default_options;
	var $valid_c;
	
	var $notifications_email = array();

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userultra';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( xoousers_path . 'xoousers.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		$this->set_default_email_messages();		
		
		$this->update_default_option_ini();
		
		$this->set_font_awesome();
		
		
		
		/* Priority actions */
		add_action('admin_menu', array(&$this, 'add_menu'), 9);
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
		add_action( 'wp_ajax_save_fields_settings', array( $this, 'save_fields_settings' ));
		add_action( 'wp_ajax_add_new_custom_profile_field', array( $this, 'add_new_custom_profile_field' ));
		add_action( 'wp_ajax_delete_profile_field', array( $this, 'delete_profile_field' ));
		add_action( 'wp_ajax_sort_fileds_list', array( $this, 'sort_fileds_list' ));
		add_action( 'wp_ajax_custom_fields_reset', array( $this, 'custom_fields_reset' ));
		
		add_action( 'wp_ajax_create_uploader_folder', array( &$this, 'create_uploader_folder' ));		
		
	
		
		
		
	}
	
	function admin_init() 
	{
		
		$this->tabs = array(
		    'main' => __('Dashboard','xoousers'),
			'fields' => __('Fields','xoousers'),
			'settings' => __('Settings','xoousers'),			
			'membership' => __('Membership','xoousers'),
			'orders' => __('Orders','xoousers'),			
			'import' => __('Sync & Import','xoousers'),			
			'mail' => __('Notifications','xoousers'),			
			'permalinks' => __('Permalinks','xoousers'),
			'gateway' => __('Gateways','xoousers'),			
			'help' => __('Doc','xoousers'),
			'pro' => __('Go Pro!','xoousers'),
		);
		
		$this->default_tab = 'main';		
		
		$this->tabs_membership = array(
		    'main' => __('Membership Plans','userultra'),
			
		);
		$this->default_tab_membership = 'main';
		
		
	}
	
	public function custom_fields_reset () 
	{
		
		if($_POST["p_confirm"]=="yes")
		{
			update_option('usersultra_profile_fields', NULL);		
		
		}	
		
		
	}
	
	
	public function update_default_option_ini () 
	{
		$this->options = get_option('userultra_options');		
		$this->userultra_set_default_options();
		
		if (!get_option('userultra_options')) 
		{
			
			update_option('userultra_options', $this->userultra_default_options );
		}
		
		
	}
	
	
	
	
	
	function get_pending_verify_requests_count()
	{
		$count = 0;
		
		// verification status
		$pending = get_option('userultra_verify_requests');
		if (is_array($pending) && count($pending) > 0){
			$count = count($pending);
		}
		
		// waiting email approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		// waiting admin approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending_admin',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		if ($count > 0){
			return '<span class="upadmin-bubble-new">'.$count.'</span>';
		}
	}
	
	function get_pending_verify_requests_count_only(){
		$count = 0;
		
		// verification status
		$pending = get_option('userultra_verify_requests');
		if (is_array($pending) && count($pending) > 0){
			$count = count($pending);
		}
		
		// waiting email approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		// waiting admin approve
		$users = get_users(array(
			'meta_key'     => '_account_status',
			'meta_value'   => 'pending_admin',
			'meta_compare' => '=',
		));
		if (isset($users)) {
			$count += count($users);
		}
		
		if ($count > 0){
			return $count;
		}
	}
	
	
		
	
	function admin_head(){
		$screen = get_current_screen();
		$slug = $this->slug;
		//$icon = userultra_url . "admin/images/$slug-32.png";
		//echo '<style type="text/css">';
			///if (in_array( $screen->id, array( $slug ) ) || strstr($screen->id, $slug) ) {
				//print "#icon-$slug {background: url('{$icon}') no-repeat left;}";
		//	}
		//echo '</style>';
	}

	function add_styles()
	{
	
		wp_register_style('userultra_admin', xoousers_url.'admin/css/userlutra.admin.css');
		wp_enqueue_style('userultra_admin');			
			
		wp_register_script('userultra_chosen', xoousers_url . 'admin/scripts/admin-chosen.js');
		wp_enqueue_script('userultra_chosen');
		
		
		//color picker		
		 wp_enqueue_style( 'wp-color-picker' );		 
		 wp_register_script( 'userultra_color_picker', xoousers_url.'admin/scripts/color-picker-js.js', array( 
			'wp-color-picker'
		) );
		wp_enqueue_script( 'userultra_color_picker' );
		
		wp_register_script( 'userultra_admin', xoousers_url.'admin/scripts/admin.js', array( 
			'jquery','jquery-ui-core','jquery-ui-draggable','jquery-ui-droppable',	'jquery-ui-sortable'
		), null );
		wp_enqueue_script( 'userultra_admin' );
		
		
		/* Font Awesome */
		wp_register_style( 'xoouserultra_font_awesome', xoousers_url.'css/css/font-awesome.min.css');
		wp_enqueue_style('xoouserultra_font_awesome');
		
		/*google graph*/
		
		wp_register_script('userultra_jsgooglapli', 'http://www.google.com/jsapi');
		wp_enqueue_script('userultra_jsgooglapli');
		
				
		
	}
	
	function add_menu() 
	{
		global $xoouserultra ;
	
		$pending_count = $xoouserultra->userpanel->get_pending_activation_count();
		$pending_title = esc_attr( sprintf(__( '%d new manual activation requests','xoousers'), $pending_count ) );
		if ($pending_count > 0)
		{
			$menu_label = sprintf( __( 'Users Ultra %s','userultra' ), "<span class='update-plugins count-$pending_count' title='$pending_title'><span class='update-count'>" . number_format_i18n($pending_count) . "</span></span>" );
			
		} else {
			
			$menu_label = __('Users Ultra','userultra');
		}
		
		add_menu_page( __('Users Ultra','userultra'), $menu_label, 'manage_options', $this->slug, array(&$this, 'admin_page'), xoousers_url .'admin/images/small_logo_16x16.png', '159.140');
		
		do_action('userultra_admin_menu_hook');
		
			
	}
	
	function admin_tabs_membership( $current = null ) 
	{
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->slug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->slug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	function admin_tabs( $current = null ) {
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			
			
			
			foreach( $tabs as $tab => $name ) :
			
				$custom_badge = "";
				
				if($tab=="pro"){
					
					$custom_badge = 'uultra-pro-tab-bubble ';
					
				}
			
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active ".$custom_badge."' href='?page=".$this->slug."&tab=$tab'>$name </a>";
				else :
					$links[] = "<a class='nav-tab ".$custom_badge."' href='?page=".$this->slug."&tab=$tab'>$name </a>";
				endif;
				
				
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	
	
	function do_action(){
		global $userultra;
				
		
	}
	
	
	function reset() {
		update_option('userultra', $this->userultra_default_options() );
		$this->options = array_merge( $this->options, $his->userultra_default_options() );
		echo '<div class="updated"><p><strong>'.__('Settings are reset to default.','userultra').'</strong></p></div>';
	}
	
	
	function woo_sync() {
		userultra_admin_woo_sync();
		echo '<div class="updated"><p><strong>'.__('WooCommerce fields have been added.','userultra').'</strong></p></div>';
	}
	
	function woo_sync_del(){
		userultra_admin_woo_sync_erase();
		echo '<div class="updated"><p><strong>'.__('WooCommerce fields have been removed.','userultra').'</strong></p></div>';
	}
	
	
	
	/* set a global option */
	function userultra_set_option($option, $newvalue)
	{
		$settings = get_option('userultra_options');
		$settings[$option] = $newvalue;
		update_option('userultra_options', $settings);
	}
	
	/* default options */
	function userultra_set_default_options()
	{
	
		$this->userultra_default_options = array(
						'html_user_login_message' => __('Please log in to view / edit your profile.','xoouser'),
						'html_login_to_view' => __('Please log in to view user profiles.','xoouser'),
						'html_private_content' => __('This content is for members only. You must log in to view this content.','xoouser'),
						'clickable_profile' => 1,
						'set_password' => 1,
						'guests_can_view' => 1,
						'users_can_view' => 1,
						'style' => 'default',
						'profile_page_id' => '0',
						'hide_admin_bar' => '1',
						'registration_rules' => '1',
						
						'media_avatar_width' => '190',
						'media_avatar_height' => '190',
						
						
						
						'media_photo_mini_width' => '80',
						'media_photo_mini_height' => '80',
						'media_photo_thumb_width' => '190',
						'media_photo_thumb_height' => '180',
						'media_photo_large_width' => '700',
						'media_photo_large_height' => '800',
						'media_uploading_folder' => 'wp-content/usersultramedia',
						
						'uultra_front_publisher_default_amount' => '9999',
						'uultra_front_publisher_default_status' => 'pending',
						'uultra_front_publisher_allows_category' => 'yes',
						'uultra_front_publisher_default_category' => '',
						
						
						'usersultra_my_account_slug' => 'myaccount',						
						'usersultra_slug' => 'profile',
						'usersultra_login_slug' => 'login',
						'usersultra_registration_slug' => 'registration',
						'usersultra_directory_slug' => 'directory',	
						'mailchimp_text' => __('Subscribe to receive our periodic email updates','xoousers'),						
														
										
						'login_page_id' => '0',
						'registration_page_id' => '0',
						'redirect_backend_profile' => '0',
						'redirect_backend_registration' => '0',
						'redirect_backend_login' => '0',
						'paid_membership_currency' => 'USD',
						'paid_membership_symbol' => '$',		
						
						'uurofile_setting_display_photos' => 'public',
						
						
						'html_private_content' => __('User registration is currently not allowed.','xoouser'),
						'messaging_welcome_email_client' => $this->get_email_template('new_account'),
						'messaging_welcome_email_client_admin' => $this->get_email_template('new_account_noti_admin'),
						
						
						'messaging_welcome_email_with_activation_client' => $this->get_email_template('new_account_activation_link'),
						
						'messaging_re_send_activation_link' => $this->get_email_template('messaging_re_send_activation_link'),
						
						'messaging_admin_moderation_user' => $this->get_email_template('new_account_admin_moderation'),
						'messaging_admin_moderation_admin' => $this->get_email_template('new_account_admin_moderation_admin'),
						
						'messaging_welcome_email_with_activation_admin' => $this->get_email_template('new_account_activation_link_admin'),
						
						
						
						'account_verified_sucess_message_body' => $this->get_email_template('account_verified_sucess_message_body'),
						
						'message_friend_request' => $this->get_email_template('message_friend_request'),
						
						
						
						
						'messaging_welcome_email_admin' => __($this->get_email_template('new_account_admin'),'xoouser'),
						'messaging_user_pm' => __($this->get_email_template('private_message_noti'),'xoouser'),
						'reset_lik_message_body' => __($this->get_email_template('reset_lik_message_body'),'xoouser'),
						'password_reset_confirmation' => $this->get_email_template('password_reset_confirmation'),
						
						'admin_account_active_message_body' => __($this->get_email_template('admin_account_active_message_body'),'xoouser'),
						'admin_account_deny_message_body' => $this->get_email_template('admin_account_deny_message_body'),
						
						'messaging_send_from_name' => __('Users Ultra Plugin','xoouser'),
						
						
						
						
						
				);
		
	}
	
	public function set_default_email_messages()
	{
		$line_break = "\r\n";
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Thanks for registering. Your account is now active.","xoousers") .  $line_break.$line_break;
		$email_body .= __("To login please visit the following URL:","xoousers") .  $line_break.$line_break;
		$email_body .= "{{userl_ultra_login_url}}" . $line_break.$line_break;
		$email_body .= __('Your account e-mail: {{userultra_user_email}}','xoousers') . $line_break;
		$email_body .= __('Your account username: {{userultra_user_name}}','xoousers') . $line_break;
		$email_body .= __('Your account password: {{userultra_pass}}','xoousers') . $line_break.$line_break;
		$email_body .= __('If you have any problems, please contact us at {{userultra_admin_email}}.','xoousers') . $line_break.$line_break;
		$email_body .= __('Best Regards!','xoousers');
	    $this->notifications_email['new_account'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Your password has been reset.","xoousers") .  $line_break.$line_break;
		$email_body .= __("To login please visit the following URL:","xoousers") .  $line_break.$line_break;
		$email_body .= "{{userl_ultra_login_url}}" . $line_break.$line_break;
		$email_body .= __('Your account e-mail: {{userultra_user_email}}','xoousers') . $line_break;
		$email_body .= __('Your account username: {{userultra_user_name}}','xoousers') . $line_break;
		$email_body .= __('Your account password: {{userultra_pass}}','xoousers') . $line_break.$line_break;
		$email_body .= __('If you have any problems, please contact us at {{userultra_admin_email}}.','xoousers') . $line_break.$line_break;
		$email_body .= __('Best Regards!','xoousers');
	    $this->notifications_email['password_reset_confirmation'] = $email_body;
		
		
		
		
		//notify admin new registration
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("A new user has been registered.","xoousers") .  $line_break.$line_break;
		
		$email_body .= __('Account e-mail: {{userultra_user_email}}','xoousers') . $line_break;
		$email_body .= __('Account username: {{userultra_user_name}}','xoousers') . $line_break;
		$email_body .= __('This is an automated notification. No further action is needed.','xoousers'). $line_break.$line_break;
		$email_body .= __('Best Regards!','xoousers');
	    $this->notifications_email['new_account_noti_admin'] = $email_body;
		
			
		
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Thanks for registering. Your account needs activation.","xoousers") .  $line_break.$line_break;
		$email_body .= __("Please click on the link below to activate your account:","xoousers") .  $line_break.$line_break;
		$email_body .= "{{user_ultra_activation_url}}" . $line_break.$line_break;
		$email_body .= __('Your account e-mail: {{userultra_user_email}}','xoousers') . $line_break;
		$email_body .= __('Your account username: {{userultra_user_name}}','xoousers') . $line_break;
		$email_body .= __('Your account password: {{userultra_pass}}','xoousers') . $line_break.$line_break;
		
		$email_body .= __('If you have any problems, please contact us at {{userultra_admin_email}}.','xoousers') . $line_break.$line_break;
		$email_body .= __('Best Regards!','xoousers');
	    $this->notifications_email['new_account_activation_link'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("We are re-sending you the activation link.","xoousers") .  $line_break.$line_break;
		$email_body .= __("Please click on the link below to activate your account:","xoousers") .  $line_break.$line_break;
		$email_body .= "{{user_ultra_activation_url}}" . $line_break.$line_break;
	
		$email_body .= __('If you have any problems, please contact us at {{userultra_admin_email}}.','xoousers') . $line_break.$line_break;
		$email_body .= __('Best Regards!','xoousers');
	    $this->notifications_email['messaging_re_send_activation_link'] = $email_body;
		
		//admin
		$email_body = __('Hi Admin,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("An account needs activation.","xoousers") .  $line_break.$line_break;
		
		$email_body .= __('Account e-mail: {{userultra_user_email}}','xoousers') . $line_break;
		$email_body .= __('Account username: {{userultra_user_name}}','xoousers') . $line_break;
		$email_body .= __('Please login to your admin to activate the account.','xoousers') . $line_break.$line_break;
		$email_body .= __('Best Regards!','xoousers');
	    $this->notifications_email['new_account_activation_link_admin'] = $email_body;	
		
		//admin manually approved --06-20-2014
		$email_body = __('Hi Admin,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("An account needs approbation.","xoousers") .  $line_break.$line_break;		
		$email_body .= __('Account e-mail: {{userultra_user_email}}','xoousers') . $line_break;
		$email_body .= __('Account username: {{userultra_user_name}}','xoousers') . $line_break;
		$email_body .= __('Please login to your admin to activate the account.','xoousers') . $line_break.$line_break;
		$email_body .= __('Best Regards!','xoousers');
	    $this->notifications_email['new_account_admin_moderation_admin'] = $email_body;
		
		//client manually approved --06-20-2014
		$email_body = __('Hi {{userultra_user_name}},' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Your account will be reviewed by the admin soon.","xoousers") .  $line_break.$line_break;		
		$email_body .= __('Best Regards!','xoousers');
	    $this->notifications_email['new_account_admin_moderation'] = $email_body;
		
		
		$email_body = __('Hi,',"xoousers") . $line_break.$line_break;
		$email_body .= __("{{userultra_user_name}} has just created a new account at {{user_ultra_blog_name}}.","xoousers") . $line_break.$line_break;
		$email_body .= __("You can check his profile via the following link:","xoousers") .$line_break;
		$email_body .= "{{userl_ultra_login_url}}" .$line_break.$line_break;
		$email_body .= __('This is an automated notification. No further action is needed.','xoousers'). $line_break.$line_break;		
		$email_body .= __('Best Regards!','xoousers') . $line_break.$line_break;		
	    $this->notifications_email['new_account_admin'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("{{userultra_user_name}} has just sent you a new private message at {{user_ultra_blog_name}}.","xoousers") . $line_break.$line_break;
		
		$email_body .= __("Subject: {{userultra_pm_subject}}","xoousers") . $line_break;
		$email_body .=__("Message: {{userultra_pm_message}}","xoousers") . $line_break;
		$email_body .= __('This is an automated notification. No further action is needed.','xoousers'). $line_break.$line_break;		
		$email_body .= __('Best Regards!','xoousers') . $line_break.$line_break;		
	    $this->notifications_email['private_message_noti'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("You have a new friend request at {{user_ultra_blog_name}}.","xoousers") . $line_break.$line_break;				
				
		$email_body .= __('Best Regards!','xoousers') . $line_break.$line_break;		
	    $this->notifications_email['message_friend_request'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Please use the following link to reset your password.","xoousers") . $line_break.$line_break;			
		$email_body .= "{{userultra_reset_link}}".$line_break.$line_break;
		$email_body .= __('If you did not request a new password delete this email.','xoousers'). $line_break.$line_break;		$email_body .= __('Best Regards!','xoousers') . $line_break.$line_break;		
	    $this->notifications_email['reset_lik_message_body'] = $email_body;	
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Your account has been activated.","xoousers") . $line_break.$line_break;	
		$email_body .= __('Please use the link below to get in your account.','xoousers'). $line_break.$line_break;	
		$email_body .=   "{{userl_ultra_login_url}}".$line_break.$line_break;	
		$email_body .= __('Best Regards!','xoousers') . $line_break.$line_break;		
	    $this->notifications_email['admin_account_active_message_body'] = $email_body;
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("Your account has been verified.","xoousers") . $line_break.$line_break;	
		$email_body .= __('Please use the link below to get in your account.','xoousers'). $line_break.$line_break;	
		$email_body .=   "{{userl_ultra_login_url}}".$line_break.$line_break;	
		$email_body .= __('Best Regards!','xoousers') . $line_break.$line_break;		
	    $this->notifications_email['account_verified_sucess_message_body'] = $email_body;	
		
		$email_body = __('Hi,' ,"xoousers") . $line_break.$line_break;
		$email_body .= __("We're sorry but your account has not been approved.","xoousers") . $line_break.$line_break;	
		$email_body .= __('This is an automated notification. No further action is needed.','xoousers'). $line_break.$line_break;	
		$email_body .= __('Best Regards!','xoousers') . $line_break.$line_break;		
	    $this->notifications_email['admin_account_deny_message_body'] = $email_body;		
		
	
	}
	
	public function get_email_template($key)
	{
		return $this->notifications_email[$key];
	
	}
	
	public function set_font_awesome()
	{
		        /* Store icons in array */
        $this->fontawesome = array(
                'cloud-download','cloud-upload','lightbulb','exchange','bell-alt','file-alt','beer','coffee','food','fighter-jet',
                'user-md','stethoscope','suitcase','building','hospital','ambulance','medkit','h-sign','plus-sign-alt','spinner',
                'angle-left','angle-right','angle-up','angle-down','double-angle-left','double-angle-right','double-angle-up','double-angle-down','circle-blank','circle',
                'desktop','laptop','tablet','mobile-phone','quote-left','quote-right','reply','github-alt','folder-close-alt','folder-open-alt',
                'adjust','asterisk','ban-circle','bar-chart','barcode','beaker','beer','bell','bolt','book','bookmark','bookmark-empty','briefcase','bullhorn',
                'calendar','camera','camera-retro','certificate','check','check-empty','cloud','cog','cogs','comment','comment-alt','comments','comments-alt',
                'credit-card','dashboard','download','download-alt','edit','envelope','envelope-alt','exclamation-sign','external-link','eye-close','eye-open',
                'facetime-video','film','filter','fire','flag','folder-close','folder-open','gift','glass','globe','group','hdd','headphones','heart','heart-empty',
                'home','inbox','info-sign','key','leaf','legal','lemon','lock','unlock','magic','magnet','map-marker','minus','minus-sign','money','move','music',
                'off','ok','ok-circle','ok-sign','pencil','picture','plane','plus','plus-sign','print','pushpin','qrcode','question-sign','random','refresh','remove',
                'remove-circle','remove-sign','reorder','resize-horizontal','resize-vertical','retweet','road','rss','screenshot','search','share','share-alt',
                'shopping-cart','signal','signin','signout','sitemap','sort','sort-down','sort-up','spinner','star','star-empty','star-half','tag','tags','tasks',
                'thumbs-down','thumbs-up','time','tint','trash','trophy','truck','umbrella','upload','upload-alt','user','volume-off','volume-down','volume-up',
                'warning-sign','wrench','zoom-in','zoom-out','file','cut','copy','paste','save','undo','repeat','text-height','text-width','align-left','align-right',
                'align-center','align-justify','indent-left','indent-right','font','bold','italic','strikethrough','underline','link','paper-clip','columns',
                'table','th-large','th','th-list','list','list-ol','list-ul','list-alt','arrow-down','arrow-left','arrow-right','arrow-up','caret-down',
                'caret-left','caret-right','caret-up','chevron-down','chevron-left','chevron-right','chevron-up','circle-arrow-down','circle-arrow-left',
                'circle-arrow-right','circle-arrow-up','hand-down','hand-left','hand-right','hand-up','play-circle','play','pause','stop','step-backward',
                'fast-backward','backward','forward','step-forward','fast-forward','eject','fullscreen','resize-full','resize-small','phone','phone-sign',
                'facebook','facebook-sign','twitter','twitter-sign','github','github-sign','linkedin','linkedin-sign','pinterest','pinterest-sign',
                'google-plus','google-plus-sign','sign-blank'
        );
        asort($this->fontawesome);
		
	
	
	}
	
		
	
	/*This Function Change the Profile Fields Order when drag/drop */
	
	public function sort_fileds_list() 
	{
		global $wpdb;
	
		$order = explode(',', $_POST['order']);
		$counter = 0;
		$new_pos = 10;
		
		$fields = get_option('usersultra_profile_fields');
		$new_fields = array();
		
		$fields_temp = $fields;
		ksort($fields);
		
		foreach ($fields as $field) 
		{
			
			$fields_temp[$order[$counter]]["position"] = $new_pos;
			
			$new_fields[$new_pos] = $fields_temp[$order[$counter]];				
			$counter++;
			$new_pos=$new_pos+10;
		}
		
		ksort($new_fields);		
		//print_r($new_fields);
		
		update_option('usersultra_profile_fields', $new_fields);
		
		die(1);
		
    }
	/*  delete profile field */
    public function delete_profile_field() 
	{						
		
		if($_POST['_item']!= "")
		{
			$fields = get_option('usersultra_profile_fields');
			
			$pos = $_POST['_item'];
			
			unset($fields[$pos]);
			
			ksort($fields);
			print_r($fields);
			update_option('usersultra_profile_fields', $fields);
			
		
		}
	
	}
	
	
	 /* create new form */
    public function add_new_custom_profile_field() 
	{		
		
		
		
		if($_POST['_meta']!= "")
		{
			$meta = $_POST['_meta'];
		
		}else{
			
			$meta = $_POST['_meta_custom'];
		}
		
		//if custom fields
		
		$fields = get_option('usersultra_profile_fields');
		
		$min = min(array_keys($fields)); 
		
		$pos = $min-1;
		
		$fields[$pos] =array(
			  'position' => $pos,
				'icon' => 0,
				'type' => filter_var($_POST['_type']),
				'field' => filter_var($_POST['_field']),
				'meta' => filter_var($meta),
				'name' => filter_var($_POST['_name']),
				'tooltip' => filter_var($_POST['_tooltip']),
				'social' =>  filter_var($_POST['_social']),
				'can_edit' => filter_var($_POST['_can_edit']),
				'allow_html' => filter_var($_POST['_allow_html']),
				'can_hide' => filter_var($_POST['_can_hide']),				
				'private' => filter_var($_POST['_private']),
				'required' => filter_var($_POST['_required']),
				'show_in_register' => filter_var($_POST['_show_in_register']),
				'predefined_options' => filter_var($_POST['_predefined_options']),
				
				'choices' => filter_var($_POST['_choices']),	
											
				'deleted' => 0
			);
			
			ksort($fields);
			//print_r($fields);
			
		   update_option('usersultra_profile_fields', $fields);         


    }
	


    // save form
    public function save_fields_settings() 
	{		
		
		$pos = filter_var($_POST['pos']);
		
		if($_POST['_meta']!= "")
		{
			$meta = $_POST['_meta'];
		
		}else{
			
			$meta = $_POST['_meta_custom'];
		}
		
		//if custom fields
		
		$fields = get_option('usersultra_profile_fields');
		
		$fields[$pos] =array(
			  'position' => $pos,
				'icon' => $_POST['_icon'],
				'type' => filter_var($_POST['_type']),
				'field' => filter_var($_POST['_field']),
				'meta' => filter_var($meta),
				'name' => filter_var($_POST['_name']),
				'tooltip' => filter_var($_POST['_tooltip']),
				'social' =>  filter_var($_POST['_social']),
				'can_edit' => filter_var($_POST['_can_edit']),
				'allow_html' => filter_var($_POST['_allow_html']),
				'can_hide' => filter_var($_POST['_can_hide']),				
				'private' => filter_var($_POST['_private']),
				'required' => filter_var($_POST['_required']),
				'show_in_register' => filter_var($_POST['_show_in_register']),
				'predefined_options' => filter_var($_POST['_predefined_options']),				
				'choices' => filter_var($_POST['_choices']),	
											
				'deleted' => 0
			);
			
			print_r($fields);
			
		    update_option('usersultra_profile_fields', $fields);
		
         


    }
		
	// update settings
    function update_settings() 
	{
		foreach($_POST as $key => $value) 
		{
            if ($key != 'submit')
			{
				if (strpos($key, 'html_') !== false)
                {
                      //$this->userultra_default_options[$key] = stripslashes($value);
                }else{
					
					 // $this->userultra_default_options[$key] = esc_attr($value);
                    }  
					
					$this->userultra_set_option($key, $value) ;
					//special setting for page
					if($key=="xoousersultra_my_account_page")
					{
						//echo "Page : " . $value;
						 update_option('xoousersultra_my_account_page',$value);
					}     

            }
        }
		
		//get checks for each tab
		
		
		 if ( isset ( $_GET['tab'] ) )
		 {
			 
			    $current = $_GET['tab'];
				
          } else {
                $current = $this->default_tab;
          }	 
            
		$special_with_check = $this->get_special_checks($current);
         
        foreach($special_with_check as $key)
        {
           
            
                if(!isset($_POST[$key]))
				{			
                    $value= '0';
					
				 } else {
					 
					  $value= $_POST[$key];
				}	 	
         
			
			$this->userultra_set_option($key, $value) ;  
            
        }
         
      $this->options = get_option('userultra_options');

        echo '<div class="updated"><p><strong>'.__('Settings saved.','xoousers').'</strong></p></div>';
    }
	
	public function get_special_checks($tab) 
	{
		$special_with_check = array();
		
		if($tab=="settings")
		{
	
		
		 $special_with_check = array('hide_admin_bar', 'uultra_allow_guest_rating', 'private_message_system','redirect_backend_profile','redirect_backend_registration','redirect_backend_login', 'social_media_fb_active', 'social_media_linked_active', 'social_media_yahoo', 'social_media_google', 'twitter_connect', 'yammer_connect', 'twitter_autopost', 'mailchimp_active', 'media_allow_photo_uploading', 'membership_display_selected_only');
		 
		}elseif($tab=="gateway"){
			
			 $special_with_check = array('gateway_paypal_active');
		 
		}
	
	return  $special_with_check ;
	
	}
	
	function admin_page_membership() 
	{
		//handle actions
		
		
		if (isset($_POST['update_settings'])) {
            $this->update_settings();
        }
		

		if (isset($_POST['reset-options'])) {
			$this->reset();
		}
		
		
		
		if (isset($_POST['woosync'])) {
			$this->woo_sync();
		}
		
		if (isset($_POST['woosync_del'])){
			$this->woo_sync_del();
		}
		
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
        
           
           
           <h2>XOO USERS </h2>
           
           <div id="icon-users" class="icon32"></div>
			
		            
            
            <h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>
            
            
			<div class="<?php echo $this->slug; ?>-admin-contain">
            
           
				
				<?php $this->include_tab_content(); ?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }
	
	
	function include_tab_content() {
		$screen = get_current_screen();
		
		if( strstr($screen->id, $this->slug ) ) 
		{
			if ( isset ( $_GET['tab'] ) ) 
			{
				$tab = $_GET['tab'];
			} else {
				$tab = $this->default_tab;
			}
			
			require_once (xoousers_path.'admin/tabs/'.$tab.'.php');
			
			
			
		}
	}
	
	function create_uploader_folder ()
	{
		global $xoouserultra;
		
		$mediafolder = true;
		
		//get folder
		$media_folder = $xoouserultra->get_option('media_uploading_folder');
		$path_pics = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
		
		//wp-content path
		
		$wp_content_path  = ABSPATH."wp-content/";
		echo $wp_content_path;
		
		if(!is_dir($path_pics)) 
		{
			
			$f_perm = substr(decoct(fileperms($wp_content_path)),2,4);
			
			if($f_perm == 777)
			{
				$this->CreateDir($path_pics);				
				chmod($wp_content_path, 0755);
								
			
			}else{
				
				chmod($wp_content_path, 0777);
				$this->CreateDir($path_pics);				
				chmod($wp_content_path, 0755);
			
			
			}
			
					
			
			echo "create: ". $path_pics;							   
		}
		
	}
	
	public function CreateDir($root)
	{

               if (is_dir($root))        {

                        $ret = "0";
                }else{

                        $oldumask = umask(0);
                        $valrRet = mkdir($root,0755);
                        umask($oldumask);


                        $re = "1";
                }

    }
	
	function checkUploadFolder()
	{
		global $xoouserultra;
		
		$mediafolder = true;
		
		//get folder
		$media_folder = $xoouserultra->get_option('media_uploading_folder');
		$path_pics = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
		
		
		$html = '';
		
		if(!is_dir($path_pics))
		{
			$mediafolder=false;
			
			$html .= ' <div ><div class="user-ultra-warning">'.__("Please create '".$media_folder."' folder with 0755 attribute. You can create it automatically by <a href='#' id='uultradmin-create-upload-folder'>clicking here</a>", 'xoousers').'</div></div>';
			
		}else{
		
			$f_perm = substr(decoct(fileperms($path_pics)),2,4);
			
			if($f_perm !=755)
			{
				//$mediafolder=false;
				
				//$html .= ' <div ><div class="user-ultra-warning">'.__("Change attributes TO 0755 for  '".$media_folder."' folder.", 'xoousers').'</div></div>';	
			
			}
			
		}

		
		return $html;
	
	
	}


	
	


	function admin_page() 
	{
		//handle actions
		
		
		
		if (isset($_POST['update_settings'])) {
            $this->update_settings();
        }
		

		if (isset($_POST['reset-options'])) {
			$this->reset();
		}
		
		
		
		if (isset($_POST['woosync'])) {
			$this->woo_sync();
		}
		
		if (isset($_POST['woosync_del'])){
			$this->woo_sync_del();
		}
		
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
        
           
           
           <h2>USERS ULTRA LITE</h2>
           
            <h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>
            
           
			<div class="<?php echo $this->slug; ?>-admin-contain">
            
           
            
           
            
            <?php echo $this->checkUploadFolder(); ?>
				
				<?php $this->include_tab_content(); ?>
                
                  
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }

}

$key = "xoouseradmin";
$this->{$key} = new XooUserAdmin();