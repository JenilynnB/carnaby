jQuery(document).ready(function($) {
	
	jQuery("body").on("click", ".uultra-photocat-edit", function(e) {
		e.preventDefault();		
		 
		 var cate_id =  jQuery(this).attr("data-gal");	
		
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "edit_photo_cate", 
						"cate_id": cate_id },
						
						success: function(data){					
						
							 $("#uu-edit-cate-box-"+cate_id).html(data);
							 jQuery("#uu-edit-cate-box-"+cate_id).slideDown();							
							
							
							}
					});
		return false;
	});
	
	jQuery("body").on("click", ".uultra-photocat-modify", function(e) {
		e.preventDefault();		
		 
		 var cate_id=  jQuery(this).attr("data-id");		
		 var cate_name = $('#uultra_photo_name_edit_'+cate_id).val();	
		 
				
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "edit_photo_cate_conf", "cate_id": cate_id, 
						"cate_name": cate_name },
						
						success: function(data){									
							//refresh
							//window.location.reload();	
							
							$("#uu-edit-cate-row-name-"+cate_id).html(data);
							 jQuery("#uu-edit-cate-box-"+cate_id).slideUp();								
													
							
							
							}
					});
		return false;
	});
	
	jQuery("body").on("click", ".uultra-photocat-del", function(e) {
		e.preventDefault();		
		 
		 var cate_id=  jQuery(this).attr("data-gal");		
			
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "edit_photo_cate_del", "cate_id": cate_id 
						 },
						
						success: function(data){
							
							//refresh
							//window.location.reload();	
							
							jQuery("#uu-edit-cate-row-"+cate_id).slideUp();						
						
													
							
							
							}
					});
		return false;
	});
	
	jQuery("body").on("click", ".uultra-photocat-close", function(e) {
		e.preventDefault();		
		 
		 var cate_id =  jQuery(this).attr("data-id");			
		jQuery("#uu-edit-cate-box-"+cate_id).slideUp();
		return false;
	});
	
});