
var image_attrs = ['image', 'img_width', 'alignment', 'link', 'target', 'animation', 'extra_class', 'visibility'];

function get_blox_element_image($content, $attrs){
	
	return '<div class="blox_item blox_image" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-picture-o"></i> \
					<span class="blox_item_title">Image</span> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
			</div>';
}


function parse_shortcode_image($content){
	$content = wp.shortcode.replace( 'blox_image', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
			}
		});		
		return get_blox_element_image( data.content, attrs );
	});
	return $content;
}

function revert_shortcode_image($content){
	$content.find('.blox_image').each(function(){
		attr = '';
		var temp_val = '';

		for (var i = 0; i < image_attrs.length; i++) {
            temp_val = jQuery(this).attr(image_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ image_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
		jQuery(this).replaceWith('[blox_image'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_image]');
	});
	return $content;
}


function add_event_blox_element_image(){
	
	jQuery('.blox_image').each(function(){
		var $this = jQuery(this);
		
		$this.find('.blox_item_actions .action_edit').unbind('click')
			.click(function(){

				var form_element = [
                			{
                				type: 'image',
                				id: 'blox_element_option_image',
                				label: 'Image',
                				value: $this.attr('image')
                			},
                			{
                				type: 'number',
                				id: 'blox_element_option_width',
                				label: 'Width',
                				std: 400,
                				value: $this.attr('img_width')
                			},
                			{
				                type: 'select',
				                id: 'blox_element_option_align',
				                label: 'Alignment',
				                value: $this.attr('alignment'),
				                options: [
				                    {value: 'left',label: 'Left'},
				                    {value: 'center',label: 'Center'},
				                    {value: 'right',label: 'Right'}
				                ]
				            },
                			{
                				type: 'input',
                				id: 'blox_element_option_link',
                				label: 'Link',
                				value: $this.attr('link')
                			},
                			{
                				type: 'select',
                				id: 'blox_element_option_target',
                				label: 'Link target',
                				value: $this.attr('target'),
                                options: [
            						{value: '_self',label: 'Same window'},
            						{value: '_blank',label: 'In a new tab'},
            						{value: 'lightbox',label: 'Lightbox'},
                                ]
                			}
                			];


                show_blox_form('Edit Image', form_element, function($form){
                    $this.attr('image', jQuery('#blox_element_option_image').val() );
                    $this.attr('img_width', jQuery('#blox_element_option_width').val() );
                    $this.attr('alignment', jQuery('#blox_element_option_align').val() );
                    $this.attr('link', jQuery('#blox_element_option_link').val() );
                    $this.attr('target',  jQuery('#blox_element_option_target').val() );
                },
                {
                	target: $this,
                	extra_field: true,
                    visibility: true
                });

			});
			
		
		$this.find('.blox_item_actions .action_clone').unbind('click')
			.click(function(){
				$this.after($this.clone());
				add_event_blox_element_image();
			});
			
		$this.find('.blox_item_actions .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
	});
	
}
