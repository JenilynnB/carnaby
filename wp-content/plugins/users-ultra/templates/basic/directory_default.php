<?php
global $xoouserultra;

$module ="";
$act="";
$gal_id= "";

if(isset($_GET["module"]))
{
	$module = $_GET["module"];

}

if(isset($_GET["act"]))
{
	$act = $_GET["act"];

}

if(isset($_GET["gal_id"]))
{
	$gal_id = $_GET["gal_id"];

}

//print_r($users_list);

$display_default_search = true;

?>

<div class="usersultra-front-directory-wrap">



	<div class="usersultra-searcher">
    
         <form method="get" action="">
         
         <?php if ($display_default_search==true) { ?>
         
         <input type="text" name="usersultra_searchuser" id="usersultra_searchuser" value="<?php echo get_query_var('searchuser'); ?>" placeholder="<?php echo _e('Search for a user...','xoousers'); ?>" />
		 
         <button type="submit" class="" title="<?php echo _e('Search','xoousers'); ?>"><?php echo _e('Search','xoousers'); ?></button>
         
		 <?php } ?>
         
         
         
         </form>
    
    
   </div>

       <?php if (isset($users_list['paginate'])) { ?>
        <div class="usersultra-paginate top_display"><?php echo $users_list['paginate']; ?></div>
		
		<?php } ?>
        
         <?php if ($display_total_found=='yes') {
			 
			 echo $total_f;
		 
		 
		 ?>      
                    
		
		<?php } ?>
        


        
	
    
    	<ul class="usersultra-front-results">
        
        <?php foreach($users_list['users'] as $user) : $user_id = $user->ID; 
		
		   if($pic_boder_type=="rounded")
		   {
			   $class_avatar = "avatar";
			   
			}
		
		?>     
            
            
            <li class="rounded" style="width:<?php echo $item_width?>">
               
               <div class="xoousers-prof-photo">
               
                    <?php echo $xoouserultra->userpanel->get_user_pic( $user_id, $pic_size, $pic_type, $pic_boder_type, $pic_size_type)?>             
               
               </div> 
               
               
               
               
                <div class="info-div">
            
			
				<p class="uu-direct-name"><?php echo  $xoouserultra->userpanel->get_display_name($user_id)?></p>
                
                
                 <div class="social-icon-divider">                                       
                 
                  </div> 
                
                 <?php if ($optional_fields_to_display!="") { ?>
                 
                 
                   <?php echo $xoouserultra->userpanel->display_optional_fields( $user_id,$display_country_flag, $optional_fields_to_display)?>   
                 
                 
                
                  <?php } ?>
                
                 </div> 
                 
                  <div class="uultra-view-profile-bar">
                  
                    <a class="uultra-btn-profile" href="<?php echo $xoouserultra->userpanel->get_user_profile_permalink( $user_id)?>">See Profile</a>
                  
                  </div> 
            
            
            </li>
            
            
       <?php endforeach; ?>
                  
        
        </ul>
        
        
		<?php if (isset($users_list['paginate'])) { ?>
        <div class="usersultra-paginate bottom_display"><?php echo $users_list['paginate']; ?></div>
		
		<?php } ?>

</div>