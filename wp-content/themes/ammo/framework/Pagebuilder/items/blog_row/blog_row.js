
var blog_row_attrs = ['categories', 'column', 'count', 'bgcolor', 'ratio', 'filter', 'order', 'exclude', 'extra_class', 'visibility'];

function get_blox_element_blog_row($content, $attrs){
    
    return '<div class="blox_item blog_row type-fullwidth" '+($attrs!=undefined ? $attrs : '')+'> \
            <div class="blox_item_title"> \
                <i class="fa-th"></i> \
                <span class="blox_item_title">Blog Fullwidth</span> \
                '+ get_blox_actions() +' \
            </div> \
            <div class="blox_item_content">'+($content!=undefined ? $content : '')+'</div> \
        </div>';
    
}


function parse_shortcode_blog_row($content){
    $content = wp.shortcode.replace( 'blog_row', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        })
        return get_blox_element_blog_row( data.content, attrs );
    });
    return $content;
}


function revert_shortcode_blog_row($content){
    $content.find('.blog_row').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < blog_row_attrs.length; i++) {
            temp_val = jQuery(this).attr(blog_row_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ blog_row_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
        jQuery(this).replaceWith('[blog_row'+attr+'/]');
    });
    return $content;
}


function add_event_blox_element_blog_row(){
	
    jQuery('.blog_row').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var form_element = [
                {
                    type: 'select',
                    id: 'blox_element_option_categories',
                    label: 'Categories',
                    value: '',
                    options: [
                        { value: 'all', label: 'All' }
                    ]
                },
                {
                    type: 'checkbox_flat',
                    id: 'blox_element_option_filter',
                    label: 'Show Category Filter',
                    value: $this.attr('filter'),
                    options: [
                        { value: 'yes', label: 'Yes' },
                        { value: 'no', label: 'No' }
                    ]

                },
                {
                    type: 'number',
                    id: 'blox_elem_column',
                    label: 'Columns',
                    std: 4,
                    value: $this.attr('column')
                },
                {
                    type: 'number',
                    id: 'blox_element_option_count',
                    label: 'Post count',
                    std: 8,
                    value: $this.attr('count')
                },
                {
                    type: 'colorpicker',
                    id: 'blox_element_option_bgcolor',
                    label: 'Color',
                    value: $this.attr('bgcolor')
                },
                {
                    type: 'select',
                    id: 'blox_element_option_ratio',
                    label: 'Image Ratio',
                    value: $this.attr('ratio'),
                    options: [
                        { value: '1x1', label: '1:1 - Image size' },
                        { value: '4x3', label: '4:3 - Image size' },
                        { value: '16x9', label: '16:9 - Image size' }
                    ]
                },
                {
                    type: 'input',
                    id: 'blox_element_option_exclude',
                    label: 'Exclude posts',
                    value: $this.attr('exclude'),
                    description: 'Please add post IDs with comma separator. Example: 125,1,65'
                },
                {
                    type: 'select',
                    id: 'blox_element_option_order',
                    label: 'Order type',
                    value: $this.attr('order'),
                    options: [
                        { value: 'default', label: 'Date' },
                        { value: 'dateasc', label: 'Date Ascending' },
                        { value: 'titleasc', label: 'Title Ascending' },
                        { value: 'titledes', label: 'Title Descending' },
                        { value: 'comment', label: 'Most Commented' },
                        { value: 'postid', label: 'Post ID' },
                        { value: 'random', label: 'Random Order' }
                    ]
                },
                {
                    type: 'input',
                    id: 'blox_element_option_class',
                    label: 'Extra Class',
                    value: $this.attr('extra_class')
                }
            ];

            show_blox_form('Edit Blog', form_element, function($form){
                $this.attr('column', jQuery('#blox_elem_column').val());
                $this.attr('count', jQuery('#blox_element_option_count').val());
                $this.attr('filter', jQuery('#blox_element_option_filter').val());
                $this.attr('exclude', jQuery('#blox_element_option_exclude').val());
                $this.attr('order', jQuery('#blox_element_option_order').val());
                $this.attr('extra_class', jQuery('#blox_element_option_class').val());
                $this.attr('bgcolor', jQuery('#blox_element_option_bgcolor').val());
                $this.attr('ratio', jQuery('#blox_element_option_ratio').val());

                var sval = jQuery('#blox_new_cats').select2('val');
                var rval = '';
                for (var i = 0; i < sval.length; i++) {
                    rval += (i==0 ? sval[i] : ','+sval[i]);
                }
                $this.attr('categories', rval);
            },
            {
                target: $this,
                visibility: true
            });

            
            jQuery('#blox_element_option_categories').parent().append( '<span class="ajax_spinner"></span>' );
            jQuery.post( ajaxurl, { 'action':'get_blox_element_blog', 'value': $this.attr('categories') }, function(data){
                if( data != "-1" ){
                    jQuery('#blox_element_option_categories').parent().find('.ajax_spinner').remove();

                    jQuery('#blox_element_option_categories').replaceWith( jQuery(data).find('#blox_new_cats') );
                    jQuery('#blox_new_cats').select2({ placeholder: 'Select Categories' });
                }
            });
            

        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_blog_row();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}

