<?php
class UsersUltraPhotoCate {

	var $options;

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'userultra';
		$this->subslug = 'uultra-photocategory';
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$this->plugin_data = get_plugin_data( uultra_dg_path . 'index.php', false, false);
		$this->version = $this->plugin_data['Version'];
		
		/* Priority actions */
		add_action('admin_menu', array(&$this, 'add_menu'), 9);
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		
		add_action( 'wp_ajax_edit_photo_cate', array(&$this, 'edit_photo_cate' ));
		add_action( 'wp_ajax_edit_photo_cate_conf', array(&$this, 'edit_photo_cate_conf' ));
		add_action( 'wp_ajax_edit_photo_cate_del', array(&$this, 'edit_photo_cate_del' ));
		
		
		
	}
	
	function admin_init() 
	{
	
		$this->tabs = array(
			'manage' => __('Manage Photo Categories','xoousers')
			
		);
		$this->default_tab = 'manage';		
		
	}
	
	
	
	public function edit_photo_cate_del ()
	{
		global $wpdb;
		
		$cate_id = $_POST["cate_id"];		
		$query = "DELETE FROM " . $wpdb->prefix ."usersultra_photo_categories  WHERE  `photo_cat_id` = '$cate_id'  ";			
			
		$wpdb->query( $query );	
		
	}
	public function edit_photo_cate_conf ()
	{
		global $wpdb;
		
		$cate_id = $_POST["cate_id"];
		$cate_name= $_POST["cate_name"];
		if($cate_id !="" &&$cate_name!="" )
		{
			$query = "UPDATE " . $wpdb->prefix ."usersultra_photo_categories SET `photo_cat_name` = '$cate_name'  WHERE  `photo_cat_id` = '$cate_id'  ";			
			
			$wpdb->query( $query );
			$html = $cate_name;
			
		}
		
		echo $html;
		die();
		
	}
	
	
	
	public function edit_photo_cate ()
	{
		global $wpdb;
		
		$cate_id = $_POST["cate_id"];
		
		if($cate_id!="")
		{
		
			$res = $wpdb->get_results( 'SELECT *  FROM ' . $wpdb->prefix . 'usersultra_photo_categories WHERE `photo_cat_id` = ' . $cate_id . '  ' );
			
			$html="";
			foreach ( $res as $photo )
			{
				
				$html .="<p>".__( 'Name:', 'xoousers' )."</p>";
				
				$html .="<p><input type='text' value='".$photo->photo_cat_name."' class='xoouserultra-input' id='uultra_photo_name_edit_".$photo->photo_cat_id."'></p>";
				
				
				$html .="<p><input type='button' class='button-primary uultra-photocat-close' value='".__( 'Close', 'xoousers' )."' data-id= ".$photo->photo_cat_id."> <input type='button'  class='button-primary uultra-photocat-modify' data-id= ".$photo->photo_cat_id." value='".__( 'Save', 'xoousers' )."'> </p>";
				
								
			}		
			
					
		}
		
		echo $html;
		die();
		
	}
	
	public function get_photo_categories () 
	{
		global $wpdb;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . 'usersultra_photo_categories ORDER BY photo_cat_name ASC  ' ;
		$res = $wpdb->get_results($sql);
		return $res ;	
	
	}
	
	function admin_head(){

	}

	function add_styles(){
	
		wp_register_script( 'uultra_cate_js', uultra_dg_url . 'admin/scripts/admin.js', array( 
			'jquery'
		) );
		wp_enqueue_script( 'uultra_cate_js' );
	
		wp_register_style('uultra_cate_css', uultra_dg_url . 'admin/css/admin.css');
		wp_enqueue_style('uultra_cate_css');
		
	}
	
	function add_menu()
	{
		add_submenu_page( 'userultra', __('Photo Categories','xoousers'), __('Photo Categories','xoousers'), 'manage_options', 'uultra-photocategory', array(&$this, 'admin_page') );
		
		do_action('userultra_admin_menu_hook');
		
		
	}

	function admin_tabs( $current = null ) {
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				else :
					$links[] = "<a class='nav-tab' href='?page=".$this->subslug."&tab=$tab'>$name</a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	function get_tab_content() {
		$screen = get_current_screen();
		if( strstr($screen->id, $this->subslug ) ) {
			if ( isset ( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = $this->default_tab;
			}
			require_once uultra_dg_path.'admin/panels/'.$tab.'.php';
		}
	}
	
	public function save()
	{
		global $wpdb;
		
		if(isset($_POST['photo_cat_name']))
		{
		
			$photo_cat_name = $_POST['photo_cat_name'];			
			$new_array = array(
							'photo_cat_id'     => null,
							'photo_cat_name'   => $photo_cat_name						
							
							
						);
						
				
			$wpdb->insert( $wpdb->prefix . 'usersultra_photo_categories', $new_array, array( '%d', '%s'));
		}
	
	
	}
	
	
	function admin_page() {
	
		
		if (isset($_POST['add-category']) && $_POST['add-category']=='add-cate') 
		{
			$this->save();
		}

		
		
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin">
			
						
			<h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?></h2>

			<div class="<?php echo $this->slug; ?>-admin-contain">
				
				<?php $this->get_tab_content(); ?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }

}

$uultra_photo_category = new UsersUltraPhotoCate();