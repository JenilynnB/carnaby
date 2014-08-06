
var social_attrs = ['fbshare', 'tweet', 'gplus', 'pinterest', 'align', 'animation', 'extra_class', 'visibility'];

function get_blox_element_social($content, $attrs){
	
	return '<div class="blox_item blox_social" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-share-square-o"></i> \
					<span class="blox_item_title">Share Buttons</span> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'+ ($content!=undefined ? $content : '') +'</div> \
			</div>';
}


function parse_shortcode_social($content){
	$content = wp.shortcode.replace( 'blox_social', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
			if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
		});		
		return get_blox_element_social(data.content, attrs);
	});
	return $content;
}

function revert_shortcode_social($content){
	$content.find('.blox_social').each(function(){
		attr = '';
		var temp_val = '';
                
		for (var i = 0; i < social_attrs.length; i++) {
            temp_val = jQuery(this).attr(social_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ social_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
		jQuery(this).replaceWith('[blox_social'+attr+'/]');
	});
	return $content;
}


function add_event_blox_element_social(){
	
	jQuery('.blox_social').each(function(){
		var $this = jQuery(this);
		
		$this.find('.blox_item_actions .action_edit').unbind('click')
			.click(function(){

				var form_element = [
                			{
                				type: 'checkbox_flat',
                				id: 'blox_element_option_fbshare',
                				label: 'Facebook Share',
                				value: $this.attr('fbshare')
                			},
                            {
                				type: 'checkbox_flat',
                				id: 'blox_element_option_tweet',
                				label: 'Tweet me button',
                				value: $this.attr('tweet')
                			},
                            {
                				type: 'checkbox_flat',
                				id: 'blox_element_option_gplus',
                				label: 'Google+',
                				value: $this.attr('gplus')
                			},
                            {
                				type: 'checkbox_flat',
                				id: 'blox_element_option_pinterest',
                				label: 'Pinterest',
                				value: $this.attr('pinterest')
                			},
                			{
                				type: 'select',
                				id: 'blox_element_option_align',
                				label: 'Align',
                				value: $this.attr('align'),
                				options: [
                						{ value: 'left', label: 'Left' },
                						{ value: 'right', label: 'Right' },
                						{ value: 'center', label: 'Center' }
        						]
                			}
            			];

                show_blox_form('Edit Social Buttons', form_element, function($form){
                    $this.attr('fbshare', jQuery('#blox_element_option_fbshare').val() );
                    $this.attr('tweet', jQuery('#blox_element_option_tweet').val() );
                	$this.attr('gplus', jQuery('#blox_element_option_gplus').val() );
                    $this.attr('pinterest', jQuery('#blox_element_option_pinterest').val() );
                    $this.attr('align', jQuery('#blox_element_option_align').val() );
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
				add_event_blox_element_social();
			});
			
		$this.find('.blox_item_actions .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
	});
	
}
