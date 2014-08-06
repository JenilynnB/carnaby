

function get_blox_element_toggle($content, $attrs, $title){
    
    return '<div class="blox_item blox_toggle" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-plus-square-o"></i> \
                    <span class="blox_toggle_title">Toggle:</span> \
                    <small>'+($title!=undefined ? $title : '')+'</small> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content">'+($content!=undefined ? $content : '')+'</div> \
            </div>';
}


function parse_shortcode_toggle($content){
    $content = wp.shortcode.replace( 'blox_toggle', $content, function(data){
        var attrs = '';
        var title = 'Toggle Element';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                if( key=='title' ){
                    attrs += ' title="'+value+'"';
                    title = value;
                }
                if( key=='toggle_state' ){
                    attrs += ' toggle_state="'+value+'"';
                }
                if( key=='animation' ){
                    attrs += ' animation="'+value+'"';
                }
                if( key=='extra_class' ){
                    attrs += ' extra_class="'+value+'"';
                }
            }
        })
        return get_blox_element_toggle(data.content, attrs, title);
    });
    return $content;
}

function revert_shortcode_toggle($content){
    $content.find('.blox_toggle').each(function(){
        attr = '';
        var temp_val = '';

        temp_val = jQuery(this).attr('title')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' title="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('toggle_state')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' toggle_state="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('animation')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' animation="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('extra_class')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' extra_class="'+temp_val+'"';
        }
		
        jQuery(this).replaceWith('[blox_toggle'+attr+']'+jQuery(this).find('.blox_item_content').html()+'[/blox_toggle]');
    });
    return $content;
}


function add_event_blox_element_toggle(){
	
    jQuery('.blox_toggle').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_title').unbind('click')
        .click(function(){
            $this.find('.blox_item_content').toggle();
        });
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){
            var $item = $this;

            var form_element = [
                        {
                            type: 'input',
                            id: 'blox_el_option_title',
                            label: 'Toggle Title',
                            value: $item.attr('title')
                        },
                        {
                            type: 'editor',
                            id: 'blox_option_editor',
                            label: 'Content',
                            value: $item.find('.blox_item_content').html()
                        },
                        {
                            type: 'checkbox_flat',
                            id: 'blox_el_option_state',
                            label: 'Toggle Opened or Closed State',
                            value: $item.attr('toggle_state')
                        }
                    ];

            show_blox_form('Edit Toggle Element', form_element, function($form){
                $item.find('.blox_toggle_title').html( jQuery('#blox_el_option_title').val() );
                $item.attr('title', jQuery('#blox_el_option_title').val());
                $item.find('.blox_item_content').html(get_content_tinymce());
                $item.attr('toggle_state', jQuery('#blox_el_option_state').val());
            },
            {
                target: $this,
                extra_field: true
            });
            return false;
        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_toggle();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
