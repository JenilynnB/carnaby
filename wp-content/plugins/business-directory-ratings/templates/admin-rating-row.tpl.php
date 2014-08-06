<?php if ( !isset( $i ) ) $i = 0; ?>
    <tr class="<?php echo $i % 2 == 0 ? 'even' : 'odd'; ?>" data-id="<?php echo $review->id; ?>">
        <td class="authoring-info">
            <b>
                <?php if ($review->user_id): ?>
                    <?php echo the_author_meta('display_name', $review->user_id); ?>
                <?php else: ?>
                    <?php echo esc_attr($review->user_name); ?>
                <?php endif; ?>
            </b><br />
            <?php echo $review->ip_address; ?>
        </td>
        <td class="score">
            <span class="wpbdp-ratings-stars" data-readonly="readonly" data-value="<?php echo $review->rating; ?>"></span>
        </td>
        <td class="comment">
            <div class="submitted-on"><?php echo date_i18n(get_option('date_format') . ' @ ' . get_option('time_format'), strtotime($review->created_on)); ?></div>
            
            <div class="comment">
                <?php echo esc_attr($review->comment); ?>
            </div>
            <div class="comment-edit" style="display: none;">
                <textarea><?php echo esc_textarea($review->comment); ?></textarea>
                <input type="button" value="<?php _e('Cancel', 'wpbdp-ratings'); ?>" class="cancel-button" />
                <input type="button" value="<?php _e('Save', 'wpbdp-ratings'); ?>" class="save-button" />
            </div>            

            <div class="row-actions">
                <span><a href="#" class="edit"><?php _e('Edit', 'wpbdp-ratings'); ?></a> | </span>
                <span class="trash"><a href="#" class="delete"><?php _e('Delete', 'wpbdp-ratings'); ?></a></span>
            </div>

        </td>
    </tr>