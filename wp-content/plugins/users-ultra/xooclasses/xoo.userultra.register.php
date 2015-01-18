<?php
class XooUserRegister {

	function __construct() {
		
		/*Form is fired*/
	    
		if (isset($_POST['xoouserultra-register-form'])) {
			
			/* Prepare array of fields */
			$this->uultra_prepare_request( $_POST );
       			
			/* Validate, get errors, etc before we create account */
			$this->uultra_handle_errors();
			
			/* Create account */
			$this->uultra_create_account();
				
		}

	}
	
	/*Prepare user meta*/
	function uultra_prepare_request ($array ) 
	{
		foreach($array as $k => $v) 
		{
			if ($k == 'usersultra-register') continue;
			$this->usermeta[$k] = $v;
		}
		return $this->usermeta;
	}
	
	/*Handle/return any errors*/
	function uultra_handle_errors() 
	{
	    global $usersultra_captcha_loader;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		
		if(get_option('users_can_register') == '1')
		{
		    foreach($this->usermeta as $key => $value) {
		    
                        
		        /* Validate username */
		        if ($key == 'user_login') {
		            if (esc_attr($value) == '') {
		                $this->errors[] = __('Please enter an emial address for registration so we know who you are.','xoousers');
		            } elseif (username_exists($value)) {
		                $this->errors[] = __('<strong>ERROR:</strong> This username is already registered. Please choose another one.','xoousers');
		            }
		        }
		    
		        /* Validate email */
		        if ($key == 'user_email') 
				{
		            if (esc_attr($value) == '') 
					{
		                $this->errors[] = __('<strong>ERROR:</strong> Please type your e-mail address.','xoousers');
		            } elseif (!is_email($value)) 
					{
		                $this->errors[] = __('Whoops! That doesn\'t really look like an email address.','xoousers');
		            } elseif (email_exists($value)) 
					{
		                $this->errors[] = __('This email address is already registered, you sure you didn\'t mean to sign in instead?','xoousers');
		            }
		        }
		    
		    }
			
			/* Validate passowrd */
		     if ($this->usermeta["user_pass"]=="") 
			 {
				 //$this->errors[] = __('<strong>ERROR:</strong> Please type your password.','xoousers');           
				
		     }
			 
			 if ($this->usermeta["user_pass"]!= $this->usermeta["user_pass_confirm"]) 
			 {
				 $this->errors[] = __('You might want to check those password boxes, they don\'t seem to match','xoousers');           
				
		     }
		    
		    if(!is_in_post('no_captcha','yes'))
		    {
		        if(!$usersultra_captcha_loader->validate_captcha(post_value('captcha_plugin')))
		        {
		            $this->errors[] = __('<strong>ERROR:</strong> Please complete Captcha Test first.','xoousers');
		        }
		    } 
		}
		else
		{
		    $this->errors[] = __('<strong>ERROR:</strong> Registration is disabled for this site.','xoousers');
		}
		
		
		
	}
	
	// File upload handler:
	function upload_front_avatar($o_id)
	{
		global $xoouserultra;
		global $wpdb;
		
		require_once(ABSPATH . 'wp-includes/link-template.php');
		$site_url = site_url()."/";
		
				
		/// Upload file using Wordpress functions:
		$file = $_FILES['user_pic'];
		
		
		$original_max_width = $xoouserultra->get_option('media_avatar_width'); 
        $original_max_height =$xoouserultra->get_option('media_avatar_height'); 
		
		if($original_max_width=="" || $original_max_height==80)
		{			
			$original_max_width = 100;			
			$original_max_height = 100;
			
		}
		
				
		$info = pathinfo($file['name']);
		$real_name = $file['name'];
        $ext = $info['extension'];
		$ext=strtolower($ext);
		
		$rand = $this->genRandomString();
		
		$rand_name = "avatar_".$rand."_".session_id()."_".time(); 		
		$path_pics = ABSPATH.$xoouserultra->get_option('media_uploading_folder');
			
			
		if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif') 
		{
			if($o_id != '')
			{
				
				   if(!is_dir($path_pics."/".$o_id."")) {
						$this->CreateDir($path_pics."/".$o_id);								   
					}					
										
					$pathBig = $path_pics."/".$o_id."/".$rand_name.".".$ext;
					
					if (copy($file['tmp_name'], $pathBig)) 
					{
						$upload_folder = $xoouserultra->get_option('media_uploading_folder');				
						$path = $site_url.$upload_folder."/".$o_id."/";
						
						//check max width
												
						list( $source_width, $source_height, $source_type ) = getimagesize($pathBig);
						
						if($source_width > $original_max_width) 
						{
							//resize
							if ($this->createthumb($pathBig, $pathBig, $original_max_width, $original_max_height,$ext)) 
							{
								$old = umask(0);
								chmod($pathBig, 0755);
								umask($old);
														
							}
						
						
						}
						
						
						
						$new_avatar = $rand_name.".".$ext;						
						$new_avatar_url = $path.$rand_name.".".$ext;					
						
						//check if there is another avatar						
						$user_pic = get_user_meta($o_id, 'user_pic', true);						
						
						if ( $user_pic!="" )
			            {
							//there is a pending avatar - delete avatar																					
									
							$path_pics = $site_url.$xoouserultra->get_option('media_uploading_folder');							
							$path_avatar = $path_pics."/".$o_id."/".$user_pic;					
														
							//delete								
							if(file_exists($path_avatar))
							{
								unlink($path_avatar);
							}
							
							//update meta
							update_user_meta($o_id, 'user_pic', $new_avatar);
							
						}else{
							
							//update meta
							update_user_meta($o_id, 'user_pic', $new_avatar);
												
						
						}
						
						//update user meta
						
						
						
					}
									
					
			     }  		
			
			  
			
        } // image type
		
		// Create response array:
		$uploadResponse = array('image' => $new_avatar_url);
		
	}
	
	 public function createthumb($imagen,$newImage,$toWidth, $toHeight,$extorig)
	{             				
				
                 $ext=strtolower($extorig);
                 switch($ext)
                  {
                   case 'png' : $img = imagecreatefrompng($imagen);
                   break;
                   case 'jpg' : $img = imagecreatefromjpeg($imagen);
                   break;
                   case 'jpeg' : $img = imagecreatefromjpeg($imagen);
                   break;
                   case 'gif' : $img = imagecreatefromgif($imagen);
                   break;
                  }

               
                $width = imagesx($img);
                $height = imagesy($img);  
				

				
				$xscale=$width/$toWidth;
				$yscale=$height/$toHeight;
				
				// Recalculate new size with default ratio
				if ($yscale>$xscale){
					$new_w = round($width * (1/$yscale));
					$new_h = round($height * (1/$yscale));
				}
				else {
					$new_w = round($width * (1/$xscale));
					$new_h = round($height * (1/$xscale));
				}
				
				
				
				if($width < $toWidth)  {
					
					$new_w = $width;	
				
				//}else {					
					//$new_w = $current_w;			
				
				}
				
				if($height < $toHeight)  {
					
					$new_h = $height;	
				
				//}else {					
					//$new_h = $current_h;			
				
				}
			
				
				
				
                $dst_img = imagecreatetruecolor($new_w,$new_h);
				
				/* fix PNG transparency issues */                       
				imagefill($dst_img, 0, 0, IMG_COLOR_TRANSPARENT);         
				imagesavealpha($dst_img, true);      
				imagealphablending($dst_img, true); 				
                imagecopyresampled($dst_img,$img,0,0,0,0,$new_w,$new_h,imagesx($img),imagesy($img));
               
                
				
				 switch($ext)
                  {
                   case 'png' : $img = imagepng($dst_img,"$newImage",9);
                   break;
                   case 'jpg' : $img = imagejpeg($dst_img,"$newImage",100);
                   break;
                   case 'jpeg' : $img = imagejpeg($dst_img,"$newImage",100);
                   break;
                   case 'gif' : $img = imagegif($dst_img,"$newImage");
                   break;
                  }
				  
				   imagedestroy($dst_img);	
				
				
				
                return true;

        }
	
	public function CreateDir($root){

               if (is_dir($root))        {

                        $retorno = "0";
                }else{

                        $oldumask = umask(0);
                        $valrRet = mkdir($root,0777);
                        umask($oldumask);


                        $retorno = "1";
                }

    }
	
	public function genRandomString() 
	{
		$length = 5;
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";
		
		$real_string_legnth = strlen($characters) ;
		//$real_string_legnth = $real_string_legnth– 1;
		$string="ID";
		
		for ($p = 0; $p < $length; $p++)
		{
			$string .= $characters[mt_rand(0, $real_string_legnth-1)];
		}
		
		return strtolower($string);
	}
	
	
	/*Create user*/
	function uultra_create_account() 
	{
		
		global $xoouserultra;
		session_start();
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
			
			/* Create profile when there is no error */
			if (!isset($this->errors)) 
			{
				
				/* Create account, update user meta */
				$sanitized_user_login = sanitize_user($_POST['user_login']);
                                
                                /*Check if we have to use email as*/				
				if($xoouserultra->get_option('allow_registering_only_with_email')=='yes')
				{
					$sanitized_user_login = sanitize_user($_POST['user_email']);
								
				}
				
				/* Get password */
				if (isset($_POST['user_pass']) && $_POST['user_pass'] != '') 
				{
					$user_pass = $_POST['user_pass'];
				} else {
					$user_pass = wp_generate_password( 12, false);
				}
				
                                if (isset($_POST['user_first']) && $POST['user_pass'] != ''){
                                    $user_first_name = $_POST['user_first'];
                                }
                                
				/* We create the New user */
				$user_id = wp_create_user( $sanitized_user_login, $user_pass, $_POST['user_email'] );
				
				if ( ! $user_id ) 
				{

				}else{
					
					/*We've got a valid user id then let's create the meta informaion*/						
					foreach($this->usermeta as $key => $value) 
					{
						
						update_user_meta($user_id, $key, esc_attr($value));

						/* update core fields - email, url, pass */
						if ( in_array( $key, array('user_email', 'user_url', 'display_name') ) )
						{
							wp_update_user( array('ID' => $user_id, $key => esc_attr($value)) );
						}
						
					}
					
					//update user pic
					if(isset($_FILES['user_pic']))
					{
						$this->upload_front_avatar($user_id );
							
					}
					
					//set account status					
					$xoouserultra->login->user_account_status($user_id);
					
					$verify_key = $xoouserultra->login->get_unique_verify_account_id();					
					update_user_meta ($user_id, 'xoouser_ultra_very_key', $verify_key);							
					
					 //mailchimp					 
					 if(isset($_POST["uultra-mailchimp-confirmation"]) && $_POST["uultra-mailchimp-confirmation"]==1)
					 {
						 $list_id =  $xoouserultra->get_option('mailchimp_list_id');					 
						 $xoouserultra->subscribe->mailchimp_subscribe($user_id, $list_id);
						 update_user_meta ($user_id, 'xoouser_mailchimp', 1);	
						 						
					
					 }
					
					
					
				}
				

				//check if it's a paid sign up				
				
				if($xoouserultra->get_option('registration_rules')==4)
				{
					//this is a paid sign up					
										
					//get package
					$package = $xoouserultra->paypal->get_package($_POST["usersultra_package_id"]);
					$amount = $package->package_amount;
					$p_name = $package->package_name;
					$package_id = $package->package_id;
					
					//payment Method
					$payment_method = 'paypal';
					
					//create transaction
					$transaction_key = session_id()."_".time();
					
					$order_data = array('user_id' => $user_id,
					 'transaction_key' => $transaction_key,
					 'amount' => $amount,
					 'order_package_id' => $package_id ,
					 'product_name' => $p_name ,
					 'status' => 'pending',
					 'method' => $payment_method); 
					 
					if( $amount > 0)
					 {
						 $xoouserultra->order->create_order($order_data);
						
					 }	
					
										
					
					//update status
					 update_user_meta ($user_id, 'usersultra_account_status', 'pending_payment');
					 
					 //store tempassword
					 update_user_meta ($user_id, 'usersultra_temp_password', $user_pass);
					 
					 //package 
					 update_user_meta ($user_id, 'usersultra_user_package_id', $package_id);
					 
					 //mailchimp					 
					 if(isset($_POST["uultra-mailchimp-confirmation"]) && $_POST["uultra-mailchimp-confirmation"]==1)
					 {						
						 //do mailchimp stuff	
						 $list_id =  $xoouserultra->get_option('mailchimp_list_id');					 
						 $xoouserultra->subscribe->mailchimp_subscribe($user_id, $list_id);	
						 update_user_meta ($user_id, 'xoouser_mailchimp', 1);					
					
					  }
					 
					 
					 
					 //set expiration date
					 // update_user_meta ($user_id, 'usersultra_account_creation_date', 'pending_payment');
					 // update_user_meta ($user_id, 'usersultra_account_expiration_date', 'pending_payment');
					 
					 if($payment_method=="paypal" && $amount > 0)
					 {
						  $ipn = $xoouserultra->paypal->get_ipn_link($order_data);
						  
						  //redirect to paypal
						  header("Location: $ipn");exit;						  
						  exit;					  
						 
					 }else{						 
						 
						 //paid membership but free plan selected						 
						 //notify depending on status
					      $xoouserultra->login->user_account_notify($user_id, $_POST['user_email'],  $sanitized_user_login, $user_pass);
						  
						  //check if requires admin approvation
						  
						  if($package->package_approvation=="yes")
						  {
							  
							  
							 
						  }else{
							  
							  //this package doesn't require moderation
							   update_user_meta ($user_id, 'usersultra_account_status', 'active');
							  //notify user					   
		 					   $xoouserultra->messaging->welcome_email($_POST['user_email'], $user_first_name, $user_pass);
							  
							   //login
							   $secure = "";		
							  //already exists then we log in
							  wp_set_auth_cookie( $user_id, true, $secure );	
							  //redirect
							  $xoouserultra->login->login_registration_afterlogin();
							  
						  
						  }
						 
						 
					 }
					 
				
				}else{
					
					//this is not a paid sign up
					
					//notify depending on status
					$xoouserultra->login->user_account_notify($user_id, $_POST['user_email'],  $sanitized_user_login, $user_pass);
										
				
				}	
				
				
				 //check if login automatically
				  $activation_type= $xoouserultra->get_option('registration_rules');
				  
				  if($activation_type==1)
				  {					  					  
					  //login
					   $secure = "";		
					  //already exists then we log in
					  wp_set_auth_cookie( $user_id, true, $secure );	
					  //redirect
		              $xoouserultra->login->login_registration_afterlogin();						
	  
	              } 
				
				
				
				
			} //end error link
			
	}
	
	/*Get errors display*/
	function get_errors() {
		global $xoouserultra;
		$display = null;
		if (isset($this->errors) && count($this->errors)>0) 
		{
		$display .= '<div class="usersultra-errors">';
			foreach($this->errors as $newError) {
				
				$display .= '<span class="usersultra-error usersultra-error-block"><i class="usersultra-icon-remove"></i>'.$newError.'</span>';
			
			}
		$display .= '</div>';
		} else {
		
			$this->registered = 1;
			$display .= '<div class="usersultra-success"><span><i class="usersultra-icon-ok"></i>'.__('Registration successful. Please check your email.','xoousers').'</span></div>';
			
			if (isset($_POST['redirect_to'])) {
				wp_redirect( $_POST['redirect_to'] );
			}
			
		}
		return $display;
	}

}

$key = "register";
$this->{$key} = new XooUserRegister();