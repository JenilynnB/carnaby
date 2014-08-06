<a name="wpbdp-ratings"></a>

<table class="widefat fixed" cellspacing="0">
<tbody>
    <tr class="no-items" style="<?php echo $reviews ? 'display: none;' : ''; ?>">
        <td colspan="3"><?php _e('This listing has not been rated yet.', 'wpbdp-ratings'); ?></td>
    </tr>
    <?php foreach ($reviews as $i => $review): ?>
        <?php echo wpbdp_render_page( plugin_dir_path( __FILE__ ) . 'admin-rating-row.tpl.php',
                                      array( 'review' => $review ) ); ?>
    <?php endforeach; ?>
</tbody>
</table>