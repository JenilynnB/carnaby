if(typeof(WPBDP)=="undefined"){WPBDP={}}if(typeof(WPBDP.ratings)=="undefined"){WPBDP.ratings={}}WPBDP.ratings._defaults={number:5,path:null,ajaxurl:null};WPBDP.ratings.handleDelete=function(e){e.preventDefault();var $rating=jQuery(this).parents(".rating");var rating_id=$rating.attr("data-id");jQuery.post(WPBDP.ratings._config.ajaxurl,{action:"wpbdp-ratings",a:"delete",id:rating_id},function(res){if(res.success){WPBDP.ratings.updateRating($rating.attr("data-listing-id"));$rating.fadeOut("fast",function(){$rating.remove()})}else{alert(res.msg)}},"json")};WPBDP.ratings.handleEdit=function(e){e.preventDefault();var $rating=jQuery(this).parents(".rating");var $editform=jQuery(".rating-comment-edit",$rating);jQuery(".rating-comment",$rating).toggle();$editform.toggle()};WPBDP.ratings.cancelEdit=function(e){e.preventDefault();var $rating=jQuery(this).parents(".rating");jQuery(".rating-comment",$rating).show();jQuery(".rating-comment-edit",$rating).hide()};WPBDP.ratings.saveEdit=function(e){e.preventDefault();var $rating=jQuery(this).parents(".rating");var comment=jQuery(".rating-comment-edit textarea",$rating).val();jQuery.post(WPBDP.ratings._config.ajaxurl,{action:"wpbdp-ratings",a:"edit",id:$rating.attr("data-id"),comment:comment},function(res){if(res.success){jQuery(".rating-comment-edit textarea",$rating).val(res.comment);jQuery(".rating-comment",$rating).html(res.comment).show();jQuery(".rating-comment-edit",$rating).hide()}else{alert(res.msg)}},"json")};WPBDP.ratings.updateRating=function(post_id){jQuery.post(WPBDP.ratings._config.ajaxurl,{action:"wpbdp-ratings",a:"info",listing_id:post_id},function(res){if(res.success){jQuery(".wpbdp-rating-info span.count .val").text(res.info.count);jQuery(".wpbdp-rating-info span.value .wpbdp-ratings-stars").raty("set",{score:res.info.average});if(res.info.count==0){jQuery(".wpbdp-ratings-reviews .no-reviews-message").show()}}else{alert(res.msg)}},"json")};WPBDP.ratings.init=function(){var $stars=jQuery(".wpbdp-ratings-stars");$stars.each(function(i,v){var $obj=jQuery(v);var rating=$obj.attr("data-value");var readOnly=false;if(typeof($obj.attr("data-readonly"))!="undefined"){readOnly=true}$obj.raty({number:WPBDP.ratings._config.number,halfShow:true,score:rating,readOnly:readOnly,path:WPBDP.ratings._config.path,hints:WPBDP.ratings._config.hints})});jQuery(".listing-ratings .edit-actions .edit").click(WPBDP.ratings.handleEdit);jQuery(".listing-ratings .edit-actions .delete").click(WPBDP.ratings.handleDelete);jQuery(".listing-ratings .rating-comment-edit input.cancel-button").click(WPBDP.ratings.cancelEdit);jQuery(".listing-ratings .rating-comment-edit input.save-button").click(WPBDP.ratings.saveEdit)};jQuery(function($){WPBDP.ratings.init()});