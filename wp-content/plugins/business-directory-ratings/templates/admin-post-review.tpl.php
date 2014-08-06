<div id="wpbdp-ratings-admin-post-review">

    <p><a href="#" class="add-review-link"><?php _e( 'Add Review', 'wpbdp-ratings' ); ?></a></p>

    <div class="form" style="display: none;">
        <input type="hidden" name="wpbdp_ratings_rating[listing_id]" value="<?php echo $listing_id; ?>" />

        <div class="field">
            <label><?php _e('Author:', 'wpbdp-ratings'); ?></label>
            <input type="text" name="wpbdp_ratings_rating[user_name]" size="30" value="<?php echo esc_attr(wpbdp_getv($_POST, 'user_name', '')); ?>" />
            <span class="description"><?php _e( 'WordPress username or arbitrary username.', 'wpbdp-ratings' ); ?></span>
        </div>

        <div class="field">
            <label><?php _e('Rating:', 'wpbdp-ratings'); ?></label>
            <span class="stars wpbdp-ratings-stars" data-value="" data-field="wpbdp_ratings_rating[rating]"></span>
        </div>

        <?php if ( wpbdp_get_option( 'ratings-comments' ) != 'disabled' ): ?>
        <div class="field">
            <textarea name="wpbdp_ratings_rating[comment]" cols="50" rows="3"></textarea>
        </div>
        <?php endif; ?>

        <p>
            <a href="" class="button-primary alignright"><?php _e('Add Review', 'wpbpd-ratings'); ?></a>
            <a href="" class="button-secondary alignleft"><?php _e('Cancel', 'wpbpd-ratings'); ?></a>
        </p>

        <br />
    </div>

</div>