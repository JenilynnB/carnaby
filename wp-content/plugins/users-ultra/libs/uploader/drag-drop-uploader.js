jQuery(document).ready(function($){
	
	// Set required variables:
	var dgdUploaderButtonClicked = false;
	
	// Media button hook:
	//$('div.wp-media-buttons a.insert-media, div.wp-media-buttons a.add_media').live('click', function()
	//{
		//dgdUploaderButtonClicked = true;
	//});
	
	$(document).on("click", "div.wp-media-buttons a.insert-media, div.wp-media-buttons a.add_media", function(e) {
		
		dgdUploaderButtonClicked = true;
	
	});
	
	// Media Library button hook (WP >= 3.5):
	//$('a#dgd_library_button').live('click', function(){
		
	$(document).on("click", "a#dgd_library_button", function(e) {	
		dgdUploaderButtonClicked = true;
		$('div.media-frame div.media-menu a.media-menu-item').eq(2).click();
		$('div.media-frame div.media-frame-router a.media-menu-item').eq(1).click();
		
		// Media Library close hook:
		if (typeof wp !== 'undefined'){
			var editor_id = $('.wp-media-buttons:eq(0) .add_media').attr('data-editor');
			var media_editor = wp.media.editor.get(editor_id);
			media_editor = 'undefined' != typeof(media_editor) ? media_editor : wp.media.editor.add(editor_id);
			if (media_editor) {
				media_editor.on('escape', function(){
					dgdUploaderButtonClicked = false;
				});
			}
		}
		
	});
	
		
		
	build_delete_pic();
	
		
	

	
});

function build_delete_pic()
{
	
	jQuery('.delete ').click(function(){
		
		item_id = jQuery(this).data("id");
		
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "delete_photo_from_gallery", "item_id": item_id },
			
			success: function(data){
				
				reload_imagesInit();
				
				
				
				}
		});
	return false;
	});
	

}
function reload_imagesInit()
		{
			jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {"action": "reload_images" , "post_id":  dgd_post_id },
			
			success: function(data){
				
				
				jQuery("#resp_t_image_list").html(data);
				build_delete_pic();
								
							
				
				
				}
		});
	      
	    }


