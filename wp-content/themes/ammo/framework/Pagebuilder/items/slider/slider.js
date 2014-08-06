

function get_blox_element_slider($content, $attrs){
    
    return '<div class="blox_item blox_slider" '+($attrs!=undefined ? $attrs : '')+'> \
                    <div class="blox_item_title"> \
                        <i class="fa-columns"></i> \
                        <span class="blox_item_title">Premium Slider</span> \
                        <small></small> \
                        '+ get_blox_actions() +' \
                    </div> \
                    <div class="blox_item_content">'+($content!=undefined ? $content : '')+'</div> \
            </div>';
}


function parse_shortcode_slider($content){
    $content = wp.shortcode.replace( 'blox_slider', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+value+'" ';
            }
        })
        return get_blox_element_slider(data.content, attrs);
    });
    return $content;
}



function revert_shortcode_slider($content){
    $content.find('.blox_slider').each(function(){
        attr = '';
        var temp_val = '';

        temp_val = jQuery(this).attr('title')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' title="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('slider')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' slider="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('extra_class')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' extra_class="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('visibility')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' visibility="'+temp_val+'"';
        }
		
        jQuery(this).replaceWith('[blox_slider'+attr+'/]');
    });
    return $content;
}


function add_event_blox_element_slider(){
	
    jQuery('.blox_slider').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){
            var $item = $this;

            var ajax_params = {
                'action':'get_blox_element_sliders', 
                'slider':$item.attr('slider'), 
                'extra_class':$item.attr('extra_class')
            };
            show_blox_form_ajax("Edit Sliders Element", ajax_params, function(){
                $item.find('.blox_item_content').html( jQuery(':selected', '#blox_elem_option_slider').text() );
                $item.attr('slider', jQuery('#blox_elem_option_slider').val());
                $item.attr('extra_class', jQuery('#blox_el_option_class').val());
            },
            {
                target: $item,
                ajax_handler: function(){},
                visibility: true
            });
            
        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_slider();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
