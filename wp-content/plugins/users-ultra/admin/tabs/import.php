<?php
global $xoouserultra;
	
?>

 <div class="user-ultra-sect ">
        
        <h3><?php _e('Synchronize Already Users','xoousers'); ?></h3>
        <p><?php _e('This feature allows you to synchronize the alreay users in you Wordpress website so they can be recognized by Users Ultra ','xoousers'); ?></p>
        
        
        <p class="submit">
	<input type="submit" name="submit" id="uultra-btn-sync-btn" class="button button-primary " value="<?php _e('Start Sync Now','xoousers'); ?>"  />
	
       </p>
       
       <p id='uultra-sync-results'>
       
       </p>
                     
       
    
</div>  

 <div class="user-ultra-sect ">
        
        <h3><?php _e('Import Users','xoousers'); ?></h3>
        <p><?php _e('This feature lets you import users easily into the Users Ultra System. The file must be CSV format. Delimited Comma-separated Values ','xoousers'); ?></p>
        
        <p><?php _e('<b>IMPORTANT: </b> The CSV format should be:  "user name", "email", "display name", "registration date", "first name", "last name", "age", "country"  ','xoousers'); ?></p>
        
                
       
   <form action=""  name="uultra-form-cvs-form" method="post" enctype="multipart/form-data" >
<input type="hidden" name="uultra-form-cvs-form-conf" />
                   
          
           <p class="submit">
	<input type="file" name="file_csv" class="" value="<?php _e('Chose File','xoousers'); ?>"  /><?php _e(' <b>ONLY CSV EXTENSIONS ALLOWED: </b>  ','xoousers'); ?>
	
     </p>
       
     <h4><?php _e('Account Activation: ','xoousers'); ?></h4>
       
     <p>
       <input name="uultra-send-welcome-email" type="checkbox" id="uultra-send-welcome-email" value="1" checked="checked" />  <?php _e('Send welcome email with new password ','xoousers'); ?><br />
         
         
          <label>
           <input name="uultra-activate-account" type="radio" id="RadioGroup1_1" value="active" checked="checked" />
           <?php _e(' Activate account automatically ','xoousers'); ?></label>
       
    <br />
       
       
 <input type="radio" name="uultra-activate-account" value="pending" id="RadioGroup1_0" />
           <?php _e(' Send Activation Link.','xoousers'); ?></label>
          <strong> PLEASE NOTE</strong>: the account status will be &quot;pending&quot; until the user clicks on the activation link.<br />
     </p>
       
     <p class="submit">
	<input type="submit" name="submit"  class="button button-primary " value="<?php _e('Start Importing','xoousers'); ?>"  />
	
       </p>
       
        <p>
        
        <?php echo $xoouserultra->userpanel->messages_process;?>
	    </p>
       
       
            
             
         </form>
    
</div>

 <div class="user-ultra-sect ">
        
        <h3><?php _e('Auto Sync with WooCommerce','xoousers'); ?></h3>
        
 <?php if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
        <p><?php _e('Syncing with WooCommerce will automatically add WooCommerce customer profile fields to your Users Ultra Plugin. A quick way to have a WooCommerce account page integrated with Users Ultra. Just click the following button and Users Ultra will do the work for you.','xoousers'); ?></p>
        
        
              
       <p><a href="<?php echo add_query_arg( array('sync' => 'woocommerce') ); ?>" class="button button-secondary"><?php _e('Sync and keep existing fields','xoousers'); ?></a> 
<a href="<?php echo add_query_arg( array('sync' => 'woocommerce_clean') ); ?>" class="button button-secondary"><?php _e('Sync and delete existing fields','xoousers'); ?></a></p>
       
       <p id='uultra-sync-woo-results'>
       
       </p>
                     
 <?php } else { ?>

<p><?php _e('Please install WooCommerce plugin first.','xoousers'); ?></p>

<?php } ?>      
    
</div> 
 

<script>
var message_sync_users = "<?php echo _e('Please wait, this process may take several minutes','xoousers')?>"
</script>
