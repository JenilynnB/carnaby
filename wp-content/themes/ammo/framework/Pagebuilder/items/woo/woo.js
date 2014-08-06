
function get_blox_element_woo($content, $attrs){

    return '<div class="blox_item blox_woo" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-shopping-cart"></i> \
                    <span class="blox_item_title">WooCommmerce</span> \
                    <small></small> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content" style="display: none;">'
                + '<div class="blox_item_woo_title">'+($content!=undefined && $content.find('.title').length>0 ? $content.find('.title').html() : '')+'</div>'
                + '<div class="blox_item_woo_type">'+($content!=undefined && $content.find('.type').length>0 ? $content.find('.type').html() : 'recent_products')+'</div>'
                + '<div class="blox_item_woo_perpage">'+($content!=undefined && $content.find('.per_page').length>0 ? $content.find('.per_page').html() : '12')+'</div>'
                + '<div class="blox_item_woo_columns">'+($content!=undefined && $content.find('.columns').length>0 ? $content.find('.columns').html() : '4')+'</div>'
                + '<div class="blox_item_woo_orderby">'+($content!=undefined && $content.find('.orderby').length>0 ? $content.find('.orderby').html() : '0')+'</div>'
                + '<div class="blox_item_woo_extra_class">'+($content!=undefined && $content.find('.extra_class').length>0 ? $content.find('.extra_class').html() : '')+'</div>'
                + '<div class="blox_item_woo_skin">'+($content!=undefined && $content.find('.skin').length>0 ? $content.find('.skin').html() : '')+'</div>'
                +'</div> \
			</div>';
}


function parse_shortcode_woo($content){
    $content = wp.shortcode.replace( 'blox_woo', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){                                
                if( key=='title' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='type' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='per_page' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='columns' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='orderby' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='extra_class' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='skin' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
            }
        });		
        return get_blox_element_woo(jQuery('<div class="tmp_pass">'+attrs+'</div>'));
    });
    return $content;
}

function revert_shortcode_woo($content){
    $content.find('.blox_woo').each(function(){
        attr = '';
        var temp_val = '';
         
        temp_val = jQuery(this).find('.blox_item_woo_title').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' title="'+temp_val+'"';
        }
        temp_val = jQuery(this).find('.blox_item_woo_type').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' type="'+temp_val+'"';
        }

        temp_val = jQuery(this).find('.blox_item_woo_perpage').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' per_page="'+temp_val+'"';
        }
                
        temp_val = jQuery(this).find('.blox_item_woo_columns').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            var_social = temp_val;
            var_social = var_social.replace(/\n/g, ',');
            attr += ' columns="'+var_social+'"';
        }
                
        temp_val = jQuery(this).find('.blox_item_woo_orderby').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            var_social = temp_val;
            var_social = var_social.replace(/\n/g, ',');
            attr += ' orderby="'+var_social+'"';
        }

        temp_val = jQuery(this).find('.blox_item_woo_extra_class').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' extra_class="'+temp_val+'"';
        }

        temp_val = jQuery(this).find('.blox_item_woo_skin').html()+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' skin="'+temp_val+'"';
        }
		
        jQuery(this).replaceWith('[blox_woo'+attr+']');
    });
    return $content;
}


function add_event_blox_element_woo(){
	
    jQuery('.blox_woo').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var form_element = [
            {
                type: 'input',
                id: 'blox_element_option_title',
                label: 'Title',
                value: $this.find('.blox_item_woo_title').html()
            },
            {
                type: 'select',
                id: 'blox_element_option_type',
                label: 'Query Type',
                value: $this.find('.blox_item_woo_type').html(),
                options: [
                    {
                        value: 'recent_products',
                        label: 'Recent Products'
                    },
                    {
                        value: 'featured_products',
                        label: 'Featured Products'
                    }                                                         
                ]
            },
            {
                type: 'number',
                id: 'blox_element_perpage',
                label: 'Item Count',
                value: $this.find('.blox_item_woo_perpage').html()
            },
            {
                type: 'number',
                id: 'blox_element_option_columns',
                label: 'Column number',
                value: $this.find('.blox_item_woo_columns').html()
            },
            {
                type: 'checkbox_flat',
                id: 'blox_element_option_orderby',
                label: 'Order By Date',
                value: $this.find('.blox_item_woo_orderby').html()
            },
            {
                type: 'input',
                id: 'blox_element_extra_class',
                label: 'Extra Class',
                value: $this.find('.blox_item_woo_extra_class').html()
            }
            ];

            $this.attr('skin', $this.find('.blox_item_woo_skin').html());

            show_blox_form('Edit WooCommerce Element', form_element, function($form){
                $this.find('.blox_item_woo_title').html(  jQuery('#blox_element_option_title').val() );
                $this.find('.blox_item_woo_type').html(  jQuery('#blox_element_option_type').val() );
                $this.find('.blox_item_woo_perpage').html(  jQuery('#blox_element_perpage').val() );
                $this.find('.blox_item_woo_columns').html(   jQuery('#blox_element_option_columns').val() );
                $this.find('.blox_item_woo_extra_class').html( jQuery('#blox_element_extra_class').val() );
                $this.find('.blox_item_woo_orderby').html( jQuery('#blox_element_option_orderby').val() );
                $this.find('.blox_item_woo_skin').html( $this.attr('skin') );
            },
            {
                target: $this,
                skin: true
            });

        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_woo();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
