<?php
global $xoouserultra;
$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');


$total_common=0;
$total_facebook= $xoouserultra->userpanel->signup_status('1');
$total_yahoo= $xoouserultra->userpanel->signup_status('3');
$total_google=$xoouserultra->userpanel->signup_status('4');
$total_linkedin=$xoouserultra->userpanel->signup_status('1');
$total_twitter = $xoouserultra->userpanel->signup_status('5');

$howmany_latest= 10;
        
?>

 <div class="user-ultra-sect-welcome-message ">
 
 <h1><?php _e('Welcome to Users Ultra Lite!','xoousers'); ?></h1>
        <div class="uu-sect-p">
        	            
            <p><?php _e("Thank you very much for installing Users Ultra Lite.  ",'xoousers'); ?></p>
        
        </div>
 
 </div>
        <div class="user-ultra-sect ">      
        
        
            <div class="left_col user-ultra-rounded">
            
              <h3><?php _e('Social Connects Stats', 'xoousers'); ?></h3>
              
          <div class="statblock-social user-ultra-rounded">
                 
                                     
                    <h3>Facebook </h3>
                 <img src="<?php echo xoousers_url?>/admin/images/facebook.png" width="35" height="38" />
            <p class="day_amount"><?php echo $total_facebook?></p>
                     
                 
              </div>
                 
                  <div class="statblock-social user-ultra-rounded">
                  
                   <h3 >Yahoo</h3>
                   <img src="<?php echo xoousers_url?>/admin/images/yahoo.png" width="35" height="38" />
                   <p class="month_amount"><?php echo $total_yahoo?></p>
                 
                 </div>
                 
                  <div class="statblock-social user-ultra-rounded">
                  
                  <h3 >LinkedIn</h3>
                  <img src="<?php echo xoousers_url?>/admin/images/linkedin.png" width="35" height="38" />
                   <p class="year_amount"><?php echo $total_linkedin?></p>
                 
                 </div>
                 
                 <div class="statblock-social user-ultra-rounded">
                  
                  <h3 >Google</h3>
                  <img src="<?php echo xoousers_url?>/admin/images/google.png" width="35" height="38" />
                   <p class="alltime_amount"><?php echo $total_google?></p>
                 
                 </div>
                 
                  <div class="statblock-social user-ultra-rounded">
                  
                  <h3 >Twitter</h3>
                  <img src="<?php echo xoousers_url?>/admin/images/twitter.png" width="35" height="38" />
                   <p class="alltime_amount"><?php echo $total_twitter?></p>
                 
                 </div>
                 
                
                  <div class="statblock-graph ">
                  
                 		<div id="piechart2" style="width:100%;height:200px"></div>
                 
                  </div>
              
              
            
            </div>
            
             <div class="right_col user-ultra-rounded">
             
              <h3> <?php _e('Subscription Stats', 'xoousers'); ?></h3>
             
             
                 <div class="statblock user-ultra-rounded">
                 
                                     
                    <h3>Today</h3>
                     <p class="day_amount"><?php echo $xoouserultra->userpanel->get_amount_period(date("m"),date("d"), date("Y"))?></p>
                 
                 </div>
                 
                  <div class="statblock user-ultra-rounded">
                  
                   <h3 >This Month</h3>
                   <p class="month_amount"><?php echo $xoouserultra->userpanel->get_amount_period(date("m"),"", date("Y"))?></p>
                 
                 </div>
                 
                  <div class="statblock user-ultra-rounded">
                  
                  <h3 >This Year</h3>
                   <p class="year_amount"><?php echo $xoouserultra->userpanel->get_amount_period("","", date("Y"))?></p>
                 
                 </div>
                 
                 <div class="statblock user-ultra-rounded">
                  
                  <h3 >All Time</h3>
                   <p class="alltime_amount"><?php echo $xoouserultra->userpanel->get_amount_period("","", "")?></p>
                 
                 </div>
                 
                 
                  <h3> <?php _e('Latest 5 Transactions', 'xoousers'); ?></h3>
                  
                  <?php
				  
				  //get latest transactions
				  
				  
			    $orders = $xoouserultra->order->get_latest(5);
			
				
				if (!empty($orders)){
				
				
				?>
       
           <table width="100%" class="wp-list-table widefat fixed posts table-generic">
            <thead>
                <tr>
                    <th width="17%"><?php _e('Date', 'xoousers'); ?></th>
                    <th width="24%"><?php _e('User', 'xoousers'); ?></th>
                    <th width="28%"><?php _e('Transaction ID', 'xoousers'); ?></th>
                    <th width="15%"><?php _e('Status', 'xoousers'); ?></th>
                    <th width="16%"><?php _e('Amount', 'xoousers'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			foreach($orders as $order) {
					
			?>
              

                <tr>
                    <td><?php echo  date("m/d/Y", strtotime($order->order_date)); ?></td>
                    <td><?php echo $order->display_name; ?> (<?php echo $order->user_login; ?>)</td>
                    <td><?php echo $order->order_txt_id; ?></td>
                     <td><?php echo $order->order_status; ?></td>
                   <td> <?php echo $currency_symbol.$order->order_amount; ?></td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no transactions yet.','xoousers'); ?></p>
			<?php	} ?>
            </table>
             
             
                 
             
              
            
            </div>
        
           
        </div>
        
      
        
        <div class="user-ultra-sect ">
        
         <div class="left_col_users user-ultra-rounded">
        
          
        <h3> <?php _e('Latest '.$howmany_latest.' Users', 'xoousers'); ?></h3>
        
         <?php
			
				$users = $xoouserultra->userpanel->get_latest_users_private($howmany_latest);
				
				if (!empty($users)){
				
				
				?>
        
        
           <table class="wp-list-table widefat fixed posts table-generic">
            <thead>
                <tr>
                    <th><?php _e('Avatar', 'xoousers'); ?></th>
                    <th><?php _e('Username', 'xoousers'); ?></th>                 
                    
                   
                   
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			foreach($users as $user) {
					$user_id = $user->ID;
			?>
              

                <tr>
                    <td><?php echo get_avatar( $user_id, 40 ); ?></td>
                    <td><?php echo $user->user_login; ?></td>
                   
                   
                    
                   
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no pending activation users.','xoousers'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
         </div>
         
         
         
         <div class="right_col_users user-ultra-rounded" >
         
             <div class="" id="uultra-pending-moderation-list">
         
             	<p><?php echo  _e('loading ...','xoousers'); ?></p>
             
             
              </div>
              
             <div class="" id="uultra-pending-activation-list">
         
                 <p><?php echo  _e('loading ...','xoousers'); ?></p>         
             
              </div>
              
               <div class="" id="uultra-pending-payment-list">
         
                 <p><?php echo  _e('loading ...','xoousers'); ?></p>         
             
              </div>
           
         
          </div>
          
          
          
        
        
        
        
        </div>
        
       
        <script type="text/javascript">
		
		reload_pending_moderation();
		reload_pending_activation();
		reload_pending_payment();
		
		
		
		
		
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
	['Social', 'Quantity', { role: 'style' }],
   
    ['Common',<?php echo $total_common?>, 'gold'],            // RGB value
    ['Facebook', <?php echo $total_facebook?>, '#369'],            // RGB value
	['Yahoo',<?php echo $total_yahoo?>, '#636'],            // English color name
	['LinkedIn', <?php echo $total_linkedin?>, '#0080FF'],
	['Google', <?php echo $total_google?>, 'color: #e5e4e2' ], // CSS-style declaration
	['Twitter', <?php echo $total_twitter?>, 'color: #39F' ], // CSS-style declaration
	
	
     
	 
	  ]);


        var options = {
          title: 'Sign up Stats',
          hAxis: {title: 'Sign up Options', titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('piechart2'));
        chart.draw(data, options);
      }
    </script>


     
