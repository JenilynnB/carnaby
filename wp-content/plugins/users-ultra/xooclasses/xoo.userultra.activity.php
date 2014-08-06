<?php
class XooActivity 
{
	
	//-------- modules: (user,photo,gallery)
	var $mDateToday ;


	function __construct() 
	{
		$this->ini_module();
		$this->mDateToday =  date("Y-m-d"); 
		
		
	}
	
	public function ini_module()
	{
		global $wpdb;
	
    	  $query = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'usersultra_activity` (
		  `act_id` int(11) NOT NULL AUTO_INCREMENT,
		  `act_user_id` int(11) NOT NULL ,
		  `act_item_id` int(11) NOT NULL,
		  `act_module` varchar(100) NOT NULL,
		  `act_title` varchar(100) NOT NULL,			 
		  `act_date` datetime NOT NULL,
		  PRIMARY KEY (`act_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';
		
		$wpdb->query( $query );
		
	
	
		
	}
	
	public function update_hits($item_id, $module) 
	{
		
		 global $wpdb;
		 
				
		//add to raw table
		$sql = "INSERT INTO " . $wpdb->prefix . "usersultra_stats_raw (stat_item_id ,  	stat_module, stat_ip , stat_date  )						
			
			VALUES(";			
		$sql.= "'".$item_id."', ";			
		$sql.= "'".$module."', ";					
		$sql.= "'".$visitor_ip."',";
		$sql.= "'".$this->mDateToday."') ";				
		$wpdb->query( $sql );
			
			
		
    }
	
	 public function get_module_stats($item_id, $module)
	 {
		 global $wpdb;
		 
		 
         $sql = "SELECT  * FROM " . $wpdb->prefix . "usersultra_stats  WHERE stat_item_id  = '$item_id' AND stat_module = '$module'  ";	 
		 
		 $res = $wpdb->get_results( $sql );
		 
		 if ( !empty( $res ) )
		 {
			foreach ( $res as $row )
			{
				return  $row;
			
			}
			
			
		 }
     }
	
	
	
	
	
	

}
$key = "activity";
$this->{$key} = new XooActivity();