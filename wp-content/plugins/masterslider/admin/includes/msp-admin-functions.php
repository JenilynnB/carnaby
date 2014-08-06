<?php // admin related functions


/**
 * Get all master slider admin screen ids
 *
 * @return array
 */
function msp_get_screen_ids() {
	$the_screen_id = sanitize_title( MSWP_SLUG );

    return apply_filters( 'masterslider_admin_screen_ids', array(
    	'toplevel_page_' . $the_screen_id
    ) );
}


function msp_get_preset_css () {
	return msp_get_option( 'preset_css' , '' );
}

function msp_generate_preset_css () {
	// load and get parser
	$parser 		= msp_get_parser();
	$preset_style  	= msp_get_option( 'preset_style' , '' );
	return $parser->get_preset_styles( $preset_style );
}

function msp_update_preset_css () {
	// store preset css in database
    msp_update_option( 'preset_css' , msp_generate_preset_css() );
}



function msp_get_buttons_css () {
	return msp_get_option( 'buttons_css' , '' );
}

function msp_generate_buttons_css () {
	// load and get parser
	$parser 		= msp_get_parser();
	$buttons_style  = msp_get_option( 'buttons_style' , '' );

	return $parser->get_buttons_styles( $buttons_style );
}

function msp_update_buttons_css () {
	// store buttons css in database
    msp_update_option( 'buttons_css'   , msp_generate_buttons_css() );
}



function msp_get_sliders_custom_css( $slider_status = 'published' ) {
	global $mspdb;
	$slider_status = sprintf( "status='%s'", $slider_status );
	$sliders_result = $mspdb->get_sliders( 0, 0, 'ID', 'DESC', $slider_status );

	$sliders_custom_css = array();
	
	if( $sliders_result ) {
		foreach ( $sliders_result as $slider ) {
			$sliders_custom_css[] = $slider['custom_styles'];
			$sliders_custom_css[] = msp_get_slider_background_css( $slider['ID'] );
		}
		// remove empty records from array
		$sliders_custom_css = array_filter( $sliders_custom_css );
	}
	
	return apply_filters( 'masterslider_get_sliders_custom_css', implode( "\n", $sliders_custom_css ), $sliders_custom_css, $sliders_result );
}


// get stored slider's custom css code from database
function msp_get_slider_custom_css( $slider_id ) {
	global $mspdb;
	$slider_custom_css = $mspdb->get_slider_field_val( $slider_id, 'custom_styles' );
	return $slider_custom_css ? $slider_custom_css : '';
}


function msp_get_slider_background_css( $slider_id ) {
	$slider_data = get_masterslider_parsed_data( $slider_id );

	$the_slider_bg  = empty( $slider_data['setting']['bg_color'] ) ? '' : $slider_data['setting']['bg_color'];
	$the_slider_bg .= empty( $slider_data['setting']['bg_image'] ) ? '' : sprintf( ' url( %s ) repeat top left', msp_get_the_absolute_media_url( $slider_data['setting']['bg_image'] ) ); 
	$the_slider_bg  = empty( $the_slider_bg ) ? '' : 'background:' . $the_slider_bg . ";"; 
	
	return empty( $the_slider_bg ) ? '' : sprintf( ".ms-parent-id-%d > .master-slider{ %s }", $slider_id, $the_slider_bg );
}


function msp_get_all_preset_css () {
	return msp_get_option( 'preset_css' , '' ) . msp_get_option( 'buttons_css' , '' );
}


function msp_get_all_custom_css () {
	$preset_css = msp_get_all_preset_css();
	$sliders_custom_css = msp_get_sliders_custom_css();

	return apply_filters( 'masterslider_get_all_custom_css', $preset_css.$sliders_custom_css, $preset_css, $sliders_custom_css );
}


/*-----------------------------------------------------------------------------------*/


/**
 * Whether the license info is valid or not
 * 
 * @param  string $username      envato username
 * @param  string $api_key       envato user secret api
 * @param  string $purchase_code item purchase code
 * @return bool   True if license info is valid and False otherwise
 */
function msp_is_valid_license ( $username, $api_key, $purchase_code ) {

    if( empty( $username ) || empty( $api_key ) || empty( $purchase_code ) ) {
        return false;
    }

    $api_url = rawurldecode( sprintf( 'http://marketplace.envato.com/api/edge/%s/%s/download-purchase:%s.json', $username, $api_key, $purchase_code ) );
    
    $request = wp_remote_get( $api_url );

    if ( is_wp_error( $request ) || wp_remote_retrieve_response_code($request) !== 200 ) {
        return false;
    }

    $result = json_decode( $request['body'], true );

    if( ! isset( $result['download-purchase']['download_url'] ) ) {
        return false;
    }

    return true;
}


/**
 * Activate license if license info is correct
 * 
 * @param  string $username      envato username
 * @param  string $api_key       envato user secret api
 * @param  string $purchase_code item purchase code
 * @return bool   True if license successfully activated, False on failure
 */
function msp_maybe_activate_license( $username, $api_key, $purchase_code ){
	$is_valid = msp_is_valid_license ( $username, $api_key, $purchase_code );
	update_option( MSWP_SLUG . '_is_license_actived', (int)$is_valid );
	return $is_valid;
}


/*-----------------------------------------------------------------------------------*/

/**
 * Get custom styles and store them in custom.css file or use inline css fallback
 * This function will be called by masterslider save handler
 * 
 * @return void
 */
function msp_save_custom_styles() {
    
    $css_file  = MSWP_AVERTA_DIR . '/assets/custom.css';
    
    $css_terms = "/*
===============================================================
  #CUSTOM CSS
 - Please do not edit this file. this file is generated from admin area.
 - Every changes here will be overwritten
===============================================================*/\n
";
    // Get all custom css styles
    $css = msp_get_all_custom_css();

    // write to custom.css file
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    WP_Filesystem();
    global $wp_filesystem;
    
    if ( ! $wp_filesystem->put_contents( $css_file, $css_terms.$css, 0644 ) ) {
        // if the directory is not writable, try inline css fallback
        msp_update_option( 'custom_inline_style' , $css ); // save css rules as option to print as inline css
    }else {
    	$custom_css_ver = get_option( 'masterslider_custom_css_ver', '1.0' );
    	$custom_css_ver = (float)$custom_css_ver + 0.1;
        msp_update_option( 'masterslider_custom_css_ver' , $custom_css_ver ); // disable inline css output
        msp_update_option( 'custom_inline_style' , '' );
    }

}












/**
 * Prints Pretty human-readable information about a variable (developer debug tool)
 * @param  mixed             The expression to be printed.
 * @param  boolean $dump     Whether to dump information about a variable or not
 * @param  boolean $return   When this parameter is set to TRUE, it will return the information rather than print it.
 * @return bool              When the return parameter is TRUE, this function will return a string. Otherwise, the return value is TRUE.
 */
if ( ! function_exists( 'axpp' ) ) {

	function axpp ( $expression, $dump = false, $return = false ) {
		if ( $return ) {
			return '<pre>' . print_r( $expression , true ) . '</pre>';
		} elseif ( $dump ) {
			echo '<pre>'; var_dump( $expression ); echo '</pre>';
		} else {
			echo '<pre>'; print_r ( $expression ); echo '</pre>';
		}
		return true;
	}

}
