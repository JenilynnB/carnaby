<h3><?php _e('Permalinks','xoousers'); ?></h3>
<form method="post" action="">
<input type="hidden" name="update_settings" />

<div class="user-ultra-sect ">
  <h3><?php _e('Users Ultra offers you the ability to create a custom URL structure for the main pages of your website','xoousers'); ?></h3>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
        'input',
        'usersultra_slug',
        __('Profile Slug','xoousers'),array(),
        __('Enter your custom Slug for the profile page.','xoousers'),
        __('Enter your custom Slug for the profile page.','xoousers')
);



$this->create_plugin_setting(
	'select',
	'usersultra_permalink_type',
	__('Profile Link Field','xoousers'),
	array(
	    'ID' => __('The Profile Link is built by using the User ID','xoousers'),
		'username' => __('The Profile Link is built by using the Username','xoousers'), 
		
		),
		
	__('Please note: This option is used for permalinks. You can choice what field will be used in the Users Profile Link','xoousers'),
  __('Please note: This option is used for permalinks. You can choice what field will be used in the Users Profile Link','xoousers')
       );
    

$this->create_plugin_setting(
        'input',
        'usersultra_login_slug',
        __('Login Slug','xoousers'),array(),
        __('Enter your custom Slug for the Login Page.','xoousers'),
        __('Enter your custom Slug for the Login Page.','xoousers')
);

$this->create_plugin_setting(
        'input',
        'usersultra_registration_slug',
        __('Registration Slug','xoousers'),array(),
        __('Enter your custom Slug for the Registration Page.','xoousers'),
        __('Enter your custom Slug for the Registration Page.','xoousers')
);

$this->create_plugin_setting(
        'input',
        'usersultra_my_account_slug',
        __('My Account Slug','xoousers'),array(),
        __('Enter your custom Slug for the "My Account" Page.','xoousers'),
        __('Enter your custom Slug for the "My Account" Page.','xoousers')
);

$this->create_plugin_setting(
        'input',
        'usersultra_directory_slug',
        __('Users Directory Slug','xoousers'),array(),
        __('Enter your custom Slug for the Users Directory Page.','xoousers'),
        __('Enter your custom Slug for the Users Directory Page.','xoousers')
);

		
?>
</table>

  
</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','xoousers'); ?>"  />
	
</p>

</form>