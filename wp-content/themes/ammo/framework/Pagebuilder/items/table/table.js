
function get_blox_element_table($content, $attrs){
	
    return '<div class="blox_item blox_table" '+($attrs!=undefined ? $attrs : '')+'> \
                    <div class="blox_item_title"> \
                        <i class="fa-table"></i> \
                        <span class="blox_item_title">Table</span> \
                        '+ get_blox_actions() +' \
                    </div> \
                    <div class="blox_item_content" style="display: none;">'
                    + '<div class="blox_item_widget_title">'+($content!=undefined && $content.find('.title').length>0 ? $content.find('.title').html() : '')+'</div>'
                    + '<div class="blox_item_table_wrapper">'+($content!=undefined && $content.find('.table').length>0 ? $content.find('.table').html() : '')+'</div>'
                    + '<div class="blox_item_button_icon">'+($content!=undefined && $content.find('.button_icon').length>0 ? $content.find('.button_icon').html() : '')+'</div>'
                    + '<div class="blox_item_animation">'+($content!=undefined && $content.find('.animation').length>0 ? $content.find('.animation').html() : '')+'</div>'
                    + '<div class="blox_item_extra_class">'+($content!=undefined && $content.find('.extra_class').length>0 ? $content.find('.extra_class').html() : '')+'</div>'
                    + '<div class="blox_item_skin">'+($content!=undefined && $content.find('.skin').length>0 ? $content.find('.skin').html() : '')+'</div>'
                    + '<div class="blox_item_visible">'+($content!=undefined && $content.find('.visibility').length>0 ? $content.find('.visibility').html() : '')+'</div>'
                    +'</div> \
			</div>';
}


function parse_shortcode_table($content){
    // parse table cell
    $content = wp.shortcode.replace( 'blox_table_cell', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += ' '+key+'="'+value+'"';
            }
        })
        return '<div class="blox_option_col"'+attrs+'>'+data.content+'</div>';
    });

    // parse table row
    $content = wp.shortcode.replace( 'blox_table_row', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += ' '+key+'="'+value+'"';
            }
        })
        return '<div class="blox_option_row"'+attrs+'>'+data.content+'</div>';
    });

    // parse table
    $content = wp.shortcode.replace( 'blox_table', $content, function(data){
        var attrs = '';
        jQuery.each(data.attrs.named, function(key, value){
            if( value!=undefined && value!='undefined' && value!='' ){
                attrs += '<div class="'+key+'">'+value+'</div>';
            }
        })
        attrs += '<div class="table"><div class="blox_option_table">'+data.content+'</div></div>';
		
        return get_blox_element_table(jQuery('<div class="tmp_pass">'+attrs+'</div>'));
    });

    return $content;
}

function revert_shortcode_table($content){
    // option cell
    $content.find('.blox_option_col').each(function(){
        var attr = '';
        var temp_val = '';

        temp_val = jQuery(this).attr('type') + '';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' type="'+temp_val+'"';
        }
        
        jQuery(this).replaceWith('[blox_table_cell'+attr+']'+jQuery(this).html()+'[/blox_table_cell]');
    });

    // option row
    $content.find('.blox_option_row').each(function(){
        var attr = '';
        var temp_val = '';

        temp_val = jQuery(this).attr('type') + '';
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' type="'+temp_val+'"';
        }
        
        jQuery(this).replaceWith('[blox_table_row'+attr+']'+jQuery(this).html()+'[/blox_table_row]');
    });

    $content.find('.blox_table').each(function(){
        var attr = '';
        var temp_val = '';

        temp_val = jQuery(this).find('.blox_item_widget_title').html();
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' title="'+temp_val+'"';
        }

        temp_val = jQuery(this).find('.blox_item_button_icon').html();
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' button_icon="'+temp_val+'"';
        }

        temp_val = jQuery(this).find('.blox_item_animation').html();
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' animation="'+temp_val+'"';
        }

        temp_val = jQuery(this).find('.blox_item_extra_class').html();
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' extra_class="'+temp_val+'"';
        }

        temp_val = jQuery(this).find('.blox_item_skin').html();
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' skin="'+temp_val+'"';
        }

        temp_val = jQuery(this).find('.blox_item_visible').html();
        if( temp_val!='undefined' && temp_val!='' ){
            attr += ' visibility="'+temp_val+'"';
        }
		
        jQuery(this).replaceWith('[blox_table'+attr+']'+jQuery(this).find('.blox_option_table').html()+'[/blox_table]');
    });
    return $content;
}


function add_event_blox_element_table(){
	
    jQuery('.blox_table').each(function(){
        var $this = jQuery(this);
		
        $this.find('.blox_item_actions .action_edit').unbind('click')
        .click(function(){

            var form_element = [
                        {
                            type: 'input',
                            id: 'blox_el_option_title',
                            label: 'Title',
                            value: $this.find('.blox_item_widget_title').html()
                        },
                        {
                            type: 'icon',
                            id: 'blox_el_option_icon',
                            label: 'Table Button Row Icon',
                            value: $this.find('.blox_item_button_icon').html()
                        }
                    ];

            $this.attr('animation', $this.find('.blox_item_animation').html());
            $this.attr('extra_class', $this.find('.blox_item_extra_class').html());
            $this.attr('skin', $this.find('.blox_item_skin').html());
            $this.attr('visibility', $this.find('.blox_item_visible').html());

            show_blox_form('Edit Table Element', form_element, function($form){
                $this.find('.blox_item_widget_title').html( jQuery('#blox_el_option_title').val() );
                $this.find('.blox_item_button_icon').html( jQuery('#blox_el_option_icon').val() );

                $this.find('.blox_item_animation').html( $this.attr('animation') );
                $this.find('.blox_item_extra_class').html( $this.attr('extra_class') );
                $this.find('.blox_item_skin').html( $this.attr('skin') );
                $this.find('.blox_item_visible').html( $this.attr('visibility') );

                var table_html = '';
                var $table = jQuery('#blox_popup_window').find('.blox_display_table');
                var row_count = $table.find('.blox_display_row').length;

                $table.find('.blox_display_row').each(function(row_index){
                    if( row_index!=0 && row_index!=row_count-1 ){
                        var cell_count = jQuery(this).find('.blox_display_cell').length;

                        table_html += '<div class="blox_option_row" type="'+jQuery(this).find('.blox_display_cell').eq(0).find('select').val()+'">';
                        jQuery(this).find('.blox_display_cell').each(function(index){
                            if( index!=0 && index!=cell_count-1 ){
                                var coltype = $table.find('.blox_display_row').eq(0).find('.blox_display_cell').eq(index).find('select').val();
                                table_html += '<div class="blox_option_col" type="'+coltype+'">'+jQuery(this).find('textarea').val()+'</div>';
                            }
                        });
                        table_html += '</div>';
                    }
                });

                table_html = '<div class="blox_option_table">'+table_html+'</div>';
                $this.find('.blox_item_table_wrapper').html(table_html);
            },
            {
                target: $this,
                extra_field: true,
                skin: true,
                visibility: true
            });

            jQuery("#blox_popup_window").find('.blox_popup_wrapper').after('<div class="blox_popup_table_wrapper"></div>');

            var table_html = '<p> \
                				___ <b>TABLE BUILDER</b> ___<br> \
								Start by adding columns and rows, then add content and styling to each.<br>\
                                _ <b>Default Row</b>, you can add here any type of content including html code as ul li elements.<br> \
                                _ <b>Pricing Row</b>, you should add your content as this structure. Price,Currency,Time Ex: <em>5,$,montly</em><br> \
                                _ <b>Button Row</b>, you should add your button text and link as this format. Link text + comma (,) + url. Ex: <em>Purchase now,http://themeforest.net</em><br> \
                                _ <b>Description Column</b>, you should add bunch text on default row.\
                				<a href="javascript:;" class="button-primary" id="blox_table_add_column" style="float:right;">Add Table Column</a> \
                				<a href="javascript:;" class="button-primary" id="blox_table_add_row" style="float:right; margin-right:10px;">Add Table Row</a> \
                			  </p>';

            var col_cell = '<div class="blox_display_cell"> \
		  								<select class="blox_display_col_style"> \
		  									<option value="default">Default Column</option> \
		  									<option value="highlight">Highlight Column</option> \
		  									<option value="description">Description Column</option> \
		  									<option value="center">Center Text Column</option> \
		  								</select> \
		  							</div>';

            var row_cell = '<div class="blox_display_cell"> \
	  								<div class="blox_display_content"></div> \
	  								<textarea class="blox_display_textarea"></textarea> \
	  							</div>';

            var row_head = '<div class="blox_display_cell"> \
                                    <a href="javascript:;" class="row-move-handler"><i class="fa-arrows"></i></a> \
	  								<select class="blox_display_row_style"> \
	  									<option value="default">Default Row</option> \
	  									<option value="header">Heading Row</option> \
	  									<option value="price">Pricing Row</option> \
	  									<option value="button">Button Row</option> \
	  								</select> \
	  							</div>';

            var row_action = '<div class="blox_display_cell cell_row_action"> \
		  								<a href="javascript:;" class="fa-times"></a> \
		  							</div>';

            var col_action = '<div class="blox_display_cell cell_col_action"> \
		  								<a href="javascript:;" class="fa-times"></a> \
		  							</div>';

            table_html += '<div class="blox_display_table"> \
			  						<div class="blox_display_row"> \
			  							<div class="blox_display_cell"></div> \
			  							<div class="blox_display_cell"></div> \
			  						</div> \
			  						<div class="blox_display_row"> \
			  							<div class="blox_display_cell"></div> \
			  							<div class="blox_display_cell"></div> \
			  						</div> \
			  					</div>';

            jQuery("#blox_popup_window").find('.blox_popup_table_wrapper').append(table_html);
            

            function add_events_row_action(){
                jQuery('#blox_popup_window').find('.cell_row_action a').unbind('click')
                .click(function(){
                    jQuery(this).parent().parent().remove();
                });

                jQuery('#blox_popup_window').find('.cell_col_action a').unbind('click')
                .click(function(){
                    var $table = jQuery('#blox_popup_window').find('.blox_display_table');
                    var item_index = jQuery(this).parent().parent().find('.blox_display_cell').index(jQuery(this).parent());

                    $table.find('.blox_display_row').each(function(index){
                        jQuery(this).find('.blox_display_cell').eq(item_index).remove();
                    });
                });

                jQuery('#blox_popup_window').find('.blox_display_table').find('.blox_display_row').eq(0).addClass('row-move-lock');
                jQuery('#blox_popup_window').find('.blox_display_table').sortable({
                    axis: "y",
                    handle: ".row-move-handler",
                    items: ".blox_display_row:not(:first-child,:last-child)"
                });
            }
                
            jQuery('#blox_table_add_row').unbind('click')
            .click(function(){
                var $table = jQuery('#blox_popup_window').find('.blox_display_table');
                var $last_row = $table.find('.blox_display_row:last-child');
                var $col_count = $table.find('.blox_display_row:last-child .blox_display_cell').length-2;

                var html = row_head;
                for(var i=0; i < $col_count; i++){
                    html += row_cell;
                }
                html += row_action;

                html = '<div class="blox_display_row">'+html+'</div>';
                $last_row.before(html);

                add_events_row_action();
            });

            jQuery('#blox_table_add_column').unbind('click')
            .click(function(){
                var $table = jQuery('#blox_popup_window').find('.blox_display_table');
                var row_count = $table.find('.blox_display_row').length;

                $table.find('.blox_display_row').each(function(index){
                    $last_item = jQuery(this).find('.blox_display_cell:last-child');
                    if( index == 0 ){
                        $last_item.before(col_cell);
                    }
                    else if( index == row_count-1 ){
                        $last_item.before(col_action);
                    }
                    else{
                        $last_item.before(row_cell);
                    }
                });

                add_events_row_action();
            });
                
            if( $this.find('.blox_option_table').length > 0 && $this.find('.blox_option_table .blox_option_row').length > 0 ){
                var $table = jQuery('#blox_popup_window').find('.blox_display_table');
                var $option_table = $this.find('.blox_option_table');

                $option_table.find('.blox_option_row').each(function(row_index){
                    var cell_count = jQuery(this).find('.blox_option_col').length;

                    // add row
                    jQuery('#blox_table_add_row').trigger('click');
                    $table.find('.blox_display_row').eq(row_index+1).find('.blox_display_cell').eq(0).find('select').val(jQuery(this).attr('type'));
                    // add column
                    if( row_index==0 ){
                        for(var i=0; i<cell_count; i++){
                            jQuery('#blox_table_add_column').trigger('click');
                        }
                    }
                    // set cell value
                    jQuery(this).find('.blox_option_col').each(function(index){
                        $table.find('.blox_display_row').eq(0).find('.blox_display_cell').eq(index+1).find('select').val(jQuery(this).attr('type'));
                        var $textarea = $table.find('.blox_display_row').eq(row_index+1).find('.blox_display_cell').eq(index+1).find('textarea');
                        $textarea.val( jQuery(this).html() );
                    });

                });
            }
            else{
                jQuery('#blox_table_add_column').trigger('click');
                jQuery('#blox_table_add_column').trigger('click');
                jQuery('#blox_table_add_column').trigger('click');

                jQuery('#blox_table_add_row').trigger('click');
                jQuery('#blox_table_add_row').trigger('click');
                jQuery('#blox_table_add_row').trigger('click');
            }


        });
		
        $this.find('.blox_item_actions .action_clone').unbind('click')
        .click(function(){
            $this.after($this.clone());
            add_event_blox_element_table();
        });
			
        $this.find('.blox_item_actions .action_remove').unbind('click')
        .click(function(){
            $this.remove();
        });
    });
	
}
