
function get_blox_element_accordion_item($attrs, $content){
	$actions_top = '<div class="blox_action_buttons clearfix"> \
                            <a href="javascript:;" class="ac_edit_current"><i class="fa-pencil"></i></a> \
                            <a href="javascript:;" class="ac_clone_current"><i class="fa-copy"></i></a> \
                            <a href="javascript:;" class="ac_remove_current"><i class="fa-times"></i></a> \
                            <a href="javascript:;" class="ac_add_blox_elem" data-rel="top"><i class="fa-plus"></i> Add Element</a> \
                    </div>';
	$actions_bottom = '<div class="blox_action_buttons"> \
                            <a href="javascript:;" class="ac_add_blox_elem float_none" data-rel="bottom"><i class="fa-plus"></i> Add Element</a> \
                    </div>';
					
	$new_content = '<div class="blox_accordion_item group" collapse="'+($attrs!=undefined && $attrs.find('collapse').html()=='1' ? '1' : '')+'">';
		$new_content += '<h3>'+($attrs!=undefined ? (($attrs.find('icon').html()!='' ? '<i class="'+$attrs.find('icon').html()+'"></i>' : '')+$attrs.find('title').html()) : 'Accordion title')+'</h3>';
		$new_content += '<div class="blox_accordion_content">'+$actions_top+'<div class="blox_accordion_item_content blox_container">'+($content!=undefined ? $content : '')+'</div>'+$actions_bottom+'</div>';
	$new_content += '</div>';
	return $new_content;
}

function get_blox_element_accordion($content, $attrs){
	actions = '<div class="blox_item_actions blox_ac_general_action"> \
                            <a href="javascript:;" class="fa-pencil action_edit"></a> \
                            <a href="javascript:;" class="fa-copy action_clone"></a> \
                            <a href="javascript:;" class="fa-times action_remove"></a> \
                    </div>';
	return '<div class="blox_item blox_accordion" '+($attrs!=undefined ? $attrs : '')+'>'
				+ actions
				+ '<div class="blox_accordion_obj">'
				+ ($content!=undefined ? $content : get_blox_element_accordion_item())
				+ '</div> \
				<div class="blox_accordion_bottom_tb"> \
					<a href="javascript:;" class="blox_ac_item_add"><i class="icon-fixed-width"></i> Add Accordion Section</a> \
				</div> \
			</div>';
}

function parse_shortcode_accordion($content){
	$content = wp.shortcode.replace( 'blox_accordion', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			attrs += key+'="'+value+'" ';
		})
		return get_blox_element_accordion(data.content, attrs);
	});
	
	$content = wp.shortcode.replace( 'blox_accordion_item', $content, function(data){
		var title = 'Accordion Title';
		var icon = '';
		var collapse = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( key=='title' ){
				title = value;
			}
			else if( key=='icon' ){
				icon = value;
			}
			else if( key=='collapse' ){
				collapse = value;
			}
		})
		var $tmp = jQuery('<div></div>')
						.append('<title>'+title+'</title>')
						.append('<icon>'+icon+'</icon>')
						.append('<collapse>'+collapse+'</collapse>');
		return get_blox_element_accordion_item($tmp, data.content);
	});
	return $content;
}

function revert_shortcode_accordion($content){
	$content.find('.blox_accordion_item').each(function(){
		var current_class = jQuery(this).find('> h3').find('i').attr('class')+'';
		current_class = current_class!='undefined' ? ' icon="'+current_class+'"' : '';
		current_class += jQuery(this).attr('collapse')!=='undefined' && jQuery(this).attr('collapse')=='1' ? ' collapse="1"' : '';
		jQuery(this).replaceWith('[blox_accordion_item'+current_class+' title="'+jQuery(this).find('> h3').text()+'"]'+jQuery(this).find('.blox_accordion_item_content').html()+'[/blox_accordion_item]');
	});
	$content.find('.blox_accordion').each(function(){
		jQuery(this).find('.blox_item_actions').remove();
		attr = '';
		var temp_val = '';

        temp_val = jQuery(this).attr('title')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' title="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('extra_class')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' extra_class="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('animation')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' animation="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('visibility')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' visibility="'+temp_val+'"';
        }

		if( jQuery(this).attr('collaps_all')=='1' ){
			attr += ' collaps_all="1"';
		}
		if( jQuery(this).attr('border')=='1' ){
			attr += ' border="1"';
		}
		jQuery(this).replaceWith('[blox_accordion'+attr+']'+jQuery(this).find('.blox_accordion_obj').html()+'[/blox_accordion]');
	});
	return $content;
}

function add_event_blox_element_accordion(){
	jQuery('.blox_accordion').each(function(){
		var $this = jQuery(this);
		$this.find('.blox_accordion_obj')
			.accordion({
				header: "> div > h3",
				heightStyle: "content"
			})
			.sortable({
				axis: "y",
		        handle: "h3",
		        stop: function( event, ui ) {
					ui.item.children( "h3" ).triggerHandler( "focusout" );
		        }
		      });
		
		$this.find('.blox_ac_item_add').unbind('click')
			.click(function(){
				$this.find('.blox_accordion_obj').accordion('destroy').append(get_blox_element_accordion_item());
				add_event_blox_element_accordion();
			});
		
		// general actions
		$this.find('.blox_ac_general_action .action_edit').unbind('click')
			.click(function(){
				var $current_ac = jQuery(this).parent().parent();

				var form_element = [
                			{
                				type: 'input',
                				id: 'blox_ac_option_widget_title',
                				label: 'Accordion Title',
                				value: $this.attr('title')
                			},
                			{
                				type: 'checkbox_flat',
                				id: 'blox_ac_option_border',
                				label: 'Active Border',
                				value: $this.attr('border')
                			}
            			];

                show_blox_form('Edit Accordion', form_element, function($form){
                    if( jQuery('#blox_ac_option_widget_title').val()!='' ){
        				$this.attr('title', jQuery('#blox_ac_option_widget_title').val());
        			}
        			else{ $this.removeAttr('title'); }
        			
        			if( jQuery('#blox_ac_option_border').val()=='1' ){
        				$this.attr('border', '1');
        			}
        			else{ $this.removeAttr('border'); }
                },
                {
                	target: $this,
                	extra_field: true,
                	visibility: true
                });

			});
		$this.find('.blox_ac_general_action .action_clone').unbind('click')
			.click(function(){
				$this.after( $this.clone() );
				refresh_blox_events();
			});
		$this.find('.blox_ac_general_action .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
		
		
		$this.find('.ac_add_blox_elem').unbind('click')
			.click(function(){
				$context = jQuery(this).parent().parent().find('.blox_accordion_item_content');
				add_blox_element($context, jQuery(this).attr('data-rel'));
			});
			
		$this.find('.ac_edit_current').unbind('click')
			.click(function(){
				$current_ac = jQuery(this).parent().parent().parent();
				var current_class = $current_ac.find('> h3').find('i').attr('class') + '';
				current_class = current_class!='undefined' ? current_class : '';

				var form_element = [
                			{
                				type: 'icon',
                				id: 'blox_acc_section_icon',
                				label: 'Icon',
                				value: current_class
                			},
                			{
                				type: 'input',
                				id: 'blox_acc_section_title',
                				label: 'Section Title',
                				value: $current_ac.find('> h3').text()
                			},
                			{
                				type: 'checkbox_flat',
                				id: 'blox_ac_section_open',
                				label: 'Open Section',
                				value: $current_ac.attr('collapse')
                			}
            			];

                show_blox_form('Edit Accordion Section', form_element, function($form){
                    $current_ac.find('> h3').html( (jQuery('#blox_acc_section_icon').val()!='' ? '<i class="'+jQuery('#blox_acc_section_icon').val()+'"></i>' : '')+jQuery('#blox_acc_section_title').val() );

                    if( jQuery('#blox_ac_section_open').val()=='1' ){
        				$current_ac.parent().find('.blox_accordion_item').removeAttr('collapse');
        				$current_ac.attr('collapse', '1');
        			}
        			else{
        				$current_ac.removeAttr('collapse');
        			}
                });

			});
		$this.find('.ac_clone_current').unbind('click')
			.click(function(){
				$current_ac = jQuery(this).parent().parent().parent();
				$this.find('.blox_accordion_obj').accordion('destroy');
				$current_ac.after( $current_ac.clone() );
				add_event_blox_element_accordion();
			});
		$this.find('.ac_remove_current').unbind('click')
			.click(function(){
				$current_ac = jQuery(this).parent().parent().parent();
				$current_ac.remove();
				$this.find('.blox_accordion_obj').accordion('destroy');
				add_event_blox_element_accordion();
			});
			
		
	});
}
