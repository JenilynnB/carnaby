<?php
class XooUserUser {
	
	var $messages_process;
	
	var $profile_order_field;
	
	var $profile_role;	
	var $profile_order;	
	var $uultra_args;

	function __construct() 
	{
		$this->set_default_user_panel();			    
		
		if (isset($_POST['xoouserultra-profile-edition-form'])) 
		{
			
			/* This prepares the array taking values from the POST */
			$this->prepare( $_POST );
       			
			/* We validate everthying before updateing the profile */
			$this->handle();
			
			/* Let's Update the Profile */
			$this->update_me();
				
		}
		
		if (isset($_POST['uultra-form-cvs-form-conf'])) 
		{
			/* Let's Update the Profile */
			$this->process_cvs($_FILES);
				
		}
		
		if (isset($_POST['uultra-conf-close-account-post'])) 
		{
			/* Let's Close this Account */
			add_action('init', array( $this, 'close_user_account' ));
				
		}
		
		add_action( 'wp_ajax_refresh_avatar', array( $this, 'refresh_avatar' ));
		add_action( 'wp_ajax_delete_user_avatar', array( $this, 'delete_user_avatar' ));	

		add_action( 'wp_ajax_nopriv_send_reset_link', array( $this, 'send_reset_link' ));
		
		add_action( 'wp_ajax_nopriv_confirm_reset_password', array( $this, 'confirm_reset_password' ));
     	add_action( 'wp_ajax_confirm_reset_password', array( $this, 'confirm_reset_password' ));
		add_action( 'wp_ajax_confirm_reset_password_user', array( $this, 'confirm_reset_password_user' ));
		add_action( 'wp_ajax_confirm_update_email_user', array( $this, 'confirm_update_email_user' ));	
		

		add_action( 'wp_ajax_get_pending_moderation_list', array( $this, 'get_pending_moderation_list' ));
		add_action( 'wp_ajax_user_approve_pending_account', array( $this, 'user_approve_pending_account' ));
		add_action( 'wp_ajax_user_resend_activation_link', array( $this, 'user_resend_activation_link' ));
		
		add_action( 'wp_ajax_user_delete_account', array( $this, 'user_delete_account' ));
		
		add_action( 'wp_ajax_get_pending_activation_list', array( $this, 'get_pending_activation_list' ));
		add_action( 'wp_ajax_get_pending_payment_list', array( $this, 'get_pending_payment_list' ));
		
		
		add_action( 'wp_ajax_sync_users', array( $this, 'sync_users' ));
		
		
		$this->method_dect = array(
            'text' => 'text_box',
            'fileupload' => '',
            'textarea' => 'text_box',
            'select' => 'drop_down',
            'radio' => 'drop_down',
            'checkbox' => 'drop_down',
            'password' => '',
            'datetime' => 'text_box'
        );
		
		
		
			

	}
	
	public function close_user_account()
	{
		global $wpdb,  $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH. 'wp-admin/includes/user.php' );
		
		//close
		$current_user = wp_get_current_user();
		wp_delete_user( $current_user->ID );		
		wp_clear_auth_cookie();		
		
	
	}
	
	public function set_default_user_panel()
	{
		require_once( ABSPATH . "wp-includes/pluggable.php" );
		
		$tabs = array();
		
		$tabs[1] =array(
			  'position' => 1,
			  'icon' => 0,			
			  'title' => __('Basic Information', 'xoousers'),											
			  'visible' => 1
		);
		
		$tabs[2] =array(
			  'position' => 2,
			  'icon' => 0,			
			  'title' => __('My Cyrcle', 'xoousers'),											
			  'visible' => 1
		);
		
		$tabs[3] =array(
			  'position' => 3,
			  'icon' => 0,			
			  'title' => __('My Photos', 'xoousers'),											
			  'visible' => 1
		);
		
		$tabs[4] =array(
			  'position' => 4,
			  'icon' => 0,			
			  'title' => __('My Galleries', 'xoousers'),											
			  'visible' => 1
		);
		
		$tabs[5] =array(
			  'position' => 5,
			  'icon' => 0,			
			  'title' => __('My Latest Posts', 'xoousers'),											
			  'visible' => 1
		);
			
		ksort($tabs);	
			
		if (!get_option('userultra_default_user_tabs')) 
		{
			
			update_option('userultra_default_user_tabs', $tabs );
		}
		
		
	
	}
	
	public function show_protected_content($atts, $content)
	{
		global  $xoouserultra;
		
		
		extract( shortcode_atts( array(	
			
			'display_rule' => 'logged_in_based', //logged_in_based, membership_based			
			'membership_id' => '', // the ID of the membership package separated by commas
			'custom_message_loggedin' =>'', // custom message
			'custom_message_membership' =>'', // custom message
						
			
		), $atts ) );
		
		$package_list = array();
		 
		 if($custom_message_loggedin == "")
		 {
				$custom_message_loggedin =  __('Content visible only for registered users. ','xoousers');
					
		 }
		 
		 if($membership_id != "")
		 {
			 $package_list  = explode(',', $membership_id);					
		 }
		 
		
			
		
		if($display_rule == "logged_in_based")
		{
			//logged in based			
			if (!is_user_logged_in()) 
			{
				return  '<div class="uupublic-ultra-info">'.$custom_message_loggedin.'</div>';
				
			} else {
				
				//the users is logged in then display content
				return do_shortcode($content);				
				
			}	
		
		}elseif($display_rule == "membership_based"){
			
			
			//check logged in		
			if (!is_user_logged_in()) 
			{
				return  '<div class="uupublic-ultra-info">'.$custom_message_membership.'</div>';
				
			} else {
				
				//the user is logged in
				$user_id = get_current_user_id();					
				$package = $this->get_user_package($user_id);	
				
				if ( in_array($package , $package_list) )
				{
					return do_shortcode($content);
					
				}else{
					
					return  '<div class="uupublic-ultra-info">'.$custom_message_membership.'</div>';
					
				}
				
				//the users is logged in then display content
								
				
			}
			
			
			
		
		}
		
		
		
		
	
	}
	
	public function get_user_package($user_id)
	{		
		global $wpdb,  $xoouserultra;
		
		return get_user_meta($user_id, 'usersultra_user_package_id', true);	
	
	}
	
	
		/*Process uploads*/
	function process_cvs($array) 
	{
		global $wpdb,  $xoouserultra;
		
		/* File upload conditions */
		$this->allowed_extensions = array("csv");
		
		
		$send_welcome_email = false;
		
		if(isset($_POST["uultra-send-welcome-email"] ) && $_POST["uultra-send-welcome-email"]==1)
		{
			$send_welcome_email = true;
		
		}
		
		$account_status = "";
		
		if(isset($_POST["uultra-activate-account"] ) )
		{
			$account_status = $_POST["uultra-activate-account"];		
		}
		
		
							
		if (isset($_FILES))
		{
			foreach ($_FILES as $key => $array) {
				
								
				extract($array);
				
				$file = $_FILES[$key];
				
				$info = pathinfo($file['name']);
				$real_name = $file['name'];
				$ext = $info['extension'];
				$ext=strtolower($ext);
		
				
				if ($name) {
				    
					
					if ( !in_array($ext, $this->allowed_extensions) )
					{
						$this->messages_process .= __('The file format is not allowed!','xoousers');											
					
					} else {
					
						/*Upload file*/
									
						$path_f = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
						
						$target_path = $path_f.'/import/';
						// Checking for upload directory, if not exists then new created. 
						if(!is_dir($target_path))
						    mkdir($target_path, 0755);
						
						$target_path = $target_path . time() . '_'. basename( $name );						
						move_uploaded_file( $tmp_name, $target_path);
						
										
						//now that the files is up we have to start the uploading
						
						$row = 1;
						if (($handle = fopen($target_path, "r")) !== FALSE) 
						{
							while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
							{
								$num = count($data);
								
								$this->create_user_import ($data, $send_welcome_email, $account_status, $row);
								
								$row++;
															
								
							}
							
							fclose($handle);
							
							$this->messages_process .= __('--- Finished ---  ', 'xoousers');
						}

						
					}
				}
			}
		}
		
	}
	
	public function create_user_import ($user, $send_welcome_email, $account_status, $count)
	{
		global $wpdb,  $xoouserultra;
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$user_name = $user[0];
		$email = $user[1];
		$display_name = $user[2];
		$reg_date = $user[3];
		
		//metadata		
		$f_name = $user[4];
		$l_name = $user[5];
		$age = $user[6];
		$country = $user[7];
				
		$user_pass = wp_generate_password( 12, false);
		
		/* Create account, update user meta */
		$sanitized_user_login = sanitize_user($user_name);
		
		if(!email_exists($email))
		{			
			
			/* We create the New user */
			$user_id = wp_create_user( $sanitized_user_login, $user_pass, $email);
			
			if ( ! $user_id ) 
			{
	
			}else{
				
				//set account status					
				$xoouserultra->login->user_account_status($user_id);
						
				$verify_key = $xoouserultra->login->get_unique_verify_account_id();	
								
				update_user_meta ($user_id, 'display_name', $display_name);
				update_user_meta ($user_id, 'first_name', $f_name);
				update_user_meta ($user_id, 'last_name', $l_name);
				update_user_meta ($user_id, 'age', $age);
				update_user_meta ($user_id, 'country', $country);
				update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);
				
				$this->messages_process .= $count." - User name: ". $user_name . " Email: ". $email."<br>";
				
							
				if($send_welcome_email)
				{
					//status
					
					if($account_status=="active")
					{
						
						update_user_meta ($user_id, 'usersultra_account_status','active');
						
						//automatic activation
						$xoouserultra->messaging->welcome_email($email, $f_name, $user_pass);
						
					}
					
					if($account_status=="pending")
					{
						
						update_user_meta ($user_id, 'usersultra_account_status','pending');
						
						 //email activation link		  
			  
						  $web_url =$xoouserultra->login->get_my_account_direct_link();			  
						  $pos = strpos("page_id", $web_url);		  
						  $unique_key = get_user_meta($user_id, 'xoouser_ultra_very_key', true);
						  
						  //patch when not set account
						  
						  
						  
						  if ($pos === false) // this is a tweak that applies when not Friendly URL is set.
						  {
								//
								$activation_link = $web_url."&act_link=".$unique_key;
									
						  } else {
									 
							   // found then we're using seo links					 
							   $activation_link = $web_url."?act_link=".$unique_key;
									
						  }
						  
						  //send link to user
						  $xoouserultra->messaging->welcome_email_with_activation($email, $f_name, $user_pass, $activation_link);

					}
					
				}
						
			}
		
		}else{
			  //email exists
		
		}
		
	}
	
	
	
	public function get_user_meta ($meta)
	{
		$user_id = get_current_user_id();		
		return get_user_meta( $user_id, $meta, true);
		
	}
	
		public function sync_users ()
	{
		global $wpdb,  $xoouserultra;
		
		$sql = 'SELECT ID,display_name FROM ' . $wpdb->prefix . 'users  ' ;
		$users = $wpdb->get_results($sql );
		
		$count = 0;
		
		if (!empty($users))
		{
			
			foreach($users as $user) 
			{
				$count++;
				$user_id = $user->ID;
				update_user_meta ($user_id, 'usersultra_account_status', 'active');
				update_user_meta ($user_id, 'display_name', $user->display_name);
				
				
			}
					
		
		}
		
		echo "<div class='user-ultra-success'>".__(" SUCESS! The sync process has been finished. ".$count." users were updated ", 'xoousers')."</div>";
		
		die();
	}
	
	/*Get Stats*/
	public function get_amount_period ($month, $day, $year)
	{
		global $wpdb,  $xoouserultra;
		
		$sql = 'SELECT count(*) as total, user_registered, ID FROM ' . $wpdb->prefix . 'users  WHERE ID <> 0  ' ;
		
		if($day!=""){$sql .= " AND DAY(user_registered) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(user_registered) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(user_registered) = '$year'";}	
		
		$users = $wpdb->get_results($sql );
		
		//echo $sql;
		
		$res_total = $xoouserultra->commmonmethods->fetch_result($users);
		
		if($res_total->total=="")
		{
			return 0;
			
		}else{
			
			return $res_total->total;
			
		}
		
	
	
	}
	
	/*Get Pending Payment*/
	public function get_pending_payment_list ($howmany)
	{
		
		global $wpdb,  $xoouserultra;
		
		$pic_boder_type = "";
		 $pic_size_type="";		
		
		$users = $this->get_pending_payment($howmany);
		
		$html = '<h3>'.__('Pending Payment','xoousers').'</h3>';
		
		$html .= '<div id="uultra-user-acti-noti"></div>';
		
		if (!empty($users))
		{
		
			$html .= '<table class="wp-list-table widefat fixed posts table-generic">
				<thead>
					<tr>
						<th style="width:10%;">'.__('Avatar', 'xoousers').'</th>
						<th style="width:15%;">'.__('Username', 'xoousers').'</th>
						
						<th >'.__('Email', 'xoousers').'</th>
						<th>'.__('Registered', 'xoousers').'</th>
						<th>'.__('Action', 'xoousers').'</th>
					</tr>
				</thead>
				
				<tbody>';
				
			
				foreach($users as $user) 
				{
					
					$user_id = $user->ID;				
				  
					$html .=' <tr>
						<td>'.$this->get_user_pic( $user_id, 30, 'avatar', $pic_boder_type, $pic_size_type).'</td>
						<td>'.$user->user_login.'</td>
						
						<td>'. $user->user_email.'</td>
						 <td>'.$user->user_registered.'</td>
					   <td> 
					   <a href="#" class="button uultradmin-user-deny" user-id="'.$user_id.'">'.__('Deny','xoousers').'					   </a> <a href="#" class="button-primary uultradmin-user-approve" user-id="'.$user_id.'">'.__('Confirm','xoousers').'
					   </a></td></tr>';
					
					
					
				}
				
				$html .= '</tbody>
        </table>';
						
			
			}else{
			
			$html .='<p>'.__('There are no pending payment users.','xoousers').'</p>';
				
			
			} 
			
		
		echo $html;
		die();	
		
		
	}
	
	/*Get Pending*/
	public function get_pending_moderation_list ($howmany)
	{
		
		global $wpdb,  $xoouserultra;
		
		$pic_boder_type = "";
		$pic_size_type = "";
		
		
		$users = $this->get_pending_moderation($howmany);
		
		$html = '<h3>'.__('Pending Moderation','xoousers').'</h3>';
		
		$html .= '<div id="uultra-user-acti-noti"></div>';
		
		if (!empty($users))
		{
		
			$html .= '<table class="wp-list-table widefat fixed posts table-generic">
				<thead>
					<tr>
						<th style="width:10%;">'.__('Avatar', 'xoousers').'</th>
						<th style="width:15%;">'.__('Username', 'xoousers').'</th>
						
						<th >'.__('Email', 'xoousers').'</th>
						<th>'.__('Registered', 'xoousers').'</th>
						<th>'.__('Action', 'xoousers').'</th>
					</tr>
				</thead>
				
				<tbody>';
				
			
				foreach($users as $user) 
				{
					
					$user_id = $user->ID;				
				  
					$html .=' <tr>
						<td>'.$this->get_user_pic( $user_id, 30, 'avatar', $pic_boder_type, $pic_size_type).'</td>
						<td>'.$user->user_login.'</td>
						
						<td>'. $user->user_email.'</td>
						 <td>'.$user->user_registered.'</td>
					   <td> 
					   <a href="#" class="button uultradmin-user-deny" user-id="'.$user_id.'">'.__('Deny','xoousers').'					   </a> <a href="#" class="button-primary uultradmin-user-approve" user-id="'.$user_id.'">'.__('Confirm','xoousers').'
					   </a></td></tr>';
					
					
					
				}
				
				$html .= '</tbody>
        </table>';
						
			
			}else{
			
			$html .='<p>'.__('There are no pending moderation users.','xoousers').'</p>';
				
			
			} 
			
		
		echo $html;
		die();	
		
		
	}
	
	/*Get Pending*/
	public function get_pending_activation_list ($howmany)
	{
		
		global $wpdb,  $xoouserultra;
		
		
		$users = $this->get_pending_activation($howmany);
		
		$html = '<h3>'.__('Pending Confirmation','xoousers').'</h3>';
		
		$html .= '<div id="uultra-user-acti-pending-noti"></div>';
		
		if (!empty($users))
		{
		
			$html .= '<table class="wp-list-table widefat fixed posts table-generic">
				<thead>
					<tr>
						<th style="width:10%;">'.__('Avatar', 'xoousers').'</th>
						<th style="width:15%;">'.__('Username', 'xoousers').'</th>
						
						<th >'.__('Email', 'xoousers').'</th>
						<th>'.__('Registered', 'xoousers').'</th>
						<th>'.__('Action', 'xoousers').'</th>
					</tr>
				</thead>
				
				<tbody>';
				
			
				foreach($users as $user) 
				{
					
					$user_id = $user->ID;				
				  
					$html .=' <tr>
						<td>'.$this->get_user_pic( $user_id, 30, 'avatar', $pic_boder_type, $pic_size_type).'</td>
						<td>'.$user->user_login.'</td>
						
						<td>'. $user->user_email.'</td>
						 <td>'.$user->user_registered.'</td>
					   <td> 
					   <a href="#" class="button uultradmin-user-deny" user-id="'.$user_id.'">'.__('Delete','xoousers').'					   </a> <a href="#" class="button-primary uultradmin-user-resend-link" user-id="'.$user_id.'">'.__('Send Link','xoousers').'
					   </a><a href="#" class="button-primary uultradmin-user-approve-2" user-id="'.$user_id.'">'.__('Confirm','xoousers').'
					   </a></td></tr>';
					
					
				}
				
				$html .= '</tbody>
        </table>';
						
			
			}else{
			
			$html .='<p>'.__('There are no pending confirmation users.','xoousers').'</p>';
				
			
			} 
			
		
		echo $html;
		die();	
		
		
	}
	
		/*Send Activation Link Account*/
	public function user_send_activation_link ()
	{
		global $wpdb,  $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$user_id = $_POST["user_id"];
		
		update_user_meta ($user_id, 'usersultra_account_status', 'active');
		
		$user = get_user_by( 'id', $user_id );
		
	
		
		$u_email=$user->user_email;
		$user_login= $user->user_login;
		
		//noti user		
		$xoouserultra->messaging->confirm_activation($u_email, $user_login);
		
		echo "<div class='user-ultra-success uultra-notification'>".__("User has been activated", 'xoousers')."</div>";
		
		die();
	
	
	}
	
	/*Resend link Account*/
	public function user_resend_activation_link ()
	{
		global $wpdb,  $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$user_id = $_POST["user_id"];
		
		$user = get_user_by( 'id', $user_id );
		$u_email=$user->user_email;
                $user_first_name=$user->first_name;
		$user_login= $user->user_login;
		
		//noti user		
		$xoouserultra->login->user_resend_activation_link($user_id, $u_email, $user_first_name);
		
		echo "<div class='user-ultra-success uultra-notification'>".__("Activation link sent", 'xoousers')."</div>";
		
		die();
	
	
	}
	
	
	/*Activate Account*/
	public function user_approve_pending_account ()
	{
		global $wpdb,  $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$user_id = $_POST["user_id"];
		
		update_user_meta ($user_id, 'usersultra_account_status', 'active');
		
		$user = get_user_by( 'id', $user_id );
		
	
		
		$u_email=$user->user_email;
		$user_login= $user->user_login;
		
		//noti user		
		$xoouserultra->messaging->confirm_activation($u_email, $user_login);
		
		echo "<div class='user-ultra-success uultra-notification'>".__("User has been activated", 'xoousers')."</div>";
		
		die();
	
	
	}
	
	/*Activate Account*/
	public function user_delete_account ()
	{
		global $wpdb,  $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$user_id = $_POST["user_id"];
		
		update_user_meta ($user_id, 'usersultra_account_status', 'deleted');
		
		$user = get_user_by( 'id', $user_id );
		
		$u_email=$user->user_email;
		$user_login= $user->user_login;
		
		//noti user		
		$xoouserultra->messaging->deny_activation($u_email, $user_login);
		
		echo "<div class='user-ultra-success uultra-notification'>".__("User has been deleted", 'xoousers')."</div>";
		
		die();
	
	
	}
	
		/*Get Pending Payment*/
	public function get_pending_payment ($howmany)
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'usersultra_account_status',                    
			'meta_value' => 'pending_payment',                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		 
		// Get the results//
		$users = $user_query->get_results();		
		return $users;
		
	
	}
	
	/*Get Pending*/
	public function get_pending_moderation ($howmany)
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'usersultra_account_status',                    //(string) - Custom field key.
			'meta_value' => 'pending_admin',                  //(string|array) - Custom field value.
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		 
		// Get the results//
		$users = $user_query->get_results();		
		return $users;
		
	
	}
	
	/*Get Pending Activation*/
	public function get_pending_activation ($howmany)
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'usersultra_account_status',                   
			'meta_value' => 'pending',                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		
		$user_query = new WP_User_Query( $args );		 
     	$users = $user_query->get_results();		
		return $users;
		
	
	}
	
	
	
	/*Get Pending Activation Count*/
	public function get_pending_activation_count ()
	{
		global $wpdb,  $xoouserultra;
		
		$total = 0;
		
		$args = array( 	
						
			'meta_key' => 'usersultra_account_status',                   
			'meta_value' => 'pending_admin',                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		
		$user_query = new WP_User_Query( $args );		 
     	$total = $user_query->get_total() ;		
		return $total;
		
	
	}
	
	
	
	
	/* This is the */
	public function signup_status( $method )
	{
		$args = array( 	
						
			'meta_key' => 'xoouser_ultra_social_signup',                    //(string) - Custom field key.
			'meta_value' => $method,                  //(string|array) - Custom field value.
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		 
		// Get the results//
		//$users = $user_query->get_results();
		
		$total = $user_query->get_total();
		return $total;
		// Output results
	
	
	}
	
	function validate_valid_email ($myString)
	{
		$ret = true;
		if (!filter_var($myString, FILTER_VALIDATE_EMAIL)) {
    		// invalid e-mail address
			$ret = false;
		}
					
		return $ret;
	
	
	}
	
	public function confirm_update_email_user()
	{
		global $wpdb,  $xoouserultra, $wp_rewrite;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/general-template.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$wp_rewrite = new WP_Rewrite();
		
		$user_id = get_current_user_id();
	
	
		$email = $_POST['email'];
		$html = '';
		$validation = '';
		
	
		//validate if it's a valid email address	
		$ret_validate_email = $this->validate_valid_email($email);
		
		if($email=="")
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Please type your new email ", 'xoousers')."</div>";
			$html = $validation;			
		}
		
		if(!$ret_validate_email)
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Please type a valid email address ", 'xoousers')."</div>";
			$html = $validation;			
		}
		
		$current_user = get_userdata($user_id);
		//print_r($user);
		$current_user_email = $current_user->user_email;
		
		//check if already used
		
		$check_user = get_user_by('email',$email);
		$user_check_id = $check_user->ID;
		$user_check_email = $check_user->ID;
		
		if($validation=="" )
		{
		
			if($user_check_id==$user_id) //this is the same user then change email
			{
				$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! You haven't changed your email. ", 'xoousers')."</div>";
				$html = $validation;
				
			
			}else{ //email already used by another user
			
				if($user_check_email!="")
				{
			
					$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! The email is in use already ", 'xoousers')."</div>";
					$html = $validation;
				
				}else{
					
					//email available
					
				}
				
			
			}
		
		}
		
		
		
		if($validation=="" )
		{
		
			if($user_id >0 )
			{
					$user = get_userdata($user_id);
					$user_id = $user->ID;
					$user_email = $user->user_email;
					$user_login = $user->user_login;	
					
					$user_id = wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );											
										
					$html = "<div class='uupublic-ultra-success'>".__(" Success!! Your email account has been changed to : ".$email."  ", 'xoousers')."</div>";
					
																			
				}else{
					
									
				}
					
			}
		 echo $html;
		 die();
		
	
	}
	
	public function confirm_reset_password_user()
	{
		global $wpdb,  $xoouserultra, $wp_rewrite;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/general-template.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		require_once(ABSPATH . 'wp-includes/user.php');
		
		$wp_rewrite = new WP_Rewrite();
		
		$user_id = get_current_user_id();
		
				
		//check redir		
		$account_page_id = get_option('xoousersultra_my_account_page');
		$my_account_url = get_permalink($account_page_id);
		
		
		
		$PASSWORD_LENGHT =7;
		
		$password1 = $_POST['p1'];
		$password2 = $_POST['p2'];
		
		$html = '';
		$validation = '';
		
		//check password
		
		if($password1!=$password2)
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Password must be identical ", 'xoousers')."</div>";
			$html = $validation;			
		}
		
		if(strlen($password1)<$PASSWORD_LENGHT)
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Password should contain at least 7 alphanumeric characters ", 'xoousers')."</div>";
			$html = $validation;		
		}
		
		
		if($validation=="" )
		{
		
			if($user_id >0 )
			{
					//echo "user id: ". $user_id;
					$user = get_userdata($user_id);
					//print_r($user);
					$user_id = $user->ID;
					$user_email = $user->user_email;
					$user_login = $user->user_login;			
					
					wp_set_password( $password1, $user_id ) ;
					
					//notify user					
					$xoouserultra->messaging->send_new_password_to_user($user_email, $user_login, $password1);
					
					$html = "<div class='uupublic-ultra-success'>".__(" Success!! The new password has been sent to ".$user_email."  ", 'xoousers')."</div>";
					
					// Here is the magic:
					wp_cache_delete($user_id, 'users');
					wp_cache_delete($username, 'userlogins'); // This might be an issue for how you are doing it. Presumably you'd need to run this for the ORIGINAL user login name, not the new one.
					wp_logout();
					wp_signon(array('user_login' => $user_login, 'user_password' => $password1));
					
														
				}else{
					
									
				}
					
			}
		 echo $html;
		 die();
		
	
	}
	
	public function confirm_reset_password()
	{
		global $wpdb,  $xoouserultra, $wp_rewrite;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/general-template.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$wp_rewrite = new WP_Rewrite();
		
				
		//check redir		
		$account_page_id = get_option('xoousersultra_my_account_page');
		$my_account_url = get_permalink($account_page_id);
		
		
		
		$PASSWORD_LENGHT =7;
		
		$password1 = $_POST['p1'];
		$password2 = $_POST['p2'];
		$key = $_POST['key'];
		
		$html = '';
		$validation = '';
		
		//check password
		
		if($password1!=$password2)
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Password must be identical ", 'xoousers')."</div>";
			$html = $validation;			
		}
		
		if(strlen($password1)<$PASSWORD_LENGHT)
		{
			$validation .= "<div class='uupublic-ultra-error'>".__(" ERROR! Password should contain at least 7 alphanumeric characters ", 'xoousers')."</div>";
			$html = $validation;		
		}
		
		
		$user = $this->get_one_user_with_key($key);
		
		
		if($validation=="" )
		{
			
			if($user->ID >0 )
			{
				//print_r($user);
				$user_id = $user->ID;
				$user_email = $user->user_email;
				$user_login = $user->user_login;
				
				wp_set_password( $password1, $user_id ) ;
				
				//notify user
				
				$xoouserultra->messaging->send_new_password_to_user($user_email, $user_login, $password1);
				
				$html = "<div class='uupublic-ultra-success'>".__(" Sucess!! The new password has been sent to ".$user_email."  ", 'xoousers')."</div>";
				
				$html .= "<div class=''>".__('<a href="'.$my_account_url.'" title="Login">CLICK HERE TO LOGIN</a>', 'xoousers')."</div>";
				
								
			}else{
				
				// we couldn't find the user			
				$html = "<div class='uupublic-ultra-error'>".__(" ERROR! Invalid reset link ", 'xoousers')."</div>";
			
			}
					
		}
		 echo $html;
		 die();
		
	
	}
	public function send_reset_link()
	{
		session_start();
		global $wpdb,  $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		$html = "";
		
		// Adding support for login by email
		if(is_email($_POST['user_login']))
		{
			  $user = get_user_by( 'email', $_POST['user_login'] );
			  
			 			  
			  // check if active					
			  $user_id =$user->ID;				
			 
			  if($user_id=="")
			  {
				  //user not found
				  $html = __('Email not found','xoousers');
			  
			  }else{
				  
				  //user found 				  
				   if(!$this->is_active($user_id) && !is_super_admin($user_id))
				   {
					   					   
					   //user is not active					   
					   $html = __('Your account is not active yet.','xoousers');				   
					   $noactive = true;
						  
				   }else{
				   
				   
				   
				   }
				 
			  }
		  
		  }else{
			  
			  // User is trying to login using username			  
			  $user = get_user_by('login',$_POST['user_login']);
			  
			  // check if active and it's not an admin		
			  $user_id =$user->ID;
			  
			  if($user_id=="")
			  {
				  //user not found
				  $html = __('User not found','xoousers');
			  
			  }else{
				  
				  //user found 
				  
				   if(!$this->is_active($user_id) && !is_super_admin($user_id))
				   {
					   					   
					   //user is not active					   
					   $html = __('Your account is not active yet.','xoousers');				   
					   $noactive = true;
						  
				   }else{
				   
				   
				   
				   }
				 
			  }	
			  
		  
		  }
		  
		  if($html=="" && isset($user))
		  {
			  //generate reset link
			  $unique_key =  $xoouserultra->login->get_unique_verify_account_id();
			  
			  //web url
			  $web_url = $xoouserultra->login->get_my_account_direct_link();
			  
			  $pos = strpos("page_id", $web_url);

			  
			  if ($pos === false) //not page_id found
			  {
				    //
					$reset_link = $web_url."?resskey=".$unique_key;
					
			  } else {
				     
					 // found then we're using seo links					 
					 $reset_link = $web_url."&resskey=".$unique_key;
					
			  }
			  
			  //update meta
			  update_user_meta ($user_id, 'xoouser_ultra_very_key', $unique_key);	
			  
			  //notify users			  
			  $xoouserultra->messaging->send_reset_link($user, $reset_link);			  
			  
			  //send reset link to user		  			  
			   $html = "<div class='uupublic-ultra-success'>".__(" A reset link has been sent to your email ", 'xoousers')."</div>";
			   
			  
			  
			 
		  }
		  
		 
		 echo $html;
		 die();
	}
	
	/* This is the */
	public function edit_profile_form( $sidebar_class=null, $redirect_to=null )
	{
		global  $xoouserultra;
		$html = null;
		
		$user_id = get_current_user_id();
		
		// Optimized condition and added strict conditions
		if (!isset($xoousers_register->registered) || $xoousers_register->registered != 1) 
		{
			
			
		$html .= '<div class="xoouserultra-clear"></div>';
				
		$html .= '<form action="" method="post" id="xoouserultra-profile-edition-form">';

		$array = get_option('usersultra_profile_fields');

		foreach($array as $key=>$field) 
		{
		    // Optimized condition and added strict conditions 
		    $exclude_array = array('user_pass', 'user_pass_confirm', 'user_email');
		    if(isset($field['meta']) && in_array($field['meta'], $exclude_array))
		    {
		        unset($array[$key]);
		    }
		}
		
		$i_array_end = end($array);
		
		if(isset($i_array_end['position']))
		{
		    $array_end = $i_array_end['position'];
		    if ($array[$array_end]['type'] == 'separator') {
		        unset($array[$array_end]);
		    }
		}
		
		
	
		foreach($array as $key => $field) 
		{

			extract($field);
			
			// WP 3.6 Fix
			if(!isset($deleted))
			    $deleted = 0;
			
			if(!isset($private))
			    $private = 0;
			
			if(!isset($required))
			    $required = 0;
			
			$required_class = '';
			if($required == 1 && in_array($field, $xoouserultra->include_for_validation))
			{
			    $required_class = ' required';
			}
			
			
			/* Fieldset seperator */
			if ( $type == 'separator' && $deleted == 0 && $private == 0 ) 
			{
				$html .= '<div class="xoouserultra-field xoouserultra-seperator xoouserultra-edit xoouserultra-edit-show">'.$name.'</div>';
			}
			
			
			if ( $type == 'usermeta' && $deleted == 0 && $private == 0)
			
			 {
				
				$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">';
				
				/* Show the label */
				if (isset($array[$key]['name']) && $name)
				 {
					$html .= '<label class="xoouserultra-field-type" for="'.$meta.'">';	
					
					if (isset($array[$key]['icon']) && $icon) {
                            $html .= '<i class="fa fa-' . $icon . '"></i>';
                    } else {
                            $html .= '<i class="fa fa-icon-none"></i>';
                    }
											
					$html .= '<span>'.$name.'</span></label>';
					
					
				} else {
					$html .= '<label class="xoouserultra-field-type">&nbsp;</label>';
				}
				
				$html .= '<div class="xoouserultra-field-value">';
					
					switch($field) {
					
						case 'textarea':
							$html .= '<textarea class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" title="'.$name.'">'.$this->get_user_meta( $meta).'</textarea>';
							break;
							
						case 'text':
							$html .= '<input type="text" class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_user_meta( $meta).'"  title="'.$name.'" />';
							break;
							
							
						case 'datetime':
						    $html .= '<input type="text" class="xoouserultra-input'.$required_class.' xoouserultra-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_user_meta( $meta).'"  title="'.$name.'" />';
						    break;
							
						case 'select':
						
							if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' ) 
							{
								$loop = $xoouserultra->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
							}elseif(isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
								
								$loop = explode(PHP_EOL, $choices);
							
							}
							
							if (isset($loop)) 
							{
								$html .= '<select class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" title="'.$name.'">';							
								
								
								foreach($loop as $sh) 
								{
									
									$option = trim($option);								    
								    $html .= '<option value="'.$sh.'" '.selected( $this->get_user_meta( $meta), $sh, 0 ).'>'.$sh.'</option>';
								
								}
								
								$html .= '</select>';
							}
							$html .= '<div class="xoouserultra-clear"></div>';
							
							break;
							
						case 'radio':
						
							if (isset($array[$key]['choices']))
							{
								$loop = explode(PHP_EOL, $choices);
							}
							
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as  $option) 
								{
								    if($counter >0)
								        $required_class = '';
								    
								    $option = trim($option);
									
									$html .= '<label class="xoouserultra-radio"><input type="radio" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'" value="'.$option.'" '.checked( $this->get_user_meta( $meta), $option, 0 );
									$html .= '/> <label for="'.$meta.'"><span></span>'.$option.'</label> </label>';
									
									$counter++;
									
								}
							}
							$html .= '<div class="xoouserultra-clear"></div>';
							break;
							
						case 'checkbox':
							if (isset($array[$key]['choices']))
							{
								
							   $loop = explode(PHP_EOL, $choices);
							}
							if (isset($loop) && $loop[0] != '') {
							  $counter =0;
								foreach($loop as $option) {
								   
								   if($counter >0)
								        $required_class = '';
								  
								  $option = trim($option);
									$html .= '<label class="xoouserultra-checkbox"><input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" value="'.$option.'" ';
									
									 $values = explode(', ', $this->get_user_meta($meta));
									
									if (in_array($option, $values)) {
									$html .= 'checked="checked"';
									}
									$html .= '/> <label for="'.$meta.'"><span></span> '.$option.'</label> </label>';
									
									$counter++;
								}
							}
							$html .= '<div class="xoouserultra-clear"></div>';
							break;
							
						
							
					}
					
					/*User can hide this from public*/
					if (isset($array[$key]['can_hide']) && $can_hide == 1) {
						
						//get meta
						$check_va = "";
						$ischecked = $this->get_user_meta("hide_".$meta);
						 
						 if($ischecked==1) $check_va = 'checked="checked"';
						
						$html .= '<div class="xoouserultra-hide-from-public">
										<input type="checkbox" name="hide_'.$meta.'" id="hide_'.$meta.'" value="1" '.$check_va.' /> <label for="checkbox1"><span></span>'.__('Hide from Public','xoousers').'</label>
									</div>';



					} elseif ($can_hide == 0 && $private == 0) {
					   
					}
					
				$html .= '</div>';
				$html .= '</div><div class="xoouserultra-clear"></div>';
			}
		}
		
		
		$html .= '<div class="xoouserultra-field xoouserultra-edit xoouserultra-edit-show">
						<label class="xoouserultra-field-type xoouserultra-field-type-'.$sidebar_class.'">&nbsp;</label>
						<div class="xoouserultra-field-value">
						    <input type="hidden" name="xoouserultra-profile-edition-form" value="xoouserultra-profile-edition-form" />
							<input type="submit" name="xoouserultra-update" id="xoouserultra-update" class="xoouserultra-button" value="'.__('Update','xoousers').'" />
						</div>
					</div><div class="xoouserultra-clear"></div>';
					
		
		$html .= '</form>';
		
		} // End of the Profile Edition Function
		
		return $html;
	}
	
	/*Update Profile*/
	function update_me() 
	{
		global  $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
	
		$user_id = get_current_user_id();
		
		 // empty checkboxes
        $array = get_option('usersultra_profile_fields');
		
		 // Get list of dattime fields
        $date_time_fields = array();

        foreach ($array as $key => $field) {
            extract($field);

            if (isset($array[$key]['field']) && $array[$key]['field'] == 'checkbox') 
			{
				//echo "is meta field: " .$meta;
                update_user_meta($user_id, $meta, null);
            }

            // Filter date/time custom fields
            if (isset($array[$key]['field']) && $array[$key]['field'] == 'datetime') {
                array_push($date_time_fields, $array[$key]['meta']);
            }
        }
		
			
			/* Check if the were errors before updating the profile */
			if (!isset($this->errors)) 
			{
				/* Now update all user meta */
				foreach($this->usermeta as $key => $value) 
				{
					// save checkboxes
                    if (is_array($value)) { // checkboxes
                        $value = implode(', ', $value);
                    }
					
					//echo $key. " ";
					update_user_meta($user_id, "hide_".$key, "");
					update_user_meta($user_id, $key, esc_attr($value));
						
				}
				
				//upate activity
				
								
			}
			
	}
	
	/*Post value*/
	function get_post_value($meta) 
	{
				
		if (isset($_POST['xoouserultra-register-form'])) {
			if (isset($_POST[$meta]) ) {
				return $_POST[$meta];
			}
		} else {
			if (strstr($meta, 'country')) {
			return 'United States';
			}
		}
	}
	
	
	/******************************************
	Get user by ID, username
	******************************************/
	function get_user_data_by_uri() 
	{
		
		global  $xoouserultra, $wpdb;
	
		require_once(ABSPATH . 'wp-includes/pluggable.php');	
		
		
		
		$u_nick = get_query_var('uu_username');
		
		if($u_nick=="") //permalink not activated
		{
			$u_nick=$this->parse_user_id_from_url();	
			
		}
		
		
		
		$nice_url_type = $xoouserultra->get_option('usersultra_permalink_type');
			
		
		if ($nice_url_type == 'ID' || $nice_url_type == '' ) 
		{
			
			$user = get_user_by('id',$u_nick);				
			
		}elseif ($nice_url_type == 'username') {
			
						
			$user = get_user_by('slug',$u_nick);
				
		}
			
		return $user;
	}
	
	public function get_display_name($user_id)
	{
		global  $xoouserultra;
		
		$display_name = "";
		
		$display_type = $xoouserultra->get_option('uprofile_setting_display_name');
		$display_type = 'display_name';
		
		$user = get_user_by('id',$user_id);
		
		if ($display_type == 'fr_la_name' || $display_type == '' ) 
		{
			$f_name = get_user_meta($user_id, 'first_name', true);
	        $l_name = get_user_meta($user_id, 'last_name', true);	
			
			$display_name = $f_name. " " .  $l_name;			
			
		}elseif ($display_type == 'username') {
				
			$display_name =$user->user_login;
		
		
		}elseif ($display_type == 'display_name') {
			
			$display_name = get_user_meta($user_id, 'display_name', true);	
			
			if($display_name=="")
			{
				$display_name =$user->display_name;
			
			}
				
			
				
		}
		
		
		return ucfirst($display_name);
	
	
	}
	
	
	
	
	/*Prepare user meta*/
	function prepare ($array )
	{
		
		foreach($array as $k => $v) 
		{
			if ($k == 'usersultra-update' || $k == 'xoouserultra-profile-edition-form'  ) continue;
			
			$this->usermeta[$k] = $v;
		}
		return $this->usermeta;
	}
	
	/*Handle/return any errors*/
	function handle() 
	{
	   
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		
	}
	
	public function get_user_info()
	{
		$current_user = wp_get_current_user();
		return $current_user;

		
	}
	
	/******************************************
	Get permalink for user
	******************************************/
	function get_user_profile_permalink( $user_id=0) 
	{
		
		global  $xoouserultra;
		
		$wp_rewrite = new WP_Rewrite();
		
		require_once(ABSPATH . 'wp-includes/link-template.php');		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		
				
		if ($user_id > 0) 
		{
		
			$user = get_userdata($user_id);
			$nice_url_type = $xoouserultra->get_option('usersultra_permalink_type');
			
						
			if ($nice_url_type == 'ID' || $nice_url_type == '' ) 
			{
				$formated_user_login = $user_id;
			
			}elseif ($nice_url_type == 'username') {
				
				$formated_user_login = $user->user_nicename;
				$formated_user_login = str_replace(' ','-',$formated_user_login);
			
			}elseif ($nice_url_type == 'name'){
				
				$formated_user_login = $xoouserultra->get_fname_by_userid( $user_id );
			
			}elseif ($nice_url_type == 'display_name'){
				
				$formated_user_login = get_user_meta( $user_id, 'display_name', true);					
				$formated_user_login = str_replace(' ','-',$formated_user_login);
			
							
				
			}
			
			$formated_user_login = strtolower ($formated_user_login);
			$profile_page_id = $xoouserultra->get_option('profile_page_id');
		    			

			/* append permalink */
			if ( $xoouserultra->get_option('usersultra_permalink_type') == '' )
			{
				$link = add_query_arg( 'uu_username', $formated_user_login, get_page_link($profile_page_id) );
				
			}else{
				
				$link = trailingslashit ( trailingslashit( get_page_link($profile_page_id) ) . $formated_user_login );
				
			}
		
		} else {
			$link = get_page_link($page_id);
		}

		return $link;
	}
	
	function parse_user_id_from_url()
	{
		$user_id="";
		
		if(isset($_GET["page_id"]) && $_GET["page_id"]>0)
		{
			$page_id = $_GET["page_id"];
			$user_id = $this->extract_string($page_id, '/', '/');
		
		
		}
		
		return $user_id;
		
	
	}
	
	function extract_string($str, $start, $end)
		{
		$str_low = $str;
		$pos_start = strpos($str_low, $start);
		$pos_end = strpos($str_low, $end, ($pos_start + strlen($start)));
		if ( ($pos_start !== false) && ($pos_end !== false) )
		{
		$pos1 = $pos_start + strlen($start);
		$pos2 = $pos_end - $pos1;
		return substr($str, $pos1, $pos2);
		}
	}
	
	/**
	Get Internatl Menu Links
	******************************************/
	public function get_internal_links($slug, $slug_2, $id)
	{
		$url = "";
			
			if(!isset($_GET["page_id"]) && !isset($_POST["page_id"]) )
			{
				$url = '?module='.$slug.'&'.$slug_2.'='. $id.'';	
				
			}else{
				
				if(isset($_GET["page_id"]) )
			    {
					
					$page_id = $_GET["page_id"];
				
				}else{
					
					$page_id = $_POST["page_id"];
					
				}
				
				
				$url = '?page_id='.$page_id.'&module='.$slug.'&'.$slug_2.'='. $id.'';			
			
			}
			
		
		return $url;	
		
	
	}
	
	/**
	Get Internal Messaging Menu Links
	******************************************/
	public function get_internal_pmb_links($slug, $slug_2, $id)
	{
		$url = "";
			
			if(!isset($_GET["page_id"]) && !isset($_POST["page_id"]) )
			{
				$url = '?module='.$slug.'&'.$slug_2.'='. $id.'';	
				
			}else{
				
				if(isset($_GET["page_id"]) )
			    {
					
					$page_id = $_GET["page_id"];
				
				}else{
					
					$page_id = $_POST["page_id"];
					
				}
				
				
				$url = '?page_id='.$page_id.'&module='.$slug.'&'.$slug_2.'='. $id.'';			
			
			}
			
		
		return $url;	
		
	
	}
	
	
	/**
	Get Menu Links
	******************************************/
	public function get_user_backend_menu($slug)
	{
		global $xoouserultra;
		
		$url = "";
		
		if($slug=="dashboard")
		{
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=dashboard"><span><i class="fa fa-tachometer fa-2x"></i></span>'.__('Dashboard', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=dashboard"><span><i class="fa fa-tachometer fa-2x"></i></span>'.__('Dashboard', 'xoousers').'</a>';			
			
			}
			
		}elseif($slug=="profile"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=profile"><span><i class="fa fa-user fa-2x"></i></span>'.__('Profile', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=profile"><span><i class="fa fa-user fa-2x"></i></span>'.__('Profile', 'xoousers').'</a>';			
			
			}
		
		}elseif($slug=="profile-customizer"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=profile"><span><i class="fa fa-user fa-2x"></i></span>'.__('Profile Customizer', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=profile"><span><i class="fa fa-user fa-2x"></i></span>'.__('Profile Customizer', 'xoousers').'</a>';			
			
			}
			
		
		}elseif($slug=="account"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=account"><span><i class="fa fa-wrench  fa-2x"></i></span>'.__('My Account', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=account"><span><i class="fa fa-wrench  fa-2x"></i></span>'.__('My Account', 'xoousers').'</a>';			
			
			}
			
		
		}elseif($slug=="settings"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=settings"><span><i class="fa fa-gear  fa-2x"></i></span>'.__('Settings', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=settings"><span><i class="fa fa-gear  fa-2x"></i></span>'.__('Settings', 'xoousers').'</a>';			
			
			}
			
		
		}elseif($slug=="wootracker"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=wootracker"><span><i class="fa fa-truck   fa-2x"></i></span>'.__('My Purchases', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=wootracker"><span><i class="fa fa-truck   fa-2x"></i></span>'.__('My Purchases', 'xoousers').'</a>';			
			
			}
		
		}elseif($slug=="myorders"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=myorders"><span><i class="fa fa-list   fa-2x"></i></span>'.__('My Orders', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=myorders"><span><i class="fa fa-list   fa-2x"></i></span>'.__('My Orders', 'xoousers').'</a>';			
			
			}
		
		}elseif($slug=="messages"){ 
			
			
			//check if unread replies or messages
			
			$user_id = get_current_user_id();
			$total = $xoouserultra->mymessage->get_unread_messages_amount($user_id);		
		
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=messages"><span><i class="fa icon-envelope"></i></span>'.__('My Messages', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=messages"><span><i class="icon-envelope"></i></span>'.__('Messages', 'xoousers').'</a>';			
			
			}
			
			if($total>0)
			{
				$url .= '<div class="uultra-noti-bubble" title="'.__('Unread Messages', 'xoousers').'">'.$total.'</div>';
			
			}
			
		
		}elseif($slug=="photos"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=photos"><span><i class="fa fa-camera fa-2x"></i></span>'.__('Photos', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=photos"><span><i class="fa fa-camera fa-2x"></i></span>'.__('Photos', 'xoousers').'</a>';			
			
			}
		
		}elseif($slug=="videos"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=videos"><span><i class="fa fa-video-camera fa-2x"></i></span>'.__('My Videos', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=videos"><span><i class="fa fa-video-camera fa-2x"></i></span>'.__('My Videos', 'xoousers').'</a>';			
			
			}
		
		}elseif($slug=="friends"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=friends"><span><i class="icon-users"></i></span>'.__('My Friends', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=friends"><span><i class="icon-users"></i></span>'.__('My Friends', 'xoousers').'</a>';			
			
			}
		
		
		}elseif($slug=="posts"){
			
			
			if(!isset($_GET["page_id"]))
			{
				$url = '<a class="uultra-btn-u-menu" href="?module=posts"><span><i class="fa fa-edit fa-2x"></i></span>'.__('My Posts', 'xoousers').'</a>';	
				
			}else{
				
				$url = '<a class="uultra-btn-u-menu" href="?page_id='.$_GET["page_id"].'&module=posts"><span><i class="fa fa-edit fa-2x"></i></span>'.__('My Posts', 'xoousers').'</a>';			
			
			}
		
		}elseif($slug=="logout"){		
							
		     $url = '<a class="uultra-btn-u-menu" href="'.$xoouserultra->get_logout_url().'"><span><i class="fa fa-arrow-circle-right fa-2x"></i></span>'.__('Logout', 'xoousers').'</a>';			
			
			
				
		}
		
		return $url;	
		
	
	
	}
	
	
	/**
	Display Public Profile
	******************************************/
	public function show_public_profile($atts)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/user.php');
		
		extract( shortcode_atts( array(
		
			'template' => 'profile', //this is the template file's name	
			'user_id' => '', //this is the template file's name	
			'template_width' => '100%', //this is the template file's name			
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'pic_size' => 230, // size in pixels of the user's picture	
			
			'gallery_type' => '', // lightbox or single page for each photo	
			
			'media_options_exclude' => '', // rating, description, tags, category
			
			'disable' => '', // photos, videos, messages	
					
			'optional_fields_to_display' => '', // 
			'optional_right_col_fields_to_display' => '', 
			'profile_fields_to_display' => '', // all or empty
			
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_social' => 'yes', // display social
			'display_photo_rating' => 'yes', // display social	
			'display_photo_description' => 'yes', //yes or no
			'display_gallery_rating' => 'yes', // display social
			'display_private_message' => 'yes', // display social
			
		), $atts ) );
		
		//exclude modules	
		$modules = array();
		$modules  = explode(',', $disable);		
		
		$display_gallery = false;
		if(isset($_GET["gal_id"]))
		{
			$display_gallery = true;			
			$gal_id = $_GET["gal_id"];				
		
		}
		
		$display_photo = false;
		if(isset($_GET["photo_id"]))
		{
			$display_photo = true;			
			$photo_id = $_GET["photo_id"];				
		
		}
		
		//check if it's a shortcode call
		
		if($user_id!="") // a shortocode attribute has been submited
		{
			
			$current_user = get_user_by('id',$user_id);
			
		}else{
			
			
			
				//get current user			
				$current_user = $this->get_user_data_by_uri();
				
				if(isset($current_user->ID))
				{
					$user_id = $current_user->ID;				
				
				}
				
				
			
				
				//check if logged in and seeing my own profile
				if (is_user_logged_in() && $user_id=="") 
				{
					
					$user_id=get_current_user_id(); 
					$current_user = get_user_by('id',$user_id);
				
				}
				
				
				//update stats for this user
				if($user_id>0)
				{
					$xoouserultra->statistc->update_hits($user_id, 'user');				
				
				}
		
		
		}	
		
		//check visibility settings		
		$photos_available = $this->do_logged_validation();
		
		//echo "LOGGED IN: ". $user_id;
		
		if($user_id>0)
		{
			//get template
			require_once(xoousers_path.'/templates/'.xoousers_template."/".$template.".php");		
				
		}else{
			
			//user not found
			echo do_shortcode("[usersultra_login]");
			
		}			
			
		
		
	}
	
	public function do_logged_validation()
	{
		global $xoouserultra;
		
		$photo_visibility = $xoouserultra->get_option("uurofile_setting_display_photos");
		
		if($photo_visibility=='public' || $photo_visibility=="")
		{
			$photos_available = true;
		
		}else{
			
			 if (!is_user_logged_in()) 
		     {
				 $photos_available = false;
			
			 }else{
				 
				 $photos_available = true;
				
			 }
		
		}
		
		return $photos_available;
	
	
	
	}
	
	/**
	Display most visited users List
	******************************************/
	public function show_most_visited_users($atts)
	{
		global    $xoouserultra;
		
		
		extract( shortcode_atts( array(	
			
			'item_width' => '25%', // this is the width of each item or user in the directory			
			'howmany' => 3, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'dynamic', // dynamic or fixed
			'pic_size' => 100, // size in pixels of the user's picture
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',
			
			
		), $atts ) );
		
		$html = "";
		
				
		$users_list = $this->get_most_visited_users($howmany);
		
		$html.='<div class="uultra-mostvisited-users">
			
			<ul>';
		
		foreach ( $users_list as $user )
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" style="width:'.$item_width.'" >
               
               <div class="prof-photo">
               
                   '.$this->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'             
               
               </div>        
            
                <div class="info-div">          
			
				 <p class="uu-direct-name">'.  $this->get_display_name($user_id).'</p>               
                
                 <div class="social-icon-divider">  </div> ';
                
                 if ($optional_fields_to_display!="") { 
                 
                 
                   $html .= $this->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);   
                 
                 
                
                  }
                
                  $html .= '</div> 
                 
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'">See Profile</a>
                  
                  </div> 
            
            
            </li>';
			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	
	public function get_most_visited_users ($howmany)
	{
		global $wpdb, $xoouserultra;
		
		$sql = ' SELECT u.*, stat.stat_item_id,
		  stat.stat_module , stat.stat_total_hits
		  
		  FROM ' . $wpdb->prefix . 'users u  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_stats stat ON (stat.stat_item_id = u.ID)";
				
		$sql .= " WHERE stat.stat_item_id = u.ID AND  stat.stat_module= 'user' ORDER BY stat.stat_total_hits DESC  LIMIT $howmany";	
			
		$rows = $wpdb->get_results($sql);
		
		return $rows;
		
	}
	
	/**
	Display top rated users List
	******************************************/
	public function show_minified_profile($atts)
	{
		global    $xoouserultra;
		
		extract( shortcode_atts( array(	
			
			'item_width' => '', // this is the width of each item or user in the directory			
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size' => 50, // size in pixels of the user's picture
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'optional_fields_to_display' => 'social,country', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => '',
			'display_country_flag' => '',
			
			
			
			
		), $atts ) );
		
		$html = "";
		
				
		$users_list = $this->get_logged_in_user();
		
		$html.='<div class="uultra-miniprofile-users">
			
			<ul>';
		
		foreach ( $users_list as $user )
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" style="width:'.$item_width.'" >
               
               <div class="prof-photo">               
                   '.$this->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'            
               </div>        
            
                <div class="info-div"> 
								
				 <p class="uu-direct-name"><a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'">'. $this->get_display_name($user_id).' </a> <span>'.$this->get_user_country_flag($user_id).'</span></p> ';
                
                 if ($optional_fields_to_display!="") 
				 { 
                 
                   $html .= $this->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);                  
                
                  }
				  
				  $html .= '<div class="tool-div-bar"><a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'" '.__('See Profile','xoousers').'><i class="fa fa-eye fa-lg"></i> </a> 
				  <a class="uultra-btn-profile" href="'.$xoouserultra->get_logout_url().'" title="'.__('Logout','xoousers').'"> <i class="fa fa-power-off fa-lg"></i> </a>  </div> ';
                
                  $html .= '</div> ';
            
            $html .=' </li>';			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	
	function get_logged_in_user()
	{
		global  $wpdb,  $xoouserultra;
		
		$logged_user_id = get_current_user_id();
		$sql = "SELECT ID, user_login, user_nicename from ".$wpdb->prefix ."users WHERE ID = '".$logged_user_id."' ";
				
		$rows = $wpdb->get_results($sql);
			
		return $rows;
	}
	
	/**
	Display top rated users List
	******************************************/
	public function show_latest_users($atts)
	{
		global    $xoouserultra;
		
		extract( shortcode_atts( array(	
			
			'item_width' => '', // this is the width of each item or user in the directory			
			'howmany' =>3, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size' => 50, // size in pixels of the user's picture
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => '',
			'display_country_flag' => '',
			
			
			
			
		), $atts ) );
		
		$html = "";
		
				
		$users_list = $this->get_latest_users($howmany);
		
		$html.='<div class="uultra-latest-users">
			
			<ul>';
		
		foreach ( $users_list as $user )
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" style="width:'.$item_width.'" >
			
			 
               
               <div class="prof-photo">
               
                   '.$this->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'             
               
               </div>        
            
                <div class="info-div"> 
				
				
				
				 
				 
				         
			
				 <p class="uu-direct-name"><a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'">'. $this->get_display_name($user_id).' </a> <span>'.$this->get_user_country_flag($user_id).'</span></p> ';
                
                 if ($optional_fields_to_display!="") { 
                 
                 
                   $html .= $this->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);                  
                
                  }
				  
				  
				  $html .= '<div class="tool-div-bar"><a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'" title="'.__('See Profile','xoousers').'" alt="'.__('See Profile','xoousers').'" "><i class="fa fa-eye fa-lg"></i> </a>  </div> ';
                
                  $html .= '</div> ';
				  
				 /* $html .= '				                   
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'">See Profile</a>
                  
                  </div> ';*/
            
            
            $html .=' </li>';
			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	

	
	function get_latest_users( $howmany )
	{
		global  $wpdb,  $xoouserultra;
		
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);
			
		// prepare arguments
		$args  = array(
		
		'orderby' => 'ID',
		'order' => 'DESC',
		'number' => $howmany,
		
		// check for two meta_values
		'meta_query' => array(
			array(
				
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
				),
			
		));

			
		
		
				

		$wp_user_query = new WP_User_Query($args);		
		$res = $wp_user_query->results;
			
		return $res;
	}
	
	function get_latest_users_private( $howmany )
	{
		global  $wpdb,  $xoouserultra;
		
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);
			
		// prepare arguments
		$args  = array(
		
		'orderby' => 'ID',
		'order' => 'DESC',
		'number' => $howmany,
		
		);

			
		
		
				

		$wp_user_query = new WP_User_Query($args);		
		$res = $wp_user_query->results;
			
		return $res;
	}
	
	
	
	/**
	Display top rated users List
	******************************************/
	public function show_top_rated_users($atts)
	{
		global    $xoouserultra;
		
		
		extract( shortcode_atts( array(	
			
			'item_width' => '46%', // this is the width of each item or user in the directory			
			'howmany' => 2, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size' => 100, // size in pixels of the user's picture
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',
			
			
		), $atts ) );
		
		$html = "";
		
				
		$users_list = $this->get_top_rated_users($howmany);
		
		$html.='<div class="uultra-toprated-users">
			
			<ul>';
		
		foreach ( $users_list as $user )
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" style="width:'.$item_width.'" >
               
               <div class="prof-photo">
               
                   '.$this->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'             
               
               </div>        
            
                <div class="info-div">          
			
				 <p class="uu-direct-name">'. $this->get_display_name($user_id).'</p>               
                
                 <div class="social-icon-divider">  </div> ';
                
                 if ($optional_fields_to_display!="") { 
                 
                 
                   $html .= $this->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);   
                 
                 
                
                  }
                
                  $html .= '</div> 
                 
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="'.$this->get_user_profile_permalink( $user_id).'">See Profile</a>
                  
                  </div> 
            
            
            </li>';
			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	
	public function get_user_display_name($user_id)
	{
		$display_name = "";
		
		$user = get_user_by('id',$user_id);
		
		$display_name = get_user_meta($user_id, 'display_name', true);
		
		if($display_name=="")
		{			
			$display_name =$user->display_name;		
		
		}
		
		return $display_name;
	
	}
	
	
	public function get_top_rated_users ($howmany)
	{
		global $wpdb, $xoouserultra;
		
		$sql = ' SELECT u.*, rate.ajaxrating_votesummary_user_id,
		  rate.ajaxrating_votesummary_total_score 
		  
		  FROM ' . $wpdb->prefix . 'users u  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_ajaxrating_votesummary rate ON (rate.ajaxrating_votesummary_user_id = u.ID)";
				
		$sql .= " WHERE rate.ajaxrating_votesummary_user_id = u.ID ORDER BY rate.ajaxrating_votesummary_total_score DESC  LIMIT $howmany";	
			
		$rows = $wpdb->get_results($sql);
		
		return $rows;
		
	}
	
	/**
	Display promoted users List
	******************************************/
	public function show_promoted_users($atts)
	{
		global    $xoouserultra;
		
		
		extract( shortcode_atts( array(
		
			'users_list' => '', // users list separated by commas
			'item_width' => '100%', // this is the width of each item or user in the directory			
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size' => 100, // size in pixels of the user's picture
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'optional_fields_to_display' => '', //
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_latest_photos' => 'yes', 			
			'display_latest_photos_size' => 90,
			'display_latest_photos_howmany' =>8, 
			'display_promote_desc' =>'',
			'display_promote_title' =>'',
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',
			
		), $atts ) );
		
		$html = "";
		
		$users_list = $this->users_shortcodes_promoted($users_list);
		
		$html.='<div class="uultra-promoted-users">
			
			<ul>';
		
		foreach($users_list['users'] as $user) 		
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" >
               
               <div class="prof-photo">
               
                   '.$xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'             
               
               </div>        
            
                <div class="info-div">          
			
				 <p class="uu-direct-name">'. $this->get_display_name($user_id).'</p> 
				 
				 <p>'.$this->get_user_country_flag($user_id).'</p>				 
				 <p>'.$this->get_user_social_icons($user_id).'</p>
				 
				 
				               
                
                 <div class="social-icon-divider">  </div> ';
				
				 if ($display_latest_photos=="yes") 
				 {
					 $html .= $this->get_user_spot_photo($user_id, $display_latest_photos_size, $display_latest_photos_howmany); 
				 
				 
				 }
				 
				  if ($display_promote_desc!="") 
				 {
					  $html .= "<h3>" .$display_promote_title."</h3>";
					 $html .= "<p class='desc'>" .$display_promote_desc."</p>";
				 
				 
				 }
				 
				 
					
				 
				 if ($optional_fields_to_display!="") 
				 { 
                 
                   $html .= $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);   
                 
                  }
				  
				  
				  
                
                  $html .= '</div> 
                 
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="'.$xoouserultra->userpanel->get_user_profile_permalink( $user_id).'">See Profile</a>
                  
                  </div> 
            
            
            </li>';
			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	
	function get_user_spot_photo($user_id, $display_latest_photos_size, $display_latest_photos_howmany)
	{
		global $wpdb, $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
		$upload_folder =  $xoouserultra->get_option('media_uploading_folder');
		
		$html = "";
		
		$rows = $xoouserultra->photogallery->get_user_photos($user_id, $display_latest_photos_howmany);
		
		if ( empty( $rows ) )
		{
		
		
		}else{
			
			$html.='<div class="uultra-promototed-photo-list">
			
			<ul>';
			
			
			foreach ( $rows as $photo )
			{
				
					
				$file=$photo->photo_thumb;				
				$thumb = $site_url.$upload_folder."/".$user_id."/".$file;					
								
				$html.= "<li id='".$photo->photo_id."' class='".$box_border." ".$box_shadow." ".$display."' >
										
				<a href='".$xoouserultra->userpanel->public_profile_get_photo_link($photo->photo_id, $user_id)."' class='' ><img src='".$thumb."' class='rounded' style='max-width:".$display_latest_photos_size."px'/> </a>";
					
							
					
				$html.= "</li>";	
							
			
			}
			
			$html.='</ul></div>';
		
		
		}
		
		return $html;
		
	
	
	}
	
	
	
	/**
	Display featured users List
	******************************************/
	public function show_featured_users($atts)
	{
		global    $xoouserultra;
		
		
		extract( shortcode_atts( array(		
		
		    'users_list' => '', // this is the width of each item or user in the directory				
			'item_width' => '21%', // this is the width of each item or user in the directory			
			'howmany' => 10, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size' => 100, // size in pixels of the user's picture
			'pic_size_type' => 'dynamic', // dynamic or fixed	
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'box_border' => 'rounded',
			'box_shadow' => 'shadow',
			'display' => 'in-line',			
			'list_order' => 'ASC', // asc or desc ordering
		), $atts ) );
		
		$html = "";
		
		$users_list = $this->users_shortcodes_featured($users_list);
		
		$html.='<div class="uultra-featured-users">
			
			<ul>';
		
		foreach($users_list['users'] as $user) 		
		{
			
			$user_id = $user->ID; 
		
		    if($pic_boder_type=="rounded")
		    {
			   $class_avatar = "avatar";
			   
		    }
			
			$html .= '<li class="'.$box_border.' '.$box_shadow.' '.$display.'" >
               
               <div class="prof-photo">
               
                   '.$xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type).'             
               
               </div>        
            
                <div class="info-div">          
			
				 <p class="uu-direct-name">'. $this->get_display_name($user_id).'</p>               
                
                 <div class="social-icon-divider">  </div> ';
                
                 if ($optional_fields_to_display!="") { 
                 
                 
                   $html .= $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display);   
                 
                 
                
                  }
                
                  $html .= '</div> 
                 
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="'.$xoouserultra->userpanel->get_user_profile_permalink( $user_id).'">See Profile</a>
                  
                  </div> 
            
            
            </li>';
			
		
		} //end foreach
		
		
		$html.='</ul></div>';
		
		return $html ;
		
	
	
	}
	
	function users_shortcodes_promoted( $users_list )
	{
		global  $wpdb,  $xoouserultra;
		
		$users_list  = explode(',', $users_list);
		
			
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);
			
		
		$query['include'][] = array($users_list);
			
				

		$wp_user_query = new WP_User_Query(array('include' =>$users_list ));		
		$arr['users'] = $wp_user_query->results;
			
		return $arr;
	}
	
	function users_shortcodes_featured( $users_list )
	{
		global  $wpdb,  $xoouserultra;
		
		$users_list  = explode(',', $users_list);
		
			
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);
			
		
		$query['include'][] = array($users_list);
			
				

		$wp_user_query = new WP_User_Query(array('include' =>$users_list ));		
		$arr['users'] = $wp_user_query->results;
			
		return $arr;
	}
	
	private function uultra_build_search_field_array() {


        $custom_fields = get_option('usersultra_profile_fields');
        $this->search_banned_field_type = array('fileupload', 'password', 'datetime');

        $this->show_combined_search_field = false;
        $this->show_nontext_search_fields = false;

        $this->all_text_search_field = array();
        $this->combined_search_field = array();
        $this->nontext_search_fields = array();
        $this->checkbox_search_fields = array();

        $included_fields = '';
        if ($this->search_args['fields'] != '')
            $included_fields = explode(',', $this->search_args['fields']);

        $excluded_fields = explode(',', $this->search_args['exclude_fields']);

        $search_filters = array();
        $search_filters = explode(',', $this->search_args['filters']);

        foreach ($custom_fields as $key => $value) {
            if (isset($value['type']) && $value['type'] == 'usermeta') {
                if (isset($value['field']) && !in_array($value['field'], $this->search_banned_field_type)) {
                    if (isset($value['meta']) && !in_array($value['meta'], $excluded_fields)) {
                        switch ($value['field']) {
                            case 'text':
                            case 'textarea':
                            case 'datetime':

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters)) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->nontext_search_fields[] = $value;
                                } else {
                                    if ($this->show_combined_search_field === false)
                                        $this->show_combined_search_field = true;

                                    $this->all_text_search_field[] = $value['meta'];

                                    if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                        $this->combined_search_field[] = $value['meta'];
                                }



                                break;

                            case 'select':
                            case 'radio':

                                $is_in_field = false;
                                $is_in_filter = false;

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters))
                                    $is_in_filter = true;

                                if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                    $is_in_field = true;

                                if ($is_in_field == true || $is_in_filter == true) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->nontext_search_fields[] = $value;
                                }
                                break;

                            case 'checkbox':

                                $is_in_field = false;
                                $is_in_filter = false;

                                if (is_array($search_filters) && in_array($value['meta'], $search_filters))
                                    $is_in_filter = true;

                                if (is_array($included_fields) && count($included_fields) > 0 && in_array($value['meta'], $included_fields))
                                    $is_in_field = true;

                                if ($is_in_filter == true || $is_in_field == true) {
                                    if ($this->show_nontext_search_fields === false) {
                                        $this->show_nontext_search_fields = true;
                                    }

                                    $this->checkbox_search_fields[] = $value;
                                }
                                break;

                            default:
                                break;
                        }
                    }
                }
            }
        }
    }

    /* Setup search form */

    function uultra_search_form($args=array()) 
	{
		global $xoouserultra, $predefined;
		

        // Determine search form is loaded
        $this->uultra_search = true;
        /* Default Arguments */
        $defaults = array(
            'fields' => null,
            'filters' => null,
            'exclude_fields' => null,
            'operator' => 'AND',
			'width' => 'AND',
            'use_in_sidebar' => null,
            'users_are_called' => 'Users',
            'combined_search_text' => 'type user name here',
            'button_text' => 'Search',
            'reset_button_text' =>'Reset'
        );

        $this->search_args = wp_parse_args($args, $defaults);

        $this->search_operator = $this->search_args['operator'];

        if (strtolower($this->search_args['operator']) != 'and' && strtolower($this->search_args['operator']) != 'or') {
            $this->search_args['operator'] = 'AND';
        }

        // Prepare array of all fields to load
        $this->uultra_build_search_field_array();

        $sidebar_class = null;
        if ($this->search_args['use_in_sidebar'])
            $sidebar_class = 'uultra-sidebar';

        $display = null;

        $display.='<div class="xoouserultra-wrap xoouserultra-wrap-form uultra-search-wrap' . $sidebar_class . '">';
        $display.='<div class="xoouserultra-inner xoouserultra-clearfix">';
        $display.='<div class="xoouserultra-head">' . sprintf(__('Search %s', 'xoousers'), $this->search_args['users_are_called']) . '</div>';
        $display.='<form action="" method="get" id="uultra_search_form" class="uultra-search-form uultra-clearfix">';

        // Check For default fields Start
        if ($this->show_combined_search_field === true) {
            $display.='<p class="uultra-p uultra-search-p">';
            $display.= $xoouserultra->htmlbuilder->text_box(array(
                        'class' => 'uultra-search-input uultra-combined-search',
                        'value' => isset($_GET['uultra_combined_search']) ? $_GET['uultra_combined_search'] : '',
                        'name' => 'uultra_combined_search',
                        'placeholder' => $this->search_args['combined_search_text']
                    ));

            if (count($this->combined_search_field) > 0) {
                $display.='<input type="hidden" name="uultra_combined_search_fields" value="' . implode(',', $this->combined_search_field) . '" />';
            } else {
                $display.='<input type="hidden" name="uultra_combined_search_fields" value="' . implode(',', $this->all_text_search_field) . '" />';
            }


            $display.='</p>';
        }

        // Check For default fields End
        // Custom Search Fields Creation Starts

        if ($this->show_nontext_search_fields === true) {
			
			
            $counter = 0;
            $display.='<p class="uultra-p uultra-search-p">';
            foreach ($this->nontext_search_fields as $key => $value) {
				
				
                $method_name = '';
                $method_name = $this->method_dect[$value['field']];
                if ($method_name != '') 
				{
					
					
					
                    if ($counter > 0 && $counter % 2 == 0) {
                        $display.='</p>';
                        $display.='<p class="uultra-p uultra-search-p">';
                    }

                    $counter++;

                    $class = 'uultra-search-input uultra-search-input-left uultra-search-meta-' . $value['meta'];
                    if ($counter > 0 && $counter % 2 == 0)
                        $class = 'uultra-search-input uultra-search-input-right uultra-search-meta-' . $value['meta'];


                    if ($method_name == 'drop_down') 
					{
						//echo "here: ".$method_name;
                        $loop = array();
						
						

                        if (isset($value['predefined_options']) && $value['predefined_options'] != '' && $value['predefined_options'] != '0') {
							
							$defined_loop = $xoouserultra->commmonmethods->get_predifined( $value['predefined_options'] );
							
                          

                            foreach ($defined_loop as $option) {
                                if ($option == '' || $option == null) {
                                    $loop[$option] = $value['name'];
                                } else {
                                    $loop[$option] = $option;
                                }
                            }
                        } else if (isset($value['choices']) && $value['choices'] != '') {
                            $loop_default = explode(PHP_EOL, $value['choices']);
                            $loop[''] = $value['name'];

                            foreach ($loop_default as $option)
                                $loop[$option] = $option;
                        }

                        if (isset($_POST['uultra_search'][$value['meta']]))
                            $_POST['uultra_search'][$value['meta']] = stripslashes_deep($_GET['uultra_search'][$value['meta']]);


                        $default = isset($_GET['uultra_search'][$value['meta']]) ? $_GET['uultra_search'][$value['meta']] : '0';
                        $name = 'uultra_search[' . $value['meta'] . ']';

                        if ($value['field'] == 'checkbox') {
                            $default = isset($_GET['uultra_search'][$value['meta']]) ? $_GET['uultra_search'][$value['meta']] : array();
                            $name = 'uultra_search[' . $value['meta'] . '][]';
                        }

                        if (count($loop) > 0) {
                            $display.= $xoouserultra->htmlbuilder->drop_down(array(
                                        'class' => $class,
                                        'name' => $name,
                                        'placeholder' => $value['name']
                                            ), $loop, $default);
                        }
                    } else if ($method_name == 'text_box') {
                        if (isset($_GET['uultra_search'][$value['meta']]))
                            $_GET['uultra_search'][$value['meta']] = stripslashes_deep($_GET['uultra_search'][$value['meta']]);


                        $default = isset($_GET['uultra_search'][$value['meta']]) ? $_GET['uultra_search'][$value['meta']] : '';
                        $name = 'uultra_search[' . $value['meta'] . ']';

                        $display.= $xoouserultra->htmlbuilder->text_box(array(
                                    'class' => $class,
                                    'name' => $name,
                                    'placeholder' => $value['name'],
                                    'value' => $default
                                ));
                    }
                }
            }
            $display.='</p>';


            if (isset($this->checkbox_search_fields) && count($this->checkbox_search_fields) > 0) {
				
				
                foreach ($this->checkbox_search_fields as $key => $value) 
				{
					
                    $display.='<p class="uultra-p uultra-search-p uultra-multiselect-p">';

                    $method_name = '';
                    $method_name = $this->method_dect[$value['field']];
                    if ($method_name != '') {
                        $class = 'uultra-search-input uultra-search-multiselect uultra-search-meta-' . $value['meta'];

                        $loop = array();

                        if (isset($value['predefined_loop']) && $value['predefined_loop'] != '' && $value['predefined_loop'] != '0') {
                            $defined_loop = $predefined->get_array($value['predefined_loop']);

                            foreach ($defined_loop as $option)
                                $loop[$option] = $option;
                        } else if (isset($value['choices']) && $value['choices'] != '') {
                            $loop_default = explode(PHP_EOL, $value['choices']);
                            $loop[''] = $value['name'];

                            foreach ($loop_default as $option)
                                $loop[$option] = $option;
                        }

                        if (isset($_GET['uultra_search'][$value['meta']]))
                            $_GET['uultra_search'][$value['meta']] = stripslashes_deep($_GET['uultra_search'][$value['meta']]);

                        $default = isset($_GET['uultra_search'][$value['meta']]) ? $_GET['uultra_search'][$value['meta']] : '0';
                        $name = 'uultra_search[' . $value['meta'] . ']';
                        if ($value['field'] == 'checkbox') {
                            $default = isset($_GET['uultra_search'][$value['meta']]) ? $_GET['uultra_search'][$value['meta']] : array();
                            $name = 'uultra_search[' . $value['meta'] . '][]';
                        }

                        if (count($loop) > 0) {
                            $display.= $xoouserultra->htmlbuilder->drop_down(array(
                                        'class' => $class,
                                        'name' => $name,
                                        'placeholder' => $value['name']
                                            ), $loop, $default);
                        }
                    }

                    $display.='</p>';
                }
            }
        }

        $display.='<input type="hidden" name="userspage" id="userspage" value="" />';

        $display.='<input type="hidden" name="uultra-search-fired" id="uultra-search-fired" value="1" />';

        // Custom Search Fields Creation Ends
        // Submit Button
        $display.='<div class="uultra-searchbtn-div">';
        $display.=$xoouserultra->htmlbuilder->button('submit', array(
                    'class' => 'uultra-button-alt xoouserultra-button uultra-search-submit',
                    'name' => 'uultra-search',
                    'value' => $this->search_args['button_text']
                ));
        $display.='&nbsp;';
        $display.=$xoouserultra->htmlbuilder->button('button', array(
                    'class' => 'uultra-button-alt xoouserultra-button uultra-search-reset',
                    'name' => 'uultra-search-reset',
                    'value' => $this->search_args['reset_button_text'],
                    'id' => 'uultra-reset-search'
                ));

        $display.='</div>';
        $display.='</form>';

        $display.='</div>';
        $display.='</div>';
        /* Extra Clearfix for Avada Theme */
        $display.='<div class="uultra-clearfix"></div>';

        return $display;
    }
	
	/* Search user by more criteria */
	function uultra_query_search_displayname( $query ) {
		global $wpdb;
		$search_string = esc_attr( trim( get_query_var('searchuser') ) );
		$query->query_where .= $wpdb->prepare( " OR $wpdb->users.display_name LIKE %s", '%' . like_escape( $search_string ) . '%' );
	}

    /* Apply search params and Generate Results */

    function search_result($args) 
	{

        global $wpdb,$blog_id;
		
		extract($args);
		
		$memberlist_verified = 1;
		
		$blog_id = get_current_blog_id();

		$page = (!empty($_GET['uultra-page'])) ? $_GET['uultra-page'] : 1;
		$offset = ( ($page -1) * $per_page);

		/** QUERY ARGS BEGIN **/
		
		if (isset($args['exclude']))
		{
			$exclude = explode(',',$args['exclude']);
			$query['exclude'] = $exclude;
		}
		
		$query['meta_query'] = array('relation' => strtoupper($relation) );
		
		
		/*This is applied only if we have to filder certain roles*/
		if (isset($role) &&  $role!=""){
			
			$roles = explode(',',$role);
			if (count($roles) >= 2){
				$query['meta_query']['relation'] = 'or';
			}
			foreach($roles as $subrole){
			$query['meta_query'][] = array(
				'key' => $wpdb->get_blog_prefix( $blog_id ) . 'capabilities',
				'value' => $subrole,
				'compare' => 'like'
			);
			}
		}
		
	
	    if (isset($_GET['uultra_search'])) 
		{

        foreach ($_GET['uultra_search'] as $key => $value)
		{
			
			
			//echo $key ." val: " . $value;
			$target =  $value;

						
						
						/*if ($->field_type($key) == 'multiselect' ||
							$->field_type($key) == 'checkbox' ||
							$uultra->field_type($key) == 'checkbox-full'
							) {
							$like = 'like';
						} else {
							$like = '=';
						}*/
					
			$like = 'like';
			if (isset($target)  && $target != '' && $key != 'role' )
			{
				if (substr( trim( htmlspecialchars_decode($args[$key])  ) , 0, 1) === '>')
				{
					$choices = explode('>', trim(  htmlspecialchars_decode($args[$key]) ));
					$target = $choices[1];
					$query['meta_query'][] = array(
									'key' => $key,
									'value' => $target,
									'compare' => '>'
						);
				}elseif (substr( trim(  htmlspecialchars_decode($args[$key]) ) , 0, 1) === '<') {
								$choices = explode('<', trim(  htmlspecialchars_decode($args[$key]) ));
								$target = $choices[1];
								$query['meta_query'][] = array(
									'key' => $key,
									'value' => $target,
									'compare' => '<'
								);
							} elseif (strstr( esc_attr( trim(  $args[$key] ) ) , ':')){
								$choices = explode(':', esc_attr( trim(  $args[$key] ) ));
								$min = $choices[0];
								$max = $choices[1];
								$query['meta_query'][] = array(
									'key' => $key,
									'value' => array($min, $max),
									'compare' => 'between'
								);
							} elseif (strstr( esc_attr( trim( $args[$key] ) ) , ',')){
								$choices = explode(',', esc_attr( trim(  $args[$key] ) ));
								foreach($choices as $choice){
									$query['meta_query'][] = array(
										'key' => $key,
										'value' => $choice,
										'compare' => $like
									);
								}
							} else {
								
									$query['meta_query'][] = array(
										'key' => $key,
										'value' => esc_attr( trim( $target ) ),
										'compare' => $like
									);
							}
							
						}
				
						
						
						
                 } //end for each
				 
				 } //end if 
	
			 if ($memberlist_verified)
			  {
				$query['meta_query'][] = array(
					'key' => 'usersultra_account_status',
					'value' => 'active',
					'compare' => 'LIKE'
				);
			}
			
			if (isset($memberlist_withavatar) && $memberlist_withavatar == 1){
				$query['meta_query'][] = array(
					'key' => 'profilepicture',
					'value' => '',
					'compare' => '!='
				);
			}
			
			
		/**
			CUSTOM SEARCH FILTERS 
		**
		**
		**/
		
		if (isset($_GET['uultra_combined_search'])) 
		{
			 //echo "YES1";
			
			/* Searchuser query param */
			$search_string = esc_attr( trim( get_value('uultra_combined_search') ) );
			
			if ($search_string != '') 
			{
				// echo "YES2";
			
				 if (get_value('uultra_combined_search_fields') != '' && get_value('uultra_combined_search') != '') 
				 {
					 
					// echo "YES3";
					//$customfilters = explode(',',$args['memberlist_filters']);
					
					$customfilters = explode(',', get_value('uultra_combined_search_fields'));

                    $combined_search_text = esc_sql(like_escape(get_value('uultra_combined_search')));

					
					if ($customfilters)
					{
						if (count($customfilters) > 1) 
						{
							//$query['meta_query']['relation'] = 'or';
						}
						
						//print_r($customfilters);
										
						$query['meta_query'][] = array(
							'key' => 'display_name',
							'value' => $search_string,
							'compare' => 'LIKE'
						);
						
					}
				}
				
				
				}
			
			}
			
			
			
			
			if ($sortby) $query['orderby'] = $sortby;			
			if ($order) $query['order'] = strtoupper($order); // asc to ASC
			
			/** QUERY ARGS END **/
			
			$query['number'] = $per_page;
			$query['offset'] = $offset;
			
			/* Search mode */
		if ( ( isset($_GET['uultra_search']) && !empty($_GET['uultra_search']) ) || count($query['meta_query']) > 1 )
		{
			$count_args = array_merge($query, array('number'=>10000));
			unset($count_args['offset']);
			//$user_count_query = $this->get_cached_query( $count_args );
			
			$user_count_query = new WP_User_Query($count_args);
						
		}

		if ($per_page) 
		{
			
		
			/* Get Total Users */
			if ( ( isset($_GET['uultra_search']) && !empty($_GET['uultra_search']) ) || count($query['meta_query']) > 1 )
			{
				$user_count = $user_count_query->get_results();		
								
				$total_users = $user_count ? count($user_count) : 1;
				
			} else {
				
				//echo "HEREE";
				
				$result = count_users();
				$total_users = $result['total_users'];
			}
			
			$total_pages = ceil($total_users / $per_page);
		
		}
		
		//$wp_user_query = $this->get_cached_query( $query );
		$wp_user_query = new WP_User_Query($query);
		
		remove_action( 'pre_user_query', 'uultra_query_search_displayname' );
		
		if (! empty( $wp_user_query->results )) 
		{
			$arr['total'] = $total_users;
			$arr['paginate'] = paginate_links( array(
					'base'         => @add_query_arg('uultra-page','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('« Previous','xoousers'),
					'next_text'    => __('Next »','xoousers'),
					'type'         => 'plain',
				));
			$arr['users'] = $wp_user_query->results;
		}
		
		//print_r($arr);
		
		$this->searched_users = $arr;
		
		//echo "<pre>";	
		//print_r($query);
		//echo "</pre>";	
		
			
			
				
		
     }
	 
	 /******************************************
	Get a cached query
	******************************************/
	function get_cached_query($query)
	{
		$cached = $this->get_cached_results;
		$testcache = serialize($query);
		if ( !isset($cached["$testcache"]) ) 
		{
			$cached["$testcache"] = new WP_User_Query( unserialize($testcache) );
			update_option('uultra_cached_results', $cached);
			$query = $cached["$testcache"];
		} else {
			$query = $cached["$testcache"];
		}
		
		return $query;
	}
	
	
	/**
	Display Members List Minified
	******************************************/
	public function show_users_directory_mini($atts)
	{
		extract( shortcode_atts( array(
		
			'template' => 'directory_mini', //this is the template file's name
			'container_width' => '100%', // this is the main container dimension
			'item_width' => '10%', // this is the width of each item or user in the directory
			'item_height' => 'auto', // auto height
			'list_per_page' => 10, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'dynamic', // dynamic or fixed			
			'pic_size' => 100, // size in pixels of the user's picture
			'optional_fields_to_display' => '', // size in pixels of the user's picture
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_total_found' => 'yes', // display total found
			'display_total_found_text' => 'Users', // display total found			
			'list_order' => 'DESC', // asc or desc ordering
		), $atts ) );
		
		
		$search_array = array('list_per_page' => $list_per_page, 'list_order' => $list_order);		
		$users_list = $this->users($search_array);
		
		//display pages
		$disp_array = array('total' => $users_list['total'], 'text' => $display_total_found_text);
		
		$total_f = $this->get_total_found($disp_array);
		
	
		//get template
		require(xoousers_path.'/templates/'.xoousers_template."/".$template.".php");
		
	}
	
	
	public function get_current_page()
	{
		$page = "";		
		if(isset($_GET["ultra-page"]))
		{
			$page = $_GET["ultra-page"];
		
		}else{
			
			$page = 1;	
		
		}
		
		return $page;
		
	
	}
	
	
	
	/**
	Display Members List
	******************************************/
	public function show_users_directory($atts)
	{
		global $xoouserultra;
		extract( shortcode_atts( array(
		
			'template' => 'directory_default', //this is the template file's name
			'searchable_field_type' => 'simple', // simple or advanced
			'searchable_fields' => '', //example: 
			'container_width' => '100%', // this is the main container dimension
			'item_width' => '21%', // this is the width of each item or user in the directory
			'item_height' => 'auto', // auto height
			'list_per_page' => 3, // how many items per page
			'pic_type' => 'avatar', // display either avatar or main picture of the user
			'pic_boder_type' => 'none', // rounded
			'pic_size_type' => 'dynamic', // dynamic or fixed			
			'pic_size' => 100, // size in pixels of the user's picture
			'optional_fields_to_display' => '', // 
			'display_social' => 'yes', // display social
			'display_country_flag' => 'name', // display flag, no,yes,only, both. Only won't display name
			'display_total_found' => 'yes', // display total found
			'display_total_found_text' => 'Users', // display total found			
			'list_order' => 'DESC', // asc or desc ordering
			'role' => '', // filter by role
			'relation' => 'AND', // filter by role
		), $atts ) );
		
		
		$page = $this->get_current_page();
		
		
		$search_array = array('list_per_page' => $list_per_page, 'list_order' => $list_order);	
		
		$args= array('per_page' => $list_per_page, 'relation' => $relation, 'role' => $role);
		
		$this->current_users_page = $page;
		
		$this->search_result($args);
		
			
		$users_list = $this->searched_users;
		
		
		//display pages
		$disp_array = array('total' => $users_list['total'], 'text' => $display_total_found_text);
		
		$total_f = $this->get_total_found($disp_array);		
		
		
		$html ='';
		
		
		$html .='<div class="usersultra-front-directory-wrap">
		       	<div class="usersultra-searcher">
			    </div>';

      
       // $html .='<div class="usersultra-paginate top_display">'.$paginate.'</div>';
		
		if (isset($users_list['paginate'])) {
			
        $html .=' <div class="usersultra-paginate top_display">'. $users_list['paginate'].'</div>';
		
		 } 
	    
		if ($display_total_found=='yes') 
		{
			
		 	$html .=$total_f;
		}
    
    	$html .='<ul class="usersultra-front-results">';
        
        if(count($users_list['users'])>0)
		{
			foreach($users_list['users'] as $user)
			{
				
				$user_id = $user->ID; 
				
				//echo "<pre>";
				//print_r($user);
				//echo "</pre>";
			
			   if($pic_boder_type=="rounded")
			   {
				   $class_avatar = "avatar";
				   
				}
			
			
				
				
				$html .='<li class="rounded" style="width:'.$item_width.'">';               
				$html .='<div class="xoousers-prof-photo">';
				   
				$html .= $xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type);             
				   
				$html .=' </div> ';
				   
				   
				   
				   
					$html .=' <div class="info-div">';		
					$html .='<p class="uu-direct-name">'.  $xoouserultra->userpanel->get_display_name($user_id).'</p>';
					
					
					$html .=' <div class="social-icon-divider">                                       
					 
					  </div> ';
					
					if ($optional_fields_to_display!="") { 
					 
					 
					   $html .= $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display)  ;
					 
					 
					
					   }
					
					$html .=' </div> 
					 
					  <div class="uultra-view-profile-bar">';
					  
					  $html .='  <a class="uultra-btn-profile" href="'.$xoouserultra->userpanel->get_user_profile_permalink( $user_id).'">See Profile</a>
					  
					  </div> ';
				
				
				$html .='</li>';
				
				
			}    //end for each
       }  
        
       $html .=' </ul>';
        
        
		if (isset($users_list['paginate'])) {
			
        $html .=' <div class="usersultra-paginate bottom_display">'. $users_list['paginate'].'</div>';
		
		 } 

 $html .='</div>';
		
		
		return $html;
		
		
	}
	
	
	public function get_result_pages($reg_count,$page, $list_perpage)
	{
			
		
		$total_pages = ceil($reg_count / $list_perpage);
		
		
		$big = 999999999; // need an unlikely integer
		$arr = paginate_links( array(
					'base'         => @add_query_arg('ultra-page','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('Previous','xoousers'),
					'next_text'    => __('Next','xoousers'),
					'type'         => 'plain',
				));
	return $arr;
	
	}
	
	public function get_custom_search_fields($fields_list)
	{
		
		$display .= '<div class="xoouserultra-field-value">';
					
					switch($field) {
					
												
						case 'text':
							$display .= '<input type="text" class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" />';
							break;							
							
						case 'datetime':
						    $display .= '<input type="text" class="xoouserultra-input'.$required_class.' xoouserultra-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" />';
						    break;
							
						case 'select':
						
							if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' )
							
							{
								$loop = $this->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
								
							}elseif (isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
								
								$loop = explode(PHP_EOL, $choices);
							}
							
							if (isset($loop)) 
							{
								$display .= '<select class="xoouserultra-input'.$required_class.'" name="'.$meta.'" id="'.$meta.'" title="'.$name.'">';
								
								foreach($loop as $option)
								{
									
								$option = trim($option);
								    
								$display .= '<option value="'.$option.'" '.selected( $this->get_post_value($meta), $option, 0 ).'>'.$option.'</option>';
								}
								$display .= '</select>';
							}
							$display .= '<div class="xoouserultra-clear"></div>';
							break;
							
						case 'radio':
						
							if (isset($array[$key]['choices']))
							{
								$loop = explode(PHP_EOL, $choices);
							}
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as $option)
								{
								    if($counter >0)
								        $required_class = '';
								    
								    $option = trim($option);
									$display .= '<label class="xoouserultra-radio"><input type="radio" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'" value="'.$option.'" '.checked( $this->get_post_value($meta), $option, 0 );
									$display .= '/> '.$option.'</label>';
									
									$counter++;
									
								}
							}
							$display .= '<div class="xoouserultra-clear"></div>';
							break;
							
						case 'checkbox':
						
							if (isset($array[$key]['choices'])) 
							{
								$loop = explode(PHP_EOL, $choices);
							}
							
							if (isset($loop) && $loop[0] != '') 
							{
							  $counter =0;
							  
								foreach($loop as $option)
								{
								   
								   if($counter >0)
								        $required_class = '';
								  
								  $option = trim($option);
									$display .= '<label class="xoouserultra-checkbox"><input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" value="'.$option.'" ';
									if (is_array($this->get_post_value($meta)) && in_array($option, $this->get_post_value($meta) )) {
									$display .= 'checked="checked"';
									}
									$display .= '/> '.$option.'</label>';
									
									$counter++;
								}
							}
							$display .= '<div class="xoouserultra-clear"></div>';
							break;
							
							
					}
		
	
	        $display .= '</div>';
			
			return  $display ;
	
	}
	
	public function get_total_found($users_list)
	{
		extract($users_list);
		
		if($total=="" ){$total=0;}
		
		$html = '<div class="uultra-search-results">
			<h1>'.__('Total found: ','xoousers').''.$total .' '.$text.'</h1>
			
			</div>';
			
		return $html;
	
	
	}
		
	public  function public_profile_get_album_link ($id, $user_id) 
	{
		$url ="";		
		$url = $this->get_user_profile_permalink($user_id)."?gal_id=".$id;		
		
		return $url;
	
	}
	
	public  function public_profile_get_photo_link ($id, $user_id) 
	{
		$url ="";		
		$url = $this->get_user_profile_permalink($user_id)."?photo_id=".$id;		
		
		return $url;
	
	}	
	
	
	public  function public_profile_display_social ($user_id) 
	{
		 global  $xoouserultra;
		 
		 $array = get_option('usersultra_profile_fields');	
		 		
		$html_social ="<div class='uultra-prof-social-icon'>";
			

		foreach($array as $key=>$field) 
		{					
			
			if($field['social']==1)
			{
									
				$icon = $field['icon'];
				
				//get meta
				$social_meta = get_user_meta($user_id, $field['meta'], true);
				
				
				
				if($social_meta!=""){
				
				$html_social .="<a href='".$social_meta."' target='_blank'><i class='uultra-social-ico fa fa-".$icon." '></i></a>";
				
				}
				
				
				
			}
			
		}	
		
		$html_social .="</div>";
		
		return $html_social;
	
	}
	
	public function get_user_country_flag($user_id)
	{
		global  $xoouserultra;
		
		$u_meta = get_user_meta($user_id, 'country', true);
		
		//get country ISO code	
		$img = "";	
											
		$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));	
		
		if($isocode!=0)		
		{	
						
			$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
			$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';
		
		}
		
		return  $img;
	
	
	}
	
	public  function display_optional_fields ($user_id, $display_country_flag, $fields_to_display) 
	{
		 global  $xoouserultra;
		
		$fields_list = "";
		$fields  = explode(',', $fields_to_display);
		
		if($fields_to_display!="")
		
		{
		
			foreach ($fields as $field) 
			{
				//get meta
				
				$u_meta = get_user_meta($user_id, $field, true);
				
				if( $field =='country')
				{
					//rule applied to country only
				
					if($display_country_flag=='only') //only flag
					{
						if($u_meta=="")				
						{
							$fields_list .= __("Country not available", 'xoousers');						
						
						}else{
							
						//get country ISO code		
												
							$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));				
							
							$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
							$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';					
							$fields_list .= "<p class='country_name'>".$img."</p>";
						
						
						}					
										
					}elseif($display_country_flag=='both'){
						
						if($u_meta=="")				
						{
							$fields_list .= __("Country not available", 'xoousers');;
							
						
						}else{
						
							$isocode = array_search($u_meta, $xoouserultra->commmonmethods->get_predifined('countries'));				
							if($isocode!="0")
							{
								$isocode  = xoousers_url."libs/flags/24/".$isocode.".png";					
								$img = '<img src="'.$isocode.'"  alt="'.$u_meta.'" title="'.$u_meta.'" class="uultra-country-flag"/>';					
								$fields_list .= "<p class='country_name'>".$img."  ".$u_meta."</p>";
							
							}
						
						}
					
					}elseif($display_country_flag=='name'){					
						
						$fields_list .= "<p class='country_name'>".$u_meta."</p>";		
							
					
					}
				
				}elseif($field =='description'){
					
					if($u_meta=="")				
					{
						$u_meta = __("This user hasn't a description yet", 'xoousers');
					
					
					}
					
					$fields_list .= "<p class='desc'>".$u_meta."</p>";
					
					
				}elseif($field =='social'){ //this rule applies only to social icons
					
									
					$array = get_option('usersultra_profile_fields');			
					$html_social ="<div class='uultra-prof-social-icon'>";
						
	
					foreach($array as $key=>$field) 
					{
						$_fsocial = "";
						
						if(isset($field['social']))	
						{
							$_fsocial = $field['social'];					
						}		
					
						
						if($_fsocial==1)
						{
												
							$icon = $field['icon'];
							
							//get meta
							$social_meta = get_user_meta($user_id, $field['meta'], true);
							
							if($social_meta!="")
							{
								$html_social .="<a href='".$social_meta."' target='_blank'><i class='uultra-social-ico fa fa-".$icon." '></i></a>";
					
							}
							
							
						}
						
					}	
					
					$html_social .="</div>";			
					
					
					$fields_list .= $html_social;
					
					
				
				
				}elseif($field =='rating'){ //this rule applies only to rating
				
								
					$fields_list.= "<div class='ratebox'>";
					$fields_list.= $xoouserultra->rating->get_rating($user_id,"user_id");
					$fields_list.= "</div>";
				
				
				}elseif($field =='like'){ //like rules			   				
					
					$fields_list.= $xoouserultra->social->get_item_likes($user_id,"user");	
				
				}elseif($field =='friend'){ //like rules			   				
					
					$fields_list.= $xoouserultra->social->get_friends($user_id);		
							
				
				}else{
						
					$fields_list .= "<p>".$u_meta."</p>";
				
				
				
				}
				
				
			
			}
			
		}
		
		return $fields_list;
		
		
	
	
	}
	
	
	
	public function get_user_social_icons($user_id)
	{
		
		
		$array = get_option('usersultra_profile_fields');			
		$html_social ="<div class='uultra-prof-social-icon'>";
					

				foreach($array as $key=>$field) 
				{			
				
					
					if($field['social']==1)
					{
											
						$icon = $field['icon'];
						
						//get meta
						$social_meta = get_user_meta($user_id, $field['meta'], true);
						
						if($social_meta!="")
						{
							$html_social .="<a href='".$social_meta."' target='_blank'><i class='uultra-social-ico fa fa-".$icon." '></i></a>";
						
						}
						
						
						
						
						
					}
					
				}	
				
				$html_social .="</div>";	
				
				return $html_social;
	
	
	}
	
	/* Get picture by ID */
	function refresh_avatar() 
	{
		$user_id = get_current_user_id();
		
		echo $this->get_user_pic( $user_id, $pic_size, 'avatar', 'rounded', 'dynamic');
		die();
	}
	
	/* delete avatar */
	function delete_user_avatar() 
	{
		$user_id = get_current_user_id();
		
		update_user_meta($user_id, 'user_pic', '');
		die();
	}
	
	
	
	/* Get picture by ID */
	function get_user_pic( $id, $size, $pic_type=NULL, $pic_boder_type= NULL, $size_type=NULL ) 
	{
		
		 global  $xoouserultra;

		 require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		 
		$avatar = "";
		$pic_size = "";
		
		$upload_folder = $xoouserultra->get_option('media_uploading_folder');				
		$path = $site_url.$upload_folder."/".$id."/";			
		$author_pic = get_the_author_meta('user_pic', $id);
		
		//get user url
		$user_url=$this->get_user_profile_permalink($id);
		
		if($size_type=="fixed" || $size_type=="")
		{
                        $dimension = "width:";
			$dimension_2 = "height:";
		}
		
		if($size_type=="dynamic" )
		{
			$dimension = "max-width:";
		
		}
		
		if($size!="")
		{
                    $pic_size = $dimension.$size."px".";".$dimension_2.$size."px";
		
		}
		
		
		
		if($pic_type=='avatar')
		{
			if ($author_pic  != '') 
			{
                                //echo $user_url;
                                $avatar_pic = $path.$author_pic;
				$avatar= '<a href="'.$user_url.'">'. '<img src="'.$avatar_pic.'" class="'.$pic_boder_type.'" style="'.$pic_size.' "   id="uultra-avatar-img-'.$id.'" /></a>';
			} else {		
					
                            $avatar= '<a href="'.$user_url.'">'. get_avatar($id,$size) .'</a>';
				
			
				
			}
		
		}elseif($pic_type=='mainpicture'){
			    
				//get user's main picture - medium size will be used to be displayed
			
			    $avatar_pic = $path.$author_pic;
				$avatar= '<a href="'.$user_url.'">'. '<img src="'.$avatar_pic.'" class="'.$pic_boder_type.'" style="'.$pic_size.' "   id="uultra-avatar-img-'.$id.'"/></a>';
		
		
		}
		return $avatar;
	}
        
        /* Get picture by ID, if you don't want it returned in formatted HTML */
	function get_user_pic_url( $id, $size, $pic_type=NULL, $pic_boder_type= NULL, $size_type=NULL ) 
	{
		
		 global  $xoouserultra;

		 require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		 
		$avatar = "";
		$pic_size = "";
		
		$upload_folder = $xoouserultra->get_option('media_uploading_folder');				
		$path = $site_url.$upload_folder."/".$id."/";			
		$author_pic = get_the_author_meta('user_pic', $id);
		
		//get user url
		$user_url=$this->get_user_profile_permalink($id);
		
		if($size_type=="fixed" || $size_type=="")
		{
                        $dimension = "width:";
			$dimension_2 = "height:";
		}
		
		if($size_type=="dynamic" )
		{
			$dimension = "max-width:";
		
		}
		
		if($size!="")
		{
                    $pic_size = $dimension.$size."px".";".$dimension_2.$size."px";
		
		}
		
		
		
		if($pic_type=='avatar')
		{
			if ($author_pic  != '') 
			{
                                //echo $user_url;
                                $avatar = $path.$author_pic;
				
			} else {		
					
                            $avatar= get_avatar_url($id,$size);
				
			
				
			}
		
		}elseif($pic_type=='mainpicture'){
			    
				//get user's main picture - medium size will be used to be displayed
			
			    $avatar = $path.$author_pic;
		
		
		}
		return $avatar;
	}
	
	public function avatar_uploader() 
	{
		
	   // Uploading functionality trigger:
	  // (Most of the code comes from media.php and handlers.js)
	      $template_dir = get_template_directory_uri();
?>
		
		<div id="uploadContainer" style="margin-top: 10px;">
			
			
			<!-- Uploader section -->
			<div id="uploaderSection" style="position: relative;">
				<div id="plupload-upload-ui-avatar" class="hide-if-no-js">
                
					<div id="drag-drop-area-avatar">
						<div class="drag-drop-inside">
							<p class="drag-drop-info"><?php	_e('Drop Avatar here', 'xoousers') ; ?></p>
							<p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
							<p class="drag-drop-buttons"><input id="plupload-browse-button-avatar" type="button" value="<?php esc_attr_e('Select Files'); ?>" class="button" /></p>
                            
                               <p class="drag-drop-buttons"><input id="btn-delete-user-avatar" type="button" value="<?php esc_attr_e('Remove Avatar'); ?>" class="button" /></p>
														
						</div>
                        
                        <div id="progressbar-avatar"></div>                 
                         <div id="symposium_filelist_avatar" class="cb"></div>
					</div>
				</div>
                
                 
			
			</div>
            
           
		</div>

		<?php
			
			$plupload_init = array(
				'runtimes'            => 'html5,silverlight,flash,html4',
				'browse_button'       => 'plupload-browse-button-avatar',
				'container'           => 'plupload-upload-ui-avatar',
				'drop_element'        => 'drag-drop-area-avatar',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'multi_selection'	  => false,
				'max_file_size'       => wp_max_upload_size().'b',
				//'max_file_size'       => get_option('drag-drop-filesize').'b',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				//'filters'             => array(array('title' => __('Allowed Files', $this->text_domain), 'extensions' => "jpg,png,gif,bmp,mp4,avi")),
				'filters'             => array(array('title' => __('Allowed Files', "xoousers"), 'extensions' => "jpg,png,gif,jpeg")),
				'multipart'           => true,
				'urlstream_upload'    => true,

				// Additional parameters:
				'multipart_params'    => array(
					'_ajax_nonce' => wp_create_nonce('photo-upload'),
					'action'      => 'ajax_upload_avatar' // The AJAX action name
					
				),
			);
			
			//print_r($plupload_init);

			// Apply filters to initiate plupload:
			$plupload_init = apply_filters('plupload_init', $plupload_init); ?>

			<script type="text/javascript">
			
				jQuery(document).ready(function($){
					
					// Create uploader and pass configuration:
					var uploader_avatar = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

					// Check for drag'n'drop functionality:
					uploader_avatar.bind('Init', function(up){
						
						var uploaddiv_avatar = $('#plupload-upload-ui-avatar');
						
						// Add classes and bind actions:
						if(up.features.dragdrop){
							uploaddiv_avatar.addClass('drag-drop');
							
							$('#drag-drop-area-avatar')
								.bind('dragover.wp-uploader', function(){ uploaddiv_avatar.addClass('drag-over'); })
								.bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv_avatar.removeClass('drag-over'); });

						} else{
							uploaddiv_avatar.removeClass('drag-drop');
							$('#drag-drop-area').unbind('.wp-uploader');
						}

					});

					
					// Init ////////////////////////////////////////////////////
					uploader_avatar.init(); 
					
					// Selected Files //////////////////////////////////////////
					uploader_avatar.bind('FilesAdded', function(up, files) {
						
						
						var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
						
						// Limit to one limit:
						if (files.length > 1){
							alert("<?php _e('You may only upload one image at a time!', 'xoousers'); ?>");
							return false;
						}
						
						// Remove extra files:
						if (up.files.length > 1){
							up.removeFile(uploader_avatar.files[0]);
							up.refresh();
						}
						
						// Loop through files:
						plupload.each(files, function(file){
							
							// Handle maximum size limit:
							if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
								alert("<?php _e('The file you selected exceeds the maximum filesize limit.', 'xoousers'); ?>");
								return false;
							}
						
						});
						
						jQuery.each(files, function(i, file) {
							jQuery('#symposium_filelist_avatar').append('<div class="addedFile" id="' + file.id + '">' + file.name + '</div>');
						});
						
						up.refresh(); 
						uploader_avatar.start();
						
					});
					
					// A new file was uploaded:
					uploader_avatar.bind('FileUploaded', function(up, file, response){
						
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {"action": "refresh_avatar"},
							
							success: function(data){
								
								$("#uu-backend-avatar-section").html(data);
								//jQuery("#uu-message-noti-id").slideDown();
								//setTimeout("hidde_noti('uu-message-noti-id')", 3000)	;
								
								
								}
						});
						
						
					
					});
					
					// Error Alert /////////////////////////////////////////////
					uploader_avatar.bind('Error', function(up, err) {
						alert("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : "") + "");
						up.refresh(); 
					});
					
					// Progress bar ////////////////////////////////////////////
					uploader_avatar.bind('UploadProgress', function(up, file) {
						
						var progressBarValue = up.total.percent;
						
						jQuery('#progressbar-avatar').fadeIn().progressbar({
							value: progressBarValue
						});
						
						jQuery('#progressbar-avatar').html('<span class="progressTooltip">' + up.total.percent + '%</span>');
					});
					
					// Close window after upload ///////////////////////////////
					uploader_avatar.bind('UploadComplete', function() {
						
						//jQuery('.uploader').fadeOut('slow');						
						jQuery('#progressbar-avatar').fadeIn().progressbar({
							value: 0
						});
						
						
					});
					
					
					
				});
				
					
			</script>
			
		<?php
	
	
	}
	
	function get_one_user_with_key($key)
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'xoouser_ultra_very_key',                    
			'meta_value' => $key,                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		 
		// Get the results//
		$users = $user_query->get_results();	
		
		if(count($users)>0)
		{
			foreach ($users as $user)
			{
				return $user;
			
			}
			
		
		}else{
			
			
			
		}
		
	
	}
	
	function get_user_with_key($key)
	{
		global $wpdb,  $xoouserultra;
		
		$args = array( 	
						
			'meta_key' => 'xoouser_ultra_very_key',                    
			'meta_value' => $key,                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		
		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		 
		// Get the results//
		$users = $user_query->get_results();	
		
		if(count($users)>0)
		{
			return true;
		
		}else{
			
			return false;
			
		}
		
	
	}
	
	function users_shortcodes( $args )
	{
		global  $wpdb,  $xoouserultra;
		
		
		extract($args);
		
		$page = (!empty($_GET['ultra-page'])) ? $_GET['ultra-page'] : 1;
		$offset = ( ($page -1) * $args['list_per_page'] );

		/* setup query params */
		//$query = $this->setup_query( $args );
		
		/* pagi stuff */
		$query['number'] = $args['list_per_page'];
		$query['offset'] = $offset;
		
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);
			
		
		$count_args = array_merge($query, array('number'=>99999999999));
		unset($count_args['offset']);
		
		$user_count_query = new WP_User_Query($count_args);

		if ($args['list_per_page']) {
		$user_count = $user_count_query->get_results();
		$total_users = $user_count ? count($user_count) : 1;
		$total_pages = ceil($total_users / $args['list_per_page']);
		}

		$wp_user_query = new WP_User_Query($query);
		
		if (! empty( $wp_user_query->results ))
			$big = 999999999; // need an unlikely integer
			$arr['paginate'] = paginate_links( array(
					'base'         => @add_query_arg('ultra-page','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('Previous','xoousers'),
					'next_text'    => __('Next','xoousers'),
					'type'         => 'plain',
				));
			$arr['users'] = $wp_user_query->results;
			
		return $arr;
	}
	
	
	function users( $args )
	{
		global  $wpdb,  $xoouserultra;
		$blog_id = get_current_blog_id();
		
		extract($args);
		
		
		$page = (!empty($_GET['ultra-page'])) ? $_GET['ultra-page'] : 1;		
		$offset = ( ($page -1) * $args['list_per_page'] );
		
		if(isset($_GET["usersultra_searchuser"]) && $_GET["usersultra_searchuser"] !="")
		{
			$key = $_GET["usersultra_searchuser"];
						
			$query['meta_query'] = array('relation' => 'AND' );			
			$query['meta_query'][] = array(
				'key' => 'display_name',
				'value' => $key,
				'compare' => 'LIKE'
			);	
			
		}
				
		$query['meta_query'][] = array(
				'key' => 'usersultra_account_status',
				'value' => 'active',
				'compare' => '='
			);		
				
		$query['number'] = $args['list_per_page'];
		$query['offset'] = $offset;
		$query['order' ] = $list_order;
		$query['orderby' ] = 'ID';
		

		$count_args = array_merge($query, array('number'=>99999999999));	
		
		unset($count_args['offset']);
		
		$user_count_query = new WP_User_Query($count_args);

		//calculates pages
		if ($args['list_per_page'])
		{
			$user_count = $user_count_query->get_results();
		    $total_users = $user_count ? count($user_count) : 1;
		    $total_pages = ceil($total_users / $args['list_per_page']);
		}
		

		$wp_user_query = new WP_User_Query($query);
		
		if (! empty( $wp_user_query->results ))
			$big = 999999999; // need an unlikely integer
			$arr['paginate'] = paginate_links( array(
					'base'         => @add_query_arg('ultra-page','%#%'),
					'total'        => $total_pages,
					'current'      => $page,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __('Previous','xoousers'),
					'next_text'    => __('Next','xoousers'),
					'type'         => 'plain',
				));
			$arr['users'] = $wp_user_query->results;
			
			$arr['total'] =$total_users;
			
		return $arr;
	}
	
	
	
	
	public function get_members_list($args)
	{
		global  $wpdb,  $xoouserultra;
						
		extract($args);
		
		$blog_id = get_current_blog_id();
		
		$query['meta_query'] = array('relation' => strtoupper($list_relation) );
		
		$query['meta_query'][] = array(
				'key' => 'userultra_verified',
				'value' => 1,
				'compare' => '='
			);
		

		//$query['orderby'] = $list_sortby;
		
		//$query['order'] = strtoupper($list_order); // asc to ASC
		
		$query['number'] = $list_per_page;	
				
			
		$wp_user_query = $xoouserultra->get_results($query);
		
		
		
		if (! empty( $wp_user_query->results ))
		{

			$arr['users'] = $wp_user_query->results;
			
			
		}
		if (isset($arr)) return $arr;
		
	}
	
	/*---->> Check if user is active before login  ****/
	
	function is_active($user_id) 
	{
		$checkuser = get_user_meta($user_id, 'usersultra_account_status', true);
		
		if ($checkuser == 'active' || $checkuser == '') //this is a tweak for already members
		{
			return true;
		
		}else{
			
			return false;
		
		}			
		
	}
	
	/*---->> Check if user is pending activation by admin   ****/
	function get_status($user_id) 
	{
		$status ="";
		$checkuser = get_user_meta($user_id, 'usersultra_account_status', true);
		
		if ($checkuser == 'pending') 
		{
			$status = "Pending";
			
		}elseif($checkuser == 'pending_admin'){
			
			$status = "Pending Admin";
		
		}elseif($checkuser == 'active' || $checkuser == ''){
			
			$status = "Active";
		
		}
		
		 
			
		return $status;
	}
	
	/*---->> Check if user is pending activation by admin   ****/
	function is_pending($user_id) 
	{
		$checkuser = get_user_meta($user_id, 'usersultra_account_status', true);
		if ($checkuser == 'pending' || $checkuser == 'pending_admin')
			return true;
		return false;
	}
	
	/*---->> Activate user    ****/
	function activate($user_id, $user_login = null)
	{
		if ($user_login != '')
		{
			$user = get_user_by('login', $user_login);
			$user_id = $user->ID;
		}
		delete_user_meta($user_id, 'usersultra_account_verify');
		update_user_meta($user_id, 'usersultra_account_status', 'active');
		
		$password = get_user_meta($user_id, 'usersultra_pending_pass', true);
		$form = get_user_meta($user_id, 'usersultra_pending_form', true);
		
		//notify user by email
		
		delete_user_meta($user_id, 'usersultra_pending_pass');
		delete_user_meta($user_id, 'usersultra_pending_form');
	}

	
	
	
	/******************************************
	Get user ID only by query var
	******************************************/
	public function get_member_by_queryvar_from_id()
	{
		$arg = get_query_var('uu_username');
		if ( $arg ) 
		{
			$user = $this->get_member_by( $arg );
			return $user->ID;
		}
	}
	
	public function get_custom_user_meta ($meta, $user_id)
	{
		return get_user_meta( $user_id, $meta, true);
		
	}
	
	
	public function  get_profile_info ($user_id)	
	{
		
		$array = get_option('usersultra_profile_fields');

		foreach($array as $key=>$field) 
		{
		    // Optimized condition and added strict conditions 
		    $exclude_array = array('user_pass', 'user_pass_confirm', 'user_email');
		    if(isset($field['meta']) && in_array($field['meta'], $exclude_array))
		    {
		        unset($array[$key]);
		    }
		}
		
		
		$i_array_end = end($array);
		
		if(isset($i_array_end['position']))
		{
		    $array_end = $i_array_end['position'];
		    if ($array[$array_end]['type'] == 'separator') {
		        unset($array[$array_end]);
		    }
		}
		
		
		$html .= '';
		
	
		foreach($array as $key => $field) 
		{

			extract($field);
			
			
			if(!isset($private))
			    $private = 0;
			
			if(!isset($show_in_widget))
			    $show_in_widget = 1;
				
			
			
			/* Fieldset seperator */
			if ( $type == 'separator' && $deleted == 0 && $private == 0  && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
			{
				$html .= '<div class="uultra-profile-seperator">'.$name.'</div>';
			}
			
			if ( $type == 'usermeta' && $deleted == 0 && $private == 0  && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1)			
			{				
				/* Show the label */
				if (isset($array[$key]['name']) && $name)
				{
					$html .= ' <span class="data-a">'.$name.':</span><span class="data-b">'.$this->get_custom_user_meta( $meta, $user_id).'</span> ';
				}
			
			}
				 	
				
			
		}
		
		$html .= '';
		return $html;
		
	}
	
	
	
	
}

$key = "userpanel";
$this->{$key} = new XooUserUser();