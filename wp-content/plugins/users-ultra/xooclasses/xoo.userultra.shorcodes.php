<?php
class XooShortCode {

	function __construct() 
	{
		
		add_action( 'wp_head',   array(&$this,'xoousers_shortcodes'));	
		add_action( 'init', array(&$this,'respo_base_unautop') );	

	}
	
	/**
	* Add the shortcodes
	*/
	function xoousers_shortcodes() 
	{	
	
	    add_filter( 'the_content', 'shortcode_unautop');
			
		add_shortcode( 'usersultra_login', array(&$this,'usersultra_login_function') );
		add_shortcode( 'usersultra_logout', array(&$this,'usersultra_logout_function') );
		add_shortcode( 'usersultra_registration', array(&$this,'usersultra_registration_function') );
		add_shortcode( 'usersultra_my_messages', array(&$this,'usersultra_mymessages_function') );
		add_shortcode( 'usersultra_my_account', array(&$this,'usersultra_my_account_function') );
		add_shortcode( 'usersultra_directory', array(&$this,'usersultra_directory_function') );
		add_shortcode( 'usersultra_directory_mini', array(&$this,'usersultra_directory_mini_function') );
		add_shortcode( 'usersultra_searchbox', array(&$this,'usersultra_searchbox') );
		
				
		
		add_shortcode( 'usersultra_profile', array(&$this,'usersultra_profile_function') );
		
		//Media ShortCodes
		add_shortcode( 'usersultra_photo_top_rated', array(&$this,'usersultra_photo_top_rated') );
		
		add_shortcode( 'usersultra_users_featured', array(&$this,'usersultra_featured_users') );
		add_shortcode( 'usersultra_users_promote', array(&$this,'usersultra_promote_users') );
		
		add_shortcode( 'usersultra_photos_promote', array(&$this,'usersultra_promote_photos') );
		
		 
		add_shortcode( 'usersultra_users_top_rated', array(&$this,'usersultra_top_rated_users') );
		add_shortcode( 'usersultra_users_most_visited', array(&$this,'usersultra_most_visited_users') );
		add_shortcode( 'usersultra_users_latest', array(&$this,'usersultra_latest_users') );		
		add_shortcode( 'usersultra_photo_latest', array(&$this,'usersultra_latest_photos') );
		
		add_shortcode( 'usersultra_images_grid', array(&$this,'usersultra_photo_grid') );
		
		add_shortcode( 'usersultra_protect_content', array(&$this,'funnction_protect_content') );
		
		add_shortcode( 'usersultra_front_publisher', array(&$this,'funnction_front_publisher') );
		
		
		
		
		//
		add_shortcode( 'one_third_first', array(&$this,'respo_base_grid_4_first') );
		add_shortcode( 'one_third',  array(&$this,'respo_base_grid_4'));
		add_shortcode( 'one_third_last',  array(&$this,'respo_base_grid_4_last'));
	
		add_shortcode( 'two_thirds_first',   array(&$this,'respo_base_grid_8_first'));
		add_shortcode( 'two_thirds',  array(&$this,'respo_base_grid_8'));
		add_shortcode( 'two_thirds_last',  array(&$this,'respo_base_grid_8_last') );
	
		add_shortcode( 'one_half_first',  array(&$this,'respo_base_grid_6_first') );
		add_shortcode( 'one_half',  array(&$this,'respo_base_grid_6') );
		add_shortcode( 'one_half_last',  array(&$this,'respo_base_grid_6_last'));
	
		add_shortcode( 'one_fourth_first',   array(&$this,'respo_base_grid_3_first'));
		add_shortcode( 'one_fourth',   array(&$this,'respo_base_grid_3'));
		add_shortcode( 'one_fourth_last',  array(&$this,'respo_base_grid_3_last'));
	
		add_shortcode( 'three_fourths_first',   array(&$this,'respo_base_grid_9_first'));
		add_shortcode( 'three_fourths',  array(&$this,'respo_base_grid_9'));
		add_shortcode( 'three_fourths_last',  array(&$this,'respo_base_grid_9_last'));
	
		add_shortcode( 'one_sixth_first',  array(&$this,'respo_base_grid_2_first'));
		add_shortcode( 'one_sixth',  array(&$this,'respo_base_grid_2'));
		add_shortcode( 'one_sixth_last',   array(&$this,'respo_base_grid_2_last'));
	
		add_shortcode( 'five_sixth_first',   array(&$this,'respo_base_grid_10_first'));
		add_shortcode( 'five_sixth', array(&$this,'respo_base_grid_10'));
		add_shortcode( 'five_sixth_last', array(&$this,'respo_base_grid_10_last') );	
		
		add_shortcode( 'respo_pricing',  array(&$this,'respo_pricing_shortcode') );

		
		
		
	}
	
	/**
	* Don't auto-p wrap shortcodes that stand alone
	*/
	function respo_base_unautop() {
		add_filter( 'the_content',  'shortcode_unautop');
	}
	
	public function  usersultra_login_function ($atts)
	{
		global $xoouserultra;
				
		if (!is_user_logged_in()) 
		{
			return $xoouserultra->login( $atts );
			
		} else {
			
			//display mini profile
			wp_redirect("/home");
                        //return $xoouserultra->show_minified_profile( $atts );		
			
			
		}
	
	}
	
	//logout
	public function  usersultra_logout_function ($atts)
	{
		global $xoouserultra;
				
		if (is_user_logged_in()) 
		{
			return $xoouserultra->custom_logout_page( $atts );				
			
		}
	
	}
	
	//Protect Content
	public function funnction_protect_content( $atts, $content = null ) 
	{
		global $xoouserultra;
		return $xoouserultra->userpanel->show_protected_content( $atts, $content );	
	}
	
	//Protect Content
	public function usersultra_searchbox( $atts ) 
	{
		global $xoouserultra;
		return $xoouserultra->userpanel->uultra_search_form( $atts );	
	}
	
	
	
	//Front Publisher
	public function  funnction_front_publisher ($atts)
	{
		global $xoouserultra;		
			
		//display publisher
		return $xoouserultra->show_front_publisher( $atts );		

	
	}
	
	
	
	
	
	
	
	public function  usersultra_photo_top_rated ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_top_rated_photos( $atts );			
		
	}
	
	public function  usersultra_latest_photos ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_latest_photos( $atts );			
		
	}
	
	public function  usersultra_photo_grid ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_photo_grid( $atts );			
		
	}
	
	
	
	
	
	public function  usersultra_featured_users ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_featured_users($atts );
		
	}
	
	public function  usersultra_promote_users ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_promoted_users($atts );
		
	}
	
	public function  usersultra_promote_photos ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_promoted_photos($atts );
		
	}
	
	public function  usersultra_latest_users ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_latest_users($atts );
			
		
	}
	
	
	
	public function  usersultra_top_rated_users ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_top_rated_users($atts );
			
		
	}
	
	public function  usersultra_most_visited_users ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_most_visited_users($atts );
			
		
	}
	
	
	public function  usersultra_profile_function ($atts)
	{
		global $xoouserultra;
		
		if (!is_user_logged_in() &&  $xoouserultra->get_option( 'guests_can_view' )==0) 
		{
			return $xoouserultra->login( $atts );
			
		}else{
			
			return $xoouserultra->show_pulic_profile( $atts );
		}
		
		
	}
	
	public function  usersultra_registration_function ($atts)
	{
		global $xoouserultra;
		//return $xoouserultra->show_registration_form( $atts );
		
		if (!is_user_logged_in()) 
		{
			return $xoouserultra->show_registration_form( $atts );
			
		} else {
			
			//display mini profile
			return $xoouserultra->show_minified_profile( $atts );		
			
			
		}
			
		
	}
	
	public function  usersultra_directory_function ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_users_directory( $atts );
			
		
	}
	
	public function  usersultra_directory_mini_function ($atts)
	{
		global $xoouserultra;
		return $xoouserultra->show_users_directory_mini( $atts );
			
	}
	
	

	public function  usersultra_mymessages_function ($atts)
	{
		global $xoouserultra;
		
		if (!is_user_logged_in()) 
		{
			return $xoouserultra->login( $atts );
			
		}else{
			
			return $xoouserultra->show_usersultra_inbox( $atts );
		}
		
		
			
		
	}
	
	public function  usersultra_my_account_function ($atts)
	{
		global $xoouserultra;
		
		if (isset($_GET['resskey'])&& $_GET['resskey']!="") 
		{
			//users is trying to reset passowrd
			
			return $xoouserultra->password_reset( $_GET['resskey'] );
			
			
		
		}else{	
			
		
			if (!is_user_logged_in()) 
			{
				
				
				return $xoouserultra->login( $atts );
				
			}else{					
				
				
				return $xoouserultra->show_usersultra_my_account( $atts );
			}
			
		
		
		}
		
		
	}
	
	
	   /**
	 * Pricing Table
	 * @since 1.1
	 *
	 */
	 
	 function respo_pricing_shortcode( $atts, $content = null  ) 
	 {
		 global $xoouserultra;

		extract( shortcode_atts( array(
			'color' => 'black',
			'position' => '',
			'featured' => 'no',
			'plan_id' => '',
			'per' => 'month',
			'button_url' => '',
			'button_text' => 'Sign up',
			'button_color' => 'green',
			'button_target' => 'self',
			'button_rel' => 'nofollow',
			'class' => '',
		), $atts ) );

		//set variables
		$featured_pricing = ( $featured == 'yes' ) ? 'featured' : NULL;
		
		//get package
		$package = $xoouserultra->paypal->get_package($plan_id);
		
		$amount = $package->package_amount;
		$p_name = $package->package_name;
		$package_id = $package->package_id;
		
		//customization
		$customization = $package->package_customization;
		$customization = unserialize($customization);
		  
		if(is_array($customization))
		{
			  $p_price_color = $customization["p_price_color"];
			  $p_price_bg_color = $customization["p_price_bg_color"];
			  
			  $p_signup_color = $customization["p_signup_color"];
			  $p_signup_bg_color = $customization["p_signup_bg_color"];
			  
			  //customization string 			  
			  $custom_s = 'style="background-color:'.$p_price_bg_color.' !important; color:'.$p_price_color.' !important"';
			  
			  $custom_signup_color = 'style="color:'.$p_signup_color.' !important"';			  
			  $custom_signup_bg_color= 'style="background-color:'.$p_signup_bg_color.' !important; border-color:'.$p_signup_bg_color.' !important; "';
			
		}
		
		//get currency
		$currency_symbol =  $xoouserultra->get_option('paid_membership_symbol');
		
		//generate url		
		$package_url = $xoouserultra->paypal->get_package_url();
		$button_url = $package_url."?plan_id=".$plan_id;	
		
		
		//custom text for free packages		
		$free_text =  $xoouserultra->get_option('membership_display_zero');
		$amount_text = $currency_symbol. $amount;
		
		if($free_text!="" &&  $amount==0)
		{
			$amount_text =$free_text ;
		
		}	

		//start content
		$pricing_content ='';
		$pricing_content .= '<div class="respo-sc-pricing-table ' . $class . '" >';
		$pricing_content .= '<div class="respo-sc-pricing ' . $featured_pricing . ' respo-sc-column-' . $position . ' ' . $class . '">';
			$pricing_content .= '<div class="respo-sc-pricing-header '. $color .'" '. $custom_s.'>';
				$pricing_content .= '<h5 '. $custom_s.'>' .$p_name . '</h5>';
				$pricing_content .= '<div class="respo-sc-pricing-cost" '. $custom_s.'>' .$amount_text . '</div><div class="respo-sc-pricing-per">' . $per . '</div>';
			$pricing_content .= '</div>';
			$pricing_content .= '<div class="respo-sc-pricing-content">';
				$pricing_content .= '' . $content . '';
			$pricing_content .= '</div>';
			if( $button_url ) {
				$pricing_content .= '<div class="respo-sc-pricing-button"><a href="' . $button_url . '" class="respo-sc-button ' . $button_color . '" target="_' . $button_target . '" rel="' . $button_rel . '" '.$custom_signup_bg_color.'><span class="respo-sc-button-inner" '.$custom_signup_color.'>' . $button_text . '</span></a></div>';
			}
		$pricing_content .= '</div>';
		$pricing_content .= '</div><div class="respo-sc-clear-floats"></div>';
		
		return $pricing_content;
	}
	
	/**
    * Columns Shortcodes
   */

	function respo_base_grid_4_first( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_4 alpha">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_4( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_4">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_4_last( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_4 omega">' . do_shortcode($content) . '</div><div class="clear"></div>';
	}
	
	function respo_base_grid_8_first( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_8 alpha">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_8( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_8">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_8_last( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_8 omega">' . do_shortcode($content) . '</div><div class="clear"></div>';
	}
	
	function respo_base_grid_6_first( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_6 alpha">'. do_shortcode($content).'</div>';
	}
	
	function respo_base_grid_6( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_6">'. do_shortcode($content).'</div>';
	}
	
	function respo_base_grid_6_last( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_6 omega">'. do_shortcode($content) .'</div><div class="clear"></div>';
	}
	
	function respo_base_grid_3_first( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_3 alpha">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_3( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_3">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_3_last( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_3 omega">' . do_shortcode($content) . '</div><div class="clear"></div>';
	}
	
	function respo_base_grid_9_first( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_9 alpha">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_9( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_9">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_9_last( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_9 omega">' . do_shortcode($content) . '</div><div class="clear"></div>';
	}
	
	function respo_base_grid_2_first( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_2 alpha">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_2( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_2">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_2_last( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_2 omega">' . do_shortcode($content) . '</div><div class="clear"></div>';
	}
	
	function respo_base_grid_10_first( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_10 alpha">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_10( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_10">' . do_shortcode($content) . '</div>';
	}
	
	function respo_base_grid_10_last( $atts, $content = null ) {
	   return '<div class="respo-sc-grid_10 omega">' . do_shortcode($content) . '</div><div class="clear"></div>';
	}
	
	
	

}
$key = "shortcode";
$this->{$key} = new XooShortCode();