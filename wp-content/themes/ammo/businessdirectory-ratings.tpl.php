<a name="ratings"></a>

<div class="wpbdp-ratings-reviews">
    <h3><?php _e('Reviews', 'wpbdp-ratings'); ?></h3>

    <p class="no-reviews-message" style="<?php echo $ratings ? 'display: none;' : ''?>"><?php _e('There are no reviews yet.', 'wpbdp-ratings'); ?></p>
    <?php if ($ratings): ?>
    <div class="listing-ratings">
        <?php foreach ($ratings as $i => $rating): ?>
        <div class="rating <?php echo $i % 2 == 0 ? 'odd' : 'even'; ?>" data-id="<?php echo $rating->id; ?>" data-listing-id="<?php echo get_the_ID(); ?>"
             itemprop="review" itemscope itemtype="http://schema.org/Review">
            <div class="edit-actions">
                <?php if ( ($rating->user_id > 0 && $rating->user_id == get_current_user_id() ) || current_user_can('administrator')): ?>
                <a href="#" class="edit">Edit</a> <a href="#" class="delete">Delete</a>
                <?php endif; ?>
            </div>

            <span itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
                <meta itemprop="worstRating" content="0" />
                <meta itemprop="ratingValue" content="<?php echo $rating->rating; ?>" />
                <meta itemprop="bestRating" content="5" />
                <span class="wpbdp-ratings-stars" data-readonly="readonly" data-value="<?php echo $rating->rating; ?>" itemprop="ratingValue"></span>                
            </span>

            <div class="rating-comment" itemprop="description">
                <?php echo esc_attr($rating->comment); ?>
            </div>
            <?php if (($rating->user_id > 0 && $rating->user_id == get_current_user_id() ) || current_user_can('administrator')): ?>
            <div class="rating-comment-edit" style="display: none;">
                <textarea><?php echo esc_textarea($rating->comment); ?></textarea>
                <input type="button" value="<?php _e('Cancel', 'wpbdp-ratings'); ?>" class="button cancel-button" />
                <input type="button" value="<?php _e('Save', 'wpbdp-ratings'); ?>" class="submit save-button" />
            </div>
            <?php endif; ?>
        
            <div class="rating-authoring-info">
                <span class="author" itemprop="author">
                    <?php if ($rating->user_id == 0): ?>
                        <?php echo esc_attr($rating->user_name); ?>
                    <?php else: ?>
                        <?php the_author_meta('display_name', $rating->user_id); ?>
                    <?php endif; ?>
                </span>
                |
                <span class="date" itemprop="datePublished" content="<?php echo $rating->created_on; ?>">
                    <?php echo date_i18n(get_option('date_format'), strtotime($rating->created_on)); ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($review_form): ?>
        <?php echo $review_form; ?>
    <?php else: ?>
        <?php if ($success): ?>
        <div class="message">
            <?php if (wpbdp_get_option('ratings-require-approval')): ?>
                <?php _e('Your review has been saved and is waiting for approval.', 'wpbdp-ratings'); ?>
            <?php else: ?>
                <?php _e('Your review has been saved.', 'wpbdp-ratings'); ?>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="message">
            <?php if ($reason == 'already-rated'): ?>
                <?php _e('(You have already rated this listing)', 'wpbdp-ratings'); ?>
            <?php else: ?>
                <?php _e('(Please login to rate this listing)', 'wpbdp-ratings'); ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>    

</div>