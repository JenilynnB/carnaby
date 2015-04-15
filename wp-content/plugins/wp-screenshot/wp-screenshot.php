<?php
/* Plugin Name: WP Screenshot
 * Plugin URI: http://www.larsbachmann.dk/screenshots-af-websites-i-wordpress.html
 * Description: Just insert a simple shortcode to show a screenshot of any website.
 * Author: Lars Bachmann
 * Author URI: http://www.larsbachmann.dk/
 * Stable tag: 1.4
 * Version: 1.4
 */

function myScreenshot($atts, $content = null) {
	 extract(shortcode_atts(array(  
        "width" => 'width'  
    ), $atts));  
return '<img src="http://s.wordpress.com/mshots/v1/http%3A%2F%2F'.$content.'?w=' . esc_attr($width) . '" />';
}
add_shortcode("screenshot", "myScreenshot");




/*
 * This method is called if the existing screenshot is out-of-date or doesn't exist yet. Gets the screenshot
 * at the provided url, with the specified attributes.
 * 
 * @param array $atts {
 *      Attributes specified for the screenshot url to be formed. 
 *      @param int $width. Width of the screenshot to get (only works with wordpress mshots method)
 *      @param array $ids. List of IDs or classes of any js popup elements to close on the page to screenshot
 *      @param array $functions. List of js functions to run to close any js popup elements on page to screenshot
 *      @param int $listing_id. ID of the listing with the screeshot to update
 * }
 * @param string $content URL of the page to take a screenshot of
 * 
 * Returns: None
 * 
 */

function updateScreenshot($post){
    
    $width = 960;
    
    $listing_id = $post->ID;
    
    //Check if the post this method is being called on is a Business Directory Listing
    $post_type = get_post_type($listing_id);
    if(strcmp($post_type, WPBDP_POST_TYPE)!= 0){
        return;
    }
    
    
    $url = wpbdp_render_listing_field('URL', $listing_id);
    $url = $url[0];
    $ids = get_field('screenshot_modal_ids', $listing_id);
    $functions = get_field('screenshot_modal_functions', $listing_id);
    
/*            
    extract(shortcode_atts(array(  
        "width" => 'width',
        "ids" => '',
        "functions" => '',
        "listing_id" => ''
    ), $atts)); 
    
*/    
    
    //Check if an image already exists for this listing
    $media = get_attached_media('image/jpeg', $listing_id);
    $post_title = get_the_title($listing_id);
    
    if(!empty($media)){
        //cycle through all attached media and look for a screenshot match
        foreach($media as $m){
            //Looks for the naming convention of previous programmatically uploaded screenshots 
            if(strpos($m->post_title, "screenshot")){
                
                //If the screenshot exists, check if it was taken more than a week ago
                $date_diff = date_diff(new DateTime(date("Y-m-d")), new DateTime($m->post_date));
                
                if($date_diff->d < 7){
                    //If it was taken less than a week ago, return
                    
                    /*These lines here for testing purposes. Should be commented out on prod*/
                    //$screenshot_url = get_screenshot_url($url, $width);
                    //save_image($screenshot_url, $listing_id);
                    
                    return;
                }
            }else{
                //if there is media uploaded and doesn't follow the programmatically uploaded
                //screenshot naming convention, do not overwrite it.
                return;
            }
        }
    }
    
    /*If the code proceeds to this point, the screenshot exists and was taken more than a week ago OR the
     * screenshot doesn't exist, take a new one.
     */
    
    
    
    //One of these two following sections should always be commented out. Used to switch the method of getting screenshots
    
    /*Use wordpress' native screenshot app to get screenshot*/ 
    $screenshot_url = get_screenshot_url($url, $width);
    save_image($screenshot_url, $listing_id);
    
    /*Use Martin's method to get screenshot*/
    //$screenshot_url = get_martin_screenshot_url($url, $ids, $functions);
    //save_martin_image($screenshot_url, $listing_id);
    
        
}
//add_shortcode("update_screenshot", "updateScreenshot");
add_action('the_post', 'updateScreenshot');

//add_action('wp_async_the_post', 'updateScreenshot', 10, 2);


/*
 * This function downloads and saves the image to the database for the specificied listing
 * 
 * @param string $url. URL of the page to take a screenshot of, formed with the correct parameters
 * @param int $listing_id. ID of the listing to attach the screenshot to once it is taken
 * 
 * 
 * Returns: int ID of the attachment that was created from the newly created file/post
 */

function save_image($url, $listing_id){
    //Get the image from the URL and save it in a temporary location
    //$response = wp_remote_get($url);
    
    $tmp = download_url($url);
    
    $post_title = get_the_title($listing_id);
    
    if(!is_wp_error($tmp)){
        $file_array = array(
            'name' => $post_title.'-screenshot.png',
            'type' => 'image/png',
            'tmp_name' => $tmp,
            'error' =>0,
            'size' => filesize($tmp)
        );
        //echo $file_array['size'];
        
        $overrides = array(
            
            // tells WordPress to not look for the POST form
            // fields that would normally be present, default is true,
            // we downloaded the file from a remote server, so there
            // will be no form fields
            'test_form' => false,

            // setting this to false lets WordPress allow empty files, not recommended
            'test_size' => true,

            // A properly uploaded file will pass this test. 
            // There should be no reason to override this one.
            'test_upload' => true,
        );
    }
    
    // move the temporary file into the uploads directory
    $upload = wp_handle_sideload( $file_array, $overrides );
    
    
    if (!empty($upload['error'])) {
	// insert any error handling here
    } else {

        $filename = $upload['file']; // full path to the file
        $local_url = $upload['url']; // URL to the file in the uploads dir
        $type = $upload['type']; // MIME type of the file
        
        // perform any actions here based in the above results
    }
    
    
    if ( $attachment_id = wp_insert_attachment(array(
            'post_mime_type' => $type,
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content' => '',
            'post_status' => 'inherit'
        ), $upload['file']) ) {
            
            //get the size and dimensions of the file specified ($filename);
        
        
            $attach_metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
            wp_update_attachment_metadata( $attachment_id, $attach_metadata );
            
            //Look through existing attached media and delete existing screenshots
            $media = get_attached_media('image/jpeg', $listing_id);
            
            $filesize = filesize( get_attached_file( $attachment_id ) );
            
            /*Checks to see if the filesize is less than 10kb, which means that the file is most likely 
             * the wordpress loading image. We do not want to set this as the thumbnail for the listing.
             */
            if($filesize>10000){
                //Save the new image media id to the listing
                wp_update_post(array('ID' => $attachment_id, 'post_parent' => $listing_id));
                set_thumbnail_id($listing_id, $attachment_id);
                
                /*
                 * Goes through all media and removes old attachments
                 */
                if(!empty($media)){
                    foreach($media as $m){
                        $title_length = strlen($post_title);
                        $nospace_post_title = preg_replace("/[\s_]/", "-",$post_title."-screenshot");

                        if(strcmp(substr($m->post_title, 0, $title_length+11),$nospace_post_title)==0){
                            wp_delete_attachment($m->ID);
                        }
                        /*
                        if($filesize<10000){
                            wp_delete_attachment($m->ID);
                        }
                         * 
                         */
                    }
                }
            }else{
                wp_delete_attachment($attachment_id);
            }
            return $attachment_id;
    }
    unlink($upload);
    
}


/*
 * THIS FUNCTION HAS NOT BEEN TESTED YET, WAITING ON FIX FOR MARTIN'S URL
 * 
 * This function downloads and saves the image to the database for the specificied listing, using the URL that 
 * Martin Madsen has provided. This URL will throw a 500 error using the download_url() method, so an
 * alternate method will be used
 * 
 * @param string $url. URL of the page to take a screenshot of, formed with the correct parameters
 * @param int $listing_id. ID of the listing to attach the screenshot to once it is taken
 * 
 * 
 * Returns: int ID of the attachment that was created from the newly created file/post
 */

function save_martin_image($url, $listing_id){
    //Get the image from the URL and save it in a temporary location
    
    $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
    $context = stream_context_create($opts);
    $tmp = file_get_contents($screenshot_url,false,$context);
    
    
    $post_title = get_the_title($listing_id);
    if(!is_wp_error($tmp)){
        $file_array = array(
            'name' => $post_title.'-screenshot.png',
            'type' => 'image/png',
            'tmp_name' => $tmp,
            'error' =>0,
            'size' => filesize($tmp)
        );
        //echo $file_array['size'];
        
        $overrides = array(
            
            // tells WordPress to not look for the POST form
            // fields that would normally be present, default is true,
            // we downloaded the file from a remote server, so there
            // will be no form fields
            'test_form' => false,

            // setting this to false lets WordPress allow empty files, not recommended
            'test_size' => true,

            // A properly uploaded file will pass this test. 
            // There should be no reason to override this one.
            'test_upload' => true,
        );
    }
    
    // move the temporary file into the uploads directory
    $upload = wp_handle_sideload( $file_array, $overrides );
    
    
    if (!empty($upload['error'])) {
	// insert any error handling here
    } else {

        $filename = $upload['file']; // full path to the file
        $local_url = $upload['url']; // URL to the file in the uploads dir
        $type = $upload['type']; // MIME type of the file
        
        // perform any actions here based in the above results
    }
    
    
    if ( $attachment_id = wp_insert_attachment(array(
            'post_mime_type' => $type,
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content' => '',
            'post_status' => 'inherit'
        ), $upload['file']) ) {
            $attach_metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
            wp_update_attachment_metadata( $attachment_id, $attach_metadata );
            
            //Look through existing attached media and delete existing screenshots
            $media = get_attached_media('image/jpeg', $listing_id);
            
            //Save the new image media id to the listing
            wp_update_post(array('ID' => $attachment_id, 'post_parent' => $listing_id));
            
            if(!empty($media)){
                foreach($media as $m){
                    $title_length = strlen($post_title);
                    $nospace_post_title = preg_replace("/[\s_]/", "-",$post_title."-screenshot");
                    
                    if(strcmp(substr($m->post_title, 0, $title_length+11),$nospace_post_title)==0){
                        wp_delete_attachment($m->ID);
                    }
                }
            }
            
            return $attachment_id;
    }
    
    
}



/*
 * This function returns the url for the screenshot to get, along with any ids 
 * and functions provided that will be passed to the screenshot engine to close 
 * popups.
 * 
 * @param string $content: the url of the site to get a screenshot for
 * @param int $width: the width of the screenshot image to return
 * 
 * 
 * Returns: string URL of screenshot with parameters added correctly
 */

function get_screenshot_url($content, $width){
    
    //Get the new screenshot
    $screenshot_base_url = "http://s.wordpress.com/mshots/v1/http%3A%2F%2F";
    
    if($content!=""){
        
        $screenshot_url = $screenshot_base_url.$content;
    }else{
        return false;
    }
   
    if($width >0){
        $screenshot_url .= "?w=".$width;
    }
    
    return $screenshot_url;
    
}

/*
 * This function returns the url for the screenshot to get, along with any ids 
 * and functions provided that will be passed to the screenshot engine to close 
 * popups.
 * 
 * @param string $content: the url of the site to get a screenshot for
 * @paran array $ids: an array with a list of ids or classes of the element to click to close
 *      the popup
 *@param array $functions: an array with a list of functions to call to attempt to close a 
 *      popup modal
 * 
 * Returns: string URL of screenshot with parameters added correctly 
 */

function get_martin_screenshot_url($content, $ids = '', $functions = ''){
    
    $screenshot_base_url = "http://merlin-devices.martinpetermadsen.com/test/jennifertest/";
    
    if($content!=""){
        
        $screenshot_url = $screenshot_base_url . "?url=".$content;
        
    }else{
        return false;
    }
    
    if(is_array($ids)){
        $ids = implode(',', $ids);
    }
    
    if(is_array($functions)){
        $functions = implode(',', $functions);
    }
    
    if($ids!=""){
        $screenshot_url .= "&ids=".$ids;
    }
    if($functions!=""){
        $screenshot_url .= "&functions=".$functions;
    }
    
    return $screenshot_url;
    
}

?>