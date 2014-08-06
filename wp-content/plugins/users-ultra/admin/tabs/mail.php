<h3><?php _e('Notifications Settings','xoousers'); ?></h3>
<form method="post" action="">
<input type="hidden" name="update_settings" />

<div class="user-ultra-sect ">
  <h3><?php _e('Custom Messages','xoousers'); ?></h3>
  
  <p><?php _e('This message will be displayed in the User Panel Controls','xoousers'); ?></p>
  
   <table class="form-table">
<?php 

$this->create_plugin_setting(
        'input',
        'messaging_private_all_users',
        __('Message To Display','xoousers'),array(),
        __('This message will be displayed in the User Panel Controls','xoousers'),
        __('This message will be displayed in the User Panel Controls','xoousers')
);

?>
  
 
 </table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('Welcome Email Address','xoousers'); ?></h3>
  
  <p><?php _e('This is the welcome email that is sent to the client when registering a new account','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
        'input',
        'messaging_send_from_name',
        __('Send From Name','xoousers'),array(),
        __('Enter the your name or companay name here.','xoousers'),
        __('Enter the your name or companay name here.','xoousers')
);

$this->create_plugin_setting(
        'input',
        'messaging_send_from_email',
        __('Send From Email','xoousers'),array(),
        __('Enter the email address to be used when sending emails.','xoousers'),
        __('Enter the email address to be used when sending emails.','xoousers')
);

$this->create_plugin_setting(
        'textarea',
        'messaging_welcome_email_client',
        __('Client Welcome Message','xoousers'),array(),
        __('This message will be sent to the user.','xoousers'),
        __('This message will be sent to the user.','xoousers')
);

$this->create_plugin_setting(
        'textarea',
        'messaging_welcome_email_with_activation_client',
        __('Account Activation Message','xoousers'),array(),
        __('This message will be sent to the user if they need to activate the account by using the activation link.','xoousers'),
        __('This message will be sent to the user if they need to activate the account by using the activation link.','xoousers')
);

$this->create_plugin_setting(
        'textarea',
        'messaging_re_send_activation_link',
        __('Resend Activation Link','xoousers'),array(),
        __('This message will be sent to the user when clicking the re-send activation option.','xoousers'),
        __('This message will be sent to the user when clicking the re-send activation option.','xoousers')
);

$this->create_plugin_setting(
        'textarea',
        'account_verified_sucess_message_body',
        __('Account Verified Message','xoousers'),array(),
        __('This message will be sent to the users when they verify their accounts.','xoousers'),
        __('This message will be sent to the users when they verify their accounts.','xoousers')
);

$this->create_plugin_setting(
        'textarea',
        'password_reset_confirmation',
        __('Password Changed Confirmation','xoousers'),array(),
        __('This message will be sent to the user when the password has been reset.','xoousers'),
        __('This message will be sent to the user when the password has been reset.','xoousers')
);







$this->create_plugin_setting(
        'textarea',
        'messaging_welcome_email_client_admin',
        __('Admin New User Message','xoousers'),array(),
        __('This message will be sent to admin.','xoousers'),
        __('This message will be sent to admin.','xoousers')
		
);

$this->create_plugin_setting(
        'textarea',
        'messaging_welcome_email_with_activation_admin',
        __('Admin Pending Activation Message','xoousers'),array(),
        __('This message will be sent to admin if the user needs manual activation.','xoousers'),
        __('This message will be sent to admin if the user needs manual activation.','xoousers')
		
);


$this->create_plugin_setting(
        'textarea',
        'messaging_admin_moderation_user',
        __('Client Admin Approbation  Email','xoousers'),array(),
        __('This message will be sent to the user if the account needs to be approved by the admin.','xoousers'),
        __('This message will be sent to the user if the account needs to be approved by the admin.','xoousers')
);
$this->create_plugin_setting(
        'textarea',
        'messaging_admin_moderation_admin',
        __('Admin New User Approbation  Email','xoousers'),array(),
        __('This message will be sent to the admin if an account needs to be approved manually.','xoousers'),
        __('This message will be sent to the admin if an account needs to be approved manually.','xoousers')
);




$this->create_plugin_setting(
        'textarea',
        'messaging_user_pm',
        __('User Private Message','xoousers'),array(),
        __('This message will be sent to users when other users send a private message.','xoousers'),
        __('This message will be sent to users when other users send a private message.','xoousers')
		
);

$this->create_plugin_setting(
        'textarea',
        'message_friend_request',
        __('Friend Request','xoousers'),array(),
        __('This message is sent to the users when a friend request is sent','xoousers'),
        __('This message is sent to the users when a friend request is sent','xoousers')
		
);




$this->create_plugin_setting(
        'textarea',
        'reset_lik_message_body',
        __('Password Reset','xoousers'),array(),
        __('This message will be sent to users when requesting a new password.','xoousers'),
        __('This message will be sent to users when requesting a new password.','xoousers')
		
);

$this->create_plugin_setting(
        'textarea',
        'admin_account_active_message_body',
        __('Account Activation','xoousers'),array(),
        __('This message is sent when the admin approves the user account.','xoousers'),
        __('This message is sent when the admin approves the user account.','xoousers')
		
);

$this->create_plugin_setting(
        'textarea',
        'admin_account_deny_message_body',
        __('Deny Account Activation','xoousers'),array(),
        __('This message is sent when the admin do not approve the user account.','xoousers'),
        __('This message is sent when the admin do not approve the user account.','xoousers')
		
);




		
?>
</table>

  
</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','xoousers'); ?>"  />

</p>

</form>