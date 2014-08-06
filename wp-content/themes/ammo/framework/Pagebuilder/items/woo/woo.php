<?php


function blox_parse_woo_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'title' => '',
                'type' => 'recent_products',
                'per_page' => '12',
                'columns' => '4',
                'orderby' => '',
                'extra_class' => '',
                'skin' => 'default'
                    ), $atts));

    $result = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';

    $result .= "[$type per_page='$per_page' columns='$columns' ".($orderby=='1' ? "orderby='date' order='desc'" : "")."]";
    
    /**
	 * Check if WooCommerce is active
	 **/
	if ( !class_exists('Woocommerce') && !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	    return "Please install / activate the WooCommerce plugin on your site.";
	}
    
    $result = "<div class='blox-element blox-woocommerce woo-$columns $extra_class' data-column='$columns'>$result</div>";

    return do_shortcode($result);
}

add_shortcode('blox_woo', 'blox_parse_woo_hook');

?>