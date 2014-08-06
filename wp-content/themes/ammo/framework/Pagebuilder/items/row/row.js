function split2class(split){
	var col;
	switch(split){
		case '1/2':
			col = 'span6';
			break;
		case '1/3':
			col = 'span4';
			break;
		case '2/3':
			col = 'span8';
			break;
		case '1/6':
			col = 'span2';
			break;
		case '1/4':
			col = 'span3';
			break;
		case '3/4':
			col = 'span9';
			break;
		case '5/6':
			col = 'span10';
			break;
		default:
			col = 'span12';
			break;
	}
	return col;
}
function class2split(split){
	var col;
	switch(split){
		case 'span6':
			col = '1/2';
			break;
		case 'span4':
			col = '1/3';
			break;
		case 'span8':
			col = '2/3';
			break;
		case 'span2':
			col = '1/6';
			break;
		case 'span3':
			col = '1/4';
			break;
		case 'span9':
			col = '3/4';
			break;
		case 'span10':
			col = '5/6';
		default:
			col = '1/1';
			break;
	}
	return col;
}


var row_attrs = ['columns', 'fullwidth', 'color', 'image', 'bg_repeat', 'bg_position', 'bg_attach', 'text_light',
					'video_active', 'video_m4v', 'video_webm', 'poster', 'no_padding', 'padding_top', 'padding_bottom',
					'overlay_color', 'overlay_opacity', 'affix', 'extra_class'];


function get_blox_row_controls(){
	$actions = '<div class="blox_controls_row clearfix"> \
					<a href="javascript:;" class="move_row"><i class="fa-arrows"></i></a> \
					<a href="javascript:;" class="split_row tt-col-1" title="1/1"></a> \
					<a href="javascript:;" class="split_row tt-col-2-1" title="1/2+1/2"></a> \
					<a href="javascript:;" class="split_row tt-col-31-32" title="1/3+2/3"></a> \
					<a href="javascript:;" class="split_row tt-col-32-31" title="2/3+1/3"></a> \
					<a href="javascript:;" class="split_row tt-col-41-43" title="1/4+3/4"></a> \
					<a href="javascript:;" class="split_row tt-col-43-41" title="3/4+1/4"></a> \
					<a href="javascript:;" class="split_row tt-col-3-1" title="1/3+1/3+1/3"></a> \
					<a href="javascript:;" class="split_row tt-col-41-42-41" title="1/4+1/2+1/4"></a> \
					<a href="javascript:;" class="split_row tt-col-42-41-41" title="1/2+1/4+1/4"></a> \
					<a href="javascript:;" class="split_row tt-col-41-41-42" title="1/4+1/4+1/2"></a> \
					<a href="javascript:;" class="split_row tt-col-4-1" title="1/4+1/4+1/4+1/4"></a> \
					<a href="javascript:;" class="split_row tt-col-5-1" title="1/6+1/6+1/6+1/6+1/6+1/6"></a> \
					'+ get_blox_actions() +' \
				</div>';
	return $actions;
}


function get_blox_row_html($row_content){
	$actions = get_blox_row_controls();
	return '<div class="blox_row row-fluid">'+$actions+'<div class="blox_row_content clearfix">'+$row_content+'</div></div>';
}

function get_blox_column_html($attr, $col_content){
    var $attr_data = typeof $attr.attr!=='undefined' ? $attr.attr : '';
    var $width = typeof $attr.width!=='undefined' ? $attr.width : 'span12';
    $width = $width!='' ? $width : 'span12';

	$col_action_top = '<div class="blox_columns_action blox_action_buttons"> \
							<a href="javascript:;" class="blox_column_action_add" data-rel="top"><i class="fa-plus"></i></a> \
                            <a href="javascript:;" class="blox_column_action_edit" data-rel="top"><i class="fa-pencil"></i></a> \
						</div>';
	$col_action_bottom = '<div class="blox_columns_action blox_action_buttons"> \
							<a href="javascript:;" class="blox_column_action_add" data-rel="bottom"><i class="fa-plus"></i></a> \
						</div>';
	return '<div class="blox_column '+$width+'" '+$attr_data+'>'+$col_action_top+'<div class="blox_column_content blox_container">'+$col_content+'</div>'+$col_action_bottom+'</div>';
}

function get_blox_row_el_html($content){
	$actions = get_blox_row_controls();
	return '<div class="blox_row row-fluid">'+$actions+'<div class="blox_row_content clearfix">'+get_blox_column_html({width:'span12'}, $content!=undefined ? $content : '')+'</div></div>';
}






// parse shortcode to row
function parse_shortcode_row_hook($content){
	$content = wp.shortcode.replace( 'blox_row', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( value!=undefined && value!='undefined' && value!='' ){
				attrs += ' '+key+'="'+value+'"';
			}
		});
		$actions = get_blox_row_controls();
		$new_content = '<div class="blox_row row-fluid"'+attrs+'>'+$actions+'<div class="blox_row_content clearfix">'+data.content+'</div></div>';
		return $new_content;
	});
	
	$content = wp.shortcode.replace( 'blox_row_inner', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( value!=undefined && value!='undefined' && value!='' ){
				attrs += ' '+key+'="'+value+'"';
			}
		});
		$actions = get_blox_row_controls();
		$new_content = '<div class="blox_row row-fluid"'+attrs+'>'+$actions+'<div class="blox_row_content clearfix">'+data.content+'</div></div>';
		return $new_content;
	});
	
	return $content;
}
function parse_shortcode_row($content){
	$content = parse_shortcode_row_hook($content);
	return parse_shortcode_column($content);
}


/* Column Elements Attributes */
var element_column_attr = ['bgcolor','text_color','bgimage','bg_repeat', 'bg_position','bg_attach', 'extra_class', 'padding_top', 'padding_bottom', 'padding_left', 'padding_right'];

// parse shortcode to column
function parse_shortcode_column_hook($content){
	$content = wp.shortcode.replace( 'blox_column', $content, function(data){
		var col = 'span12';
        var attr = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( key=='width' ){
				col = split2class(value);
			}
            if( element_column_attr.indexOf(key)!=-1 ){
                attr += ' '+key+'="'+value+'"';
            }
		})
		return get_blox_column_html({width:col, attr:attr}, data.content);
	});
	
	$content = wp.shortcode.replace( 'blox_column_inner', $content, function(data){
		var col = 'span12';
        var attr = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( key=='width' ){
				col = split2class(value);
			}
            if( element_column_attr.indexOf(key)!=-1 ){
                attr += ' '+key+'="'+value+'"';
            }
		})
		return get_blox_column_html({width:col, attr:attr}, data.content);
	});
	
	return $content;
}
function parse_shortcode_column($content){
	$content = parse_shortcode_column_hook($content);
	return $content;
}





// revert to shortcode rows
function revert_shortcode_row_hook($content, $hook){
	$content.find('.blox_row').each(function(){
		var attr = '';
		var temp_val = '';

		for (var i = 0; i < row_attrs.length; i++) {
            temp_val = jQuery(this).attr(row_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ row_attrs[i] +'="'+ temp_val +'"';
            }
        }

		if( typeof $hook!=='undefined' && $hook ){
			jQuery(this).replaceWith('[blox_row_inner'+attr+']'+jQuery(this).find('> .blox_row_content').html()+'[/blox_row_inner]');
		}
		else{
			jQuery(this).replaceWith('[blox_row'+attr+']'+jQuery(this).find('> .blox_row_content').html()+'[/blox_row]');
		}

	});
}
function revert_shortcode_row($content){
	revert_shortcode_row_hook($content);
	revert_shortcode_row_hook($content, true);
	return revert_shortcode_column($content);
}






// revert to shortcode columns
function revert_shortcode_column_hook($content, $hook){
	if( typeof $hook!=='undefined' && $hook ){
		$content.find('.blox_column').each(function(){
			var size = '1/1';
			$this = jQuery(this);
			
			if( $this.hasClass('span6') ){
				size = '1/2';
			}else if( $this.hasClass('span4') ){
				size = '1/3';
			}else if( $this.hasClass('span8') ){
				size = '2/3';
			}else if( $this.hasClass('span2') ){
				size = '1/6';
			}else if( $this.hasClass('span3') ){
				size = '1/4';
			}else if( $this.hasClass('span9') ){
				size = '3/4';
			}

            var attr_data = '';
            for(var i=0; i<element_column_attr.length; i++){
                if( typeof $this.attr(element_column_attr[i])!=='undefined' && $this.attr(element_column_attr[i])!='' ){
                    attr_data += element_column_attr[i]+'="'+$this.attr(element_column_attr[i])+'" ';
                }
            }

			jQuery(this).replaceWith('[blox_column_inner width="'+size+'" '+attr_data+']'+jQuery(this).find('> .blox_column_content').html()+'[/blox_column_inner]');
		});
	}
	else{
		$content.find('.blox_column').each(function(){
			var size = '1/1';
			$this = jQuery(this);
			
			if( $this.hasClass('span6') ){
				size = '1/2';
			}else if( $this.hasClass('span4') ){
				size = '1/3';
			}else if( $this.hasClass('span8') ){
				size = '2/3';
			}else if( $this.hasClass('span2') ){
				size = '1/6';
			}else if( $this.hasClass('span3') ){
				size = '1/4';
			}else if( $this.hasClass('span9') ){
				size = '3/4';
			}

            var attr_data = '';
            for(var i=0; i<element_column_attr.length; i++){
                if( typeof $this.attr(element_column_attr[i])!=='undefined' && $this.attr(element_column_attr[i])!='' ){
                    attr_data += element_column_attr[i]+'="'+$this.attr(element_column_attr[i])+'" ';
                }
            }

			jQuery(this).replaceWith('[blox_column width="'+size+'" '+attr_data+']'+jQuery(this).find('> .blox_column_content').html()+'[/blox_column]');
		});
	}
}
function revert_shortcode_column($content){
	revert_shortcode_column_hook($content);
	revert_shortcode_column_hook($content, true);
	return $content;
}





function get_blox_element_row($content){
	return get_blox_row_el_html($content);
}


function add_event_blox_element_row(){
	
	jQuery('.blox_controls_row').each(function(){
  		var $context = jQuery(this).parent();
  		if( $context.attr('columns') ){
  			jQuery(this).find('.split_row').each(function(){
  				if( jQuery(this).attr('title') && jQuery(this).attr('title')==$context.attr('columns') ){
  					jQuery(this).addClass('active');
  				}
  			});
  		}
  		jQuery(this).find('.split_row').unbind('click')
  			.click(function(){
  				
  				jQuery(this).parent().find('.split_row').removeClass('active');
  				jQuery(this).addClass('active');
  				
  				var size_title = jQuery(this).attr('title');
  				var size_arr = size_title.split('+');
  				var $row = jQuery(this).parent().parent();
  				var $tmp_array = new Array();

  				$context.attr('columns', size_title);
  				
  				$row.find('> .blox_row_content > .blox_column').each(function(index){
  					$col = jQuery(this);
  					$tmp_array[index] = jQuery(this);
  				});
  				
  				$row.find('> .blox_row_content').html('');
  				for(i=0; i<size_arr.length; i++){
  					html = '';
  					if( i<$tmp_array.length ){
  						html = get_blox_column_html({ width:split2class(size_arr[i]) }, $tmp_array[i].find('.blox_column_content').html());
  					}
  					else{
  						html = get_blox_column_html({ width:split2class(size_arr[i]) }, '');
  					}
  					$row.find('> .blox_row_content').append(html);
  				}
  				if( $tmp_array.length > size_arr.length  ){
  					for(i=size_arr.length; i<$tmp_array.length; i++){
  						$row.find('> .blox_row_content > .blox_column').eq(size_arr.length-1).find('.blox_column_content').append($tmp_array[i].find('.blox_column_content').html());
  					}
  				}
  				addEventsBloxLayout();
  			});
  			
		jQuery(this).find('.action_remove').unbind('click')
			.click(function(){
				$context.remove();
			});
		
		jQuery(this).find('.action_clone').unbind('click')
			.click(function(){
				$context.after( $context.clone() );
				addEventsBloxLayout();
			});
			
		jQuery(this).find('.action_edit').unbind('click')
			.click(function(){
				
				var form_element = [
							{
                				type: 'checkbox_flat',
                				id: 'blox_row_attr_fullwidth',
                				label: 'Enable Fullwidth',
                				value: ( typeof $context.attr('fullwidth')!=='undefined' && $context.attr('fullwidth')=='true' ? '1' : '0' )
                			},
                			{
                				type: 'colorpicker',
                				id: 'blox_row_attr_color',
                				label: 'Background Color',
                				value: $context.attr('color')
                			},
                			{
                				type: 'image',
                				id: 'blox_row_attr_image',
                				label: 'Background Image',
                				value: $context.attr('image')

                			},
                			{
                				type: 'select',
                				id: 'blox_row_attr_bgrepeat',
                				label: 'Background Repeat',
                				value: $context.attr('bg_repeat'),
                				options: [
                					{ value: 'no-repeat', label: 'No Repeat' },
                					{ value: 'repeat', label: 'Repeat' },
                					{ value: 'repeat-x', label: 'Repeat-X' },
                					{ value: 'repeat-y', label: 'Repeat-Y' },
                					{ value: 'cover', label: 'No Repeat/Image Cover' }
                				]
                			},
                			{
                				type: 'select',
                				id: 'blox_row_attr_bgposition',
                				label: 'Background Position',
                				value: $context.attr('bg_position'),
                				options: [
                					{ value: 'left top', label: 'Left Top' },
                					{ value: 'left center', label: 'Left Center' },
                					{ value: 'left bottom', label: 'Left Bottom' },
                					{ value: 'center top', label: 'Center Top' },
                					{ value: 'center center', label: 'Center Center' },
									{ value: 'center bottom', label: 'Center Bottom' },
                					{ value: 'right top', label: 'Right Top' },
                					{ value: 'right center', label: 'Right Center' },
                					{ value: 'right bottom', label: 'Right Bottom' }
                				]
                			},
                			{
                				type: 'select',
                				id: 'blox_row_attr_attachment',
                				label: 'Background Attachment Type',
                				value: $context.attr('bg_attach'),
                				options: [
                					{ value: 'scroll', label: 'Scroll' },
                					{ value: 'fixed', label: 'Fixed' },
                					{ value: 'parallax', label: 'Parallax' }
                				],
                                description: 'Controls how your background image affect when you scroll up & down. If you select here <b>Parallax</b> value, background image moves beautiful motion.'
                			},
                			{
                				type: 'checkbox_flat',
                				id: 'row_text_light',
                				label: 'Row Text is Light?',
                				value: $context.attr('text_light')
                			},
                			{
                				type: 'checkbox_flat',
                				id: 'section-video_active',
                				label: 'Background Video Active',
                				value: $context.attr('video_active')
                			},
                			{
				                type: 'video',
				                id: 'section-video_m4v',
				                label: 'Video M4V',
				                value: $context.attr('video_m4v'),
				                description: 'Webkit browsers support this format for HTML5 video'
				            },
				            {
				                type: 'video',
				                id: 'section-video_webm',
				                label: 'Video WEBM',
				                value: $context.attr('video_webm'),
				                description: 'Firefox browser support webm format for HTML5 video'
				            },
				            {
                				type: 'image',
                				id: 'blox_row_attr_poster',
                				label: 'Video Poster',
                				value: $context.attr('poster'),
                				description: 'It is a poster of background video and it appears when you visit from mobile devices. Background Video will not play on mobile devices.'

                			},
				            {
                				type: 'checkbox_flat',
                				id: 'row_nopadding',
                				label: 'No padding Columns',
                				value: $context.attr('no_padding')
                			},
                			{
                				type: 'colorpicker',
                				id: 'blox_row_overlay_color',
                				label: 'Overlay Color',
                				value: $context.attr('overlay_color')
                			},
                			{
                				type: 'text',
                				id: 'blox_row_overlay_opacity',
                				label: 'Overlay Opacity',
                				value: $context.attr('overlay_opacity'),
                				description: 'Overlay opacity value. Any float number between 0 and 1. Ex: [0.1, 0.7, 1]'
                			},
                			{
                				type: 'text',
                				id: 'blox_row_padding_top',
                				label: 'Row Padding Top',
                				value: $context.attr('padding_top'),
                                description: 'Padding top value. Include number value here and don\'t need PX metrics'
                			},
                			{
                				type: 'text',
                				id: 'blox_row_padding_bottom',
                				label: 'Row Padding Bottom',
                				value: $context.attr('padding_bottom'),
                                description: 'Padding bottom value. Include number value here and don\'t need PX metrics'
                			},
                			/*{
                				type: 'checkbox_flat',
                				id: 'row_affix',
                				label: 'Enable Row Affix',
                				value: $context.attr('affix')
                			},*/
                			{
                				type: 'input',
                				id: 'blox_row_extra_class',
                				label: 'Extra Class',
                				value: $context.attr('extra_class')
                			}
            			];

                show_blox_form('Edit Row Option', form_element, function($form){
                	$context.attr('fullwidth', (jQuery('#blox_row_attr_fullwidth').val() == 1 ? 'true' : '') );
                	$context.attr('color', jQuery('#blox_row_attr_color').val());
                	$context.attr('image', jQuery('#blox_row_attr_image').val());
                	$context.attr('text_light', jQuery('#row_text_light').val());

                	$context.attr('padding_top', jQuery('#blox_row_padding_top').val());
                	$context.attr('padding_bottom', jQuery('#blox_row_padding_bottom').val());

                	$context.attr('overlay_color', jQuery('#blox_row_overlay_color').val());
                	$context.attr('overlay_opacity', jQuery('#blox_row_overlay_opacity').val());
                	
                	$context.removeAttr('bg_repeat');
                	$context.removeAttr('bg_position');
                	$context.removeAttr('bg_attach');
                	$context.removeAttr('extra_class');

                	$context.removeAttr('video_active');
                	$context.removeAttr('video_m4v');
                	$context.removeAttr('video_webm');
                	$context.removeAttr('poster');

                	$context.removeAttr('no_padding');

                	if( jQuery('#blox_row_attr_image').val()!='' ){
                		$context.attr('bg_repeat', jQuery('#blox_row_attr_bgrepeat').val());
                		$context.attr('bg_position', jQuery('#blox_row_attr_bgposition').val());
                		$context.attr('bg_attach', jQuery('#blox_row_attr_attachment').val());
                	}

                	if( jQuery('#blox_row_extra_class').val()!='' ){
                		$context.attr('extra_class', jQuery('#blox_row_extra_class').val());
                	}

                	if( jQuery('#section-video_active').val() == '1' ){
                		$context.attr('video_active', jQuery('#section-video_active').val());
                		$context.attr('video_m4v', jQuery('#section-video_m4v').val());
                		$context.attr('video_webm', jQuery('#section-video_webm').val());
                		$context.attr('poster', jQuery('#blox_row_attr_poster').val());
                	}

                	if( jQuery('#row_nopadding').val() == '1' ){
                		$context.attr('no_padding', jQuery('#row_nopadding').val());
                	}

                	/* Affix row value */
                	if( !$context.parent().hasClass('blox_column_content') ){
						$context.attr('affix', '');
					}
					else{
						$context.attr('affix', jQuery('#row_affix').val());
					}

                },
                {
                	target: $context
                }
                );
				
				/* Affix */
				if( !$context.parent().hasClass('blox_column_content') ){
					jQuery('#row_affix').parent().parent().hide();
				}
				
				
				//Image field event
                jQuery('#blox_row_attr_image').change(function(){
                	if( this.value!='' ){
                		jQuery('#blox_row_attr_bgrepeat').parent().show();
		                jQuery('#blox_row_attr_bgposition').parent().show();
		                jQuery('#blox_row_attr_attachment').parent().show();
                	}
                	else{
                		jQuery('#blox_row_attr_bgrepeat').parent().hide();
		                jQuery('#blox_row_attr_bgposition').parent().hide();
		                jQuery('#blox_row_attr_attachment').parent().hide();
                	}
                });
                jQuery('#blox_row_attr_image').change();


                //Video active event
                jQuery('#section-video_active').change(function(){
                	var $this = jQuery(this);
                	if( this.value == '1' ){
						jQuery('#section-video_m4v').parent().show();
		                jQuery('#section-video_webm').parent().show();
		                jQuery('#blox_row_attr_poster').parent().show();
                	}
                	else{
						jQuery('#section-video_m4v').parent().hide();
		                jQuery('#section-video_webm').parent().hide();
		                jQuery('#blox_row_attr_poster').parent().hide();
                	}
                });

                //Row fullwidth event
                jQuery('#blox_row_attr_fullwidth').change(function(){
                	var $this = jQuery(this);
                	if( this.value == '1' ){
                		jQuery('#blox_row_attr_color').parent().parent().parent().show();
                		jQuery('#blox_row_attr_image').parent().show();
                		jQuery('#blox_row_attr_image').change();
                		jQuery('#section-video_active').parent().parent().show();
                		jQuery('#section-video_active').change();

                		jQuery('#row_text_light').parent().parent().show();

                		jQuery('#blox_row_overlay_color').parent().parent().parent().show();
                		jQuery('#blox_row_overlay_opacity').parent().show();
                	}
                	else{
                		jQuery('#blox_row_attr_color').parent().parent().parent().hide();
                		jQuery('#blox_row_attr_image').parent().hide();
                		jQuery('#blox_row_attr_bgrepeat').parent().hide();
		                jQuery('#blox_row_attr_bgposition').parent().hide();
		                jQuery('#blox_row_attr_attachment').parent().hide();
		                jQuery('#row_text_light').parent().parent().hide();
		                
		                jQuery('#section-video_active').parent().parent().hide();
		                jQuery('#section-video_m4v').parent().hide();
		                jQuery('#section-video_webm').parent().hide();
		                jQuery('#blox_row_attr_poster').parent().hide();

		                jQuery('#blox_row_overlay_color').parent().parent().parent().hide();
                		jQuery('#blox_row_overlay_opacity').parent().hide();
                	}
                });

				jQuery('#blox_row_attr_fullwidth').change();
                
			});
  	});
  	
  	
  	jQuery('.blox_columns_action .blox_column_action_add').unbind('click')
  		.click(function(){
  			$context = jQuery(this).parent().parent().find('> .blox_column_content');
  			add_blox_element($context, jQuery(this).attr('data-rel'));
  		});


    jQuery('.blox_columns_action .blox_column_action_edit').unbind('click')
        .click(function(){

            var $column = jQuery(this).parent().parent();
            
            var form_element = [
                            {
                                type: 'colorpicker',
                                id: 'blox_col_bgcolor',
                                label: 'Background Color',
                                value: $column.attr('bgcolor')
                            },
                            {
                                type: 'select',
                                id: 'blox_col_text_color',
                                label: 'Text Color',
                                value: $column.attr('text_color'),
                                options: [
                                    { value: 'default-text', label: 'Default' },
                                    { value: 'light', label: 'Light Text' },
                                    { value: 'dark', label: 'Dark Text' }
                                ],
                                description: 'Column\'s content text color.'
                            },
                            {
                                type: 'image',
                                id: 'blox_col_bgimage',
                                label: 'Background Image',
                                value: $column.attr('bgimage')
                            },
                            {
                                type: 'select',
                                id: 'blox_col_bgrepeat',
                                label: 'Background Repeat',
                                value: $column.attr('bg_repeat'),
                                options: [
                                    { value: 'no-repeat', label: 'No Repeat' },
                                    { value: 'repeat', label: 'Repeat' },
                                    { value: 'repeat-x', label: 'Repeat-X' },
                                    { value: 'repeat-y', label: 'Repeat-Y' },
                                    { value: 'cover', label: 'No Repeat/Image Cover' }
                                ]
                            },
                            {
                                type: 'select',
                                id: 'blox_col_bgposition',
                                label: 'Background Position',
                                value: $column.attr('bg_position'),
                                options: [
                                    { value: 'left top', label: 'Left Top' },
                                    { value: 'left center', label: 'Left Center' },
                                    { value: 'left bottom', label: 'Left Bottom' },
                                    { value: 'center top', label: 'Center Top' },
                                    { value: 'center center', label: 'Center Center' },
                                    { value: 'center bottom', label: 'Center Bottom' },
                                    { value: 'right top', label: 'Right Top' },
                                    { value: 'right center', label: 'Right Center' },
                                    { value: 'right bottom', label: 'Right Bottom' }
                                ]
                            },
                            {
                                type: 'select',
                                id: 'blox_col_bgattachment',
                                label: 'Background Attachment Type',
                                value: $column.attr('bg_attach'),
                                options: [
                                    { value: 'scroll', label: 'Scroll' },
                                    { value: 'fixed', label: 'Fixed' }
                                ],
                                description: 'Controls how your background image affect when you scroll up & down. If you select here <b>Parallax</b> value, background image moves beautiful motion.'
                            },
                            {
                                type: 'text',
                                id: 'blox_col_extra_class',
                                label: 'Extra Class',
                                value: $column.attr('extra_class'),
                                description: 'Additional Extra Classes'
                            },
                            {
                                type: 'text',
                                id: 'blox_col_padding_top',
                                label: 'Padding Top',
                                value: $column.attr('padding_top'),
                                description: 'Enter number not with px'
                            },
                            {
                                type: 'text',
                                id: 'blox_col_padding_bottom',
                                label: 'Padding Bottom',
                                value: $column.attr('padding_bottom'),
                                description: 'Enter number not with px'
                            },
                            {
                                type: 'text',
                                id: 'blox_col_padding_left',
                                label: 'Padding Left',
                                value: $column.attr('padding_left'),
                                description: 'Enter number not with px'
                            },
                            {
                                type: 'text',
                                id: 'blox_col_padding_right',
                                label: 'Padding Right',
                                value: $column.attr('padding_right'),
                                description: 'Enter number not with px'
                            }
                        ];

                show_blox_form('Edit Column Option', form_element, function($form){
                    $column.attr('bgcolor', jQuery('#blox_col_bgcolor').val());
                    $column.attr('text_color', jQuery('#blox_col_text_color').val());
                    $column.attr('bgimage', jQuery('#blox_col_bgimage').val());
                    $column.attr('bg_repeat', jQuery('#blox_col_bgrepeat').val());
                    $column.attr('bg_position', jQuery('#blox_col_bgposition').val());
                    $column.attr('bg_attach', jQuery('#blox_col_bgattachment').val());
                    $column.attr('extra_class', jQuery('#blox_col_extra_class').val());

                    $column.attr('padding_top', jQuery('#blox_col_padding_top').val());
                    $column.attr('padding_bottom', jQuery('#blox_col_padding_bottom').val());
                    $column.attr('padding_left', jQuery('#blox_col_padding_left').val());
                    $column.attr('padding_right', jQuery('#blox_col_padding_right').val());
                },
                {
                    target: $column
                }
                );
                

                var split_arrs = ['1/2+1/2', '1/3+2/3', '2/3+1/3', '1/4+3/4', '3/4+1/4'];
                var $active_spliter = $column.parent().parent().find('>.blox_controls_row > a.active');
                console.log( $active_spliter, $active_spliter.attr('title') );
                if( $active_spliter.length>0 && typeof $active_spliter.attr('title')!=='undefined' && split_arrs.indexOf($active_spliter.attr('title'))>-1 ){

                }
                else{
                    jQuery('#blox_col_bgcolor').parent().parent().parent().hide();
                    jQuery('#blox_col_text_color').parent().hide();
                    jQuery('#blox_col_bgimage').parent().hide();
                    jQuery('#blox_col_bgimage').val('');

                    jQuery('#blox_col_bgrepeat').parent().hide();
                    jQuery('#blox_col_bgposition').parent().hide();
                    jQuery('#blox_col_bgattachment').parent().hide();
                }


                jQuery('#blox_col_bgimage').change(function(){
                    if( this.value!='' ){
                        jQuery('#blox_col_bgrepeat').parent().show();
                        jQuery('#blox_col_bgposition').parent().show();
                        jQuery('#blox_col_bgattachment').parent().show();
                    }
                    else{
                        jQuery('#blox_col_bgrepeat').parent().hide();
                        jQuery('#blox_col_bgposition').parent().hide();
                        jQuery('#blox_col_bgattachment').parent().hide();
                    }
                });
                jQuery('#blox_col_bgimage').change();


        });

        
	
}

