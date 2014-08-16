<?php
/**
 * Search & Filter Pro
 * 
 * @package   Search_Filter_Display_Shortcode
 * @author    Ross Morsali
 * @link      http://www.designsandcode.com/
 * @copyright 2014 Designs & Code
 */

//form prefix


class Search_Filter_Display_Results {
	
	
	public function __construct($plugin_slug)
	{
		/*
		 * Call $plugin_slug from public plugin class.
		 */
		
		//$plugin = Search_Filter::get_instance();
		$this->plugin_slug = $plugin_slug;

	}
	
	public function output_results($sfid, $settings)
	{
		global $sf_form_data;
		
		$returnvar = "";
		
		$returnvar .= "<div class=\"search-filter-results\" id=\"sf-results-".$sfid."\">";
		$returnvar .= "";
		$returnvar .= "</div>";
		
		return $returnvar;
	}
	
	
}
