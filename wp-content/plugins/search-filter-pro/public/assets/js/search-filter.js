/* noui slider */
(function(f){if(f.zepto&&!f.fn.removeData)throw new ReferenceError("Zepto is loaded without the data module.");f.fn.noUiSlider=function(C,D){function s(a,b){return 100*b/(a[1]-a[0])}function E(a,b){return b*(a[1]-a[0])/100+a[0]}function t(a){return a instanceof f||f.zepto&&f.zepto.isZ(a)}function n(a){return!isNaN(parseFloat(a))&&isFinite(a)}function r(a,b){f.isArray(a)||(a=[a]);f.each(a,function(){"function"===typeof this&&this.call(b)})}function F(a,b){return function(){var c=[null,null];c[b]=f(this).val();
a.val(c,!0)}}function G(a,b){a=a.toFixed(b.decimals);0===parseFloat(a)&&(a=a.replace("-0","0"));return a.replace(".",b.serialization.mark)}function u(a){return parseFloat(a.toFixed(7))}function p(a,b,c,d){var e=d.target;a=a.replace(/\s/g,h+" ")+h;b.on(a,function(a){var b=e.attr("disabled");if(e.hasClass("noUi-state-tap")||void 0!==b&&null!==b)return!1;var g;a.preventDefault();var b=0===a.type.indexOf("touch"),h=0===a.type.indexOf("mouse"),l=0===a.type.indexOf("pointer"),v,H=a;0===a.type.indexOf("MSPointer")&&
(l=!0);a.originalEvent&&(a=a.originalEvent);b&&(g=a.changedTouches[0].pageX,v=a.changedTouches[0].pageY);if(h||l)l||void 0!==window.pageXOffset||(window.pageXOffset=document.documentElement.scrollLeft,window.pageYOffset=document.documentElement.scrollTop),g=a.clientX+window.pageXOffset,v=a.clientY+window.pageYOffset;g=f.extend(H,{pointX:g,pointY:v,cursor:h});c(g,d,e.data("base").data("options"))})}function I(a){var b=this.target;if(void 0===a)return this.element.data("value");!0===a?a=this.element.data("value"):
this.element.data("value",a);void 0!==a&&f.each(this.elements,function(){if("function"===typeof this)this.call(b,a);else this[0][this[1]](a)})}function J(a,b,c){if(t(b)){var d=[],e=a.data("target");a.data("options").direction&&(c=c?0:1);b.each(function(){f(this).on("change"+h,F(e,c));d.push([f(this),"val"])});return d}"string"===typeof b&&(b=[f('<input type="hidden" name="'+b+'">').appendTo(a).addClass(g[3]).change(function(a){a.stopPropagation()}),"val"]);return[b]}function K(a,b,c){var d=[];f.each(c.to[b],
function(e){d=d.concat(J(a,c.to[b][e],b))});return{element:a,elements:d,target:a.data("target"),val:I}}function L(a,b){var c=a.data("target");c.hasClass(g[14])||(b||(c.addClass(g[15]),setTimeout(function(){c.removeClass(g[15])},450)),c.addClass(g[14]),r(a.data("options").h,c))}function w(a,b){var c=a.data("options");b=u(b);a.data("target").removeClass(g[14]);a.css(c.style,b+"%").data("pct",b);a.is(":first-child")&&a.toggleClass(g[13],50<b);c.direction&&(b=100-b);a.data("store").val(G(E(c.range,b),
c))}function x(a,b){var c=a.data("base"),d=c.data("options"),c=c.data("handles"),e=0,k=100;if(!n(b))return!1;if(d.step){var m=d.step;b=Math.round(b/m)*m}1<c.length&&(a[0]!==c[0][0]?e=u(c[0].data("pct")+d.margin):k=u(c[1].data("pct")-d.margin));b=Math.min(Math.max(b,e),0>k?100:k);if(b===a.data("pct"))return[e?e:!1,100===k?!1:k];w(a,b);return!0}function A(a,b,c,d){a.addClass(g[5]);setTimeout(function(){a.removeClass(g[5])},300);x(b,c);r(d,a.data("target"));a.data("target").change()}function M(a,b,c){var d=
b.a,e=a[b.d]-b.start[b.d],e=100*e/b.size;if(1===d.length){if(a=x(d[0],b.c[0]+e),!0!==a){0<=f.inArray(d[0].data("pct"),a)&&L(b.b,!c.margin);return}}else{var k,m;c.step&&(a=c.step,e=Math.round(e/a)*a);a=k=b.c[0]+e;e=m=b.c[1]+e;0>a?(e+=-1*a,a=0):100<e&&(a-=e-100,e=100);if(0>k&&!a&&!d[0].data("pct")||100===e&&100<m&&100===d[1].data("pct"))return;w(d[0],a);w(d[1],e)}r(c.slide,b.target)}function N(a,b,c){1===b.a.length&&b.a[0].data("grab").removeClass(g[4]);a.cursor&&y.css("cursor","").off(h);z.off(h);
b.target.removeClass(g[14]+" "+g[20]).change();r(c.set,b.target)}function B(a,b,c){1===b.a.length&&b.a[0].data("grab").addClass(g[4]);a.stopPropagation();p(q.move,z,M,{start:a,b:b.b,target:b.target,a:b.a,c:[b.a[0].data("pct"),b.a[b.a.length-1].data("pct")],d:c.orientation?"pointY":"pointX",size:c.orientation?b.b.height():b.b.width()});p(q.end,z,N,{target:b.target,a:b.a});a.cursor&&(y.css("cursor",f(a.target).css("cursor")),1<b.a.length&&b.target.addClass(g[20]),y.on("selectstart"+h,function(){return!1}))}
function O(a,b,c){b=b.b;var d,e;a.stopPropagation();c.orientation?(a=a.pointY,e=b.height()):(a=a.pointX,e=b.width());d=b.data("handles");var k=a,m=c.style;1===d.length?d=d[0]:(m=d[0].offset()[m]+d[1].offset()[m],d=d[k<m/2?0:1]);a=100*(a-b.offset()[c.style])/e;A(b,d,a,[c.slide,c.set])}function P(a,b,c){var d=b.b.data("handles"),e;e=c.orientation?a.pointY:a.pointX;a=(e=e<b.b.offset()[c.style])?0:100;e=e?0:d.length-1;A(b.b,d[e],a,[c.slide,c.set])}function Q(a,b){function c(a){if(2!==a.length)return!1;
a=[parseFloat(a[0]),parseFloat(a[1])];return!n(a[0])||!n(a[1])||a[1]<a[0]?!1:a}var d={f:function(a,b){switch(a){case 1:case 0.1:case 0.01:case 0.001:case 1E-4:case 1E-5:a=a.toString().split(".");b.decimals="1"===a[0]?0:a[1].length;break;case void 0:b.decimals=2;break;default:return!1}return!0},e:function(a,b,c){if(!a)return b[c].mark=".",!0;switch(a){case ".":case ",":return!0;default:return!1}},g:function(a,b,c){function d(a){return t(a)||"string"===typeof a||"function"===typeof a||!1===a||t(a[0])&&
"function"===typeof a[0][a[1]]}function g(a){var b=[[],[]];d(a)?b[0].push(a):f.each(a,function(a,e){1<a||(d(e)?b[a].push(e):b[a]=b[a].concat(e))});return b}if(a){var l,h;a=g(a);b.direction&&a[1].length&&a.reverse();for(l=0;l<b.handles;l++)for(h=0;h<a[l].length;h++){if(!d(a[l][h]))return!1;a[l][h]||a[l].splice(h,1)}b[c].to=a}else b[c].to=[[],[]];return!0}};f.each({handles:{r:!0,t:function(a){a=parseInt(a,10);return 1===a||2===a}},range:{r:!0,t:function(a,b,d){b[d]=c(a);return b[d]&&b[d][0]!==b[d][1]}},
start:{r:!0,t:function(a,b,d){if(1===b.handles)return f.isArray(a)&&(a=a[0]),a=parseFloat(a),b.start=[a],n(a);b[d]=c(a);return!!b[d]}},connect:{r:!0,t:function(a,b,c){if("lower"===a)b[c]=1;else if("upper"===a)b[c]=2;else if(!0===a)b[c]=3;else if(!1===a)b[c]=0;else return!1;return!0}},orientation:{t:function(a,b,c){switch(a){case "horizontal":b[c]=0;break;case "vertical":b[c]=1;break;default:return!1}return!0}},margin:{r:!0,t:function(a,b,c){a=parseFloat(a);b[c]=s(b.range,a);return n(a)}},direction:{r:!0,
t:function(a,b,c){switch(a){case "ltr":b[c]=0;break;case "rtl":b[c]=1;b.connect=[0,2,1,3][b.connect];break;default:return!1}return!0}},behaviour:{r:!0,t:function(a,b,c){b[c]={tap:a!==(a=a.replace("tap","")),extend:a!==(a=a.replace("extend","")),drag:a!==(a=a.replace("drag","")),fixed:a!==(a=a.replace("fixed",""))};return!a.replace("none","").replace(/\-/g,"")}},serialization:{r:!0,t:function(a,b,c){return d.g(a.to,b,c)&&d.f(a.resolution,b)&&d.e(a.mark,b,c)}},slide:{t:function(a){return f.isFunction(a)}},
set:{t:function(a){return f.isFunction(a)}},block:{t:function(a){return f.isFunction(a)}},step:{t:function(a,b,c){a=parseFloat(a);b[c]=s(b.range,a);return n(a)}}},function(c,d){var f=a[c],g=void 0!==f;if(d.r&&!g||g&&!d.t(f,a,c))throw console&&console.log&&console.group&&(console.group("Invalid noUiSlider initialisation:"),console.log("Option:\t",c),console.log("Value:\t",f),console.log("Slider(s):\t",b),console.groupEnd()),new RangeError("noUiSlider");})}function R(a){this.data("options",f.extend(!0,
{},a));a=f.extend({handles:2,margin:0,connect:!1,direction:"ltr",behaviour:"tap",orientation:"horizontal"},a);a.serialization=a.serialization||{};Q(a,this);a.style=a.orientation?"top":"left";return this.each(function(){var b=f(this),c,d=[],e,k=f("<div/>").appendTo(b);if(b.data("base"))throw Error("Slider was already initialized.");b.data("base",k).addClass([g[6],g[16+a.direction],g[10+a.orientation]].join(" "));for(c=0;c<a.handles;c++)e=f("<div><div/></div>").appendTo(k),e.addClass(g[1]),e.children().addClass([g[2],
g[2]+g[7+a.direction+(a.direction?-1*c:c)]].join(" ")),e.data({base:k,target:b,options:a,grab:e.children(),pct:-1}).attr("data-style",a.style),e.data({store:K(e,c,a.serialization)}),d.push(e);switch(a.connect){case 1:b.addClass(g[9]);d[0].addClass(g[12]);break;case 3:d[1].addClass(g[12]);case 2:d[0].addClass(g[9]);case 0:b.addClass(g[12])}k.addClass(g[0]).data({target:b,options:a,handles:d});b.val(a.start);if(!a.behaviour.fixed)for(c=0;c<d.length;c++)p(q.start,d[c].children(),B,{b:k,target:b,a:[d[c]]});
a.behaviour.tap&&p(q.start,k,O,{b:k,target:b});a.behaviour.extend&&(b.addClass(g[19]),a.behaviour.tap&&p(q.start,b,P,{b:k,target:b}));a.behaviour.drag&&(c=k.find("."+g[9]).addClass(g[18]),a.behaviour.fixed&&(c=c.add(k.children().not(c).data("grab"))),p(q.start,c,B,{b:k,target:b,a:d}))})}function S(){var a=f(this).data("base"),b=[];f.each(a.data("handles"),function(){b.push(f(this).data("store").val())});return 1===b.length?b[0]:a.data("options").direction?b.reverse():b}function T(a,b){f.isArray(a)||
(a=[a]);return this.each(function(){var c=f(this).data("base"),d,e=Array.prototype.slice.call(c.data("handles"),0),g=c.data("options");1<e.length&&(e[2]=e[0]);g.direction&&a.reverse();for(c=0;c<e.length;c++)if(d=a[c%2],null!==d&&void 0!==d){"string"===f.type(d)&&(d=d.replace(",","."));var h=g.range;d=parseFloat(d);d=s(h,0>h[0]?d+Math.abs(h[0]):d-h[0]);g.direction&&(d=100-d);!0!==x(e[c],d)&&e[c].data("store").val(!0);!0===b&&r(g.set,f(this))}})}function U(a){var b=[[a,""]];f.each(a.data("base").data("handles"),
function(){b=b.concat(f(this).data("store").elements)});f.each(b,function(){1<this.length&&this[0].off(h)});a.removeClass(g.join(" "));a.empty().removeData("base options")}function V(a){return this.each(function(){var b=f(this).val()||!1,c=f(this).data("options"),d=f.extend({},c,a);!1!==b&&U(f(this));a&&(f(this).noUiSlider(d),!1!==b&&d.start===c.start&&f(this).val(b))})}var z=f(document),y=f("body"),h=".nui",W=f.fn.val,g="noUi-base noUi-origin noUi-handle noUi-input noUi-active noUi-state-tap noUi-target -lower -upper noUi-connect noUi-horizontal noUi-vertical noUi-background noUi-stacking noUi-block noUi-state-blocked noUi-ltr noUi-rtl noUi-dragable noUi-extended noUi-state-drag".split(" "),
q=window.navigator.pointerEnabled?{start:"pointerdown",move:"pointermove",end:"pointerup"}:window.navigator.msPointerEnabled?{start:"MSPointerDown",move:"MSPointerMove",end:"MSPointerUp"}:{start:"mousedown touchstart",move:"mousemove touchmove",end:"mouseup touchend"};f.fn.val=function(){return this.hasClass(g[6])?arguments.length?T.apply(this,arguments):S.apply(this):W.apply(this,arguments)};return(D?V:R).call(this,C)}})(window.jQuery||window.Zepto);

(function($){
	
	"use strict";
	
	$(function(){
		
		function get_ajax_counts($searchform)
		{
			var form_data = $searchform.serialize();
			
			//loop through and find all taxonomy fields
			var field_names = new Array();
			$(".searchandfilter > ul > li").each(function(){
				
				var field_name = $(this).attr("data-sf-field-name");
				field_names.push(field_name);
			});
			
			var data = {
				'action': 'get_counts',
				'fields': field_names,
				'form_data': form_data
			};
			
			$searchform.find("> ul > li select, > ul > li input:not(.sf-field-submit input), > ul > li > ul").stop(true, true).animate({ opacity: 0.7 }, "fast"); //loading
			
			jQuery.post(SF_LDATA.ajax_url+"?action=get_counts", form_data, function(response)
			{				
				handle_count_table($searchform, response);
				
				$searchform.find("> ul > li select, > ul > li input:not(.sf-field-submit input), > ul > li > ul").stop(true, true).animate({ opacity: 1}, "fast"); //loading
				
			}, 'json');
			
		}
		
		function set_form_loading($searchform)
		{
			$searchform.stop(true, true).fadeIn("fast");
		}
		
		function handle_count_table($searchform, count_table)
		{
			if(typeof(count_table)!="undefined")
			{
				
				for (var key in count_table)
				{
					if (count_table.hasOwnProperty(key))
					{
						var $term_element = $searchform.find("[data-sf-cr="+key+"]")
						
						if($term_element.length>0)
						{
							var term_hide_empty = $term_element.attr("data-sf-hide-empty");
							
							// radio boxes & checkboxes
							if($term_element.is("input"))
							{//then it is a checkbox or radio
								
								if(($term_element.prop("type")=="checkbox")||($term_element.prop("type")=="radio"))
								{
									if(count_table[key]==0)
									{
										$term_element.prop( "checked", false );
										$term_element.prop("disabled", true);
										$term_element.parent().parent().attr("data-sf-disabled", "1");
										
										if(term_hide_empty==1)
										{
											$term_element.parent().parent().addClass("hide");
										}
										else
										{
											$term_element.parent().parent().addClass("disabled");
										}
										
									}
									else
									{
										$term_element.prop("disabled", false);
										$term_element.parent().parent().attr("data-sf-disabled", "1");
										
										if(term_hide_empty==1)
										{
											$term_element.parent().parent().removeClass("hide");
										}
										else
										{
											$term_element.parent().parent().removeClass("disabled");
										}
										
									}
									
									var $sfcount = $term_element.parent().find(".sf-count");
									if($sfcount.length>0)
									{
										$sfcount.html('('+count_table[key]+')');
									}
								}								
							}
							else if($term_element.is("option"))
							{//then it is a select or multiselect
								if(count_table[key]==0)
								{
									$term_element.prop( "selected", false );
									$term_element.prop("disabled", true);
									$term_element.attr("data-sf-disabled", "1");
									
									if(($term_element.parent().attr("data-combobox")!=1)&&($term_element.parent().attr("multiple")!="multiple"))
									{
										if(term_hide_empty==1)
										{
											$term_element.addClass("hide");
										}
										else
										{
											$term_element.addClass("disabled");
										}
									}
									else
									{
										
									}
									
								}
								else
								{
									$term_element.prop("disabled", false);
									$term_element.attr("data-sf-disabled", "0");
									
									if($term_element.parent().attr("data-combobox")==1)
									{
										
									}
									else
									{										
										if(term_hide_empty==1)
										{
											$term_element.removeClass("hide");
										}
										else
										{
											$term_element.removeClass("disabled");
										}
									}
								}
								
								var option_text = $term_element.text();
								var split_index = option_text.lastIndexOf("(");
								//alert(split_index);
								if(split_index!=-1)
								{
									var end_text = option_text.substring(split_index);
									//$term_element.html("SDF");
									var new_option_text = option_text.replace(end_text, '('+count_table[key]+')');
									$term_element.text(new_option_text);
									
								}
								$term_element.parent().trigger("chosen:updated");
							}
						}
					}
				}
			}
		}
		addDatePickers(".searchandfilter .datepicker");
		
		addRangeSliders(".searchandfilter .meta-range");
		
		initSearchForms();
		
		
		
		function addDatePickers(date_picker_selector)
		{
			var $date_picker = $(date_picker_selector);
			
			if($date_picker.length>0)
			{
				$date_picker.each(function(){
				
					var $this = $(this);
					var dateFormat = $this.closest(".sf_date_field").attr("data-date-format");
					
					var $closest_date_wrap = $this.closest(".sf_date_field");
					if($closest_date_wrap.length>0)
					{
						dateFormat = $closest_date_wrap.attr("data-date-format");
					}
				
					$this.datepicker({
						inline: true,
						showOtherMonths: true,
						onSelect: dateSelect,
						dateFormat: dateFormat
					});
					
					
				});
				
				if($('.ll-skin-melon').length==0)
				{
					$date_picker.datepicker('widget').wrap('<div class="ll-skin-melon"/>');
				}
				
			}
		}
		
		function addRangeSliders(date_picker_selector)
		{
			var $meta_range = $(date_picker_selector);
			
			if($meta_range.length>0)
			{		
				$meta_range.each(function(){
					
					var $this = $(this);
					var min = $this.attr("data-min");
					var max = $this.attr("data-max");
					var smin = $this.attr("data-start-min");
					var smax = $this.attr("data-start-max");
					var step = $this.attr("data-step");
					var $start_val = $this.find('.range-min');
					var $end_val = $this.find('.range-max');
					
					$(this).find(".meta-slider").noUiSlider({
						range: [min,max],
						start: [smin,smax],
						handles: 2,
						connect: true,
						step: step,
						serialization: {
							 to: [ $start_val, $end_val],
							 resolution: 1
						},
						behaviour: 'extend-tap'
					});
					
				});
			}
		}
		
		
		$(".searchandfilter").submit(function(e)
		{
			var $thisform = $(this);
			
			var template_is_loaded = $thisform.attr("data-template-loaded");
			var use_ajax = $thisform.attr("data-ajax");
			var use_ajax_shortcode = $thisform.attr("data-ajax-shortcode");
			
			
			if(use_ajax_shortcode!="1")
			{
				var form_id = $thisform.attr("data-sf-form-id");
				
				//if(template_is_loaded==1)
				//{//if a template is loaded then use ajax
					
					if(use_ajax==1)
					{
						e.preventDefault();
						
						var timestamp = new Date().getTime();
						$thisform.find("input[name=_sf_ajax_timestamp]").remove();
						$thisform.append('<input type="hidden" name="_sf_ajax_timestamp" value="'+timestamp+'" />');
						postAjaxResults($thisform, form_id);
						
						return false;
					}
				//}
			}
			else
			{
				return false;
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
			 
                        
			var $ajax_target_object = jQuery(ajax_target_attr);
			var $ajax_links_object = jQuery(ajax_links_selector);
                        
			$ajax_target_object.animate({ opacity: 0.5 }, "fast"); //loading
			//var pageNumber = $ajax_target_object.attr("data-paged");
                        
			$thisform.trigger("sf:ajaxstart", [ "Custom", "Event" ]);
			
			var jqxhr = $.post(SF_LDATA.homeurl, $thisform.serialize(), function(data, status, request)
			{
                             
                                var $timestamp_input = $(data).find('.sf_ajax_timestamp');
				var newTimestamp = 0;
				if($timestamp_input.length>0)
				{
					newTimestamp = $timestamp_input.val()
				}
				
				if(newTimestamp>ajaxLastTimestamp)
				{
					ajaxLastTimestamp = newTimestamp;
					handleAjaxUpdate(data, form_id, use_history_api, ajax_target_attr, ajax_links_selector, $ajax_target_object, $ajax_links_object);
				}
				else
				{
					
				}
                                
                                
			
			}).fail(function()
			{
				
			}).always(function()
			{
				$ajax_target_object.stop(true,true).animate({ opacity: 1}, "fast"); //finished loading				
				$thisform.trigger("sf:ajaxfinish", [ "Custom", "Event" ]);
			});
		}
		
		function handleAjaxUpdate(data, form_id, use_history_api, ajax_target_attr, ajax_links_selector, $ajax_target_object, $ajax_links_object)
		{
			var $data_obj = $(data);
			var $ajaxed_search_form_post = $data_obj.find('*[data-sf-form-id='+form_id+']');
                        var this_url = $ajaxed_search_form_post.attr('data-ajax-url');
                        var $new = $data_obj.find(ajax_target_attr);
			
                        
                        $ajax_target_object.html($new.html());
                      
			if(use_history_api==1)
			{
			
				//now check if the browser supports history state push :)
				if (window.history && window.history.pushState)
				{
					history.pushState(null, null, this_url);
				}
			}
                        
		}
		
		function dateSelect()
		{
			var $this = $(this);
			var $thisform = $this.closest(".searchandfilter");
			var auto_update = $thisform.attr("data-auto-update");
			
			if(auto_update==1)
			{
				var $tf_date_pickers = $thisform.find(".datepicker");
				var no_date_pickers = $tf_date_pickers.length;
				
				if(no_date_pickers>1)
				{					
					//then it is a date range, so make sure both fields are filled before updating
					var dp_counter = 0;
					var dp_empty_field_count = 0;
					$tf_date_pickers.each(function(){
					
						if($(this).val()=="")
						{
							dp_empty_field_count++;
						}
						
						dp_counter++;
					});
					
					if(dp_empty_field_count==0)
					{
						inputUpdate(1);
					}
				}
				else
				{
					inputUpdate(1);
				}
			}
		}
		function dateInputType()
		{
			var $this = $(this);
			var $thisform = $this.closest(".searchandfilter");
			var auto_update = $thisform.attr("data-auto-update");
			
			if(auto_update==1)
			{
				var $tf_date_pickers = $thisform.find(".datepicker");
				var no_date_pickers = $tf_date_pickers.length;
				
				if(no_date_pickers>1)
				{					
					//then it is a date range, so make sure both fields are filled before updating
					var dp_counter = 0;
					var dp_empty_field_count = 0;
					$tf_date_pickers.each(function(){
					
						if($(this).val()=="")
						{
							dp_empty_field_count++;
						}
						
						dp_counter++;
					});
					
					if(dp_empty_field_count==0)
					{
						inputUpdate(1200);
					}
				}
				else
				{
					inputUpdate(1200);
				}
			}
		}
		
		function inputUpdate(delayDuration)
		{
			if(typeof(delayDuration)=="undefined")
			{
				var delayDuration = 300;
			}
			
			resetTimer(delayDuration);
		}
		
		var firstInput = false;
		var inputTimer = 0;
		var ajaxLastTimestamp = 0;
		
		function resetTimer(delayDuration)
		{
			clearTimeout(inputTimer)
			inputTimer = setTimeout(submitForm, delayDuration);
			
		}
		
		function submitForm()
		{
			var $thisform = $('.searchandfilter');
			
			var auto_update = $thisform.attr("data-auto-update");
			var use_ajax_shortcode = $thisform.attr("data-ajax-shortcode");
			
			if(use_ajax_shortcode!="1")
			{
				if(auto_update==1)
				{
					$thisform.submit();
				}
			}
			
			
		}
		
		function initSearchForms()
		{
			var $search_forms = $('.searchandfilter');
			
			if($search_forms.length>0)
			{//loop through each page form, and see if they have pagination
				
				$search_forms.each(function(){
					
					//submit without submit button
					
					var $thisform = $(this);
					
					var use_ajax_shortcode = $thisform.attr("data-ajax-shortcode");
					
					if(use_ajax_shortcode!="1")
					{
						
						$(this).find('input').keypress(function(e) {
							// Enter pressed?
							if(e.which == 10 || e.which == 13) {
								$thisform.submit();
							}
						});
						
						var template_is_loaded = $thisform.attr("data-template-loaded");
						var use_ajax = $thisform.attr("data-ajax");
						var auto_update = $thisform.attr("data-auto-update");
						var auto_count = $thisform.attr("data-auto-count");
						
						var form_id = $thisform.attr("data-sf-form-id");
						
						//init combo boxes
						var $chosen = $thisform.find("select[data-combobox=1]");
						
						if($chosen.length>0)
						{
							$chosen.chosen();
						}
						
						if(auto_count==1)
						{
							//auto refresh counts
							$($thisform).on('change', 'input[type=radio], input[type=checkbox], select, .meta-slider', function(e)
							{
								get_ajax_counts($thisform);
							});
							
							$($thisform).on('input', 'input[type=number], input[type=text]:not(.datepicker)', function(e)
							{
								get_ajax_counts($thisform);
							});
						}
						
						
						//$($thisform).on('input', 'input.datepicker', dateInputType);
						
						//if(template_is_loaded==1)
						//{//if a template is loaded then use ajax
							
							if(use_ajax==1)
							{
								if(auto_update==1)
								{
									$($thisform).on('change', 'input[type=radio], input[type=checkbox], select', function(e)
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
									$($thisform).on('input', 'input.datepicker', dateInputType);
								}
								
								
								var use_history_api = $thisform.attr("data-use-history-api");
								var ajax_target_attr = $thisform.attr("data-ajax-target");
								var ajax_links_selector = $thisform.attr("data-ajax-links-selector");
								if(typeof(ajax_links_selector)!="undefined")
								{
									var $ajax_target_object = jQuery(ajax_target_attr);
									var $ajax_links_object = jQuery(ajax_links_selector);
									
									if($ajax_links_object.length>0)
									{
										$(document.body).on('click', ajax_links_selector, function(e) {
											
											e.preventDefault();
											
											$ajax_target_object.animate({ opacity: 0.5 }, "fast"); //loading
											
											var link = jQuery(this).attr('href');
											
											
											$thisform.trigger("sf:ajaxstart", [ "Custom", "Event" ]);
											// #entries is the ID of the inner div wrapping your posts
											var jqxhr_get = $.get(link, function(data)
											{
												handleAjaxUpdate(data, form_id, use_history_api, ajax_target_attr, ajax_links_selector, $ajax_target_object, $ajax_links_object)
												$ajax_target_object.stop(true,true).animate({ opacity: 1}, "fast"); //finished loading				
											})
											.always(function()
											{
												$ajax_target_object.stop(true,true).animate({ opacity: 1}, "fast"); //finished loading				
												$thisform.stop(true,true).animate({ opacity: 1}, "fast"); //finished loading
												$thisform.trigger("sf:ajaxfinish", [ "Custom", "Event" ]);
											});
											
											return false;
										});
									}
								}
								
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
		
		window.addEventListener("popstate", function(e)
		{
			var form_id = SF_LDATA.sfid;
			
			if(form_id==0)
			{
				//window.location.replace(location);
			}
			else
			{
				var $thisform = $('*[data-sf-form-id='+form_id+']');
				var use_ajax = $thisform.attr("data-ajax");
				
				var use_history_api = 0;
				if (window.history && window.history.pushState)
				{
					use_history_api = $thisform.attr("data-use-history-api");
				}
				
				
				if((use_history_api==1)&&(use_ajax==1))
				{
					var ajax_target_attr = $thisform.attr("data-ajax-target");
					var ajax_links_selector = $thisform.attr("data-ajax-links-selector");
					var $ajax_links = jQuery(ajax_links_selector);
					var $ajax_target_object = jQuery(ajax_target_attr);
								
					$ajax_target_object.animate({ opacity: 0.5 }, "fast"); //loading
					$thisform.animate({ opacity: 0.5 }, "fast"); //loading
					
					var jqxhr_get = $.get( location, function(data, status, request)
					{
						var $data_obj_get = $(data);
						
						//update results data
						$ajax_target_object.html($data_obj_get.find(ajax_target_attr).html());
						
						//update form data / presets
						var $ajaxed_search_form_get = $data_obj_get.find('*[data-sf-form-id='+form_id+']');
						$thisform.html($ajaxed_search_form_get.html());
						
						//re attach any js handlers
						addDatePickers(".searchandfilter .datepicker");
						addRangeSliders(".searchandfilter .meta-range");
						
						
					}).always(function()
					{
						$ajax_target_object.stop(true,true).animate({ opacity: 1}, "fast"); //finished loading				
						$thisform.stop(true,true).animate({ opacity: 1}, "fast"); //finished loading				
					});
				}
				else
				{
					//window.location.replace(location);
				}
			}
		});
	});
	
})(window.jQuery);
