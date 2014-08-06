
var message_attrs = ['title', 'type', 'alignment', 'dismissable', 'animation', 'extra_class', 'visibility'];

function get_blox_element_notification($content, $attrs){
	
	return '<div class="blox_item blox_notification" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-square-o"></i> \
					<span class="blox_item_title">Message Box</span> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
			</div>';
}


function parse_shortcode_notification($content){
	$content = wp.shortcode.replace( 'blox_notification', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( value!=undefined && value!='undefined' && value!='' ){
				attrs += key+'="'+ value +'" ';
			}
		})
		
		return get_blox_element_notification( data.content, attrs );
	});
	return $content;
}

function revert_shortcode_notification($content){
	$content.find('.blox_notification').each(function(){
		attr = '';
		var temp_val = '';

		for (var i = 0; i < message_attrs.length; i++) {
            temp_val = jQuery(this).attr(message_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ message_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
		jQuery(this).replaceWith('[blox_notification'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_notification]');
	});
	return $content;
}


function add_event_blox_element_notification(){
	
	jQuery('.blox_notification').each(function(){
		var $this = jQuery(this);
		
		$this.find('.blox_item_actions .action_edit').unbind('click')
			.click(function(){

				var form_element = [
	                			{
	                				type: 'input',
	                				id: 'message_title',
	                				label: 'Title',
	                				value: $this.attr('title')
	                			},
	                			{
		                            type: 'editor',
		                            id: 'blox_option_editor',
		                            label: 'Content',
		                            value: $this.find('.blox_item_content').html()
		                        },
	                			{
		                            type: 'select',
		                            id: 'message_type',
		                            label: 'Message Box Style (Color)',
		                            value: $this.attr('type'),
		                            options:[
		                                { value: 'default', label: 'Default (White)' },
		                                { value: 'alert-success', label: 'Success (Orange)' },
		                                { value: 'alert-info', label: 'Info (Powder blue)' },
		                                { value: 'alert-warning', label: 'Warning (Yellow)' },
		                                { value: 'alert-danger', label: 'Danger (Red)' }
		                            ]
		                        },
		                        {
		                            type: 'select',
		                            id: 'text_align',
		                            label: 'Text Alignment',
		                            value: $this.attr('alignment'),
		                            options:[
		                                { value: 'left', label: 'Left' },
		                                { value: 'center', label: 'Center' },
		                                { value: 'right', label: 'Right' }
		                            ],
		                            description: 'Typically you can align your text with editor styles. But this option aligns both text and title.'
		                        },
		                        {
				                    type: 'checkbox_flat',
				                    id: 'message_dismissable',
				                    label: 'Dismissable (Have a close [x] botton at right top)',
				                    value: $this.attr('dismissable')
				                }
                			];

                show_blox_form('Edit Message Box', form_element, function($form){
                    $this.attr('title', jQuery('#message_title').val());
	                $this.attr('type', jQuery('#message_type').val());
	                $this.attr('alignment', jQuery('#text_align').val());
	                $this.attr('dismissable', jQuery('#message_dismissable').val());
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
				add_event_blox_element_notification();
			});
			
		$this.find('.blox_item_actions .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
	});
	
}