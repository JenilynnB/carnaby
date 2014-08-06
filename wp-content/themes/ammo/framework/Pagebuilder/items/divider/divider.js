

var divider_attrs = ['type', 'space', 'fullwidth', 'extra_class', 'visibility'];

function get_blox_element_divider($content, $attrs){
    
    return '<div class="blox_item blox_divider" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-user"></i> \
                    <span class="blox_item_title">Divider / Space</span> \
                    <small></small> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content">'+($content!=undefined ? $content : '')+'</div> \
        </div>';
}


function parse_shortcode_divider($content){
    $content = wp.shortcode.replace( 'blox_divider', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        });		
        return get_blox_element_divider( data.content, attrs );
    });
    return $content;
}

function revert_shortcode_divider($content){
    $content.find('.blox_divider').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < divider_attrs.length; i++) {
            temp_val = jQuery(this).attr(divider_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ divider_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
        jQuery(this).replaceWith('[blox_divider'+attr+'/]');
    });
    return $content;
}


function add_event_blox_element_divider(){
	
    jQuery('.blox_divider').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var form_element = [
            {
                type: 'select',
                id: 'divider_type',
                label: 'Style',
                value: $this.attr('type'),
                options: [
                    { value: 'default', label: 'Default' },
                    { value: 'dashed', label: 'Dashed' },
                    { value: 'dotted', label: 'Dotted' },
                    { value: 'double', label: 'Double' },
                    { value: 'groove', label: 'Groove' },
                    { value: 'space', label: 'Space' }
                ]
            },
            {
                type: 'checkbox_flat',
                id: 'divider_fullwidth',
                label: 'Fullwidth',
                value: $this.attr('fullwidth')
            },
            {
                type: 'number',
                id: 'divider_space',
                label: 'Space',
                std: 5,
                value: $this.attr('space')
            },
            {
                type: 'input',
                id: 'divider_class',
                label: 'Extra Class',
                value: $this.attr('extra_class')
            }
            ];

            show_blox_form('Edit Divider', form_element, function($form){
                $this.attr('type', jQuery('#divider_type').val());
                $this.attr('space', jQuery('#divider_space').val());
                $this.attr('fullwidth', jQuery('#divider_fullwidth').val());
                $this.attr('extra_class', jQuery('#divider_class').val());
            },{
                target: $this,
                visibility: true
            });

            jQuery('#divider_type').change(function(){
                if( this.value == 'space' ){
                    jQuery('#divider_space').parent().show();
                }
                else{
                    jQuery('#divider_space').parent().hide();
                }
            });
            jQuery('#divider_type').change();
            

        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_divider();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
