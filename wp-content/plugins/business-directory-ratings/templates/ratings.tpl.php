<section class="primary-section reviews">
    <div class="container">   
    <a name="ratings"></a>
        <?php global $xoouserultra;   ?>
        <div class="row">
            <div class="col-md-8">


                <div class="wpbdp-ratings-reviews">
                    <h4><?php _e('Reviews', 'wpbdp-ratings'); ?></h4>

                    <div class="col-md-12">
                    <div class="row">
                        <div class="listing-ratings">
                            <?php if ($ratings): ?>
                                <div class="row">
                                    <?php if ($success): ?>
                                        <div class="alert alert-success">
                                            <?php if (wpbdp_get_option('ratings-require-approval')): ?>
                                                <?php _e('Your review has been saved and is waiting for approval.', 'wpbdp-ratings'); ?>
                                            <?php else: ?>
                                                <?php _e('Your review has been published!', 'wpbdp-ratings'); ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>

                                        <div class="message">
                                            <?php if ($reason == 'already-rated'): ?>
                                                <?php _e('(You have already rated this listing)', 'wpbdp-ratings'); ?>


                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                        
                        
                                <!--Loop for each review-->
                                <?php foreach ($ratings as $i => $rating): 
                                
                                $vars=array();
                                $vars['rating'] = $rating;
                                echo wpbdp_render(WPBDP_RATINGS_TEMPLATES_PATH.'/single-review.tpl.php', $vars);
                                ?>
                                
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                        

                        <div class="row">
                            <!--Message for no reviews-->
                            <p class="no-reviews-message" style="<?php echo $ratings ? 'display: none;' : ''?>"><?php _e('There are no reviews yet.', 'wpbdp-ratings'); ?></p>
                            
                            <?php if (!has_written_review() || !is_user_logged_in()) : ?>
                                <?php echo $review_form; ?>
                            <?php else: ?>
                                
                            <?php endif; ?>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade modal-small" id="confirm-review-delete" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body">
        <p>Are you sure you want to delete this review?</p>
        <button type="button" class="btn btn-default" data-dismiss="modal">Woah, no way!</button>
        <button type="button" class="btn btn-primary confirm-delete" data-dismiss="modal">Yep, trash it</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->