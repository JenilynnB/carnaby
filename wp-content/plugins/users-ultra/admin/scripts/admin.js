
var $ = jQuery;

jQuery(document).ready(function($) {

	jQuery("#uultra-add-new-custom-field-frm").slideUp();
	
	
	/* open package form */
	jQuery('.uultra-add-new-package').live('click',function(e){
		e.preventDefault();
		
		jQuery("#uultra-add-package").slideDown();
				
		return false;
	});
	
	/* close package form */
	jQuery('.uultra-close-new-package').live('click',function(e){
		e.preventDefault();
		
		jQuery("#uultra-add-package").slideUp();
				
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER -  Edit Field Form */
	jQuery('.uultra-btn-edit-field').live('click',function(e)
	{
		
		e.preventDefault();
		var block_id =  jQuery(this).attr("data-edition");		
		jQuery("#uu-edit-fields-bock-"+block_id).slideDown();				
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER -  ClosedEdit Field Form */
	jQuery('.uultra-btn-close-edition-field').live('click',function(e)
	{
		
		e.preventDefault();
		var block_id =  jQuery(this).attr("data-edition");		
		jQuery("#uu-edit-fields-bock-"+block_id).slideUp();				
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER -  Add New Field Form */
	jQuery('#uultra-add-field-btn').live('click',function(e)
	{
		
		e.preventDefault();
			
		jQuery("#uultra-add-new-custom-field-frm").slideDown();				
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER -  restore default */
	jQuery('#uultra-restore-fields-btn').live('click',function(e)
	{
		
		e.preventDefault();
		
		doIt=confirm(custom_fields_reset_confirmation);
		  
		  if(doIt)
		  {
			// jQuery("#uultra-sucess-delete-fields").slideDown();	
			  
				jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "custom_fields_reset", 
						"p_confirm": "yes" },
						
						success: function(data){
							
							jQuery("#fields-mg-reset-conf").slideDown();			
						
							 window.location.reload();						
							
							
							}
					});
			
		  }
			
					
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER -  Add New Field Form */
	jQuery('#uultra-close-add-field-btn').live('click',function(e){
		
		e.preventDefault();
			
		jQuery("#uultra-add-new-custom-field-frm").slideUp();				
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER - Delete Field */
	jQuery('.uultra-delete-profile-field-btn').live('click',function(e)
	{
		e.preventDefault();
		
		var doIt = false;
		
		doIt=confirm(custom_fields_del_confirmation);
		  
		  if(doIt)
		  {
			  
			  var p_id =  jQuery(this).attr("data-field");	
		
				jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "delete_profile_field", 
						"_item": p_id },
						
						success: function(data){					
						
							//alert("a");	
							
							jQuery("#uultra-sucess-delete-fields-"+p_id).slideDown();
						    setTimeout("hidde_noti('uultra-sucess-delete-fields-" + p_id +"')", 3000);
							jQuery( "#"+p_id ).addClass( "uultra-deleted" );
							setTimeout("hidde_noti('" + p_id +"')", 5000);
							
													
							
							
							}
					});
			
		  }
		  else{
			
		  }		
		
				
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER - Update Field Data */
	jQuery('.uultra-btn-submit-field').live('click',function(e){
		e.preventDefault();
		
		var key_id =  jQuery(this).attr("data-edition");	
		
		$('#p_name').val()		  
		
		var _position = $("#uultra_" + key_id + "_position").val();		
		var _type =  $("#uultra_" + key_id + "_type").val();
		var _field = $("#uultra_" + key_id + "_field").val();		
		var _meta =  $("#uultra_" + key_id + "_meta").val();
		var _meta_custom = $("#uultra_" + key_id + "_meta_custom").val();		
		var _name = $("#uultra_" + key_id + "_name").val();
		var _tooltip =  $("#uultra_" + key_id + "_tooltip").val();		
		var _social =  $("#uultra_" + key_id + "_social").val();
		
		var _can_edit =  $("#uultra_" + key_id + "_can_edit").val();		
		var _allow_html =  $("#uultra_" + key_id + "_allow_html").val();
		var _can_hide =  $("#uultra_" + key_id + "_can_hide").val();		
		var _private = $("#uultra_" + key_id + "_private").val();
		var _required =  $("#uultra_" + key_id + "_required").val();		
		var _show_in_register = $("#uultra_" + key_id + "_show_in_register").val();
		var _choices =  $("#uultra_" + key_id + "_choices").val();	
		var _predefined_options =  $("#uultra_" + key_id + "_predefined_options").val();	
		
		
		var _icon =  $('input:radio[name=uultra_' + key_id +'_icon]:checked').val();

		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "save_fields_settings", 
						"_position": _position , "_type": _type ,
						"_field": _field ,
						"_meta": _meta ,"_meta_custom": _meta_custom  
						,"_name": _name  ,"_tooltip": _tooltip ,
						"_tooltip": _tooltip ,
						"_social": _social ,
						"_icon": _icon ,
						"_can_edit": _can_edit ,"_allow_html": _allow_html  ,
						"_can_hide": _can_hide  ,"_private": _private, 
						"_required": _required  ,"_show_in_register": _show_in_register ,
						"_choices": _choices,  "_predefined_options": _predefined_options, "pos": key_id },
						
						success: function(data){		
						
												
						jQuery("#uultra-sucess-fields-"+key_id).slideDown();
						setTimeout("hidde_noti('uultra-sucess-fields-" + key_id +"')", 3000)		
						
							 							
							
							
							}
					});
			
		 
		
				
		return false;
	});
	
	
	/* 	FIELDS CUSTOMIZER - Add New Field Data */
	jQuery('#uultra-btn-add-field-submit').live('click',function(e){
		e.preventDefault();
		
		
		  
		
		var _position = $("#uultra_position").val();		
		var _type =  $("#uultra_type").val();
		var _field = $("#uultra_field").val();		
		var _meta =  $("#uultra_meta").val();
		var _meta_custom = $("#uultra_meta_custom").val();		
		var _name = $("#uultra_name").val();
		var _tooltip =  $("#uultra_tooltip").val();		
		var _social =  $("#uultra_social").val();
		var _can_edit =  $("#uultra_can_edit").val();		
		var _allow_html =  $("#uultra_allow_html").val();
		var _can_hide =  $("#uultra_can_hide").val();		
		var _private = $("#uultra_private").val();
		var _required =  $("#uultra_required").val();		
		var _show_in_register = $("#uultra_show_in_register").val();
		var _choices =  $("#uultra_choices").val();	
		var _predefined_options =  $("#uultra_predefined_options").val();	
		
		 
		
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "add_new_custom_profile_field", 
						"_position": _position , "_type": _type ,"_field": _field 
						,"_meta": _meta ,"_meta_custom": _meta_custom  
						,"_name": _name  ,"_tooltip": _tooltip ,
						"_tooltip": _tooltip ,"_social": _social ,
						"_can_edit": _can_edit ,"_allow_html": _allow_html  ,
						"_can_hide": _can_hide  ,"_private": _private, 
						"_required": _required  ,"_show_in_register": _show_in_register ,
						"_choices": _choices,  "_predefined_options": _predefined_options },
						
						success: function(data){		
						
												
						jQuery("#uultra-sucess-add-field").slideDown();
						setTimeout("hidde_noti('uultra-sucess-add-field')", 3000)		
						//alert("done");
						
						window.location.reload();
							 							
							
							
							}
					});
			
		 
		
				
		return false;
	});
	
	/* 	FIELDS CUSTOMIZER -  Add New Field Form */
	jQuery('#uultra-btn-sync-btn').live('click',function(e){
		
		e.preventDefault();
		
		$("#uultra-sync-results").html(message_sync_users);		
		jQuery("#uultra-sync-results").slideDown();
		
		
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "sync_users"},
						
						success: function(data){					
						
							 $("#uultra-sync-results").html(data);
							 jQuery("#uultra-sync-results").slideDown();
							 setTimeout("hidde_noti('uultra-sync-results')", 5000)
													
							
							
							}
					});
			
				
		return false;
	});
	
	
	
	
	
	
	


    /* close package form */
	jQuery('.uultra-package-edit').live('click',function(e){
		e.preventDefault();
		
		 
		 var p_id =  jQuery(this).attr("data-package");	
		
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "package_edit_form", 
						"p_id": p_id },
						
						success: function(data){					
						
							 $("#uu-edit-package-box-"+p_id).html(data);
							 $('.color-picker').wpColorPicker();
							 jQuery("#uu-edit-package-box-"+p_id).slideDown();							
							
							
							}
					});
			
		 	
		
				
		return false;
	});
	
	/* close package form */
	jQuery('.uultra-package-deactivate').live('click',function(e){
		e.preventDefault();
		
		doIt=confirm(package_confirmation);
		  
		  if(doIt)
		  {
			  
			  var p_id =  jQuery(this).attr("data-package");	
		
				jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "package_delete", 
						"p_id": p_id },
						
						success: function(data){					
						
							 reload_packages();								
							
							
							}
					});
			
		  }
		  else{
			
		  }		
		
				
		return false;
	});
	
	/* confirm package addition */
	jQuery('.uultra-add-new-package-confirm').live('click',function(e){
		e.preventDefault();
		
		//validate
		
		var p_name = $('#p_name').val();
		var p_desc = $('#p_desc').val();			
		var p_price = $('#p_price').val();
		
		var p_period = $('#p_period').val();
		var p_every = $('#p_every').val();
		var p_type = $('#p_type').val();
		
		
		var p_moderation = $('#p_moderation').val();
		var p_max_photos = $('#p_max_photos').val();
		var p_max_gallery = $('#p_max_gallery').val();	
		
		
		var p_price_color = $('#p_price_color').val();
		var p_price_bg_color = $('#p_price_bg_color').val();
		
		var p_signup_color = $('#p_signup_color').val();
		var p_signup_bg_color = $('#p_signup_bg_color').val();
				
		
		if(p_name==""){alert(package_error_message_name);return}
		if(p_desc==""){alert(package_error_message_desc);return}
		if(p_price==""){alert(package_error_message_price);return}
		
		
		
		jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "package_add_new", "p_name": p_name , "p_desc": p_desc , 				"p_price": p_price, "p_period": p_period  , "p_every": p_every ,  "p_type": p_type ,  "p_moderation": p_moderation ,  "p_max_photos": p_max_photos ,  "p_max_gallery": p_max_gallery ,  "p_price_color": p_price_color ,  "p_price_bg_color": p_price_bg_color ,  "p_signup_color": p_signup_color ,  "p_signup_bg_color": p_signup_bg_color},
				
				success: function(data){	
				
				
				     reload_packages();
								
									
					
					
					}
			});
			
		
		
		
		jQuery("#uultra-add-package").slideDown();
				
		return false;
	});
	
	/* confirm package addition */
	jQuery('.uultra-edit-package-confirm').live('click',function(e){
		e.preventDefault();
		
		 var p_id =  jQuery(this).attr("data-package");
		
		//validate
		
		var p_name = $('#p_name_'+p_id).val();
		var p_desc = $('#p_desc_'+p_id).val();			
		var p_price = $('#p_price_'+p_id).val();
		
		var p_period = $('#p_period_'+p_id).val();
		var p_every = $('#p_every_'+p_id).val();
		var p_type = $('#p_type_'+p_id).val();
		
		
		var p_moderation = $('#p_moderation_'+p_id).val();
		var p_max_photos = $('#p_max_photos_'+p_id).val();
		var p_max_gallery = $('#p_max_gallery_'+p_id).val();	
		
		
		var p_price_color = $('#p_price_color_'+p_id).val();
		var p_price_bg_color = $('#p_price_bg_color_'+p_id).val();
		
		var p_signup_color = $('#p_signup_color_'+p_id).val();
		var p_signup_bg_color = $('#p_signup_bg_color_'+p_id).val();
				
		
		if(p_name==""){alert(package_error_message_name);return}
		if(p_desc==""){alert(package_error_message_desc);return}
		if(p_price==""){alert(package_error_message_price);return}
		
		
		
		jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "package_edit_confirm", "p_id":p_id,  "p_name": p_name , "p_desc": p_desc , 				"p_price": p_price, "p_period": p_period  , "p_every": p_every ,  "p_type": p_type ,  "p_moderation": p_moderation ,  "p_max_photos": p_max_photos ,  "p_max_gallery": p_max_gallery ,  "p_price_color": p_price_color ,  "p_price_bg_color": p_price_bg_color ,  "p_signup_color": p_signup_color ,  "p_signup_bg_color": p_signup_bg_color},
				
				success: function(data){
					
					//display message							
					
					$("#package-edit-noti-mess-"+p_id).html(data);
					jQuery("#package-edit-noti-mess-"+p_id).slideDown();
					setTimeout("reload_packages()", 2000);		
				
					
					
					}
			});
			
		
		
		
		e.preventDefault();
				
		return false;
	});
	
	/* Verify user */
	
	jQuery('.uultradmin-user-approve').live('click',function(e){
		e.preventDefault();
		
		 
		 var user_id =  jQuery(this).attr("user-id");	
		
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "user_approve_pending_account", 
						"user_id": user_id },
						
						success: function(data){					
						
								$("#uultra-user-acti-noti").html(data);
								//jQuery("#package-edit-noti-mess-"+p_id).slideDown();
								setTimeout("reload_pending_moderation()", 2000);	
								setTimeout("reload_pending_payment()", 2000);					
							
							
							}
					});
			
		 	
		
				
		return false;
	});
	
	
	/* Verify user */
	
	jQuery('.uultradmin-user-approve-2').live('click',function(e){
		e.preventDefault();
		
		 
		 var user_id =  jQuery(this).attr("user-id");	
		
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "user_approve_pending_account", 
						"user_id": user_id },
						
						success: function(data){					
						
								$("#uultra-user-acti-noti").html(data);
								setTimeout("reload_pending_activation()", 2000);	
														
							
							}
					});
		
				
		return false;
	});
	
	/* Unverify user */
	
		jQuery('.uultradmin-user-deny').live('click',function(e){
		e.preventDefault();
		
		 
		 var user_id =  jQuery(this).attr("user-id");	
		
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "user_delete_account", 
						"user_id": user_id },
						
						success: function(data){					
						
								$("#uultra-user-acti-noti").html(data);
								//jQuery("#package-edit-noti-mess-"+p_id).slideDown();
								setTimeout("reload_pending_moderation()", 2000);
								setTimeout("reload_pending_payment()", 2000);						
							
							
							}
					});
			
		 	
		
				
		return false;
	});
	
	
	jQuery('#uultradmin-confirm-rearrange').live('click',function(e){
		e.preventDefault();
		
		
		 var itemList = jQuery('#uu-fields-sortable');
		
		$('#loading-animation').show(); // Show the animate loading gif while waiting
		
		opts = {
                url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
                type: 'POST',
                async: true,
                cache: false,
                dataType: 'json',
                data:{
                    action: 'sort_fileds_list', // Tell WordPress how to handle this ajax request
                    order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
                },
                success: function(response) {
                    $('#loading-animation').hide(); // Hide the loading animation
					window.location.reload();
                    return; 
                },
                error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
                    alert(e);
                    // alert('There was an error saving the updates');
                  //  $('#loading-animation').hide(); // Hide the loading animation
                    return; 
                }
            };
            jQuery.ajax(opts);
		
		 
		
			 	
		
				
		return false;
	});
	
		//create upload folder
	jQuery('#uultradmin-create-upload-folder').live('click',function(e){
		e.preventDefault();
		
		 
	
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "create_uploader_folder" 
						 },
						
						success: function(data){
							
							window.location.reload();
								
							
							}
					});
			
		 	
		
				
		return false;
	});
	
	jQuery('#uultradmin-create-basic-fields').live('click',function(e){
		e.preventDefault();
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "create_default_pages_auto" 
						 },
						
						success: function(data){
							
							window.location.reload();
								
							
							}
					});
			
		return false;
	});
	
	jQuery('#uultradmin-remove-ratingmessage').live('click',function(e){
		e.preventDefault();
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "hide_rate_message" 
						 },
						
						success: function(data){
							
							window.location.reload();
								
							
							}
					});
			
		return false;
	});
	
	jQuery('#uultradmin-remove-proversionmessage').live('click',function(e){
		e.preventDefault();
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "hide_proversion_message" 
						 },
						
						success: function(data){
							
							window.location.reload();
								
							
							}
					});
			
		return false;
	});
	
	//user ultra re-send activation link
	jQuery(document).on("click", ".uultradmin-user-resend-link", function(e) {
		
		e.preventDefault();	
		 
		 var user_id =  jQuery(this).attr("user-id");
		jQuery.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {"action": "user_resend_activation_link", 
						"user_id": user_id },
						
						success: function(data){			
									
						
							//jQuery("#uu-edit-user-box-"+user_id).html(data);
							//jQuery("#uu-edit-user-box-"+user_id).slideDown();							
							
							
							}
					});
			
		return false;
	});
	
	
});

function reload_packages ()	
	{
		  jQuery.post(ajaxurl, {
							action: 'get_packages_ajax'
									
							}, function (response){									
																
							jQuery("#usersultra-data_list").html(response);
							jQuery('.color-picker').wpColorPicker();
							jQuery(".user-ultra-success").slideDown();
							setTimeout("hidde_package_noti('user-ultra-success')", 3000);
									
														
		 });
		
	}

function reload_pending_moderation ()	
	{
		  jQuery.post(ajaxurl, {
							action: 'get_pending_moderation_list'
									
							}, function (response){									
																
							jQuery("#uultra-pending-moderation-list").html(response);
																
														
		 });
		
}

function reload_pending_payment ()	
	{
		  jQuery.post(ajaxurl, {
							action: 'get_pending_payment_list'
									
							}, function (response){									
																
							jQuery("#uultra-pending-payment-list").html(response);
																
														
		 });
		
}


function reload_pending_activation ()	
	{
		  jQuery.post(ajaxurl, {
							action: 'get_pending_activation_list'
									
							}, function (response){									
																
							jQuery("#uultra-pending-activation-list").html(response);
																
														
		 });
		
}


function hidde_package_noti (div_d)
{
		jQuery("."+div_d).slideUp();		
		
}

function hidde_noti (div_d)
{
		jQuery("#"+div_d).slideUp();		
		
}

function sortable_fields_list ()
{
	 var itemList = jQuery('#uu-fields-sortable');

   /*
    itemList.sortable({
        update: function(event, ui) {
           // $('#loading-animation').show(); // Show the animate loading gif while waiting

            opts = {
                url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
                type: 'POST',
                async: true,
                cache: false,
                dataType: 'json',
                data:{
                    action: 'sort_fileds_list', // Tell WordPress how to handle this ajax request
                    order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
                },
                success: function(response) {
                   // $('#loading-animation').hide(); // Hide the loading animation
                    return; 
                },
                error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
                    alert(e);
                    // alert('There was an error saving the updates');
                  //  $('#loading-animation').hide(); // Hide the loading animation
                    return; 
                }
            };
            jQuery.ajax(opts);
        }
    }); 
	
	*/
	

    itemList.sortable({
        update: function(event, ui) {
           // $('#loading-animation').show(); // Show the animate loading gif while waiting

           
        }
    }); 
	
	
	
}

jQuery(document).ready(function($) 
{ 
   sortable_fields_list();
});