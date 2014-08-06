
var carousel_attrs = ['title', 'post_type', 'category', 'count', 'item_style', 'overlay', 'animation', 'extra_class', 'skin', 'visibility'];


function get_blox_element_carousel($content, $attrs){
	
    return '<div class="blox_item blox_carousel" '+($attrs!=undefined ? $attrs : '')+'> \
            <div class="blox_item_title"> \
                <i class="fa-exchange"></i> \
                <span class="blox_item_title">Carousel</span> \
                <small></small> \
                '+ get_blox_actions() +' \
            </div> \
            <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
    </div>';
}


function parse_shortcode_carousel($content){
    $content = wp.shortcode.replace( 'blox_carousel', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        });
		
        return get_blox_element_carousel( data.content, attrs );
    });
    return $content;
}

function revert_shortcode_carousel($content){
    $content.find('.blox_carousel').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < carousel_attrs.length; i++) {
            temp_val = jQuery(this).attr(carousel_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ carousel_attrs[i] +'="'+ temp_val +'"';
            }
        }
        
        jQuery(this).replaceWith('[blox_carousel'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_carousel]');
    });
    return $content;
}


function add_event_blox_element_carousel(){
	
    jQuery('.blox_carousel').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var ajax_params = {
                'action': 'get_blox_element_carousel', 
                'title': $this.attr('title'),
                'post_type': $this.attr('post_type'),
                'category': $this.attr('category'),
                'count': $this.attr('count'),
                'item_style': $this.attr('item_style'),
                'overlay': $this.attr('overlay')
            };
            show_blox_form_ajax("Edit Carousel Element", ajax_params, function(){
                $this.attr('title', jQuery('#blox_el_option_title').val() );
                $this.attr('post_type', jQuery('#blox_option_post_type').val() );
                $this.attr('category', jQuery('#blox_option_taxonomy_'+jQuery('#blox_option_post_type').val()).val() );
                $this.attr('count', jQuery('#blox_option_posts_count').val() );
                $this.attr('item_style', jQuery('#blox_carousel_item_style').val() );
                $this.attr('overlay', jQuery('#blox_carousel_overlay').val() );
            },
            {
                target: $this,
                extra_field: true,
                skin: true,
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
            add_event_blox_element_carousel();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
