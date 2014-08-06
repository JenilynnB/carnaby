
function get_blox_element_dropcap($content, $attrs){
	
	return '<div class="blox_item blox_dropcap" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-user"></i> \
					<span class="blox_item_title">Dropcap</span> \
					<small></small> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'
				+ '<div class="blox_item_dropcap_text">'+($content!=undefined && $content.find('.text').length>0 ? $content.find('.text').html() : '')+'</div>'
                                + '<div class="blox_item_dropcap_style">'+($content!=undefined && $content.find('.style').length>0 ? $content.find('.style').html() : '')+'</div>'
                                + '<div class="blox_item_dropcap_color">'+($content!=undefined && $content.find('.color').length>0 ? $content.find('.color').html() : '')+'</div>'
                                + '<div class="blox_item_dropcap_size">'+($content!=undefined && $content.find('.size').length>0 ? $content.find('.size').html() : '')+'</div>'
				+ '<div class="blox_item_dropcap_extra_class">'+($content!=undefined && $content.find('.extra_class').length>0 ? $content.find('.extra_class').html() : '')+'</div>'
				+'</div> \
			</div>';
}


function parse_shortcode_dropcap($content){
	$content = wp.shortcode.replace( 'blox_dropcap', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( value!=undefined && value!='undefined' && value!='' ){
                                if( key=='text' ){
					attrs += '<div class="text">'+value+'</div>';
				}
				if( key=='style' ){
					attrs += '<div class="style">'+value+'</div>';
				}
				if( key=='color' ){
					attrs += '<div class="color">'+value+'</div>';
				}
                                if( key=='size' ){
					attrs += '<div class="size">'+value+'</div>';
				}
				if( key=='extra_class' ){
					attrs += '<div class="extra_class">'+value+'</div>';
				}
			}
		})		
		return get_blox_element_dropcap(jQuery('<div class="tmp_pass">'+attrs+'</div>'));
	});
	return $content;
}

function revert_shortcode_dropcap($content){
	$content.find('.blox_dropcap').each(function(){
		attr = '';
		var temp_val = '';

		temp_val = jQuery(this).find('.blox_item_dropcap_text').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' text="'+temp_val+'"';
		}
                
		temp_val = jQuery(this).find('.blox_item_dropcap_style').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' style="'+temp_val+'"';
		}

		temp_val = jQuery(this).find('.blox_item_dropcap_color').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' color="'+temp_val+'"';
		}
                
		temp_val = jQuery(this).find('.blox_item_dropcap_size').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			var_social = temp_val;
			var_social = var_social.replace(/\n/g, ',');
			attr += ' size="'+var_social+'"';
		}

		temp_val = jQuery(this).find('.blox_item_dropcap_extra_class').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' extra_class="'+temp_val+'"';
		}
		
		jQuery(this).replaceWith('[blox_dropcap'+attr+']');
	});
	return $content;
}


function add_event_blox_element_dropcap(){
	
	jQuery('.blox_dropcap').each(function(){
		var $this = jQuery(this);
		
		$this.find('.blox_item_actions .action_edit').unbind('click')
			.click(function(){

				var form_element = [
                			{
                				type: 'input',
                				id: 'blox_element_option_text',
                				label: 'Button text',
                				value: $this.find('.blox_item_dropcap_text').html()

                			},
                			{
                				type: 'select',
                				id: 'blox_element_option_style',
                				label: 'Style',
                				value: $this.find('.blox_item_dropcap_style').html(),
                				options: [
                						{
                							value: 'style1',
                							label: 'Rectangle'
                						},
                						{
                							value: 'style2',
                							label: 'Circle'
                						},
                						{
                							value: 'style3',
                							label: 'Flat'
                						},
                						{
                							value: 'style4',
                							label: '3D'
                						}                                                                
                						]
                			},
                			{
                				type: 'colorpicker',
                				id: 'blox_element_option_color',
                				label: 'Color',
                				value: $this.find('.blox_item_dropcap_color').html()
                			},
                                        {
                				type: 'input',
                				id: 'blox_element_option_size',
                				label: 'Size',
                				value: $this.find('.blox_item_dropcap_size').html()
                			},
                			{
                				type: 'input',
                				id: 'blog_element_extra_class',
                				label: 'Extra Class',
                				value: $this.find('.blox_item_dropcap_extra_class').html()
                			}
                			];

                show_blox_form('Edit Dropcap', form_element, function($form){
                        $this.find('.blox_item_dropcap_text').html(   jQuery('#blox_element_option_text').val() );
                	$this.find('.blox_item_dropcap_style').html(  jQuery('#blox_element_option_style').val() );
                        $this.find('.blox_item_dropcap_color').html(  jQuery('#blox_element_option_color').val() );
                        $this.find('.blox_item_dropcap_size').html(   jQuery('#blox_element_option_size').val() );
            		$this.find('.blox_item_dropcap_extra_class').html( jQuery('#blog_element_extra_class').val() );
                });

			});
			
		
		$this.find('.blox_item_actions .action_clone').unbind('click')
			.click(function(){
				$this.after($this.clone());
				add_event_blox_element_dropcap();
			});
			
		$this.find('.blox_item_actions .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
	});
	
}
