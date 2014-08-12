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
?>