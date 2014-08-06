
var list_attrs = ['title', 'icon', 'color', 'animation', 'extra_class', 'visibility'];

function get_blox_element_list($content, $attrs){
    
    return '<div class="blox_item blox_list" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-list-ul"></i> \
                    <span class="blox_item_title">Iconic List</span> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
            </div>';
}


function parse_shortcode_list($content){
    $content = wp.shortcode.replace( 'blox_list', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        });

        return get_blox_element_list(data.content, attrs);
    });
    return $content;
}

function revert_shortcode_list_hook($this){
    attr = '';
    var temp_val = '';

    for (var i = 0; i < list_attrs.length; i++) {
        temp_val = $this.attr(list_attrs[i])+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' '+ list_attrs[i] +'="'+ temp_val +'"';
        }
    }
        
    $this.replaceWith('[blox_list'+attr+']'+$this.find('> .blox_item_content').html()+'[/blox_list]');
}

function revert_shortcode_list($content){
    $content.find('.blox_list').each(function(){
        revert_shortcode_list_hook( jQuery(this) );
    });
    return $content;
}


function add_event_blox_element_list(){
	
    jQuery('.blox_list').each(function(){
        var $this = jQuery(this);

        if( !$this.parent().hasClass('blox_container') ){
            revert_shortcode_list_hook( $this );
        }
        else{
            $this.find('.blox_item_actions .action_edit').unbind('click').click(function(){
                
                var form_element = [
                    {
                        type: 'input',
                        id: 'blox_element_option_title',
                        label: 'Title',
                        value: $this.attr('title')
                    },
                    {
                        type: 'icon',
                        id: 'blox_element_option_icon',
                        label: 'List Icon',
                        value: $this.attr('icon')
                    },
                    {
                        type: 'colorpicker',
                        id: 'blox_element_option_color',
                        label: 'Icon Color',
                        value: $this.attr('color')
                    },
                    {
                        type: 'textarea',
                        id: 'blox_element_option_list',
                        label: 'List Content (Please add list items as separated by line breaks. No need html tags)',
                        value: ''
                    }
                ];

                show_blox_form('Edit Icon list', form_element, function($form){
                    $this.attr('title', jQuery('#blox_element_option_title').val() );
                    $this.attr('icon', jQuery('#blox_element_option_icon').val() );
                    $this.attr('color', jQuery('#blox_element_option_color').val() );

                    $this.find('.blox_item_content').html('');
                    var splited = jQuery('#blox_element_option_list').val().split('\n');
                    for(var i=0; i<splited.length; i++){
                        $this.find('.blox_item_content').html( $this.find('.blox_item_content').html() + '<li>'+splited[i]+'</li>' );
                    }
                },
                {
                    target: $this,
                    extra_field: true,
                    visibility: true
                });

                $this.find('.blox_item_content').find('li').each(function(index){
                    jQuery('#blox_element_option_list').val( jQuery('#blox_element_option_list').val()+(index==0 ? '' : '\n')+jQuery(this).text());
                });

            });
                
            
            $this.find('.blox_item_actions .action_clone').unbind('click')
            .click(function(){
                $this.after($this.clone());
                add_event_blox_element_list();
            });
                
            $this.find('.blox_item_actions .action_remove').unbind('click')
            .click(function(){
                $this.remove();
            });

        }

    });
	
}


