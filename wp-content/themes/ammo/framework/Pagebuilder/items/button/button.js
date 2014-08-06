
var blox_button_attrs = ['text', 'link', 'target', 'button_type', 'size', 'icon', 'align', 'animation', 'extra_class', 'visibility'];

function get_blox_element_button($content, $attrs){
    
    return '<div class="blox_item blox_button" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-hand-o-up"></i> \
					<span class="blox_item_title">Button</span> \
                    <small></small> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
			</div>';
}


function parse_shortcode_button($content){
    $content = wp.shortcode.replace( 'blox_button', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        });		
        return get_blox_element_button( data.content, attrs );
    });
    return $content;
}


function revert_button_shortcode_hook($this){
    attr = '';
    var temp_val = '';

    for (var i = 0; i < blox_button_attrs.length; i++) {
        temp_val = $this.attr(blox_button_attrs[i])+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' '+ blox_button_attrs[i] +'="'+ temp_val +'"';
        }
    }
    
    $this.replaceWith('[blox_button'+attr+'/]');
}

function revert_shortcode_button($content){
    $content.find('.blox_button').each(function(){
        revert_button_shortcode_hook( jQuery(this) );
    });
    return $content;
}


function add_event_blox_element_button(){
	
    jQuery('.blox_button').each(function(){
        var $this = jQuery(this);

        if( !$this.parent().hasClass('blox_container') ){
            revert_button_shortcode_hook( $this );
        }
        else{

            $this.find('.blox_item_actions .action_edit').unbind('click')
                .click(function(){

                    var form_element = [
                    {
                        type: 'input',
                        id: 'button-text',
                        label: 'Button text',
                        value: $this.attr('text')

                    },
                    {
                        type: 'input',
                        id: 'button-link',
                        label: 'Link',
                        value: $this.attr('link')

                    },
                    {
                        type: 'select',
                        id: 'button-target',
                        label: 'Open link in a new tab?',
                        value: $this.attr('target'),
                        options: [
                            { value: '_self', label: 'Same window' },
                            { value: '_blank', label: 'In a new tab' },
                            { value: 'lightbox', label: 'Lightbox (For image or video url)' }
                        ]
                    },
                    {
                        type: 'select',
                        id: 'button-type',
                        label: 'Button Style (Color)',
                        value: $this.attr('button_type'),
                        options: [
                            { value: 'btn-default', label: 'Default (Black)' },
                            { value: 'btn-primary', label: 'Primary (as primary color)' },
                            { value: 'btn-success', label: 'Success (Green)' },
                            { value: 'btn-info', label: 'Info (Blue)' },
                            { value: 'btn-warning', label: 'Warning (Orange)' },
                            { value: 'btn-danger', label: 'Danger (Red)' },
                            { value: 'btn-link', label: 'Link (just a link)' }
                        ]
                    },
                    {
                        type: 'select',
                        id: 'button-size',
                        label: 'Button Size',
                        value: $this.attr('size'),
                        options: [
                            { value: 'btn-md', label: 'Medium' },
                            { value: 'btn-lg', label: 'Large' },
                            { value: 'btn-sm', label: 'Small' },
                            { value: 'btn-xs', label: 'Extra Small' }
                        ]
                    },
                    {
                        type: 'icon',
                        id: 'button-icon',
                        label: 'Icon',
                        value: $this.attr('icon')
                    },
                    {
                        type: 'select',
                        id: 'button-align',
                        label: 'Align',
                        value: $this.find('.align').html(),
                        options: [
                            { value: 'left', label: 'Left' },
                            { value: 'right', label: 'Right' },
                            { value: 'center', label: 'Center' }
                        ]

                    }
                    ];

                    show_blox_form('Edit Button', form_element, function($form){
                        $this.attr('text', jQuery('#button-text').val() );
                        $this.attr('link', jQuery('#button-link').val() );
                        $this.attr('target', jQuery('#button-target').val() );
                        $this.attr('button_type', jQuery('#button-type').val() );
                        $this.attr('size', jQuery('#button-size').val() );
                        $this.attr('icon', jQuery('#button-icon').val() );
                        $this.attr('align', jQuery('#button-align').val() );
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
                add_event_blox_element_button();
            });
                
            $this.find('.blox_item_actions .action_remove').unbind('click')
            .click(function(){
                $this.remove();
            });

        }
    });
	
}
