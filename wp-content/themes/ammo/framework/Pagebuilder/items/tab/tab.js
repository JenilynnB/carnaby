function get_blox_element_tab_item($attrs, $content){
    $actions_top = '<div class="blox_action_buttons"> \
                            <a href="javascript:;" class="tab_edit_current"><i class="fa-pencil"></i></a> \
                            <a href="javascript:;" class="tab_clone_current"><i class="fa-copy"></i></a> \
                            <a href="javascript:;" class="tab_remove_current"><i class="fa-times"></i></a> \
                            <a href="javascript:;" class="tab_add_blox_elem" data-rel="top"><i class="fa-plus"></i> Add Element</a> \
                    </div>';
    $actions_bottom = '<div class="blox_action_buttons"> \
                            <a href="javascript:;" class="tab_add_blox_elem" data-rel="bottom"><i class="fa-plus"></i> Add Element</a> \
                    </div>';

    return '<div class="blox_tab_item" id="tab'+guid()+'" icon="'+($attrs!=undefined ? $attrs.find('icon').html() : '')+'" title="'+($attrs!=undefined ? $attrs.find('title').html() : 'Tab item')+'"> \
                '+$actions_top+' \
                <div class="blox_tab_item_content blox_container">'+($content!=undefined ? $content : '')+'</div> \
                '+$actions_bottom+' \
            </div>';
}

function get_blox_element_tab($content, $attrs){
    actions = '<div class="blox_item_actions blox_tab_general_action"> \
                    <a href="javascript:;" class="fa-pencil action_edit"></a> \
                    <a href="javascript:;" class="fa-copy action_clone"></a> \
                    <a href="javascript:;" class="fa-times action_remove"></a> \
                </div>';
	
    return '<div class="blox_item blox_tab" '+($attrs!=undefined ? $attrs : '')+'>'
                + actions
                + '<div class="blox_tab_obj">'
                + ($content!=undefined ? $content : get_blox_element_tab_item())
                + '</div> \
			</div>';
}

function parse_shortcode_tab($content){
    $content = wp.shortcode.replace( 'blox_tab', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            attrs += key+'="'+value+'" ';
        })
        return get_blox_element_tab(data.content, attrs);
    });
	
    $content = wp.shortcode.replace( 'blox_tab_item', $content, function(data){
        var title = 'Tab item';
        var icon = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( key=='title' ){
                title = value;
            }
            else if( key=='icon' ){
                icon = value;
            }
        });
        return get_blox_element_tab_item(jQuery('<div></div>').append('<title>'+title+'</title>').append('<icon>'+icon+'</icon>'), data.content);
    });
    return $content;
}

function revert_shortcode_tab($content){
    $content.find('.blox_tab_item').each(function(index){
        jQuery(this).replaceWith('[blox_tab_item icon="'+jQuery(this).attr('icon')+'" title="'+jQuery(this).attr('title')+'"]'+jQuery(this).find('.blox_tab_item_content').html()+'[/blox_tab_item]');
    });
    $content.find('.blox_tab').each(function(){
        //jQuery(this).find('.blox_tab_obj').find('> ul').remove();
        jQuery(this).find('.blox_tab_obj').find('.ui-tabs-nav').remove();
        attr = '';
        var temp_val = '';

        temp_val = jQuery(this).attr('title')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' title="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('nav_style')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' nav_style="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('animation')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' animation="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('extra_class')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' extra_class="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('visibility')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' visibility="'+temp_val+'"';
        }

        temp_val = jQuery(this).attr('skin')+'';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' skin="'+temp_val+'"';
        }
        
        jQuery(this).replaceWith('[blox_tab'+attr+']'+jQuery(this).find('.blox_tab_obj').html()+'[/blox_tab]');
    });
    return $content;
}

function add_event_blox_element_tab(){
    jQuery('.blox_tab').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_tab_obj > ul').remove();
        $this.find('.blox_tab_obj').prepend('<ul></ul>');
        $this.find('.blox_tab_obj > .blox_tab_item').each(function(){
            li = '<li><a href="#'+jQuery(this).attr('id')+'">'+(jQuery(this).attr('icon')!='undefined' && jQuery(this).attr('icon')!='' ? '<i class="'+jQuery(this).attr('icon')+'"></i>' : '')+jQuery(this).attr('title')+'</a></li>';
            $this.find('.blox_tab_obj > ul').append(li);
        });
        li = '<li class="new-tab-li"><a href="#new-tab" class="new-tab-button"><i class="fa-align-left"></i> Add Tab</a></li>';
        $this.find('.blox_tab_obj > ul').append(li);
		
        var $tabs = null;
        if( $this.find('.blox_tab_obj').data('ui-tabs') ){
            var current_index = 0;
            $this.find('.ui-tabs-nav > li').each(function(indyx){
                if( jQuery(this).hasClass('ui-tabs-active') ){
                    current_index = indyx;
                }
            });
            $this.find('.blox_tab_obj').tabs('destroy');
            $tabs = $this.find('.blox_tab_obj').tabs({
                active:current_index
            });
        }
        else{
            $tabs = $this.find('.blox_tab_obj').tabs({
                active:0
            });
        }
        $tabs.find( ".ui-tabs-nav" ).sortable({
            axis: "x",
            items: '> li:not(.new-tab-li)',
            stop: function() {
                $tabs.tabs( "refresh" );
            }
        });

        $this.find('.ui-tabs-nav > li > a').click(function(event){
            if( jQuery(this).parent().hasClass('tabitemselected') ){
                var aindex = $this.find('.ui-tabs-nav a').index(jQuery(this));
                if( aindex+1 < $this.find('.ui-tabs-nav a').length ){
                    $this.find('.tab_edit_current').eq(aindex).trigger('click');
                }
            }
            $this.find('.ui-tabs-nav > li').removeClass('tabitemselected');
            jQuery(this).parent().addClass('tabitemselected');
        });


        $this.find('.blox_tab_obj > ul').find('.new-tab-button').unbind('click')
        .click(function(){
            var $new_item = jQuery(get_blox_element_tab_item());
            $this.find('.blox_tab_obj').append($new_item);
            li = '<li><a href="#'+$new_item.attr('id')+'">'+$new_item.attr('title')+'</a></li>';
            jQuery(this).parent().before(li);

            refresh_blox_events(function(){
                $tabs.tabs({ active:$this.find('.blox_tab_obj > ul > li').length-2 });
            });
        });
		
		
        $this.find('.tab_add_blox_elem').unbind('click')
        .click(function(){
            var current_index = 0;
            $this.find('.ui-tabs-nav > li').each(function(indyx){
                if( jQuery(this).hasClass('ui-tabs-active') ){
                    current_index = indyx;
                }
            });

            $context = jQuery(this).parent().parent().find('.blox_tab_item_content');
            add_blox_element($context, jQuery(this).attr('data-rel'), function(){
                $tabs.tabs({ active:current_index });
            });

            
        });
        $this.find('.tab_edit_current').unbind('click')
        .click(function(){
            var $context = jQuery(this).parent().parent();
            var $li_context = $context.parent().find('> ul a[href="#'+$context.attr('id')+'"]').parent();
				
            var $modal = jQuery('<div id="blox_modal"></div>');
            $modal.append('<div class="blox_modal_content"> \
								<p> \
									<label>Icon</label> \
									<input type="text" id="blox_tab_section_icon" value="'+$context.attr('icon')+'" /> \
									<a href="javascript: themeton_get_font(jQuery(\'#blox_tab_section_icon\'));" class="button">Browse icons...</a> \
								</p> \
								<p> \
									<label>Title</label> \
									<input type="text" id="blox_tab_section_title" value="'+$context.attr('title')+'" /> \
								</p> \
							</div>');
            $modal.dialog({
                'title'			: 'Edit Tab Section',
                'dialogClass'   : 'wp-dialog blox_dialog',
                'modal'         : true,
                'width'			: '400',
                'buttons'       : [
                {
                    'text'	: 'Save',
                    'class' : 'button-primary',
                    'click'	: function(){
                        $context.attr('title', jQuery('#blox_tab_section_title').val());
                        $context.attr('icon', jQuery('#blox_tab_section_icon').val());
                        $li_context.find('> a').html('<i class="'+jQuery('#blox_tab_section_icon').val()+'"></i> '+jQuery('#blox_tab_section_title').val());
                        jQuery(this).dialog('close');
                        jQuery(this).dialog('destroy').remove();
                    }
                },
                {
                    'text'	: 'Cancel',
                    'class' : 'button',
                    'click'	: function(){
                        jQuery(this).dialog('close');
                        jQuery(this).dialog('destroy').remove();
                    }
                }
                ]
            });
            $modal.dialog('open');
        });
        $this.find('.tab_clone_current').unbind('click')
        .click(function(){
            $context = jQuery(this).parent().parent();
            $li_context = $context.parent().find('> ul a[href="#'+$context.attr('id')+'"]').parent();
				
            newid = guid();
				
            $new_li = $li_context.clone();
            $new_li.attr('href', '#tab'+newid);
            $li_context.after( $new_li );
				
            $new_cont = $context.clone();
            $new_cont.attr('id', 'tab'+newid);
            $context.after( $new_cont );
				
            refresh_blox_events();
        });
        $this.find('.tab_remove_current').unbind('click')
        .click(function(){
            $context = jQuery(this).parent().parent();
            $context.parent().find('> ul a[href="#'+$context.attr('id')+'"]').parent().remove();
            $context.remove();
            $tabs.tabs( "refresh" );
        });
			
		
		
        $this.find('.blox_tab_general_action .action_edit').unbind('click')
        .click(function(){


            var form_element = [
                        {
                            type: 'input',
                            id: 'blox_tab_widget_title',
                            label: 'Title',
                            value: $this.attr('title')
                        },
                        {
                            type: 'select',
                            id: 'blox_tab_option_type',
                            label: 'Tab Header style',
                            value: $this.attr('nav_style'),
                            options: [
                                { value: 'nav-tabs', label: 'Default' },
                                { value: 'nav-tabs nav-justified', label: 'Default + Justified' },
                                { value: 'nav-pills', label: 'Navigation Pills' },
                                { value: 'nav-pills nav-justified', label: 'Navigation Pills + Justified' },
                                { value: 'nav-pills nav-stacked', label: 'Navigation Pills + Stacked' }
                            ]
                        }
                    ];

            show_blox_form('Edit Tab Element', form_element, function($form){
                $this.attr('title', jQuery('#blox_tab_widget_title').val());
                $this.attr('nav_style', jQuery('#blox_tab_option_type').val());
            },
            {
                target: $this,
                extra_field: true,
                skin: true,
                visibility: true
            });
        });
        $this.find('.blox_tab_general_action .action_clone').unbind('click')
        .click(function(){
            $this.after( $this.clone() );
            refresh_blox_events();
        });
        $this.find('.blox_tab_general_action .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
		
    });
}
