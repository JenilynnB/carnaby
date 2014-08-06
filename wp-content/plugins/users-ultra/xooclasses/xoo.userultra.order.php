<?php
class XooOrder 
{
	var $pages;
	var $total_result;

	function __construct() 
	{
		$this->ini_db();		

	}
	
	public function ini_db()
	{
		global $wpdb;

			
		//create orders page
		
		// Create table
			$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'usersultra_orders (
				`order_id` bigint(20) NOT NULL auto_increment,
				`order_user_id` int(11) NOT NULL,
				`order_package_id` int(11) NOT NULL,
				`order_method_name`  varchar(60) NOT NULL,				
				`order_key` varchar(250) NOT NULL,
				`order_txt_id` varchar(60) NOT NULL,
				`order_status` varchar(60) NOT NULL,
				`order_amount` decimal(11,2) NOT NULL,
				`order_date` date NOT NULL,					 			
				PRIMARY KEY (`order_id`)
			) COLLATE utf8_general_ci;';
	
	
		$wpdb->query( $query );
		
	}
	
	/*Create Order*/
	public function create_order ($orderdata)
	{
		global $wpdb,  $xoouserultra;
		
		extract($orderdata);
		
		//update database
		$query = "INSERT INTO " . $wpdb->prefix ."usersultra_orders (`order_user_id`,`order_package_id`, `order_method_name`, `order_key` ,`order_status` ,`order_amount`, `order_date`) VALUES ('$user_id','$order_package_id','$method','$transaction_key', '$status','$amount', '".date('Y-m-d')."')";
		
		//echo $query;						
		$wpdb->query( $query );						
						
	}
	
	public function update_order_status ($id,$status)
	{
		global $wpdb,  $xoouserultra;
		
		//update database
		$query = "UPDATE " . $wpdb->prefix ."usersultra_orders SET order_status = '$status' WHERE order_id = '$id' ";
		$wpdb->query( $query );
	
	}
	
	public function update_order_payment_response ($id,$order_txt_id)
	{
		global $wpdb,  $xoouserultra;
		
		//update database
		$query = "UPDATE " . $wpdb->prefix ."usersultra_orders SET order_txt_id = '$order_txt_id' WHERE order_id = '$id' ";
		$wpdb->query( $query );
	
	}
	
	
	/*Get Order*/
	public function get_order ($id)
	{
		global $wpdb,  $xoouserultra;
		
		$orders = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'usersultra_orders WHERE order_key = "'.$id.'" ' );
		
		if ( empty( $orders ) )
		{
		
		
		}else{
			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
		
	
	}
	
	/*Get Latest*/
	public function get_latest ($howmany)
	{
		global $wpdb,  $xoouserultra;
		
		$sql = 'SELECT ord.*, usu.*	 FROM ' . $wpdb->prefix . 'usersultra_orders ord  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users  usu ON (usu.ID = ord.order_user_id)";		
		$sql .= " WHERE ord.order_id <> 0 AND usu.ID = ord.order_user_id ORDER BY ord.order_id desc  LIMIT $howmany";	
			
		$orders = $wpdb->get_results($sql );
		
		return $orders ;
		
	
	}
	
	
	/*Get all*/
	public function get_all ()
	{
		global $wpdb,  $xoouserultra;
		
		$keyword = "";
		$month = "";
		$day = "";
		$year = "";
		$howmany = "";
		$ini = "";
		
		if(isset($_GET["keyword"]))
		{
			$keyword = $_GET["keyword"];		
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
		
		if(isset($_GET["howmany"]))
		{
			$howmany = $_GET["howmany"];		
		}
		
		
				
		$uri= $_SERVER['REQUEST_URI'] ;
		$url = explode("&ini=",$uri);
		
		if(is_array($url ))
		{
			//print_r($url);
			if(isset($url["1"]))
			{
				$ini = $url["1"];
			    if($ini == ""){$ini=1;}
			
			}
		
		}		
		
		
		
		if($howmany == ""){$howmany=20;}
		
		
		
		//get total
				
		$sql = 'SELECT count(*) as total, ord.*, usu.*, package.package_id, package.package_name	 FROM ' . $wpdb->prefix . 'usersultra_orders ord  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users  usu ON (usu.ID = ord.order_user_id)";	
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_packages  package ON (package.package_id = ord.	order_package_id)";		
		$sql .= " WHERE ord.order_id <> 0 AND package.package_id = ord.order_package_id ";	
			
		if($keyword!="")
		{
			$sql .= " AND (ord.order_txt_id LIKE '%".$keyword."%' OR usu.display_name LIKE '%".$keyword."%' OR usu.user_email LIKE '%".$keyword."%' OR usu.user_login LIKE '%".$keyword."%'  )  ";
		}
		
		if($day!=""){$sql .= " AND DAY(ord.order_date) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(ord.order_date) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(ord.order_date) = '$year'";}	
		
		$orders = $wpdb->get_results($sql );
		$orders_total = $this->fetch_result($orders);
		$orders_total = $orders_total->total;
		$this->total_result = $orders_total ;
		
		$total_pages = $orders_total;
				
		$limit = "";
		$current_page = $ini;
		$target_page =  site_url()."/wp-admin/admin.php?page=userultra&tab=orders";
		
		$how_many_per_page =  $howmany;
		
		$to = $how_many_per_page;
		
		//caluculate from
		$from = $this->calculate_from($ini,$how_many_per_page,$orders_total );
		
		$this->pages = $xoouserultra->commmonmethods->getPages($total_pages, $current_page, $target_page, $how_many_per_page);
		
		//get all			
		
		$sql = 'SELECT ord.*, usu.*, package.package_id, package.package_name	 FROM ' . $wpdb->prefix . 'usersultra_orders ord  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."users  usu ON (usu.ID = ord.order_user_id)";
		$sql .= " RIGHT JOIN ".$wpdb->prefix ."usersultra_packages  package ON (package.package_id = ord.	order_package_id)";		
		$sql .= " WHERE ord.order_id <> 0 AND package.package_id = ord.order_package_id";		
			
		if($keyword!="")
		{
			$sql .= " AND (ord.order_txt_id LIKE '%".$keyword."%' OR usu.display_name LIKE '%".$keyword."%' OR usu.user_email LIKE '%".$keyword."%' OR usu.user_login LIKE '%".$keyword."%'  )  ";
		}
		
		if($day!=""){$sql .= " AND DAY(ord.order_date) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(ord.order_date) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(ord.order_date) = '$year'";}	
		
		$sql .= " ORDER BY ord.order_id DESC";		
		
	    if($from != "" && $to != ""){	$sql .= " LIMIT $from,$to"; }
	 	if($from == 0 && $to != ""){	$sql .= " LIMIT $from,$to"; }
		
					
		$orders = $wpdb->get_results($sql );
		
		return $orders ;
		
	
	}
	
	public function calculate_from($ini, $howManyPagesPerSearch, $total_items)	
	{
		if($ini == ""){$initRow = 0;}else{$initRow = $ini;}
		
		if($initRow<= 1) 
		{
			$initRow =0;
		}else{
			
			if(($howManyPagesPerSearch * $ini)-$howManyPagesPerSearch>= $total_items) {
				$initRow = $totalPages-$howManyPagesPerSearch;
			}else{
				$initRow = ($howManyPagesPerSearch * $ini)-$howManyPagesPerSearch;
			}
		}
		
		
		return $initRow;
		
		
	}
	
	public function fetch_result($results)
	{
		if ( empty( $results ) )
		{
		
		
		}else{
			
			
			foreach ( $results as $result )
			{
				return $result;			
			
			}
			
		}
		
	}
	
	public function get_order_pending ($id)
	{
		global $wpdb,  $xoouserultra;
		
		$orders = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'usersultra_orders WHERE order_key = "'.$id.'"  AND order_status="pending" ' );
		
		if ( empty( $orders ) )
		{
		
		
		}else{
			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
	
	}
	
	public function get_order_confirmed ($id)
	{
		global $wpdb,  $xoouserultra;
		
		$orders = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'usersultra_orders WHERE order_key = "'.$id.'"  AND order_status="confirmed" ' );
		
		if ( empty( $orders ) )
		{
		
		
		}else{
			
			
			foreach ( $orders as $order )
			{
				return $order;			
			
			}
			
		}
		
	
	}
	
	/*Get Latest*/
	public function get_latest_user ($user_id, $howmany)
	{
		global $wpdb,  $xoouserultra;
		
		$sql = 'SELECT ord.*, usu.*	 FROM ' . $wpdb->prefix . 'usersultra_orders ord  ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ord.order_user_id)";		
		$sql .= " WHERE ord.order_id <> 0 AND usu.ID = '".$user_id."' ORDER BY ord.order_id desc  LIMIT $howmany";	
			
		$orders = $wpdb->get_results($sql );
		
		return $orders ;		
	
	}
	
	/**
	 * My Orders 
	 */
	function show_my_latest_orders($howmany, $status=null)
	{
		global $wpdb, $current_user, $xoouserultra; 
		
		require_once(ABSPATH . 'wp-includes/pluggable.php');
		require_once(ABSPATH. 'wp-admin/includes/user.php' );
		require_once(ABSPATH.  'wp-includes/query.php' );	
		
		
		$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
		
		
		$user_id = get_current_user_id();
		
		
		
		 
		
        $drOr = $this->get_latest_user($user_id,30);
		
		//print_r($loop );
				
		
		if (  empty( $drOr) )
		{
			echo '<p>', __( 'You have no orders.', 'xoousers' ), '</p>';
		}
		else
		{
			$n = count( $drOr );
			
			
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
						<th class="manage-column" ><?php _e( 'Package', 'xoousers' ); ?></th>
                        <th class="manage-column" ><?php _e( 'Status', 'xoousers' ); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
							
							foreach ( $drOr as $order){
							$order_id = $order->order_id;
							
							//get package
							
							$package = $xoouserultra->paypal->get_package($order->order_package_id);
							
							
							//print_r($order );
							
							?>
						<tr>
							                         
                            
							<td>#<?php echo $order_id; ?></td>
                            <td><?php echo  $currency_symbol.$order->order_amount?></td>
							<td> <?php echo $order->order_date; ?></td>
							<td><?php echo $package->package_name; ?></td>
                            <td><?php echo $order->order_status; ?></td>
                            
                            
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
	
	
	

}
$key = "order";
$this->{$key} = new XooOrder();