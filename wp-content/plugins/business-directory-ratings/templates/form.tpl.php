    
<div class="form_wrapper" id="form_wrapper">

    <?php if(!has_written_review() || !is_user_logged_in()): ?>
        
        <div class="flip-form write-review-btn review-trigger active">
            <div class="review-button flex active">
                <div class="listing-action review">
                    <?php if(is_user_logged_in()):?>

                        <a href="javascript:void(0)" rel="write-review-form" class="linkform write-review-btn-trigger">
                            <i class="fa fa-plus-square-o"></i>  Write a Review
                        </a>
                    <?php else:?>
                        <?php $registration_url=  site_url("/registration");?>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#registrationModal">
                            <i class="fa fa-plus-square-o"></i>  Write a Review
                        </a>
                        <!--<a href="<?php $registration_url?>" rel="registration-form" class="linkform write-review-btn-trigger"><i class="fa fa-plus-square-o"></i>  Write a Review</a>-->
                    <?php endif;?>
                </div>
            </div>
        </div>
       
    <?php endif; ?>
    
    <?php if (!is_user_logged_in()):?>
        <?php 
        $modal_class = "";
        $modal_style = '';
        $modal_hidden = 'false';
        $reg_modal = "active";
        $login_modal = "";
        
        if (isset($_POST['xoouserultra-register-form'])) : 
            $modal_class = "in";
            $modal_style = "style=display:block;";
            $modal_hidden = "true";
        elseif (isset($_POST['xoouserultra-login'])) :
            $modal_class = "in";
            $modal_style = "style=display:block;";
            $modal_hidden = "true";
            $login_modal = "active";
            $reg_modal = "";
        endif; 
        
        ?>
    
        <div class="modal fade <?php echo $modal_class;?>"  <?php echo $modal_style;?> id="registrationModal" tabindex="-1" role="dialog" aria-labelledby="registrationModalLabel" aria-hidden="<?php echo $modal_hidden; ?>">
            <div class="modal-dialog" id="reg-modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="reg_form_wrapper" id="reg_form_wrapper">
                        <div class="flip-form registration-form registration-form-embed review-action  <?php echo $reg_modal;?>">
                                <div class="alert alert-warning text-center" id="reg-alert-warning">
                                    <?php _e('(Please create an account or log in to write a review or save stores)', 'wpbdp-ratings'); ?>
                                </div>
                                <?php echo do_shortcode("[usersultra_registration]");?>

                        </div>
                        <div class="flip-form login-form login-form-embed  <?php echo $login_modal;?>" id="login-alert-warning">

                                <div class="alert alert-warning text-center">
                                    <?php _e('(Please create an account or log in to write a review or save stores)', 'wpbdp-ratings'); ?>
                                </div>
                                <?php echo do_shortcode("[usersultra_login]"); ?>

                        </div>
                    </div>            
                </div>
            </div> 
        </div>
    
       <!--
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
        -->
    <?php else: ?>
    
        <div class="flip-form write-review-form review-action">
            <div class="review-form ">
                <div class="review-form-header">

                    <h5><?php _e(has_written_review()?'Edit My Review':'Write a review', 'wpbdp-ratings'); ?></h5>

                </div>

                <div class="form">
                    <div class="alert alert-danger hidden" id="validation-errors">
                        <ul>
                            <li class="validation-error-rating hidden">Please enter a rating for this store</li>
                            <li class="validation-error-comment hidden">Please write a review to submit with your rating</li>
                            <li class="validation-error-user hidden">Please log in to rate this store</li>
                        </ul>
                    </div>
                    
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
