<?php
class XooRating extends XooUserUltraCommon 
{
	
	var $mMeberToVote;
    var $mMemberWhoVotes;
    var $mQualification;   
    var $mIp;
    var $mTotalScore = 2;	
	var $mDateToday ;


	function __construct() 
	{
		require_once( ABSPATH . "wp-includes/pluggable.php" );
		
		$this->ini_module();
		$this->mDateToday =  date("Y-m-d"); 
		
		
		add_action( 'wp_ajax_rating_vote',  array( $this, 'rating_vote' ));			
		add_action( 'wp_ajax_nopriv_rating_vote',  array( $this, 'rating_vote' ));
					
		
	
				
	}
	
	public function ini_module()
	{
		global $wpdb;

			// Create table
			$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'usersultra_ajaxrating_vote (
  `ajaxrating_vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `ajaxrating_vote_created_by` int(11) NOT NULL,
  `ajaxrating_photo_id` int(11) DEFAULT NULL,
  `ajaxrating_user_id` int(11) DEFAULT NULL,
  `ajaxrating_gallery_id` int(11) DEFAULT NULL,
  `ajaxrating_vote_ip` varchar(15) DEFAULT NULL,
  `ajaxrating_vote_score` int(11) DEFAULT NULL,
  `ajaxrating_vote_created_on` datetime DEFAULT NULL,
  `ajaxrating_vote_modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ajaxrating_vote_modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`ajaxrating_vote_id`),
  KEY `mt_ajaxrating_vote_ip` (`ajaxrating_vote_ip`),
  KEY `mt_ajaxrating_vote_blog_id` (`ajaxrating_photo_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;
';
	
		$wpdb->query( $query );
		
		// Create table photos
			$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'usersultra_ajaxrating_votesummary (
  `ajaxrating_votesummary_id` int(11) NOT NULL AUTO_INCREMENT,
  `ajaxrating_votesummary_avg_score` float DEFAULT NULL,
  `ajaxrating_votesummary_photo_id` int(11) DEFAULT NULL,
   `ajaxrating_votesummary_user_id` int(11) DEFAULT NULL,
   `ajaxrating_votesummary_gallery_id` int(11) DEFAULT NULL,
  `ajaxrating_votesummary_total_score` int(11) DEFAULT NULL,
  `ajaxrating_votesummary_vote_count` int(11) DEFAULT NULL,
  `ajaxrating_votesummary_created_on` datetime DEFAULT NULL,
  `ajaxrating_votesummary_created_by` int(11) DEFAULT NULL,
  `ajaxrating_votesummary_modified_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ajaxrating_votesummary_modified_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`ajaxrating_votesummary_id`),
  KEY `mt_ajaxrating_votesummary_vote_count` (`ajaxrating_votesummary_vote_count`),
  KEY `mt_ajaxrating_votesummary_total_score` (`ajaxrating_votesummary_total_score`),
  KEY `mt_ajaxrating_votesummary_avg_score` (`ajaxrating_votesummary_avg_score`),
  KEY `mt_ajaxrating_votesummary_blog_id` (`ajaxrating_votesummary_photo_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;';
	
		$wpdb->query( $query );
		
		
		
	}
	
	 public function check_ip($data_id, $data_target, $ip)
	 {
		 global $wpdb;	
		 
         $sql = "SELECT * FROM " . $wpdb->prefix . "usersultra_ajaxrating_vote  WHERE ajaxrating_vote_ip  = '$ip' AND ajaxrating_".$data_target." = '$data_id'";	 
		 
		 $votes = $wpdb->get_results( $sql );
		 
		 if ( empty( $votes ) )
			{
				return true;
			
			}else{
				
				return false;
		 }
     }
	
	
	public function rating_vote() 
	{
		global $wpdb, $xoouserultra;	
		
		$user_id= "";
		
		$user_id = get_current_user_id();
		
		$data_id =  $_POST["data_id"];
		$data_target = $_POST["data_target"];
		$data_vote =  $_POST["data_vote"];	
	
		//logged user, the voter
		$t_member_that_votes = get_current_user_id();		
		
		/*procced to put a vote */
	    $this->mMeberToVote = $data_id;
	    $this->mMemberWhoVotes = $t_member_that_votes;	
	    $this->mQualification = $data_vote; 	
	    $this->mIp = $_SERVER['REMOTE_ADDR'];			
			
			
		/*get total votes for that user*/		
		$check_vote = $this->check_ip($data_id, $data_target, $_SERVER['REMOTE_ADDR']);
		
		//check if trying to rate itlsef
		
		$rate_itself = false;
		
		if($data_id==$user_id && $data_target=='user_id')
		{
			$rate_itself = true;		
		
		}
		
		$guest_allowed = $xoouserultra->get_option('uultra_allow_guest_rating');
		
		
		
		if(!$rate_itself)
		{
			
			if (is_user_logged_in() || $guest_allowed==1) 
			{
				if($check_vote) 
				{
					
					$sql = "INSERT INTO " . $wpdb->prefix . "usersultra_ajaxrating_vote 
					(ajaxrating_vote_created_by ,ajaxrating_".$data_target." ,ajaxrating_vote_ip ,ajaxrating_vote_score ,ajaxrating_vote_created_on) VALUES(";
					$sql.= "'".$this->mMemberWhoVotes."',";
					$sql.= "'".$data_id."',";
					$sql.= "'".$this->mIp."',";
					$sql.= "'".($this->mQualification*2)."',";
					$sql.= "'".$this->mDateToday."')";
					$wpdb->query( $sql );
					
					 $rowRating = $this->get_all_of($data_id, $data_target);
					  
					 if(!isset($rowRating->ajaxrating_votesummary_total_score) || $rowRating->ajaxrating_votesummary_total_score== "")
					  {
						
						/*Calculation*/
						$score = $this->mQualification * 2;
						$total_votes = 1;
						$rating1 = number_format($score/$total_votes,1);
						
						
						$sql = "INSERT INTO  " . $wpdb->prefix . "usersultra_ajaxrating_votesummary  
						(ajaxrating_votesummary_".$data_target." ,  ajaxrating_votesummary_total_score , ajaxrating_votesummary_vote_count, ajaxrating_votesummary_avg_score,  ajaxrating_votesummary_created_on) VALUES(";
						
						$sql.= "'".$this->mMeberToVote."',";
						$sql.= "'".$score."',";
						$sql.= "'".$total_votes."',";
						$sql.= "'".$rating1."',";
						$sql.= "'".$this->mDateToday."')";
						$wpdb->query( $sql );
									
						
					}else{
						
						$total_score = 0;
						if(isset($rowPrevRating->ajaxrating_votesummary_total_score))
						{
							$total_score = $rowPrevRating->ajaxrating_votesummary_total_score;
							
						}
						
						$votes_count = 0;
						if(isset($rowPrevRating->ajaxrating_votesummary_vote_count))
						{
							$votes_count = $rowPrevRating->ajaxrating_votesummary_vote_count;
							
						}
						
						/*Calculation*/
						$rowPrevRating = $rowRating;
						$score = ($this->mQualification  * 2) + $total_score;
						$total_votes = $votes_count+ 1;
						
						$rating1 = number_format($score/$total_votes,1);
						
						$sql = "UPDATE  " . $wpdb->prefix . "usersultra_ajaxrating_votesummary  
						SET   ajaxrating_votesummary_total_score = '".$score."' , ajaxrating_votesummary_vote_count = '".$total_votes."', ajaxrating_votesummary_avg_score = '".$rating1."'  WHERE ajaxrating_votesummary_".$data_target." = '".$data_id."'";
						
						$wpdb->query( $sql );	
						
					}
					
					$html = __("Thank you for your rating",'xoousers');
				
				}else{
					
					
					$html = __("You've already left your rate ",'xoousers');			
					
				
				}
			
			}else{
				   
				   
				   //user is not logged in
					
					
					$html = __("You have to be logged in to leave your rate  ",'xoousers');			
					
				
				}	
		}else{
			
			   //is trying to rate itlsef				   
     			$html = __("You cannot rate yourself  ",'xoousers');			
					
				
		}		
		
		echo $html;
		
		die();
    }

    	
	
	
	
	public function get_rating($id, $data_target) 
	{
		global $wpdb;
          
		  $rowRating = $this->get_all_of($id, $data_target);
		  
		  if(isset($rowRating->ajaxrating_votesummary_total_score) && $rowRating->ajaxrating_votesummary_total_score != "")
		  {
			 			  
			  $score = $rowRating->ajaxrating_votesummary_total_score ;
			  $total_votes = $rowRating->ajaxrating_votesummary_vote_count;
			  
			  $perfect_rating = $total_votes * 10;		  
			  $current_rating =($score*100)/$perfect_rating;
			  $current_rating_percentage =number_format($current_rating,0);
			  
			  /*Average*/
			  
			  $rating1 = number_format($score/$total_votes,1);
		  }else{
			  
			  $current_rating_percentage = 0;
			  $total_votes = 0;
			  $rating1 = "0.0";		   
		   
		  }	  
		  
		  $rating = "";		  	  
		  $res = '
		  
		  <ul class="star-rating" >	
		            <li class="current-rating" style="width:'.$current_rating_percentage.'%;"></li>
					
					<li><a href="#" title="1 star out of 5" class="one-star rating-s" data-id="'.$id.'" data-vote="1" data-target="'.$data_target.'">1</a></li>					
					<li><a href="#" title="2 star out of 5" class="two-stars rating-s" data-id="'.$id.'" data-vote="2" data-target="'.$data_target.'">2</a></li>
					<li><a href="#" title="3 star out of 5" class="three-stars rating-s" data-id="'.$id.'" data-vote="3" data-target="'.$data_target.'">3</a></li>
					<li><a href="#" title="4 star out of 5" class="four-stars rating-s" data-id="'.$id.'" data-vote="4" data-target="'.$data_target.'">4</a></li>
					<li><a href="#" title="5 star out of 5" class="five-stars rating-s" data-id="'.$id.'" data-vote="5" data-target="'.$data_target.'">5</a></li>
				</ul>
				<p> Rating:  '.$rating1.'/10 </p>
				<p>('.$total_votes.' votes cast)</p>';
				
		return $res;
		  
    }
	
	
	function get_all_of($id, $data_target) 	
	{
		global $wpdb;
        $sql = "SELECT * FROM  " . $wpdb->prefix . "usersultra_ajaxrating_votesummary WHERE ajaxrating_votesummary_".$data_target." = '$id' ";
		
		$rating = $wpdb->get_results($sql);
		
		
		foreach ( $rating as $rate )
	    {
			return $rate;
			
		}
		
    }
	
	

}
$key = "rating";
$this->{$key} = new XooRating();