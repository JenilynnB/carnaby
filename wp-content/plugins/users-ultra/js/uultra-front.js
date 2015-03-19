if(typeof $ == 'undefined'){
	var $ = jQuery;
}
(function($) {
    $(document).ready(function () { 
	
	   "use strict";
       
		
				
		$('.rating-s').click(function() {
			
			
			var data_id =  jQuery(this).attr("data-id");
			var data_target =  jQuery(this).attr("data-target");
			var data_vote =  jQuery(this).attr("data-vote");	
			

		
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {action: "rating_vote", data_id: data_id , data_target: data_target , data_vote: data_vote },
				
				success: function(data){
					
					alert(data);
					
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
				
        });
		
		
		$('#uu-send-private-message').click(function() {
			
			
			$( "#uu-pm-box" ).slideDown();	
			 return false;
    		e.preventDefault();
				
        });
		
		$('#uu-close-private-message-box').click(function() {		
			
			$( "#uu-pm-box" ).slideUp();	
			
			
			 return false;
    		e.preventDefault();
				
        });
		
		$('#uu-send-private-message').click(function() {			
			
			$( "#uu-upload-avatar-box" ).slideDown();	
			 return false;
    		e.preventDefault();
				
        });
		
		
		
		$('#uu-send-private-message-confirm').click(function() {
			
			
			var receiver_id =  jQuery(this).attr("data-id");
			
			var uu_subject =   $('#uu_subject').val();
			var uu_message =   $('#uu_message').val();
			
                        var $uu_error = $('#msg-error-alert-text');
                        
			if(uu_subject==""){
                            //$uu_error.removeClass("hidden");
                            $uu_error.slideDown();
                            $uu_error.html(uu_subject_empty);
                            $('#uu_subject').focus(); 
                            return;}
			if(uu_message==""){
                            //$uu_error.removeClass("hidden");
                            $uu_error.slideDown();
                            $uu_error.html(uu_message_empty);
                            //alert(uu_message_empty);  
                            $('#uu_message').focus(); 
                            return;
                        }

			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "send_private_message", "receiver_id": receiver_id , "uu_subject": uu_subject , "uu_message": uu_message },
				
				success: function(data){
					
					$('#uu_subject').val("");
					$('#uu_message').val("");
                                        $('#uu_msg_form').addClass("hidden");
                                        $uu_error.css("display", "none");
                                        //$uu_error.addClass("hidden");
                                        
                                        jQuery("#msg-success-alert-text").slideDown();
                                        $('#msg-success-alert-text').html("Your message has been sent");
                                        
					//$("#uu-message-noti-id").html(data);
                                        //setTimeout("hidde_noti('uu-message-noti-id')", 3000)	;
					//jQuery("#uu-message-noti-id").slideDown();
                                        setTimeout(function(){
                                                $('#sendMessageModal').modal('hide');
                                                $('#uu_msg_form').removeClass("hidden");
                                                $('#msg-success-alert-text').css("display", "none");
                                                $('#msg-error-alert-text').css("display", "none");
                                            }, 5000);
                                        
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 

				
        });
		
		$('#uu-reply-private-message-confirm').click(function(e) {
			e.preventDefault();
			
			var message_id =  jQuery(this).attr("message-id");			
			
			var uu_message =   $('#uu_message').val();
			
			if(uu_message==""){
                            $('#inbox-msg-error').slideDown();  
                            $('#uu_message').focus(); 
                            return;
                        }

			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "reply_private_message", "message_id": message_id ,  "uu_message": uu_message },
				
				success: function(data){
					
										
					$("#uu-message-noti-id").html(data);
					jQuery("#uu-message-noti-id").slideDown();
					setTimeout("hidde_noti('uu-message-noti-id')", 3000)	;
					 window.location.reload();
					
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 

				
        });
		
		
		$('.uu-private-message-change-status').click(function() {
			
			
			var message_id =  jQuery(this).attr("message-id");			
			var message_status =  jQuery(this).attr("message-status");			
			
			
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "message_change_status", "message_id": message_id ,  "message_status": message_status },
				
				success: function(data){
					
										
					$("#uu-message-noti-id").html(data);
					jQuery("#uu-message-noti-id").slideDown();
					setTimeout("hidde_noti('uu-message-noti-id')", 3000)	;
					window.location.reload();	
					
					
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 

				
        });
		
		$('.uu-private-message-delete').click(function() {
			
			
			var message_id =  jQuery(this).attr("message-id");			
			
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "message_delete", "message_id": message_id  },
				
				success: function(data){
					
										
					$("#uu-message-noti-id").html(data);
					jQuery("#uu-message-noti-id").slideDown();
					setTimeout("hidde_noti('uu-message-noti-id')", 3000)	;
					window.location.reload();
					
					
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 

				
        });
		
		$(document).on("click", ".btn-gallery-conf", function(e) {
			
			e.preventDefault();		
			
			
				var gal_id =  jQuery(this).attr("data-id");	
				var gal_name= $("#uultra_gall_name_edit_"+gal_id).val()	;
				var gal_desc =  $("#uultra_gall_desc_edit_"+gal_id).val();
				var gal_visibility =  $("#uultra_gall_visibility_edit_"+gal_id).val();
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "edit_gallery_confirm", "gal_id": gal_id , "gal_name": gal_name , "gal_desc": gal_desc , "gal_visibility": gal_visibility },
					
					success: function(data){					
						
												
						$( "#gallery-edit-div-"+gal_id ).slideUp();
						reload_gallery_list();
						
						
						}
				});
			
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 
				
        });
		
		$(document).on("click", "a[href='#uultra-forgot-link']", function(e) {
		
			
			e.preventDefault();
			$( "#xoouserultra-forgot-pass-holder" ).slideDown();
			 return false;
    		e.preventDefault();
			 
				
        });
		
				$(document).on("click", "#xoouserultra-reset-confirm-pass-btn", function(e) {		
			
			e.preventDefault();			
		
		
				var p1= $("#preset_password").val()	;
				var p2= $("#preset_password_2").val()	;
				var u_key= $("#uultra_reset_key").val()	;
				
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "confirm_reset_password", "p1": p1, "p2": p2, "key": u_key },
					
					success: function(data){
						
					
						jQuery("#uultra-reset-p-noti-box").html(data);
						jQuery("#uultra-reset-p-noti-box").slideDown();
						//setTimeout("hidde_noti('uultra-reset-p-noti-box')", 3000)	;			
												
												
						
						}
				});
			
			
			
			 return false;
    		e.preventDefault();
			 
				
        });
		
		$(document).on("click", "#xoouserultra-forgot-pass-btn-confirm", function(e) {		
			
			e.preventDefault();			
		
		
				var email= $("#user_name_email").val()	;
				
									
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: {"action": "send_reset_link", "user_login": email },
					
					success: function(data){
						
					
						$("#uultra-signin-ajax-noti-box").html(data);
						jQuery("#uultra-signin-ajax-noti-box").slideDown();
						setTimeout("hidde_noti('uultra-signin-ajax-noti-box')", 3000)	;			
												
												
						
						}
				});
			
			
			
			 return false;
    		e.preventDefault();
			 
				
        });
		
		//send friend request				
		$(document).on("click", "#uu-send-friend-request", function(e) {
			
			
                    var user_id =  jQuery(this).attr("data-user-id");		

                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {"action": "send_friend_request", "user_id": user_id },

                        success: function(data){					

                            //  Show the alert modal, but time it to disappear after 5 seconds
                            $('#friend-request-confirm').modal('show');
                            if(data=="success"){
                                //if success, show the success alert in the modal
                                $('#msg-success-friend-request').slideDown();

                            }else if(data=="already_sent"){
                                //if already sent, show the already sent alert in the modal
                                $('#msg-error-friend-request').slideDown();
                            }
                                
                            setTimeout(function(){
                                    $('#friend-request-confirm').modal('hide');
                                    $('#msg-success-friend-request').css("display", "none");
                                    $('#msg-error-friend-request').css("display", "none");
                            }, 5000);

                            if(data=="success"){
                            //  Then change the text in the button to "Friend requested" and grey it out
                                $('#uu-send-friend-request').html('<span class="disabled"><i class="fa fa fa-users fa-lg"></i>&nbsp;&nbsp;Friend Requested</span>');
                            }

                            //alert(data);


                            }
                    });
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 

				
        });
		
		//like item
				
		$(document).on("click", "#uu-like-item", function(e) {
			
			e.preventDefault();			
			
			var item_id =  jQuery(this).attr("item-id");
			var module =  jQuery(this).attr("data-module");	
			var vote =  jQuery(this).attr("data-vote");		
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "like_item", "item_id": item_id , "module": module , "vote": vote },
				
				success: function(data){					
										
					$("#uu-like-sore-id-"+item_id).html(data);
					
					setTimeout("get_total_likes('"+item_id+"','"+module+"')", 2000)	;
					
					
					
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 

				
        });
		
		
		$(document).on("click", "#uu-approvedeny-friend", function(e) {
			
			e.preventDefault();			
			
			var item_id =  jQuery(this).attr("item-id");			
			var item_action =  jQuery(this).attr("action-id");		
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "friend_request_action", "item_id": item_id , "item_action": item_action },
				
				success: function(data){
								
					reload_friend_request();								
					reload_my_friends();
					
					
					}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();
			 

				
        });
		
		//reset password				
		$(document).on("click", "#xoouserultra-backenedb-eset-password", function(e) {			
			
			var p1 =   $('#p1').val();
			var p2 =   $('#p2').val();		
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "confirm_reset_password_user", "p1": p1 , "p2": p2 },
				
				success: function(data){					
					
                                    
                                    if(data=="success"){
                                        $("#password_reset_error").slideUp();
                                        $("#password_reset_success").html("Success! Your password has been changed");
                                        $("#password_reset_success").slideDown();
                                    }else if(data==="not_same"){
                                        $("#password_reset_error").html("Error! Passwords must be identical");
                                        $("#password_reset_error").slideDown();
                                    }else if(data==="not_valid"){
                                        $("#password_reset_error").html("Error! Password should contain at least 7 alphanumeric characters");
                                        $("#password_reset_error").slideDown();
                                    }
                                    	
				}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();			 

				
        });
		
		//update email				
		$(document).on("click", "#xoouserultra-backenedb-update-email", function(e) {			
			
			var email =   $('#email').val();			
			
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "confirm_update_email_user", "email": email },
				
				success: function(data){					
					
                                    var error = $('#email_reset_error');
                                    var success = $('#email_reset_success');
                                    
                                    if(data==="success"){
                                        error.slideUp();
                                        success.slideDown();
                                        success.html("Success! Your email address has been changed")
                                    }else if(data==="not_entered"){
                                        error.slideDown();
                                        error.html("Error! Please type a new email adress");
                                    }else if(data==="not_new"){
                                        error.slideDown();
                                        error.html("Error! You haven't changed your email address");
                                    }else if(data==="not_valid"){
                                        error.slideDown();
                                        error.html("Error! Please type a valid email address");
                                    }else if(data==="in_use"){
                                        error.slideDown();
                                        error.html("Error! That email is already in use");
                                    }
                                    	
				}
			});
			
			 // Cancel the default action
			 return false;
    		e.preventDefault();			 

				
        });
		
		
		
		jQuery("#xoouserultra-registration-form").validationEngine({promptPosition: 'inline'});

		
		   
 
       
    }); //END READY
})(jQuery);


function get_total_likes (item_id, module)
{
	jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "get_item_likes_amount_only", "item_id": item_id , "module": module  },
				
				success: function(data){					
										
					$("#uu-like-sore-id-"+item_id).html(data);
					
													
					
					}
			});
			
		
}

function reload_my_friends ()
{
	jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "show_all_my_friends"  },
				
				success: function(data){					
										
								$("#friends-list").html(data);
					
					}
			});
			
		
}

function reload_friend_request ()
{
	jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {"action": "show_friend_request"  },
				
				success: function(data){					
										
                                    //$("#uultra-my-friends-request").html(data);
                                    var $friendRequestList = $("#friend-request-list");
                                    
                                    if(data===""){
                                        $friendRequestList.fadeOut();
                                    }else{
                                        $friendRequestList.fadeOut(500, function(){
                                            $friendRequestList.html(data);
                                            $friendRequestList.fadeIn(500);
                                        });
                                    }
                                    
					
					}
			});
			
		
}

function hidde_noti (div_d)
{
		jQuery("#"+div_d).slideUp();		
		
}

