
<div class="form_wrapper" id="form_wrapper">
    
    <div class="flip-form write-review-btn active">
        <div class="review-button flex active">
            <div class="listing-action review">
                <?php $registration_url=  site_url("/registration");?>
                <a href="<?php $registration_url?>" rel="write-review-form" class="linkform"><i class="fa fa-plus-square-o"></i>  Write a Review</a>

            </div>
        </div>
    </div>
    
    
    
    <div class="flip-form write-review-form">
    <div class="review-form ">
        <div class="review-form-header">
            <h5><?php _e('Write a review', 'wpbdp-ratings'); ?></h5>
        </div>

        <div class="form">
            <?php if ($validation_errors): ?>
                <ul class="validation-errors">
                    <?php foreach($validation_errors as $error_msg): ?>
                        <li><?php echo $error_msg; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
                <a name="rate-listing-form"></a>
                <form action="#rate-listing-form" method="POST">
                    <input type="hidden" name="listing_id" value="<?php echo get_the_ID(); ?>" />

                    <div class="field">
                        <label><?php _e('Rating:', 'wpbdp-ratings'); ?></label>
                        <span class="stars wpbdp-ratings-stars" data-value="<?php isset($review) ? $review['score'] : 0; ?>"></span>
                    </div>

                    <?php if (!is_user_logged_in()): ?>
                    <div class="field">
                        <label><?php _e('Name:', 'wpbdp-ratings'); ?>
                        <input type="text" name="user_name" size="30" value="<?php echo esc_attr(wpbdp_getv($_POST, 'user_name', '')); ?>" /></label>
                    </div>
                    <?php endif; ?>

                    <?php if ( wpbdp_get_option( 'ratings-comments' ) != 'disabled' ): ?>
                    <div class="field">
                        <textarea name="comment" cols="50" rows="3" placeholder="<?php _e('Your review.', 'wpbdp-ratings'); ?>"><?php echo esc_textarea(wpbdp_getv($_POST, 'comment', '')); ?></textarea>
                    </div>
                    <?php endif; ?>

                    <div class="submit">
                        <a href="" class="cancel-btn linkform" name="cancel_rate_listing" rel="write-review-btn">Cancel</a>
                        <input type="submit" class="submit" name="rate_listing" value="<?php _e('Post your review', 'wpbdp-ratings'); ?>" />
                    </div>
                </form>
        </div>
    </div>
    </div>
</div>
