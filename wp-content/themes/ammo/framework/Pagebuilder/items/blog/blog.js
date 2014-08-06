
var blog_attrs = ['title', 'blog_type', 'categories', 'blog_filter', 'content', 'readmore', 'count', 'pager', 'ignoresticky',
                    'overlay', 'exclude', 'order', 'skip', 'skin', 'extra_class', 'visibility'];

function get_blox_element_blog($content, $attrs){
    
    return '<div class="blox_item blox_blog" '+($attrs!=undefined ? $attrs : '')+'> \
                <div class="blox_item_title"> \
                    <i class="fa-th-large"></i> \
                    <span class="blox_item_title">Blog:</span> \
                    <small></small> \
                    '+ get_blox_actions() +' \
                </div> \
                <div class="blox_item_content" style="display: none;"></div> \
			</div>';
}


function parse_shortcode_blog($content){
    $content = wp.shortcode.replace( 'blox_blog', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += key+'="'+ value +'" ';
            }
        })
        return get_blox_element_blog(data.content, attrs);
    });
    return $content;
}


function revert_shortcode_blog($content){
    $content.find('.blox_blog').each(function(){
        attr = '';
        var temp_val = '';

        for (var i = 0; i < blog_attrs.length; i++) {
            temp_val = jQuery(this).attr(blog_attrs[i])+'';
            if( temp_val!='undefined' && temp_val!='' ){
                attr += ' '+ blog_attrs[i] +'="'+ temp_val +'"';
            }
        }
		
        jQuery(this).replaceWith('[blox_blog'+attr+'/]');
    });
    return $content;
}


function add_event_blox_element_blog(){
	
    jQuery('.blox_blog').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var form_element = [
            {
                type: 'input',
                id: 'blog_title',
                label: 'Title',
                value: $this.attr('title')
            },
            {
                type: 'select',
                id: 'blog_type',
                label: 'Layout Style',
                value: (typeof $this.attr('blog_type')!=='undefined' ? $this.attr('blog_type') : 'regular'),
                options: [
                    { value: 'regular', label: '1. Regular' },
                    { value: 'grid2', label: '2. Grid 2 columns' },
                    { value: 'grid3', label: '3. Grid 3 columns' },
                    { value: 'grid4', label: '4. Grid 4 columns' },
                    { value: 'masonry2', label: '5. Masonry 2 columns' },
                    { value: 'masonry3', label: '6. Masonry 3 columns' },
                    { value: 'masonry4', label: '7. Masonry 4 columns' }
                ]
            },
            {
                type: 'select',
                id: 'blog_categories',
                label: 'Post Filter Type',
                value: $this.attr('categories'),
                options: [
                    { value: 'all', label: 'All' },
                    { value: 'categories', label: 'Categories' },
                    { value: 'tags', label: 'Tags' },
                    { value: 'format', label: 'Post Formats' }
                ]
            },
            {
                type: 'select',
                id: 'blox_elem_multi_category',
                label: 'Choose Include Categories',
                value: '',
                options: [
                    { value: 'all', label: 'All' },
                ]

            },
            {
                type: 'select',
                id: 'blox_elem_multi_tags',
                label: 'Choose Include Tags',
                value: '',
                options: [
                    { value: 'all', label: 'All' },
                ]

            },
            {
                type: 'select',
                id: 'blox_elem_multi_formats',
                label: 'Choose Include Post Formats',
                value: '',
                options: [
                    { value: 'all', label: 'All' },
                ]

            },
            {
                type: 'select',
                id: 'blog_content',
                label: 'Post Content Structure',
                value: $this.attr('content'),
                options: [
                    { value: 'both', label: 'Excerpt + Read more link' },
                    { value: 'content', label: 'Full content' },
                    { value: 'excerpt', label: 'Excerpt' },
                    { value: 'nocontent', label: 'No content' }
                ]

            },
            {
                type: 'input',
                id: 'blog_readmore',
                label: 'Read More text',
                value: $this.attr('readmore'),
                description: 'Read more button text. Default: Read more'
            },
            {
                type: 'number',
                id: 'blog_count',
                std: 10,
                label: 'Post Count',
                value: $this.attr('count')
            },
            {
                type: 'checkbox_flat',
                id: 'blog_pager',
                label: 'Show Pagination',
                value: $this.attr('pager'),
                options: [
                    { value: 'yes', label: 'Yes' },
                    { value: 'no', label: 'No' }
                ]

            },
            {
                type: 'select',
                id: 'blog_overlay',
                label: 'Image Overlay Type',
                value: $this.attr('overlay'),
                options: [
                    { value: 'none', label: 'No Icon (only image clickable)' },
                    { value: 'permalink', label: 'Permalink Icon' },
                    { value: 'lightbox', label: 'Lightbox Icon' },
                    { value: 'both', label: 'Permalink & Lightbox Icons' }
                ]

            },
            {
                type: 'input',
                id: 'blog_exclude',
                label: 'Exclude Posts',
                value: $this.attr('exclude'),
                description: 'Please add post IDs separated by comma. Example: 125,1,65'
            },
            {
                type: 'select',
                id: 'blog_order',
                label: 'Order Type',
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
                type: 'number',
                id: 'blog_skip',
                std: 0,
                label: 'Skip Count',
                value: $this.attr('skip'),
                description: 'Prevents duplication of posts If you use Blog element multiple times on this page.'
            },
            {
                type: 'input',
                id: 'blog_class',
                label: 'Extra Class',
                value: $this.attr('extra_class')
            }
            ];


            show_blox_form('Edit Blog', form_element, function($form){
                $this.attr('title', jQuery('#blog_title').val());
                $this.attr('blog_type', jQuery('#blog_type').val());
                $this.attr('categories', jQuery('#blog_categories').val());
                $this.attr('content', jQuery('#blog_content').val());
                $this.attr('readmore', jQuery('#blog_readmore').val());
                $this.attr('count', jQuery('#blog_count').val());
                $this.attr('pager', jQuery('#blog_pager').val());
                $this.attr('ignoresticky', jQuery('#blog_ignoresticky').val());
                $this.attr('overlay', jQuery('#blog_overlay').val());
                $this.attr('exclude', jQuery('#blog_exclude').val());
                $this.attr('order', jQuery('#blog_order').val());
                $this.attr('extra_class', jQuery('#blog_class').val());

                var el_val = jQuery('#blog_categories').val();
                if( el_val=='categories' ){
                    var sval = jQuery('#blox_new_cats').select2('val');
                    var rval = '';
                    for (var i = 0; i < sval.length; i++) {
                        rval += (i==0 ? sval[i] : ','+sval[i]);
                    }
                    $this.attr('blog_filter', rval);
                }
                else if( el_val=='tags' ){
                    var sval = jQuery('#blox_new_tags').select2('val');
                    var rval = '';
                    for (var i = 0; i < sval.length; i++) {
                        rval += (i==0 ? sval[i] : ','+sval[i]);
                    }
                    $this.attr('blog_filter', rval);
                }
                else if( el_val=='format' ){
                    var sval = jQuery('#blox_new_formats').select2('val');
                    var rval = '';
                    for (var i = 0; i < sval.length; i++) {
                        rval += (i==0 ? sval[i] : ','+sval[i]);
                    }
                    $this.attr('blog_filter', rval);
                }
                else{
                    $this.attr('blog_filter', '');
                }

            },
            {
                target: $this,
                skin: true,
                visibility: true
            });
            
            
            jQuery('#blog_categories').parent().append( '<span class="ajax_spinner"></span>' );
            jQuery.post( ajaxurl, { 'action':'get_blox_element_blog', 'filter': jQuery('#blog_categories').val(), 'value': $this.attr('blog_filter') }, function(data){
                if( data != "-1" ){
                    jQuery('#blog_categories').parent().find('.ajax_spinner').remove();

                    jQuery('#blox_elem_multi_category').replaceWith( jQuery(data).find('#blox_new_cats') );
                    jQuery('#blox_elem_multi_tags').replaceWith( jQuery(data).find('#blox_new_tags') );
                    jQuery('#blox_elem_multi_formats').replaceWith( jQuery(data).find('#blox_new_formats') );
                    jQuery('#blox_new_cats').select2({ placeholder: 'Select Categories' });
                    jQuery('#blox_new_tags').select2({ placeholder: 'Select Tags' });
                    jQuery('#blox_new_formats').select2({ placeholder: 'Select Post Formats' });

                    jQuery('#s2id_blox_new_cats, #s2id_blox_new_tags, #s2id_blox_new_formats').parent().hide();

                    jQuery('#blog_categories').unbind('change')
                        .change(function(){
                            var value = jQuery('#blog_categories').val();
                            jQuery('#s2id_blox_new_cats, #s2id_blox_new_tags, #s2id_blox_new_formats').parent().hide();
                            if( value=='categories' ){
                                jQuery('#s2id_blox_new_cats').parent().show();
                            }
                            else if( value=='tags' ){
                                jQuery('#s2id_blox_new_tags').parent().show();
                            }
                            else if( value=='format' ){
                                jQuery('#s2id_blox_new_formats').parent().show();
                            }
                        });

                    jQuery('#blog_categories').change();

                }
            });
            
            // Folds
            jQuery('#blog_content').change(function(){
                if( jQuery('#blog_content').val() == 'both' ){
                    jQuery('#blog_readmore').parent().show();
                }
                else{
                    jQuery('#blog_readmore').parent().hide();
                }
            });
            jQuery('#blog_content').change();
            
        });
			
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_blog();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}

