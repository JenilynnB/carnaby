
function get_blox_element_testimonial_item($attrs, $content){
    $actions_top = '<div class="blox_action_buttons clearfix"> \
                        <a href="javascript:;" class="ac_edit_current"><i class="fa-pencil"></i></a> \
                        <a href="javascript:;" class="ac_clone_current"><i class="fa-copy"></i></a> \
                        <a href="javascript:;" class="ac_remove_current"><i class="fa-times"></i></a> \
                </div>';

    $new_content = '<div class="blox_testimonial_item group" '+($attrs!=undefined ? $attrs : '')+'>';
    $new_content += '<h3>Testimonial item</h3>';
    $new_content += '<div class="blox_accordion_content">'+$actions_top+'<div class="blox_testimonial_content blox_text_wrapper">'+($content!=undefined ? $content : '')+'</div></div>';
    $new_content += '</div>';
    return $new_content;
}

function get_blox_element_testimonial($content, $attrs){
    actions = '<div class="blox_item_actions blox_ac_general_action"> \
                        <a href="javascript:;" class="fa-pencil action_edit"></a> \
                        <a href="javascript:;" class="fa-copy action_clone"></a> \
                        <a href="javascript:;" class="fa-times action_remove"></a> \
                </div>';
    return '<div class="blox_item blox_testimonial" '+($attrs!=undefined ? $attrs : '')+'>'
    + actions
    + '<div class="blox_accordion_obj">'
    + ($content!=undefined ? $content : get_blox_element_testimonial_item())
    + '</div> \
                <div class="blox_accordion_bottom_tb"> \
                        <a href="javascript:;" class="blox_ac_item_add"><i class="fa-plus"></i> Add Testimonial Item</a> \
                </div> \
        </div>';
}

function parse_shortcode_testimonial($content){
    $content = wp.shortcode.replace( 'blox_testimonial', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            attrs += key+'="'+value+'" ';
        })
        return get_blox_element_testimonial(data.content, attrs);
    });

    $content = wp.shortcode.replace( 'blox_testimonial_item', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            attrs += key+'="'+value+'" ';
        })
        return get_blox_element_testimonial_item(attrs, data.content);
    });
    return $content;
}

function revert_shortcode_testimonial($content){
    $content.find('.blox_testimonial_item').each(function(){
        attr = '';
        var temp_val = '';

        temp_val = jQuery(this).attr('author')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' author="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('position')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' position="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('company')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' company="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('image')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' image="'+temp_val+'"';
        }

        jQuery(this).replaceWith('[blox_testimonial_item'+attr+']'+jQuery(this).find('.blox_testimonial_content').html()+'[/blox_testimonial_item]');
    });
    $content.find('.blox_testimonial').each(function(){
        jQuery(this).find('.blox_item_actions').remove();
        attr = '';
        var temp_val = '';

        temp_val = jQuery(this).attr('title')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' title="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('type')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' type="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('color')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' color="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('animation')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' animation="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('extra_class')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' extra_class="'+temp_val+'"';
        }

        jQuery(this).replaceWith('[blox_testimonial'+attr+']'+jQuery(this).find('.blox_accordion_obj').html()+'[/blox_testimonial]');
    });
    return $content;
}

function add_event_blox_element_testimonial(){
    jQuery('.blox_testimonial').each(function(){
        var $this = jQuery(this);
        $this.find('.blox_accordion_obj')
        .accordion({
            header: "> div > h3",
            heightStyle: "content"
        })
        .sortable({
            axis: "y",
            handle: "h3",
            stop: function( event, ui ) {
                ui.item.children( "h3" ).triggerHandler( "focusout" );
            }
        });

        $this.find('.blox_ac_item_add').unbind('click')
        .click(function(){
            $this.find('.blox_accordion_obj').accordion('destroy').append(get_blox_element_testimonial_item());
            add_event_blox_element_testimonial();
        });

        // general actions
        $this.find('.blox_ac_general_action .action_edit').unbind('click')
        .click(function(){
            var $current_ac = jQuery(this).parent().parent();

            var form_element = [
            {
                type: 'input',
                id: 'blox_el_title',
                label: 'Widget Title',
                value: $this.attr('title')

            },
            {
                type: 'select',
                id: 'blox_el_style',
                label: 'Style',
                value: $this.attr('type'),
                options: [
                    { value: 'single_color', label: 'Regular Testimonial Slider' },
                    { value: 'full_color', label: 'Big Testimonial Slider / Fits Perfect on Fullwidth Row' }
                ]
            }
            ];

            show_blox_form('Edit Testimonial', form_element, function($form){
                $this.attr('title', jQuery('#blox_el_title').val());
                $this.attr('type', jQuery('#blox_el_style').val());
                $this.attr('color', jQuery('#blox_el_color').val());
            },
            {
                target: $this,
                extra_field: true
            });

        });
        $this.find('.blox_ac_general_action .action_clone').unbind('click')
        .click(function(){
            $this.after( $this.clone() );
            refresh_blox_events();
        });
        $this.find('.blox_ac_general_action .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });


        $this.find('.ac_add_blox_elem').unbind('click')
        .click(function(){
            $context = jQuery(this).parent().parent().find('.blox_testimonial_content');
            add_blox_element($context, jQuery(this).attr('data-rel'));
        });

        $this.find('.ac_edit_current').unbind('click')
        .click(function(){
            $current_ac = jQuery(this).parent().parent().parent();


            var form_element = [
            {
                type: 'input',
                id: 'blox_el_author',
                label: 'Author',
                value: $current_ac.attr('author')

            },
            {
                type: 'textarea',
                id: 'blox_el_testimonial',
                label: 'Testimonial',
                value: $current_ac.find('.blox_testimonial_content').html()

            },
            {
                type: 'image',
                id: 'blox_el_image',
                label: 'Image',
                value: $current_ac.attr('image')

            },
            {
                type: 'input',
                id: 'blox_el_company',
                label: 'Company',
                value: $current_ac.attr('company')

            },
            {
                type: 'input',
                id: 'blox_el_position',
                label: 'Position',
                value: $current_ac.attr('position')

            }
            ];

            show_blox_form('Edit Testimonial Item', form_element, function($form){
                $current_ac.find('.blox_testimonial_content').html(jQuery('#blox_el_testimonial').val());
                $current_ac.attr('author', jQuery('#blox_el_author').val());
                $current_ac.attr('image', jQuery('#blox_el_image').val());
                $current_ac.attr('company', jQuery('#blox_el_company').val());
                $current_ac.attr('position', jQuery('#blox_el_position').val());
            });
            
        });

        $this.find('.ac_clone_current').unbind('click')
        .click(function(){
            $current_ac = jQuery(this).parent().parent().parent();
            $this.find('.blox_accordion_obj').accordion('destroy');
            $current_ac.after( $current_ac.clone() );
            add_event_blox_element_testimonial();
        });

        $this.find('.ac_remove_current').unbind('click')
        .click(function(){
            $current_ac = jQuery(this).parent().parent().parent();
            $current_ac.remove();
            $this.find('.blox_accordion_obj').accordion('destroy');
            add_event_blox_element_testimonial();
        });
    });
}
