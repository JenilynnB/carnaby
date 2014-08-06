<?php
global $xoouserultra;
$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
$orders = $xoouserultra->order->get_all();

$howmany = "";
$year = "";
$month = "";
$day = "";

if(isset($_GET["howmany"]))
{
	$howmany = $_GET["howmany"];		
}

if(isset($_GET["month"]))
{
	$month = $_GET["month"];		
}

if(isset($_GET["day"]))
{
	$day = $_GET["day"];		
}

if(isset($_GET["year"]))
{
	$year = $_GET["year"];		
}
		
?>

        
        <div class="user-ultra-sect ">
        
        <h3><?php _e('Orders','xoousers'); ?></h3>
        
       
       
        <form action="" method="get">
         <input type="hidden" name="page" value="userultra" />
          <input type="hidden" name="tab" value="orders" />
        
        <div class="user-ultra-success uultra-notification"><?php _e('Sucess ','xoousers'); ?></div>
        
        <div class="user-ultra-sect-second user-ultra-rounded" >
        
         <h3> <?php _e('Search Transactions ','xoousers'); ?></h3>
         
        
         
        
         
           <table width="100%" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td width="17%"><?php _e('Keywords: ','xoousers'); ?></td>
             <td width="5%"><?php _e('Month: ','xoousers'); ?></td>
             <td width="5%"><?php _e('Day: ','xoousers'); ?></td>
             <td width="52%"><?php _e('Year:','xoousers'); ?></td>
             <td width="21%">&nbsp;</td>
           </tr>
           <tr>
             <td><input type="text" name="keyword" id="keyword" placeholder="<?php _e('write some text here ...','xoousers'); ?>" /></td>
             <td><select name="month" id="month">
               <option value="" selected="selected"><?php _e('All','xoousers'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=12){
			  ?>
               <option value="<?php echo $i?>"  <?php if($i==$month) echo 'selected="selected"';?>><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select></td>
             <td><select name="day" id="day">
               <option value="" selected="selected"><?php _e('All','xoousers'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=31){
			  ?>
               <option value="<?php echo $i?>"  <?php if($i==$day) echo 'selected="selected"';?>><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select></td>
             <td><select name="year" id="year">
               <option value="" selected="selected"><?php _e('All','xoousers'); ?></option>
               <?php
			  
			  $i = 2014;
              
			  while($i <=2020){
			  ?>
               <option value="<?php echo $i?>" <?php if($i==$year) echo 'selected="selected"';?> ><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select></td>
             <td>&nbsp;</td>
           </tr>
          </table>
         
         <p>
         
         <button><?php _e('Filter','xoousers'); ?></button>
        </p>
        
       
        </div>
        
        
          <p> <?php _e('Total: ','xoousers'); ?> <?php echo $xoouserultra->order->total_result;?> | <?php _e('Displaying per page: ','xoousers'); ?>: <select name="howmany" id="howmany">
               <option value="20" <?php if(20==$howmany ||$howmany =="" ) echo 'selected="selected"';?>>20</option>
                <option value="40" <?php if(40==$howmany ) echo 'selected="selected"';?>>40</option>
                 <option value="50" <?php if(50==$howmany ) echo 'selected="selected"';?>>50</option>
                  <option value="80" <?php if(80==$howmany ) echo 'selected="selected"';?>>80</option>
                   <option value="100" <?php if(100==$howmany ) echo 'selected="selected"';?>>100</option>
               
          </select></p>
        
         </form>
         
         <div class="uupagination">              
         
            <?php echo $xoouserultra->order->pages;?>
         
         </div>
        
         <?php
			
			
				
				if (!empty($orders)){
				
				
				?>
       
           <table width="100%" class="wp-list-table widefat fixed posts table-generic">
            <thead>
                <tr>
                    <th width="3%"><?php _e('#', 'xoousers'); ?></th>
                    <th width="11%"><?php _e('Date', 'xoousers'); ?></th>
                    <th width="23%"><?php _e('User', 'xoousers'); ?></th>
                     <th width="18%"><?php _e('User Email', 'xoousers'); ?></th>
                    <th width="16%"><?php _e('Transaction ID', 'xoousers'); ?></th>
                    <th width="11%"><?php _e('Plan', 'xoousers'); ?></th>
                     <th width="9%"><?php _e('Method', 'xoousers'); ?></th>
                     <th width="9%"><?php _e('Status', 'xoousers'); ?></th>
                    <th width="9%"><?php _e('Amount', 'xoousers'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			foreach($orders as $order) {
					
			?>
              

                <tr>
                    <td><?php echo $order->order_id; ?></td>
                    <td><?php echo  date("m/d/Y", strtotime($order->order_date)); ?></td>
                    <td><?php echo $order->display_name; ?> (<?php echo $order->user_login; ?>)</td>
                    <td><?php echo $order->user_email; ?> </td>
                    <td><?php echo $order->order_txt_id; ?></td>
                     <td><?php echo $order->package_name; ?></td>
                      <td><?php echo $order->order_method_name; ?></td>
                      <td><?php echo $order->order_status; ?></td>
                   <td> <?php echo $currency_symbol.$order->order_amount; ?></td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no transactions yet.','xoousers'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        


        
        
        
        
        </div>
        
       
         
                  
        
        
         <h3>&nbsp;</h3>
