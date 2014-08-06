
var callout_attrs = ['title', 'alignment', 'skin', 'animation', 'extra_class', 'visibility'];

function get_blox_element_callout($content, $attrs){
    
    return '<div class="blox_item blox_callout" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-mail-forward"></i> \
                    <span class="blox_item_title">Callout:</span> \
                    <small></small> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
            </div>';
}


function parse_shortcode_callout($content){
    $content = wp.shortcode.replace( 'blox_callout', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        })
        return get_blox_element_callout(data.content, attrs);
    });
    return $content;
}


function revert_shortcode_callout($content){
    $content.find('.blox_callout').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < callout_attrs.length; i++) {
            temp_val = jQuery(this).attr(callout_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ callout_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
        jQuery(this).replaceWith('[blox_callout'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_callout]');
    });
	
    return $content;
}


function add_event_blox_element_callout(){
	
    jQuery('.blox_callout').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            
            var form_element = [
                        {
                            type: 'input',
                            id: 'blox_title',
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
                            id: 'text_align',
                            label: 'Text Alignment',
                            value: $this.attr('alignment'),
                            options:[
                                { value: 'left', label: 'Left' },
                                { value: 'center', label: 'Center' },
                                { value: 'right', label: 'Right' }
                            ]
                        }
                    ];

            show_blox_form('Edit Callout', form_element, function($form){
                $this.attr('title', jQuery('#blox_title').val());
                $this.attr('alignment', jQuery('#text_align').val());
                $this.find('.blox_item_content').html(get_content_tinymce());
            },
            {
                target: $this,
                extra_field: true,
                skin: true,
                visibility: true
            });
            
            
        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_callout();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
