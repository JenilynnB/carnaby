<?php
class XooNewsLetter extends XooUserUltraCommon 
{
	
	var $mail_chimp_api ;


	function __construct() 
	{
		$this->set_malichimp_api();
		
	}
	
	public function set_malichimp_api () 
	{
		
		
	
	}
	
	/*Subscribe to mailchimp*/
	function mailchimp_subscribe($user_id, $list_id=null) 
	{
		require_once(ABSPATH . 'wp-includes/pluggable.php');		
		require_once(xoousers_path.'/libs/mailchimp/MailChimp.php');	
		
		global  $xoouserultra;	
		
		
		$this->mail_chimp_api =  $xoouserultra->get_option('mailchimp_api');
		
		$user = get_user_by( 'id', $user_id );	
		$email=$user->user_email;
				
		$f_name = get_user_meta($user_id, 'first_name', true);
	    $l_name = get_user_meta($user_id, 'last_name', true);
		
		$MailChimp = new MailChimp( $this->mail_chimp_api );
		
				
		$MailChimp->call('lists/subscribe', array(
                'id'                => $list_id,
                'email'             => array('email'=> $email),
                'merge_vars'        => array('FNAME'=> $f_name, 'LNAME'=> $l_name),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
                'send_welcome'      => false,
		));
		
	}
	
	/*Unsubscribe to mailchimp*/
	function mailchimp_unsubscribe($user_id, $list_id=null) 
	{
		require_once(ABSPATH . 'wp-includes/pluggable.php');		
		require_once(xoousers_path.'/libs/mailchimp/MailChimp.php');
		
		global  $xoouserultra;
		
		$this->mail_chimp_api =  $xoouserultra->get_option('mailchimp_api');
		
		$email = uultra_profile_data('user_email', $user_id);
		
		$MailChimp = new MailChimp( $this->mail_chimp_api );
		$MailChimp->call('lists/unsubscribe', array(
                'id'                => $list_id,
                'email'             => array('email'=> $email)
		));
		
	}
	
	/*Subscription Status*/
	function mailchimp_is_subscriber($user_id, $list_id=null)
	{
		require_once(ABSPATH . 'wp-includes/pluggable.php');		
		require_once(xoousers_path.'/libs/mailchimp/MailChimp.php');
		
		global  $xoouserultra;
	
		$email = uultra_profile_data('user_email', $user_id);
	
		$MailChimp = new MailChimp( $this->mail_chimp_api );
		$results = $MailChimp->call('helper/lists-for-email', array(
				'email'				=> array('email'=> $email)
		));
		
		if (isset($results) && is_array($results)){
			foreach($results as $k=> $arr){
				if (isset($arr['id']) && $arr['id'] == $list_id){
					return true;
				}
			}
		}
		return false;
		
	}
	

	
	

}
$key = "subscribe";
$this->{$key} = new XooNewsLetter();