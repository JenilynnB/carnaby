
var viewport_attrs = ['title', 'image', 'layout', 'viewport_height', 'link', 'animation', 'extra_class', 'visibility'];

function get_blox_element_viewport($content, $attrs){
    
    return '<div class="blox_item blox_viewport" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-picture-o"></i> \
                    <span class="blox_item_title">Viewport Image</span> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
            </div>';
}


function parse_shortcode_viewport($content){
    $content = wp.shortcode.replace( 'blox_viewport', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        })
        
        return get_blox_element_viewport(data.content, attrs);
    });
    return $content;
}

function revert_shortcode_viewport($content){
    $content.find('.blox_viewport').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < viewport_attrs.length; i++) {
            temp_val = jQuery(this).attr(viewport_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ viewport_attrs[i] +'="'+ temp_val +'"';
            }
        }
        
        jQuery(this).replaceWith('[blox_viewport'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_viewport]');
    });
    return $content;
}


function add_event_blox_element_viewport(){
    
    jQuery('.blox_viewport').each(function(){
        var $this = jQuery(this);
        
        $this.find('.blox_item_actions .action_edit').unbind('click')
            .click(function(){

                var form_element = [
                            {
                                type: 'input',
                                id: 'viewport_title',
                                label: 'Title',
                                value: $this.attr('title')
                            },
                            {
                                type: 'image',
                                id: 'viewport_image',
                                label: 'Image',
                                value: $this.attr('image')
                            },
                            {
                                type: 'select',
                                id: 'viewport_layout',
                                label: 'Viewport Layout',
                                value: $this.attr('layout'),
                                options: [
                                    { value: 'default', label: 'Default' },
                                    { value: 'imac', label: 'iMac Frame' },
                                    { value: 'laptop', label: 'Laptop Frame' },
                                    { value: 'iphone', label: 'iPhone Frame' }
                                ]
                            },
                            {
                                type: 'number',
                                id: 'viewport_height',
                                label: 'Height',
                                value: typeof($this.attr('viewport_height'))!=='undefined' ? parseInt($this.attr('viewport_height')) : '200'
                            },
                            {
                                type: 'input',
                                id: 'viewport_link',
                                label: 'Link',
                                value: $this.attr('link')
                            }
                        ];

                show_blox_form('Edit Viewport Image', form_element, function($form){
                    $this.attr('title', jQuery('#viewport_title').val() );
                    $this.attr('image', jQuery('#viewport_image').val() );
                    $this.attr('layout', jQuery('#viewport_layout').val() );
                    $this.attr('viewport_height', jQuery('#viewport_height').val() );
                    $this.attr('link', jQuery('#viewport_link').val() );

                },
                {
                    target: $this,
                    extra_field: true,
                    visibility: true
                });

                jQuery('#viewport_layout').change(function(){
                    if( this.value=='default' ){
                        jQuery('#viewport_height').parent().slideDown();
                    }
                    else{
                        jQuery('#viewport_height').parent().slideUp();
                    }
                });
                jQuery('#viewport_layout').change();
                
            });
        
        $this.find('.blox_item_actions .action_clone').unbind('click')
            .click(function(){
                $this.after($this.clone());
                add_event_blox_element_viewport();
            });
            
        $this.find('.blox_item_actions .action_remove').unbind('click')
            .click(function(){
                $this.remove();
            });
    });
    
}
