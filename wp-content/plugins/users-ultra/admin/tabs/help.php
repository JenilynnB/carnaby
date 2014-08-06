<?php
global $xoouserultra;
	
?>

 <div class="user-ultra-sect ">
        
        <h3><?php _e('Documentation and User Guide','xoousers'); ?></h3>
        <p><?php _e("Here there are some useful shortocodes that will help you to start your online community in minutes.",'xoousers'); ?></p>
        
        <p><?php _e("<a href='http://usersultra.com/doc.html' target='_blank'>CLICK HERE </a>to check the online documentation",'xoousers'); ?></p>
        
         <p><?php _e("<a href='http://www.usersultra.com/support/' target='_blank'>CLICK HERE </a>to visit Support Forum",'xoousers'); ?></p>
        
        <p>If you like this plugin, please don't forget to rate it <img src="<?php echo xoousers_url?>/admin/images/star-rating.png" width="20" height="20" /><img src="<?php echo xoousers_url?>/admin/images/star-rating.png" width="20" height="20" /><img src="<?php echo xoousers_url?>/admin/images/star-rating.png" width="20" height="20" /><img src="<?php echo xoousers_url?>/admin/images/star-rating.png" width="20" height="20" /><img src="<?php echo xoousers_url?>/admin/images/star-rating.png" width="20" height="20" />. <?php _e("<a href='http://wordpress.org/plugins/users-ultra/' target='_blank'>CLICK HERE TO RATE IT </a>",'xoousers'); ?></p>
        
       
   <h3>Common Shortcodes</h3>
         
          <strong>Registration Form</strong>
                  <pre>[usersultra_registration]</pre>
                 <strong>Login Form</strong>
                 <pre>[usersultra_login]</pre>
                 
                  <strong>Logout</strong>
                 <pre>[usersultra_logout]</pre>
                 
                 <strong>Members Directory</strong>
                 <pre>[usersultra_directory]</pre>
                 
                   <strong>Filter Users By Role</strong>
                 <pre>[usersultra_directory role='author']</pre>
                 
                  <strong>Top Rated Users</strong>
                 <pre> [usersultra_users_top_rated optional_fields_to_display='friend,rating,social,country'  display_country_flag='both'] </pre>
                  <strong>Most Visited Users</strong>
                 <pre> [usersultra_users_most_visited optional_fields_to_display='friend,social' pic_size='80' ] </pre>
                 
                  <strong>User Spotlight</strong>
                 <pre> [usersultra_users_promote optional_fields_to_display='rating,social' users_list='59'  display_country_flag='both']  </pre>
                 
                   <strong>User Profile</strong>
                 <pre>[usersultra_profile optional_fields_to_display='age,country,social']</pre>
                 
                  <strong>User Profile, displaying all fields</strong>
                 <pre>[usersultra_profile profile_fields_to_display='all']</pre>               
                 
                 
                   <strong>User Profile With Lightbox Gallery</strong>
                 <pre>[usersultra_profile gallery_type='lightbox'] </pre>
                 
                   <strong>Latest Users</strong>
                 <pre> [usersultra_users_latest optional_fields_to_display='social' ]   </pre>
                 
                   <strong>Logged in Protection</strong>
                 <pre> [usersultra_protect_content display_rule='logged_in_based'  custom_message_loggedin='Only Logged in users can see the content']Your private content here [/usersultra_protect_content]  </pre>
                 
                 <strong>Membership Protection</strong>
                 <pre> [usersultra_protect_content display_rule='membership_based' membership_id='1'  custom_message_membership='Only Gold and Platinum Members can see this Video'] Private Content... [/usersultra_protect_content] </pre>
                 
                   <strong>Excluding Modules From Members Panel</strong>
                 <pre> [usersultra_my_account disable='messages,photos']</pre>
                 
                   <strong>Pricing Table</strong>
                 <pre> [respo_pricing plan_id='input the plan id here' per='per month' button_text='Sign Up' button_color='blue' color='blue' button_target='self' button_rel='nofollow' ]<ul>	<li>Write Something here</li>	<li>Write Something here</li>	<li>Write Something here</li>	<li>Write Something here</li></ul>[/respo_pricing]</pre>
                 
                          
    
</div>
