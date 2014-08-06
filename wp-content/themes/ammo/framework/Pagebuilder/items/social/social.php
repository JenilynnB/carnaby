<?php

function blox_parse_social_hook( $atts, $content=null ) {
	extract( shortcode_atts( array(
		'fbshare' => '',
        'tweet' => '',
		'gplus' => '',
		'pinterest' => '',
        'align' => 'left',
        'animation' => '',
        'extra_class' => ''
	), $atts ) );

	$animate_class = get_blox_animate_class($animation);

	$html = '';
	$html .= $fbshare=='1' ? "<span class='st_facebook_hcount' displayText='Facebook'></span>" : '';
	$html .= $tweet=='1' ? "<span class='st_twitter_hcount' displayText='Google +'></span>" : '';
	$html .= $gplus=='1' ? "<span class='st_googleplus_hcount' displayText='Google +'></span>" : '';
	$html .= $pinterest=='1' ? "<span class='st_pinterest_hcount' displayText='Pinterest'></span>" : '';

	$script = '<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
			   <script type="text/javascript"> if( typeof stLight!=="undefined" ){ stLight.options({publisher: "e6b1ba09-bf07-47cb-951a-cf58d1c03f3a", doNotHash: true, doNotCopy: false, hashAddressBar: false}); }</script>';

	return "<div class='blox-element blox-element-socials $animate_class $extra_class' style='text-align:$align;'>$html</div>$script";
}
add_shortcode( 'blox_social', 'blox_parse_social_hook' );

?>