
function get_blox_element_countdown($content, $attrs){
	
	return '<div class="blox_item blox_countdown" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-user"></i> \
					<span class="blox_item_title">Countdown</span> \
					<small></small> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'
                + '<div class="blox_item_countdown_style">'+($content!=undefined && $content.find('.style').length>0 ? $content.find('.style').html() : '')+'</div>'
                + '<div class="blox_item_countdown_date">'+($content!=undefined && $content.find('.date').length>0 ? $content.find('.date').html() : '')+'</div>'
                + '<div class="blox_item_countdown_bgcolor">'+($content!=undefined && $content.find('.bgcolor').length>0 ? $content.find('.bgcolor').html() : '')+'</div>'
                + '<div class="blox_item_countdown_fgcolor">'+($content!=undefined && $content.find('.fgcolor').length>0 ? $content.find('.fgcolor').html() : '')+'</div>'
                + '<div class="blox_item_countdown_textcolor">'+($content!=undefined && $content.find('.textcolor').length>0 ? $content.find('.textcolor').html() : '')+'</div>'
                + '<div class="blox_item_countdown_width">'+($content!=undefined && $content.find('.circle_width').length>0 ? $content.find('.circle_width').html() : '')+'</div>'
				+ '<div class="blox_item_countdown_extra_class">'+($content!=undefined && $content.find('.extra_class').length>0 ? $content.find('.extra_class').html() : '')+'</div>'
				+ '<div class="blox_item_countdown_animation">'+($content!=undefined && $content.find('.animation').length>0 ? $content.find('.animation').html() : '')+'</div>'
				+'</div> \
			</div>';
}


function parse_shortcode_countdown($content){
	$content = wp.shortcode.replace( 'blox_countdown', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( value!=undefined && value!='undefined' && value!='' ){
                                                          
				if( key=='style' ){
					attrs += '<div class="style">'+value+'</div>';
				}
				if( key=='date' ){
					attrs += '<div class="date">'+value+'</div>';
				}
				if( key=='bgcolor' ){
					attrs += '<div class="bgcolor">'+value+'</div>';
				}
				if( key=='fgcolor' ){
					attrs += '<div class="fgcolor">'+value+'</div>';
				}
				if( key=='textcolor' ){
					attrs += '<div class="textcolor">'+value+'</div>';
				}
				if( key=='circle_width' ){
					attrs += '<div class="circle_width">'+value+'</div>';
				}
				if( key=='animation' ){
					attrs += '<div class="animation">'+value+'</div>';
				}
				if( key=='extra_class' ){
					attrs += '<div class="extra_class">'+value+'</div>';
				}
			}
		});		
		return get_blox_element_countdown(jQuery('<div class="tmp_pass">'+attrs+'</div>'));
	});
	return $content;
}

function revert_shortcode_countdown($content){
	$content.find('.blox_countdown').each(function(){
		attr = '';
		var temp_val = '';

		temp_val = jQuery(this).find('.blox_item_countdown_style').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' style="'+temp_val+'"';
		}

		temp_val = jQuery(this).find('.blox_item_countdown_date').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' date="'+temp_val+'"';
		}

		temp_val = jQuery(this).find('.blox_item_countdown_bgcolor').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' bgcolor="'+temp_val+'"';
		}

		temp_val = jQuery(this).find('.blox_item_countdown_fgcolor').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' fgcolor="'+temp_val+'"';
		}

		temp_val = jQuery(this).find('.blox_item_countdown_textcolor').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' textcolor="'+temp_val+'"';
		}

		temp_val = jQuery(this).find('.blox_item_countdown_width').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' circle_width="'+temp_val+'"';
		}

		temp_val = jQuery(this).find('.blox_item_countdown_animation').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' animation="'+temp_val+'"';
		}

		temp_val = jQuery(this).find('.blox_item_countdown_extra_class').html()+'';
		if( temp_val!='undefined' && temp_val!='' ){
			attr += ' extra_class="'+temp_val+'"';
		}
		
		jQuery(this).replaceWith('[blox_countdown'+attr+']');
	});
	return $content;
}


function add_event_blox_element_countdown(){
	
	jQuery('.blox_countdown').each(function(){
		var $this = jQuery(this);
		
		$this.find('.blox_item_actions .action_edit').unbind('click')
			.click(function(){

				var form_element = [
                            {
                				type: 'datetime',
                				id: 'blox_element_option_date',
                				label: 'End date',
                				value: $this.find('.blox_item_countdown_date').html(),
                                                description: 'Please select End Date of count then element calculates remaining day and time since now.'
                			},
                			{
                				type: 'colorpicker',
                				id: 'blox_element_option_bgcolor',
                				label: 'Circle Background Color',
                				value: $this.find('.blox_item_countdown_bgcolor').html()
                			},
                			{
                				type: 'colorpicker',
                				id: 'blox_element_option_fgcolor',
                				label: 'Circle Holder Color',
                				value: $this.find('.blox_item_countdown_fgcolor').html()
                			},
                			{
                				type: 'colorpicker',
                				id: 'blox_element_option_textcolor',
                				label: 'Circle Text Color',
                				value: $this.find('.blox_item_countdown_textcolor').html()
                			},
                			{
                				type: 'number',
                				id: 'blox_element_option_width',
                				label: 'Circle Width',
                				value: parseInt($this.find('.blox_item_countdown_width').html())>0 ? $this.find('.blox_item_countdown_width').html() : '170',
                				description: 'Circle width (px)'
                			}
                			];

               	$this.attr('animation', $this.find('.blox_item_countdown_animation').html());
        		$this.attr('extra_class', $this.find('.blox_item_countdown_extra_class').html());

                show_blox_form('Edit Countdown', form_element, function($form){
                    $this.find('.blox_item_countdown_date').html(  jQuery('#blox_element_option_date').val() );
                    $this.find('.blox_item_countdown_bgcolor').html(  jQuery('#blox_element_option_bgcolor').val() );
                    $this.find('.blox_item_countdown_fgcolor').html(  jQuery('#blox_element_option_fgcolor').val() );
                    $this.find('.blox_item_countdown_textcolor').html(  jQuery('#blox_element_option_textcolor').val() );
                    $this.find('.blox_item_countdown_width').html(  jQuery('#blox_element_option_width').val() );

                    $this.find('.blox_item_countdown_animation').html( $this.attr('animation') );
            		$this.find('.blox_item_countdown_extra_class').html( $this.attr('extra_class') );
                },
                {
                	target: $this,
                	extra_field: true
                });

			});
			
		
		$this.find('.blox_item_actions .action_clone').unbind('click')
			.click(function(){
				$this.after($this.clone());
				add_event_blox_element_countdown();
			});
			
		$this.find('.blox_item_actions .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
	});
	
}
