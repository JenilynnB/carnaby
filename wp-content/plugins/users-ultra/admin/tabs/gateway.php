<h3><?php _e('Payment Gateways Settings','xoousers'); ?></h3>
<form method="post" action="">
<input type="hidden" name="update_settings" />

<div class="user-ultra-sect ">
  <h3><?php _e('Paypal Settings','xoousers'); ?></h3>
  
  <p><?php _e('Here you can configure Paypal if you wish to accept paid registrations','xoousers'); ?></p>
  
  
  <table class="form-table">
<?php 

$this->create_plugin_setting(
                'checkbox',
                'gateway_paypal_active',
                __('Activate Paypal','xoousers'),
                '1',
                __('If checked, Paypal will be activated as payment method','xoousers'),
                __('If checked, Paypal will be activated as payment method','xoousers')
        ); 

$this->create_plugin_setting(
        'input',
        'gateway_paypal_email',
        __('Paypal Email Address','xoousers'),array(),
        __('Enter email address associated to your paypal account.','xoousers'),
        __('Enter email address associated to your paypal account.','xoousers')
);

$this->create_plugin_setting(
        'input',
        'gateway_paypal_sandbox_email',
        __('Paypal Sandbox Email Address','xoousers'),array(),
        __('This is not used for production, you can use this email for testing.','xoousers'),
        __('This is not used for production, you can use this email for testing.','xoousers')
);

$this->create_plugin_setting(
        'input',
        'gateway_paypal_currency',
        __('Currency','xoousers'),array(),
        __('Please enter the currency, example USD.','xoousers'),
        __('Please enter the currency, example USD.','xoousers')
);

$this->create_plugin_setting(
	'select',
	'gateway_paypal_mode',
	__('Mode','xoousers'),
	array(
		1 => __('Production Mode','xoousers'), 
		2 => __('Test Mode (Sandbox)','xoousers')
		),
		
	__('Select Mode','xoousers'),
  __('Select Mode','xoousers')
       );




		
?>
</table>

  
</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','xoousers'); ?>"  />
	
</p>

</form>