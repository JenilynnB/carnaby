function orderby_onepages(){
    var $onepage = jQuery('#onepages');
    var $onepage_names = jQuery('#onepages_names');
    var $onepage_links = jQuery('#onepages_links');
    $onepage.val('');
    $onepage_names.val('');
    $onepage_links.val('');
    var $page_ids = '';
    var $page_names = '';
    var $page_links = '';
    jQuery('#onepage_container').find('.onepage_item').each(function(index){
        $page_ids += index>0 ? ','+jQuery(this).attr('data') : jQuery(this).attr('data');
        $page_names += index>0 ? '^'+jQuery(this).find('span').text() : jQuery(this).find('span').text();
        $page_links += index>0 ? '^'+jQuery(this).attr('data-link') : jQuery(this).attr('data-link');
    });
    $onepage.val($page_ids);
    $onepage_names.val($page_names);
    $onepage_links.val($page_links);
}


var up_less_editor = null;

jQuery(function(){

    jQuery('#up_less_editor').parent().css({ 'width': '100%' });
    jQuery('#pmeta_less_option .hndle').click(function(){
        if( up_less_editor==null ){
            up_less_editor = CodeMirror.fromTextArea(document.getElementById("up_less_editor"), {
                lineNumbers : true,
                matchBrackets : true,
                mode: "text/x-less",
                theme: "monokai"
            });
            up_less_editor.setSize("100%", "500px");
        }
    });
    jQuery('#publish').click(function(){
        if( up_less_editor!=null ){
            jQuery('#up_less_editor').val( less_editor.getValue() );
        }
    });



    jQuery('#title_show').change(function(){
        if( this.value == '1' ){
            jQuery('#title_options').slideDown();
        }
        else{
            jQuery('#title_options').slideUp();
        }
    });
    jQuery('#title_show').change();
	

    if( jQuery('#page_template').val() == 'page-one-page.php' ){
        jQuery('#pmeta_onepage').slideDown();
        jQuery('#postdivrich').hide();
        jQuery('#blox_contentbuilder').hide();
    }
    else if( jQuery('#page_template').val() == 'page-blank.php' ){
        jQuery('#pmeta_page').hide();
    }
    else if( jQuery('#page_template').val() == 'page-ultimate.php' ){
        jQuery('#pmeta_ultimate_page').show();
        jQuery('#pmeta_less_option').show();
    }

    jQuery('#pmeta_less_option').addClass('closed');

    jQuery('#page_template').change(function(){
        if( this.value == 'page-one-page.php' ){
            jQuery('#pmeta_onepage').slideDown();
            jQuery('#pmeta_page').slideDown();
            jQuery('#postdivrich').hide();
            jQuery('#blox_contentbuilder').hide();
        }
        else{
            jQuery('#pmeta_onepage').slideUp();
            jQuery('#pmeta_page').slideDown();
            
            if( get_cookie_editor_mode() ){
                jQuery('#blox_contentbuilder').slideDown();
            }
            else{
                jQuery('#postdivrich').slideDown();
            }
        }

        if( this.value == 'page-blank.php' ){
            jQuery('#pmeta_page').hide();
        }

        // Ultimate page Option
        if( this.value == 'page-ultimate.php' ){
            jQuery('#pmeta_ultimate_page').slideDown();
            jQuery('#pmeta_less_option').slideDown();
        }
        else{
            jQuery('#pmeta_ultimate_page').hide();
            jQuery('#pmeta_less_option').hide();
        }

    });
	
    var $onepage = jQuery('#onepages');
    var $onepage_names = jQuery('#onepages_names');
    var $onepage_links = jQuery('#onepages_links');
    var $splited_pages = $onepage.length>0 ? $onepage.val().split(',') : [];
    var $splited_names = $onepage_names.length>0 ? $onepage_names.val().split('^') : [];
    var $splited_links = $onepage_links.length>0 ? $onepage_links.val().split('^') : [];
    for(var i=0; i<$splited_pages.length; i++){
        if( $splited_pages!='' ){
            html = get_onepage_item_html( $splited_pages[i], $splited_names[i], (typeof $splited_links[i]!=='undefined' ? $splited_links[i] : '') );
            jQuery('#onepage_container').append(html);
        }
    }

    var $op_container = jQuery('#onepage_container');
    var opform = '<div id="one_page_custom_link"> \
                        <p> \
                                <label>Custom Title</label> \
                                <input type="text" id="op_custom_title" /> \
                        </p> \
                        <p> \
                                <label>Custom Link</label> \
                                <input type="text" id="op_custom_link" /> \
                        </p> \
                        <p><a href="javascript:;" class="button" id="op_custom_link_add">Add</a></p> \
                </div>';
    $op_container.before(opform);

    jQuery('#button_op_custom_link').click(function(){
        jQuery('#one_page_custom_link').toggle('fast');
    });

    jQuery('#op_custom_link_add').click(function(){
        if( jQuery('#op_custom_title').val()!='' && jQuery('#op_custom_link').val()!='' ){
            html = get_onepage_item_html( 0, jQuery('#op_custom_title').val(), jQuery('#op_custom_link').val() );
            jQuery('#onepage_container').append(html);
            jQuery('#op_custom_title').val('');
            jQuery('#op_custom_link').val('');
            orderby_onepages();
        }
    });

    jQuery('#onpage_allpages').change(function(){
        if( jQuery(this).val()!='0' ){
            jQuery('#button_onepage_add').trigger('click');
            jQuery(this).val('0').change();
        }
    });
	
    jQuery('#onepage_container').sortable({
        update: function( event, ui ){
            orderby_onepages();
        }
    });
    jQuery('#button_onepage_add').unbind('click')
    .click(function(){
        $opage = jQuery('#onpage_allpages');
        html = get_onepage_item_html( $opage.val(), $opage.find("option[value='"+$opage.val()+"']").text(), $opage.find("option[value='"+$opage.val()+"']").attr('data-link') );
        jQuery('#onepage_container').append(html);
        orderby_onepages();
    });




    jQuery('#up_layout').change(function(){
        if( this.value == 'boxed' ){
            jQuery('#option_wrapper_up_margin_top,#option_wrapper_up_margin_bottom,#option_wrapper_up_background_img').slideDown();
        }
        else{
            jQuery('#option_wrapper_up_margin_top,#option_wrapper_up_margin_bottom,#option_wrapper_up_background_img').slideUp();
        }
    });
    jQuery('#up_layout').change();


    jQuery('#up_header_height').change(function(){
        jQuery('#less_header-height').val( this.value+'px' );
    });


    jQuery('#up_menu_font,#up_heading_font,#up_body_font').change(function(){
        var $this = jQuery(this);
        if( $this.parent().find('.google_fonts_preview').length<1 ){
            $this.parent().append('<span class="google_fonts_preview">Preview Text</span>');
        }
        jQuery('.gf_'+$this.attr('id')).remove();
        if( this.value!='default' ){
            jQuery('head').append('<link href="http://fonts.googleapis.com/css?family='+ this.value +'" rel="stylesheet" type="text/css" class="gf_'+ $this.attr('id') +'">');
            $this.parent().find('.google_fonts_preview').css('font-family', this.value);
        }
        else{
            $this.parent().find('.google_fonts_preview').attr('style');
        }
    });

    /* Font actions */
    jQuery('#up_body_font').change(function(){
        jQuery('#less_base-font-body').val( this.value=='default' ? 'arial' : this.value );
    });
    jQuery('#up_heading_font').change(function(){
        jQuery('#less_base-font-heading').val( this.value=='default' ? 'arial' : this.value );
    });
    jQuery('#up_menu_font').change(function(){
        jQuery('#less_base-font-menu').val( this.value=='default' ? 'arial' : this.value );
    });

    jQuery('#up_menu_font').change();
    jQuery('#up_body_font').change();
    jQuery('#up_heading_font').change();
    jQuery('#less_grid-columns').attr('readonly', 'readonly');

    jQuery('#pmeta_less_option').find('.inside').prepend('<div class="page_option_fieldset"><a href="javascript:;" id="button_reset_less" class="button-primary">Reset LESS Variables</a></div>');
    jQuery('#button_reset_less').click(function(){
        jQuery(this).parent().append('<span class="spinner" style="display: inline; float:left;"></span>');
        jQuery.post( ajaxurl, { action: 'up_reset_less_options', post_id: jQuery('#post_ID').val() },
        function(data){
            window.location.reload();
        });
    });



    jQuery('#up_header').change(function(){
        if( this.value == '1' ){ jQuery('#up_header_group').slideDown(); }
        else{ jQuery('#up_header_group').slideUp(); }
    });
    jQuery('#up_header').change();
    
	
});


function get_onepage_item_html($page_id, $page_title, $page_link){
    var pid = '';
    if($page_id!=0) { pid = ' ('+$page_id+')'; }
    return '<div class="onepage_item" data="'+$page_id+'" data-link="'+$page_link+'"> \
                <span>'+$page_title+pid+'</span> \
                <a href="javascript:;" onclick="jQuery(this).parent().remove(); orderby_onepages();" class="fa-times"></a> \
                <a href="'+ajaxurl.replace('admin-ajax.php','post.php?post='+$page_id+'&action=edit')+'" target="_blank" class="op_edit fa-pencil"></a> \
            </div>';
}
