<section class="primary-section reviews">
    <div class="container">   
    <a name="ratings"></a>
        <?php global $xoouserultra;   ?>
        <div class="row">
            <div class="col-md-8">


                <div class="wpbdp-ratings-reviews">
                    <h4><?php _e('Reviews', 'wpbdp-ratings'); ?></h4>

                    <div class="col-md-12">
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
                        
                        <div class="row">
                            <div class="listing-ratings">
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
                                            
                                            <?php /*if ( $review_to_edit->user_id > 0 && $review_to_edit->user_id == get_current_user_id() ):*/ ?> 
                                            
                                            <?php if ( ($rating->user_id > 0 && $rating->user_id == get_current_user_id() )): ?>
                                                <?php echo $review_form; ?>
                                            <?php endif; ?>
                                            
                                            <?php if ( current_user_can('administrator')): ?>
                                                <div class="edit-actions">
                                                    <a href="#" class="edit" ><i class="icon-pencil"></i> Admin edit</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="delete"><i class="icon-trash"></i></a>
                                                </div>
                                            <?php endif; ?>
                                            
                                        </div>
                                    </div>




                                    <!--Review Edit form (as author)-->   
                                    <?php if (($rating->user_id > 0 && $rating->user_id == get_current_user_id() ) || current_user_can('administrator')): ?>
                                    <div class="rating-comment-edit" style="display: none;">
                                         
                                        <textarea><?php echo esc_textarea($rating->comment); ?></textarea>
                                        <input type="button" value="<?php _e('Cancel', 'wpbdp-ratings'); ?>" class="button cancel-button" />
                                        <input type="button" value="<?php _e('Save', 'wpbdp-ratings'); ?>" class="submit save-button" />
                                    </div>
                                    <?php endif; ?>


                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php endif; ?>

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
    