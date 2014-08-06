<?php
require_once file_require(BLOX_DIR . 'items/items.php');

function blox_post_type() {
    global $pagenow, $typenow;
    global $settings_url, $modules;

    if (empty($typenow) && !empty($_GET['post'])) {
        $post = get_post($_GET['post']);
        $typenow = $post->post_type;
    }
    if (is_admin() && ($pagenow == 'post-new.php' OR $pagenow == 'post.php')) {
        return $typenow;
    }
    return '-1';
}

function get_current_post_type() {
    global $post, $typenow, $current_screen;

    //we have a post so we can just get the post type from that
    if ( $post && $post->post_type )
        return $post->post_type;

    //check the global $typenow - set in admin.php
    elseif( $typenow )
        return $typenow;

    //check the global $current_screen object - set in sceen.php
    elseif( $current_screen && $current_screen->post_type )
        return $current_screen->post_type;

    //lastly check the post_type querystring
    elseif( isset( $_REQUEST['post_type'] ) )
        return sanitize_key( $_REQUEST['post_type'] );

    //we do not know the post type!
    return '-1';
}




$blox_settings = get_option('blox_settings_data_group');
$post_types = array();//json_decode($blox_settings['blox_settings_data'], true);

global $smof_data;
if( isset($smof_data['pb_posts']) && $smof_data['pb_posts']=='1' ){
    $post_types['post'] = 'post';
}
if( isset($smof_data['pb_pages']) && $smof_data['pb_pages']=='1' ){
    $post_types['page'] = 'page';
}
if( isset($smof_data['pb_port']) && $smof_data['pb_port']=='1' ){
    $post_types['portfolio'] = 'portfolio';
}
if( isset($smof_data['pb_dir']) && $smof_data['pb_dir']=='1' ){
    $post_types['wpbdp_listing'] = 'wpbdp_listing';
}



add_action('admin_enqueue_scripts', 'blox_render_scripts');

function blox_render_scripts() {

    wp_enqueue_script('jquery');

    /* General defined plugins */
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    /* Fontawesome */
    wp_enqueue_style('font-awesome',            get_template_directory_uri().'/assets/plugins/font-awesome/css/font-awesome.min.css');

    /* Simple Line Icons */
    wp_enqueue_style('simple-line-icons',       get_template_directory_uri().'/assets/plugins/simple-line-icons/simple-line-icons.css');
    
    /* Fancybox */
    wp_enqueue_style('fancybox',                get_template_directory_uri().'/assets/plugins/fancybox/jquery.fancybox.css');
    wp_enqueue_script('fancybox',               get_template_directory_uri().'/assets/plugins/fancybox/jquery.fancybox.pack.js', false, false, true);
    
    /* Select-2 */
    wp_enqueue_style('select2',                 get_template_directory_uri().'/assets/plugins/select2/select2.css');
    wp_enqueue_script('select2',                get_template_directory_uri().'/assets/plugins/select2/select2.min.js', false, false, true);

    /* Blox Global Style and Scripts */
    wp_enqueue_style('blox-admin-global-css',   BLOX_PATH.'css/blox-admin-global.css');
    wp_enqueue_script('blox-admin-global-js',   BLOX_PATH.'js/blox-admin-global.js', false, false, true);


    global $post_types;
    if ($post_types != '') {
        foreach ($post_types as $key => $value) {
            if (blox_post_type() == $value) {

                /* Core styles */
                wp_enqueue_style('wp-jquery-ui-dialog');

                /* Core scripts */
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-accordion');
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('jquery-ui-dialog');
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_script('jquery-ui-draggable');
                wp_enqueue_script('jquery-ui-droppable');

                /* Datetime picker */
                wp_enqueue_style('bootstrap-datetimepicker', get_template_directory_uri().'/assets/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css');
                wp_enqueue_script('bootstrap-datetimepicker', get_template_directory_uri().'/assets/plugins/datetimepicker/js/bootstrap-datetimepicker.min.js', false, false, true);

                /* Bootstrap only grid style admin scripts */
                wp_enqueue_style('bootstrap', BLOX_PATH.'css/bootstrap-grid.css');
                wp_enqueue_script('bootstrap', get_template_directory_uri().'/assets/plugins/bootstrap/js/bootstrap.min.js', false, false, true);

                /* jQuery plugins */
                wp_enqueue_script('themeton_shortcode', BLOX_PATH.'js/tinymce_plugin.js', false, false, true);
                wp_enqueue_script('isotope', get_template_directory_uri().'/assets/plugins/isotope.pkgd.min.js', false, false, true);

                /* General Admin Style and Scripts */
                wp_enqueue_style('blox-admin-style', BLOX_PATH.'css/blox.css');
                wp_enqueue_script('blox-admin-script', BLOX_PATH.'js/blox.js', false, false, true);


                include 'items/items.php';
                $files = array();
                $file_index = 0;
                foreach ($blox_items as $item){
                    if( isset($item['js']) && $item['js']!='' ){
                        $files []= BLOX_PATH.'items/'.$item['js'];
                        wp_enqueue_script('blox_render_scripts'.$file_index, BLOX_PATH.'items/'.$item['js'], false, false, true);
                        $file_index++;
                    }
                }
                //$minify = new TTMinify();
                //$minify->minify_admin_script($files);
            }
        }
    }
}



/* Add Button on Editor */
/* ==================================== */
function themeton_shortcode_addbuttons() {
   if ( ( current_user_can('edit_posts') || current_user_can('edit_pages') ) && get_user_option('rich_editing') ) {
     add_filter("mce_external_plugins", "add_themeton_shortcode_tinymce_plugin");
     add_filter('mce_buttons', 'register_themeton_shortcode_button');
   }
}
 
function register_themeton_shortcode_button($buttons) {
   array_push($buttons, "|", "themeton_shortcode");
   return $buttons;
}
 
function add_themeton_shortcode_tinymce_plugin($plugin_array) {
   $plugin_array['themeton_shortcode'] = BLOX_PATH.'js/tinymce_plugin.js';
   return $plugin_array;
}

add_action('init', 'themeton_shortcode_addbuttons');
/* ==================================== */
/* End Section Button on Editor */





$font_metrize = array();

$font_awesome = array();


/* Simple Line Icons */
@ $font_pointer = fopen( get_template_directory()."/assets/plugins/simple-line-icons/icons.txt", "r");
if ($font_pointer) {
    while (!feof($font_pointer)) {
        $font_item = fgets($font_pointer, 999);
        $font_item = trim($font_item . '');
        $font_awesome[] = $font_item;
    }
    fclose($font_pointer);
}

/* Font Awesome */
@ $font_pointer = fopen( get_template_directory()."/assets/plugins/font-awesome/less/icons.less", "r");
if ($font_pointer) {
    while (!feof($font_pointer)) {
        $font_item = fgets($font_pointer, 999);
        $font_item = trim($font_item . '');
        if( substr($font_item, 0, 2) == '.@' ){
            $chars = explode(':', $font_item);
            $font_item = $chars[0];
            $font_item = str_replace('.@{fa-css-prefix}', 'fa', $font_item);
            $font_awesome[] = $font_item;
        }
    }
    fclose($font_pointer);
}

function blox_fonts_admin_head() {
    global $font_metrize;
    global $font_awesome;
    $items = '';
    $index = 0;
    foreach ($font_metrize as $font) {
        $items .= ($index != 0 ? ',' : '') . "'$font'";
        $index++;
    }
    $items_fa = '';
    $index = 0;
    foreach ($font_awesome as $font) {
        $items_fa .= ($index != 0 ? ',' : '') . "'$font'";
        $index++;
    }
    echo '<script type="text/javascript">
			var font_metrize = [' . $items . '];
			var font_awesome = [' . $items_fa . '];
		</script>';
}

add_action('admin_head', 'blox_fonts_admin_head');

//Add Section on Post types
function blox_pagebuilder_section() {
    global $post_types;
    if ($post_types != '') {
        foreach ($post_types as $key => $value) {
            add_meta_box(
                'blox_contentbuilder', __('Blox Content Builder', 'themeton'), 'render_blox_pagebuilder', $value, 'normal', 'high'
            );
        }
    }
}

add_action('add_meta_boxes', 'blox_pagebuilder_section', 1);




foreach ($blox_items as $item) {
    $item_path = '';
    if (isset($item['path']) && $item['path'] != '') {
        $item_path = BLOX_DIR . 'items/' . $item['path'];
    } else {
        $file = $item['item'];
        if (file_exists(BLOX_DIR . "items/$file/$file.php")) {
            $item_path = BLOX_DIR . "items/$file/$file.php";
        }
    }
    include_once file_require($item_path);
}

function blox_define_items_admin_head() {
    global $blox_items;
    echo '<script type="text/javascript">';
    $item_types = '';
    $item_titles = '';
    $item_filters = '';
    $item_icons = '';
    $counter = 0;
    foreach ($blox_items as $item) {
        if ($item['title'] != '') {
            $item_types .= ($counter == 0 ? '' : ',') . '"' . $item['item'] . '"';
            $item_titles .= ($counter == 0 ? '' : ',') . '"' . $item['title'] . '"';
            $item_filters .= ($counter == 0 ? '' : ',') . '"' . $item['element_type'] . '"';
            $item_icons .= ($counter == 0 ? '' : ',') . '"' . $item['element_icon'] . '"';
            $counter++;
        }
    }
    echo "var blox_items = new Array($item_types); ";
    echo "var blox_item_titles = new Array($item_titles); ";
    echo "var blox_item_filters = new Array($item_filters); ";
    echo "var blox_item_icons = new Array($item_icons); ";
    echo '</script>';
}

add_action('admin_head', 'blox_define_items_admin_head');

function render_blox_pagebuilder() {
    //wp_nonce_field(plugin_basename(__FILE__), 'myplugin_noncename');
    global $post;
    ?>

    <div id="blox_template_storage" style="display: none;">
    <?php
    $templates = blox_get_template();
    foreach ($templates as $template) {
        echo '<span><a href="javascript: blox_load_template(&quot;' . $template['id'] . '&quot;);" data-template="' . $template['id'] . '">' . $template['title'] . '</a><i class="fa-times" onclick="blox_remove_template(jQuery(this));"></i></span>';
    }
    ?>
    </div>

    <input type="hidden" id="blox_uri_admin_ajax" value="<?php echo site_url(); ?>/wp-admin/admin-ajax.php" />

    <div class="blox_nav clearfix">
        <a href="javascript:;" class="button" id="blox_add_row"><i class="fa fa-plus-circle"></i> <span>Add Row</span></a>
        <a href="javascript:;" class="button" id="blox_add_element"><i class="fa fa-plus-circle"></i> <span>Add Element</span></a>
		<div class="pull-right">
	        <a href="javascript: switch_blox_builder(false);" id="blox-switch-classic" class="button-primary inline-buttons" title="Switch Classic Editor"><i class="fa-arrow-circle-left"></i> <span>Switch Classic Editor</span></a>
	        <a href="javascript:;" class="button button-primary inline-buttons" id="blox_fullscreen"><i class="fa-arrows-alt"></i> <span>Fullscreen</span></a>
	        <span class="button blox-dropdown inline-buttons" id="blox_templates">
	            Template <i class="fa fa-angle-down"></i>
	            <span class="blox_templates_wrapper">
	                <span class="template_container">
	                    <div id="blox_template_list" class="blox_template_list"></div>
	                   	<a href="javascript: blox_save_template();" class="button-primary">Save Entry as Template</a>
	                </span>
	            </span>
        	</span>
        	<a href="javascript:;" class="button" id="blox_trigger_publish"><i class="fa-globe"></i> <span>Publish</span></a>
        </div>
    </div>

    <div id="blox_preview"></div>

    <div id="blox_popup_window">
        <div class="blox_popup_toolbar">
            <div>
                <span class="title"></span>
                <a href="javascript:;" class="button blox_popup_button_close">&nbsp;&nbsp;Close&nbsp;&nbsp;</a>
                <a href="javascript:;" class="button-primary blox_popup_button_update">&nbsp;&nbsp;Update Element&nbsp;&nbsp;</a>
            </div>
        </div>
        <div class="blox_popup_wrapper"></div>
    </div>


    <?php
}
?>