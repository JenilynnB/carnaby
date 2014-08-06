
var audio_attrs = ['title', 'color', 'animation', 'extra_class', 'visibility'];

function get_blox_element_audio($content, $attrs){
    
    return '<div class="blox_item blox_audio" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-music"></i> \
                    <span class="blox_item_title">Audio Player</span> \
                    <small></small> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
            </div>';
}


function parse_shortcode_audio($content){
    $content = wp.shortcode.replace( 'blox_audio', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        })
		
        return get_blox_element_audio( data.content, attrs );
    });
    return $content;
}

function revert_shortcode_audio($content){
    $content.find('.blox_audio').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < audio_attrs.length; i++) {
            temp_val = jQuery(this).attr(audio_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ audio_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
        jQuery(this).replaceWith('[blox_audio'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_audio]');
    });
    return $content;
}


function add_event_blox_element_audio(){
	
    jQuery('.blox_audio').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            // check url or embed
            var attr_embed_url = $this.find('.blox_item_content').html();
            if( attr_embed_url.substring(0,4) == 'http' )
                attr_embed_url = 'url';
            else
                attr_embed_url = 'embed';

            var form_element = [
            {
                type: 'input',
                id: 'audio_title',
                label: 'Title',
                value: $this.attr('title')

            },
            {
                type: 'select',
                id: 'audio_type',
                label: 'Audio Source Type',
                value: attr_embed_url,
                options: [
                    { value: 'url', label: 'By URL' },
                    { value: 'embed', label: 'Custom Embed' }
                ]
            },
            {
                type: 'file',
                id: 'audio_url',
                label: 'Audio URL',
                value: (attr_embed_url=='url' ? $this.find('.blox_item_content').html() : '')

            },
            {
                type: 'textarea',
                id: 'audio_embed',
                label: 'Embed code',
                value: (attr_embed_url=='embed' ? $this.find('.blox_item_content').html() : '')

            },
            {
                type: 'colorpicker',
                id: 'audio_elem_option_color',
                label: 'Player Color (optional)',
                value: $this.attr('color'),
                description: 'Select proper color for your HTML5 player.'
            }
            ];

            show_blox_form('Edit Audio element', form_element, function($form){
                $this.attr('title', jQuery('#audio_title').val() );
                $this.attr('color', jQuery('#audio_elem_option_color').val() );
                if( jQuery('#audio_type').val()=='url' ){
                    $this.find('.blox_item_content').html( jQuery('#audio_url').val() );
                }
                else{
                    $this.find('.blox_item_content').html( jQuery('#audio_embed').val() );
                }
            },
            {
                target: $this,
                extra_field: true,
                visibility: true
            });
            
            //Folds
            jQuery('#audio_type').change(function(){
                if( jQuery('#audio_type').val() == 'url' ){
                    jQuery('#audio_embed').parent().hide();
                    jQuery('#audio_url').parent().show();
                    jQuery('.wp-picker-container').parent().show();
                }
                else{
                    jQuery('#audio_url').parent().hide();
                    jQuery('#audio_embed').parent().show();
                    jQuery('.wp-picker-container').parent().hide();
                }
            });
            jQuery('#audio_type').change();

        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_audio();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}