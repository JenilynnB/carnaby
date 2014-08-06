
var gallery_attrs = ['title', 'images', 'layout', 'animation', 'extra_class', 'visibility'];

function get_blox_element_gallery($content, $attrs){
	
	return '<div class="blox_item blox_gallery" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-picture-o"></i> \
					<span class="blox_item_title">Gallery / Image Slider</span> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
			</div>';
}


function parse_shortcode_gallery($content){
	$content = wp.shortcode.replace( 'blox_gallery', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( value!=undefined && value!='undefined' && value!='' ){
				attrs += key+'="'+ value +'" ';
			}
		})
		
		return get_blox_element_gallery(data.content, attrs);
	});
	return $content;
}

function revert_shortcode_gallery($content){
	$content.find('.blox_gallery').each(function(){
		attr = '';
		var temp_val = '';

		for (var i = 0; i < gallery_attrs.length; i++) {
            temp_val = jQuery(this).attr(gallery_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ gallery_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
		jQuery(this).replaceWith('[blox_gallery'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_gallery]');
	});
	return $content;
}


function add_event_blox_element_gallery(){
	
	jQuery('.blox_gallery').each(function(){
		var $this = jQuery(this);
		
		$this.find('.blox_item_actions .action_edit').unbind('click')
			.click(function(){

				var form_element = [
                			{
                				type: 'input',
                				id: 'gallery_title',
                				label: 'Title',
                				value: $this.attr('title')
                			},
                			{
                				type: 'gallery',
                				id: 'gallery_images',
                				label: 'Insert Gallery Images',
                				value: $this.attr('images')
                			},
                			{
                				type: 'select',
                				id: 'gallery_layout',
                				label: 'Slider Type',
                				value: $this.attr('layout'),
                				options: [
                					{ value: 'default', label: 'Default Slider' },
                					{ value: 'imac', label: 'iMac Frame Slider' },
                					{ value: 'laptop', label: 'Laptop Frame Slider' },
                					{ value: 'iphone', label: 'iPhone Frame Slider' }
                				]
                			}
            			];

                show_blox_form('Edit Gallery / Image slider', form_element, function($form){
                    $this.attr('title', jQuery('#gallery_title').val() );
	                $this.attr('images', jQuery('#gallery_images').val() );
	        		$this.attr('layout', jQuery('#gallery_layout').val() );

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
				add_event_blox_element_gallery();
			});
			
		$this.find('.blox_item_actions .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
	});
	
}
