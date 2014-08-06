<?php
class XooStat extends XooUserUltraCommon 
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
	
    	  $query = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'usersultra_stats_raw` (
		  `stat_id` int(11) NOT NULL AUTO_INCREMENT,
		  `stat_item_id` int(11) NOT NULL,
		  `stat_module` varchar(100) NOT NULL,
		  `stat_ip` varchar(250) NOT NULL,
		  `stat_date` datetime NOT NULL,
		  PRIMARY KEY (`stat_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';
		
		$wpdb->query( $query );
		
		 $query = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'usersultra_stats` (
		  `stat_id` int(11) NOT NULL AUTO_INCREMENT,
		  `stat_item_id` int(11) NOT NULL,
		  `stat_module` varchar(100) NOT NULL,
		  `stat_total_hits` int(11) NOT NULL,
		  `stat_recent_visit` datetime NOT NULL,
		  PRIMARY KEY (`stat_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';
	
		$wpdb->query( $query );
		
	
		
	}
	
	public function update_hits($item_id, $module) 
	{
		
		 global $wpdb;
		 
		//check if already registered
		$visitor_ip = $_SERVER['REMOTE_ADDR'];
		
		
		
		if($this->check_ip($item_id, $module, $visitor_ip))
		{
			//first time the visitor sees the item
			
			//add to raw table
			$sql = "INSERT INTO " . $wpdb->prefix . "usersultra_stats_raw (stat_item_id ,  	stat_module, stat_ip , stat_date  )						
			
			VALUES(";			
			$sql.= "'".$item_id."', ";			
			$sql.= "'".$module."', ";					
			$sql.= "'".$visitor_ip."',";
			$sql.= "'".$this->mDateToday."') ";				
			$wpdb->query( $sql );
			
			//create or update the stats table
			$stat = $this->get_module_stats($item_id, $module);
			
			if(isset($stat->stat_id) && $stat->stat_id=="")
			{
				//this is the virst visitor
				$sql = "INSERT INTO " . $wpdb->prefix . "usersultra_stats (stat_item_id ,  	stat_module, 	stat_total_hits  , stat_recent_visit  )						
			
				VALUES(";			
				$sql.= "'".$item_id."', ";			
				$sql.= "'".$module."', ";					
				$sql.= "'1',";
				$sql.= "'".$this->mDateToday."') ";				
				$wpdb->query( $sql );
			
			
			}else{
				
				//we have to update the hits only				
				$sql = "UPDATE " . $wpdb->prefix . "usersultra_stats SET stat_total_hits = stat_total_hits+1, stat_recent_visit ='".$this->mDateToday."' WHERE stat_item_id = '".$item_id."' AND	stat_module = '".$module."'  ";				
				$wpdb->query( $sql );
			
			}
			
		}
			
		
		
		
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
	
	 public function check_ip($item_id, $module, $ip)
	 {
		 global $wpdb;
		 
		 $day = date("d");
		 $month = date("m");
		 $year = date("Y");	
		 
         $sql = "SELECT  stat_id FROM " . $wpdb->prefix . "usersultra_stats_raw  WHERE stat_item_id  = '$item_id' AND stat_module = '$module' AND stat_ip = '$ip' AND DAY(stat_date) = '$day'  AND MONTH(stat_date) = '$month'  AND YEAR(stat_date) = '$year' ";	 
		 
		 
		 $votes = $wpdb->get_results( $sql );
		 
		 if ( empty( $votes ) )
			{
				return true;
			
			}else{
				
				return false;
		 }
     }
	
	
	
	
	

}
$key = "statistc";
$this->{$key} = new XooStat();