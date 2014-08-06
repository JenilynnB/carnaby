<h3><?php _e('General Settings','xoousers'); ?></h3>
<form method="post" action="">
<input type="hidden" name="update_settings" />

<div class="user-ultra-sect ">
  <h3><?php _e('Miscellaneous  Settings','xoousers'); ?></h3>
  
  <p><?php _e('.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 
   
$this->create_plugin_setting(
                'checkbox',
                'hide_admin_bar',
                __('Hide WP Admin Tool Bar','xoousers'),
                '1',
                __('If checked, User will not see the WP Admin Tool Bar','xoousers'),
                __('If checked, User will not see the WP Admin Tool Bar.','xoousers')
        ); 
		

$this->create_plugin_setting(
                'checkbox',
                'uultra_allow_guest_rating',
                __('Allow Guests to use the rating system','xoousers'),
                '1',
                __('If checked, users will be able to leave rates without being logged in','xoousers'),
                __('If checked, User will not see the WP Admin Tool Bar.','xoousers')
        ); 
		
		
?>
</table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('Membership  Settings','xoousers'); ?></h3>
  
  <p><?php _e('.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 
   
$this->create_plugin_setting(
                'checkbox',
                'membership_display_selected_only',
                __('Display Only Selected Package','xoousers'),
                '1',
                __('If checked, Only the Selected package will be displayed in the payment form. <strong>PLEASE NOTE: </strong>This setting is used only if you are using the pricing tables feature.','xoousers'),
                __('If checked, Only the Selected package will be displayed in the payment form','xoousers')
        ); 
$this->create_plugin_setting(
        'input',
        'membership_display_zero',
        __('Text for free membership:','xoousers'),array(),
        __('This text will be displayed for the free membership rather than showing <strong>"$0.00"<strong>. Please input some text like: "Free"','xoousers'),
        __('This text will be displayed for the free membership rather than showing <strong>"$0.00"<strong>. Please input some text like:','xoousers')
);		
?>
</table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('Media Settings','xoousers'); ?></h3>
  
  <p><?php _e('.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 
		
$this->create_plugin_setting(
        'input',
        'media_uploading_folder',
        __('Upload Folder:','xoousers'),array(),
        __('This is the folder where the user photos will be stored in. Please make sure to assing 755 privileges to it. The default folder is <strong>wp-content/usersultramedia</strong>','xoousers'),
        __('This is the folder where the user photos will be stored in. Please make sure to assing 755 privileges to it. The default folder is <strong>wp-content/usersultramedia</strong>','xoousers')
);

$this->create_plugin_setting(
        'input',
        'media_photo_mini_width',
        __('Mini Thumbnail Width','xoousers'),array(),
        __('Set width in pixels','xoousers'),
        __('Set width in pixels','xoousers')
);

$this->create_plugin_setting(
        'input',
        'media_photo_mini_height',
        __('Mini Thumbnail Height','xoousers'),array(),
        __('Set Height in pixels','xoousers'),
        __('Set Height in pixels','xoousers')
);

$this->create_plugin_setting(
        'input',
        'media_photo_thumb_width',
        __('Thumbnail Width','xoousers'),array(),
        __('Set Width in pixels','xoousers'),
        __('Set Width in pixels','xoousers')
);

$this->create_plugin_setting(
        'input',
        'media_photo_thumb_height',
        __('Thumbnail Height','xoousers'),array(),
        __('Set Height in pixels','xoousers'),
        __('Set Height in pixels','xoousers')
);

$this->create_plugin_setting(
        'input',
        'media_photo_large_width',
        __('Large Photo Max Width','xoousers'),array(),
        __('Set Width in pixels','xoousers'),
        __('Set Width in pixels','xoousers')
);

$this->create_plugin_setting(
        'input',
        'media_photo_large_height',
        __('Large Photo Max Height','xoousers'),array(),
        __('Set Height in pixels','xoousers'),
        __('Set Height in pixels','xoousers')
);
		
?>
</table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('Frontend Publishing  Settings','xoousers'); ?></h3>
  
  <p><?php _e('.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'uultra_front_publisher_default_amount',
        __('Max Posts Per User:','xoousers'),array(),
        __('Please set 9999 for unlimited posts. This value is used for free and general users','xoousers'),
        __('Please set 9999 for unlimited posts. This value is used for free and general users','xoousers')
);

$this->create_plugin_setting(
	'select',
	'uultra_front_publisher_default_status',
	__('Default Status','xoousers'),
	array(
		'pending' => __('Pending','xoousers'), 
		'publish' => __('Publish','xoousers'),
		),
		
	__('This is the status of the post when the users submit new posts through Users Ultra.','xoousers'),
  __('This is the status of the post when the users submit new posts through Users Ultra.','xoousers')
       );
	   
$this->create_plugin_setting(
	'select',
	'uultra_front_publisher_allows_category',
	__('Alows users to select category','xoousers'),
	array(
		'yes' => __('Yes','xoousers'), 
		'no' => __('No','xoousers'),
		),
		
	__('If "yes" authors will be able to select the category, if "no" is set the default category will be used to save the post.','xoousers'),
  __('If "yes" authors will be able to select the category, if "no" is set the default category will be used to save the post.','xoousers')
       );
	   
   $this->create_plugin_setting(
            'select',
            'uultra_front_publisher_default_category',
            __('Default Category','xoousers'),
            $this->get_all_sytem_cagegories(),
            __('The category if authors are not allowed to select a custom category.','xoousers'),
            __('The category if authors are not allowed to select a custom category.','xoousers')
    );

		
?>
</table>

  
</div>




<div class="user-ultra-sect ">
  <h3><?php _e('Avatar Settings','xoousers'); ?></h3>
  
  <p><?php _e('.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'media_avatar_width',
        __('Avatar Width:','xoousers'),array(),
        __('Set width in pixels','xoousers'),
        __('Set width in pixels','xoousers')
);

$this->create_plugin_setting(
        'input',
        'media_avatar_height',
        __('Avatar Height','xoousers'),array(),
        __('Set Height in pixels','xoousers'),
        __('Set Height in pixels','xoousers')
);

		
?>
</table>

  
</div>


<div class="user-ultra-sect ">
  <h3><?php _e('Mailchimp Settings','xoousers'); ?></h3>
  
  <p><?php _e('.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'mailchimp_api',
        __('MailChimp API Key','xoousers'),array(),
        __('Fill out this field with your MailChimp API key here to allow integration with MailChimp subscription.','xoousers'),
        __('Fill out this field with your MailChimp API key here to allow integration with MailChimp subscription.','xoousers')
);

$this->create_plugin_setting(
        'input',
        'mailchimp_list_id',
        __('MailChimp List ID','xoousers'),array(),
        __('Fill out this field your list ID.','xoousers'),
        __('Fill out this field your list ID.','xoousers')
);

$this->create_plugin_setting(
                'checkbox',
                'mailchimp_active',
                __('Activate/Deactivate Mailchimp','xoousers'),
                '1',
                __('If checked, Users will be asked to subscribe through mailchimp','xoousers'),
                __('If checked, Users will be asked to subscribe through mailchimp','xoousers')
        );
$this->create_plugin_setting(
        'input',
        'mailchimp_text',
        __('MailChimp Text','xoousers'),array(),
        __('Please input the text that will appear when asking users to get periodical updates.','xoousers'),
        __('Please input the text that will appear when asking users to get periodical updates.','xoousers')
);

		
?>
</table>

  
</div>
<div class="user-ultra-sect ">
  <h3><?php _e('Registration Settings','xoousers'); ?></h3>
  
  <p><?php _e('.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
	'select',
	'registration_rules',
	__('Registration Type','xoousers'),
	array(
		1 => __('Login automatically after registration','xoousers'), 
		2 => __('E-mail Activation -  An confirmation link is sent to the user email','xoousers'),
		3 => __('Manually Activation - The admin approves the accounts manually','xoousers'),
		4 => __('Paid Activation - Enables the Membership Features','xoousers')),
		
	__('Please note: Paid Activation does not work with social connects at this moment.','xoousers'),
  __('Please note: Paid Activation does not work with social connects at this moment.','xoousers')
       );
    
    
?>
</table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('User Profiles Settings','xoousers'); ?></h3>
  
  <p><?php _e('.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
	'select',
	'uprofile_setting_display_name',
	__('Profile Display Name: ','xoousers'),
	array(
		'username' => __('Display User Name','xoousers'), 
		'display_name' => __('Use the Display Name set by the User in the Profile','xoousers')),
		
	__('Set how the users ultra will make the user name.','xoousers'),
  __('Set how the users ultra will make the user name.','xoousers')
       );

$this->create_plugin_setting(
	'select',
	'uurofile_setting_display_photos',
	__('Display Photos: ','xoousers'),
	array(
		'private' => __('Only for regitered/logged in users','xoousers'), 
		'public' => __('All visitor can see the user photos without registration','xoousers')
		),
		
	__('Specify if the user photos are public or private','xoousers'),
  __('Specify if the user photos are public or private','xoousers')
       );
    
    
?>
</table>

  
</div>

<div class="user-ultra-sect ">
  <h3><?php _e('Paid Membership Settings','xoousers'); ?></h3>
  
  <p><?php _e('.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 
   
$this->create_plugin_setting(
        'input',
        'paid_membership_currency',
        __('Currency','xoousers'),array(),
        __('The default symbol for paypal payments is USD','xoousers'),
        __('The default symbol for paypal payments is USD','xoousers')
);

$this->create_plugin_setting(
        'input',
        'paid_membership_symbol',
        __('Currency Symbol','xoousers'),array(),
        __('Input the currency symbol: Example: $','xoousers'),
        __('Input the currency symbol: Example: $','xoousers')
);


		
?>
</table>

  
</div>


<div class="user-ultra-sect ">
  <h3><?php _e('Users Ultra Pages Setting','xoousers'); ?></h3>
  
  <p><?php _e('The following pages are automatically created when Users Ultra Plugin
   is activated. You can leave them as they are or change to custom pages here.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 


$this->create_plugin_setting(
            'select',
            'xoousersultra_my_account_page',
            __('Users Ultra My Account','xoousers'),
            $this->get_all_sytem_pages(),
            __('Make sure you have the <code>[usersultra_my_account]</code> shortcode on this page.','xoousers'),
            __('This page is where users will view their account.','xoousers')
    );
	
    $this->create_plugin_setting(
            'select',
            'profile_page_id',
            __('Users Ultra Profile Page','xoousers'),
            $this->get_all_sytem_pages(),
            __('Make sure you have the <code>[usersultra_profile]</code> shortcode on this page.','xoousers'),
            __('This page is where users will view their own profiles, or view other user profiles from the member directory if allowed.','xoousers')
    );
    
    $this->create_plugin_setting(
            'select',
            'login_page_id',
            __('Users Ultra Login Page','xoousers'),
            $this->get_all_sytem_pages(),
            __('If you wish to change default Users Ultra login page, you may set it here. Make sure you have the <code>[usersultra_login]</code> shortcode on this page.','xoousers'),
            __('The default front-end login page.','xoousers')
    );
    
    $this->create_plugin_setting(
            'select',
            'registration_page_id',
            __('Users Ultra Registration Page','xoousers'),
            $this->get_all_sytem_pages(),
            __('Make sure you have the <code>[usersultra_registration]</code> shortcode on this page.','xoousers'),
            __('The default front-end Registration page where new users will sign up.','xoousers')
    );
	
	$this->create_plugin_setting(
            'select',
            'directory_page_id',
            __('Users Ultra Directory Page','xoousers'),
            $this->get_all_sytem_pages(),
            __('Make sure you have the <code>[usersultra_directory]</code> shortcode on this page.','xoousers'),
            __('The default front-end Registration page where new users will sign up.','xoousers')
    );
	
	
	 
    
    
?>
</table>

  
</div>



<div class="user-ultra-sect ">
  <h3><?php _e('Social Media Settings','xoousers'); ?></h3>
  
  <p><?php _e('.','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 
   
   
$this->create_plugin_setting(
                'checkbox',
                'social_media_fb_active',
                __('Facebook Connect','xoousers'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Facebook.','xoousers'),
                __('If checked, User will be able to Sign up & Sign in through Facebook.','xoousers')
        );
		
$this->create_plugin_setting(
        'input',
        'social_media_facebook_app_id',
        __('Facebook App ID','xoousers'),array(),
        __('Obtained when you created the Facebook Application.','xoousers'),
        __('Obtained when you created the Facebook Application.','xoousers')
);



$this->create_plugin_setting(
        'input',
        'social_media_facebook_secret',
        __('Facebook App Secret','xoousers'),array(),
        __('Facebook settings','xoousers'),
        __('Obtained when you created the Facebook Application.','xoousers')
);

$this->create_plugin_setting(
                'checkbox',
                'social_media_linked_active',
                __('LinkedIn Connect','xoousers'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through LinkedIn.','xoousers'),
                __('If checked, User will be able to Sign up & Sign in through LinkedIn.','xoousers')
        );
    
$this->create_plugin_setting(
        'input',
        'social_media_linkedin_api_public',
        __('LinkedIn API Key Public','xoousers'),array(),
        __('Obtained when you created your applicaton.','xoousers'),
        __('Obtained when you created your applicaton.','xoousers')
);

$this->create_plugin_setting(
        'input',
        'social_media_linkedin_api_private',
        __('LinkedIn API Key Private','xoousers'),array(),
        __('<br><br> VERY IMPORTANT: Set OAuth 1.0 Accept Redirect URL to "?uultralinkedin=1". Example: http://yourdomain.com/?uultralinkedin=1','xoousers'),
        __('VERY IMPORTANT: Set OAuth 1.0 Accept Redirect URL to "?uultralinkedin=1','xoousers')
);  


$this->create_plugin_setting(
                'checkbox',
                'social_media_yahoo',
                __('Yahoo Sign up','xoousers'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Yahoo.','xoousers'),
                __('If checked, User will be able to Sign up & Sign in through Yahoo.','xoousers')
        );
$this->create_plugin_setting(
                'checkbox',
                'social_media_google',
                __('Google Sign up','xoousers'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Google.','xoousers'),
                __('If checked, User will be able to Sign up & Sign in through Google.','xoousers')
        ); 

$this->create_plugin_setting(
        'input',
        'google_client_id',
        __('Google Client ID','xoousers'),array(),
        __('Paste the client id that you got from google API Console','xoousers'),
        __('Paste the client id that you got from google API Console','xoousers')
);  

$this->create_plugin_setting(
        'input',
        'google_client_secret',
        __('Google Client Secret','xoousers'),array(),
        __('Set the client secret','xoousers'),
        __('Obtained when you created the Google Application.','xoousers')
);

$this->create_plugin_setting(
        'input',
        'google_redirect_uri',
        __('Google Redirect URI','xoousers'),array(),
        __('Paste the redirect URI where you given in APi Console. You will get the Access Token here during login success. Find more information here https://developers.google.com/console/help/new/#console.  <br><br> VERY IMPORTANT: Your URL should end with "?uultraplus=1". Example: http://yourdomain.com/?uultraplus=1','xoousers'),
        __('Your URL should end with "?uultraplus=1". Example: http://yourdomain.com/?uultraplus=1','xoousers')
);


/// add to array
$this->create_plugin_setting(
                'checkbox',
                'twitter_connect',
                __('Twitter Sign up','xoousers'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Twitter.','xoousers'),
                __('If checked, User will be able to Sign up & Sign in through Twitter.','xoousers')
        );
		

$this->create_plugin_setting(
        'input',
        'twitter_consumer_key',
        __('Consumer Key','xoousers'),array(),
        __('Paste the Consumer Key','xoousers'),
        __('Obtained when you created your Applicatoin at Twitter Developer Center','xoousers')
);  

$this->create_plugin_setting(
        'input',
        'twitter_consumer_secret',
        __('Consumer Secret','xoousers'),array(),
        __('Paste the Consumer Secret','xoousers'),
        __('Obtained when you created your Applicatoin at Twitter Developer Center','xoousers')
);

$this->create_plugin_setting(
                'checkbox',
                'twitter_autopost',
                __('Twitter Auto Post','xoousers'),
                '1',
                __('If checked, Users Ultra will post a message automatically to the user twitter timeline when registering.','xoousers'),
                __('If checked, Users Ultra will post a message automatically to the user twitter timeline when registering.','xoousers','xoousers')
        );

$this->create_plugin_setting(
        'input',
        'twitter_autopost_msg',
        __('Message','xoousers'),array(),
        __('Input the message that will be posted right after user registration','xoousers'),
        __('Input the message that will be posted right after user registration','xoousers')
);		
/// yammer
$this->create_plugin_setting(
                'checkbox',
                'yammer_connect',
                __('Yammer Sign up','xoousers'),
                '1',
                __('If checked, User will be able to Sign up & Sign in through Yammer.','xoousers'),
                __('If checked, User will be able to Sign up & Sign in through Yammer.','xoousers')
        );
		

$this->create_plugin_setting(
        'input',
        'yammer_client_id',
        __('Client Id','xoousers'),array(),
        __('Paste the Yammer Client ID','xoousers'),
        __('The same used when you signed up.','xoousers')
);  

$this->create_plugin_setting(
        'input',
        'yammer_client_secret',
        __('Client Secret','xoousers'),array(),
        __('Paste the Yammer Client Secret','xoousers'),
        __('Obtained at Yammer developer center.','xoousers')
);

$this->create_plugin_setting(
        'input',
        'yammer_redir_url',
        __('Redirect URL','xoousers'),array(),
        __('Paste the Yammer Client Secret','xoousers'),
        __('<br><br> VERY IMPORTANT: Your URL should end with "?uultryammer=1". Example: http://yourdomain.com/?uultryammer=1','xoousers')
);


		
?>
</table>

  
</div>

<div class="user-ultra-sect ">
 <h3><?php _e('Redirect Settings','xoousers'); ?></h3>

  <p><?php _e('.','xoousers'); ?></p>



<table class="form-table">
    <?php 
        $this->create_plugin_setting(
                'checkbox',
                'redirect_backend_profile',
                __('Redirect Backend Profiles','xoousers'),
                '1',
                __('If checked, non-admin users who try to access backend WP profiles will be redirected to Users Ultra Profile Page specified above.','xoousers'),
                __('Checking this option will send all users to the front-end Users Ultra Profile Page if they try to access the default backend profile page in wp-admin. The page can be selected in the Users Ultra System Pages settings.','xoousers')
        );
        
        $this->create_plugin_setting(
                'checkbox',
                'redirect_backend_login',
                __('Redirect Backend Login','xoousers'),
                '1',
                __('If checked, non-admin users who try to access backend login form will be redirected to the front end Users Ultra Login Page specified above.','xoousers'),
                __('Checking this option will send all users to the front-end Users Ultra Login Page if they try to access the default backend login form. The page can be selected in the Users Ultra System Pages settings.','xoousers')
        );
        
        $this->create_plugin_setting(
                'checkbox',
                'redirect_backend_registration',
                __('Redirect Backend Registrations','xoousers'),
                '1',
                __('If checked, non-admin users who try to access backend registration form will be redirected to the front end Users Ultra Registration Page specified above.','xoousers'),
                __('Checking this option will send all users to the front-end Users Ultra Registration Page if they try to access the default backend registraiton form. The page can be selected in the Users Ultra System Pages settings.','xoousers')
        );
		
		
		    $this->create_plugin_setting(
            'select',
            'redirect_after_registration_login',
            __('After Registration','xoousers'),
            $this->get_all_sytem_pages(),
            __('The user will be taken to this page after registration if the account activation is set to automatic ','xoousers'),
            __('The user will be taken to this page after registration if the account activation is set to automatic','xoousers')
    );
        
    ?>
</table>

</div>

<h3><?php _e('Registration Options','xoousers'); ?></h3>
<table class="form-table">

<?php

$this->create_plugin_setting(
	'select',
	'set_password',
	__('User Selected Passwords','xoousers'),
	array(
		1 => __('Enabled, allow users to set password','xoousers'), 
		0 => __('Disabled, email a random password to users','xoousers')),
	__('Enable/disable setting a user selected password at registration','xoousers'),
  __('If enabled, users can choose their own password at registration. If disabled, WordPress will email users a random password when they register.','xoousers')
        );
		

?>

</table>

<h3><?php _e('Privacy Options','xoousers'); ?></h3>
<table class="form-table">

<?php

$this->create_plugin_setting(
	'select',
	'users_can_view',
	__('Logged-in user viewing of other profiles','xoousers'),
	array(
		1 => __('Enabled, logged-in users may view other user profiles','xoousers'), 
		0 => __('Disabled, users may only view their own profile','xoousers')),
	__('Enable or disable logged-in user viewing of other user profiles. Admin users can always view all profiles.','xoousers'),
  __('If enabled, logged-in users are allowed to view other user profiles. If disabled, logged-in users may only view theor own profile.','xoousers')
        );

$this->create_plugin_setting(
	'select',
	'guests_can_view',
	__('Guests viewing of profiles','xoousers'),
	array(
		1 => __('Enabled, make profiles publicly visible','xoousers'), 
		0 => __('Disabled, users must login to view profiles','xoousers')),
	__('Enable or disable guest and non-logged in user viewing of profiles.','xoousers'),
  __('If enabled, profiles will be publicly visible to non-logged in users. If disabled, guests must log in to view profiles.','xoousers')
       );

?>

</table>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','xoouser'); ?>"  />
</p>

</form>