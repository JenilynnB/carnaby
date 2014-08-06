<?php
define('uultraxoousers_pro_url','http://usersultra.com/');
/* Load plugin text domain (localization) */
add_action('init', 'xoousers_load_textdomain');

function xoousers_load_textdomain() 
{
        $locale = apply_filters( 'uultra_locale', get_locale() );
        $mofile = xoousers_path . "languages/xoousers-$locale.mo";
		
        if ( file_exists( $mofile ) ) 
		{
            load_plugin_textdomain( 'xoousers', false,  $mofile );
        }
}
	
		
add_action('init', 'xoousers_output_buffer');
function xoousers_output_buffer() {
		ob_start();
}