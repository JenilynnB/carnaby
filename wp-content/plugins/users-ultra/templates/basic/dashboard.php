<?php
global $xoouserultra;

$module = "";
$act= "";
$gal_id= "";
$page_id= "";
$view= "";
$reply= "";
$post_id ="";


if(isset($_GET["module"])){	$module = $_GET["module"];	}
if(isset($_GET["act"])){$act = $_GET["act"];	}
if(isset($_GET["gal_id"])){	$gal_id = $_GET["gal_id"];}
if(isset($_GET["page_id"])){	$page_id = $_GET["page_id"];}
if(isset($_GET["view"])){	$view = $_GET["view"];}
if(isset($_GET["reply"])){	$reply = $_GET["reply"];}
if(isset($_GET["post_id"])){	$post_id = $_GET["post_id"];}

$current_user = $xoouserultra->userpanel->get_user_info();

$user_id = $current_user->ID;
$user_email = $current_user->user_email;


$howmany = 5;


?>
<div class="usersultra-dahsboard-cont">

	<div class="usersultra-dahsboard-left"> 
   
    
      <div class="myavatar rounded">
        
      <div class="pic" id="uu-backend-avatar-section">
        
            <?php echo $xoouserultra->userpanel->get_user_pic( $user_id, "", 'avatar', 'rounded', 'dynamic')?>
            
           
            </div>
            
             <div class="btnupload">
             <a class="uultra-btn-upload-avatar" href="#" id="uu-send-private-message" data-id="<?php echo $user_id?>"><span><i class="fa fa-camera fa-2x"></i></span><?php echo _e("Update Profile Image", 'xoousers')?></a>
             </div>
             
            
           <div class="uu-upload-avatar-sect" id="uu-upload-avatar-box">           
            
            <?php echo $xoouserultra->userpanel->avatar_uploader()?>         
            
            </div>
            
                      
            
      </div>
         
           <ul class="main_menu">
           
              <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('dashboard');?></li>
              
               <?php if(!in_array("account",$modules)){?>  
               <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('account');?></li>
               <?php }?>
               
                <?php if(!in_array("myorders",$modules)){?>  
               <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('myorders');?></li>
               <?php }?>
               
                 <?php if(!in_array("wootracker",$modules)){?>  
               <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('wootracker');?></li>
               <?php }?>
               
               <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('profile');?></li>             
                              
               <?php if(!in_array("messages",$modules)){?>           
                
                <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('messages');?></li>
                
                <?php }?>
                
				<?php if(!in_array("friends",$modules)){?>                 
                <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('friends');?></li>
                <?php }?>
                
                <?php if(!in_array("posts",$modules)){?>                 
                <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('posts');?></li>
                <?php }?>
                
                <?php if(!in_array("photos",$modules)){?>                 
                <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('photos');?></li>           
                <?php }?>
                
                <?php if(!in_array("videos",$modules)){?> 
               <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('videos');?></li>
               
               
               <?php }?>
               <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('settings');?></li>
               <li><?php echo $xoouserultra->userpanel->get_user_backend_menu('logout');?></li>
           
           </ul>
    </div>
    
    
	<div class="usersultra-dahsboard-center"> 
    
        <?php
        //cutom message
		
		$message_custom = $xoouserultra->get_option('messaging_private_all_users');
		
		if($message_custom!="")
		{
			echo "<p><div class='uupublic-ultra-info'><p>".$message_custom."</p></div></p>";
		
		}
		
		?>
    
    
            <?php 
			
			//dashboard
			
			   if($module=="dashboard" ||$module==""  ) 
			   {
			?> 
            
               <h1>Hello <?php echo $current_user->display_name?>. <?php  _e('Welcome to your dashboard','xoousers');?></h1>     
               
               <p style="text-align:right"><?php  _e('Account Status','xoousers');?>: 	<?php echo $xoouserultra->userpanel->get_status($current_user->ID);?></p>    
               
               
                 <?php if(!in_array("messages",$modules)){?>  
             
              <div class="expandable-panel xoousersultra-shadow-borers" id="cp-1">
                                
                      <div class="expandable-panel-heading">
                              <h2><?php  _e('My Latest Messages','xoousers');?><span class="icon-close-open"></span></h2>
                     </div>
                     
                      <div class="expandable-panel-content" >
                     
                       	<?php  $xoouserultra->mymessage->show_usersultra_latest_messages($howmany);?>
                     
                     
                     </div>                    
                     
                     
               </div>
               
               <?php }?>

   		 
         
          <?php if(!in_array("photos",$modules)){?>  
              <div class="expandable-panel xoousersultra-shadow-borers" id="cp-2">
                                
                      <div class="expandable-panel-heading">
                              <h2>My Latest Photos<span class="icon-close-open"></span></h2>
                     </div>
                     
                      <div class="expandable-panel-content">
                     
                      <?php  echo $xoouserultra->photogallery->show_latest_photos_private(10);?>
                     
                     
                     </div>                    
                     
                     
               </div>
               
             <?php }?>   
              
              
        <?php }?>
        
         <?php
	   
	   //my friends
	   if($module=="friends" && !in_array("friends",$modules)) 
	   {
		   
		   
	   
	   ?>
       
	<div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                              <h2> <?php  _e('My Friends','xoousers');?> </h2>
                       </div>
                       
                       
                       <p class="paneldesc"><?php echo _e('Here you can manage your friends. Your friends will be able to see private/restricted conent','xoousers')?></p>
                     
                      <div class="commons-panel-content" id="uultra-my-friends-request">
                                            
                          <?php  _e('loading ...','xoousers');?>          
                       
                        
                       </div>
                       
                        <div class="commons-panel-content" id="uultra-my-friends-list">                        
                     
                                            
                                    <?php  _e('loading ...','xoousers');?>
                       
                        
                       </div>
                       
                       
                       <script type="text/javascript">
						jQuery(document).ready(function($){
							
							
								$.post(ajaxurl, {
									   
									   action: 'show_friend_request'
												
										}, function (response){									
																			
											$("#uultra-my-friends-request").html(response);
											//alert	(response);
											show_all_friends();										
																	
									});
								
						});				
						
						
						function show_all_friends()
						{
							$.post(ajaxurl, {
								   
								   action: 'show_all_my_friends'
											
									}, function (response){									
																		
										$("#uultra-my-friends-list").html(response);										
																
								});
							
						
						}
				
                    
                 </script>
                        
                        
                  
                             
                     
                     
               </div>
               
              
       <?php }?>
       
        <?php
	   
	   //my posts
	   if($module=="posts" && !in_array("posts",$modules)) 
	   {
		   
		   
	   
	   ?>
       
             <?php  if($act=="") {?> 
       
                    <div class="commons-panel xoousersultra-shadow-borers" >
                              <div class="commons-panel-heading">
                                      <h2> <?php  _e('My Posts','xoousers');?> </h2>
                               </div>
                               
                               
                               <p class="paneldesc"><?php echo _e('Here you can manage your posts. ','xoousers')?></p>
                             
                              <div class="commons-panel-content" >  
                              
                              <?php echo $xoouserultra->publisher->show_my_posts();?>
                              
                               </div>
                                              
                      
                           
                       </div>
               
                <?php }?>
               
                 <?php  if($act=="add") {?>                  
                       
                    <?php echo do_shortcode('[usersultra_front_publisher]');?>                   
                        
                 <?php }?>
                 
                  <?php  if($act=="edit") {?>                  
                       
                    <?php echo  $xoouserultra->publisher->edit_post($post_id);?>                   
                        
                 <?php }?>
                   
               
              
       <?php }?>
       
        <?php    
	    if($module=="myorders" && !in_array("myorders",$modules)) 
	   {
		   
	   ?>
       
             <?php  if($act=="") {?> 
       
                    <div class="commons-panel xoousersultra-shadow-borers" >
                              <div class="commons-panel-heading">
                                      <h2> <?php  _e('My Orders','xoousers');?> </h2>
                               </div>
                               
                               
                               <p class="paneldesc"><?php echo _e('Here you can check your orders. ','xoousers')?></p>
                             
                              <div class="commons-panel-content" >  
                              
                              <?php echo $xoouserultra->order->show_my_latest_orders(10);?>
                              
                               </div>
                                              
                      
                           
                       </div>
               
                <?php }?>
               
                                 
               
              
       <?php }?>
       
       <?php    
	    if($module=="wootracker" && !in_array("wootracker",$modules)) 
	   {
		   
		   
	   
	   ?>
       
             <?php  if($act=="") {?> 
       
                    <div class="commons-panel xoousersultra-shadow-borers" >
                              <div class="commons-panel-heading">
                                      <h2> <?php  _e('My Orders','xoousers');?> </h2>
                               </div>
                               
                               
                               <p class="paneldesc"><?php echo _e('Here you can track your orders. ','xoousers')?></p>
                             
                              <div class="commons-panel-content" >  
                              
                              <?php echo $xoouserultra->woocommerce->show_my_latest_orders(10);?>
                              
                               </div>
                                              
                      
                           
                       </div>
               
                <?php }?>
               
                                 
               
              
       <?php }?>
       
       
       
       <?php
	   
	   //my photos
	   if($module=="photos" && !in_array("photos",$modules)) 
	   {
		   
		   
	   
	   ?>
       
<div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                              <h2> <?php  _e('My Galleries','xoousers');?> / </h2>
                     </div>
                     
                      <div class="commons-panel-content">
                      
                      <p><?php  _e('Here you can manage your galleries and photos.','xoousers');?></p>
                      
                         <a  id="add_gallery"  href="#">Add Gallery</a>
                        <div class="gallery-list">
                        
                         <div class="add-new-gallery" id="new_gallery_div">
                         
                            <p><?php  _e('Name','xoousers');?>
                            <br />
                            
                            <input type="hidden" name="xoouserultra_current_gal"  id="xoouserultra_current_gal" />
                           <input type="text" class="xoouserultra-input" name="new_gallery_name" id="new_gallery_name" value=""> 
                           <?php  _e('Description','xoousers');?>
                           <br />
                            <textarea class="xoouserultra-input'" name="new_gallery_desc" id="new_gallery_desc" ></textarea>
                            </p>
                            
                            <div class="usersultra-btn-options-bar">
                            <a class="buttonize" href="#" id="close_add_gallery"><?php  _e('Cancel','xoousers');?></a>
                            <a class="buttonize green"  href="#" id="new_gallery_add"><?php  _e('Submit','xoousers');?></a>
                            
                            </div>
                        
                        
                         </div>
                        
                        
                                                  
                        <ul id="usersultra-gallerylist">
                                loading ..
                              
                             
                         </ul>
                          
                          </div>
                     
                     
                     </div>                    
                     
                     
               </div>
               
               <script type="text/javascript">
				jQuery(document).ready(function($){
					
					
					var page_id_val =   $('#page_id').val(); 
               
					   $.post(ajaxurl, {
									action: 'reload_galleries', 'page_id': page_id_val
									
									}, function (response){									
																
									$("#usersultra-gallerylist").html(response);
									
														
							});
							
					
				});
				
				 
				   var gallery_delete_confirmation_message = '<?php echo _e( 'Delete this gallery?', 'xoousers' ) ?>';			
                                   
                    
                 </script>
       
       <?php }?>
       
        
               <input type="hidden" value="<?php echo $page_id?>" name="page_id" id="page_id" />
       
        <?php
	   
	   //my photos
	   if($module=="photos-files" && !in_array("photos",$modules)) 
	   {
		   
		   //get selected gallery
		   $current_gal = $xoouserultra->photogallery->get_gallery($gal_id)
	   
	   ?>
       
       <div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                              <h2> <?php  _e('My Photos','xoousers');?> / <?php echo $current_gal->gallery_name?></h2>
                       </div>
                     
                      <div class="commons-panel-content">
                      
                      <p><?php  _e('Here you can manage your photos.','xoousers');?></p>
                      
                         <a  id="add_new_files"  href="#">Upload Files</a>
                        <div class="photo-list">                         
                        
                         <div class="res_sortable_container" id="resp_t_image_list">
						 
						 <?php $xoouserultra->photogallery->post_media_display($gal_id);?>                       
                         
                         </div>
                                                                                  
                                <ul id="usersultra-photolist" class="usersultra-photolist-private">
                                       <?php  _e('loading photos ...','xoousers');?>
                                      
                                     
                                 </ul>
                          
                          </div>
                     
                     
                     </div>                    
                     
                     
               </div>
               
              
               
                <script type="text/javascript">
				jQuery(document).ready(function($){
					
					
               
					   $.post(ajaxurl, {
									action: 'reload_photos', 'gal_id': '<?php echo $gal_id?>'
									
									}, function (response){									
																
									$("#usersultra-photolist").html(response);
									
														
							});
							
							
							
					
				});
                    
                 </script>
               
                     
      <?php }?>
      
       <?php
	   
	   
	   if($module=="profile" && !in_array("profile",$modules)) 
	   {
		   
		  
	   
	   ?>
       
       <div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                          <h2> <?php  _e('My Profile','xoousers');?> </h2>
                       </div>
                     
                      <div class="commons-panel-content">
                      
                       <?php echo $xoouserultra->userpanel->edit_profile_form();?>
                                         
                      </div>
                     
                     
          </div>
               
                
               
                     
      <?php }?>
      
      
     
      
            <?php if($module=="messages" && !in_array("messages",$modules)) 
	   {	  
	   
			   ?>
               
               
			  
			   <div class="commons-panel xoousersultra-shadow-borers" >
										
							  <div class="commons-panel-heading">
								  <h2> <?php  _e('Received Messages','xoousers');?> </h2>
							   </div>
							 
							  <div class="commons-panel-content">
							  
								   <div class="uultra-myprivate-messages">       
							  
									<?php  
									
									if(!$view && !$reply) 
									{
										$xoouserultra->mymessage->show_usersultra_my_messages();
									
									}
									
									if(isset($view) && $view>0) 
									{
										//display view box
										$xoouserultra->mymessage->show_view_my_message_form($view);
										
									
									}
									
									?>
							  
								   </div>
												 
							  </div>
							 
							 
				  </div>
                  
                  
                              
                
               
                     
      <?php }?>
      
      <?php if($module=="messages_sent" && !in_array("messages",$modules)) 
	   {	  
	   
			   ?>
               
               
			  
			   <div class="commons-panel xoousersultra-shadow-borers" >
										
							  <div class="commons-panel-heading">
								  <h2> <?php  _e('Sent Messages','xoousers');?> </h2>
							   </div>
							 
							  <div class="commons-panel-content">
							  
								   <div class="uultra-myprivate-messages">       
							  
									<?php  
									
									
										$xoouserultra->mymessage->show_usersultra_my_messages_sent();
									
																		
									?>
							  
								   </div>
												 
							  </div>
							 
							 
				  </div>
                  
                  
                              
                
               
                     
      <?php }?>
      
      <?php
     
       //my account
	   if($module=="account" && !in_array("account",$modules)) 
	   {
		   
		   
	   
	   ?>
       
		<div class="commons-panel xoousersultra-shadow-borers" >
        
        <div class="commons-panel-heading">
                              <h2> <?php  _e('My Account','xoousers');?>  </h2>
                     </div>
        
                    
                      <div class="commons-panel-content">
                       <h2> <?php  _e('Remove Account','xoousers');?>  </h2>
                      
                      <div class="uupublic-ultra-warning">WARNING! This action cannot be reverted.</div>
                      
                      <p><?php  _e('Here you can remove your account.','xoousers');?></p>
                       <form method="post" name="uultra-close-account" id="uultra-close-account">
                 			<input type="hidden" name="uultra-conf-close-account-post" value="ok" />
                             <p><input type="button" name="xoouserultra-register" id="xoouserultra-close-acc-btn" class="xoouserultra-button" value="<?php  _e('YES, CLOSE MY ACCOUNT','xoousers');?>" /></p>
               		  </form>
                      
                                           
                     </div>
                     
                     
                                          
               </div>
               
               <script type="text/javascript">
		
				 
				   var delete_account_confirmation_mesage = '<?php echo _e( 'Are you totally sure that you want to close your account. This action cannot be reverted?', 'xoousers' ) ?>';			
                                   
                    
                 </script>
       
       <?php }?>
       
       
        
	  <?php
	  //my settings
	   if($module=="settings") 
	   {
	   
	   ?>
       
		<div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                              <h2> <?php  _e('Settings','xoousers');?>  </h2>
                     </div>
                     
                     
                      <div class="commons-panel-content">
                       <h2> <?php  _e('Update Password','xoousers');?>  </h2>                     
                                           
                     
                       <form method="post" name="uultra-close-account" >
                       <p><?php  _e('Type your New Password','xoousers');?></p>
                 			 <p><input type="password" name="p1" id="p1" /></p>
                            
                             <p><?php  _e('Re-type your New Password','xoousers');?></p>
                 			 <p><input type="password"  name="p2" id="p2" /></p>
                            
                         <p><input type="button" name="xoouserultra-backenedb-eset-password" id="xoouserultra-backenedb-eset-password" class="xoouserultra-button" value="<?php  _e('CLICK HERE TO RESET PASSWORD','xoousers');?>" /></p>
                         
                         <p id="uultra-p-reset-msg"></p>
               		  </form>
                      
                       <h2> <?php  _e('Update Email','xoousers');?>  </h2> 
                                           
                     
                       <form method="post" name="uultra-change-email" >
                       <p><?php  _e('Type your New Email','xoousers');?></p>
                 			 <p><input type="text" name="email" id="email" value="<?php echo $user_email?>" /></p>
                                                        
                         <p><input type="button" name="xoouserultra-backenedb-update-email" id="xoouserultra-backenedb-update-email" class="xoouserultra-button" value="<?php  _e('CLICK HERE TO UPDATE YOUR EMAIL','xoousers');?>" /></p>
                         
                         <p id="uultra-p-changeemail-msg"></p>
               		  </form>
                      
                                           
                     </div>
                     
                                          
               </div>
               
               <script type="text/javascript">
		
				 
				   var delete_account_confirmation_mesage = '<?php echo _e( 'Are you totally sure that you want to close your account. This action cannot be reverted?', 'xoousers' ) ?>';			
                                   
                    
                 </script>
       
       <?php }?>
      
      
      <?php
	   
	   
	   if($module=="videos" && !in_array("videos",$modules)) 
	   {
		   
		  
	   
	   ?>
       
       <div class="commons-panel xoousersultra-shadow-borers" >
                                
                      <div class="commons-panel-heading">
                          <h2> <?php  _e('My Videos','xoousers');?> </h2>
                       </div>
                     
                      <div class="commons-panel-content">
                      
                      
                      
                      <p><?php  _e('Here you can manage your videos.','xoousers');?></p>
                      
                         <a  id="add_new_video"  href="#">Add Video</a>
                         
                          <div class="add-new-video" id="new_video_div">
                         
                            <p><?php  _e('Name','xoousers');?>
                            <br />
                            
                           
                           <input type="text" class="xoouserultra-input" name="new_video_name" id="new_video_name" value=""> 
                           
                           </p>
                           <p>
                           
                           <?php  _e('Video ID','xoousers');?>
                           <br />
                            <input type="text" class="xoouserultra-input" name="new_video_unique_vid" id="new_video_unique_vid" value=""> 
                            </p>
                            
                           
                             <p>
                           
                           <?php  _e('Video Type','xoousers');?>
                           <br />
                            <select  name="new_video_type" id="new_video_type" class="xoouserultra-input" >
                              <option value="youtube">Youtube</option>
                              <option value="vimeo">Vimeo</option>
                              dd</select>
                            </p>
                            
                            <div class="usersultra-btn-options-bar">
                            <a class="buttonize" href="#" id="close_add_video"><?php  _e('Cancel','xoousers');?></a>
                            <a class="buttonize green"  href="#" id="new_video_add_confirm"><?php  _e('Submit','xoousers');?></a>
                            
                            </div>  
                            
                         </div>       
                        <div class="video-list">     
                        
                        
                                    
                                                                       
                                <ul id="usersultra-videolist" class="usersultra-video-private">
                                       <?php  _e('loading videos ...','xoousers');?>
                                      
                                     
                                 </ul>
                          
                          </div>
                     
                     
                           
                                         
                      </div>         
                     
     
               
               <script type="text/javascript">
				jQuery(document).ready(function($){			
				
               
					   $.post(ajaxurl, {
									action: 'reload_videos'
									
									}, function (response){																
																
									$("#usersultra-videolist").html(response);
									
														
							});
							
					
				});
				
				 
				   var video_delete_confirmation_message = '<?php echo _e( 'Delete this video?', 'xoousers' ) ?>';			
				  var video_empy_field_name= '<?php echo _e( 'Pelase input a name', 'xoousers' ) ?>';
				  var video_empy_field_id= '<?php echo _e( 'Pelase input video ID', 'xoousers' ) ?>';			
                                   
                    
                 </script>  
               
                     
      <?php }?>


    </div>
    
   


</div>