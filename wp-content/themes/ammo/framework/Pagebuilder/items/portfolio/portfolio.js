function get_blox_element_portfolio($content, $attrs){
    
    return '<div class="blox_item blox_portfolio" '+($attrs!=undefined ? $attrs : '')+'> \
            <div class="blox_item_title"> \
                <i class="fa-th"></i> \
                <span class="blox_item_title">Portfolio</span> \
                '+ get_blox_actions() +' \
            </div> \
            <div class="blox_item_content" style="display: none;"></div> \
        </div>';
    
}


function parse_shortcode_portfolio($content){
    $content = wp.shortcode.replace( 'blox_portfolio', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
	
            if( key=='title' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }
            if( key=='style' && value!='undefined' ){
                attrs += 'portfolio_style="'+value+'" ';
            }
            if( key=='categories' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }
            if( key=='count' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }     
            if( key=='height' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }     
            if( key=='pager' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }
            if( key=='filter' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }
            if( key=='ignoresticky' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }
            if( key=='content_type' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }     
            if( key=='overlay' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }     
            if( key=='exclude' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }     
            if( key=='order' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }     
            if( key=='extra_class' && value!='undefined' ){
                attrs += key+'="'+value+'" ';
            }
            if( key=='element_style' && value!='undefined' ){
                attrs += 'skin="'+value+'" ';
            }
            if( key=='visibility' && value!='undefined' ){
                attrs += 'visibility="'+value+'" ';
            }

            
        })
        return get_blox_element_portfolio(jQuery('<div class="tmp_pass"></div>'), attrs);
    });
    return $content;
}


function revert_shortcode_portfolio($content){
    $content.find('.blox_portfolio').each(function(){
        attr = '';
        var temp_val = '';

        temp_val = jQuery(this).attr('title')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' title="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('portfolio_style')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' style="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('categories')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' categories="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('count')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' count="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('height')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' height="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('pager')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' pager="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('filter')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' filter="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('ignoresticky')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' ignoresticky="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('content_type')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' content_type="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('overlay')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' overlay="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('exclude')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' exclude="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('order')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' order="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('extra_class')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' extra_class="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('skin')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' element_style="'+temp_val+'"';
        }
        temp_val = jQuery(this).attr('visibility')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' visibility="'+temp_val+'"';
        }
		
        
        jQuery(this).replaceWith('[blox_portfolio'+attr+'/]');
    });
    return $content;
}


function add_event_blox_element_portfolio(){
	
    jQuery('.blox_portfolio').each(function(){
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
                id: 'blox_element_option_style',
                label: 'Blog Style',
                value: $this.attr('portfolio_style'),
                options: [
                    { value: 'masonry2', label: '1. Masonry 2 columns' },
                    { value: 'masonry3', label: '2. Masonry 3 columns' },
                    { value: 'masonry4', label: '3. Masonry 4 columns' },
                    { value: 'grid2', label: '4. Grid 2 columns' },
                    { value: 'grid3', label: '5. Grid 3 columns' },
                    { value: 'grid4', label: '7. Grid 4 columns' }
                ]
            },
            {
                type: 'number',
                id: 'blox_element_option_height',
                label: 'Image height',
                std: 200,
                value: $this.attr('height'),
                description: 'Optional for grid layout. If you send 0 for masonry, image will show high as proportional height.'

            },
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
                id: 'blox_element_option_count',
                label: 'Post count',
                std: 8,
                value: $this.attr('count')

            },
            {
                type: 'checkbox_flat',
                id: 'blox_element_option_pager',
                label: 'Pagination',
                value: $this.attr('pager'),
                options: [
                    { value: 'no', label: 'No' },
                    { value: 'yes', label: 'Yes' }
                ]

            },
            {
                type: 'select',
                id: 'blox_element_content_type',
                label: 'Portfolio Item Style',
                value: $this.attr('content_type'),
                options: [
                    { value: 'default', label: 'Default' },
                    { value: 'with_excerpt', label: 'With Excerpt' },
                    { value: 'alternative', label: 'Alternative Style' }
                ]

            },
            {
                type: 'select',
                id: 'blox_element_option_overlay',
                label: 'Image overlay type',
                value: $this.attr('overlay'),
                options: [
                    { value: 'none', label: 'None (image with link)' },
                    { value: 'permalink', label: 'Permalink' },
                    { value: 'lightbox', label: 'Lightbox' },
                    { value: 'both', label: 'Permalink & Lightbox buttons' }
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

            show_blox_form('Edit Portfolio', form_element, function($form){
                $this.attr('title', jQuery('#blox_element_option_title').val());
                $this.attr('portfolio_style', jQuery('#blox_element_option_style').val());
                $this.attr('count', jQuery('#blox_element_option_count').val());
                $this.attr('height', jQuery('#blox_element_option_height').val());
                $this.attr('pager', jQuery('#blox_element_option_pager').val());
                $this.attr('filter', jQuery('#blox_element_option_filter').val());
                $this.attr('ignoresticky', jQuery('#blox_element_option_ignoresticky').val());
                $this.attr('content_type', jQuery('#blox_element_content_type').val());
                $this.attr('overlay', jQuery('#blox_element_option_overlay').val());
                $this.attr('exclude', jQuery('#blox_element_option_exclude').val());
                $this.attr('order', jQuery('#blox_element_option_order').val());
                $this.attr('extra_class', jQuery('#blox_element_option_class').val());

                var sval = jQuery('#blox_new_cats').select2('val');
                var rval = '';
                for (var i = 0; i < sval.length; i++) {
                    rval += (i==0 ? sval[i] : ','+sval[i]);
                }
                $this.attr('categories', rval);
            },
            {
                target: $this,
                skin: true,
                visibility: true
            });

            
            jQuery('#blox_element_option_categories').parent().append( '<span class="ajax_spinner"></span>' );
            jQuery.post( ajaxurl, { 'action':'get_blox_element_portfolio', 'value': $this.attr('categories') }, function(data){
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
            add_event_blox_element_portfolio();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}

