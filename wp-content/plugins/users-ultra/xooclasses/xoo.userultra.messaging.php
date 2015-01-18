<?php
class XooMessaging extends XooUserUltraCommon 
{
	var $mHeader;
	var $mEmailPlainHTML;
	var $mHeaderSentFromName;
	var $mHeaderSentFromEmail;
	var $mCompanyName;
	

	function __construct() 
	{
		$this->setContentType();
		$this->setFromEmails();
				
		$this->set_headers();	
		
	}
	
	function setFromEmails() 
	{
		global $xoouserultra;
			
		$from_name =  $this->get_option('messaging_send_from_name'); 
		$from_email = $this->get_option('messaging_send_from_email'); 	
		if ($from_email=="")
		{
			$from_email =get_option('admin_email');
			
		}		
		$this->mHeaderSentFromName=$from_name;
		$this->mHeaderSentFromEmail=$from_email;	
		
    }
	
	function setContentType() 
	{
			
		$type = 0; 			
		
		if($type==0)
		{
			$this->mEmailPlainHTML="text/plain";		
		}
		
		if($type==1)
		{
			$this->mEmailPlainHTML="text/html";		
		}
    }
	
	public function set_headers() {   			
		//Make Headers aminnistrators
		$header ="MIME-Version: 1.0\n"; 
		$header .= "Content-type: ".$this->mEmailPlainHTML."; charset=UTF-81\n"; 	
		$header .= "From: ".$this->mHeaderSentFromName." <".$this->mHeaderSentFromEmail.">\n";	
		$header .= "Organization: ".$this->mCompanyName." \n";
		$header .=" X-Mailer: PHP/". phpversion()."\n";		
		$this->mHeader = $header;		
    }
	
	
	public function  send ($to, $subject, $message)
	{
		global $xoouserultra;	
		require_once(ABSPATH . 'wp-includes/pluggable.php');	
		
		wp_mail( $to , $subject, $message, $this->mHeader);
					
		
	}
	
	//--- Automatic Activation	
	public function  welcome_email($u_email, $user_name, $user_pass)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
                //get welcome email
		$template_client =stripslashes($this->get_option('messaging_welcome_email_client'));
		$template_admim = stripslashes($this->get_option('messaging_welcome_email_client_admin'));
		
		$login_url =site_url("/");
		
		$subject = __('Welcome to Carnaby West','xoousers');
		$subject_admin = __('New Registration','xoousers');
		
		$template_client = str_replace("{{userl_ultra_login_url}}", $login_url,  $template_client);				
		$template_client = str_replace("{{userultra_user_email}}", $u_email,  $template_client);
		$template_client = str_replace("{{userultra_user_name}}", $user_name,  $template_client);
		$template_client = str_replace("{{userultra_pass}}", $user_pass,  $template_client);
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);
		
		//admin
		$template_admim = str_replace("{{userultra_user_email}}", $u_email,  $template_admim);
		$template_admim = str_replace("{{userultra_user_name}}", $user_login,  $template_admim);
		$template_admim = str_replace("{{userultra_admin_email}}", $admin_email,  $template_admim);		
		
		//send to client
		$this->send($u_email, $subject, $template_client);
		
		//send to admin		
		$this->send($admin_email, $subject_admin, $template_admim);
		
					
		
	}
	
	//--- Link Activation Resend	
	public function  re_send_activation_link($u_email, $user_login, $activation_link)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
		
		$subject = __('Verify Your Account','xoousers');
		
		//get welcome email
		$template_client =stripslashes($this->get_option('messaging_re_send_activation_link'));
		
		$login_url =site_url("/");
		
		$template_client = str_replace("{{user_ultra_activation_url}}", $activation_link,  $template_client);
		$template_client = str_replace("{{userultra_user_email}}", $u_email,  $template_client);
		$template_client = str_replace("{{userultra_user_name}}", $user_login,  $template_client);
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);
		
		
		$this->send($u_email, $subject, $template_client);
		
	}
	
		//--- Admin Activation	
	public function  welcome_email_with_admin_activation($u_email, $user_login, $user_pass,  $activation_link)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
		
		$subject = __('Account Verification','xoousers');
		$subject_admin = __('New Account To Approve','xoousers');
		
		//get welcome email
		$template_client =stripslashes($this->get_option('messaging_admin_moderation_user'));
		$template_admim = stripslashes($this->get_option('messaging_admin_moderation_admin'));
		
		$login_url =site_url("/");
		
		$template_client = str_replace("{{userultra_user_name}}", $user_login,  $template_client);
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);
		
		//admin
		$template_admim = str_replace("{{userultra_user_email}}", $u_email,  $template_admim);
		$template_admim = str_replace("{{userultra_user_name}}", $user_login,  $template_admim);
		$template_admim = str_replace("{{userultra_admin_email}}", $admin_email,  $template_admim);	
				
		
		//send user
		$this->send($u_email, $subject, $template_client);
		
		//send to admin		
		$this->send($admin_email, $subject_admin, $template_admim);
		
	}
	
	
	//--- Link Activation	
	public function  welcome_email_with_activation($u_email, $user_login, $user_pass,  $activation_link)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
		
		$subject = __('Welcome to Carnaby West','xoousers');
		$subject_admin = __('New Account To Verify','xoousers');
		
		//get welcome email
		$template_client =stripslashes($this->get_option('messaging_welcome_email_with_activation_client'));
		$template_admim = stripslashes($this->get_option('messaging_welcome_email_with_activation_admin'));
		
		$login_url =site_url("/");
		
		$template_client = str_replace("{{user_ultra_activation_url}}", $activation_link,  $template_client);
		$template_client = str_replace("{{userultra_user_email}}", $u_email,  $template_client);
		$template_client = str_replace("{{userultra_user_name}}", $user_login,  $template_client);
		$template_client = str_replace("{{userultra_pass}}", $user_pass,  $template_client);
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);
		
		//admin
		$template_admim = str_replace("{{userultra_user_email}}", $u_email,  $template_admim);
		$template_admim = str_replace("{{userultra_user_name}}", $user_login,  $template_admim);
		$template_admim = str_replace("{{userultra_admin_email}}", $admin_email,  $template_admim);	
				
		
		$this->send($u_email, $subject, $template_client);
		
		//send to admin		
		$this->send($admin_email, $subject_admin, $template_admim);
		
		
		
	}
	
	//---  Activation	
	public function  confirm_activation($u_email, $user_login)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		
		$admin_email =get_option('admin_email'); 
		
		//get welcome email
		$template_client =stripslashes($this->get_option('admin_account_active_message_body'));
		
		$login_url =site_url("/");
		
		$subject = __('Account Activation','xoousers');
		
		
		$template_client = str_replace("{{userl_ultra_login_url}}", $login_url,  $template_client);				
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);		
		
		$this->send($u_email, $subject, $template_client);
		
					
		
	}
	
	//---  Verification Sucess	
	public function  confirm_verification_sucess($u_email)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		
		$admin_email =get_option('admin_email'); 
		
		//get welcome email
		$template_client =stripslashes($this->get_option('account_verified_sucess_message_body'));
		
		$login_url =site_url("/");
		
		$subject = __('Account Verified','xoousers');
		
		
		$template_client = str_replace("{{userl_ultra_login_url}}", $login_url,  $template_client);				
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);		
                $template_client = str_replace("{{userultra_user_name}}", $user_nick,  $template_client);		
		
		$this->send($u_email, $subject, $template_client);
		
					
		
	}
	
	//---  Deny	
	public function  deny_activation($u_email, $user_login)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		
		$admin_email =get_option('admin_email'); 
		
		//get welcome email
		$template_client =stripslashes($this->get_option('admin_account_deny_message_body'));
		
		$login_url =site_url("/");
		
		$subject = __('Account Activation Deny','xoousers');		
					
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);		
		
		$this->send($u_email, $subject, $template_client);
		
					
		
	}
	
	//--- Paid Activation	
	public function  welcome_email_paid($u_email, $user_login, $user_pass)
	{
		global $xoouserultra;
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
		
		//get welcome email
		$template_client =stripslashes($this->get_option('messaging_welcome_email_client'));
		$template_admim = stripslashes($this->get_option('messaging_welcome_email_client_admin'));
		
		$login_url =site_url("/");
		
		$subject = __('Registration Information','xoousers');
		$subject_admin = __('New Paid Subscrition','xoousers');
		
		$template_client = str_replace("{{userl_ultra_login_url}}", $login_url,  $template_client);				
		$template_client = str_replace("{{userultra_user_email}}", $u_email,  $template_client);
		$template_client = str_replace("{{userultra_user_name}}", $user_login,  $template_client);
		$template_client = str_replace("{{userultra_pass}}", $user_pass,  $template_client);
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);		
		
		$this->send($u_email, $subject, $template_client);
		
		//send admin email		
		$this->send($u_email, $subject_admin, $template_admim);
		
					
		
	}
	
	//--- Email Activation	
	public function  welcome_email_link_activation($u_email, $user_login, $user_pass)
	{
		global $xoouserultra;
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
		//get welcome email
		$template_client =stripslashes($this->get_option('messaging_welcome_email_client'));
		$template_admim = stripslashes($this->get_option('messaging_welcome_email_client_admin'));
		
		$login_url =site_url("/");
		
		$subject = __('Welcome to Carnaby West ','xoousers');
		
		$template_client = str_replace("{{userl_ultra_login_url}}", $login_url,  $template_client);				
		$template_client = str_replace("{{userultra_user_email}}", $u_email,  $template_client);
		$template_client = str_replace("{{userultra_user_name}}", $user_login,  $template_client);
		$template_client = str_replace("{{userultra_pass}}", $user_pass,  $template_client);
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);		
		
		$this->send($u_email, $subject, $template_client);
					
		
	}
	
	//--- Private Message to User	
	public function  send_private_message_user($receiver, $sender_first, $sender_last, $uu_subject, $uu_message)
	{
		global $xoouserultra;
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
		$u_email = $receiver->user_email;
		
		$template_client =stripslashes($this->get_option('messaging_user_pm'));
		
		$blogname  = $this->get_option('messaging_send_from_name');
		
		$login_url =site_url("/");		
		$sender_nick = $sender_first . " ". substr($sender_last, 0, 1). ".";
		
		$subject = __($sender_nick.' has sent you a message','xoousers');
		
		$template_client = str_replace("{{userultra_user_name}}", $sender_nick,  $template_client);
		$template_client = str_replace("{{user_ultra_blog_name}}", $blogname,  $template_client);
		$template_client = str_replace("{{userultra_pm_subject}}", $uu_subject,  $template_client);
		$template_client = str_replace("{{userultra_pm_message}}", stripslashes($uu_message),  $template_client);
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);		
		
		$this->send($u_email, $subject, $template_client);
					
		
	}
	
	
		//--- Send Friend Request	
	public function  send_friend_request($receiver, $sender)
	{
		global $xoouserultra;
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
		$u_email = $receiver->user_email;
		
		$template_client =stripslashes($this->get_option('message_friend_request'));		
		$blogname  = $this->get_option('messaging_send_from_name');
		
		$login_url =site_url("/");				
		
		$subject = __('New Friend Request','xoousers');
		
		$template_client = str_replace("{{user_ultra_blog_name}}", $blogname,  $template_client);
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);			
		
		$this->send($u_email, $subject, $template_client);
					
		
	}
		//--- Reset Link	
	public function  send_reset_link($receiver, $link)
	{
		global $xoouserultra;
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
		$u_email = $receiver->user_email;
		
		$template_client =stripslashes($this->get_option('reset_lik_message_body'));
		
		$blogname  = $this->get_option('messaging_send_from_name');
		
		
		$subject = __('Reset Your Password','xoousers');
		
		$template_client = str_replace("{{userultra_reset_link}}", $link,  $template_client);
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);		
		
		$this->send($u_email, $subject, $template_client);	
				
		
	}
	
	//--- confirm password reset	
	public function  send_new_password_to_user($u_email, $user_login, $user_pass)
	{
		global $xoouserultra;
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
		
		$subject = __('Password Reset Confirmation','xoousers');
		
		//get welcome email
		$template_client =stripslashes($this->get_option('password_reset_confirmation'));
		
		$login_url =site_url("/");
		
		$template_client = str_replace("{{userl_ultra_login_url}}", $login_url,  $template_client);		
		$template_client = str_replace("{{userultra_user_name}}", $user_login,  $template_client);		
		$template_client = str_replace("{{userultra_user_email}}", $u_email,  $template_client);
		$template_client = str_replace("{{userultra_pass}}", $user_pass,  $template_client);
		$template_client = str_replace("{{userultra_admin_email}}", $admin_email,  $template_client);
		
		
		$this->send($u_email, $subject, $template_client);
		
		
	}
	
	
	public function  paypal_ipn_debug( $message)
	{
		global $xoouserultra;
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH . 'wp-includes/link-template.php');
		
		$admin_email =get_option('admin_email'); 
		
		
		
		$this->send($admin_email, "IPN notification", $message);
					
		
	}
	
	
	

}
$key = "messaging";
$this->{$key} = new XooMessaging();