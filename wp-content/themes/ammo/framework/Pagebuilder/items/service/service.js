
var service_attrs = ['title', 'layout', 'heading', 'alignment', 'icon', 'icon_size', 'icon_location', 'icon_link', 'skin', 'animation', 'extra_class', 'visibility'];

function get_blox_element_service($content, $attrs){
	
    var $item = jQuery('<div '+(typeof $attrs!=='undefined' ? $attrs : '')+'></div>');

    return '<div class="blox_item blox_service" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-asterisk"></i> \
					<span class="blox_item_title">Service</span> \
                    <small>'+(typeof $item.attr('title')!=='undefined' ? $item.attr('title') : '')+'</small> \
					'+ get_blox_actions() +' \
				</div> \
				<div class="blox_item_content" style="display: none;">'+ ($content!=undefined ? $content : '') +'</div> \
			</div>';
}


function parse_shortcode_service($content){
    $content = wp.shortcode.replace( 'blox_service', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        })
		
        return get_blox_element_service( data.content, attrs );
    });
    return $content;
}

function revert_shortcode_service($content){
    $content.find('.blox_service').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < service_attrs.length; i++) {
            temp_val = jQuery(this).attr(service_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ service_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
        jQuery(this).replaceWith('[blox_service'+attr+']'+jQuery(this).find('.blox_item_content').html()+'[/blox_service]');
    });
    return $content;
}


function validate_url(s) {
    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
    return regexp.test(s);
}

function add_event_blox_element_service(){
	
    jQuery('.blox_service').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){
            
            var form_element = [
                        {
                            type: 'input',
                            id: 'service_title',
                            label: 'Title',
                            value: $this.attr('title')
                        },
                        {
                            type: 'editor',
                            id: 'blox_option_editor',
                            label: 'Service Text',
                            value: $this.find('.blox_item_content').html()
                        },
                        {
                            type: 'select',
                            id: 'service_layout',
                            label: 'Service Layout',
                            value: $this.attr('layout'),
                            options: [
                                { value: 'default', label: '1. Default' },
                                { value: 'small_icon', label: '2. Small Icon with Title + Content' },
                                { value: 'left_icon', label: '3. Left Icon with Title + Content' }
                            ]
                        },
                        {
                            type: 'select',
                            id: 'text_align',
                            label: 'Service Alignment',
                            value: $this.attr('alignment'),
                            options:[
                                { value: 'left', label: 'Left' },
                                { value: 'center', label: 'Center' },
                                { value: 'right', label: 'Right' }
                            ]
                        },
                        {
                            type: 'select',
                            id: 'service_heading',
                            label: 'Title Heading',
                            value: (typeof $this.attr('heading')!=='undefined' ? $this.attr('heading') : 'h3'),
                            options:[
                                { value: 'h1', label: 'Headind 1' },
                                { value: 'h2', label: 'Headind 2' },
                                { value: 'h3', label: 'Headind 3' },
                                { value: 'h4', label: 'Headind 4' },
                                { value: 'h5', label: 'Headind 5' },
                                { value: 'h6', label: 'Headind 6' }
                            ]
                        },
                        {
                            type: 'select',
                            id: 'service_icon_or_image',
                            label: 'Select icon type',
                            value: validate_url(typeof $this.attr('icon')!=='undefined' ? $this.attr('icon') : '' ) ? 'image' : 'icon',
                            options: [
                                { value: 'icon', label: 'Icon' },
                                { value: 'image', label: 'Image' }
                            ],
                            description: "You should turn this option to Image and upload your custom image If you have specific images for your services excepts suggesting icons."
                        },
                        {
                            type: 'icon',
                            id: 'service_icon',
                            label: 'Icon',
                            value: $this.attr('icon')
                        },
                        {
                            type: 'image',
                            id: 'service_icon_image',
                            label: 'Image',
                            value: $this.attr('icon')
                        },
                        {
                            type: 'select',
                            id: 'service_icon_size',
                            label: 'Icon Size',
                            value: $this.attr('icon_size'),
                            options:[
                                { value: 'lg', label: 'Large' },
                                { value: 'md', label: 'Medium' },
                                { value: 'sm', label: 'Small' }
                            ],
                            description: 'It won\'t aspect If you upload custom image. Your image show own size and container restricts overflow if the image is too big.'
                        },
                        {
                            type: 'select',
                            id: 'icon_location',
                            label: 'Icon Position',
                            value: $this.attr('icon_location'),
                            options:[
                                { value: 'top', label: 'Top' },
                                { value: 'middle', label: 'Middle' },
                                { value: 'bottom', label: 'Bottom' }
                            ]
                        },
                        {
                            type: 'input',
                            id: 'icon_link',
                            label: 'Icon link (optional)',
                            value: $this.attr('icon_link'),
                            description: 'This option works only for Large Icon size. Dont forget to add http protocol in your url :)'
                        },
                    ];


            show_blox_form('Edit Service Element', form_element, function($form){
                
                $this.attr('title', jQuery('#service_title').val());
                $this.attr('layout', jQuery('#service_layout').val());
                $this.attr('heading', jQuery('#service_heading').val());
                $this.attr('alignment', jQuery('#text_align').val());
                $this.attr('icon', jQuery('#service_icon_or_image').val()=='icon' ? jQuery('#service_icon').val() : jQuery('#service_icon_image').val());
                $this.attr('icon_size', jQuery('#service_icon_size').val());
                $this.attr('icon_location', jQuery('#icon_location').val());
                $this.attr('icon_link', jQuery('#icon_link').val());
                $this.find('.blox_item_content').html( get_content_tinymce() );

            },
            {
                target: $this,
                extra_field: true,
                skin: true,
                visibility: true
            });
                            
            // Icon or Image Selector
            jQuery('#service_icon_or_image').change(function(){
                if( this.value == 'icon' ){
                    jQuery('#service_icon_image').parent().hide();
                    jQuery('#service_icon').parent().show();
                }
                else{
                    jQuery('#service_icon').parent().hide();
                    jQuery('#service_icon_image').parent().show();
                }
            });
            jQuery('#service_icon_or_image').change();


            // Service layout Selector
            jQuery('#service_layout').change(function(){
                if( this.value == 'default' ){
                    jQuery('#text_align').parent().show();
                    jQuery('#service_icon_size').parent().show();
                    jQuery('#icon_location').parent().show();
                }
                else{
                    jQuery('#text_align').parent().hide();
                    jQuery('#service_icon_size').parent().hide();
                    jQuery('#icon_location').parent().hide();
                    jQuery('#service_icon_or_image').val('icon').change();
                }
            });
            jQuery('#service_layout').change();


        });
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_service();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
