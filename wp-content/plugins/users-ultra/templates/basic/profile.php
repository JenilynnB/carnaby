<?php
global $xoouserultra;

?>

<?php
    $first_name = $xoouserultra->userpanel->get_user_meta('first_name');
    $last_name = $xoouserultra->userpanel->get_user_meta('last_name');
    $headline = $xoouserultra->userpanel->get_user_meta('headline');
    $location = $xoouserultra->userpanel->get_user_meta('location');
    $headline = $xoouserultra->userpanel->get_user_meta('headline');
    $description = $xoouserultra->userpanel->get_user_meta('description');
    $loves = $xoouserultra->userpanel->get_user_meta('loves');
    $website = $xoouserultra->userpanel->get_user_meta('user_url');
    $facebook = $xoouserultra->userpanel->get_user_meta('facebook');
    $instagram = $xoouserultra->userpanel->get_user_meta('instagram');
    $pinterest = $xoouserultra->userpanel->get_user_meta('pinterest');
    $google = $xoouserultra->userpanel->get_user_meta('google');
    $twitter = $xoouserultra->userpanel->get_user_meta('twitter');
    
    
    $user_reviews = BusinessDirectory_RatingsModule::get_reviews_by_user($user_id);
    $num_reviews = sizeof($user_reviews);
    
    $user_favorites = wpfp_get_users_favorites($user_id);
    $num_favorites = sizeof($user_favorites);
    
    $current_user = wp_get_current_user();
?>


<div class="col-md-4">
    <div class="avatar">
        <?php echo $xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type,  $pic_size_type)?>
    </div>
    <div class="profile-sidebar-info">
        <h2><?php    
            echo  $first_name . ' ' . $last_name;
            ?>
        </h2>
        <div class="user_stats">
            
            <div class="user_stats_reviews">
                <?php echo $num_reviews. " Reviews"; ?>
            </div>
            <div class="user_stats_favorites">
                <?php echo $num_favorites. " Favorites"; ?>
            </div>
        </div>
        
        <div class="profile-headline">
            <?php if($headline!=''){ ?>
                <h5>"<?php echo $headline ?>"</h5>
            <?php } ?>
        </div>
        <div class="profile-text-element">
            <?php
                if($current_user->id==$user_id || $description != "" ){?>
                    <label>My Style</label>
            <?php    
                }
                if($description != ""){
                    echo $description;
                }elseif($current_user->id==$user_id ){
                    echo "<a href='".site_url('/myaccount/?module=profile')."'>Add this</a>";
                }
            ?>
        </div>
        <div class="profile-text-element">
            <?php
                if($current_user->id==$user_id || $location != "" ){?>
                    <label>My Location</label>
            <?php    
                }
                if($location != ""){
                    echo $location;
                }elseif($current_user->id==$user_id ){
                    echo "<a href='".site_url('/myaccount/?module=profile')."'>Add this</a>";
                }
            ?>
        </div>
        <div class="profile-text-element">
            <?php
                if($current_user->id==$user_id || $hometown != "" ){?>
                    <label>My Hometown</label>
            <?php    
                }
                if($hometown != ""){
                    echo $hometown;
                }elseif($current_user->id==$user_id ){
                    echo "<a href='".site_url('/myaccount/?module=profile')."'>Add this</a>";
                }
            ?>
        </div>
        
        <div class="profile-text-element">
            <?php
                if($current_user->id==$user_id || $website != "" ){?>
                    <label>My Favorite Website</label>
            <?php    
                }
                if($website != ""){
                    echo $website;
                }elseif($current_user->id==$user_id ){
                    echo "<a href='".site_url('/myaccount/?module=profile')."'>Add this</a>";
                }
            ?>
           
        </div>
        <div class="profile-text-element">
            <?php
                if($current_user->id==$user_id || $loves != "" ){?>
                    <label>Things I Love</label>
            <?php    
                }
                if($loves != ""){
                    echo $loves;
                }elseif($current_user->id==$user_id ){
                    echo "<a href='".site_url('/myaccount/?module=profile')."'>Add this</a>";
                }
            ?>
           
        </div>
        <div class="profile-text-element">
            <?php
                if($current_user->id==$user_id || $facebook != "" ){?>
                    <label>Facebook Profile</label>
            <?php    
                }
                if($facebook != ""){
                    echo $facebook;
                }elseif($current_user->id==$user_id ){
                    echo "<a href='".site_url('/myaccount/?module=profile')."'>Add this</a>";
                }
            ?>
        </div>
        <div class="profile-text-element">
            <?php
                if($current_user->id==$user_id || $instagram != "" ){?>
                    <label>Instagram</label>
            <?php    
                }
                if($instagram != ""){
                    echo $instagram;
                }elseif($current_user->id==$user_id ){
                    echo "<a href='".site_url('/myaccount/?module=profile')."'>Add this</a>";
                }
            ?>
             
        </div>
        <div class="profile-text-element">
            <?php
                if($current_user->id==$user_id || $pinterest != "" ){?>
                    <label>Pinterest</label>
            <?php    
                }
                if($pinterest != ""){
                    echo $pinterest;
                }elseif($current_user->id==$user_id ){
                    echo "<a href='".site_url('/myaccount/?module=profile')."'>Add this</a>";
                }
            ?>
             
        </div>
        <div class="profile-text-element">
            <?php
                if($current_user->id==$user_id || $twitter != "" ){?>
                    <label>Twitter</label>
            <?php    
                }
                if($twitter != ""){
                    echo $twitter;
                }elseif($current_user->id==$user_id ){
                    echo "<a href='".site_url('/myaccount/?module=profile')."'>Add this</a>";
                }
            ?>
             
        </div>
        <div class="profile-text-element">
            <?php
                if($current_user->id==$user_id || $google != "" ){?>
                    <label>Google+</label>
            <?php    
                }
                if($google != ""){
                    echo $google;
                }elseif($current_user->id==$user_id ){
                    echo "<a href='".site_url('/myaccount/?module=profile')."'>Add this</a>";
                }
            ?>
             
        </div>
    </div>
</div>

<div class="col-md-8">
    
    
    <div class="profile-user-reviews">
        <h4><?php echo $first_name."'s Reviews" ?></h4>
            
        <?php 
            foreach($user_reviews as $ur){
                $review_html = '';                
                //get the listing with $ur->listing_id;
                
                $post = get_post($ur->listing_id);
                if(!empty($post)){
        ?>
            <div class="profile-review-business-img">
                
            </div>
            <div class="profile-review-business-name">
                <?php 
                echo get_the_title($ur->listing_id);
                echo get_listing_image($ur->listing_id);
                ?>
            </div>
<!--
            <div class="profile-review-business-categories">
                <?php
                    $top_listing_categories = get_top_apparel_categories($ur->listing_id);
                    $top_listing_categories_names = array();
                    foreach($top_listing_categories as $tc){
                        $top_listing_categories_names[] = $tc->name;
                    }
                    echo implode (", ", $top_listing_categories_names);
                ?>
            
            </div>
-->
            <span class="date" itemprop="datePublished" content="<?php echo $ur->created_on ?>">
                <?php echo date_i18n(get_option('date_format'), strtotime($ur->created_on)) ?>
            </span>

            <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
            <meta itemprop="worstRating" content="0" />
            <meta itemprop="ratingValue" content="<?php echo $ur->rating; ?>" />
            <meta itemprop="bestRating" content="5" />
            <span class="wpbdp-ratings-stars" data-readonly="readonly" data-value="<?php echo $ur->rating; ?>" itemprop="ratingValue"></span>
            </span>   

            <div class="rating-comment" itemprop="description">
                <?php echo $ur->comment ?>
            </div>
        <?php
                }
            } 
        ?> 
    </div>
    <div class="profile-favorite-listings">
        <h4>Favorite Stores</h4>
        <?php echo do_shortcode("[wp-favorite-posts]"); ?>
    </div>
</div>
    
<!--
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
        
 <!--       
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
   -->      
         
         
  <!--       
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
         
              
             
   --> 
    
        </div>
        
 </div>   

</div>