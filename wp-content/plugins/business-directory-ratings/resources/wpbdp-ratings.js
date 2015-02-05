if (typeof(WPBDP) == "undefined") {
    WPBDP = {}
}
if (typeof(WPBDP.ratings) == "undefined") {
    WPBDP.ratings = {}
}
WPBDP.ratings._defaults = {
    number: 5,
    path: null,
    ajaxurl: null
};
WPBDP.ratings.handleDelete = function(e) {
    e.preventDefault();
    
    var rating_id = $(this).attr("data-id");
    var $rating = jQuery(".rating[data-id='"+rating_id+"']");
    
    jQuery.post(WPBDP.ratings._config.ajaxurl, {
        action: "wpbdp-ratings",
        a: "delete",
        id: rating_id
    }, function(res) {
        if (res.success) {
            WPBDP.ratings.updateRating($rating.attr("data-listing-id"));
            $rating.fadeOut("fast", function() {
                $rating.remove()
            })
        } else {
            alert(res.msg)
        }
    }, "json")
};
WPBDP.ratings.handleEdit = function(e) {
    e.preventDefault();
    var $link = $(this);
    var $rating = jQuery(this).parents(".rating");
    try{
        var $form_wrapper = $('#form_wrapper_edit', $rating), 
            $currentForm = $form_wrapper.children('.active');
    }catch(e){
    }
    
    var target = $link.attr('rel');
    
    $currentForm.fadeOut(400, function(){
        $currentForm.removeClass('active');
        $currentForm = $form_wrapper.children("."+target);
        $form_wrapper.stop()
            .animate({
                width: $currentForm.data('width') + 'px',
                height: $currentForm.data('height') + 'px'
            }, 500, function(){
                $currentForm.addClass('active');
                $currentForm.fadeIn(500);
            });
        });
    
};
WPBDP.ratings.updateRating = function(post_id) {
    jQuery.post(WPBDP.ratings._config.ajaxurl, {
        action: "wpbdp-ratings",
        a: "info",
        listing_id: post_id
    }, function(res) {
        if (res.success) {
            jQuery(".wpbdp-rating-info span.count .val").text(res.info.count);
            jQuery(".wpbdp-rating-info span.value .wpbdp-ratings-stars").raty("set", {
                score: res.info.average
            });
            if (res.info.count == 0) {
                jQuery(".wpbdp-ratings-reviews .no-reviews-message").show()
            }
        } else {
            alert(res.msg)
        }
    }, "json")
};
WPBDP.ratings.saveEdit = function(e) {
    e.preventDefault();
    var $link = $(this);
    var $rating = jQuery(this).parents(".rating");
    var comment = jQuery(".review-edit textarea", $rating).val();
    var score = jQuery(".review-edit input[name='score'][type='hidden']", $rating).val();
    
    var errors = WPBDP.ratings.validate(comment,score);
    if(errors=="error"){
        return;
    }
    jQuery.post(WPBDP.ratings._config.ajaxurl, {
        action: "wpbdp-ratings",
        a: "edit",
        id: $rating.attr("data-id"),
        comment: comment,
        score: score
    }, function(res) {
        if (res.success) {
            
            var target = $link.attr('rel');
            try{
                var $form_wrapper = $('#form_wrapper_edit', $rating), 
                    $currentForm = $form_wrapper.children('.active');
            }catch(e){
            }
            
            jQuery(".rating-comment-edit textarea", $rating).val(res.comment);
            jQuery(".wpbdp-ratings-stars", $rating).attr('data-value', res.score);
            jQuery(".wpbdp-ratings-stars input[name='score']", $rating).attr('value', res.score);
            
            jQuery(".review-details .rating-comment", $rating).html(res.comment);
            jQuery(".review-details .wpbdp-ratings-stars", $rating).attr('data-value', res.score);
            
            
            for (i=1; i<=5; i++){
                if(i<=res.score){
                    jQuery(".wpbdp-ratings-stars i[data-score="+i+"]", $rating).attr('class', 'on');
                }else{
                    jQuery(".wpbdp-ratings-stars i[data-score="+i+"]", $rating).attr('class', 'off');
                }
            }
    
            $currentForm.fadeOut(400, function(){
                $currentForm.removeClass('active');
                $currentForm = $form_wrapper.children("."+target);
                $form_wrapper.stop()
                    .animate({
                        width: $currentForm.data('width') + 'px',
                        height: $currentForm.data('height') + 'px'
                    }, 500, function(){
                        $currentForm.addClass('active');
                        $currentForm.fadeIn(400);
                    });
            });
        } else {
            alert(res.msg)
        }
    }, "json")
};
WPBDP.ratings.saveNew = function(e) {
    e.preventDefault();
    var $rating = jQuery(this).parents(".review-form");
    var $link = $(this);
    var comment = jQuery(".form textarea", $rating).val();
    var score = jQuery("input[name='score']", $rating).val();
    var id = jQuery("input[name='listing_id']", $rating).val();
    try{
        var $form_wrapper = $('#form_wrapper'), 
            $currentForm = $form_wrapper.children('.active');
    }catch(e){
    }
    var errors = WPBDP.ratings.validate(comment,score);
    if(errors=="error"){
        return;
    }
    jQuery.post(WPBDP.ratings._config.ajaxurl, {
        action: "wpbdp-ratings",
        a: "new",
        listing_id: id,
        comment: comment,
        score: score
    }, function(res) {
        if (res.success) {
            var target = $link.attr('rel');
            jQuery(".review-form textarea", $rating).val(res.comment);
            jQuery(".listing-ratings").append(res.review);
            
            jQuery(".no-reviews-message").addClass("hidden");
            $currentForm.fadeOut(400, function(){
            $currentForm.removeClass('active');
            $currentForm = $form_wrapper.children("."+target);
            $form_wrapper.stop()
                .animate({
                    width: $currentForm.data('width') + 'px',
                    height: $currentForm.data('height') + 'px'
                }, 500, function(){
                    $currentForm.addClass('active');
                    $currentForm.fadeIn(400);
                });
            });
            var $newReview = jQuery("[class='rating'][data-listing-id='"+id+"']");
            
            var $stars = jQuery(".wpbdp-ratings-stars", $newReview);
            $stars.each(function(i, v) {
                var $obj = jQuery(v);
                var rating = $obj.attr("data-value");
                var readOnly = false;
                if (typeof($obj.attr("data-readonly")) != "undefined") {
                    readOnly = true
                }
                $obj.raty({
                    number: WPBDP.ratings._config.number,
                    halfShow: true,
                    score: rating,
                    readOnly: readOnly,
                    path: WPBDP.ratings._config.path,
                    hints: WPBDP.ratings._config.hints
                })
            });
            
            jQuery(".edit-actions .edit", $newReview).click(WPBDP.ratings.handleEdit);
            //jQuery(".edit-actions .delete", $newReview).click(WPBDP.ratings.handleDelete);
            jQuery(".review-edit .save-edit", $newReview).click(WPBDP.ratings.saveEdit);
            
        } else {
            alert(res.msg)
        }
    }, "json")
};

WPBDP.ratings.validate = function(comment, score){
    
    var $validationErrors = jQuery("#validation-errors");
    var return_val = "success";
    var $rating_error = jQuery(".validation-error-rating", $validationErrors);
    var $comment_error = jQuery(".validation-error-comment", $validationErrors);
    $rating_error.addClass("hidden");
    $comment_error.addClass("hidden");
    
    try{
        var $form_wrapper = $('#form_wrapper');
        if ($form_wrapper.length==0){
            var $form_wrapper = $('#form_wrapper_edit'); 
        }
    }catch(e){
        return;
    }
    var $currentForm = $form_wrapper.children('.active'); 
    if (score<=0 || score >5){
        $validationErrors.removeClass("hidden");
        $rating_error.removeClass("hidden");
        return_val =  "error";
        
        $form_wrapper.stop().animate({
                        width: $currentForm.data('width') + 'px',
                        height: $currentForm.data('height') + 'px'
                    }, 500);
    }
    if(comment.length<1){
        $validationErrors.removeClass("hidden");
        $comment_error.removeClass("hidden");
        return_val = "error";
        
        $form_wrapper.animate({
                width: $currentForm.width() + 'px',
                height: $currentForm.height() + 'px'
            });
            
    }
    return return_val;
}

WPBDP.ratings.switchView = function(e){
    e.preventDefault();
    try{
        var $form_wrapper = $('#form_wrapper'), 
            $currentForm = $form_wrapper.children('.active');
    }catch(e){
        return;
    }
    var $link = jQuery(this);
    var target = $link.attr('rel');
    
    
    $currentForm.fadeOut(400, function(){
        $currentForm.removeClass('active');
        $currentForm = $form_wrapper.children("."+target);
    $form_wrapper.stop()
            .animate({
                width: $currentForm.data('width') + 'px',
                height: $currentForm.data('height') + 'px'
            }, 500, function(){
                $currentForm.addClass('active');
                $currentForm.fadeIn(400);
            });
    });
    
}

WPBDP.ratings.init = function() {
    var $stars = jQuery(".wpbdp-ratings-stars");
    $stars.each(function(i, v) {
        var $obj = jQuery(v);
        var rating = $obj.attr("data-value");
        var readOnly = false;
        if (typeof($obj.attr("data-readonly")) != "undefined") {
            readOnly = true
        }
        $obj.raty({
            number: WPBDP.ratings._config.number,
            halfShow: true,
            score: rating,
            readOnly: readOnly,
            path: WPBDP.ratings._config.path,
            hints: WPBDP.ratings._config.hints
        })
    });
    
    try{
        $('#confirm-review-delete').on('show.bs.modal', function(e){
            $(this).find(".confirm-delete").attr('data-id', $(e.relatedTarget).attr('data-id'));
        });
    }catch(e){}
    
    jQuery(".listing-ratings .edit-actions .edit").click(WPBDP.ratings.handleEdit);
    //jQuery(".listing-ratings .edit-actions .delete").click(WPBDP.ratings.handleDelete);
    jQuery(".modal .confirm-delete").click(WPBDP.ratings.handleDelete);
    jQuery(".listing-ratings .review-edit .cancel-edit").click(WPBDP.ratings.handleEdit);
    jQuery(".listing-ratings .review-edit .save-edit").click(WPBDP.ratings.saveEdit);
    jQuery("#save-new-rate-listing").click(WPBDP.ratings.saveNew);
    jQuery(".flip-form .write-review-btn-trigger").click(WPBDP.ratings.switchView);
    //jQuery(".flip-form .cancel_rate_listing").click(WPBDP.ratings.switchView);
    
};




jQuery(function($) {
    WPBDP.ratings.init()
});