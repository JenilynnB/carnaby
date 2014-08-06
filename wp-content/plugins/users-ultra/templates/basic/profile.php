<?php
global $xoouserultra;

?>
<div class="uultra-profile-basic-wrap" style="width:<?php echo $template_width?>">

<div class="commons-panel xoousersultra-shadow-borers" >

        <div class="uu-left">
        
        
           <div class="uu-main-pict "> 
           
             <h2><?php echo $xoouserultra->userpanel->get_display_name($current_user->ID);?></h2>
           
           
               <?php echo $xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type,  $pic_size_type)?>   
               
                 
                 
                   <?php if ($optional_fields_to_display!="") { ?>                 
                 
                   <?php echo $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display)?>                 
                
                  <?php } ?>        
                 
                  <?php if ($profile_fields_to_display=="all") { ?>                 
                 
                   <?php echo $xoouserultra->userpanel->get_profile_info( $user_id)?>                 
                
                  <?php } ?>   
                      
               
                
            
           
           </div>
           
           <p></p>        
                
       
        
            
        </div>
        
        
        <div class="uu-right">
        
        
         <?php if($display_private_message=="yes"){?>
        
         <div class="uu-options-bar">
         
             <div class="opt">
             
               <?php if($display_private_message=="yes"){?>
             
             <a class="uultra-btn-email" href="#" id="uu-send-private-message" data-id="<?php echo $user_id?>"><span><i class="fa fa-envelope-o"></i></span><?php echo _e("Send Message", 'xoousers')?></a>
             
                            
               <?php }?>
               
             </div>
         </div>
         
          <?php }?>
         
         
         
         
         <?php if($display_private_message=="yes"){?>
         
             <div class="uu-private-messaging rounded" id="uu-pm-box">
             
                 <?php echo $xoouserultra->mymessage->get_send_form( $user_id);?>
                 
                  <div id="uu-message-noti-id"></div>
             
             </div>
         
          <?php }?>
          
          
      <?php if(!in_array("photos",$modules)){?> 
        
       <?php if($photos_available){?>       
              
        <?php if($display_gallery){
			
			 //get selected gallery
		      $current_gal = $xoouserultra->photogallery->get_gallery_public($gal_id, $user_id);
			  
			  
			
			?>
            
              <?php if( $current_gal->gallery_name!=""){
				  
				  $xoouserultra->statistc->update_hits($gal_id, 'gallery');	
				  
				  ?>
            
              <h3><a href="<?php echo $xoouserultra->userpanel->get_user_profile_permalink( $user_id);?>"><?php echo _e("Main", 'xoousers')?></a>  / <?php echo $current_gal->gallery_name?></h3>
            
                <div class="photos">
             
                       <ul>
                          <?php echo $xoouserultra->photogallery->get_photos_of_gal_public($gal_id, $display_photo_rating, $gallery_type);?>
                       
                       </ul>
            
                </div>
            <?php }?>
        
        <?php }?>
        
        
        <?php if($display_photo)
		{
			
			  
			  $current_photo = $xoouserultra->photogallery->get_photo($photo_id, $user_id);		
			 
			 //get selected gallery
		      $current_gal = $xoouserultra->photogallery->get_gallery_public( $current_photo->photo_gal_id, $user_id);
			  
			 			
			?>
            
            <?php if( $current_gal->gallery_name!="" && $photo_id > 0){
				  
				  $xoouserultra->statistc->update_hits($photo_id, 'photo');	
				  
			 ?>
            
            
            
               <h3><a href="<?php echo $xoouserultra->userpanel->get_user_profile_permalink( $user_id);?>"><?php echo _e("Main", 'xoousers')?></a> /  <a href="<?php echo $xoouserultra->userpanel->public_profile_get_album_link( $current_gal->gallery_id, $user_id);?>"><?php echo $current_gal->gallery_name?></a></h3>
        
                  <div class="photo_single">
                 
                          
                       <?php echo $xoouserultra->photogallery->get_single_photo($photo_id, $user_id, $display_photo_rating, $display_photo_description);?>
                           
                
                  </div>
          
          
           <?php } //end if photo not empty?>
        
        
        <?php }?>
        
       
        
         <?php if(!$display_gallery && !$display_photo){?>
        
         <div class="photolist">
         
             <h2><?php echo _e("My Photo Galleries", 'xoousers')?></h2>
         
           <ul>
              <?php echo $xoouserultra->photogallery->reload_galleries_public($user_id);?>
           
           </ul>
        
         </div>
         
         
             <?php if(!in_array("videos",$modules)){?> 
          
                 <div class="videolist">
                 
                  <h2><?php echo _e("My Videos", 'xoousers')?></h2>
                 
                   <ul> 
                      <?php echo $xoouserultra->photogallery->reload_videos_public($user_id);?>
                   
                   </ul>
                
                 </div>
         
             <?php }?>  
             
             
            
         
         
         
          <?php }?>
          
                   
           <?php }else{?>           
                 
                 <?php echo _e("Photos available only for registered users", 'xoousers');?>           
           
            <?php }?>
            
            
           <?php } //end exclude?>
           
          
          
            
            
             <?php if ($optional_right_col_fields_to_display!="") { ?>                 
                 
                   <?php echo $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_right_col_fields_to_display)?>                 
                
           <?php } ?>   
         
              
             
    
    
        </div>
        
 </div>   

</div>