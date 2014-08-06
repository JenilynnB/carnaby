
function get_blox_element_placeholder($content, $attrs){
    
    return '<div class="blox_item blox_placeholder" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-user"></i> \
					<span class="blox_item_title">Placeholder</span> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'
                + '<div class="blox_item_placeholder_icon">'+($content!=undefined && $content.find('.icon').length>0 ? $content.find('.icon').html() : '')+'</div>'
                + '<div class="blox_item_placeholder_size">'+($content!=undefined && $content.find('.size').length>0 ? $content.find('.size').html() : '')+'</div>'
                + '<div class="blox_item_placeholder_animation">'+($content!=undefined && $content.find('.animation').length>0 ? $content.find('.animation').html() : '')+'</div>'
                + '<div class="blox_item_placeholder_extra_class">'+($content!=undefined && $content.find('.extra_class').length>0 ? $content.find('.extra_class').html() : '')+'</div>'
                +'</div> \
			</div>';
}


function parse_shortcode_placeholder($content){
    $content = wp.shortcode.replace( 'blox_placeholder', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                if( key=='icon' ){
                    attrs += '<div class="icon">'+value+'</div>';
                }
                if( key=='size' ){
                    attrs += '<div class="size">'+value+'</div>';
                }
                if( key=='animation' ){
                    attrs += '<div class="animation">'+value+'</div>';
                }
                if( key=='extra_class' ){
                    attrs += '<div class="extra_class">'+value+'</div>';
                }
            }
        });		
        return get_blox_element_placeholder(jQuery('<div class="tmp_pass">'+attrs+'</div>'));
    });
    return $content;
}

function revert_shortcode_placeholder($content){
    $content.find('.blox_placeholder').each(function(){
        attr = '';
        var temp_val = '';

        temp_val = jQuery(this).find('.blox_item_placeholder_icon').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            var_social = temp_val;
            var_social = var_social.replace(/\n/g, ',');
            attr += ' icon="'+var_social+'"';
        }
                
        temp_val = jQuery(this).find('.blox_item_placeholder_size').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            var_social = temp_val;
            var_social = var_social.replace(/\n/g, ',');
            attr += ' size="'+var_social+'"';
        }
        
        temp_val = jQuery(this).find('.blox_item_placeholder_animation').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' animation="'+temp_val+'"';
        }

        temp_val = jQuery(this).find('.blox_item_placeholder_extra_class').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' extra_class="'+temp_val+'"';
        }
        jQuery(this).replaceWith('[blox_placeholder'+attr+']');
    });
    return $content;
}


function add_event_blox_element_placeholder(){
	
    jQuery('.blox_placeholder').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var form_element = [
            {
                type: 'icon',
                id: 'blox_element_option_icon',
                label: 'Icon',
                value: $this.find('.blox_item_placeholder_icon').html()

            },
            {
                type: 'number',
                id: 'blox_element_option_size',
                label: 'Place Height',
                std: 250,
                value: $this.find('.blox_item_placeholder_size').html()
            }
            ];

            $this.attr('animation', $this.find('.blox_item_placeholder_animation').html());
            $this.attr('extra_class', $this.find('.blox_item_placeholder_extra_class').html());

            show_blox_form('Edit Placeholder', form_element, function($form){
                $this.find('.blox_item_placeholder_icon').html(   jQuery('#blox_element_option_icon').val() );
                $this.find('.blox_item_placeholder_size').html(   jQuery('#blox_element_option_size').val() );
                
                $this.find('.blox_item_placeholder_animation').html(  $this.attr('animation') );
                $this.find('.blox_item_placeholder_extra_class').html( $this.attr('extra_class') );
            },
            {
                target: $this,
                extra_field: true
            });

        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_placeholder();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}


