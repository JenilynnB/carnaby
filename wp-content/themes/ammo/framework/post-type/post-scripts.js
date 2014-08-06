jQuery(document).ready(function($){

	if( jQuery('.wp-post-format-ui').length < 1 ){
		post_format = ['standard', 'image', 'gallery', 'link', 'video', 'audio', 'chat', 'status', 'quote', 'aside'];
		
		html = '<div id="tt_post_format_container">';
		for(i=0; i<post_format.length; i++){
			html += '<a href="javascript:;" class="post_format_item"> \
						<div class="post-'+post_format[i]+'"></div> \
						<span>'+post_format[i]+'</span> \
					</a>';
		}
		html += '</div>';
	
		jQuery('#poststuff').before(html);
	
		jQuery('#tt_post_format_container a.post_format_item').click(function(){
			var pformat = jQuery(this).find('> span').text();
			pformat = pformat=='standard' ? '' : pformat;
			jQuery('#tt_post_format_container a.post_format_item').removeClass('active');
			jQuery(this).addClass('active');
			jQuery('#post_format').val( pformat );
			
			jQuery('.post_format_wrapper').hide();
			if( pformat!='image' ){
				jQuery('.format_content_'+pformat).slideDown();
			}

			if( pformat=='gallery' ){
				jQuery('#post-images').slideDown();
			}
			else{
				jQuery('#post-images').slideUp();
			}
		});

		jQuery('input.post-format').each(function(){
			if( this.checked ){
				var format = this.value;
				format = format=='0' ? 'standard' : format;
				jQuery('#tt_post_format_container').find('.post-'+format).parent().trigger('click');
			}
		});
		
		post_format_html = '<div id="post_format_content_div">';
			post_format_html += '<div class="post_format_wrapper format_content_image"> \
									<div class="pf_image_preview"></div> \
									<label>Image URL or HTML</label> \
									<textarea id="format_image_data" name="format_image_data"></textarea> \
									<div class="pf_link"><a href="javascript:;">Select Image From Media</a></div> \
									<div class="clearfix"></div> \
								</div>';
								
			post_format_html += '<div class="post_format_wrapper format_content_link"> \
									<label>Link URL</label> \
									<input type="text" id="format_link_url_data" name="format_link_url_data" /> \
								</div>';
			
			post_format_html += '<div class="post_format_wrapper format_content_video"> \
									<label>Video embed code or URL(Youtube, Vimeo, Self hosted)</label> \
									<textarea id="format_video_embed_data" name="format_video_embed_data"></textarea> \
									<div class="pf_link"><a href="javascript:;">Select Video From Media</a></div> \
									<div class="clearfix"></div> \
								</div>';
								
			post_format_html += '<div class="post_format_wrapper format_content_audio"> \
									<label>Audio embed code or URL</label> \
									<textarea id="format_audio_embed_data" name="format_audio_embed_data"></textarea> \
									<div class="pf_link"><a href="javascript:;">Select Audio From Media</a></div> \
									<div class="clearfix"></div> \
								</div>';
			
			post_format_html += '<div class="post_format_wrapper format_content_quote"> \
									<textarea id="format_quote_text_data" placeholder="Quote text"></textarea> \
									<div style="float:left; width: 48%;"> \
										<label>Quote source</label> \
										<input type="text" id="format_quote_source_name_data" name="format_quote_source_name_data" /> \
										<label>Source info</label> \
										<input type="text" id="format_quote_source_url_data" name="format_quote_source_url_data" /> \
									</div> \
									<div class="clearfix"></div> \
								</div>';
		post_format_html += '</div>';
		
		jQuery('#postdivrich').before(post_format_html);
		
		jQuery('#format_image_data').val( jQuery('#format_image').val() );
		jQuery('#format_video_embed_data').val( jQuery('#format_video_embed').val() );
		jQuery('#format_audio_embed_data').val( jQuery('#format_audio_embed').val() );
		
		jQuery('#format_link_url_data').val( jQuery('#format_link_url').val() );
		jQuery('#format_quote_text_data').val( jQuery('#format_quote_text').val() );
		jQuery('#format_quote_source_name_data').val( jQuery('#format_quote_source_name').val() );
		jQuery('#format_quote_source_url_data').val( jQuery('#format_quote_source_url').val() );
		
		
		// insert image
		jQuery('.format_content_image .pf_link a').click(function(){
			$this = jQuery(this);
			var send_attachment_bkp = wp.media.editor.send.attachment;
			wp.media.editor.send.attachment = function(props, attachment){
				wp.media.editor.send.attachment = send_attachment_bkp;
				$this.parent().parent().find('textarea').val('<img src="'+attachment.url+'" />');
			}
			wp.media.editor.open();
	        return false;
		});
		
		// insert video
		jQuery('.format_content_video .pf_link a').click(function(){
			$this = jQuery(this);
			var send_attachment_bkp = wp.media.editor.send.attachment;
			wp.media.editor.send.attachment = function(props, attachment){
				wp.media.editor.send.attachment = send_attachment_bkp;
				$this.parent().parent().find('textarea').val(attachment.url);
			}
			wp.media.editor.open();
	        return false;
		});
		
		// insert audio
		jQuery('.format_content_audio .pf_link a').click(function(){
			$this = jQuery(this);
			var send_attachment_bkp = wp.media.editor.send.attachment;
			wp.media.editor.send.attachment = function(props, attachment){
				wp.media.editor.send.attachment = send_attachment_bkp;
				$this.parent().parent().find('textarea').val(attachment.url);
			}
			wp.media.editor.open();
	        return false;
		});
		
		$post_format = jQuery('#post_format').val();
		jQuery('div.post-'+$post_format).parent().trigger('click');
		
		jQuery('#post').submit(function(e){
			//e.preventDefault();
			jQuery('#format_image').val( jQuery('#format_image_data').val() );
			jQuery('#format_video_embed').val( jQuery('#format_video_embed_data').val() );
			jQuery('#format_audio_embed').val( jQuery('#format_audio_embed_data').val() );
			
			jQuery('#format_link_url').val( jQuery('#format_link_url_data').val() );

			jQuery('#format_quote_text').val( jQuery('#format_quote_text_data').val() );
			jQuery('#format_quote_source_name').val( jQuery('#format_quote_source_name_data').val() );
			jQuery('#format_quote_source_url').val( jQuery('#format_quote_source_url_data').val() );
			return true;
		});
	}
	else{
		jQuery('.wp-post-format-ui').show();
	}







	// Uploading files
    var product_gallery_frame;
    var $image_gallery_ids = $('#format_gallery_images');
    var $product_images = $('#post_images_container ul.post_images');

    jQuery('.add_post_images').on( 'click', 'a', function( event ) {

        var $el = $(this);
        var attachment_ids = $image_gallery_ids.val();

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( product_gallery_frame ) {
            product_gallery_frame.open();
            return;
        }

        // Create the media frame.
        product_gallery_frame = wp.media.frames.downloadable_file = wp.media({
            // Set the title of the modal.
            title: 'Add Images to Post Gallery',
            button: {
                text: 'Add to gallery',
            },
            multiple: true
        });

        // When an image is selected, run a callback.
        product_gallery_frame.on( 'select', function() {

            var selection = product_gallery_frame.state().get('selection');

            selection.map( function( attachment ) {

                attachment = attachment.toJSON();

                if ( attachment.id ) {
                    attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;

                    $product_images.append('\
                        <li class="image" data-attachment_id="' + attachment.id + '">\
                            <img src="' + attachment.url + '" />\
                            <ul class="actions">\
                                <li><a href="#" class="delete" title="Delete image">Delete</a></li>\
                            </ul>\
                        </li>');
                }

            } );

            $image_gallery_ids.val( attachment_ids );
        });

        // Finally, open the modal.
        product_gallery_frame.open();
    });

    // Image ordering
    $product_images.sortable({
        items: 'li.image',
        cursor: 'move',
        scrollSensitivity:40,
        forcePlaceholderSize: true,
        forceHelperSize: false,
        helper: 'clone',
        opacity: 0.65,
        placeholder: 'tt-metabox-sortable-placeholder',
        start:function(event,ui){
            ui.item.css('background-color','#f6f6f6');
        },
        stop:function(event,ui){
            ui.item.removeAttr('style');
        },
        update: function(event, ui) {
            var attachment_ids = '';

            $('#post_images_container ul li.image').css('cursor','default').each(function() {
                var attachment_id = jQuery(this).attr( 'data-attachment_id' );
                attachment_ids = attachment_ids + attachment_id + ',';
            });

            $image_gallery_ids.val( attachment_ids );
        }
    });

    // Remove images
    $('#post_images_container').on( 'click', 'a.delete', function() {

        $(this).closest('li.image').remove();

        var attachment_ids = '';

        $('#post_images_container ul li.image').css('cursor','default').each(function() {
            var attachment_id = jQuery(this).attr( 'data-attachment_id' );
            attachment_ids = attachment_ids + attachment_id + ',';
        });

        $image_gallery_ids.val( attachment_ids );

        return false;
    } );





	
});
