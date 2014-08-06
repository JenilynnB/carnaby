(function ( $ ) {
	"use strict";

	$(function () {
		/* metabox processing :) */
		
		//thickbox mods
		jQuery.fn.SfPopup = function(options)
		{
			var defaults = {
				startOpened: false
			};
			var opts = jQuery.extend(defaults, options);
			
			//loop through each item matched
			$(this).each(function()
			{
				var item = $(this);
				
				var $widget_ref = $(this).closest('.widget');
				var $meta_options_list = $widget_ref.find('.meta_options_list');
				
				
				function getMetaKeyValues(meta_key, $target)
				{
					
					var meta_prefs_action_name = "get_meta_values";
					
					$.post( ajaxurl, {meta_key: meta_key, action: meta_prefs_action_name}).done(function(data)
					{//don't do anything
						
						if(data)
						{
							$target.html(data);
						}
					});
					
				}
				
				function getMetaKey($this)
				{
					/* ***************** */
					var $the_field = $this.closest(".widget");
					var $meta_key_manual_toggle  = $the_field.find(".meta_key_manual_toggle");
					var $meta_key_manual  = $the_field.find(".meta_key_manual");
					var $meta_key = $the_field.find(".meta_key");
					
					var meta_key_value = $meta_key.val();
					
					if($meta_key_manual_toggle.is(":checked"))
					{
						meta_key_value = $meta_key_manual.val();
					}
					
					if(meta_key_value!="")
					{
						return meta_key_value;
					}
					else 
					{
						return "";
					}
				}
				
				item.click(function()
				{
					//add overlay
					var $overlay = jQuery('<div/>', {
						id: 'foo',
						'class': 'sf-thickbox-overlay',
					}).appendTo('body');
					
					//add popup div
					var $popup = jQuery('<div/>', {
						'class': 'sf-thickbox',
					}).appendTo('body');
					
					var popup_hd_str = "";
					popup_hd_str += '<div class="sf-thickbox-title">';
					popup_hd_str += '<div class=".sf-ajax-window-title"></div>';
					popup_hd_str += '<div class="sf-thickbox-title-inner">Add Options</div>';
					popup_hd_str += '<div class="sf-close-ajax-window">';
					popup_hd_str += '<a href="#" id="TB_closeWindowButton" name="TB_closeWindowButton"></a>';
					popup_hd_str += '<div class="sf-close-icon"></div>';
					popup_hd_str += '</div>';
					popup_hd_str += '</div>';
					
					/* init content */
					var meta_key = getMetaKey($(this));
					
					var popup_content_str = "";
					popup_content_str += '<div class="sf-ajax-content">';
					popup_content_str += '<p>Found the following values in use for meta key `<strong>'+meta_key+'</strong>`</p>';
					popup_content_str += '<p class="sf-thickbox-content">';
					popup_content_str += '<span class="spinner" style="display: block; float:left; text-align:left;"></span>';
					popup_content_str += '</p>';
					popup_content_str += '</div>';
					
					var popup_ft_str = "";
					popup_ft_str += '<div class="sf-thickbox-frame-toolbar">';
					popup_ft_str += '<div class="sf-thickbox-toolbar">';
					popup_ft_str += '<p><a href="#" class="button-secondary sf-meta-select-none">Select None</a> <a href="#" class="button-secondary sf-meta-select-all">Select All</a> <a href="#" class="button-primary sf-thickbox-action-right sf-meta-add-options">Add Options</a> <label class="replace-meta-options-label">Replace current options? &nbsp;<input type="checkbox" class="replace-options-checkbox" /></label></p>';
					popup_ft_str += '</div>';
					popup_ft_str += '</div>';
					
					var $popup_header = $(popup_hd_str);
					var $popup_content = $(popup_content_str);
					var $popup_footer = $(popup_ft_str);
					
					$popup.append($popup_header);
					$popup.append($popup_content);
					$popup.append($popup_footer);
					
					var $close_button = $popup_header.find(".sf-close-ajax-window");
					$close_button.click(function()
					{
						$('.sf-thickbox-overlay').remove();
						$popup.remove();
						
					});
					
					var $select_none_button = $popup_footer.find(".sf-meta-select-none");
					$select_none_button.click(function()
					{
						$popup_content.find(".sf-thickbox-content input[type=checkbox]").each(function(){
						
							this.checked = false;							
						});
						
						return false;
						
					});
					
					var $select_all_button = $popup_footer.find(".sf-meta-select-all");
					$select_all_button.click(function()
					{
						$popup_content.find(".sf-thickbox-content input[type=checkbox]").each(function(){
						
							this.checked = true;							
						});
						
						return false;
						
					});
					
					var $add_options_button = $popup_footer.find(".sf-meta-add-options");
					$add_options_button.click(function()
					{
						var $no_sort_label = $widget_ref.find(".no_sort_label");
						var $replace_options_checkbox = $popup_footer.find('.replace-options-checkbox');
						var $checked_options = $popup_content.find(".sf-thickbox-content input[type=checkbox]:checked");
						
						if($checked_options.length>0)
						{
							if($replace_options_checkbox.prop('checked'))
							{
								var $meta_options = $meta_options_list.find("li:not(.meta-option-template)");
								
								$meta_options.each(function(){
									
									var $meta_option = $(this);
									
									$meta_option.slideUp("fast",function(){
										$meta_option.remove();
										
										if($meta_options_list.find("li:not(.meta-option-template)").length==0)
										{
											$no_sort_label.show();
										}
									});
								});
							}
							
							
							$checked_options.each(function(){
								
								var optionsId = $meta_options_list.find("li:not(.meta-option-template)").length;
								
								var $meta_option = $meta_options_list.find("li.meta-option-template").clone();
								$meta_option.removeClass("meta-option-template");
								
								setOptionVars($meta_option, $widget_ref.attr('data-widget-id'), optionsId);
								$meta_options_list.append($meta_option);
								$meta_option.slideDown("fast");
								
								$meta_option.find("input[type=text]").val($(this).val());
								
								initMetaOptionControls($meta_option, $meta_options_list, $no_sort_label);
								
								$no_sort_label.hide();
								
								
								this.checked = true;					
							});
						}
						
						//hide popup
						$('.sf-thickbox-overlay').remove();
						$popup.remove();
						
						return false;
						
					});
					
					getMetaKeyValues(meta_key, $popup_content.find(".sf-thickbox-content"));
					
					
					return false;
					
					
				});
				
			});
		}
		
		String.prototype.parseArray = function (arr) {
			return this.replace(/\{([0-9]+)\}/g, function (_, index) { return arr[index]; });
		};
		
		String.prototype.parse = function (a, b, c) {
			return this.parseArray([a, b, c]);
		};
		$.fn.hasAttr = function(name) {  
		   return this.attr(name) !== undefined;
		};
		
		
		$("#search-filter-search-form").addClass("widgets-search-filter-sortables").addClass("ui-search-filter-sortable");
		
		//create all default handlers, open/close/delete/save
		jQuery.fn.makeWidget = function(options)
		{
			var defaults = {
				startOpened: false
			};
			var opts = jQuery.extend(defaults, options);
			
			//loop through each item matched
			this.each(function()
			{
				var item = $(this);
				item.addClass("widget-enabled");
				var field_type = item.attr("data-field-type");
				
				var itemInside = item.find('.widget-inside');
				
				if(opts.startOpened==true)
				{
					itemInside.show();
				}
				var container = item.parent();
			
				item.find('.widget-top').click(function(e){
					
					e.stopPropagation();
					e.preventDefault();
					
					var allowExpand = container.attr("data-allow-expand");
					if(typeof(allowExpand)=="undefined")
					{//init data-dragging if its not on already
						container.attr("data-allow-expand", "1");
						allowExpand = "1";
					}
					
					if(allowExpand==1)
					{					
						var dataDragging = item.attr("data-dragging");
						if(typeof(dataDragging)=="undefined")
						{//init data-dragging if its not on already
							item.attr("data-dragging", "0");
							dataDragging = "0";
						}
						
						if(dataDragging=="0") 
						{
							itemInside.slideToggle("fast");
						}
					}
					
					return false;
				});
				
				item.find('.widget-control-remove').click(function(e){				
					
					item.slideUp("fast", function(){
						
						item.remove();
						if(item.length==1)
						{
							//$emptyPlaceholder.show();
						}
						
					});
					
					return false;
				});
				
				item.find('.widget-control-advanced').click(function(e){
				
					$(this).parent().find(".advanced-settings").slideToggle("fast");
					$(this).toggleClass("active");
					
					return false;
				});
				
				item.find('.widget-control-close').click(function(e){
				
					itemInside.slideUp("fast");
					
					return false;
				
				});
				
				//widget specific JS
				//categories
				if((field_type=="category")||(field_type=="tag")||(field_type=="taxonomy")||(field_type=="post_type"))
				{
					
					var $input_type_field = item.find('.sf_input_type select');
					
					// start off by showing/hiding correct fields
					showHideFields($input_type_field.val());
					
					//grab the input type
					$input_type_field.change(function(e)
					{
						var input_type = $(this).val();
						showHideFields(input_type);
						
					});
					
					var $combobox = item.find(".sf_make_combobox input");
					
					$combobox.change(function()
					{
						var tinput_type = $(this).parent().parent().find('.sf_input_type select').val();
						
						if(tinput_type=="multiselect")
						{
							if($(this).prop("checked"))
							{
								item.find(".sf_all_items_label").show();
							}
							else
							{
								item.find(".sf_all_items_label").hide();
							}
						}
					});
					
				}
				
				if(field_type=="author")
				{
					
					var $input_type_field = item.find('.sf_input_type select');
					
					// start off by showing/hiding correct fields
					showHideFieldsAuthor($input_type_field.val());
					
					var $combobox = item.find(".sf_make_combobox input");
					
					$combobox.change(function()
					{
						var tinput_type = $(this).parent().parent().find('.sf_input_type select').val();
						
						if(tinput_type=="multiselect")
						{
							if($(this).prop("checked"))
							{
								item.find(".sf_all_items_label").show();
							}
							else
							{
								item.find(".sf_all_items_label").hide();
							}
						}
					});
					
					
					//grab the input type
					$input_type_field.change(function(e)
					{
						var input_type = $(this).val();
						showHideFieldsAuthor(input_type);						
					});
				}
				
				if(field_type=="taxonomy")
				{
					var $taxonomy_select = item.find('.sf_taxonomy_name select');
					
					$taxonomy_select.on("change",function()
					{
						var current_tax_name = $taxonomy_select.find("option[value='"+$taxonomy_select.val()+"']").html();
						item.find('.in-widget-title').html(": "+current_tax_name);
					
					});
					
					var current_tax_name = $taxonomy_select.find("option[value='"+$taxonomy_select.val()+"']").html();
					item.find('.in-widget-title').html(": "+current_tax_name);
					
				}
				
				if(field_type=="sort_order")
				{
					setPostMetaManualFields();
					
					var $meta_key_manual_toggle = item.find('.meta_key_manual_toggle');
					
					var $input_type_field = item.find('.sf_input_type select');
					
					// start off by showing/hiding correct fields
					showHideFields($input_type_field.val());
					
					$input_type_field.change(function(e)
					{
						var input_type = $(this).val();
						showHideFields(input_type);
					});
					
					
					//grab the input type
					$meta_key_manual_toggle.change(function(e)
					{
						setPostMetaManualFields();
					});
				}
				
				if(field_type=="post_meta")
				{
					setPostMetaManualFields();
					
					var $meta_key_manual_toggle = item.find('.meta_key_manual_toggle');
					
					var $input_type_field = item.find('.sf_input_type select');
					
					// start off by showing/hiding correct fields
					showHideFields($input_type_field.val());
					
					$input_type_field.change(function(e)
					{
						var input_type = $(this).val();
						showHideFields(input_type);
					});
					
					
					var $combobox = item.find(".sf_make_combobox input");
					
					$combobox.change(function()
					{
						var tinput_type = $(this).parent().parent().find('.sf_input_type select').val();
						
						if(tinput_type=="multiselect")
						{
							if($(this).prop("checked"))
							{
								item.find(".sf_all_items_label").show();
							}
							else
							{
								item.find(".sf_all_items_label").hide();
							}
						}
					});
					
					//grab the input type
					$meta_key_manual_toggle.change(function(e)
					{
						setPostMetaManualFields();
					});
				}
				
				if(field_type=="sort_order")
				{
					var $add_sort_button = item.find(".add-sort-button");
					var $clear_option_button = item.find(".clear-option-button");
					var $sort_options_list = item.find(".sort_options_list");
					var $no_sort_label = item.find(".no_sort_label");
					
					var $current_sort_options = $sort_options_list.find("li:not(.sort-option-template)");
					$current_sort_options.show();
					
					if($current_sort_options.length>0)
					{

						 $no_sort_label.hide();
					}
					
					$current_sort_options.each(function(){
						
						initSortOptionControls($(this), $sort_options_list, $no_sort_label);
						
					});					
					
					$sort_options_list.sortable({
						opacity: 0.6,
						revert: 200,
						cursor: 'move',
						handle: '.slimmove',
						//cancel: '.widget-title-action,h3,.sidebar-description',
						items: 'li:not(.sort-option-template)',
						placeholder: 'sort-option-placeholder',
						'start': function (event, ui) {
							ui.placeholder.show();
						},
						stop: function(e,ui) {
							
							var optionsCount = 0;
							var $sort_options_list = ui.item.find(".sort_options_list");
							var widgetCount = ui.item.attr("data-widget-id");
							
							$sort_options_list.find("li:not(.sort-option-template)").each(function()
							{
								
								setOptionVars($(this), widgetCount, optionsCount);
								optionsCount++;
							
							});
						}
					});
					
					$clear_option_button.click(function(){
					
						
						var $sort_options = $sort_options_list.find("li:not(.sort-option-template)");
								
						$sort_options.each(function(){
							
							var $sort_option = $(this);
							
							$sort_option.slideUp("fast",function(){
								$sort_option.remove();
								
								if($sort_options_list.find("li:not(.sort-option-template)").length==0)
								{
									$no_sort_label.show();
								}
							});
						});
						
						return false;
					
					});
					
					$add_sort_button.click(function(){
					
						
						var $meta_key_manual_toggle  = item.find(".meta_key_manual_toggle");
						var $meta_key_manual  = item.find(".meta_key_manual");
						var $meta_key = item.find(".meta_key");
						
						var meta_key_value = $meta_key.val();
						
						if($meta_key_manual_toggle.is(":checked"))
						{
							meta_key_value = $meta_key_manual.val();
						}
						
						if(meta_key_value!="")
						{
							//reset meta fields
							$meta_key.removeAttr("selected");
							$meta_key[0].selectedIndex = 0;
							$meta_key_manual.val("");
							$meta_key_manual_toggle.prop("checked", false);
							$meta_key.removeAttr("disabled");
							$meta_key_manual.attr("disabled", "disabled");
							
							
							var optionsId = $sort_options_list.find("li:not(.sort-option-template)").length;
							
							var option_html = "";
							
							var $sort_option = $sort_options_list.find("li.sort-option-template").clone();
							$sort_option.removeClass("sort-option-template");
							$sort_option.hide();
							$sort_option.find(".meta_key_val, .meta_disabled, .name").val(meta_key_value);
							
							setOptionVars($sort_option, item.attr('data-widget-id'), optionsId);
							
							var $sort_by_option = $sort_option.find(".sort_by_option");
							
							if($sort_by_option.val()=="meta_value")
							{
								$sort_option.find('.sort-options-advanced').show();
							}
							else
							{
								$sort_option.find('.sort-options-advanced').hide();
							}
							
							$sort_options_list.append($sort_option);
							$sort_option.slideDown("fast");
							
							initSortOptionControls($sort_option, $sort_options_list, $no_sort_label);
							
							$no_sort_label.hide();
							
						}
						return false;
					
					});
				}
				
				if(field_type=="post_meta")
				{
					/* init meta type radios */
					/* set up meta type radios */
					var $meta_type_radio = item.find('.sf_meta_type input[type=radio]');
					var $meta_type_labels = item.find('.sf_meta_type label');
					var $checked_radio = item.find(".sf_meta_type input[data-radio-checked='1']");
					
					$meta_type_radio.each(function(){
						this.checked = false;
						$(this).attr("data-radio-checked", 0);
					});
					
					
					if($checked_radio.length==0)
					{
						$checked_radio = item.find(".sf_meta_type label:first-child input");
						
					}
					
					$checked_radio.attr("data-radio-checked", 1);
					$checked_radio.prop('checked',true);
					var meta_type_val = $checked_radio.val();
					//alert("checked val: "+meta_type_val);
					
					metaTypeChange($checked_radio);
					
					$meta_type_radio.change(function()
					{
						$meta_type_radio.attr("data-radio-checked", 0);
						$(this).attr("data-radio-checked", 1);
						metaTypeChange($(this));
						
					});
					
					
					/* ****************************************************** */
					
					
					var $add_option_button = item.find(".add-option-button");
					var $detect_option_button = item.find(".detect-option-button");
					var $clear_option_button = item.find(".clear-option-button");
					var $meta_options_list = item.find(".meta_options_list");
					var $no_sort_label = item.find(".no_sort_label");
					
					var $current_meta_options = $meta_options_list.find("li:not(.meta-option-template)");
					
					$meta_options_list.sortable({
						opacity: 0.6,
						revert: 200,
						cursor: 'move',
						handle: '.slimmove',
						//cancel: '.widget-title-action,h3,.sidebar-description',
						items: 'li:not(.meta-option-template)',
						placeholder: 'meta-option-placeholder',
						'start': function (event, ui) {
							ui.placeholder.show();
						},
						stop: function(e,ui) {
							
							var optionsCount = 0;
							var $meta_options_list = ui.item.find(".meta_options_list");
							var widgetCount = ui.item.attr("data-widget-id");
							
							$meta_options_list.find("li:not(.meta-option-template)").each(function()
							{
								
								setOptionVars($(this), widgetCount, optionsCount);
								optionsCount++;
							
							});
							
						}
					});
					
					$current_meta_options.show();
					
					if($current_meta_options.length>0)
					{
						 $no_sort_label.hide();
					}
					
					$current_meta_options.each(function(){
						
						initMetaOptionControls($(this), $meta_options_list, $no_sort_label);
						
					});
					
					$add_option_button.click(function(){
					
						
						var $meta_key_manual_toggle  = item.find(".meta_key_manual_toggle");
						
						var optionsId = $meta_options_list.find("li:not(.meta-option-template)").length;
						
						var option_html = "";
						
						var $meta_option = $meta_options_list.find("li.meta-option-template").clone();
						$meta_option.removeClass("meta-option-template");
						
						//$meta_option.find(".meta_key_val, .meta_disabled, .name").val(meta_key_value);
						
						setOptionVars($meta_option, item.attr('data-widget-id'), optionsId);
						$meta_options_list.append($meta_option);
						$meta_option.slideDown("fast");
						
						initMetaOptionControls($meta_option, $meta_options_list, $no_sort_label);
						
						$no_sort_label.hide();
						
						
						return false;
					
					});
					
					
					$detect_option_button.SfPopup();
					
					$clear_option_button.click(function(){
					
						
						var $meta_options = $meta_options_list.find("li:not(.meta-option-template)");
								
						$meta_options.each(function(){
							
							var $meta_option = $(this);
							
							$meta_option.slideUp("fast",function(){
								$meta_option.remove();
								
								if($meta_options_list.find("li:not(.meta-option-template)").length==0)
								{
									$no_sort_label.show();
								}
							});
						});
						
						return false;
					
					});
				}
				
				function initSortOptionControls($sort_option, $sort_options_list, $no_sort_label)
				{
					var $sort_by_option = $sort_option.find(".sort_by_option");
					
					if($sort_by_option.val()=="meta_value")
					{
						$sort_option.find('.sort-options-advanced').show();
					}
					else
					{
						$sort_option.find('.sort-options-advanced').hide();
					}
					
					$sort_by_option.change(function()
					{
						if($(this).val()=="meta_value")
						{
							$sort_option.find('.sort-options-advanced').slideDown("fast");
						}
						else
						{
							$sort_option.find('.sort-options-advanced').slideUp("fast");
						}
					});
					
					$sort_option.find(".widget-control-option-remove").click(function(){
								
						$sort_option.slideUp("fast",function(){
							$sort_option.remove();
							
							if($sort_options_list.find("li:not(.sort-option-template)").length==0)
							{
								$no_sort_label.show();
							}
						});
						
						return false;
						
					});
					
					/*$advanced_button.click(function(){
						
						$(this).toggleClass("active");
						$sort_option.find('.sort-options-advanced').slideToggle("fast");
						return false;
						
					});*/
					
				}
				
				
				
				function setPostMetaManualFields()
				{
					var $meta_key_manual = item.find(".meta_key_manual");
					var $meta_key_manual_hidden = item.find(".meta_key_manual_hidden");
					var $meta_key = item.find(".meta_key");
					var $meta_key_hidden = item.find(".meta_key_hidden");
					var $meta_key_manual_toggle = item.find(".meta_key_manual_toggle");
					
					if($meta_key_manual_toggle.is(":checked"))
					{
						$meta_key_manual.removeAttr("disabled");
						$meta_key_manual_hidden.attr("disabled", "disabled");
						
						$meta_key_hidden.removeAttr("disabled");
						$meta_key_hidden.val($meta_key.val());
						$meta_key.attr("disabled", "disabled");
						
						$meta_key_manual.focus();
					}
					else
					{
						$meta_key_manual_hidden.removeAttr("disabled");
						$meta_key_manual_hidden.val($meta_key_manual.val());
						$meta_key_manual.attr("disabled", "disabled");
						
						$meta_key.removeAttr("disabled");
						$meta_key_hidden.attr("disabled", "disabled");
					}

				}
				
				function showHideFields(input_type)
				{					
					if(input_type=="select")
					{
						item.find(".sf_operator").hide();
						item.find(".sf_drill_down").show();
						item.find(".sf_all_items_label").show();
						item.find(".sf_make_combobox").show();
					}
					else if(input_type=="checkbox")
					{
						item.find(".sf_operator").show();
						item.find(".sf_drill_down").hide();
						item.find(".sf_all_items_label").hide();
						item.find(".sf_make_combobox").hide();
					}
					else if(input_type=="radio")
					{
						item.find(".sf_operator").hide();
						item.find(".sf_drill_down").hide();
						item.find(".sf_make_combobox").hide();
						item.find(".sf_all_items_label").show();
					}
					else if(input_type=="multiselect")
					{
						item.find(".sf_operator").show();
						item.find(".sf_drill_down").hide();
						item.find(".sf_all_items_label").hide();
						item.find(".sf_make_combobox").show();
						
						if(item.find(".sf_make_combobox input").prop("checked"))
						{
							item.find(".sf_all_items_label").show();
						}
					}
					else if(input_type=="range-slider")
					{

					}
					else if(input_type=="range-number")
					{

					}
					else if(input_type=="range-radio")
					{

					}
					else if(input_type=="range-checkbox")
					{

					}
					
				}
				function showHideFieldsAuthor(input_type)
				{					
					if(input_type=="select")
					{
						//item.find(".sf_operator").hide();
						item.find(".sf_all_items_label").show();
						item.find(".sf_make_combobox").show();
					}
					else if(input_type=="checkbox")
					{
						//item.find(".sf_operator").show();
						item.find(".sf_all_items_label").hide();
						item.find(".sf_make_combobox").hide();
					}
					else if(input_type=="radio")
					{
						//item.find(".sf_operator").hide();
						item.find(".sf_all_items_label").show();
						item.find(".sf_make_combobox").hide();
					}
					else if(input_type=="multiselect")
					{
						//item.find(".sf_operator").show();
						item.find(".sf_all_items_label").hide();
						item.find(".sf_make_combobox").show();
						
						if(item.find(".sf_make_combobox input").prop("checked"))
						{
							item.find(".sf_all_items_label").show();
						}
					}
					
				}
			})
			
			return this;
		};
		
		jQuery.fn.makeSortables = function(options)
		{
			/*
			//initialise options
			var opts = jQuery.extend(defaults, options);
			*/
			
			setWidgetFormIds();
			
			//loop through each item matched
			this.each(function()
			{
				
				var container = $(this);
				var allowExpand = true;
				
				var allowExpand = container.attr("data-allow-expand");
				if(typeof(allowExpand)=="undefined")
				{//init data-dragging if its not on already
					container.attr("data-allow-expand", "1");
					allowExpand = "1";
				}
				//var $emptyPlaceholder = $(this).find("#empty-placeholder");
				var received = false;
				
				container.sortable({
					opacity: 0.6,
					revert: container.hasClass("closed") ? false : 200,
					cursor: 'move',
					handle: '.widget-top',
					cancel: '.widget-title-action,h3,.sidebar-description',
					items: '.inside #search-form > .widget:not(.sidebar-name,.sidebar-disabled)',
					placeholder: 'widget-placeholder',
					connectWith: '.ui-search-filter-sortable',
					stop: function(e,ui){
						
						setWidgetFormIds();
						
						ui.item.attr("data-dragging", "0");
						var field_type = ui.item.attr("data-field-type");
						
						//check to see if the context (the source item) has the class "widget enabled", if it doesn't then it was from the available fields list, if it doesn't then we were moving the item already in the Search Form
						if(!$(ui.item.context).hasClass("widget-enabled"))
						{
							//if we are moving the item from teh Available fields, automatically slide open
							ui.item.find('.widget-inside').slideDown("fast");
						}
						
						
						if(field_type=="post_meta")
						{
							/* set up meta type radios */
							var $meta_type_radio = ui.item.find('.sf_meta_type input[type=radio]');
							var $meta_type_labels = ui.item.find('.sf_meta_type label');
							var $checked_radio = ui.item.find(".sf_meta_type input[data-radio-checked='1']");
							
							$meta_type_radio.each(function(){
								this.checked = false;
								$(this).attr("data-radio-checked", 0);
							});
							
							
							if($checked_radio.length==0)
							{
								$checked_radio = ui.item.find(".sf_meta_type label:first-child input");
								$checked_radio.prop('checked',true);
							}
							
							$checked_radio.attr("data-radio-checked", 1);
							$checked_radio.prop('checked',true);
							var meta_type_val = $checked_radio.val();
							
							metaTypeChange($checked_radio);
							
							$meta_type_radio.change(function()
							{
								$meta_type_radio.attr("data-radio-checked", 0);
								$(this).attr("data-radio-checked", 1);
								metaTypeChange($(this));
								
							});
							
							
						}
						
						
						
						var $date_format_hidden = ui.item.find('.date_format_hidden');
						if($date_format_hidden.length==1)
						{
							var selected_radio = $date_format_hidden.val();
							//find any radios
							var $date_radio_inputs = ui.item.find("input.date_format_radio");
							if($date_radio_inputs.length>0)
							{
								//make sure default is selected
								if($($date_radio_inputs.get(selected_radio)).length>0)
								{
									$($date_radio_inputs.get(selected_radio)).prop('checked', true);
								}
							}
						}
												
						
					},
					over: function(){	
						
						//$emptyPlaceholder.hide();
						
					},
					out: function(){
						if(!received)
						{
							//$emptyPlaceholder.show();
						}
					},
					start: function(e,ui){
						ui.item.attr("data-dragging", "1");
						ui.item.find('.widget-inside').stop(true,true).hide();
						//if(!ui.placeholder.parent().hasClass("inside"))
						//{//if it is getting appended to the wrong place, then force it in to the right container :)
							ui.placeholder.appendTo(ui.placeholder.parent().find(".inside #search-form"));
						//alert("start");
					},
					receive: function(ev, ui)
					{
						received = true;
						//$emptyPlaceholder.hide();
						//alert("receive");
					},
					change: function(e,ui){
						//alert("change");
					}
					
				});
				
				container.click(function()
				{//prevent animation when the container is closed - no need to animate the helper to an invisible DIV.....
					if(container.hasClass("closed"))
					{
						
						container.sortable( "option", "revert", false );
					}
					else
					{
						container.sortable( "option", "revert", 200 );
					}
				});
				
					
				var items = container.find(".widget");
				items.makeWidget();
				
			});
			
			return this;
		};
		
		$("#search-filter-search-form").makeSortables();
		
		
		$(".widgets-search-filter-draggables .widget").draggable({
            connectToSortable: ".ui-search-filter-sortable",
			helper: 'clone',
			start: startDrag,
			stop: enableNewWidgets
        });
		
		function startDrag(event, ui)
		{
			//@TODO: add and remove hover effect class
			$("#search-filter-search-form").addClass("post-box-hover");
		}
		
		
		function enableNewWidgets(event, ui)
		{
			//@TODO: add and remove hover effect class
			$("#search-filter-search-form").removeClass("post-box-hover");
			
			var $droppedWidget = $('.widgets-search-filter-sortables .widget:not(.widget-enabled)');
			$droppedWidget.makeWidget();
			

		}
		
		function setWidgetFormIds()
		{
			var widgetCount = 0;
			
			var $active_widgets = $("#search-filter-search-form").find(".widget");
			
			/*var $widgets_radio = $active_widgets.find("input[type=radio]");
			$widgets_radio.each(function(){
			
				$(this).attr("data-radio-val", $(this).prop("checked"));
				
			});*/
			
			$active_widgets.each(function()
			{
				
				
				setFormVars($(this), widgetCount);
				
				//if type is sort_order then loop through the option
				if($(this).attr("data-field-type")=="sort_order")
				{
					
					var optionsCount = 0;
					var $sort_options_list = $(this).find(".sort_options_list");
					$sort_options_list.find("li:not(.sort-option-template)").each(function()
					{
						
						setOptionVars($(this), widgetCount, optionsCount);
						optionsCount++;
					
					});
				}
				
				if($(this).attr("data-field-type")=="post_meta")
				{
					
					var optionsCount = 0;
					var $meta_options_list = $(this).find(".meta_options_list");
					$meta_options_list.find("li:not(.meta-option-template)").each(function()
					{
						
						setOptionVars($(this), widgetCount, optionsCount);
						optionsCount++;
					
					});
				}

				widgetCount++;
			});
			
			var $widgets_radio = $active_widgets.find("input[type=radio]");
			
			$widgets_radio.each(function()
			{
				if($(this).attr("data-radio-checked")==1)
				{
					$(this).prop("checked", true);
					
					
					
				}
				
			});
			
		}
	
		function setFormVars($droppedWidget, widgetId)
		{
			$droppedWidget.attr("data-widget-id", widgetId);
			var $inputFields = $droppedWidget.find("input, select").not(".ignore-template-init input, .ignore-template-init select");
			var $inputLabels = $droppedWidget.find("label").not(".ignore-template-init label");
			
			$inputFields.each(function()
			{
				//copy structure
				if(!$(this).hasAttr("data-field-template-id"))
				{
					$(this).attr("data-field-template-id", $(this).attr("id"))
				}
				
				if(!$(this).hasAttr("data-field-template-name"))
				{
					$(this).attr("data-field-template-name", $(this).attr("name"))
				}
				
				//rename
				$(this).attr("id",$(this).attr("data-field-template-id").parse("widget-field", widgetId));
				$(this).attr("name",$(this).attr("data-field-template-name").parse("widget-field", widgetId));
				
			});
			
			$inputLabels.each(function()
			{
				//copy structure
				if(!$(this).hasAttr("data-field-template-for"))
				{
					$(this).attr("data-field-template-for", $(this).attr("for"))
				}
				
				$(this).attr("for",$(this).attr("data-field-template-for").parse("widget-field", widgetId));
				
			});
		}
		function setOptionVars($sortOption, widgetId, optionId)
		{
			var $inputFields = $sortOption.find("input, select");
			var $inputLabels = $sortOption.find("label");
			
			$inputFields.each(function()
			{
				//copy structure
				if(!$(this).hasAttr("data-field-template-id"))
				{
					$(this).attr("data-field-template-id", $(this).attr("id"))
				}
				
				if(!$(this).hasAttr("data-field-template-name"))
				{
					$(this).attr("data-field-template-name", $(this).attr("name"))
				}
				
				//rename
				$(this).attr("id",$(this).attr("data-field-template-id").parse("widget-field", widgetId, optionId));
				$(this).attr("name",$(this).attr("data-field-template-name").parse("widget-field", widgetId, optionId));
				
			});
			
			$inputLabels.each(function()
			{
				//copy structure
				if(!$(this).hasAttr("data-field-template-for"))
				{
					$(this).attr("data-field-template-for", $(this).attr("for"))
				}
				
				$(this).attr("for",$(this).attr("data-field-template-for").parse("widget-field", widgetId, optionId));
				
			});
		}
		
		function initMetaOptionControls($meta_option, $meta_options_list, $no_sort_label)
		{
			$meta_option.find(".widget-control-option-remove").click(function(){
						
				$meta_option.slideUp("fast",function(){
					$meta_option.remove();
					
					if($meta_options_list.find("li:not(.meta-option-template)").length==0)
					{
						$no_sort_label.show();
					}
				});
				
				return false;
				
			});
			
			$meta_option.find(".widget-control-option-advanced").click(function(){
				
				$(this).toggleClass("active");
				$meta_option.find('.meta-options-advanced').slideToggle("fast");
				return false;
				
			});
		}
		
		function metaTypeChange($radio_field)
		{
			
			var $meta_type_labels = $radio_field.parent().parent().find("label");
			var item = $radio_field.closest(".widget");
			
			$meta_type_labels.removeClass("active");
			var $meta_type_label = $radio_field.closest("label");
			$meta_type_label.addClass("active");
			
			var radio_val = $radio_field.val();
			
			item.find(".sf_input_type_meta input[data-radio-checked='1']").prop('checked',true);
			
			item.find(".sf_input_type_meta").hide();
			item.find(".sf_field_data").hide();
			
			item.find(".sf_input_type_meta.sf_"+radio_val).show();
			item.find(".sf_field_data.sf_"+radio_val).show();
		}
	
		function initSetupField()
		{
			var $use_template_manual_toggle = $('.setup .use_template_manual_toggle');
			var $use_ajax_toggle = $('.setup .use_ajax_toggle');
			var $sf_ajax_container = $('.setup .sf_ajax');
			
			//grab the input type
			$use_template_manual_toggle.change(function(e)
			{
				handleSetupTemplateToggle($(this), $sf_ajax_container, $use_ajax_toggle);
			});
			
			handleSetupTemplateToggle($use_template_manual_toggle, $sf_ajax_container, $use_ajax_toggle);
			
			//grab the input type
			$use_ajax_toggle.change(function(e)
			{
				handleSetupAjaxToggle($(this));
			});
			
			handleSetupAjaxToggle($use_ajax_toggle);
			
		}
		
		function handleSetupAjaxToggle($this)
		{
			var $ajax_target = $('.setup .ajax_target');
			var $ajax_target_hidden = $('.setup .ajax_target_hidden');
			
			var $ajax_links_selector = $('.setup .ajax_links_selector');
			var $ajax_links_selector_hidden = $('.setup .ajax_links_selector_hidden');
			
			var $ajax_auto_submit = $('.setup .auto_submit');
			var $ajax_auto_submit_hidden = $('.setup .auto_submit_hidden');
			
			var $selectors_div = $('.setup .ajax-selectors');
			
			if($this.is(":checked"))
			{
				$ajax_target.removeAttr("disabled");
				$ajax_target_hidden.attr("disabled","disabled");
				
				$ajax_links_selector.removeAttr("disabled");
				$ajax_links_selector_hidden.attr("disabled","disabled");
				
				$ajax_auto_submit.removeAttr("disabled");
				$ajax_auto_submit_hidden.attr("disabled","disabled");
				
				$selectors_div.css("opacity", 1);
				//$selectors_div.slideDown("fast")
				
			}
			else
			{
				
				$ajax_target.attr("disabled","disabled");
				$ajax_target_hidden.removeAttr("disabled");
				$ajax_target_hidden.val($ajax_target.val());
				
				$ajax_links_selector.attr("disabled","disabled");
				$ajax_links_selector_hidden.removeAttr("disabled");
				$ajax_links_selector_hidden.val($ajax_links_selector.val());
				
				$ajax_auto_submit.attr("disabled","disabled");
				$ajax_auto_submit_hidden.removeAttr("disabled");
				
				$selectors_div.css("opacity", 0.5);
				
			}
			
		}
		
		function handleSetupTemplateToggle($this, $sf_ajax_container, $use_ajax_toggle)
		{
			var $template_name_manual = $('.setup .template_name_manual');
			var $template_name_manual_hidden = $('.setup .template_name_manual_hidden');
			
			var $page_slug = $('.setup .page_slug');
			var $page_slug_hidden = $('.setup .page_slug_hidden');
			
			var $template_div = $(".setup .custom_template");
			
			if($this.is(":checked"))
			{
				$template_name_manual.removeAttr("disabled");
				$template_name_manual_hidden.attr("disabled","disabled");
				
				$page_slug.removeAttr("disabled");
				$page_slug_hidden.attr("disabled","disabled");
				
				$template_div.css("opacity", 1);
				
			}
			else
			{
			
				$template_name_manual.attr("disabled","disabled");
				$template_name_manual_hidden.removeAttr("disabled");
				$template_name_manual_hidden.val($template_name_manual.val());
				
				$page_slug.attr("disabled","disabled");
				$page_slug_hidden.removeAttr("disabled");
				$page_slug_hidden.val($page_slug.val());
				
				$template_div.css("opacity", 0.5);
				
			}
			
		}
		
		initSetupField();
		
		//load tooltips
		$('[data-hint]').live('mouseover', function() {
			
			$(this).qtip({
				overwrite: false, // Make sure another tooltip can't overwrite this one without it being explicitly destroyed
				content: {
					attr: 'data-hint' // Tell qTip2 to look inside this attr for its content
				},
				style: { classes: 'sf-tootlip' },
				position: {
					my: 'bottom left',
					at: 'top center',
					viewport: $(window),
					adjust: {
						method: 'shift none'
					}
				},
				show: {
				ready: true // Needed to make it show on first mouseover event
				}
			});
		})
		
	});
	
	

}(jQuery));