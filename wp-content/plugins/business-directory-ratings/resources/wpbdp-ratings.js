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
    var $rating = jQuery(this).parents(".rating");
    var rating_id = $rating.attr("data-id");
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
    var $rating = jQuery(this).parents(".rating");
    var $editform = jQuery(".rating-comment-edit", $rating);
    jQuery(".rating-comment", $rating).toggle();
    $editform.toggle()
};
WPBDP.ratings.cancelEdit = function(e) {
    e.preventDefault();
    var $rating = jQuery(this).parents(".rating");
    jQuery(".rating-comment", $rating).show();
    jQuery(".rating-comment-edit", $rating).hide()
};
WPBDP.ratings.saveEdit = function(e) {
    e.preventDefault();
    var $rating = jQuery(this).parents(".rating");
    var comment = jQuery(".rating-comment-edit textarea", $rating).val();
    jQuery.post(WPBDP.ratings._config.ajaxurl, {
        action: "wpbdp-ratings",
        a: "edit",
        id: $rating.attr("data-id"),
        comment: comment
    }, function(res) {
        if (res.success) {
            jQuery(".rating-comment-edit textarea", $rating).val(res.comment);
            jQuery(".rating-comment", $rating).html(res.comment).show();
            jQuery(".rating-comment-edit", $rating).hide()
        } else {
            alert(res.msg)
        }
    }, "json")
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
    jQuery(".listing-ratings .edit-actions .edit").click(WPBDP.ratings.handleEdit);
    jQuery(".listing-ratings .edit-actions .delete").click(WPBDP.ratings.handleDelete);
    jQuery(".listing-ratings .rating-comment-edit input.cancel-button").click(WPBDP.ratings.cancelEdit);
    jQuery(".listing-ratings .rating-comment-edit input.save-button").click(WPBDP.ratings.saveEdit)
};

$(function(){
    try{
        var $form_wrapper = $('#form_wrapper'), 
            $currentForm = $form_wrapper.children('.active'), 
            $linkform = $form_wrapper.find('.linkform');
    }catch(e){
        return;
    }
    
    $form_wrapper.children('.flip-form').each(function(i){
        var $theForm = $(this);
        if(!$theForm.hasClass('active'))
            $theForm.hide();
        $theForm.data({
            width : $theForm.width(),
            height: $theForm.height() 
       });
    });

    setWrapperWidth();
    
    $linkform.bind('click', function(e){
        var $link = $(this);
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
        e.preventDefault();
    });
    function setWrapperWidth(){
            $form_wrapper.css({
                    width	: $currentForm.data('width') + 'px',
                    height	: $currentForm.data('height') + 'px'
            });
    }
    
    $form_wrapper.find('input[type="submit"]')
     .click(function(e){
        e.preventDefault();
    });
});

jQuery(function($) {
    WPBDP.ratings.init()
});