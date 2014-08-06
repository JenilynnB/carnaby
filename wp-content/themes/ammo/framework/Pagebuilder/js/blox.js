
function get_blox_actions(){
	return '<div class="blox_item_actions"> \
				<a href="javascript:;" class="action_edit"><i class="fa-pencil"></i></a> \
				<a href="javascript:;" class="action_clone"><i class="fa-copy"></i></a> \
				<a href="javascript:;" class="action_remove"><i class="fa-times"></i></a> \
			</div>';
}

/*
 * INIT TINYMCE EDITOR
 */
function init_tinymce(editor_id, element) {
	
	var qtags_settings = {id: editor_id, buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close"};
	quicktags(qtags_settings);
	QTags._buttonsInit();

	jQuery('#'+editor_id).css({ color: '#000' });

	var switch_btn = element.find('.wp-switch-editor').removeAttr("onclick");
	var editor_wrapper = element.parents('.wp-editor-wrap:eq(0)');
	switch_btn.bind('click', function(){
		var button = jQuery(this);
		if(button.is('.switch-tmce')){
			editor_wrapper.removeClass('html-active').addClass('tmce-active');
            
            var editor_value = element.find('textarea.wp-editor-area').eq(0).val();
            editor_value = editor_value!='' ? (editor_value+'').replace(/\\/g,'') : '';

            /* Create tinymce */
            tt_tinymce_create(editor_id);
			window.tinymce.get(editor_id).setContent(window.switchEditors.wpautop(editor_value), {format:'raw'});
		}
		else{
			editor_wrapper.removeClass('tmce-active').addClass('html-active');
            tt_tinymce_destroy(editor_id);
		}
	}).trigger('click');
}

/* Create Instance TinyMCE */
function tt_tinymce_create(editor_id){
    if( typeof(tinymce)!=='undefined' && typeof(tinymce.majorVersion)!=='undefined' && parseInt(tinymce.majorVersion)<4 ){
        window.tinymce.execCommand('mceAddControl', true, editor_id);
    }
    else{
        tinymce.EditorManager.execCommand('mceAddEditor',true, editor_id);
    }
}
/* Destroy Instance of TinyMCE */
function tt_tinymce_destroy(editor_id){
    if( typeof(tinymce)!=='undefined' && typeof(tinymce.majorVersion)!=='undefined' && parseInt(tinymce.majorVersion)<4 ){
        window.tinymce.execCommand('mceRemoveControl', false, editor_id);
    }
    else{
        window.tinymce.EditorManager.execCommand('mceRemoveEditor', false, editor_id);
        window.tinymce.execCommand('mceRemoveControl', false, editor_id);
    }
}



/*
 * GET CONTENT FROM EDITOR
 */
function get_content_tinymce(){
	var mce_id = 'blox_tinymce_editor',
		html_back;

	try {
		html_back = window.tinymce.get(mce_id).getContent();
        tt_tinymce_destroy(mce_id);
        
	}
	catch (err) {
		html_back = switchEditors.wpautop(jQuery('#'+mce_id).val());
	}

	return html_back;
}





var blox_iso_items = [];
var $blox_iso_items_obj = null;

function isotopeSearch(kwd){
	// reset results arrays
	var matches = [];
	var misses = [];

	jQuery('.isotope-item').removeClass('match miss'); // get rid of any existing classes

	if ( (kwd != '') && (kwd.length >= 2) ) { // min 2 chars to execute query:

        // loop through items array
        _.each(blox_iso_items, function(item){
            if ( item.name.indexOf(kwd) !== -1 ) { // keyword matches element
                matches.push( jQuery('#'+item.id)[0] );
            } else {
                misses.push( jQuery('#'+item.id)[0] );
            }
        });
        
        // add appropriate classes and call isotope.filter
        jQuery(matches).addClass('match');
        jQuery(misses).addClass('miss');
        $blox_iso_items_obj.isotope({ filter: jQuery(matches) }); // isotope.filter will take a jQuery object instead of a class name as an argument - sweet!
            
	} else {
        // show all if keyword less than 2 chars
        $blox_iso_items_obj.isotope({ filter: '.isotope-item' });
	}
}


/*
 * ADD ELEMENT MODAL
 */
function add_blox_element($container, $pos, callback){
	var $lis = '';
	blox_iso_items = [];
	for(i=0; i<blox_items.length; i++){
		$lis += '<li class="'+blox_item_filters[i]+'" id="_'+i+'"><a href="javascript:;" type="'+blox_items[i]+'"><i class="'+(blox_item_icons[i]!='' ? blox_item_icons[i] : 'fa-star')+'"></i><span>'+blox_item_titles[i]+'</span></a></li>';
		var tmp = {};
        tmp.id = '_'+i;
        tmp.name = blox_item_titles[i].toLowerCase();
        blox_iso_items.push( tmp );
	}
	
	var $modal = jQuery('<div id="blox_modal"></div>');
	$modal.append('<div class="blox_elements_toolbar"> \
						<span class="element_filter"> \
							<a class="all" href="javascript:;" data-filter="*">All</a> \
							<a class="filter-container" href="javascript:;" data-filter=".type-container">Container</a> \
							<a class="filter-element" href="javascript:;" data-filter=".type-element">Elements</a> \
							<a class="filter-media" href="javascript:;" data-filter=".type-media">Media</a> \
							<a class="filter-element" href="javascript:;" data-filter=".type-fullwidth">Fullwidth</a> \
							<input type="text" id="blox_iso_item_search" placeholder="Search element..." onkeyup="isotopeSearch(jQuery(this).val().toLowerCase());" />\
							<a href="javascript:;" onclick="jQuery(\'#blox_iso_item_search\').val(\'\'); isotopeSearch(false); return false;"><i class="fa-times"></i></a> \
						</span> \
					</div>');
	$modal.append('<ul class="blox_elements_wrapper">'+$lis+'</ul>');
    $modal.dialog({
    	'title'			: 'Blox Elements',
        'dialogClass'   : 'wp-dialog blox_dialog',
        'modal'         : true,
        'width'			: '80%',
        'buttons'       : [
        	{
        		'text'	: 'Close',
        		'class' : 'button-primary',
        		'click'	: function(){
        			jQuery(this).dialog('close');
        			jQuery(this).dialog('destroy').remove();
        		}
        	}
        ]
    });
    
    $blox_iso_items_obj = $modal.find('.blox_elements_wrapper');
    $blox_iso_items_obj.isotope();
    $modal.find('.element_filter a').unbind('click')
    	.click(function(){
    		$modal.find('.element_filter a').removeClass('active');
    		jQuery(this).addClass('active');
    		
    		var selector = jQuery(this).attr('data-filter');
    		$blox_iso_items_obj.isotope({ filter: selector });
    		return false;
    	});
    
    $modal.find('.blox_elements_wrapper a').unbind('click')
    	.click(function(){
    		var $type = jQuery(this).attr('type');
    		var $el = window['get_blox_element_'+$type]();

    		if( jQuery(this).parent().hasClass('type-fullwidth') ){
    			jQuery('#blox_preview').append( $el );
    		}
    		else{
    			if( $container != undefined ){
					if( $pos=='top' ){
						$container.prepend($el);
					}
					else{
						$container.append($el);
					}
	    			
	    		}
	    		else{
	    			jQuery('#blox_preview').append( get_blox_row_el_html($el) );
	    		}
    		}
    		
    		addEventsBloxLayout();

    		if( typeof callback!=='undefined' ){
    			callback();
    		}
    		
    		$modal.dialog('close');
    		$modal.dialog('destroy').remove();
    	});
    $modal.dialog('open');

    jQuery(document).keyup(function(e) {
		if (e.keyCode == 27) {
			jQuery('#blox_modal').dialog().dialog('close').remove();
		}
	});

}





function refresh_blox_events(callback){
	addEventsBloxLayout();
	if( typeof callback!=='undefined' ){
		callback();
	}
}


/*
 * ADD EVENTS ON BLOX ELEMENTS
 */
function add_event_blox_elements(){
	for(i=0; i<blox_items.length; i++){
		if( typeof window['add_event_blox_element_'+blox_items[i]] === 'function' ){
			window['add_event_blox_element_'+blox_items[i]]();
		}
	}
}


function addEventsBloxLayout(){
	
	jQuery('#blox_preview')
		.sortable({
			axis: "y",
	        handle: ".move_row, .blox_item_title",
	        placeholder: 'blox_item blox_placeholder',
	        start: function(event, ui) {
	            jQuery(ui.placeholder).height(jQuery(ui.item).height());
	        },
	        stop: function( event, ui ) {
				ui.item.children( ".blox_row" ).triggerHandler( "focusout" );
	        }
	      });
	
	jQuery(".blox_container").sortable({
		placeholder: 'blox_item blox_placeholder',
		connectWith: ".blox_container, #blox_preview",
		cursor: 'move',
		update: function(event, ui){
            
        },
        start: function(event, ui) {
            jQuery(ui.placeholder).height(jQuery(ui.item).height());
        },
        stop: function(event, ui){
        	// only row element into preview container
        	if( jQuery(ui.item).hasClass('blox_item') && jQuery(ui.item).parent().attr('id')=='blox_preview' ){
        		jQuery(this).sortable('cancel');
        	}
        	else{
        		ui.item.children( ".blox_row" ).triggerHandler( "focusout" );
        	}
        	
        	// accordion not into accordion
        	if( jQuery(ui.item).hasClass('blox_accordion') && jQuery(ui.item).parent().hasClass('blox_accordion_item_content') ){
        		jQuery(this).sortable('cancel');
        	}
        	// accordion not into tab
        	if( jQuery(ui.item).hasClass('blox_accordion') && jQuery(ui.item).parent().hasClass('blox_tab_item_content') ){
        		jQuery(this).sortable('cancel');
        	}
        	// tab not into tab
        	if( jQuery(ui.item).hasClass('blox_tab') && jQuery(ui.item).parent().hasClass('blox_tab_item_content') ){
        		jQuery(this).sortable('cancel');
        	}
        	// tab not into accordion
        	if( jQuery(ui.item).hasClass('blox_tab') && jQuery(ui.item).parent().hasClass('blox_accordion_item_content') ){
        		jQuery(this).sortable('cancel');
        	}
        }
	});
	
	add_event_blox_elements();
}




/*
 * SWITCH BLOX BUILDER
 */
function switch_blox_builder(switcher){
	switchEditors.switchto(document.getElementById('content-tmce'));
	if(switcher){
		blox_setCookie('blox_editor_mode', 'true', 1);
		blox_setCookie('blox_editor_mode_post', jQuery('#post_ID').val(), 1);
		jQuery("#postdivrich").hide();
		jQuery("#blox_contentbuilder").show();

		//$content = tinyMCE.activeEditor ? tinyMCE.activeEditor.getContent() : jQuery('#content').val();
		$content = window.tinyMCE.get('content') ? window.tinyMCE.get('content').getContent() : jQuery('#content.wp-editor-area').val();
		$content = switchEditors.pre_wpautop($content);
		
		for(i=0; i<blox_items.length; i++){
			if( typeof window['parse_shortcode_'+blox_items[i]] === 'function' ){
				$content = window['parse_shortcode_'+blox_items[i]]($content);
			}
		}
		
		jQuery('#blox_preview').html($content);
		
		jQuery('#blox_add_row').unbind('click')
			.click(function(){
				$new_row = get_blox_row_el_html();
				jQuery('#blox_preview').append($new_row);
				addEventsBloxLayout();
			});
		
		jQuery('#blox_add_element').unbind('click')
			.click(function(){
				add_blox_element();
			});
		
		addEventsBloxLayout();
	}
	else{
		blox_setCookie('blox_editor_mode', '', 0);
		blox_setCookie('blox_editor_mode_post', '', 0);
		var $content = jQuery('#blox_preview').clone();
		
		for(indx=0; indx<blox_items.length; indx++){
			if( typeof window['revert_shortcode_'+blox_items[indx]] === 'function' ){
				$content = window['revert_shortcode_'+blox_items[indx]]($content);
			}
		}
		
		//tinyMCE.activeEditor.setContent($content.html());
		//window.tinyMCE.get('content').setContent($content.html());
		
		window.tinyMCE.get('content').setContent(window.switchEditors.wpautop($content.html()), {format:'raw'});
		window.tinyMCE.get('content').execCommand('mceRepaint');
		window.tinyMCE.get('content').execCommand('mceRepaint');
		
		
		jQuery("#postdivrich").show();
		jQuery("#blox_contentbuilder").hide();
	}
}





function blox_save_template(){
	
	var $modal = jQuery('<div id="blox_modal"></div>');
	$modal.append('<div class="blox_modal_content"> \
					<label>Template Name</label> \
					<input type="text" id="blox_save_template_name" value="" /> \
					<div class="blox_modal_form_desc">Name format [A-B, a-b, 0-9]</div> \
				</div>');
	
    $modal.dialog({
    	'title'			: 'Save Template',
        'dialogClass'   : 'wp-dialog blox_dialog',
        'modal'         : true,
        'width'			: '400',
        'buttons'       : [
        	{
        		'text'	: 'Save',
        		'class' : 'button-primary',
        		'click'	: function(){
        			
        			if( jQuery('#blox_save_template_name').val()!='' ){
        				var $content = jQuery('#blox_preview').clone();
						for(indx=0; indx<blox_items.length; indx++){
							if( typeof window['revert_shortcode_'+blox_items[indx]] === 'function' ){
								$content = window['revert_shortcode_'+blox_items[indx]]($content);
							}
						}
						jQuery.post( ajaxurl, {'action':'blox_template_save', 'title':jQuery('#blox_save_template_name').val(), 'content':$content.html()}, function(data){
							if( data!='-1' ){
								var $json = jQuery.parseJSON(data);
								var html = '';
								for(var i=0; i<$json.length; i++){
									html += '<span><a href="javascript: blox_load_template(&quot;'+$json[i].id+'&quot;);" data-template="'+$json[i].id+'">'+$json[i].title+'</a><i class="fa-times" onclick="blox_remove_template(jQuery(this));"></i></span>';
								}
								jQuery('#blox_template_storage').html(html);
								jQuery('#blox_template_list').html(html);
							}
						});
	        			
	        			jQuery(this).dialog('close');
	        			jQuery(this).dialog('destroy').remove();
        			}
        			else{
        				jQuery('#blox_save_template_name').parent().find('.blox_modal_form_desc').html('Template name is empty [A-B, a-b, 0-9]');
        			}
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
}

function blox_remove_template($context){
	jQuery.post( ajaxurl, {'action':'blox_template_remove', 'id':$context.parent().find('a').attr('data-template')}, function(data){
		if( data!='-1' ){
			var $json = jQuery.parseJSON(data);
			var html = '';
			for(var i=0; i<$json.length; i++){
				html += '<span><a href="javascript: blox_load_template(&quot;'+$json[i].id+'&quot;);" data-template="'+$json[i].id+'">'+$json[i].title+'</a><i class="fa-times" onclick="blox_remove_template(jQuery(this));"></i></span>';
			}
			jQuery('#blox_template_storage').html(html);
			jQuery('#blox_template_list').html(html);
		}
	});
}

function blox_load_template($id){
	jQuery.post( ajaxurl, {'action':'blox_template_load', 'id':$id}, function(data){
		if( data!='-1' ){
			jQuery('#blox_preview').fadeOut();
			var $content = data;
			$content = switchEditors.pre_wpautop($content);
			for(i=0; i<blox_items.length; i++){
				if( typeof window['parse_shortcode_'+blox_items[i]] === 'function' ){
					$content = window['parse_shortcode_'+blox_items[i]]($content);
				}
			}
			
			jQuery('#blox_preview').html($content);
			jQuery('#blox_preview').fadeIn();
			addEventsBloxLayout();
		}
	});
}


/*
 * DOCUMENT ONREADY
 */
jQuery(function(){
	
	//blox
	jQuery('#wp-content-media-buttons').before('<a id="content-blox" class="wp-switch-editor button_blox_switcher" onclick="switch_blox_builder(true);">Blox Content Builder</a>');
	
	jQuery('#publish').click(function(){
		if( jQuery("#blox_contentbuilder").css('display') == 'block' ){
            switch_blox_builder(false);
		}
	});
	
	// init blox template
	jQuery('#blox_template_list').html(jQuery('#blox_template_storage').html());
	
	
	jQuery('#blox_fullscreen').click(function(){
		if( jQuery('#blox_contentbuilder').hasClass('fullscreen') ){
			jQuery('#blox_contentbuilder').removeClass('fullscreen');
			jQuery('html').css('overflow', 'auto');
		}
		else{
			jQuery('#blox_contentbuilder').addClass('fullscreen');
			jQuery('html').css('overflow', 'hidden');
		}
		jQuery(window).trigger('scroll');
	});
	
	jQuery('#blox_trigger_publish').click(function(){
		jQuery('#publish').trigger('click');
	});
	
	jQuery(window).scroll(function(){
		var scrollTop = jQuery(window).scrollTop();
		var offsetTop = 0;
		if(jQuery('#blox_contentbuilder').offset()){
			offsetTop = jQuery('#blox_contentbuilder').offset().top
		}
		else if(jQuery('#blox_contentbuilder').position()){
			offsetTop = jQuery('#blox_contentbuilder').position().top
		}
		if ( scrollTop > jQuery('#blox_contentbuilder').offset().top ) {
			if( !jQuery('#blox_contentbuilder .blox_nav').hasClass('blox_nav_fixed') )
				jQuery('#blox_contentbuilder .blox_nav').addClass('blox_nav_fixed');
		}
		else{
			if( jQuery('#blox_contentbuilder .blox_nav').hasClass('blox_nav_fixed') )
				jQuery('#blox_contentbuilder .blox_nav').removeClass('blox_nav_fixed');
		}
	});
	
});



jQuery(window).load(function(){

    // Repair current session
    if( get_cookie_editor_mode() ){
        switch_blox_builder(true);
    }

});




function get_cookie_editor_mode(){
	var cookie_editor_mode = blox_getCookie('blox_editor_mode');
	var cookie_editor_post = blox_getCookie('blox_editor_mode_post');
	if( cookie_editor_mode!=null && cookie_editor_mode=='true' && cookie_editor_post==jQuery('#post_ID').val() ){
		return true;
	}
	return false;
}




/* COOKIE */
function blox_setCookie(c_name,value,exdays){
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}
function blox_getCookie(c_name){
	var c_value = document.cookie;
	var c_start = c_value.indexOf(" " + c_name + "=");
	if (c_start == -1){
		c_start = c_value.indexOf(c_name + "=");
	}
	if (c_start == -1){
		c_value = null;
	}
	else{
		c_start = c_value.indexOf("=", c_start) + 1;
		var c_end = c_value.indexOf(";", c_start);
		if (c_end == -1){
			c_end = c_value.length;
		}
		c_value = unescape(c_value.substring(c_start,c_end));
	}
	return c_value;
}







/* Dialog Form Builder */

function show_blox_form(title, elements, callback, params){
	jQuery("#blox_popup_window").find('.blox_popup_toolbar .title').html(title);
    jQuery("#blox_popup_window").find('.blox_popup_wrapper').html('<br><br><br><br><br>');

    // build elements
    var $form = jQuery('<div class="blox_form_elements"></div>');
	jQuery.each(elements, function(index, elem){
		if( elem.type ){
			switch(elem.type){
				case 'input':
					$form.append( get_bform_field_input(elem) );
					break;
				case 'number':
					$form.append( get_bform_field_number(elem) );
					break;
				case 'datetime':
					$form.append( get_bform_field_datetime(elem) );
					break;
				case 'textarea':
					$form.append( get_bform_field_textarea(elem) );
					break;
				case 'colorpicker':
					$form.append( get_bform_field_colorpicker(elem) );
					break;
				case 'image':
					$form.append( get_bform_field_image(elem) );
					break;
				case 'gallery':
					$form.append( get_bform_field_gallery(elem) );
					break;
				case 'video':
					$form.append( get_bform_field_video(elem) );
					break;
				case 'file':
					$form.append( get_bform_field_file(elem) );
					break;
				case 'icon':
					$form.append( get_bform_field_icon(elem) );
					break;
				case 'select':
					$form.append( get_bform_field_select(elem) );
					break;
				case 'checkbox':
					$form.append( get_bform_field_checkbox(elem) );
					break;
				case 'checkbox_flat':
					$form.append( get_bform_field_checkbox_flat(elem) );
					break;
				case 'animation':
					$form.append( get_bform_field_animation(elem) );
					break;
				case 'editor':
					$form.append( get_bform_field_editor(elem) );
					break;
				default:
					$form.append( get_bform_field_input(elem) );
					break;
			}
		}
	});
	
	show_blox_form_handler($form, callback, params);
}

function show_blox_form_ajax(title, ajax_params, callback, params){
	jQuery("#blox_popup_window").find('.blox_popup_toolbar .title').html(title);
    jQuery("#blox_popup_window").find('.blox_popup_wrapper').html('<br><br><br><br><br>');

    // build elements
    var $form = jQuery('<div class="blox_form_elements"></div>');

    if( jQuery('#blox_ajax_loading').length<1 ){
    	jQuery('body').append('<div id="blox_ajax_loading"></div>');
    }
    jQuery('#blox_ajax_loading').fadeIn();

    jQuery.post( ajaxurl, ajax_params, function(data){
        if( data != "-1" ){
        	$form.append( data );
        	show_blox_form_handler($form, callback, params);
        	if( typeof params!=='undefined' && params.ajax_handler!=='undefined' ){
        		params.ajax_handler();
        	}
        }
        jQuery('#blox_ajax_loading').fadeOut();
    });
}

function show_blox_form_handler($form, callback, params){
	

	if( (typeof params!=='undefined') && params.hasOwnProperty('target') ){
		/* Skin option
		==========================================*/
		if( (typeof params.skin!=='undefined') && params.skin == true ){
			$form.append( get_bform_field_select({
				type: 'select',
	            id: 'blox_element_style',
	            label: 'Element Style',
	            options: [
	            	{ value: 'default', label: 'Default' },
	            	{ value: 'boxed', label: 'Boxed Style' },
	            	{ value: 'bordered', label: 'Bordered Style' }
	            ],
	            value: params.target.attr('skin'),
	            description: 'Please activate this option If you need clean (without border & padding) style. It appears on only Grid and Masonry styles.'
			}) );
		}
		/* Extra Fields
		==========================================*/
		if( typeof params.extra_field!=='undefined' && params.extra_field == true ){
			$form.append( get_bform_field_animation({
				type: 'animation',
	            id: 'blox_element_animation',
	            label: 'Element Animation',
	            value: params.target.attr('animation'),
	            description: 'The animation when element visible first time.'
			}) );
			$form.append( get_bform_field_input({
				type: 'input',
	            id: 'blox_element_class',
	            label: 'Extra Class',
	            value: params.target.attr('extra_class'),
	            description: 'Add here a class name/names then add your related styles on Theme Options => <b>Custom CSS</b> tab.'
			}) );
		}
		/* Visibility Options
		==========================================*/
		if( (typeof params.visibility!=='undefined') && params.visibility == true ){
			$form.append( get_bform_field_input({
				type: 'input',
	            id: 'blox_element_visibility',
	            label: 'Responsive Visibility',
	            value: params.target.attr('visibility'),
	            description: '<br>Use one or multiple combination of available classes for toggling content across viewport breakpoints.'
			}) );
		}
	}

	// remove table wrappers
	jQuery('#blox_popup_window').find('.blox_popup_table_wrapper').remove();
	
	// build form
	jQuery("#blox_popup_window").find('.blox_popup_wrapper').append($form);
    jQuery("#blox_popup_window").show();
    jQuery('#wpadminbar').hide();
    jQuery('html').css('overflow', 'hidden');
    jQuery(document).keyup(function(e) {
		if (e.keyCode == 27) {
			jQuery('#blox_popup_window').find('.blox_popup_button_close').trigger('click');
		}
	});


    if( jQuery.browser.mozilla ){
    	document.body.className = document.body.className.replace(' js ',' replaced-js ');
    }

    // form close
    jQuery('#blox_popup_window').find('.blox_popup_button_close').unbind('click')
		.click(function(){
			if( jQuery.browser.mozilla ){
				document.body.className = document.body.className.replace('replaced-js','js');
			}
            /* Destroy TinyMCE */
            tt_tinymce_destroy('blox_tinymce_editor');

			jQuery('#blox_popup_window').hide();
			jQuery('#wpadminbar').show();
			jQuery('html').css('overflow', 'auto');

			if( typeof(params)!=='undefined' && params.hasOwnProperty('target') ){
				params.target.addClass('fade-border');

				setTimeout(function(){
					params.target.css({
						'transition': 'all .5s',
						'transform': 'scale(1)'
					});
				}, 50);

				setTimeout(function(){
					params.target.removeClass('fade-border');
					params.target.removeAttr('style');
				}, 800);

			}
		});

	// form update button
	jQuery('#blox_popup_window').find('.blox_popup_button_update').unbind('click')
    	.click(function(){

    		if( typeof params!=='undefined' && params.hasOwnProperty('target') ){
    			if( typeof params.skin!=='undefined' && params.skin == true ){
	    			params.target.attr('skin', jQuery('#blox_element_style').val());
	    		}

    			if( typeof params.extra_field!=='undefined' && params.extra_field == true ){
	    			params.target.attr('animation', jQuery('#blox_element_animation').val());
	                params.target.attr('extra_class', jQuery('#blox_element_class').val());
	    		}

	    		if( typeof params.visibility!=='undefined' && params.visibility == true ){
	    			params.target.attr('visibility', jQuery('#blox_element_visibility').val());
	    		}
    		}

    		callback(jQuery('#blox_popup_window'));
    		jQuery('#blox_popup_window').find('.blox_popup_button_close').trigger('click');
    	});

	
	show_blox_form_events();
}


function show_blox_form_events(){

	// init colorpicker
    jQuery("#blox_popup_window").find('.blox_elem_colorpicker').wpColorPicker({
    	palettes: [
					'#16a085', '#27ae60', '#2980b9', '#8e44ad', '#f39c12',
					'#f39c12','#d35400', '#c0392b', '#bdc3c7', '#7f8c8d'
				]
    });

    // init image field
    jQuery("#blox_popup_window").find('.blox_elem_image_browse').unbind('click')
		.click(function(){
			var $context = jQuery(this);
	    	var send_attachment_bkp = wp.media.editor.send.attachment;
			wp.media.editor.send.attachment = function(props, attachment){
				wp.media.editor.send.attachment = send_attachment_bkp;
				$context.parent().find('input').val(attachment.url);
				$context.parent().find('.browse_preview').html('<img src="'+attachment.url+'" style="width: 100%;" />');
				$context.parent().find('.browse_preview').append('<a href="javascript:;">Remove</a>');
				$context.parent().find('.browse_preview').find('a').unbind('click')
					.click(function(){
						$context.parent().find('.browse_preview').html('');
						$context.parent().find('input').val('');
						$context.parent().find('.browse_preview').hide();
						$context.parent().find('input').change();
					});
				$context.parent().find('input').change();
				$context.parent().find('.browse_preview').show();
			}
			wp.media.editor.open();
	    	return false;
	    });
    jQuery("#blox_popup_window").find('.browse_preview a').unbind('click')
    	.click(function(){
    		var $context = jQuery(this).parent();
    		$context.parent().find('.browse_preview').html('');
			$context.parent().find('input').val('');
			$context.parent().find('.browse_preview').hide();
			$context.parent().find('input').change();
    	});

	// init file field
    jQuery("#blox_popup_window").find('.blox_elem_file_browse').unbind('click')
		.click(function(){
			var $context = jQuery(this);
	    	var send_attachment_bkp = wp.media.editor.send.attachment;
			wp.media.editor.send.attachment = function(props, attachment){
				wp.media.editor.send.attachment = send_attachment_bkp;
				$context.parent().find('input').val(attachment.url);
				//$context.parent().find('input').change();
			}
			wp.media.editor.open();
	    	return false;
	    });

    // init file field
    jQuery("#blox_popup_window").find('.blox_elem_video_browse').unbind('click')
		.click(function(){
			var $context = jQuery(this);
			blox_media('blox_insert_video', 'Videos', '', function(selection){
				values = selection.map( function( attachment ){
 					element = attachment.toJSON();
 					$context.parent().find('input').val(element.url);
 				});
			});
	    	return false;
	    });

	// init select
	jQuery('.blox_elem_select').each(function(){
		var data = jQuery(this).attr('data') + '';
		data = data!='undefined' ? data : '';
		jQuery(this).val(data);
		jQuery(this).change();

		if( data=='' ){
			jQuery(this).find('option').eq(0).attr('selected', 'selected');
			jQuery(this).change();
		}
	});

	// init datetimepicker
	jQuery('.input_datetimepicker input').each(function(){
		var $this = jQuery(this);
		$this.datetimepicker({
			startDate: new Date()
		});
	});

	// init editor
	init_tinymce('blox_tinymce_editor', jQuery("#blox_popup_window").find('.blox_popup_wrapper'));


	// init gallery
	jQuery("#blox_popup_window").find('.gallery_browse_images').each(function(){
		var $parent = jQuery(this).parent();
		if( $parent.find('input').val()!='' ){
			jQuery.post( ajaxurl, {'action':'get_blox_element_galleryimg', 'images':$parent.find('input').val() }, function(data){
		    	if( data != "-1" ){
		    		img_spans = '';
		            $img_arrs = data.split('^');
		            for(var i=0; i<$img_arrs.length; i++){
		            	if( $img_arrs[i]!='' ){
		            		img_spans += '<span style="background-image:url('+$img_arrs[i]+');"></span>';
		            	}
		            }
		            $parent.find('.gallery_preview').html(img_spans);
		    	}
		    });
		}
	});

	jQuery("#blox_popup_window").find('.gallery_browse_images').unbind('click')
    	.click(function(){
    		var $context = jQuery(this);
    		var $input = jQuery(this).parent().find('input');

    		blox_media( $input.val()!='' ? 'gallery-edit' : 'gallery-library', 'Add/Edit Gallery', $input.val(), function(selection){
				$counter = 0;
 				$input.val('');
 				$context.parent().find('.gallery_preview').html('');
 				
 				values = selection.map( function( attachment ){
 					element = attachment.toJSON();
 					$input.val($input.val()+($counter==0 ? '' : ',')+element.id);
 					$context.parent().find('.gallery_preview').append('<span style="background-image: url('+element.url+');"></span>');
 					$counter++;
 				});
			});
        	return false;
        });

    // blox switcher event
	jQuery('.blox_switcher_field').each(function(){
		var $this = jQuery(this);
		$this.click(function(){
			 $this.toggleClass("on");
			 if( $this.hasClass('on') ){
			 	$this.find('input').val('1').trigger('change');
			 }
			 else{
			 	$this.find('input').val('0').trigger('change');
			 }
		});
	});

	/* Responsibility Visibility */
	jQuery('#blox_element_visibility').select2({
		placeholder: 'Select Hidden Devices',
		allowClear: true,
		multiple: true,
		data: [ { id: 'hidden-lg', text: 'Hide on Large devices' },
				{ id: 'hidden-md', text: 'Hide on Medium devices' },
				{ id: 'hidden-sm', text: 'Hide on Small devices' },
				{ id: 'hidden-xs', text: 'Hide on Extra small devices' }
			   ]
	});
	

}



// input element
function get_bform_field_input(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class='+item_class+'>\
                                <label>'+elem.label+'</label> \
                                <input type="text" id="'+elem.id+'" /> \
                                '+item_desc+' \
                                <span class="clearfix"></span> \
                        </p>');
	$obj.find('input').val(value);
	return $obj;
}

// number element
function get_bform_field_number(elem){
	var value = elem.value ? elem.value+'' : elem.std;
	value = value!='undefined' ? value : elem.std;
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class='+item_class+'>\
                                <label>'+elem.label+':</label> \
                                <input type="number" id="'+elem.id+'" step="1" min="0" value="0" class="small-text" /> \
                                '+item_desc+' \
                                <span class="clearfix"></span> \
                        </p>');
	$obj.find('input').val(value);
	return $obj;
}

// datetime element
function get_bform_field_datetime(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class="input_datetimepicker '+item_class+'">\
                            <label>'+elem.label+':</label> \
                            <input type="text" data-format="yyyy-MM-dd hh:mm" id="'+elem.id+'" /> \
                            '+item_desc+' \
                            <span class="clearfix"></span> \
                        </p>');
	$obj.find('input').val(value);
	return $obj;
}

// textarea element
function get_bform_field_textarea(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class='+item_class+'>\
			    			<label>'+elem.label+':</label> \
			    			<textarea id="'+elem.id+'"></textarea> \
			    			'+item_desc+' \
                            <span class="clearfix"></span> \
			    		</p>');
	$obj.find('textarea').val(value);
	return $obj;
}

// colorpicker element
function get_bform_field_colorpicker(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class='+item_class+'>\
		    			<label>'+elem.label+':</label> \
		    			<input type="text" id="'+elem.id+'" class="blox_elem_colorpicker" /> \
		    			'+item_desc+' \
                        <span class="clearfix"></span> \
		    		</p>');
	$obj.find('input').val(value);
	return $obj;
}

// image element
function get_bform_field_image(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class="blox_elem_field_image '+item_class+'">\
                			<label>'+elem.label+':</label> \
                			<input type="text" id="'+elem.id+'" /> \
                			<a href="javascript:;" class="button blox_elem_image_browse">Browse Image...</a> \
                			<span class="browse_preview" style="display: block;"></span> \
                			'+item_desc+' \
                            <span class="clearfix"></span> \
                		</p>');
	$obj.find('input').val(value);
	if( value!='' ){
		$obj.find('.browse_preview').html('<img src="'+value+'" style="width: 100%;" />');
		$obj.find('.browse_preview').append('<a href="javascript:;">Remove</a>');
	}
	return $obj;	
}

// gallery element
function get_bform_field_gallery(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class="blox_elem_field_gallery '+item_class+'">\
							<label>'+elem.label+':</label> \
                			<input type="hidden" id="'+elem.id+'" /> \
                			<a href="javascript:;" class="button gallery_browse_images">Insert/Update Gallery...</a> \
                			'+item_desc+' \
                            <span class="clearfix"></span> \
                			<span class="gallery_preview"></span> \
                		</p>');
	$obj.find('input').val(value);

	return $obj;	
}

// video element
function get_bform_field_video(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class="blox_video_field '+item_class+'">\
                			<label>'+elem.label+':</label> \
                			<input type="text" id="'+elem.id+'" /> \
                			<a href="javascript:;" class="button blox_elem_video_browse">Browse...</a> \
                			'+item_desc+' \
                            <span class="clearfix"></span> \
                		</p>');
	$obj.find('input').val(value);
	return $obj;	
}

// file element
function get_bform_field_file(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class="'+item_class+'">\
                			<label>'+elem.label+':</label> \
                			<input type="text" id="'+elem.id+'" /> \
                			<a href="javascript:;" class="button blox_elem_file_browse">Browse...</a> \
                			'+item_desc+' \
                            <span class="clearfix"></span> \
                		</p>');
	$obj.find('input').val(value);
	return $obj;	
}

// icon font element
function get_bform_field_icon(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class="blox_elem_field_icon '+item_class+'"> \
							<label>'+elem.label+':</label> \
							<input type="text" id="'+elem.id+'"  /> \
							<a href="javascript: themeton_get_font(jQuery(\'#'+elem.id+'\'));" class="button">Browse icons...</a> \
							'+item_desc+' \
                            <span class="clearfix"></span> \
						</p>');
	$obj.find('input').val(value);
	return $obj;
}

// select element
function get_bform_field_select(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var options = '';
	jQuery.each(elem.options, function(index, element){
		if( value=='' && index==0 ){
			value = element.value;
		}
		options += '<option value="'+element.value+'">'+element.label+'</option>'
	});

	var $obj = jQuery('<p class="'+item_class+'"> \
							<label>'+elem.label+'</label> \
							<select id="'+elem.id+'" data="'+value+'" class="blox_elem_select">'+options+'</select> \
							'+item_desc+' \
                            <span class="clearfix"></span> \
						</p>');
	return $obj;
}

// checkbox element
function get_bform_field_checkbox(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class="'+item_class+'"> \
							<label><input type="checkbox" id="'+elem.id+'" '+(value=='1' || value=='true' ? 'checked="checked"' : '')+' value="1" /> '+elem.label+'</label> \
							'+item_desc+' \
                            <span class="clearfix"></span> \
						</p>');
	return $obj;
}

// checkbox element
function get_bform_field_checkbox_flat(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var $obj = jQuery('<p class="blox_form_element_switcher'+item_class+'"> \
                            <span class="blox_switcher blox_switcher_field '+(value=='1' ? 'on' : '')+'"> \
		                        <span class="handle"></span> \
		                        <input type="hidden" id="'+elem.id+'" value="'+(value=='1' ? '1' : '0')+'" /> \
		                    </span> \
		                    <label>'+elem.label+'</label> \
		                    '+item_desc+' \
		                    <span class="clearfix"></span> \
						</p>');
	return $obj;
}




// animation select element
function get_bform_field_animation(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';
	var item_desc = (typeof elem.description!=='undefined' ? '<span class="item_desc">'+elem.description+'</span>' : '');
	var item_class = (typeof elem.description!=='undefined' ? 'blox_field_with_desc' : '');

	var options = '';
	options += '<option value="none">No Animation</option>';
	options += '<option>flipInX</option>';
	options += '<option>flipInY</option>';
	options += '<option>fadeIn</option>';
	options += '<option>fadeInUp</option>';
	options += '<option>fadeInDown</option>';
	options += '<option>fadeInLeft</option>';
	options += '<option>fadeInRight</option>';
	options += '<option>fadeInUpBig</option>';
	options += '<option>fadeInDownBig</option>';
	options += '<option>fadeInLeftBig</option>';
	options += '<option>fadeInRightBig</option>';
	options += '<option>slideInDown</option>';
	options += '<option>slideInLeft</option>';
	options += '<option>slideInRight</option>';
	options += '<option>bounceIn</option>';
	options += '<option>bounceInDown</option>';
	options += '<option>bounceInUp</option>';
	options += '<option>bounceInLeft</option>';
	options += '<option>bounceInRight</option>';
	options += '<option>rotateIn</option>';
	options += '<option>rotateInDownLeft</option>';
	options += '<option>rotateInDownRight</option>';
	options += '<option>rotateInUpLeft</option>';
	options += '<option>rotateInUpRight</option>';
	options += '<option>lightSpeedIn</option>';
	options += '<option>rollIn</option>';

	var $obj = jQuery('<p class="'+item_class+'"> \
							<label>'+elem.label+'</label> \
							<select id="'+elem.id+'" data="'+value+'" class="blox_elem_select">'+options+'</select> \
							'+item_desc+' \
                            <span class="clearfix"></span> \
						</p>');
	return $obj;
}


// editor element
function get_bform_field_editor(elem){
	var value = elem.value ? elem.value+'' : '';
	value = value!='undefined' ? value : '';

	var $obj = jQuery('<div id="wp-blox_tinymce_editor-wrap" class="wp-core-ui wp-editor-wrap tmce-active has-dfw"> \
                            <link rel="stylesheet" id="editor-buttons-css"  href="'+ajaxurl.replace('/wp-admin/admin-ajax.php', '')+'/wp-includes/css/editor.min.css" type="text/css" media="all" /> \
                            <div id="wp-blox_tinymce_editor-editor-tools" class="wp-editor-tools hide-if-no-js"> \
                                <div id="wp-blox_tinymce_editor-media-buttons" class="wp-media-buttons"> \
                                    <a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="blox_tinymce_editor" title="Add Media"><span class="wp-media-buttons-icon"></span> Add Media</a> \
                                </div> \
                                <div class="wp-editor-tabs"> \
                                    <a id="blox_tinymce_editor-html" class="wp-switch-editor switch-html" onclick="switchEditors.switchto(this);">Text</a> \
                                    <a id="blox_tinymce_editor-tmce" class="wp-switch-editor switch-tmce" onclick="switchEditors.switchto(this);">Visual</a> \
                                </div> \
                            </div> \
                            <div id="wp-blox_tinymce_editor-editor-container" class="wp-editor-container"> \
                                <textarea class="wp-editor-area" style="height: 360px" autocomplete="off" cols="40" name="blox_tinymce_editor" id="blox_tinymce_editor"></textarea> \
                            </div> \
                        </div>');
	$obj.find('#blox_tinymce_editor').val( value );

	return $obj;
}






function set_blox_selectbox(){
	jQuery('.blox_elem_select').each(function(){
		var data = jQuery(this).attr('data') + '';
		data = data!='undefined' ? data : '';
		jQuery(this).val(data);
		jQuery(this).change();

		if( data=='' ){
			jQuery(this).find('option').eq(0).attr('selected', 'selected');
			jQuery(this).change();
		}
	});
}





