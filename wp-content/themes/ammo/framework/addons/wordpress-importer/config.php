<?php


/* Export Widgets
======================================================*/
add_action('wp_ajax_template_widget_data', 'template_widget_data_hook');
add_action('wp_ajax_nopriv_template_widget_data', 'template_widget_data_hook');
function template_widget_data_hook(){
    global $wp_registered_widget_controls;
	$widget_controls = $wp_registered_widget_controls;
	$available_widgets = array();
	foreach ( $widget_controls as $widget ) {
		if ( ! empty( $widget['id_base'] ) && !isset( $available_widgets[$widget['id_base']] ) ){
			$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
			$available_widgets[$widget['id_base']]['name'] = $widget['name'];
		}
	}
	
	$widget_instances = array();
	foreach ( $available_widgets as $widget_data ) {
		$instances = get_option( 'widget_' . $widget_data['id_base'] );
		if ( ! empty( $instances ) ) {
			foreach ( $instances as $instance_id => $instance_data ) {
				if ( is_numeric( $instance_id ) ) {
					$unique_instance_id = $widget_data['id_base'] . '-' . $instance_id;
					$widget_instances[$unique_instance_id] = $instance_data;
				}
			}
		}
	}

	// Gather sidebars with their widget instances
	$sidebars_widgets = get_option( 'sidebars_widgets' ); // get sidebars and their unique widgets IDs
	$sidebars_widget_instances = array();
	foreach ( $sidebars_widgets as $sidebar_id => $widget_ids ) {
		if ( 'wp_inactive_widgets' == $sidebar_id ) {
			continue;
		}
		if ( ! is_array( $widget_ids ) || empty( $widget_ids ) ) {
			continue;
		}
		foreach ( $widget_ids as $widget_id ) {
			if ( isset( $widget_instances[$widget_id] ) ) {
				$sidebars_widget_instances[$sidebar_id][$widget_id] = $widget_instances[$widget_id];
			}
		}
	}

	echo serialize($sidebars_widget_instances);
    exit;
}



function _get_page_by_name($page_name){
	global $wpdb;
	$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type='page'", $page_name ));
	if ( $page )
		return $page;

	return 0;
}




function demo_import_page_styles_only(){
	wp_enqueue_style('demo-importer', trailingslashit(get_template_directory_uri()).'framework/addons/wordpress-importer/importer.css');
}
function demo_import_page_scripts_only(){
	wp_enqueue_script('demo-importer', trailingslashit(get_template_directory_uri()).'framework/addons/wordpress-importer/importer.js', array( 'jquery' ));
}


/* Demo Data Import Hook
======================================================*/
function demo_importer_submenu_page_callback(){

	if( isset($_POST) ){
		demo_importer_save_hook($_POST);
	}

	$msg = '';
	if( isset($_GET['msg']) && $_GET['msg']=='success' ){
		$msg = '<div id="message" class="updated below-h2"><p>Demo Data Imported.</p></div>';
	}


	$templates = '';
	$file_dir = file_require( get_stylesheet_directory().'/framework/addons/wordpress-importer/files/data/' );
    foreach( glob( $file_dir . '*.xml' ) as $filename ) {
        $filename = basename($filename);
        $filename = str_replace('.xml', '', $filename);
        $fname = $filename;
        $fname = substr($fname, 0, strpos($fname, '(') );
    	$fname = str_replace('_', ' ', $fname);
    	$img = substr($fname, 0, 2);
    	$img = file_require( trailingslashit(get_template_directory_uri()).'framework/addons/wordpress-importer/files/thumbs/'.$img.'.png', true );
        $templates .= '<label>
	        				<input type="radio" name="template" value="'.$filename.'" />
	        				<span class="thumb"><img src="'.$img.'" /></span>
	        				<h5 class="label">'.$fname.'</h5>
    				   </label>';
    }

	echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
		echo '<h2>Demo Data Importer</h2>';
		echo $msg;
		echo '<form method="post">
			<table class="form-table">
			<tr>
				<th scope="row"><label>Choose Demo Data:</label></th>
				<td>
					<fieldset class="templates-layouts">'.$templates.'</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label>Import Theme Options:</label></th>
				<td>
					<label>
					<input type="checkbox" name="import_theme_options" value="1" /> Please backup your current setup of your Theme Options.
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><label>Import Revolution Sliders:</label></th>
				<td>
					<label>
					<input type="checkbox" name="import_revo_slider" value="1" /> Import Revolution Sliders which is used on Demo Sites
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><button type="submit" class="button-primary">Import Demo Data</button></th>
				<td></td>
			</tr>
			</table>
		</form>';
	echo '</div>';
}



function demo_importer_save_hook($param){
	if( !class_exists('WP_Import') )
		require file_require(get_template_directory() . '/framework/addons/wordpress-importer' . '/wordpress-importer.php');

	/* File Paths */
	$options_file = file_require(get_template_directory_uri().'/framework/addons/wordpress-importer/files/theme-options.txt', true);
	$options_file_path = file_require(dirname(__FILE__).'/files/theme-options.txt');

	$nav_file = file_require(get_template_directory_uri().'/framework/addons/wordpress-importer/files/navigation.xml', true);
	$nav_file_path = file_require(dirname(__FILE__).'/files/navigation.xml', true);

	$widget_file = file_require(get_template_directory_uri().'/framework/addons/wordpress-importer/files/widgets.txt', true);
	$widget_file_path = file_require(dirname(__FILE__).'/files/widgets.txt', true);


	if( isset($param['template']) && !empty($param['template']) ){

		/* Import Selected Content */
		$data_file = file_require(dirname(__FILE__).'/files/data/'.$param['template'].'.xml');
		preg_match('/\((.*?)\)/', $param['template'], $slug_out);
		$home_slug =  is_array($slug_out) && !empty($slug_out) ? $slug_out[0] : '';
		$home_slug = str_replace('$', '/', $home_slug);
		$home_slug = str_replace('(', '', $home_slug);
		$home_slug = str_replace(')', '', $home_slug);

		if( file_exists($data_file) && !empty($home_slug) ){
			$d = explode('/', $home_slug);
			$home_slug = isset($d[1]) ? $d[1] : $home_slug;
			$page_id_by_name = _get_page_by_name($home_slug);

			if( $page_id_by_name!='0' ){

				wp_delete_post( $page_id_by_name, true );

				$wp_import = new WP_Import();
				$wp_import->fetch_attachments = false;
				ob_start();
				set_time_limit(0);
				$wp_import->import( $data_file );
				ob_end_clean();

				// Set Home Page
				update_option('show_on_front', 'page');
				update_option('page_on_front',  $page_id_by_name);
			}
		}
		/*=== End Contents */



		/* Import Widgets */
		if( file_exists($widget_file_path) ){
			$get_widget_file = wp_remote_get($widget_file);
			$widget_body = $get_widget_file['body'];
			if( !empty($widget_body) ){
				require file_require(get_template_directory() . '/framework/addons/wordpress-importer' . '/widget-importer.php');
				$data = unserialize(trim($widget_body));
				tt_widget_importer($data);
			}
		}
		/*=== End Widgets */


		/* Import Theme Option */
		if( isset($param['import_theme_options']) && $param['import_theme_options']=='1' ){
			if( file_exists($options_file_path) ){
				$get_options_file = wp_remote_get($options_file);
				$options_body = $get_options_file['body'];
				if( !empty($options_body) ){
					$options_content = $options_body;
					$smof_data = unserialize(base64_decode($options_content));
					of_save_options($smof_data);
					echo '<iframe src="'.admin_url().'admin-ajax.php?action=themeton_regenerate_css" style="width:0px;height:0px;visibility:hidden;"></iframe>';
				}
			}
		}
		/*=== End Theme Options */


		/* Revo Sliders */
		if( isset($param['import_revo_slider']) && $param['import_revo_slider']=='1' ){
			require file_require(get_template_directory() . '/framework/addons/wordpress-importer' . '/revo-importer.php');
			tt_revo_importer();

			if( class_exists('MSP_Importer') ){
				require file_require(get_template_directory() . '/framework/addons/wordpress-importer' . '/masterslide-import.php');
				$master_import = new MasterSliderImporter();
				$master_import->import_master_sliders();
			}
		}
		/*=== End Revo Sliders */


		/* Check Navigation Menu */
		$locations = get_theme_mod( 'nav_menu_locations' ); 
		$menus = wp_get_nav_menus();
		$exist_nav = false;
		if( is_array($menus) ) {
			foreach($menus as $menu) {
				if( $menu->name == 'Main Menu' ) {
					$locations['primary'] = $menu->term_id;
					set_theme_mod( 'nav_menu_locations', $locations );
					$exist_nav = true;
				}
			}
		}


		/* Import Main Contents */
		try{
			$main_file = file_require(dirname(__FILE__).'/files/main-content.xml');
			if( file_exists($main_file) && !$exist_nav ){
				$wp_import = new WP_Import();
				$wp_import->fetch_attachments = false;
				ob_start();
				set_time_limit(0);
				$wp_import->import( $main_file );
				ob_end_clean();
			}
		}
		catch(Exception $e){}
		/*=== End Main Contents */



		/* Import Navigation */
		$locations = get_theme_mod( 'nav_menu_locations' ); 
		$menus = wp_get_nav_menus();
		$exist_nav = false;
		if( is_array($menus) ) {
			foreach($menus as $menu) {
				if( $menu->name == 'Main Menu' ) {
					$locations['primary'] = $menu->term_id;
					set_theme_mod( 'nav_menu_locations', $locations );
					$exist_nav = true;
				}
			}
		}
		if( !$exist_nav ){
			if( file_exists($nav_file_path) ){
				$get_nav_file = wp_remote_get($nav_file);
				$nav_body = $get_nav_file['body'];
				if( !empty($nav_body) ){
					/* Import Navigation Content */
					$wp_import = new WP_Import();
					$wp_import->fetch_attachments = true;
					ob_start();
					$wp_import->import( $nav_file_path );
					ob_end_clean();

					/* Set Main Navigation */
					$locations = get_theme_mod( 'nav_menu_locations' ); 
					$menus = wp_get_nav_menus();
					if( is_array($menus) ) {
						foreach($menus as $menu) {
							if( $menu->name == 'Main Menu' ) {
								$locations['primary'] = $menu->term_id;
							}
						}
					}
					set_theme_mod( 'nav_menu_locations', $locations );
					/*=== End Main Navigation */
				}
			}
		}
		/*=== End Navigation */

		//wp_redirect( admin_url( 'admin.php?page=theme-options-importer&msg=success' ) );
		
	}
}
add_action('wp_ajax_themeton_regenerate_css', 'themeton_regenerate_css_hook');
add_action('wp_ajax_nopriv_themeton_regenerate_css', 'themeton_regenerate_css_hook');
function themeton_regenerate_css_hook(){
	global $smof_data;
	generate_css_from_less_hook( $smof_data );
	die(1);
}







/* Theme Activation Hook */
add_action('wp_ajax_themeton_template_init', 'theme_after_switch_hook');
add_action('wp_ajax_nopriv_themeton_template_init', 'theme_after_switch_hook');
function theme_after_switch_hook(){
	themeton_less_init();

	$filename = trailingslashit(get_template_directory())."framework/addons/wordpress-importer/files/blox-templates.txt";
	if( file_exists($filename) ){
		$file_data = file_get_contents(file_require($filename), FILE_USE_INCLUDE_PATH);
		$blox_templates = blox_get_template();
		if( !empty($file_data) && empty($blox_templates) ){
			set_theme_mod( 'blox_templates', trim($file_data) );
		}
	}
	exit;
}
function theme_after_switch_init_hook() {
    
}

/* Theme Switch Hook */
add_action("after_switch_theme", "tt_after_switch_theme", 10 ,  2);
function tt_after_switch_theme($old_theme_name, $old_theme = false){
	update_option('themeton_admin_notice', '1');
}

add_action('admin_head', 'print_script_run_less_compiler');
function print_script_run_less_compiler(){
	if( isset($_GET['activated']) && $_GET['activated']=='true' ){
		echo '<script type="text/javascript">
		    	document.addEventListener("DOMContentLoaded", function() {
		    		var iframe_hook = document.createElement("iframe");
		    		iframe_hook.setAttribute("src", "'.admin_url().'/admin-ajax.php?action=themeton_template_init"); 
		    		iframe_hook.style.width = "0px"; 
					iframe_hook.style.height = "0px";
					iframe_hook.style.visibility = "hidden"; 
					document.body.appendChild(iframe_hook);
				});
				</script>';
	}
}

add_action('admin_notices', 'theme_activation_admin_notice');
function theme_activation_admin_notice(){
	if( get_option('themeton_admin_notice')!==false && get_option('themeton_admin_notice')=="0" ){
		return;
	}
    echo '<div class="updated" id="theme-admin-notice">
	       	<h3 style="text-transform: uppercase;">Welcome to '.wp_get_theme()->template.' theme.</h3>
	       	<p>
       			<a href="'.admin_url().'admin.php?page=theme-options-importer" class="button-primary" style="text-decoration:none;"><i class="fa fa-folder-open"></i> One Click Demo Data</a>
       			<a href="'.admin_url().'customize.php" class="button-primary" style="text-decoration:none;"><i class="fa fa-laptop"></i> Live Customizer</a>
       			<a href="'.admin_url().'admin.php?page=theme-options" class="button-primary" style="text-decoration:none;"><i class="fa fa-wrench"></i> Theme Options</a>
       			<a href="javascript: template_hide_admin_notice();" class="button" style="text-decoration:none;"><i class="fa fa-times"></i> Hide Notice</a>
       		</p>
	    </div>';
}

add_action('wp_ajax_template_hide_admin_notice', 'template_hide_admin_notice_hook');
add_action('wp_ajax_nopriv_template_hide_admin_notice', 'template_hide_admin_notice_hook');
function template_hide_admin_notice_hook(){
	update_option('themeton_admin_notice', '0');
	exit;
}









$themeton_dummy_files = array(
							array(
								'id' 		=> 'file1',
								'title' 	=> 'Home Page',
								'location' 	=> 'data(home).xml'
							),
							array(
								'id' 		=> 'file2',
								'title' 	=> 'About us + Portfolio posts',
								'location' 	=> 'data(about-us).xml'
							),
							array(
								'id' 		=> 'file3',
								'title' 	=> 'Portfolio page',
								'location' 	=> 'data(portfolio-page).xml'
							),
							array(
								'id' 		=> 'file5',
								'title' 	=> 'Team page + Portfolio posts',
								'location' 	=> 'data(team-progress).xml'
							),
							array(
								'id' 		=> 'file6',
								'title' 	=> 'Price table page',
								'location' 	=> 'data(price).xml'
							),
							array(
								'id' 		=> 'file7',
								'title' 	=> 'Full content',
								'location' 	=> 'data(full).xml'
							),
						);


$themeton_dummy_layerslider = 'LayerSlider_Export.json';



/* Import Dummy Data */
add_action('wp_ajax_tt_import_dummy_data', 'tt_import_dummy_data_hook');
add_action('wp_ajax_nopriv_tt_import_dummy_data', 'tt_import_dummy_data_hook');

function tt_import_dummy_data_hook() {
    try {
        global $themeton_dummy_files;
        $file = isset($_POST['tt_file']) ? $_POST['tt_file'] : '';
        $file_location = '';

        if( $file!='' ){
        	foreach ($themeton_dummy_files as $dummy_data){
	            if( $dummy_data['id'] == $file ){
	            	$file_location = $dummy_data['location'];
	            }
	        }

	        if( $file_location!='' ){
	        	$file_location = file_require(dirname(__FILE__).'/files/'.$file_location);

	        	$wp_import = new WP_Import();
				$wp_import->fetch_attachments = true;
				$wp_import->import( $file_location );
	        }

	        // Import LayerSlider
	        tt_import_layerslider_data();

	        echo "1";
        }

        echo "-1";
    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}


function tt_import_layerslider_data(){
	global $themeton_dummy_layerslider;
	global $wpdb;

	if( $themeton_dummy_layerslider=='' || !file_exists(dirname(__FILE__).'/files/'.$themeton_dummy_layerslider) ){
		return -1;
	}

	//  DB stuff
	$table_name = $wpdb->prefix . "layerslider";
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name){
		return -1;
	}

	try{
		// Get decoded file data
		$data = base64_decode(file_get_contents(dirname(__FILE__).'/files/'.$themeton_dummy_layerslider));

		// Parsing JSON or PHP object
		if(!$parsed = json_decode($data, true)) {
			$parsed = unserialize($data);
		}

		// Iterate over imported sliders
		if(is_array($parsed)){
			

			$ls_items = array();
			$layer_sliders = $wpdb->get_results("SELECT data FROM $table_name
                                            WHERE flag_hidden = '0' AND flag_deleted = '0'
                                            ORDER BY date_c ASC LIMIT 100");

			foreach ($layer_sliders as $key => $item){
				$ls_items[] = $item->data;
			}

			// Import sliders
			foreach($parsed as $item) {

				// Fix for export issue in v4.6.4
				if(is_string($item)) {
					$item = json_decode($item, true);
				}

				$data = json_encode($item);

				if( !in_array($data, $ls_items) ){
					// Add to DB
					$wpdb->query(
						$wpdb->prepare("INSERT INTO $table_name (name, data, date_c, date_m)
												VALUES (%s, %s, %d, %d)",
												$item['properties']['title'], $data, time(), time()
											)
						);
				}
			}

			return 1;
		}

		return -1;

	} catch (Exception $e) {
        return -1;
    }
}








add_action('wp_ajax_widget_data_importer', 'widget_data_importer_hook');
add_action('wp_ajax_nopriv_widget_data_importer', 'widget_data_importer_hook');
function widget_data_importer_hook(){

	if( isset($_POST['widget_content']) && !empty($_POST['widget_content']) ){
		/* Import Widgets */
		require file_require(get_template_directory() . '/framework/addons/wordpress-importer' . '/widget-importer.php');
		$data = unserialize( str_replace('\"', '"', trim($_POST['widget_content'])) );
		tt_widget_importer($data);
		echo "Success :( :)";
	}

	echo '<form method="post">
			<textarea name="widget_content" style="width:100%; height:200px;"></textarea>
			<button type="submit">Insert Widgets</button>
		  </form>';

	exit;
}


add_action('wp_ajax_clear_pages_editor_data', 'clear_pages_editor_data_hook');
add_action('wp_ajax_nopriv_clear_pages_editor_data', 'clear_pages_editor_data_hook');
function clear_pages_editor_data_hook(){

	$pages = get_pages();
	foreach ($pages as $page) {
		update_post_meta($page->ID, "_up_less_editor", "");
		if( get_post_meta( $page->ID, '_wp_page_template', true )!='page-ultimate.php' ){
			update_post_meta($page->ID, "_less_page_variables", "");
			echo $page->ID.'<br>';
        }
	}


	echo "done!";

	exit;
}



add_action('wp_ajax_clear_blox_template_data', 'clear_blox_template_data_hook');
add_action('wp_ajax_nopriv_clear_blox_template_data', 'clear_blox_template_data_hook');
function clear_blox_template_data_hook(){
	set_theme_mod('blox_templates', '');
	echo "done!";
	exit;
}


?>