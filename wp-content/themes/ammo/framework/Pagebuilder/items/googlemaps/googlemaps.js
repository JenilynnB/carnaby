
var gmap_attrs = ['title', 'type', 'lat', 'long', 'zoom', 'viewtype', 'pin', 'map_height', 'map_color', 'animation', 'extra_class', 'visibility'];

function get_blox_element_googlemaps($content, $attrs){
    
    return '<div class="blox_item blox_gmap" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-user"></i> \
					<span class="blox_item_title">Google Maps</span> \
					'+ get_blox_actions() +' \
				</div> \
                <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
			</div>';
}


function parse_shortcode_googlemaps($content){
    $content = wp.shortcode.replace( 'blox_gmap', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
                        
        });

        return get_blox_element_googlemaps(data.content, attrs);
    });
    return $content;
}

function revert_shortcode_googlemaps($content){
    $content.find('.blox_gmap').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < gmap_attrs.length; i++) {
            temp_val = jQuery(this).attr(gmap_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ gmap_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
        jQuery(this).replaceWith('[blox_gmap'+attr+']'+jQuery(this).find('> .blox_item_content').html()+'[/blox_gmap]');
    });
    return $content;
}


function add_event_blox_element_googlemaps(){
	
    jQuery('.blox_gmap').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var form_element = [
            {
                type: 'input',
                id: 'blox_element_option_title',
                label: 'Title',
                value: $this.attr('title')
            },
            {
                type: 'select',
                id: 'blox_element_option_select',
                label: 'Map Source Type',
                value: $this.attr('type'),
                options: [
                    {value: 'embed',label: 'Embed code'},
                    {value: 'custom',label: 'Custom map settings'}
                ]
            },
            {
                type: 'textarea',
                id: 'blox_element_option_embed',
                label: 'Map Embed',
                value: $this.find('.blox_item_content').html()
            },
            {
                type: 'input',
                id: 'blox_element_option_lat',
                label: 'Latitude',
                value: $this.attr('lat'),
                std: '48.856614',
                description: 'Latitude: 48.856614'
            },
            {
                type: 'input',
                id: 'blox_element_option_long',
                label: 'Longitute',
                value: $this.attr('long'),
                std: '2.352222',
                description: 'Longitute: 2.352222'
            },
            {
                type: 'select',
                id: 'blox_element_option_type',
                label: 'View Type',
                value: $this.attr('viewtype'),
                options: [
                    {value: 'ROADMAP',label: 'Map'},
                    {value: 'SATELLITE',label: 'Satellite'},
                    {value: 'TERRAIN',label: 'Terrain'}
                ]
            },
            {
                type: 'input',
                id: 'blox_element_option_mapcolor',
                label: 'Map Color',
                value: $this.attr('map_color'),
                std: '',
                description: 'Default value is blank, You can select map hue color.'
            },
            {
                type: 'image',
                id: 'blox_element_option_pin',
                label: 'Image',
                value: $this.attr('pin')
            },
            {
                type: 'number',
                id: 'blox_element_option_zoom',
                label: 'Zoom',
                std: 14,
                value: $this.attr('zoom'),
                description: 'Zoom value have to 1 to 20. Default: 14.'
            },
            {
                type: 'number',
                id: 'blox_element_option_height',
                label: 'Map Height',
                std: 400,
                value: $this.attr('map_height')
            }
            ];

            show_blox_form('Edit Google Map', form_element, function($form){
                $this.attr('title', jQuery('#blox_element_option_title').val() );
                $this.attr('type', jQuery('#blox_element_option_select').val() );
                $this.attr('lat', jQuery('#blox_element_option_lat').val() );
                $this.attr('long', jQuery('#blox_element_option_long').val() );
                $this.attr('zoom', jQuery('#blox_element_option_zoom').val() );
                $this.attr('viewtype', jQuery('#blox_element_option_type').val() );
                $this.attr('pin', jQuery('#blox_element_option_pin').val() );
                $this.attr('map_color', jQuery('#blox_element_option_mapcolor').val() );
                $this.attr('map_height', jQuery('#blox_element_option_height').val() );
                $this.find('.blox_item_content').html( jQuery('#blox_element_option_embed').val() );
            },{
                target: $this,
                extra_field: true,
                visibility: true
            });

            jQuery('#blox_element_option_select').change(function(){
                if( this.value=='embed' ){
                    jQuery('#blox_element_option_embed').parent().show();
                    jQuery('#blox_element_option_lat,#blox_element_option_long,#blox_element_option_type, #blox_element_option_zoom,#blox_element_option_height,#blox_element_option_pin,#blox_element_option_mapcolor').parent().hide();
                }
                else{
                    jQuery('#blox_element_option_embed').parent().hide();
                    jQuery('#blox_element_option_lat,#blox_element_option_long,#blox_element_option_type, #blox_element_option_zoom,#blox_element_option_height,#blox_element_option_pin,#blox_element_option_mapcolor').parent().show();
                }
            });
            jQuery('#blox_element_option_select').change();

        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_googlemaps();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
