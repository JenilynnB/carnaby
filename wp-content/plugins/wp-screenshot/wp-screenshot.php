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
    
    //Start by checking if the standard listing thumbnail exists and/or needs to be uploaded
    $std_thumb_id = get_thumbnail_id($listing_id);
    $url = wpbdp_render_listing_field('URL', $listing_id);
    $url = $url[0];
    if($std_thumb_id){
        $image = get_post($std_thumb_id);
        //Looks for the naming convention of previous programmatically uploaded screenshots 
        if(strpos($image->post_title, "screenshot")||!($image)){
            //If the screenshot exists, check if it was taken more than a week ago
            $date_diff = date_diff(new DateTime(date("Y-m-d")), new DateTime($image->post_date));
            if($date_diff->d >= 7){
                //upload a new screenshot
                //echo "Regular thumbnail needs updating";
                $screenshot_url = get_screenshot_url($url, $width);
                save_image($screenshot_url, $listing_id);
            }
        }
    
        /* Create an array with the categories and urls for each image */
        $listing_categories = get_top_apparel_categories_with_kids($listing_id);
        /* Cycle through each category to check if it needs a new screenshot */
        foreach($listing_categories as $lc){
            $category_thumb_id = get_thumbnail_id($listing_id, $lc->slug);
            /* If there is a screenshot for the category, check if it needs updating
             * If no thumbnail id is returned, it is added
             */

            //echo "category thumb id: ".$category_thumb_id;
            //Get the URL for that thumbnail type
            if($lc->slug=="women"){
                $cat_url = get_field('womens_url');
            }else if($lc->slug=="men"){
                $cat_url = get_field("mens_url");
            }else if($lc->slug=="kids-baby"){
                $cat_url = get_field("kids_url");
            }else if($lc->slug=="girls"){
                $cat_url = get_field("girls_url");
            }else if($lc->slug=="boys"){
                $cat_url = get_field("boys_url");
            }else if($lc->slug=="baby"){
                $cat_url = get_field("baby_url");
            }
            
            if($category_thumb_id){
                $image = get_post($category_thumb_id);

                //Looks for the naming convention of previous programmatically uploaded screenshots 
                if(strpos($image->post_title, "screenshot")){
                    //If the screenshot exists, check if it was taken more than a week ago
                    $date_diff = date_diff(new DateTime(date("Y-m-d")), new DateTime($image->post_date));
                    // If screenshot was taken more than a week ago OR the category
                    // has the same thumbnail as the standard AND the url is set (which means the URL has
                    // been added recently), proceed to updating the screenshot

                    if($date_diff->d < 7 && !($category_thumb_id == $std_thumb_id && ($cat_url || $cat_url!=""))){
                        continue;
                    }
                }else{
                    continue;
                }
            }


            //if it doesn't exist, set the category thumbnail as the standard thumbnail
            if(!$cat_url||$cat_url==""){

                //echo "Setting thumb id, no url ".$std_thumb_id.$lc->slug;
                set_thumbnail_id($listing_id, $std_thumb_id, $lc->slug);
            }else{
                //get the screenshot URL and upload a new image
                //echo "Adding category thumbnail ". $lc->slug;
                $screenshot_url = get_screenshot_url($cat_url, $width);
                save_image($screenshot_url, $listing_id, $lc->slug);
            }
        }
    
    }else{
        //echo "Regular thumbnail is missing ";
        $screenshot_url = get_screenshot_url($url, $width);
        save_image($screenshot_url, $listing_id);
    }
    
   /* 
    //Check if an image already exists for this listing
    $media = get_attached_media('image/jpeg', $listing_id);
    
    if(!empty($media)){
        //cycle through all attached media and look for a screenshot match
        foreach($media as $m){
            //Looks for the naming convention of previous programmatically uploaded screenshots 
            if(strpos($m->post_title, "screenshot")){
                
                //If the screenshot exists, check if it was taken more than a week ago
                $date_diff = date_diff(new DateTime(date("Y-m-d")), new DateTime($m->post_date));
                
                if($date_diff->d < 7){
                    //If it was taken less than a week ago, return
                    
                    //These lines here for testing purposes. Should be commented out on prod
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
    */
    
    
        
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

function save_image($url, $listing_id, $category=""){
    //Get the image from the URL and save it in a temporary location
    //$response = wp_remote_get($url);
    //echo "listing id: ".$listing_id." category: ".$category." url: ".$url;
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
            
            
            $filesize = filesize( get_attached_file( $attachment_id ) );
            /*Checks to see if the filesize is less than 10kb, which means that the file is most likely 
             * the wordpress loading image. We do not want to set this as the thumbnail for the listing.
             */
            if($filesize>40000){
                $old_thumb_id = get_thumbnail_id($listing_id, $category);
                //delete the old image
                wp_delete_attachment($old_thumb_id);
                //Save the new image media id to the listing
                wp_update_post(array('ID' => $attachment_id, 'post_parent' => $listing_id));
                set_thumbnail_id($listing_id, $attachment_id, $category);
                
                //Find if there are other categories using that thumbnail and update them too
                $listing_categories = get_top_apparel_categories_with_kids($listing_id);
                foreach($listing_categories as $lc){
                    if ($lc==$old_thumb_id){
                        set_thumbnail_id($listing_id, $attachment_id, $lc->slug);
                    }
                }
                
                
                /*
                 * Goes through all media and removes old attachments
                 */
                
                /*
                //Look through existing attached media and delete existing screenshots
                $media = get_attached_media('image/jpeg', $listing_id);
                if(!empty($media)){
                    foreach($media as $m){
                        $title_length = strlen($post_title);
                        $nospace_post_title = preg_replace("/[\s_]/", "-",$post_title."-screenshot");

                        if(strcmp(substr($m->post_title, 0, $title_length+11),$nospace_post_title)==0){
                            wp_delete_attachment($m->ID);
                        }
                        
                    }
                }
                 *
                 */
            }else{
                wp_delete_attachment($attachment_id);
            }
            return $attachment_id;
    }
    unlink($upload);
    
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
    
    if($content!=""){
        if(strstr($content, "http://")){
            $content = substr($content, 7);
            $screenshot_base_url = "http://s.wordpress.com/mshots/v1/http%3A%2F%2F";
        }else if(strstr($content, "https://")){
            $content = substr($content, 8);
            $screenshot_base_url = "http://s.wordpress.com/mshots/v1/https%3A%2F%2F";
        }else{
            $screenshot_base_url = "http://s.wordpress.com/mshots/v1/https%3A%2F%2F";
        }
        
        $screenshot_url = $screenshot_base_url.$content;
        //echo $screenshot_url;
    }else{
        return false;
    }
   
    if($width >0){
        $screenshot_url .= "?w=".$width;
    }
    
    return $screenshot_url;
    
}



?>