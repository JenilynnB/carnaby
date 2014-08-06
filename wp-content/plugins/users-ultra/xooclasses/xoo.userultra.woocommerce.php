<?php
class XooWooCommerce {

	public $woocommerce_file_path;
	public $woocommerce_version;
	public $woocommerce_version_status;
	 

	function __construct() {
		global $pagenow;

		if('admin.php' == $pagenow && isset($_GET['page']) && ('import' == $_GET['tab']) 
			&& in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){

			// Load Woocommerce file based on version number
			$this->woocommerce_version =  get_option('woocommerce_db_version');
			$plugin_dir_path = plugin_dir_path( dirname(dirname(__FILE__)) );
			if(version_compare( $this->woocommerce_version, '2.0.20') == '1'){
				$this->woocommerce_file_path = $plugin_dir_path.'woocommerce/includes/admin/class-wc-admin-profile.php';
				
				$this->woocommerce_version_status = '1';
				$this->woo_admin_profile = include($this->woocommerce_file_path);

			}else{
				$this->woocommerce_file_path = $plugin_dir_path.'woocommerce/admin/woocommerce-admin-users.php';
				$this->woocommerce_version_status = '0';
				require_once $this->woocommerce_file_path;
			}

			if ( isset($_REQUEST['sync']) && !isset($_POST['submit']) && !isset($_POST['uultra-add']) && !isset($_POST['reset-options']) && !isset($_POST['reset-options-fields']) ) {
				
				if ($_REQUEST['sync'] == 'woocommerce') {
				
				/* load fields */
				$fields = get_option('usersultra_profile_fields');

				/*  Add the exisitng entries to prevent duplication of woocommerce fields */
				$field_meta_array = array();
				$separator_array = array();
				foreach ($fields as $field) {
					$field_meta = isset($field['meta']) ? $field['meta'] : '';
					if('' != $field_meta){
						array_push($field_meta_array, $field['meta']);
					}else if('separator' == $field['type']){
						array_push($separator_array, $field['name']);
					}				
				}
				
				/* Add WooCommerce profile fields */
				$woo_meta  = array();
				if(file_exists($this->woocommerce_file_path)){
					if('1' == $this->woocommerce_version_status){

						//$woo_admin_profile = new WC_Admin_Profile();
						$woo_meta = $this->woo_admin_profile->get_customer_meta_fields();
					}else{
						$woo_meta = woocommerce_get_customer_meta_fields();
					}				
				}else{
					//echo __('Woocommerce File Doesn\'t Exist','xoousers');exit;
				}	
				
				$new_index = max(array_keys($fields));

				foreach($woo_meta as $group => $array) {

					
					if(!in_array($array['title'], $separator_array)){
						$fields[$new_index+=10] = array( 
							'type' => 'separator', 
							'name' => $array['title'],
							'private' => 0,
							'deleted' => 0, 
							'meta' => $array['title'].'_separator'
						);
					}				
					
					foreach($array['fields'] as $meta => $label) {

						if(!in_array($meta, $field_meta_array)){				
						
						/* switch icon */
						switch ($meta) {
							case 'billing_first_name': $icon = 'user'; break;
							case 'billing_last_name': $icon = 0; break;
							case 'billing_company': $icon = 'building'; break;
							case 'billing_address_1': $icon = 'home'; break;
							case 'billing_address_2': $icon = 0; break;
							case 'billing_city': $icon = 0; break;
							case 'billing_postcode': $icon = 0; break;
							case 'billing_state': $icon = 0; break;
							case 'billing_country': $icon = 'map-marker'; break;
							case 'billing_phone': $icon = 'phone'; break;
							case 'billing_email': $icon = 'envelope'; break;
							case 'shipping_first_name': $icon = 'user'; break;
							case 'shipping_last_name': $icon = 0; break;
							case 'shipping_company': $icon = 'building'; break;
							case 'shipping_address_1': $icon = 'home'; break;
							case 'shipping_address_2': $icon = 0; break;
							case 'shipping_city': $icon = 0; break;
							case 'shipping_postcode': $icon = 0; break;
							case 'shipping_state': $icon = 0; break;
							case 'shipping_country': $icon = 'map-marker'; break;
							default: $icon = 0; break;
						}
						
						switch($meta) {
							
							case 'billing_country':
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'select', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'predefined_options' => 'countries',
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;

							case 'shipping_country':
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'select', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'predefined_options' => 'countries',
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;
								
							default:
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'text', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;
						
						}
					}
						
					}
					
				}
				
				update_option('usersultra_profile_fields', $fields);
				//echo '<div class="updated"><p><strong>'.__('WooCommerce customer fields have been added successfully.','xoousers').'</strong></p></div>';
				
				}
				
				if ($_REQUEST['sync'] == 'woocommerce_clean') {


				/* Add WooCommerce profile fields */
				$woo_meta  = array();
				if(file_exists($this->woocommerce_file_path)){
					if('1' == $this->woocommerce_version_status){
						
						
						$woo_meta = $this->woo_admin_profile->get_customer_meta_fields();
					}else{
						$woo_meta = woocommerce_get_customer_meta_fields();
					}				
				}else{
					echo __('Woocommerce File Doesn\'t Exist','xoousers');exit;
				}	

							
				$new_index = 0;
				
				foreach($woo_meta as $group => $array) {
					
					$fields[$new_index+=10] = array( 
						'type' => 'separator', 
						'name' => $array['title'],
						'private' => 0,
						'deleted' => 0,
						'meta' => $array['title'].'_separator'
					);
					
					foreach($array['fields'] as $meta => $label) {
						
						/* switch icon */
						switch ($meta) {
							case 'billing_first_name': $icon = 'user'; break;
							case 'billing_last_name': $icon = 0; break;
							case 'billing_company': $icon = 'building'; break;
							case 'billing_address_1': $icon = 'home'; break;
							case 'billing_address_2': $icon = 0; break;
							case 'billing_city': $icon = 0; break;
							case 'billing_postcode': $icon = 0; break;
							case 'billing_state': $icon = 0; break;
							case 'billing_country': $icon = 'map-marker'; break;
							case 'billing_phone': $icon = 'phone'; break;
							case 'billing_email': $icon = 'envelope'; break;
							case 'shipping_first_name': $icon = 'user'; break;
							case 'shipping_last_name': $icon = 0; break;
							case 'shipping_company': $icon = 'building'; break;
							case 'shipping_address_1': $icon = 'home'; break;
							case 'shipping_address_2': $icon = 0; break;
							case 'shipping_city': $icon = 0; break;
							case 'shipping_postcode': $icon = 0; break;
							case 'shipping_state': $icon = 0; break;
							case 'shipping_country': $icon = 'map-marker'; break;
							default: $icon = 0; break;
						}
						
						switch($meta) {
							
							case 'billing_country':
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'select', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'predefined_options' => 'countries',
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;
							
							case 'shipping_country':
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'select', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'predefined_options' => 'countries',
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;
								
							default:
							$fields[$new_index+=10] = array(
								'icon' => $icon, 
								'field' => 'text', 
								'type' => 'usermeta', 
								'meta' => $meta, 
								'name' => $label['label'],
								'can_hide' => 1,
								'can_edit' => 1,
								'private' => 0,
								'social' => 0,
								'deleted' => 0
							);
							break;
						
						}
						
					}
					
				}
				
				update_option('usersultra_profile_fields', $fields);
				//echo '<div class="updated"><p><strong>'.__('WooCommerce customer fields have been added successfully.','xoousers').'</strong></p></div>';
				
				}
					
			}
		}
		
	}
	
	/**
	 * My Orders 
	 */
	function show_my_latest_orders($howmany, $status=null)
	{
		global $wpdb, $current_user, $xoouserultra, $woocommerce; 
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH. 'wp-admin/includes/user.php' );
		require_once(ABSPATH.  'wp-includes/query.php' );	
		
		
		$user_id = get_current_user_id();
		
		$args = array(
         'numberposts' => -1,
         'meta_key' => '_customer_user',
         'meta_value' => $user_id,
         'post_type' => 'shop_order',
         'post_status' => 'publish',
         'tax_query'=>array(
                     array(
                     'taxonomy' =>'shop_order_status',
                     'field' => 'slug',
					 'terms' => array('processing','pending','completed','cancelled')
                    
                     )
         )
        );
 
		
        $loop = new WP_Query( $args );
		
		//print_r($loop );
				
		if ( !empty( $status ) )
		{
			echo '<div id="message" class="updated fade"><p>', $status, '</p></div>';
		}
		if (  !$loop->have_posts() )
		{
			echo '<p>', __( 'You have no orders.', 'xoousers' ), '</p>';
		}
		else
		{
			$n = count( $msgs );
			
			
			?>
			<form action="" method="get">
				<?php wp_nonce_field( 'usersultra-bulk-action_inbox' ); ?>
				<input type="hidden" name="page" value="usersultra_inbox" />
	
				
	
				<table class="widefat fixed" id="table-3" cellspacing="0">
					<thead>
					<tr>
						
                       
						<th class="manage-column" ><?php _e( 'Order #', 'xoousers' ); ?></th>
                        <th class="manage-column"><?php _e( 'Total', 'xoousers' ); ?></th>
						<th class="manage-column"><?php _e( 'Date', 'xoousers' ); ?></th>
						<th class="manage-column" ><?php _e( 'Modified Date', 'xoousers' ); ?></th>
                        <th class="manage-column" ><?php _e( 'Status', 'xoousers' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
							
							while ( $loop->have_posts() ) : $loop->the_post();
							$order_id = $loop->post->ID;
							$order = new WC_Order($order_id);
							
							//print_r($order );
							
							?>
						<tr>
							                         
                            
							<td>#<?php echo $order_id; ?></td>
                            <td><?php echo woocommerce_price($order->order_total);?></td>
							<td> <?php echo $order->order_date; ?></td>
							<td><?php echo $order->modified_date; ?></td>
                            <td><?php echo $order->status; ?></td>
                            
                            
							<?php
	
						endwhile;
						?>
					</tbody>
					
				</table>
			</form>
			<?php
	
		}
		?>

	<?php
	}

}
$key = "woocommerce";
$this->{$key} = new XooWooCommerce();