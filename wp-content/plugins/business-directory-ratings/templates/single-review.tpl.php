<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$author_first_name = get_the_author_meta('first_name', $rating->user_id);
$author_last_name = get_the_author_meta('last_name', $rating->user_id);
if($author_last_name!=''){
    $author_last_initial = substr($author_last_name,0,1);
    $author_display_name = $author_first_name." ".$author_last_initial.".";
}else{
    $author_display_name = $author_first_name;
}
$user_profile_url = site_url('/profile/?uu_username='.$rating->user_id);

$listing_name = get_the_title($rating->listing_id);
$listing_url = get_permalink($rating->listing_id);

?>


<div class="rating wpbdp-ratings-reviews" data-id="<?php echo $rating->id; ?>" data-listing-id="<?php echo get_the_ID(); ?>"
     itemprop="review" itemscope itemtype="http://schema.org/Review">


    <div class="row">
        <div class="col-md-2">


            <div class="rating-authoring-info">
                <?php echo get_user_profile_thumb_circle(100, $rating->user_id);?>
                <!--
                <div class="author" itemprop="author">
                    <?php if ($rating->user_id == 0): ?>
                        <?php echo esc_attr($rating->user_name); ?>
                    <?php else: ?>
                    <?php endif; ?>
                </div>
                -->
            </div>  
        </div>

        <div class="col-md-10">
            <div id='form_wrapper_edit' class='form-wrapper'>
                <div class='flip-form review-details active' style='width:100%'>
                    <div class="author">
                        <a href="<?php echo $user_profile_url; ?>"><?php echo $author_display_name;?></a>
                        <div class="author-review-store-info"> wrote a review for 
                            <a href="<?php echo $listing_url; ?>"><?php echo $listing_name;?></a>
                        </div>
                    </div>
                    <div class="review-rating-date">
                        <span class="date" itemprop="datePublished" content="<?php echo $rating->created_on; ?>">
                                <?php echo date_i18n(get_option('date_format'), strtotime($rating->created_on)); ?>
                        </span>
                    </div>
                    <div class="review-rating">
                        <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                            <meta itemprop="worstRating" content="0" />
                            <meta itemprop="ratingValue" content="<?php echo $rating->rating; ?>" />
                            <meta itemprop="bestRating" content="5" />
                            <span class="wpbdp-ratings-stars" data-readonly="readonly" data-value="<?php echo $rating->rating; ?>" itemprop="ratingValue"></span>                
                        </span>
                    </div>
                    <div class="rating-comment" itemprop="description">
                        <?php echo esc_attr($rating->comment); ?>

                    </div>
                    


                    <?php if ( ($rating->user_id > 0 && $rating->user_id == get_current_user_id() )): ?>
                        <div class="edit-actions">
                            <a href="" class="edit linkform" rel="review-edit"><i class="icon-pencil"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="" data-toggle="modal" data-target="#confirm-review-delete" data-id="<?php echo $rating->id?>">
                                    <i class="icon-trash"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>


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