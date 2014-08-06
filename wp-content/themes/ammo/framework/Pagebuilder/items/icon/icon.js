
function get_blox_element_icon($content, $attrs){
    
    return '<div class="blox_item blox_icon" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-user"></i> \
					<span class="blox_item_title">Icon</span> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'
                + '<div class="blox_item_icon_link">'+($content!=undefined && $content.find('.link').length>0 ? $content.find('.link').html() : '')+'</div>'
                + '<div class="blox_item_icon_target">'+($content!=undefined && $content.find('.target').length>0 ? $content.find('.target').html() : '')+'</div>'
                + '<div class="blox_item_icon_style">'+($content!=undefined && $content.find('.style').length>0 ? $content.find('.style').html() : '')+'</div>'
                + '<div class="blox_item_icon_color">'+($content!=undefined && $content.find('.color').length>0 ? $content.find('.color').html() : '')+'</div>'
                + '<div class="blox_item_icon_icon">'+($content!=undefined && $content.find('.icon').length>0 ? $content.find('.icon').html() : '')+'</div>'
                + '<div class="blox_item_icon_size">'+($content!=undefined && $content.find('.size').length>0 ? $content.find('.size').html() : '')+'</div>'
				+ '<div class="blox_item_icon_align">'+($content!=undefined && $content.find('.align').length>0 ? $content.find('.align').html() : '')+'</div>'
                + '<div class="blox_item_icon_animation">'+($content!=undefined && $content.find('.animation').length>0 ? $content.find('.animation').html() : '')+'</div>'
                + '<div class="blox_item_icon_extra_class">'+($content!=undefined && $content.find('.extra_class').length>0 ? $content.find('.extra_class').html() : '')+'</div>'
                +'</div> \
			</div>';
}


function parse_shortcode_icon($content){
    $content = wp.shortcode.replace( 'blox_icon', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                if( key=='target' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='link' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }                                
                if( key=='style' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='icon' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='color' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='size' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
				if( key=='align' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='animation' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
                if( key=='extra_class' ){
                    attrs += '<div class="'+key+'">'+value+'</div>';
                }
            }
        })		
        return get_blox_element_icon(jQuery('<div class="tmp_pass">'+attrs+'</div>'));
    });
    return $content;
}

function revert_shortcode_icon_hook($this){
    attr = '';
    var temp_val = '';

    temp_val = $this.find('.blox_item_icon_link').html()+'';
    if( temp_val!='undefined' && temp_val!='' ){
        attr += ' link="'+temp_val+'"';
    }

    temp_val = $this.find('.blox_item_icon_target').html()+'';
    if( temp_val!='undefined' && temp_val!='' ){
        attr += ' target="'+temp_val+'"';
    }

    temp_val = $this.find('.blox_item_icon_style').html()+'';
    if( temp_val!='undefined' && temp_val!='' ){
        attr += ' style="'+temp_val+'"';
    }

    temp_val = $this.find('.blox_item_icon_color').html()+'';
    if( temp_val!='undefined' && temp_val!='' ){
        attr += ' color="'+temp_val+'"';
    }
            
    temp_val = $this.find('.blox_item_icon_icon').html()+'';
    if( temp_val!='undefined' && temp_val!='' ){
        attr += ' icon="'+temp_val+'"';
    }
            
    temp_val = $this.find('.blox_item_icon_size').html()+'';
    if( temp_val!='undefined' && temp_val!='' ){
        attr += ' size="'+temp_val+'"';
    }
    temp_val = $this.find('.blox_item_icon_align').html()+'';
    if( temp_val!='undefined' && temp_val!='' ){
        attr += ' align="'+temp_val+'"';
    }

    temp_val = $this.find('.blox_item_icon_animation').html()+'';
    if( temp_val!='undefined' && temp_val!='' ){
        attr += ' animation="'+temp_val+'"';
    }

    temp_val = $this.find('.blox_item_icon_extra_class').html()+'';
    if( temp_val!='undefined' && temp_val!='' ){
        attr += ' extra_class="'+temp_val+'"';
    }
    
    $this.replaceWith('[blox_icon'+attr+']');

}
function revert_shortcode_icon($content){
    $content.find('.blox_icon').each(function(){
        revert_shortcode_icon_hook( jQuery(this) );
    });
    return $content;
}


function add_event_blox_element_icon(){
	
    jQuery('.blox_icon').each(function(){
        var $this = jQuery(this);

        if( !$this.parent().hasClass('blox_container') ){
            revert_shortcode_icon_hook( $this );
        }
        else{

            $this.find('.blox_item_actions .action_edit').unbind('click').click(function(){
                
                var form_element = [
                {
                    type: 'icon',
                    id: 'blox_element_option_icon',
                    label: 'Icon',
                    value: $this.find('.blox_item_icon_icon').html()

                },
                {
                    type: 'select',
                    id: 'blox_element_option_style',
                    label: 'Style',
                    value: $this.find('.blox_item_icon_style').html(),
                    options: [
                        { value: 'blox_elem_icon_no_bordered', label: '1. Icon No Bordered' },
                        { value: 'blox_elem_icon_circle', label: '2. Icon Circle' },
                        { value: 'blox_elem_icon_filled', label: '3. Icon Filled' },
                        { value: 'blox_elem_icon_rectangle', label: '4. Icon Rectangle' }
                    ],
                    description: "You should visit <a href='http://themeton.freshdesk.com/support/solutions/articles/152088-icon-element' target='_blank'>this link</a> and see how these styles look like."
                },
                {
                    type: 'colorpicker',
                    id: 'blox_element_option_color',
                    label: 'Color',
                    value: $this.find('.blox_item_icon_color').html()
                },
                {
                    type: 'input',
                    id: 'blox_element_option_link',
                    label: 'Link (optional)',
                    value: $this.find('.blox_item_icon_link').html()

                },
                {
                    type: 'checkbox_flat',
                    id: 'blox_element_option_target',
                    label: 'Link open in a new tab?',
                    value: $this.find('.blox_item_icon_target').html(),
                    options: [
                    {
                        value: '_self',
                        label: 'Same window'
                    },
                    {
                        value: '_blank',
                        label: 'In a new tab'
                    }
                    ]

                },
                {
                    type: 'number',
                    id: 'blox_element_option_size',
                    label: 'Icon Size',
                    std: 48,
                    value: $this.find('.blox_item_icon_size').html()

                },
				{
                        type: 'select',
                        id: 'blox_element_option_align',
                        label: 'Align',
                        value: $this.find('.blox_item_icon_align').html(),
                        options: [
                        {
                            value: 'left',
                            label: 'Left'
                        },
                        {
                            value: 'right',
                            label: 'Right'
                        },
                        {
                            value: 'center',
                            label: 'Center'
                        }
                        ]

                }
                ];

                $this.attr('animation', $this.find('.blox_item_icon_animation').html());
                $this.attr('extra_class', $this.find('.blox_item_icon_extra_class').html());

                show_blox_form('Edit Icon', form_element, function($form){
                    $this.find('.blox_item_icon_link').html(   jQuery('#blox_element_option_link').val() );
                    $this.find('.blox_item_icon_target').html( jQuery('#blox_element_option_target').val() );
                    $this.find('.blox_item_icon_style').html(  jQuery('#blox_element_option_style').val() );
                    $this.find('.blox_item_icon_color').html(  jQuery('#blox_element_option_color').val() );
                    $this.find('.blox_item_icon_icon').html(   jQuery('#blox_element_option_icon').val() );
                    $this.find('.blox_item_icon_size').html(   jQuery('#blox_element_option_size').val() );
					$this.find('.blox_item_icon_align').html(   jQuery('#blox_element_option_align').val() );
                    
                    $this.find('.blox_item_icon_animation').html( $this.attr('animation') );
                    $this.find('.blox_item_icon_extra_class').html( $this.attr('extra_class') );
                },
                {
                    target: $this,
                    extra_field: true
                });
                
                // Folds
                $element = jQuery('#blox_element_option_link');
                $element.change(function(){
	                if($element.val() != '') {
	                	jQuery('.blox_form_element_switcher').show();
	                } else {
		                jQuery('.blox_form_element_switcher').hide();
	                }
                });
                $element.change();

            });
                
            
            $this.find('.blox_item_actions .action_clone').unbind('click')
            .click(function(){
                $this.after($this.clone());
                add_event_blox_element_icon();
            });
                
            $this.find('.blox_item_actions .action_remove').unbind('click')
            .click(function(){
                $this.remove();
            });

        }

    });
	
}


