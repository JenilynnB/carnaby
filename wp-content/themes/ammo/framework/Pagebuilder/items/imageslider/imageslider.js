
function get_blox_element_imageslider($content, $attrs){
	
	return '<div class="blox_item blox_imageslider" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-picture-o"></i> \
					<span class="blox_item_title">Image Slider</span> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'
				+ '<div class="blox_item_imageslider_images">'+($content!=undefined && $content.find('.images').length>0 ? $content.find('.images').html() : '')+'</div>'
                                + '<div class="blox_item_imageslider_click">'+($content!=undefined && $content.find('.click').length>0 ? $content.find('.click').html() : '')+'</div>'
				+ '<div class="blox_item_imageslider_slide">'+($content!=undefined && $content.find('.slide').length>0 ? $content.find('.slide').html() : '')+'</div>'
				+ '<div class="blox_item_imageslider_style">'+($content!=undefined && $content.find('.style').length>0 ? $content.find('.style').html() : '')+'</div>'
                                + '<div class="blox_item_imageslider_thumb">'+($content!=undefined && $content.find('.thumb').length>0 ? $content.find('.thumb').html() : '')+'</div>'
                                + '<div class="blox_item_imageslider_arrow">'+($content!=undefined && $content.find('.arrow').length>0 ? $content.find('.arrow').html() : '')+'</div>'
                                + '<div class="blox_item_imageslider_link">'+($content!=undefined && $content.find('.link').length>0 ? $content.find('.link').html() : '')+'</div>'
                                + '<div class="blox_item_imageslider_size">'+($content!=undefined && $content.find('.size').length>0 ? $content.find('.size').html() : '')+'</div>'
				+ '<div class="blox_item_imageslider_extra_class">'+($content!=undefined && $content.find('.extra_class').length>0 ? $content.find('.extra_class').html() : '')+'</div>'
				+'</div> \
			</div>';
}


function parse_shortcode_imageslider($content){
	$content = wp.shortcode.replace( 'blox_imageslider', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( value!=undefined && value!='undefined' && value!='' ){
                            if( key=='arrow' ){attrs += '<div class="arrow">'+value+'</div>';}
                            if( key=='slide' ){attrs += '<div class="slide">'+value+'</div>';}
                            if( key=='style' ){attrs += '<div class="style">'+value+'</div>';}
                            if( key=='thumb' ){attrs += '<div class="thumb">'+value+'</div>';}
                            if( key=='size' ){attrs += '<div class="size">'+value+'</div>';}
                            if( key=='click' ){attrs += '<div class="click">'+value+'</div>';}
                            if( key=='images' ){attrs += '<div class="images">'+value+'</div>';}
                            if( key=='link' ){attrs += '<div class="link">'+value+'</div>';}
                            if( key=='extra_class' ){attrs += '<div class="extra_class">'+value+'</div>';}
			}
		})
		
		return get_blox_element_imageslider(jQuery('<div class="tmp_pass">'+attrs+'</div>'));
	});
	return $content;
}

function revert_shortcode_imageslider($content){
	$content.find('.blox_imageslider').each(function(){
		attr = '';
                if( jQuery(this).find('.blox_item_imageslider_images').html()!='undefined' && jQuery(this).find('.blox_item_imageslider_images').html()!='' ){
			attr += ' images="'+jQuery(this).find('.blox_item_imageslider_images').html()+'"';
		}
                if( jQuery(this).find('.blox_item_imageslider_link').html()!='undefined' && jQuery(this).find('.blox_item_imageslider_link').html()!='' ){
			attr += ' link="'+jQuery(this).find('.blox_item_imageslider_link').html()+'"';
		}
                if( jQuery(this).find('.blox_item_imageslider_thumb').html()!='undefined' && jQuery(this).find('.blox_item_imageslider_thumb').html()!='' ){
			attr += ' thumb="'+jQuery(this).find('.blox_item_imageslider_thumb').html()+'"';
		}
                if( jQuery(this).find('.blox_item_imageslider_arrow').html()!='undefined' && jQuery(this).find('.blox_item_imageslider_arrow').html()!='' ){
			attr += ' arrow="'+jQuery(this).find('.blox_item_imageslider_arrow').html()+'"';
		}
		if( jQuery(this).find('.blox_item_imageslider_size').html()!='undefined' && jQuery(this).find('.blox_item_imageslider_size').html()!='' ){
			attr += ' size="'+jQuery(this).find('.blox_item_imageslider_size').html()+'"';
		}
                if( jQuery(this).find('.blox_item_imageslider_click').html()!='undefined' && jQuery(this).find('.blox_item_imageslider_click').html()!='' ){
			attr += ' click="'+jQuery(this).find('.blox_item_imageslider_click').html()+'"';
		}
		if( jQuery(this).find('.blox_item_imageslider_slide').html()!='undefined' && jQuery(this).find('.blox_item_imageslider_slide').html()!='' ){
			attr += ' slide="'+jQuery(this).find('.blox_item_imageslider_slide').html()+'"';
		}
		if( jQuery(this).find('.blox_item_imageslider_style').html()!='undefined' && jQuery(this).find('.blox_item_imageslider_style').html()!='' ){
			attr += ' style="'+jQuery(this).find('.blox_item_imageslider_style').html()+'"';
		}
		if( jQuery(this).find('.blox_item_imageslider_extra_class').html()!='undefined' && jQuery(this).find('.blox_item_imageslider_extra_class').html()!='' ){
			attr += ' extra_class="'+jQuery(this).find('.blox_item_imageslider_extra_class').html()+'"';
		}
		
		jQuery(this).replaceWith('[blox_imageslider'+attr+']');
	});
	return $content;
}


function add_event_blox_element_imageslider(){
	jQuery('.blox_imageslider').each(function(){
		var $this = jQuery(this);
		
		$this.find('.blox_item_actions .action_edit').unbind('click')
			.click(function(){

				var form_element = [
                			{
                				type: 'gallery',
                				id: 'blox_element_option_images',
                				label: 'Slider images',
                				value: $this.find('.blox_item_imageslider_images').html()

                			},
                                        {
                				type: 'select',
                				id: 'blox_element_option_style',
                				label: 'Layout style',
                				value: $this.find('.blox_item_imageslider_style').html(),
                				options: [
                						{
                							value: 'style1',
                							label: 'Simple slider'
                						},
                						{
                							value: 'style2',
                							label: 'Macbook slider'
                						},
                						{
                							value: 'style3',
                							label: 'iPhone slider'
                						},
                						{
                							value: 'style4',
                							label: 'iPad slider'
                						}                                                                
                						]
                			},
                			{
                				type: 'checkbox',
                				id: 'blox_element_option_arrow',
                				label: 'Arrows',
                				value: $this.find('.blox_item_imageslider_arrow').html()

                			},
                			{
                				type: 'checkbox',
                				id: 'blox_element_option_thumb',
                				label: 'Thumbnail',
                				value: $this.find('.blox_item_imageslider_thumb').html()

                			},
                			{
                				type: 'select',
                				id: 'blox_element_option_slide',
                				label: 'Auto slide',
                				value: $this.find('.blox_item_imageslider_slide').html(),
                                                options: [
                						{value: '3',label: '3 seconds'},
                                                                {value: '4',label: '4 seconds'},
                                                                {value: '5',label: '5 seconds (Default)'},
                                                                {value: '6',label: '6 seconds'},
                                                                {value: '7',label: '7 seconds'},
                                                                {value: '8',label: '8 seconds'},
                                                                {value: '9',label: '9 seconds'},
                                                                {value: '10',label: '10 seconds'},
                                                                {value: '0',label: 'No auto slide'},
                                                                
                                                            ]

                			},
                                        {
                				type: 'input',
                				id: 'blox_element_option_size',
                				label: 'Height value (optional)',
                				value: $this.find('.blox_item_imageslider_size').html()
                			},
                			{
                				type: 'select',
                				id: 'blox_element_option_click',
                				label: 'Mouse click/tap event',
                				value: $this.find('.blox_item_imageslider_click').html(),
                				options: [
                						{
                							value: 'lightbox',
                							label: 'Lighbox'
                						},
                						{
                							value: 'nothing',
                							label: 'Nothing'
                						},
                						{
                							value: 'link',
                							label: 'Custom link'
                						}
                						]

                			},
                                        {
                				type: 'input',
                				id: 'blox_element_option_link',
                				label: 'Custom link when click on images (optional)',
                				value: $this.find('.blox_item_imageslider_link').html()
                			},
                			{
                				type: 'input',
                				id: 'blox_element_extra_class',
                				label: 'Extra Class',
                				value: $this.find('.blox_item_imageslider_extra_class').html()
                			}
                			];

                show_blox_form('Edit Image Slider', form_element, function($form){
                        $this.find('.blox_item_imageslider_images').html(   jQuery('#blox_element_option_images').val() );
                        $this.find('.blox_item_imageslider_link').html(   jQuery('#blox_element_option_link').val() );
                        $this.find('.blox_item_imageslider_arrow').html( jQuery('#blox_element_option_arrow').val() );
                	$this.find('.blox_item_imageslider_style').html(  jQuery('#blox_element_option_style').val() );
                        $this.find('.blox_item_imageslider_thumb').html(  jQuery('#blox_element_option_thumb').val() );
                	$this.find('.blox_item_imageslider_click').html(   jQuery('#blox_element_option_click').val() );
                        $this.find('.blox_item_imageslider_size').html(   jQuery('#blox_element_option_size').val() );
                        $this.find('.blox_item_imageslider_slide').html(  jQuery('#blox_element_option_slide').val() );
            		$this.find('.blox_item_imageslider_extra_class').html( jQuery('#blox_element_extra_class').val() );
                });

			});
			
		
		$this.find('.blox_item_actions .action_clone').unbind('click')
			.click(function(){
				$this.after($this.clone());
				add_event_blox_element_imageslider();
			});
			
		$this.find('.blox_item_actions .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
	});
       
}
