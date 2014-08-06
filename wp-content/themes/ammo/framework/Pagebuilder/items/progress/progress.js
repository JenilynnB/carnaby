
var progress_attrs = ['title', 'percent', 'type', 'striped', 'active', 'animation', 'extra_class', 'visibility'];

function get_blox_element_progress($content, $attrs){
    
    return '<div class="blox_item blox_progress" '+($attrs!=undefined ? $attrs : '')+'> \
				<div class="blox_item_title"> \
					<i class="fa-tasks"></i> \
					<span class="blox_item_title">Progress Bar</span> \
					'+ get_blox_actions() +' \
				</div> \
                <div class="blox_item_content" style="display: none;">'+($content!=undefined ? $content : '')+'</div> \
			</div>';
}


function parse_shortcode_progress($content){
    $content = wp.shortcode.replace( 'blox_progress', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        });		
        return get_blox_element_progress( data.content, attrs );
    });
    return $content;
}

function revert_shortcode_progress($content){
    $content.find('.blox_progress').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < progress_attrs.length; i++) {
            temp_val = jQuery(this).attr(progress_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ progress_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
        jQuery(this).replaceWith('[blox_progress'+attr+']');
    });
    return $content;
}


function add_event_blox_element_progress(){
	
    jQuery('.blox_progress').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var form_element = [
                {
                    type: 'input',
                    id: 'progress_title',
                    label: 'Title',
                    value: $this.attr('title')
                },
                {
                    type: 'number',
                    id: 'progress_percent',
                    std: 60,
                    label: 'Fill Percent (%)',
                    value: $this.attr('percent')
                },
                {
                    type: 'select',
                    id: 'progress_type',
                    label: 'Progress Bar Style',
                    value: $this.attr('type'),
                    options: [
                        { value: 'default', label: 'Default' },
                        { value: 'progress-bar-success', label: 'Success' },
                        { value: 'progress-bar-info', label: 'Info' },
                        { value: 'progress-bar-warning', label: 'Warning' },
                        { value: 'progress-bar-danger', label: 'Danger' }
                    ]
                },
                {
                    type: 'checkbox_flat',
                    id: 'progress_striped',
                    label: 'Progress Bar Striped',
                    value: $this.attr('striped')
                },
                {
                    type: 'checkbox_flat',
                    id: 'progress_active',
                    label: 'Progress Bar Animated',
                    value: $this.attr('active')
                }
            ];

            show_blox_form('Edit Progress Bar', form_element, function($form){
                $this.attr( 'title', jQuery('#progress_title').val() );
                $this.attr( 'percent', jQuery('#progress_percent').val() );
                $this.attr( 'type', jQuery('#progress_type').val() );
                $this.attr( 'striped', jQuery('#progress_striped').val() );
                $this.attr( 'active', jQuery('#progress_active').val() );
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
            add_event_blox_element_progress();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
