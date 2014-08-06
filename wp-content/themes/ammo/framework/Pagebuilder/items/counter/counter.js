
var blox_counter_attrs = ['text', 'icon', 'number', 'count_type', 'animation', 'extra_class', 'visibility'];

function get_blox_element_counter($content, $attrs){
    
    return '<div class="blox_item blox_counter" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-sort-numeric-asc"></i> \
                    <span class="blox_item_title">Counter</span> \
                    <small></small> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
            </div>';
}


function parse_shortcode_counter($content){
    $content = wp.shortcode.replace( 'blox_counter', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        });     
        return get_blox_element_counter( data.content, attrs );
    });
    return $content;
}


function revert_shortcode_counter($content){
    $content.find('.blox_counter').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < blox_counter_attrs.length; i++) {
            temp_val = jQuery(this).attr(blox_counter_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ blox_counter_attrs[i] +'="'+ temp_val +'"';
            }
        }
        
        jQuery(this).replaceWith('[blox_counter'+attr+'/]');

    });
    return $content;
}


function add_event_blox_element_counter(){
    
    jQuery('.blox_counter').each(function(){
        var $this = jQuery(this);

        $this.find('.blox_item_actions .action_edit').unbind('click')
            .click(function(){

                var form_element = [
                {
                    type: 'input',
                    id: 'counter-number',
                    label: 'Number',
                    value: $this.attr('number')
                },
                {
                    type: 'input',
                    id: 'counter-text',
                    label: 'Counter text',
                    value: $this.attr('text')
                },
                {
                    type: 'icon',
                    id: 'counter-icon',
                    label: 'Icon',
                    value: $this.attr('icon')
                },
                {
                    type: 'select',
                    id: 'counter-type',
                    label: 'Counter Animate Type',
                    value: $this.attr('count_type'),
                    options: [
                        { value: 'scroll', label: 'Scrolling' },
                        { value: 'count', label: 'Counting' }
                    ]
                }
                ];

                show_blox_form('Edit Counter', form_element, function($form){
                    $this.attr('text', jQuery('#counter-text').val() );
                    $this.attr('icon', jQuery('#counter-icon').val() );
                    $this.attr('number', jQuery('#counter-number').val() );
                    $this.attr('count_type', jQuery('#counter-type').val() );
                },
                {
                    target: $this,
                    extra_field: true,
                    visibility: true
                });

            });
                
            
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_counter();
        });
            
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
    
}
