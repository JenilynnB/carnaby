
var video_attrs = ['title', 'image', 'color', 'animation', 'extra_class', 'visibility'];

function get_blox_element_video($content, $attrs){
	
    return '<div class="blox_item blox_video" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-video-camera"></i> \
                    <span class="blox_item_title">Video</span> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
            </div>';
}


function parse_shortcode_video($content){
    $content = wp.shortcode.replace( 'blox_video', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        })
		
        return get_blox_element_video( data.content, attrs );
    });
    return $content;
}

function revert_shortcode_video($content){
    $content.find('.blox_video').each(function(){
        attr = '';
        var temp_val = '';
        
        for (var i = 0; i < video_attrs.length; i++) {
            temp_val = jQuery(this).attr(video_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ video_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
        jQuery(this).replaceWith('[blox_video'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_video]');
    });
    return $content;
}


function add_event_blox_element_video(){
	
    jQuery('.blox_video').each(function(){
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
                    id: 'edit_form_widget_title',
                    label: 'Title',
                    value: $this.attr('title')

                },
                {
                    type: 'select',
                    id: 'video_type',
                    label: 'Video Source Type',
                    value: attr_embed_url,
                    options: [
                        {value: 'url', label: 'By URL' },
                        { value: 'embed', label: 'Custom Embed' }
                    ]
                },
                {
                    type: 'video',
                    id: 'edit_form_video_url',
                    label: 'Video URL',
                    value: (attr_embed_url=='url' ? $this.find('.blox_item_content').html() : ''),
                    description: 'Youtube, Vimeo link or MP4 media file link.'
                },
                {
                    type: 'image',
                    id: 'edit_form_video_img',
                    label: 'Poster image for self hosted video',
                    value: $this.attr('image'),
                    description: 'If you added mp4 file on previous source field, you should add here poster image for those mobile and small screens.'
                },
                {
                    type: 'textarea',
                    id: 'video_embed',
                    label: 'Embed code',
                    value: (attr_embed_url=='embed' ? $this.find('.blox_item_content').html() : '')

                },
                {
                    type: 'colorpicker',
                    id: 'video_elem_option_color',
                    label: 'Player Color',
                    value: $this.attr('color'),
                    description: 'Self hosted video plays with HTML player and it is possible to change color of that. If you selected local video on above, you should set color of player.'
                }
            ];

            show_blox_form('Edit Video element', form_element, function($form){
                $this.attr('title', jQuery('#edit_form_widget_title').val() );
                $this.attr('color', jQuery('#video_elem_option_color').val() );
                if( jQuery('#video_type').val()=='url' ){
                    $this.find('.blox_item_content').html( jQuery('#edit_form_video_url').val() );
                    $this.attr('image', jQuery('#edit_form_video_img').val() );
                }
                else{
                    $this.find('.blox_item_content').html( jQuery('#video_embed').val() );
                    $this.removeAttr('image');
                }
            },
            {
                target: $this,
                extra_field: true,
                visibility: true
            });
        	
            jQuery('#video_type').change(function(){
                if( jQuery('#video_type').val() == 'url' ){
                    jQuery('#video_embed').parent().hide();
                    jQuery('#edit_form_video_url').parent().show();
                    jQuery('#edit_form_video_img').parent().show();
                }
                else{
                    jQuery('#edit_form_video_url').parent().hide();
                    jQuery('#edit_form_video_img').parent().hide();
                    jQuery('#video_embed').parent().show();
                }
            });
            jQuery('#video_type').change();
        });
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_video();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
