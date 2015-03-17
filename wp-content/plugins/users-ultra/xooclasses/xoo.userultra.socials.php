<?php
class XooSocial
{
	
	
	var $mDateToday ;
	
	
	function __construct() 
	{
		
		
		$this->ini_module();
		$this->mDateToday =  date("Y-m-d"); 
		
		$this->set_ajax();
		
		
	}
	
	public function set_ajax()
	{
		require_once( ABSPATH . "wp-includes/pluggable.php" );
		
		//if (is_user_logged_in() ) 
	//	{
			add_action( 'wp_ajax_send_friend_request',  array( $this, 'send_friend_request' ));
					
	//	}else{
			
			add_action( 'wp_ajax_nopriv_send_friend_request',  array( $this, 'send_friend_request' ));			
								
		//}
		
		//if (is_user_logged_in() ) 
	//	{
			add_action( 'wp_ajax_like_item',  array( $this, 'like_item' ));
					
		//}else{
			
			add_action( 'wp_ajax_nopriv_like_item',  array( $this, 'like_item' ));			
								
	//	}
		
		add_action( 'wp_ajax_get_item_likes_amount_only',  array( $this, 'get_item_likes_amount_only' ));
		add_action( 'wp_ajax_friend_request_action',  array( $this, 'friend_request_action' ));
		add_action( 'wp_ajax_show_all_my_friends',  array( $this, 'show_all_my_friends' ));
		add_action( 'wp_ajax_show_friend_request',  array( $this, 'show_friend_request' ));
		
		
		
	}
	
	public function ini_module()
	{
		global $wpdb;
	
    	  $query = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'usersultra_friends` (
		  `friend_id` int(11) NOT NULL AUTO_INCREMENT,
		  `friend_receiver_id` int(11) NOT NULL ,
		  `friend_sender_user_id` int(11) NOT NULL,
		  `friend_status` int(1) NOT NULL,		 		 
		  `friend_date` datetime NOT NULL,
		  PRIMARY KEY (`friend_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';
		
		$wpdb->query( $query );
		
		//likes
		$query = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . 'usersultra_likes` (
		  `like_id` int(11) NOT NULL AUTO_INCREMENT,
		  `like_liked_id` int(11) NOT NULL ,
		  `like_liker_user_id` int(11) NOT NULL,
		  `like_module` varchar(50) NOT NULL,	
		  `like_vote` int(2) NOT NULL,	 		 
		  `like_date` datetime NOT NULL,
		  PRIMARY KEY (`like_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';
		
		$wpdb->query( $query );
		
	
	
		
	}
	
	public function send_friend_request()
	{
		
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
		
		$receiver_id = $_POST["user_id"];
                $sender = get_user_by('id',$logged_user_id);
		
		$sender_id = $sender->ID;		
		$receiver = get_user_by('id',$receiver_id);		
		
		//store in the db
		
		if(!($this->check_if_sent($receiver_id ))) {
		
			if(isset($logged_user_id) && $logged_user_id >0 )
			{
				
				$data = array(
							'friend_id'        => NULL,
							'friend_receiver_id'   => $receiver_id,						
							'friend_sender_user_id'   => $sender_id,
							'friend_status'   => '0',
							'friend_date'=> date('Y-m-d H:i:s')
							
							
						);
						
				// insert into database
				$wpdb->insert( $wpdb->prefix . 'usersultra_friends', $data, array( '%d', '%s', '%s', '%s',  '%s' ));
						
				$xoouserultra->messaging->send_friend_request($receiver ,$sender);
				
				
				//echo __(" Friend Request Sent ", 'xoousers');
                                echo "success";
				
			}else{
				
				echo __("You have to be logged in to send a friend request ", 'xoousers');
				
			
			}
		
		}else{
			
			//echo __("Request Already Sent", 'xoousers');
			echo "already_sent";
		
		}
		
		
		die();	
		
		
	}
	
	public function check_if_sent($friend_id) 
	{
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
		
		$sql = "SELECT friend_id FROM " . $wpdb->prefix . "usersultra_friends  WHERE friend_receiver_id  = '$friend_id' AND  friend_sender_user_id = '$logged_user_id'	 ";	 
		 
		 $res = $wpdb->get_results( $sql );
		 
		 if(empty($res))
		 {
			 return false; //first time
			
		 }else{
			 return true;
		
		}
	
	}
        public function check_if_friends($friend_id) 
	{
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
		
		$sql = "SELECT friend_id FROM " . $wpdb->prefix . "usersultra_friends  WHERE friend_receiver_id  = '$friend_id' AND  friend_sender_user_id = '$logged_user_id'	AND friend_status = 1 ";	 
		 
		 $res = $wpdb->get_results( $sql );
		 
		 if(empty($res))
		 {
			 return false; //first time
			
		 }else{
			 return true;
		
		}
	
	}
        
	
	public function like_item()
	{
		
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
		
		$item_id = $_POST["item_id"];		
		$module = $_POST["module"]; 
		$vote = $_POST["vote"];
		
		$already_voted = $this->check_already_voted($item_id, $module, $vote);
		
		if($already_voted==0)
		{
		
			//store in the db		
			if(isset($logged_user_id) && $logged_user_id >0)
			{
				//check if already liked it				
				$data = array(
							'like_id'        => NULL,
							'like_liked_id'   => $item_id,						
							'like_liker_user_id'   => $logged_user_id,
							'like_module'   => $module,
							'like_vote'   => $vote,
							'like_date'=> date('Y-m-d H:i:s')
							
							
						);
						
						// insert into database
				$wpdb->insert( $wpdb->prefix . 'usersultra_likes', $data, array( '%d', '%s', '%s', '%s', '%s',  '%s' ));
						
						
				
				echo __("Thanks ", 'xoousers');
				
			}else{
				
				echo __("Please login to rate ", 'xoousers');
				
			
			}
		}else{
			
			echo __("You've alredy liked it ", 'xoousers');
		
		
		}
		
		die();
		
		
		
	}
	
	public function get_friends($user_id)
	{
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$total = 0;		
		
		 $sql = "SELECT count(*) as total FROM " . $wpdb->prefix . "usersultra_friends  WHERE friend_receiver_id  = '$user_id' AND 	friend_status = 1 ";	 
		 
		 $res = $wpdb->get_results( $sql );
		 
		 if(!empty($res))
		 {
			  foreach ( $res as $like )
			 {
				$total = $like->total;				
			 }
			 
		
		  }else{
			  
			  $total = 0;  
		  
		  }
		  
		 if($total=="")$total = 0;	
		
		$html = "";
		
		$html.= "<div class='uultra-friend-request-box'>";
		$html.= "<p class='total_likes' id='uu-friends-total-id-".$user_id."'>". $total." ".__('Friends','xoousers')." </p>";	
		$html .= '<a class="uultra-btn-friend" id="uu-send-friend-request" href="#" user-id="'.$user_id.'" title="'.__('Send Friend Request','xoousers').'"><span><i class="fa fa fa-users fa-lg"></i> Be a Friend</span> </a>';
		
		$html.= "</div>";
		
		return $html;
	
	
	}
        
        /*Returns the friends list as a multi-dimensional array*/
        public function get_friends_list($user_id){
            global $wpdb,  $xoouserultra;		
            require_once(ABSPATH . 'wp-includes/formatting.php');

            $sql = "SELECT friend_sender_user_id FROM " . $wpdb->prefix . "usersultra_friends  WHERE friend_receiver_id  = '$user_id' AND 	friend_status = 1 ";	 

            $res = $wpdb->get_results( $sql );
            
            foreach($res as $r){
                $friends[] = $r->friend_sender_user_id;
            }
            
            return $friends;
        }
        
        public function get_friends_list_html($user_id=0){
            global $wpdb,  $xoouserultra;	
            
            if($user_id==0){$user_id = get_current_user_id();}
            
            require_once(ABSPATH . 'wp-includes/formatting.php');

            $sql = "SELECT friend_sender_user_id FROM " . $wpdb->prefix . "usersultra_friends  WHERE friend_receiver_id  = '$user_id' AND 	friend_status = 1 ";	 

            $res = $wpdb->get_results( $sql );
            $html = '';
            if(!empty($res)){
                    $html .= '<h2>My Friends</h2>';
                }
            foreach($res as $r){
                $friend_id = $r->friend_sender_user_id;
                //get photo link
                $friend_avatar_url = $xoouserultra->userpanel->get_user_pic_url( $friend_id, "150", 'avatar', 'rounded', 'dynamic');
                //get link to profile
                $friend_profile_url = site_url('/profile?uu_username='.$friend_id);
                //get first name and last initial
                $friend_first_name = $xoouserultra->userpanel->get_user_meta("first_name", $friend_id);
                $friend_last_name = $xoouserultra->userpanel->get_user_meta("last_name", $friend_id);;
                $friend_display_name = $friend_first_name." ".substr($friend_last_name,0,1).".";
                //get number of reviews
                $friend_reviews = BusinessDirectory_RatingsModule::get_reviews_by_user($friend_id);
                if(sizeof($friend_reviews)==1){
                    $friend_num_reviews = sizeof($friend_reviews)." Review";
                }else{
                    $friend_num_reviews = sizeof($friend_reviews)." Reviews";
                }
                
                //get number of favorites
                $friend_favorites = wpfp_get_users_favorites($r);
                if(is_array($friend_favorites) && sizeof($friend_favorites)==1){
                    $friend_num_favorites = sizeof($friend_favorites)." Favorite";
                }else{
                    $friend_num_favorites = is_array($friend_favorites)? sizeof($friend_favorites)." Favorites":"0 Favorites";
                }
                
                //display all these things
                
                $html .= "<div class='friend-display'>
                            <a href='".$friend_profile_url."'>    
                                <div style='background-image:url(".$friend_avatar_url.");' class='user-avatar-rounded user-avatar-small'></div>
                                ".$friend_display_name."
                            </a>
                            <div class='user-stats'>
                                <div class='user-stats-reviews'>
                                    <span>
                                        <i class='fa fa-fw fa-star star-on'></i>
                                        ".$friend_num_reviews."
                                    </span>
                                </div>
                                <div class='user-stats-favorites'>
                                    <span>
                                        <i class='fa fa-fw fa-heart heart-on'></i>
                                        ".$friend_num_favorites."
                                    </span>
                                </div>
                            </div>";
                $html .= "</div>";
    
                
            }
            
            return $html;
        }
	
	public function get_friend_count($user_id){
            global $wpdb,  $xoouserultra;		
            //require_once(ABSPATH . 'wp-includes/formatting.php');

            $total = 0;		

             $sql = "SELECT count(*) as total FROM " . $wpdb->prefix . "usersultra_friends  WHERE friend_receiver_id  = '$user_id' AND 	friend_status = 1 ";	 

             $res = $wpdb->get_results( $sql );

             if(!empty($res))
             {
                      foreach ( $res as $like )
                     {
                            $total = $like->total;				
                     }


              }else{

                      $total = 0;  

              }

             if($total=="")$total = 0;	


            return $total;
	
	
        }
	
	
	public function friend_request_action()
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
			
			//auto friend			
			
			$this->auto_friend($item_id);	
			
			
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
	
	public function auto_friend($item_id)
	{
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		//get friend request		
		 $sql = "SELECT * FROM " . $wpdb->prefix . "usersultra_friends  WHERE friend_id  = '$item_id' ";		 
		 $res = $wpdb->get_results( $sql );
		 
		 if(!empty($res))
		 {
			 foreach ( $res as $friend )
			 {
				$friend_receiver_id =	$friend->friend_receiver_id;
				$friend_sender_user_id =	$friend->friend_sender_user_id;					
					
			 } 
		
		  }	 
		
		$data = array(
		
				'friend_id'        => NULL,
				'friend_receiver_id'   => $friend_sender_user_id,						
				'friend_sender_user_id'   => $friend_receiver_id,
				'friend_status'   => '1',
				'friend_date'=> date('Y-m-d H:i:s')							
							
	 	);
						
		// insert into database
		$wpdb->insert( $wpdb->prefix . 'usersultra_friends', $data, array( '%d', '%s', '%s', '%s',  '%s' ));
			
	
	
	}
	
	public function get_item_likes($item_id, $module)
	{
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$total = 0;
		
		
		 $sql = "SELECT SUM(like_vote) as total FROM " . $wpdb->prefix . "usersultra_likes  WHERE like_liked_id  = '$item_id' AND like_module = '$module'";	 
		 
		 $res = $wpdb->get_results( $sql );
		 
		 if(!empty($res))
		 {
			 foreach ( $res as $like )
			 {
				$total = $like->total;				
			 }
			 
		
		  }else{
			  
			  $total = 0;  
		  
		  }
		  
		 if($total=="")$total = 0;	
		
		$html = "";
		
		$html.= "<div class='likebox'>";
		$html.= "<p class='total_likes' id='uu-like-sore-id-".$item_id."'>". $total." ".__('Likes','xoousers')." </p>";	
		$html .= '<a class="uultra-btn-like" id="uu-like-item" href="#" item-id="'.$item_id.'" data-module="'.$module.'" data-vote="1" title="'.__('Like','xoousers').'"><span><i class="fa fa fa-thumbs-o-up fa-lg"></i></span> </a> <a class="uultra-btn-like" id="uu-like-item" href="" title="'.__('Dislike','xoousers').'" item-id="'.$item_id.'" data-module="'.$module.'" data-vote="-1"><span><i class="fa fa fa-thumbs-o-down fa-lg"></i></span> </a>';
		
		$html.= "</div>";
		
		return $html;
	
	
	}
	
	public function get_item_likes_amount_only()
	{
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$item_id ="";
		$module = "";
		
		if(isset($_POST["item_id"]))
		{
			$item_id = $_POST["item_id"];
		}
		
		if(isset($_POST["module"]))
		{
			$module = $_POST["module"];
		}
		
		
		 $sql = "SELECT SUM(like_vote) as total FROM " . $wpdb->prefix . "usersultra_likes  WHERE like_liked_id  = '$item_id' AND like_module = '$module'";	 
		 
 
		 $res = $wpdb->get_results( $sql );
		 
		 if(!empty($res))
		 {
			  foreach ( $res as $like )
			 {
				$total = $like->total;				
			 }
			 
		
		  }else{
			  
			  $total = 0;  
		  
		  }		
		
				
		echo $total." ".__('Likes','xoousers');
		die();
	
	
	}
	
	public function check_already_voted($item_id, $module, $vote)
	{
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
		
		 $sql = "SELECT count(*) as total FROM " . $wpdb->prefix . "usersultra_likes  WHERE like_liked_id  = '$item_id' AND like_module = '$module' AND like_liker_user_id = '$logged_user_id' AND like_vote = '$vote' ";	 
		 
 
		 $res = $wpdb->get_results( $sql );
		 
		 if(!empty($res))
		 {
			  foreach ( $res as $like )
			 {
				$total = $like->total;				
			 }
			 
		
		  }else{
			  
			  $total = 0;  
		  
		  }		
		
				
		return $total;	
	
	
	}
	
	function show_friend_request()
		
	{
            global $xoouserultra;
            echo $xoouserultra->social->get_friend_request_list_html();
            die();
            
            /*
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();		
	
		$sql = ' SELECT friend.*, u.ID
		  
		  FROM ' . $wpdb->prefix . 'usersultra_friends friend  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users u ON ( u.ID = friend.friend_receiver_id)";
		
		$sql .= " WHERE u.ID = friend.friend_receiver_id  AND  friend.friend_status = 0 AND friend.friend_receiver_id = '".$user_id."'  ORDER BY friend.friend_id DESC ";	
		
		//echo $sql;
			
		$rows = $wpdb->get_results($sql);
		$html = " ";
		$html .='<div class="tablenav">	';	
		
		$html .= "<h3>".__('Latest Friend Requests','xoousers')."</h3> ";
	                        
                  
                    
		$html.='		</div>
	
				<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>						
                        <th class="manage-column" >'.__( 'Pic', 'xoousers' ).'</th>
						<th class="manage-column">'. __( 'Sender', 'xoousers' ).'</th>						
						<th class="manage-column" >'. __( 'Date', 'xoousers' ).'</th>
						<th class="manage-column" >'. __( 'Action', 'xoousers' ).'</th>
					</tr>
					</thead>
					<tbody>';
					
					
						
					foreach ( $rows as $msg )
					{
						$friend_sender_user_id = $msg->friend_sender_user_id;
						$request_id = $msg->friend_id;	
												
						$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE ID = '$msg->friend_sender_user_id'" );
							
							
					$html .= '<tr >
												
                             <td >'. $xoouserultra->userpanel->get_user_pic( $friend_sender_user_id, 50, 'avatar', 'rounded', 'dynamic' ).'</td>
                             
							<td>'.$msg->sender.'</td>
							
							<td>'.$msg->friend_date.'</td>
							
							<td><a class="uultra-btn-denyapprove" id="uu-approvedeny-friend" href="#" item-id="'.$request_id.'" action-id="approve" title="'.__('Approve','xoousers').'"><span><i class="fa fa fa-thumbs-o-up fa-lg"></i></span> Accept </a> <a class="uultra-btn-denyred" id="uu-approvedeny-friend" href="" title="'.__('Deny','xoousers').'" item-id="'.$request_id.'" action-id="deny"><span><i class="fa fa fa-thumbs-o-down fa-lg"></i></span> Deny </a></td>
						</tr>';
							
	
						}
						
						
						
					$html .='</tbody>
					
				</table>';
				
				echo $html;
				die();
                                */
			
	
	}
        
        function get_friend_request_list_html()
		
	{
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();		
	
		$sql = ' SELECT friend.*, u.ID FROM ' . $wpdb->prefix . 'usersultra_friends friend  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users u ON ( u.ID = friend.friend_receiver_id)";
		$sql .= " WHERE u.ID = friend.friend_receiver_id  AND  friend.friend_status = 0 AND friend.friend_receiver_id = '".$user_id."'  ORDER BY friend.friend_id DESC ";	
			
		$rows = $wpdb->get_results($sql);
		
                $html = '';
                if(!empty($rows)){
                    $html .= '<h2>Friend Requests</h2>';
                }				
                foreach ( $rows as $request )
                {
                    
                    $friend_id = $request->friend_sender_user_id;
                    $request_id = $request->friend_id;	

                    //$request->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE ID = '$request->friend_sender_user_id'" );

                    //$friend_id = $request->friend_sender_user_id;
                    
                    //get photo link
                    $friend_avatar_url = $xoouserultra->userpanel->get_user_pic_url( $friend_id, "150", 'avatar', 'rounded', 'dynamic');
                    //get link to profile
                    $friend_profile_url = site_url('/profile?uu_username='.$friend_id);
                    //get first name and last initial
                    $friend_first_name = $xoouserultra->userpanel->get_user_meta("first_name", $friend_id);
                    $friend_last_name = $xoouserultra->userpanel->get_user_meta("last_name", $friend_id);;
                    $friend_display_name = $friend_first_name." ".substr($friend_last_name,0,1).".";
                    //get number of reviews
                    $friend_reviews = BusinessDirectory_RatingsModule::get_reviews_by_user($friend_id);
                    $friend_num_reviews = sizeof($friend_reviews)==1? sizeof($friend_reviews)." Review": sizeof($friend_reviews)." Reviews";
                    
                    
                    $friend_friend_count = $this->get_friend_count($friend_id);
                    $friend_num_friends = $friend_friend_count.($friend_friend_count==1 ? " Friend": " Friends");
                    
                    
                    //get number of favorites
                    $friend_favorites = wpfp_get_users_favorites($r);
                    if(is_array($friend_favorites) && sizeof($friend_favorites)==1){
                        $friend_num_favorites = sizeof($friend_favorites)." Favorite";
                    }else{
                        $friend_num_favorites = is_array($friend_favorites)? sizeof($friend_favorites)." Favorites":"0 Favorites";
                    }
                    
                    
                    $html .= "
                                <div class='friend-request clearfix' id='request-container-".$request_id."'>
                                    <div class='col-md-3'>
                                    <a href='".$friend_profile_url."'>    
                                        <div style='background-image:url(".$friend_avatar_url.");' class='user-avatar-rounded user-avatar-small'></div>
                                    </a>
                                    </div>
                                    <div class='col-md-4 user-stats'>
                                        <a href='".$friend_profile_url."'>    
                                            ".$friend_display_name."
                                        </a>
                                        <div class='user-stats-reviews'>
                                            <span>
                                                <i class='fa fa-fw fa-star star-on'></i>
                                                ".$friend_num_reviews."
                                            </span>
                                        </div>
                                        <div class='user-stats-favorites'>
                                            <span>
                                                <i class='fa fa-fw fa-heart heart-on'></i>
                                                ".$friend_num_favorites."
                                            </span>
                                        </div>
                                        <div class='user-stats-friends'>
                                            <span>
                                                <i class='fa fa-fw icon-users'></i>
                                                ".$friend_num_friends."
                                            </span>
                                        </div>
                                    </div>
                                    <div class='col-md-4 friend-request-actions'>
                                            <button class='btn btn-primary' id='uu-approvedeny-friend' href='#' item-id='".$request_id."' action-id='approve' title='".__('Approve','xoousers')."'>
                                            <span><i class='fa fa fa-thumbs-o-up fa-lg'></i></span> Accept 
                                            </button> 
                                            <button class='btn btn-default' id='uu-approvedeny-friend' href='' title='".__('Deny','xoousers')."' item-id='".$request_id."' action-id='deny'>
                                            <span><i class='fa fa fa-thumbs-o-down fa-lg'></i></span> Deny 
                                            </button>
                                    </div>";
                    $html .= "</div>";

                }
						
		return $html;
	}
	
	function show_all_my_friends()		
	{
                global $xoouserultra;
                echo $xoouserultra->social->get_friends_list_html();
                die();
                /*
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();		
	
		$sql = ' SELECT friend.*, u.ID
		  
		  FROM ' . $wpdb->prefix . 'usersultra_friends friend  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users u ON ( u.ID = friend.friend_receiver_id)";
		
		$sql .= " WHERE u.ID = friend.friend_receiver_id  AND  friend.friend_status = 1 AND friend.friend_receiver_id = '".$user_id."'  ORDER BY friend.friend_id DESC ";	
		
		//echo $sql;
			
		$rows = $wpdb->get_results($sql);
		
		$html = " ";
		$html .='<div class="tablenav">	';	
		
		$html .= "<h3>".__('All My Friends','xoousers')."</h3> ";
	                        
                  
                    
		$html.='		</div>
	
				<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>						
                        <th class="manage-column" >'.__( 'Pic', 'xoousers' ).'</th>
						<th class="manage-column">'. __( 'Sender', 'xoousers' ).'</th>						
						<th class="manage-column" >'. __( 'Date', 'xoousers' ).'</th>
						<th class="manage-column" >'. __( 'Action', 'xoousers' ).'</th>
					</tr>
					</thead>
					<tbody>';
					
					
						
					foreach ( $rows as $msg )
					{
						$friend_sender_user_id = $msg->friend_sender_user_id;
						$request_id = $msg->friend_id;	
												
						$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE ID = '$msg->friend_sender_user_id'" );
							
							
					$html .= '<tr>
							
                             <td>'. $xoouserultra->userpanel->get_user_pic( $friend_sender_user_id, 50, 'avatar', 'rounded', 'dynamic' ).'</td>
                             
							<td>'.$msg->sender.'</td>
							
							<td>'.$msg->friend_date.'</td>
							
							<td><a class="uultra-btn-denyred" id="uu-approvedeny-friend" href="" title="'.__('Block','xoousers').'" item-id="'.$request_id.'" action-id="block"><span><i class="fa fa fa-thumbs-o-down fa-lg"></i></span> Block </a></td>
						</tr>';
							
	
						}
						
						
						
					$html .='</tbody>
					
				</table>';
				
				echo  $html;
				
				die();
		*/	
	
	}


}
$key = "social";
$this->{$key} = new XooSocial();