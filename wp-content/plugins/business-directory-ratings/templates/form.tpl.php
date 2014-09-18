    
<div class="form_wrapper" id="form_wrapper">

        
    <?php if(!has_written_review() || !is_user_logged_in()): ?>
        
        <div class="flip-form write-review-btn review-trigger active">
            <div class="review-button flex active">
                <div class="listing-action review">
                    <?php if(is_user_logged_in()):?>

                        <a href="" rel="write-review-form" class="linkform write-review-btn-trigger"><i class="fa fa-plus-square-o"></i>  Write a Review</a>
                    <?php else:?>
                        <?php $registration_url=  site_url("/registration");?>
                        <a href="<?php $registration_url?>" rel="registration-form" class="linkform write-review-btn-trigger"><i class="fa fa-plus-square-o"></i>  Write a Review</a>
                    <?php endif;?>
                </div>
            </div>
        </div>
       
    <?php endif; ?>
    
    <?php if (!is_user_logged_in()):?>
        <div class="flip-form registration-form registration-form-embed review-action">
            <div class="registration-form-wrapper">
                <div class="alert alert-warning text-center">
                    <?php _e('(Please register or login to rate this listing)', 'wpbdp-ratings'); ?>
                </div>
                <?php echo do_shortcode("[usersultra_registration]");?>
            </div> 
        </div>
        <div class="flip-form login-form login-form-embed">
            <div class="login-form-wrapper">
                <div class="alert alert-warning text-center">
                    <?php _e('(Please register or login to rate this listing)', 'wpbdp-ratings'); ?>
                </div>
                <?php echo do_shortcode("[usersultra_login]"); ?>
            </div>
        </div>
    <?php else: ?>
    
        <div class="flip-form write-review-form review-action">
            <div class="review-form ">
                <div class="review-form-header">

                    <h5><?php _e(has_written_review()?'Edit My Review':'Write a review', 'wpbdp-ratings'); ?></h5>

                </div>

                <div class="form">
                    <?php if ($validation_errors): ?>
                        <ul class="validation-errors">
                            <?php foreach($validation_errors as $error_msg): ?>
                                <li><?php echo $error_msg; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                        <a name="rate-listing-form" id="rate-listing-form"></a>
                        <!--<form action="#rate-listing-form" method="POST">-->
                            <input type="hidden" name="listing_id" value="<?php echo get_the_ID(); ?>" />

                            <div class="field">
                                <label><?php _e('Rating:', 'wpbdp-ratings'); ?></label>
                                <?php 
                                    if(isset($review)){
                                        $data_value = $review->rating;
                                    }else if(isset($review_to_edit)){
                                        $data_value = $review_to_edit->rating;
                                    }else{
                                        $data_value = 0;
                                    }

                                ?>
                                <span class="stars wpbdp-ratings-stars" data-value="<?php echo $data_value; ?>"></span>

                            </div>

                            <?php if (!is_user_logged_in()): ?>
                            <div class="field">
                                <label><?php _e('Name:', 'wpbdp-ratings'); ?>
                                <input type="text" name="user_name" size="30" value="<?php echo esc_attr(wpbdp_getv($_POST, 'user_name', '')); ?>" /></label>
                            </div>
                            <?php endif; ?>

                            <?php if ( wpbdp_get_option( 'ratings-comments' ) != 'disabled' ): ?>
                            <div class="field">
                                <?php if ($edit_review){?>
                                    <textarea name="comment" cols="50" rows="3" ><?php  echo esc_textarea($review_to_edit->comment ); ?></textarea>
                                <?php } else { ?>
                                    <textarea name="comment" cols="50" rows="3" placeholder="<?php _e('Your review.', 'wpbdp-ratings'); ?>"><?php echo esc_textarea(wpbdp_getv($_POST, 'comment', '')); ?></textarea>
                                <?php } ?>   

                            </div>
                            <?php endif; ?>

                            <div class="submit">

                                <a href="" class="btn btn-link linkform cancel_rate_listing" id="cancel_rate_listing" rel=<?php echo $edit_review?"edit-actions":"write-review-btn";?> >Cancel</a>
                                <?php if(has_written_review()){ ?>
                                    <a href="" class="submit btn btn-primary btn-md linkform" id="save-edit-rate-listing" rel="edit-actions">Save My Review</a>
                                <?php }else{ ?>
                                    <a href="" class="submit btn btn-primary btn-md linkform" id="save-new-rate-listing" rel="edit-actions">Post My Review</a>
                                <?php } ?>
                            </div>
                        <!--</form>-->
                </div>
            </div>
        </div>
    
    <?php endif; ?>
    
</div>
