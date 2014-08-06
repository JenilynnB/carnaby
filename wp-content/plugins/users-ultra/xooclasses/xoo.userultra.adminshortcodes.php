<?php
class XooUserAdminShortCode 
{
	
	public function __construct()
	{
				
		//add_filter('widget_text', array(&$this,'do_shortcode') );			
		add_action( 'init',   array(&$this,'respo_add_shortcode_button') );
	    add_filter( 'tiny_mce_version',  array(&$this,'respo_refresh_mce')  );	
		
			
		
    }

	

	function respo_refresh_mce( $ver ) {
		$ver += 3;
		return $ver;
	}

	

	function respo_add_shortcode_button() 
	{
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) return;
		if ( get_user_option('rich_editing') == 'true') :
		
		
			add_filter('mce_external_plugins',  array(&$this,'respo_add_shortcode_tinymce_plugin') );
			add_filter('mce_buttons',   array(&$this,'respo_register_shortcode_button'));
			
		endif;
	}

	function respo_register_shortcode_button($buttons) 
	{
		
		array_push($buttons, "|", "uultra_shortcodes_button");
		return $buttons;
	}

	function respo_add_shortcode_tinymce_plugin($plugin_array) 
	{
			wp_enqueue_script( 'editor_plugin_short_options', xoousers_url.'/admin/scripts/editor_plugin_short_options.js', array( 'jquery' ),'5.6.0',true);
		
		$plugin_array['USERSULTRAShortcodes'] = xoousers_url . '/admin/scripts/editor_plugin.js';
		return $plugin_array;
			
		
		
	}

}
$key = "adminshortcode";
$this->{$key} = new XooUserAdminShortCode()
?>