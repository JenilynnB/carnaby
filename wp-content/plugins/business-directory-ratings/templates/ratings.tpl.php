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
                                <?php foreach ($ratings as $i => $rating): ?>


                                <div class="rating <?php echo $i % 2 == 0 ? 'odd' : 'even'; ?>" data-id="<?php echo $rating->id; ?>" data-listing-id="<?php echo get_the_ID(); ?>"
                                     itemprop="review" itemscope itemtype="http://schema.org/Review">


                                    <div class="row">
                                        <div class="col-md-2">
                                        <!--Author info--> 

                                            <div class="rating-authoring-info">
                                                <?php echo get_user_profile_thumb_circle(100, $rating->user_id);?>
                                                <div class="author" itemprop="author">

                                                    <?php if ($rating->user_id == 0): ?>
                                                        <?php echo esc_attr($rating->user_name); ?>
                                                    <?php else: ?>
                                                        <?php 

                                                            $author_first_name = get_the_author_meta('first_name', $rating->user_id);
                                                            $author_last_name = get_the_author_meta('last_name', $rating->user_id);
                                                            if($author_last_name!=''){
                                                                $author_last_initial = substr($author_last_name,0,1);
                                                                $author_display_name = $author_first_name." ".$author_last_initial.".";
                                                            }else{
                                                                $author_display_name = $author_first_name;
                                                            }
                                                            $user_profile_url = site_url('/profile/?uu_username='.$rating->user_id);
                                                        ?>
                                                        <a href="<?php echo $user_profile_url; ?>"><?php echo $author_display_name;?></a>

                                                    <?php endif; ?>
                                                </div>
                                            </div>  
                                        </div>

                                        <div class="col-md-10">
                                            <div id='form_wrapper_edit' class='form-wrapper'>
                                                <div class='flip-form review-details active' style='width:100%'>
                                                    <!--Star rating-->
                                                    <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                                                        <meta itemprop="worstRating" content="0" />
                                                        <meta itemprop="ratingValue" content="<?php echo $rating->rating; ?>" />
                                                        <meta itemprop="bestRating" content="5" />
                                                        <span class="wpbdp-ratings-stars" data-readonly="readonly" data-value="<?php echo $rating->rating; ?>" itemprop="ratingValue"></span>                
                                                    </span> 
                                                    <div class="rating-comment" itemprop="description">
                                                        <?php echo esc_attr($rating->comment); ?>

                                                    </div>
                                                    <div class="rating-date">
                                                        <span class="date" itemprop="datePublished" content="<?php echo $rating->created_on; ?>">
                                                                <?php echo date_i18n(get_option('date_format'), strtotime($rating->created_on)); ?>
                                                        </span>
                                                    </div>
                                                    <!--Actions to edit/delete review as author-->

                                                    <?php if ( ($rating->user_id > 0 && $rating->user_id == get_current_user_id() )): ?>
                                                        <div class="edit-actions">
                                                            <a href="" class="edit linkform" rel="review-edit"><i class="icon-pencil"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <a href="" data-toggle="modal" data-target="#confirm-review-delete" data-id="<?php echo $rating->id?>">
                                                                    <i class="icon-trash"></i>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <!--Review Edit form (as author)-->   
                                                <?php if (($rating->user_id > 0 && $rating->user_id == get_current_user_id() ) || current_user_can('administrator')): ?>
                                                <div class="flip-form review-edit"  style='width:100%'>
                                                    <div class="alert alert-danger hidden" id="validation-errors">
                                                        <ul>
                                                            <li class="validation-error-rating hidden">Please enter a rating for this store</li>
                                                            <li class="validation-error-comment hidden">Please write a review of at least 50 characters</li>
                                                            <li class="validation-error-user hidden">Please log in to rate this store</li>
                                                        </ul>
                                                    </div> 
                                                    
                                                    <div class="field">
                                                        <label><?php _e('Rating:', 'wpbdp-ratings'); ?></label>
                                                        <?php 
                                                            if(isset($rating->rating)){
                                                                $data_value = $rating->rating;
                                                            }else{
                                                                $data_value = 0;
                                                            }

                                                        ?>
                                                        <span class="stars wpbdp-ratings-stars" data-value="<?php echo $data_value; ?>"></span>

                                                    </div>
                                                    <textarea><?php echo esc_textarea($rating->comment); ?></textarea>
                                                    <a href="" class="btn btn-link linkform cancel-edit" rel="review-details"><?php _e('Cancel', 'wpbdp-ratings'); ?></a>
                                                    <a href="" class="submit btn btn-primary btn-md linkform save-edit" rel="review-details"><?php _e('Save', 'wpbdp-ratings'); ?></a>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
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