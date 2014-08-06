<?php
class XooBadge
{
	var $mBadgesArray =  array();
	
	
	
	
	
	function __construct() 
	{
		
		
		$this->ini_module();
		
		
		
	}

	
	public function ini_module()
	{
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		if(isset($_POST["item_id"]))
		{
			$item_id = $_POST["item_id"];
		}
		
		if(isset($_POST["item_action"]))
		{
			$item_action = $_POST["item_action"];
		}
		
		$logged_user_id = get_current_user_id();
		
		if($item_action=='approve')
		{
			$sql = "UPDATE " . $wpdb->prefix . "usersultra_friends SET friend_status = 1  WHERE friend_id  = '$item_id' AND friend_receiver_id = '$logged_user_id'";
			
			$message = __('Request approved','xoousers'); 	
		}
		
		if($item_action=='block')
		{
			$sql = "UPDATE " . $wpdb->prefix . "usersultra_friends SET friend_status = 2  WHERE friend_id  = '$item_id' AND friend_receiver_id = '$logged_user_id'";
			
			$message = __('Request approved','xoousers'); 	
		}
		
		if($item_action=='deny')
		{
			$sql = "DELETE FROM " . $wpdb->prefix . "usersultra_friends  WHERE friend_id  = '$item_id' AND friend_receiver_id = '$logged_user_id'";
			
			$message = __('Request rejected','xoousers'); 	
		}	
		
		echo $message;	 
		 
		$res = $wpdb->query( $sql );
		
		
		
		die();
		
		
		
		
	
	}
	
	

}
$key = "badge";
$this->{$key} = new XooBadge();