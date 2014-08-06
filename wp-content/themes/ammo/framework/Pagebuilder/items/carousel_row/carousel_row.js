
var carousel_row_attrs = ['post_type', 'category', 'count', 'ratio', 'extra_class', 'visibility'];


function get_blox_element_carousel_row($content, $attrs){
	
    return '<div class="blox_item carousel_fullwidth type-fullwidth" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-exchange"></i> \
                    <span class="blox_item_title">Carousel Fullwidth</span> \
                    <small></small> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
            </div>';
}


function parse_shortcode_carousel_row($content){
    $content = wp.shortcode.replace( 'carousel_fullwidth', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        });
		
        return get_blox_element_carousel_row( data.content, attrs );
    });
    return $content;
}

function revert_shortcode_carousel_row($content){
    $content.find('.carousel_fullwidth').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < carousel_row_attrs.length; i++) {
            temp_val = jQuery(this).attr(carousel_row_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ carousel_row_attrs[i] +'="'+ temp_val +'"';
            }
        }
        
        jQuery(this).replaceWith('[carousel_fullwidth'+attr+']');
    });
    return $content;
}


function add_event_blox_element_carousel_row(){
	
    jQuery('.carousel_fullwidth').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var ajax_params = {
                'action': 'get_blox_element_carouselrow', 
                'post_type': $this.attr('post_type'),
                'category': $this.attr('category'),
                'count': $this.attr('count'),
                'extra_class': $this.attr('extra_class')
            };
            show_blox_form_ajax("Edit Carousel Element", ajax_params, function(){
                $this.attr('post_type', jQuery('#blox_option_post_type').val() );
                $this.attr('category', jQuery('#blox_option_taxonomy_'+jQuery('#blox_option_post_type').val()).val() );
                $this.attr('count', jQuery('#blox_option_posts_count').val() );
                $this.attr('ratio', jQuery('#blox_element_ratio').val() );
                $this.attr('extra_class', jQuery('#blox_option_extra_class').val() );
            },
            {
                target: $this,
                visibility: true,
                ajax_handler: function(){
                    jQuery('#blox_option_post_type').change(function(){
                        jQuery('.blox_option_taxonomies').hide();
                        jQuery('#blox_option_taxonomy_'+jQuery(this).val()).show();
                    });

                    jQuery('#blox_popup_window').find('.select_data_val').each(function(){
                        var $val = jQuery(this).attr('data_val');
                        jQuery(this).val($val).change();
                    });

                    jQuery('#blox_option_taxonomy_'+jQuery('#blox_option_post_type').val()).val(jQuery('#blox_option_post_type').attr('data_cat')).change();
                }
            });
        		
        });
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_carousel_row();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
