
var text_attrs = ['title', 'animation', 'extra_class', 'visibility'];

function get_blox_element_text($content, $attrs){
	
	return '<div class="blox_item blox_text" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-align-left"></i> \
					<span class="blox_item_title">Text Element:</span> \
					<small></small> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content">'+($content!=undefined ? $content : '')+'</div> \
			</div>';
}


function parse_shortcode_text($content){
	$content = wp.shortcode.replace( 'blox_text', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
		})
		return get_blox_element_text(data.content, attrs);
	});
	return $content;
}


function revert_shortcode_text($content){
	$content.find('.blox_text').each(function(){
		attr = '';
		var temp_val = '';

        for (var i = 0; i < text_attrs.length; i++) {
            temp_val = jQuery(this).attr(text_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ text_attrs[i] +'="'+ temp_val +'"';
            }
        }
        
        jQuery(this).replaceWith('[blox_text'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_text]');
	});
	return $content;
}


function add_event_blox_element_text(){
	
	jQuery('.blox_text').each(function(){
		var $this = jQuery(this);
		
		$this.find('.blox_item_actions .action_edit').unbind('click')
			.click(function(){

				var form_element = [
                			{
                				type: 'input',
                				id: 'blox_option_title',
                				label: 'Title',
                				value: $this.attr('title')
                			},
                			{
                				type: 'editor',
                				id: 'blox_option_editor',
                				label: 'Content',
                				value: $this.find('.blox_item_content').html()
                			}
            			];

                show_blox_form('Edit Text Element', form_element, function($form){
                    $this.attr('title', jQuery('#blox_option_title').val());
                    $this.find('.blox_item_content').html(get_content_tinymce());
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
				add_event_blox_element_text();
			});
			
		$this.find('.blox_item_actions .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
	});
	
}
