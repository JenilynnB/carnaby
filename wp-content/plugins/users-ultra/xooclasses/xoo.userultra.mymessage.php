<?php
class XooUserMyMessage {

	public $allowed_extensions;

	function __construct() 
	{
		//handle request
		if(isset($_GET["message_status"]) && $_GET["message_status"]>0)
		{
			//$this->change_message_status();
			
		}
		
		$this->ini_messagingsystem();
		
		add_action( 'wp_ajax_send_private_message',  array( $this, 'send_private_message' ));
		add_action( 'wp_ajax_reply_private_message',  array( $this, 'reply_private_message' ));
		add_action( 'wp_ajax_message_change_status',  array( $this, 'message_change_status' ));
		add_action( 'wp_ajax_message_delete',  array( $this, 'message_delete' ));
		
		

	}
	
	public function ini_messagingsystem()
	{
		global $wpdb;

			// Create table
			$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'users_ultra_pm (
				`id` bigint(20) NOT NULL auto_increment,
				`parent` bigint(20) NOT NULL default -1,
				`subject` text NOT NULL,
				`content` text NOT NULL,
				`sender` int(11) NOT NULL,
				`recipient` int(11) NOT NULL,
				`date` datetime NOT NULL,
				`readed` tinyint(1) NOT NULL,
				`deleted` tinyint(1) NOT NULL,
				PRIMARY KEY (`id`)
			) COLLATE utf8_general_ci;';
	
		// Note: deleted = 1 if message is deleted by sender, = 2 if it is deleted by recipient
	
		$wpdb->query( $query );
		
		$this->update_table();
		
	}
	
	function update_table()
	{
		global $wpdb;
		
		//2014-03-08 			
		$sql ='SHOW columns from ' . $wpdb->prefix . 'users_ultra_pm where field="parent" ';		
		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	//photo_desc
			$sql = 'Alter table  ' . $wpdb->prefix . 'users_ultra_pm add column parent bigint (20) default -1 ; ';
			$wpdb->query($sql);
		}
		
		//2014-05-02 			
		$sql ='SHOW columns from ' . $wpdb->prefix . 'users_ultra_pm where field="deleted_sender" ';		
		$rows = $wpdb->get_results($sql);		
		if ( empty( $rows ) )
		{	
			$sql = 'Alter table  ' . $wpdb->prefix . 'users_ultra_pm add column deleted_sender tinyint (1) default 0 ; ';
			$wpdb->query($sql);
		}
		
		
		
		
	}
	
	
	
	public function send_private_message()
	{
		
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
		
		$receiver_id =  sanitize_text_field($_POST["receiver_id"]);
		$uu_subject =   sanitize_text_field($_POST["uu_subject"]);
		$uu_message =   sanitize_text_field($_POST["uu_message"]);
		
		//get receiver
		
		$receiver = get_user_by('id',$receiver_id);		
		$sender = get_user_by('id',$logged_user_id);
		
		//store in the db
		
		if($receiver->ID >0)
		{
			
			$new_message = array(
						'id'        => NULL,
						'subject'   => $uu_subject,						
						'content'   => $uu_message,
						'sender'   => $logged_user_id,
						'recipient'   => $receiver_id,
						
						'date'=> date('Y-m-d H:i:s'),
						'readed'   => 0,
						'deleted'   => 0
						
					);
					
					// insert into database
					$wpdb->insert( $wpdb->prefix . 'users_ultra_pm', $new_message, array( '%d', '%s', '%s', '%s',  '%s', '%s', '%s' , '%s' ));
					
			
			$xoouserultra->messaging->send_private_message_user($receiver ,$sender->first_name,  $sender->last_name,$uu_subject,$_POST["uu_message"]);
			
			
		
		}
		
		echo "<div class='uupublic-ultra-success'>".__(" Message sent ", 'xoousers')."</div>";
		die();
		
		
		
	}
	
	public function reply_private_message()
	{
		
		global $wpdb,  $xoouserultra;		
		require_once(ABSPATH . 'wp-includes/formatting.php');
		
		$logged_user_id = get_current_user_id();
				
		
		$message_id =  sanitize_text_field($_POST["message_id"]);				
		$uu_message =   sanitize_text_field($_POST["uu_message"]);
		
		$message = $this->get_one($message_id, $logged_user_id);
		
		$uu_subject =   __("Re: ", 'xoousers')." ".$message->subject;
		
		//check if reply equal to sender
		$receiver_id = $message->sender;
		
		if($receiver_id==$logged_user_id)
		{
			
			$receiver_id = $message->recipient;
		
		
		}
		
		//get receiver
		
		$receiver = get_user_by('id',$receiver_id);		
		$sender = get_user_by('id',$logged_user_id);
		
		//store in the db
		
		if($receiver->ID >0)
		{
			
			$new_message = array(
						'id'        => NULL,
						'subject'   => $uu_subject,						
						'content'   => $uu_message,
						'sender'   => $logged_user_id,
						'recipient'   => $receiver_id,	
						'parent'   => $message->id,						
						'date'=> date('Y-m-d H:i:s'),
						'readed'   => 0,
						'deleted'   => 0
						
					);
					
					// insert into database
					$wpdb->insert( $wpdb->prefix . 'users_ultra_pm', $new_message, array( '%d', '%s', '%s', '%s',  '%s', '%s', '%s', '%s' , '%s' ));
					
			
			$xoouserultra->messaging->send_private_message_user($receiver ,$sender->first_name, $sender->last_name, $uu_subject,$_POST["uu_message"]);
			
			
		
		}
		
		echo "<div class='uupublic-ultra-success'>".__(" Reply sent ", 'xoousers')."</div>";
		die();
		
		
		
	}
	
	
	public function get_send_form($receiver_user_id, $in_modal=false){
		
		global $wpdb,  $xoouserultra;
		
		$logged_user_id = get_current_user_id();
		
		$html = "";
		
		
		if($logged_user_id>0 && $logged_user_id != "")
		{
			//is logged in.
			if($logged_user_id==$receiver_user_id)
			{
				//$html .= "<p>".__("You cannot send a private message to yourself", 'xoousers')."</p>";
                                $html .= "";
			
			}else{
                                  
                            $html .= '<div class="modal fade" id="sendMessageModal" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header no-border">
                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-danger fade-in" style="display:none" role="alert" id="msg-error-alert-text"></div>
                                                    <div class="alert alert-success fade-in" style="display:none" role="alert" id="msg-success-alert-text"></div>
                                                    <div id="uu_msg_form">
                                                        <p>Subject:</p>          
                                                        <p><input name="uu_subject"  id="uu_subject" type="text" /></p>

                                                        <p>Message:</p>          
                                                        <p><textarea name="uu_message" id="uu_message" cols="" rows=""></textarea></p>          
                                                        <p>
                                                     
                                                        <div class="modal-footer no-border">
                                                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                                                <i class="fa fa-times"></i>
                                                                Cancel
                                                            </button>
                                                            <button type="button" class="btn btn-primary" id="uu-send-private-message-confirm" data-id="'.$receiver_user_id.'">
                                                                <i class="fa fa-check"></i>
                                                                Send 
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                            
                            $html .='';
				  
				  $html .='<script type="text/javascript">
				  
				  var uu_subject_empty = "'.__("Please input a subject", 'xoousers').'";
				  var uu_message_empty = "'.__("Please write a message", 'xoousers').'";
								
				
                    
                                    </script>';
				
			
                        }
		}else{
                    
                    //Show login/register modal
                    $html .= get_registration_modal("Please create an account or login to send a message");
                    
                    //$html .= "<p>".__("You have to be logged in to send private messages", 'xoousers'. "</p>");
			
		
		}
		  
		  
		  echo $html;
		
		
	
	
	}
	
	public function get_unread_messages_amount($user_id) 
	{
		global $wpdb, $xoouserultra;
		
		$total = 0;
		

		$messages = $wpdb->get_results( 'SELECT  count(*) as total  FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `recipient` = '.$user_id.'  AND  `readed` ="0"  AND `deleted` <> "2" ' );
		
		foreach ( $messages as $message )
		{
			$total= $message->total;
							
		}
		
		return $total;
		
	
	}
	
	public function get_one($id, $receiver_id) 
	{
		global $wpdb, $xoouserultra;
		

		$messages = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `id` = ' . $id . ' AND (`recipient` = '.$receiver_id.' OR `sender` = '.$receiver_id.')  ' );
		
	//	echo 'SELECT *  FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `id` = ' . $id . ' AND (`recipient` = '.$receiver_id.' OR `sender` = '.$receiver_id.')  ';
		foreach ( $messages as $message )
		{
			return $message;
							
		}
		
	
	}
        
	public function message_delete()
	{
		
		global $wpdb,  $xoouserultra;
		
		$message_id = $_POST["message_id"];
		$logged_user_id = get_current_user_id();
		
		//$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `deleted` = '2' WHERE `id` = '$message_id' AND  `recipient` = '".$logged_user_id."' ";
		
		$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `deleted` = '2' WHERE `id` = '$message_id' AND  `recipient` = '".$logged_user_id."' ";
		
		$wpdb->query($sql);
		
		echo "<div class='uupublic-ultra-success'>".__(" The message has been deleted. Please refresh your screen.", 'xoousers')."</div>";
		die();
	
	}
	
	public function message_change_status()
	{
		
		global $wpdb,  $xoouserultra;
		
		$message_id = $_POST["message_id"];
		$message_status = $_POST["message_status"];
		
		$logged_user_id = get_current_user_id();
		
		$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `readed` = '".$message_status."' WHERE `id` = '$message_id' AND  `recipient` = '".$logged_user_id."' ";
		
		$wpdb->query($sql);
		
		$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `readed` = '".$message_status."' WHERE `parent` = '$message_id' AND  `recipient` = '".$logged_user_id."' ";
		
		$wpdb->query($sql);
		
		echo "<div class='uupublic-ultra-success'>".__(" Status has been changed", 'xoousers')."</div>";
		die();
	
	
	}
	
	//this mark as read parent and all replies	
	function update_read_status($message_id)
	{
		
		global $wpdb,  $xoouserultra;
		
		$logged_user_id = get_current_user_id();		
				
		$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `readed` = '1' WHERE `id` = '$message_id' AND  `recipient` = '".$logged_user_id."' ";		
		$wpdb->query($sql);
		
		//update all replies		
		$sql = "UPDATE " . $wpdb->prefix . "users_ultra_pm SET `readed` = '1' WHERE `parent` = '$message_id' AND  `recipient` = '".$logged_user_id."'";		
		$wpdb->query($sql);
		
		
	}
	
	
	/**
	 * Inbox page
	 */
	function show_view_my_message_form($message_id)
	{
		
		global $wpdb,  $xoouserultra;
		
		$logged_user_id = get_current_user_id();
		
		//chech if this is one of my messages		
		$messages = array();
                $messages[0] = $this->get_one($message_id, $logged_user_id);
                $replies = $this->get_parent_messages($message_id);
                foreach($replies as $reply){
                    array_push($messages, $reply);
                }
                ?>

                
                <div class="nav-links">
                    <a href="?module=messages" ><span><i class="fa fa-chevron-left"></i></span>Back to Inbox</a>
                </div>
                <!-- Back to Inbox Link  -->
                <!-- Subject display -->
                <div class="inbox-message-thread">
                        <div class="col-md-12 inbox-message-subject">
                            <span><?php echo $messages[0]->subject; ?></span>
                        </div>
                    
                <?php
                
                foreach($messages as $msg){
                    $message_sender_id = $msg->sender;
                    $msg_sender_name = $xoouserultra->userpanel->get_user_meta('first_name', $message_sender_id);
                    $orig_msg_date = date("F j, Y, g:i a", strtotime($msg->date));
                    
                    $my_message_class = "";
                    if($message_sender_id==$logged_user_id){
                        $my_message_class = "viewers-message";
                    }
                    $content = stripcslashes($msg->content);
                    
                    ?>

                        <div class="inbox-message <?php echo $my_message_class?>">
                                <div class="row">
                                    
                                </div>
                                <div class="row">
                                    <?php if($message_sender_id!=$logged_user_id){?>
                                        <div class="col-md-3 text-right">
                                            <div class="text-center">
                                                <?php 
                                                $user_image_url = $xoouserultra->userpanel->get_user_pic_url( $message_sender_id, "", 'avatar', 'rounded', 'dynamic');
                                                ?>
                                                <div style="background-image: url('<?php echo $user_image_url;?>')" class="user-avatar-rounded user-avatar-small"></div>
                                                <a href=""><?php echo $msg_sender_name; ?></a>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div>                                            
                                                <span><?php echo $content?></span>
                                            </div>
                                            <div class="pull-right">
                                                <span class="message-date pull-right"><?php echo $orig_msg_date; ?></span>
                                            </div>
                                        </div>
                                        <div class="md-3">

                                        </div>
                                    
                                    <?php }else{ ?>
                                        <div class="col-md-3">
                                        </div>
                                        <div class="col-md-6">
                                            <div>                                            
                                                <span><?php echo $content?></span>
                                            </div>
                                            <div class="pull-right">
                                                <span class="message-date pull-right"><?php echo $orig_msg_date; ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-left">
                                            <div class="text-center">
                                                <?php 
                                                $user_image_url = $xoouserultra->userpanel->get_user_pic_url( $message_sender_id, "", 'avatar', 'rounded', 'dynamic');
                                                ?>
                                                <div style="background-image: url('<?php echo $user_image_url;?>')" class="user-avatar-rounded user-avatar-small"></div>
                                                <a href=""><?php echo $msg_sender_name; ?></a>
                                            </div>
                                        </div>

                                        
                                    <?php }?>
                                    <!--
                                    <div class="col-md-3 text-center push-down-20">
                                        <a class="uultra-btn-deletemessage  uu-private-message-delete" href="#" id="" message-id="<?php echo $message_id?>" ><span><i class="icon-trash"></i></span>
                                    </a>
                                    -->
                                    </div>
                                </div>
                <?php
                }
                ?>
                
                <div class="alert alert-danger" id="inbox-msg-error" style="display:none;"><span>Please enter message text</span></div>
                <div class="uultra-reply-box"><textarea name="uu_message" class="uultra-reply-box-st" id="uu_message" cols="" rows="7" placeholder="Type your reply here..." ></textarea></div>
                <div class="pull-right">
                    <a class="btn btn-primary" href="#" id="uu-reply-private-message-confirm" message-id="<?php echo $messages[0]->id;?>"><span><i class="fa fa-reply"></i></span>&nbsp;&nbsp;Send Reply</a>
                </div>
                </div>
                <?php
                /*
                $message = $this->get_one($message_id, $logged_user_id);
		
                
		$message_sender_id = $message->sender;
		
		 $pic_boder_type = "";
		
				
		
		if($message != "")
		{
			$message_status = "";
			
			if($message->readed==1)
			{
				$message_status = __('Mark as Unread', 'xoousers');	
				$new_status = 0;		
			
			}else{
				
				$message_status = __('Mark as Read', 'xoousers');	
				$new_status = 1;
			
			
			}
			
			//mark as read
		
			$this->update_read_status($message_id);
                        
                        $orig_msg_date = date("F j, Y, g:i a", strtotime($message->date));
			
                        $content = stripslashes($message->content);
			
			//date
			//$orig_msg_date = date("F j, Y, g:i a", strtotime($message->date));
						
			//$content = stripslashes($message->content);
			$html  = ' <div class="uu-private-messaging-backend rounded" >';
			
						
			//main message
			$html .= '<ul class="replylist">'	;
			
			
			$html .= '<li class="rounded">'	;
			$html .= '<span class="uultra-u-avatar">'.$xoouserultra->userpanel->get_user_pic( $message_sender_id, 80, 'avatar', $pic_boder_type, 'fixed'). '</span>'	;
			
			$html .= '<div class="uultra-msg-content-box">
			
			<div class="uultra-msg-date">
			'.$orig_msg_date.'
			</div>
		     '.$content .''	;
			 
			$html .= '</div>'	;			
			$html .= '</li>'	;
			
			//replies
			
			$replies = $this->get_parent_messages($message->id);
			//echo print_r($replies);
			
			foreach ( $replies as $reply )
			{
				$receiver_id = $reply->recipient	;
				$sender_id = $reply->sender	;
				$reply_msg_date = date("F j, Y, g:i a", strtotime($reply->date));
				
				$content = stripslashes($reply->content);
				
				$html .= '<li class="rounded replybox">'	;
				$html .= '<span class="uultra-u-avatar">'.$xoouserultra->userpanel->get_user_pic( $sender_id, 80, 'avatar', $pic_boder_type, 'fixed'). '</span>'	;
				
				$html .= '<div class="uultra-msg-content-box">
				
				<div class="uultra-msg-date">
				'.$reply_msg_date.'
				</div>
				 '.$content .''	;
				 
				$html .= '</div>'	;			
				$html .= '</li>'	;
			
			}		
					
			 
			$html .='<li class="mbsbox rounded"><div class="uultra-reply-box"><textarea name="uu_message" class="uultra-reply-box-st" id="uu_message" cols="" rows="7" placeholder="'. __("Type your reply here...", 'xoousers').'" ></textarea></div></li>';
				
			$html .= '</ul>'		;	  
					  					  
			$html .='	           
					   
					  
					          
					  <p><a class="uultra-btn-email" href="#" id="uu-close-private-message-box" data-id="'.$receiver_id.'"><span><i class="fa fa-chevron-left"></i></span>'. __("Back", 'xoousers').'</a>
					   <a class="uultra-btn-email" href="#" id="uu-reply-private-message-confirm" message-id="'.$message->id.'"><span><i class="fa fa-reply"></i></span>'. __("Send Reply", 'xoousers').'</a>
					   
					 					  
			</p>
					  ';
					  
					  
			 
			 		 
			 $html .='<div id="uu-message-noti-id"></div>';
			 
			 $html  .= '</div>';
				  
			 $html .='<script type="text/javascript">
					  
					 	   var uu_message_empty = "'.__("Please write a message", 'xoousers').'";
						
					 </script>';
		   
		}else{
			
			$html .= 'Error'; 
	   
		}
		
		 echo $html;
		
		*/
	}
	
	function get_parent_messages($message_id)
	{
		global $wpdb, $current_user, $xoouserultra;
		
		//current user login
		$user_id = get_current_user_id();
					
		// show all parent messages
		$msgs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE  `parent` = "'.$message_id.'" ORDER BY `date` ASC' );
		
		return $msgs;
	
	}
	
	/**
	 * Inbox page
	 */
	function show_usersultra_my_messages()
	{
            global $wpdb, $current_user, $xoouserultra;

            $user_id = get_current_user_id();


            // show all messages which have not been deleted by this user (deleted status != 2)
            $msgs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `recipient` = "' . $user_id. '"  AND `deleted` != "2"   ORDER BY `date` DESC' );


            ?>
                
            <ul class="nav nav-pills">
                <li role="presentation" class="active">
                    <a href="?module=messages">Inbox</a>
                </li>
                <li role="presentation">
                    <a href="?module=messages_sent">Sent Messages</a>
                </li>
            </ul>

                 
            <?php
            if ( !empty( $status ) )
            {
                    echo '<div id="message" class="updated fade"><p>', $status, '</p></div>';
            }
            if ( empty( $msgs ) )
            {
                    echo '<div class="inbox-message push-down-20">', __( 'You have no messages.', 'xoousers' ), '</div>';
            }
            else
            {
                $n = count( $msgs );
                $num_unread = 0;
                foreach ( $msgs as $msg )
                {
                    if ( !( $msg->readed ) )
                    {
                            $num_unread++;
                    }
                }


                //echo '<span style="float:right">', sprintf( _n( 'You have %d private message (%d unread).', 'You have %d private messages (%d unread)', $n, 'xoousers' ), $n, $num_unread ), '</span>'     ;    

            ?>
            
            
		
						
            <form action="" method="get">
                <?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
                <input type="hidden" name="page" value="usersultra_inbox" />

                <div class="inbox-messages-list">
                        <?php
                        foreach ( $msgs as $msg )
                        {
                            
                            $user_id = $msg->sender;

                            //$msg_sender_name = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE ID = '$msg->sender'" );
                            $msg_sender_name = $xoouserultra->userpanel->get_user_meta('first_name', $user_id)." ".substr($xoouserultra->userpanel->get_user_meta('last_name', $user_id),0,1).".";
                            //main conversation id		

                            $message_id =		$msg->id;			
                            if($msg->parent=='-1')
                            {
                                $conversa_id = $msg->id;

                            }else{
                                $conversa_id = $msg->parent;
                            }

                            $message_status = "";
/*
                            if($msg->readed==1)
                            {
                                $message_status ='fa-eye';	
                                $message_status_text = __('Mark as Unread', 'xoousers');	
                                $new_status = 0;		

                            }else{

                                $message_status ='fa-eye-slash';
                                $message_status_text = __('Mark as Read', 'xoousers');		
                                $new_status = 1;
                            }
*/
                            $read_class="";
                            if($msg->readed==0)
                            {
                                    $read_class="uultra-unread-message";
                            }


                            ?>
                            <div class="inbox-message <?php echo $read_class?>">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php $msg_date = date_i18n('F j, Y', strtotime($msg->date));?>
                                        
                                        <span class="message-date pull-right"><?php echo $msg_date; ?></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 text-center">
                                        <?php 
                                        $user_image_url = $xoouserultra->userpanel->get_user_pic_url( $user_id, "", 'avatar', 'rounded', 'dynamic');
                                        ?>
                                        <div style="background-image: url('<?php echo $user_image_url;?>')" class="user-avatar-rounded user-avatar-small"></div>
                                        <a href=""><?php echo $msg_sender_name; ?></a>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="">
                                            <a href="<?php echo $xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id) ?>">
                                                <b><?php  echo stripcslashes( $msg->subject ) ?></b>
                                            </a>
                                        </div>
                                        <div>
                                            <?php 
                                            $newline_break = strpos(wordwrap($msg->content, 100), "\n");
                                            if($newline_break>0){
                                                $msg_truncated = substr(stripslashes($msg->content), 0, $newline_break)."...";
                                            }else{
                                                $msg_truncated = stripslashes($msg->content);
                                            }
                                            ?>
                                            <span><?php echo $msg_truncated?></span>
                                        </div>
                                        <div class="row-actions pull-right">
                                            <span>
                                                <a href="<?php echo $xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id) ?>"><?php _e( 'More...', 'xoousers' ); ?></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center push-down-20">
                                        <a class="uultra-btn-deletemessage  uu-private-message-delete" href="#" id="" message-id="<?php echo $message_id?>" ><span><i class="icon-trash"></i></span>
                                    </a>
                                    </div>
                                </div>
                                
                            </div>
                        <?php
                        }
                        ?>
                </div>
            </form>
			<?php
	
		}
		

	
	}
	
	/**
	 * Outbox page
	 */
	function show_usersultra_my_messages_sent()
	{
            global $wpdb, $current_user, $xoouserultra;

            $user_id = get_current_user_id();


            // show all messages which have not been deleted by this user (deleted status != 2)
            $msgs = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `sender` = "' . $user_id. '"  AND `deleted` != "2"   ORDER BY `date` DESC' );

            ?>
                
            <ul class="nav nav-pills">
                <li role="presentation">
                    <a href="?module=messages">Inbox</a>
                </li>
                <li role="presentation" class="active">
                    <a href="?module=messages_sent">Sent Messages</a>
                </li>
            </ul>
	
                   
            <?php
            if ( !empty( $status ) )
            {
                    echo '<div id="message" class="updated fade"><p>', $status, '</p></div>';
            }
            if ( empty( $msgs ) )
            {
                    echo '<div class="inbox-message push-down-20">', __( 'You have no sent messages.', 'xoousers' ), '</p>';
            }
            else
            {
                    $n = count( $msgs );
                    $num_unread = 0;
                    foreach ( $msgs as $msg )
                    {
                            if ( !( $msg->readed ) )
                            {
                                    $num_unread++;
                            }
                    }


                ?>

                <form action="" method="get">
                    <?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
                    <input type="hidden" name="page" value="usersultra_inbox" />

                    <div class="inbox-messages-list">
                        <?php
                        foreach ( $msgs as $msg )
                        {
                            $user_id = $msg->sender;
                            //$msg_sender_name = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE ID = '$msg->sender'" );
                            $msg_sender_name = $xoouserultra->userpanel->get_user_meta('first_name', $user_id)." ".substr($xoouserultra->userpanel->get_user_meta('last_name', $user_id),0,1).".";
                            

                            //main conversation id		

                            $message_id =		$msg->id;			
                            if($msg->parent=='-1')
                            {
                                $conversa_id = $msg->id;

                            }else{
                                $conversa_id = $msg->parent;
                            }
/*
                            $message_status = "";

                            if($msg->readed==1)
                            {
                                $message_status ='fa-eye';	
                                $message_status_text = __('Mark as Unread', 'xoousers');	
                                $new_status = 0;		

                            }else{

                                $message_status ='fa-eye-slash';
                                $message_status_text = __('Mark as Read', 'xoousers');		
                                $new_status = 1;
                            }

                            $read_class="";
                            if($msg->readed==0)
                            {
                                    $read_class="uultra-unread-message";
                            }
*/

                            ?>
                            <div class="inbox-message">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php $msg_date = date_i18n('F j, Y', strtotime($msg->date));?>

                                        <span class="message-date pull-right"><?php echo $msg_date; ?></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 text-center">
                                        <?php 
                                        $user_image_url = $xoouserultra->userpanel->get_user_pic_url( $user_id, "", 'avatar', 'rounded', 'dynamic');
                                        ?>
                                        <div style="background-image: url('<?php echo $user_image_url;?>')" class="user-avatar-rounded user-avatar-small"></div>
                                        <a href=""><?php echo $msg_sender_name; ?></a>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="">
                                            <a href="<?php echo $xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id) ?>">
                                                <b><?php  echo stripcslashes( $msg->subject ) ?></b>
                                            </a>
                                        </div>
                                        <div>
                                            <?php 
                                            $newline_break = strpos(wordwrap($msg->content, 100), "\n");
                                            if($newline_break>0){
                                                $msg_truncated = substr(stripslashes($msg->content), 0, $newline_break)."...";
                                            }else{
                                                $msg_truncated = stripslashes($msg->content);
                                            }
                                            ?>
                                            <span><?php echo $msg_truncated?></span>
                                        </div>
                                        <div class="row-actions pull-right">
                                            <span>
                                                <a href="<?php echo $xoouserultra->userpanel->get_internal_pmb_links('messages','view',$conversa_id) ?>"><?php _e( 'More...', 'xoousers' ); ?></a>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center push-down-20">
                                        <a class="uultra-btn-deletemessage  uu-private-message-delete" href="#" id="" message-id="<?php echo $message_id?>" ><span><i class="icon-trash"></i></span>
                                    </a>
                                    </div>
                                </div>

                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </form>
                <?php
	
            }
		

	
	}
	
	/**
	 * Inbox page
	 */
        /*
	function show_usersultra_latest_messages($howmany)
		
	{
		global $wpdb, $current_user, $xoouserultra;
		
		$user_id = get_current_user_id();
		
	
		// show all messages which have not been deleted by this user (deleted status != 2)
		$msgs = $wpdb->get_results( 'SELECT  * FROM ' . $wpdb->prefix . 'users_ultra_pm WHERE `recipient` = "' . $user_id. '"  AND `deleted` != "2"  ORDER BY `date` DESC LIMIT  '.$howmany.'  '  );
		
		
		
		?>
	
		
		<?php
		if ( !empty( $status ) )
		{
			echo '<div id="message" class="updated fade"><p>', $status, '</p></div>';
		}
		if ( empty( $msgs ) )
		{
			echo '<p>', __( 'You have no items in inbox.', 'xoousers' ), '</p>';
		}
		else
		{
			$n = count( $msgs );
			$num_unread = 0;
			foreach ( $msgs as $msg )
			{
				if ( !( $msg->readed ) )
				{
					$num_unread++;
				}
			}
			
			?>
			<form action="" method="get">
				<?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
				<input type="hidden" name="page" value="usersultra_inbox" />
	
				<div class="tablenav">
					                    
                    <?php echo '<span style="float:right">', sprintf( _n( 'You have %d private message (%d unread)', 'You have %d private messages (%d unread)', $n, 'xoousers' ), $n, $num_unread ), '</span>'; ?>
                    
				</div>
	
				<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>
						
                        <th class="manage-column" ><?php _e( 'Pic', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Sender', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Subject', 'xoousers' ); ?></th>
						<th class="manage-column" ><?php _e( 'Date', 'xoousers' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $msgs as $msg )
						{
							$user_id = $msg->sender;
							$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE ID = '$msg->sender'" );
							
							//main conversation id							
							if($msg->parent=='-1')
							{
								$conversa_id = $msg->id;
							
							
							}else{
								
								$conversa_id = $msg->parent;
								
								
							}
							
							$read_class="";
							if($msg->readed==0)
							{
								
								$read_class="class='uultra-unread-message'";
								
							}
							
							?>
						<tr <?php echo $read_class?> >
							
                             <td>
							 
							 <span class="uultra-u-avatar"><?php echo $xoouserultra->userpanel->get_user_pic( $user_id, 50, 'avatar', null, null) ?></span>						 
							</td>
                             
							<td><?php echo $msg->sender; ?></td>
							<td>
								<?php
								
									echo '<a href="'. $xoouserultra->userpanel->get_internal_pmb_links("messages","view",$conversa_id).'">'. stripcslashes( $msg->subject ). '</a>';
								
								?>
								
							</td>
							<td><?php echo $msg->date; ?></td>
						</tr>
							<?php
	
						}
						?>
					</tbody>
					
				</table>
			</form>
			<?php
	
		}
		?>

	<?php
	}
	
	/**
	 * Inbox page
	 */
        /*
	function show_usersultra_inbox()
	{
		global $wpdb, $current_user;
		
		$user_id = get_current_user_id();
	
	// if view message
		if ( isset( $_GET['action'] ) && 'view' == $_GET['action'] && !empty( $_GET['id'] ) )
		{
			$id = $_GET['id'];
	
			check_admin_referer( "usersultra-view_inbox_msg_$id" );
	
			// mark message as read
			$wpdb->update( $wpdb->prefix . 'pm', array( 'readed' => 1 ), array( 'id' => $id ) );
	
			// select message information
			$msg = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->prefix . 'pm WHERE `id` = "' . $id . '" LIMIT 1' );
			$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE user_login = '$msg->sender'" );
			?>
		<div class="xoouserultra-main">
			<h2><?php _e( 'Inbox \ View Message', 'xoousers' ); ?></h2>
	
			<p><a href="?page=usersultra_inbox"><?php _e( 'Back to inbox', 'xoousers' ); ?></a></p>
			<table class="widefat fixed" cellspacing="0">
				<thead>
				<tr>
					<th class="manage-column" width="20%"><?php _e( 'Info', 'xoousers' ); ?></th>
					<th class="manage-column"><?php _e( 'Message', 'xoousers' ); ?></th>
					<th class="manage-column" width="15%"><?php _e( 'Action', 'xoousers' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><?php printf( __( '<b>Sender</b>: %s<br /><b>Date</b>: %s', 'xoousers' ), $msg->sender, $msg->date ); ?></td>
					<td><?php printf( __( '<p><b>Subject</b>: %s</p><p>%s</p>', 'xoousers' ), stripcslashes( $msg->subject ) , nl2br( stripcslashes( $msg->content ) ) ); ?></td>
					<td>
							<span class="delete">
								<a class="delete"
									href="<?php echo wp_nonce_url( "?page=usersultra&action=delete&id=$msg->id", 'usersultra-delete_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Delete', 'xoousers' ); ?></a>
							</span>
							<span class="reply">
								| <a class="reply"
								href="<?php echo wp_nonce_url( "?page=usersultra_send&recipient=$msg->sender&id=$msg->id&subject=Re: " . stripcslashes( $msg->subject ), 'usersultra-reply_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Reply', 'xoousers' ); ?></a>
							</span>
					</td>
				</tr>
				</tbody>
				<tfoot>
				<tr>
					<th class="manage-column" width="20%"><?php _e( 'Info', 'xoousers' ); ?></th>
					<th class="manage-column"><?php _e( 'Message', 'xoousers' ); ?></th>
					<th class="manage-column" width="15%"><?php _e( 'Action', 'xoousers' ); ?></th>
				</tr>
				</tfoot>
			</table>
		</div>
		<?php
	// don't need to do more!
			return;
		}
	
		// if mark messages as read
		if ( isset( $_GET['action'] ) && 'mar' == $_GET['action'] && !empty( $_GET['id'] ) )
		{
			$id = $_GET['id'];
	
			if ( !is_array( $id ) )
			{
				check_admin_referer( "usersultra-mar_inbox_msg_$id" );
				$id = array( $id );
			}
			else
			{
				check_admin_referer( "usersultra-bulk-action_inbox" );
			}
			$n = count( $id );
			$id = implode( ',', $id );
			if ( $wpdb->query( 'UPDATE ' . $wpdb->prefix . 'pm SET `readed` = "1" WHERE `id` IN (' . $id . ')' ) )
			{
				$status = _n( 'Message marked as read.', 'Messages marked as read', $n, 'xoousers' );
			}
			else
			{
				$status = __( 'Error. Please try again.', 'xoousers' );
			}
		}
	
		// if delete message
		if ( isset( $_GET['action'] ) && 'delete' == $_GET['action'] && !empty( $_GET['id'] ) )
		{
			$id = $_GET['id'];
	
			if ( !is_array( $id ) )
			{
				check_admin_referer( "usersultra-delete_inbox_msg_$id" );
				$id = array( $id );
			}
			else
			{
				check_admin_referer( "usersultra-bulk-action_inbox" );
			}
	
			$error = false;
			foreach ( $id as $msg_id )
			{
				// check if the sender has deleted this message
				$sender_deleted = $wpdb->get_var( 'SELECT `deleted` FROM ' . $wpdb->prefix . 'pm WHERE `id` = "' . $msg_id . '" LIMIT 1' );
	
				// create corresponding query for deleting message
				if ( $sender_deleted == 1 )
				{
					$query = 'DELETE from ' . $wpdb->prefix . 'pm WHERE `id` = "' . $msg_id . '"';
				}
				else
				{
					$query = 'UPDATE ' . $wpdb->prefix . 'pm SET `deleted` = "2" WHERE `id` = "' . $msg_id . '"';
				}
	
				if ( !$wpdb->query( $query ) )
				{
					$error = true;
				}
			}
			if ( $error )
			{
				$status = __( 'Error. Please try again.', 'xoousers' );
			}
			else
			{
				$status = _n( 'Message deleted.', 'Messages deleted.', count( $id ), 'xoousers' );
			}
		}
	
		// show all messages which have not been deleted by this user (deleted status != 2)
		$msgs = $wpdb->get_results( 'SELECT `id`, `sender`, `subject`, `readed`, `date` FROM ' . $wpdb->prefix . 'pm WHERE `recipient` = "' . $user_id. '" AND `deleted` != "2" ORDER BY `date` DESC' );
		
		?>
	<div class="wrap">
		<h2><?php _e( 'Inbox', 'xoousers' ); ?></h2>
		<?php
		if ( !empty( $status ) )
		{
			echo '<div id="message" class="updated fade"><p>', $status, '</p></div>';
		}
		if ( empty( $msgs ) )
		{
			echo '<p>', __( 'You have no items in inbox.', 'xoousers' ), '</p>';
		}
		else
		{
			$n = count( $msgs );
			$num_unread = 0;
			foreach ( $msgs as $msg )
			{
				if ( !( $msg->readed ) )
				{
					$num_unread++;
				}
			}
			echo '<p>', sprintf( _n( 'You have %d private message (%d unread).', 'You have %d private messages (%d unread).', $n, 'xoousers' ), $n, $num_unread ), '</p>';
			?>
			<form action="" method="get">
				<?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
				<input type="hidden" name="page" value="usersultra_inbox" />
	
				<div class="tablenav">
					<select name="action">
						<option value="-1" selected="selected"><?php _e( 'Bulk Action', 'xoousers' ); ?></option>
						<option value="delete"><?php _e( 'Delete', 'xoousers' ); ?></option>
						<option value="mar"><?php _e( 'Mark As Read', 'xoousers' ); ?></option>
					</select> <input type="submit" class="button-secondary" value="<?php _e( 'Apply', 'xoousers' ); ?>" />
				</div>
	
				<table class="widefat fixed" cellspacing="0">
					<thead>
					<tr>
						<th class="manage-column check-column"><input type="checkbox" /></th>
						<th class="manage-column" width="10%"><?php _e( 'Sender', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Subject', 'xoousers' ); ?></th>
						<th class="manage-column" width="20%"><?php _e( 'Date', 'xoousers' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $msgs as $msg )
						{
							$msg->sender = $wpdb->get_var( "SELECT display_name FROM $wpdb->users WHERE user_login = '$msg->sender'" );
							?>
						<tr>
							<th class="check-column"><input type="checkbox" name="id[]" value="<?php echo $msg->id; ?>" />
							</th>
							<td><?php echo $msg->sender; ?></td>
							<td>
								<?php
								if ( $msg->readed )
								{
									echo '<a href="', wp_nonce_url( "?page=usersultra_inbox&action=view&id=$msg->id", 'usersultra-view_inbox_msg_' . $msg->id ), '">', stripcslashes( $msg->subject ), '</a>';
								}
								else
								{
									echo '<a href="', wp_nonce_url( "?page=usersultra_inbox&action=view&id=$msg->id", 'usersultra-view_inbox_msg_' . $msg->id ), '"><b>', stripcslashes( $msg->subject ), '</b></a>';
								}
								?>
								<div class="row-actions">
								<span>
									<a href="<?php echo wp_nonce_url( "?page=usersultra_inbox&action=view&id=$msg->id", 'usersultra-view_inbox_msg_' . $msg->id ); ?>"><?php _e( 'View', 'xoousers' ); ?></a>
								</span>
									<?php
									if ( !( $msg->readed ) )
									{
										?>
										<span>
									| <a href="<?php echo wp_nonce_url( "?page=usersultra_inbox&action=mar&id=$msg->id", 'usersultra-mar_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Mark As Read', 'xoousers' ); ?></a>
								</span>
										<?php
	
									}
									?>
									<span class="delete">
									| <a class="delete"
										href="<?php echo wp_nonce_url( "?page=usersultra_inbox&action=delete&id=$msg->id", 'ulsersultra-delete_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Delete', 'xoousers' ); ?></a>
								</span>
								<span class="reply">
									| <a class="reply"
									href="<?php echo wp_nonce_url( "?page=usersultra_send&recipient=$msg->sender&id=$msg->id&subject=Re: " . stripcslashes( $msg->subject ), 'usersultra-reply_inbox_msg_' . $msg->id ); ?>"><?php _e( 'Reply', 'xoousers' ); ?></a>
								</span>
								</div>
							</td>
							<td><?php echo $msg->date; ?></td>
						</tr>
							<?php
	
						}
						?>
					</tbody>
					<tfoot>
					<tr>
						<th class="manage-column check-column"><input type="checkbox" /></th>
						<th class="manage-column"><?php _e( 'Sender', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Subject', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Date', 'xoousers' ); ?></th>
					</tr>
					</tfoot>
				</table>
			</form>
			<?php
	
		}
		?>
	</div>
	<?php
	}
	*/

}
$key = "mymessage";
$this->{$key} = new XooUserMyMessage();