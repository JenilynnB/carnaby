(function($){
	
	"use strict";
	
	$(function(){
		
		
		function set_form_loading($searchform)
		{
			$searchform.stop(true, true).fadeIn("fast");
		}
		
		initSearchForms();
		
		$(".searchandfilter").submit(function(e)
		{
			var $thisform = $(this);
			
			var template_is_loaded = $thisform.attr("data-template-loaded");
			var use_ajax = $thisform.attr("data-ajax");
			var use_ajax_shortcode = $thisform.attr("data-ajax-shortcode");
			
			var $ajax_target_object = jQuery($thisform.attr("data-ajax-target"));
			$ajax_target_object.attr("data-paged", 1);
			
			if(use_ajax_shortcode==1)
			{
				var form_id = $thisform.attr("data-sf-form-id");
				
				if(use_ajax==1)
				{
					
					e.preventDefault();
					
					var timestamp = new Date().getTime();
					
					$thisform.find("input[name=_sf_ajax_timestamp]").remove();
					$thisform.append('<input type="hidden" name="_sf_ajax_timestamp" value="'+timestamp+'" />');
					postAjaxResults($thisform, form_id);
					
					return false;
				}
			
			}
		});
		
		function postAjaxResults($thisform, form_id)
		{
			var use_history_api = 0;
			
			if (window.history && window.history.pushState)
			{
				use_history_api = $thisform.attr("data-use-history-api");
			}
			
			var ajax_target_attr = $thisform.attr("data-ajax-target");
			var ajax_links_selector = $thisform.attr("data-ajax-links-selector");
			
			//var $ajax_target_object = jQuery(ajax_target_attr);
			var $ajax_target_object = jQuery($thisform.attr("data-ajax-target"));
			
			$ajax_target_object.animate({ opacity: 0.5 }, "fast"); //loading
			var pageNumber = $ajax_target_object.attr("data-paged");
			
			
			$thisform.trigger("sf:ajaxstart", [ "Custom", "Event" ]);
			
			var jqxhr = $.post(SF_LDATA.ajax_url+"?action=get_results&paged="+pageNumber, $thisform.serialize(), function(data, status, request)
			{
				$ajax_target_object.html(data);
				
				//setup pagination
				var $pagiPrev = $ajax_target_object.find(".pagi-prev");
				var $pagiNext = $ajax_target_object.find(".pagi-next");
				
				if($pagiNext.length>0)
				{
					$pagiNext.click(function(e){
						
						if(!$(this).hasClass("disabled"))
						{
							e.preventDefault();
							
							var pageNumber = $ajax_target_object.attr("data-paged");
							pageNumber++;
							$ajax_target_object.attr("data-paged", pageNumber);
							
							postAjaxResults($thisform, form_id);
						}
						
						return false;
					});
				}
				if($pagiPrev.length>0)
				{
					$pagiPrev.click(function(e){
						
						e.preventDefault();
						
						if(!$(this).hasClass("disabled"))
						{
							var pageNumber = $ajax_target_object.attr("data-paged");
							pageNumber--;
							if(pageNumber<1)
							{
								pageNumber = 1;
							}
							$ajax_target_object.attr("data-paged", pageNumber);
							
							postAjaxResults($thisform, form_id);
						}
						
						return false;
					});
				}
				
				
				
			
			}).fail(function()
			{
				
			}).always(function()
			{
				$ajax_target_object.stop(true,true).animate({ opacity: 1}, "fast"); //finished loading				
				$thisform.trigger("sf:ajaxfinish", [ "Custom", "Event" ]);
			});
		}
		
		
		function initSearchForms()
		{
			var $search_forms = $('.searchandfilter');
			
			if($search_forms.length>0)
			{//loop through each page form, and see if they have pagination
				
				$search_forms.each(function(){
					
					//submit without submit button
					
					var $thisform = $(this);
					var use_shortcode = $thisform.attr("data-ajax-shortcode");
					
					if(use_shortcode==1)
					{
						$(this).find('input').keypress(function(e) {
							// Enter pressed?
							if(e.which == 10 || e.which == 13) {
								$thisform.submit();
								$ajax_target_object.attr("data-paged", 1);
							}
						});
						
						var template_is_loaded = $thisform.attr("data-template-loaded");
						var use_ajax = $thisform.attr("data-ajax");
						var auto_update = $thisform.attr("data-auto-update");
						var auto_count = $thisform.attr("data-auto-count");
						
						var $ajax_target_object = jQuery($thisform.attr("data-ajax-target"));
						$ajax_target_object.attr("data-paged", 1);
						
						var form_id = $thisform.attr("data-sf-form-id");
						
						//init combo boxes
						var $chosen = $thisform.find("select[data-combobox=1]");
						
						if($chosen.length>0)
						{
							
							$chosen.chosen();
						}
						
						//$($thisform).on('input', 'input.datepicker', dateInputType);
						
						//if(template_is_loaded==1)
						//{//if a template is loaded then use ajax
							
							if(use_ajax==1)
							{
							
								postAjaxResults($thisform,form_id); //load initial results
								
								if(auto_update==1)
								{
									
										/*$($thisform).on('change', 'input[type=radio], input[type=checkbox], select', function(e)
										{
											inputUpdate(200);
										});
										$($thisform).on('change', '.meta-slider', function(e)
										{
											inputUpdate(200);
										});
										$($thisform).on('input', 'input[type=number]', function(e)
										{
											inputUpdate(800);
										});
										
										$($thisform).on('input', 'input[type=text]:not(.datepicker)', function()
										{
											inputUpdate(1200);									
										});
										$($thisform).on('input', 'input.datepicker', dateInputType);*/
									
								}
								
								
								var use_history_api = $thisform.attr("data-use-history-api");
								var ajax_target_attr = $thisform.attr("data-ajax-target");
								
								$thisform.trigger("sf:init", [ "Custom", "Event" ]);
								//udpate
								return false;
							}
						//}
						
						$thisform.trigger("sf:init", [ "Custom", "Event" ]);
					}
				});
				
			}
			
		}
		
		
		function getURLParameter(name) {
			return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
		}
		
		
	});
	
})(window.jQuery);
