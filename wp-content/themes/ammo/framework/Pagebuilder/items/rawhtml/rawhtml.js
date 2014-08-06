function html_escape(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
function html_unescape(str) {
    return String(str).replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
}


var raw_attrs = ['title', 'animation', 'extra_class', 'visibility'];

function get_blox_element_rawhtml($content, $attrs){
	
	return '<div class="blox_item blox_rawhtml" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-square-o"></i> \
					<span class="blox_item_title">Raw HTML and JS Element:</span> \
					<small></small> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content">'+($content!=undefined ? $content : '')+'</div> \
			</div>';
}


function parse_shortcode_rawhtml($content){
	$content = wp.shortcode.replace( 'blox_raw', $content, function(data){
		var attrs = '';
		jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
		})
		return get_blox_element_rawhtml(data.content, attrs);
	});
	return $content;
}


function revert_shortcode_rawhtml($content){
	$content.find('.blox_rawhtml').each(function(){
		attr = '';
		var temp_val = '';

        for (var i = 0; i < raw_attrs.length; i++) {
            temp_val = jQuery(this).attr(text_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ raw_attrs[i] +'="'+ temp_val +'"';
            }
        }
        
        jQuery(this).replaceWith('[blox_raw'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_raw]');
	});
	return $content;
}


function add_event_blox_element_rawhtml(){
	
	jQuery('.blox_rawhtml').each(function(){
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
                				type: 'textarea',
                				id: 'blox_option_rawhtml',
                				label: 'Content',
                                value: html_unescape($this.find('.blox_item_content').html() )
                			}
            			];

                show_blox_form('Edit Raw HTML & JS Element', form_element, function($form){
                    $this.attr('title', jQuery('#blox_option_title').val());
                    $this.find('.blox_item_content').text( html_escape(jQuery('#blox_option_rawhtml').val()) );
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
				add_event_blox_element_rawhtml();
			});
			
		$this.find('.blox_item_actions .action_remove').unbind('click')
			.click(function(){
				$this.remove();
			});
	});
	
}
