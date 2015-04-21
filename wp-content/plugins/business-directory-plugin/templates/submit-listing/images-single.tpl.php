<?php
$is_thumbnail = isset( $is_thumbnail ) ? $is_thumbnail : false;
?>

<div class="wpbdp-image" data-imageid="<?php echo $image_id; ?>">
    <img src="<?php echo wp_get_attachment_thumb_url( $image_id ); ?>" /><br />
    <input type="button"
           class="button delete-image"
           value="<?php _ex('Delete Image', 'templates', 'WPBDM'); ?>"
           data-action="<?php echo esc_url( add_query_arg( array( 'action' => 'wpbdp-listing-submit-image-delete',
                                                                  'state_id' => isset( $state_id ) ? $state_id : '',
                                                                  'image_id' => $image_id ), admin_url( 'admin-ajax.php' ) ) ); ?>" /> <br />
    
    <label>
        <input type="radio" name="thumbnail_id" value="<?php echo $image_id; ?>" <?php echo $is_thumbnail ? 'checked="checked"' : ''; ?> />
        <?php _ex('Set this image as the listing thumbnail.', 'templates', 'WPBDM'); ?>
    </label>
    <br/>
    <?php if($has_women) { ?>
    <label>
        <input type="radio" name="womens_thumb_id" value="<?php echo $image_id; ?>" <?php echo $is_womens_thumb ? 'checked="checked"' : ''; ?> />
        <?php _ex('Women\'s thumbnail.', 'templates', 'WPBDM'); ?>
    </label>
    <br/>
    <?php } ?>
    <?php if($has_men) { ?>
    <label>
        <input type="radio" name="mens_thumb_id" value="<?php echo $image_id; ?>" <?php echo $is_mens_thumb ? 'checked="checked"' : ''; ?> />
        <?php _ex('Men\'s thumbnail.', 'templates', 'WPBDM'); ?>
    </label>
    <br/>
    <?php } ?>
    <?php if($has_kids) { ?>
    <label>
        <input type="radio" name="kids_thumb_id" value="<?php echo $image_id; ?>" <?php echo $is_kids_thumb ? 'checked="checked"' : ''; ?> />
        <?php _ex('Kid\'s thumbnail', 'templates', 'WPBDM'); ?>
    </label>
    <br/>
    <?php } ?>
    <?php if($has_boys) { ?>
    <label>
        <input type="radio" name="boys_thumb_id" value="<?php echo $image_id; ?>" <?php echo $is_boys_thumb ? 'checked="checked"' : ''; ?> />
        <?php _ex('Boy\'s thumbnail.', 'templates', 'WPBDM'); ?>
    </label>
    <br/>
    <?php } ?>
    <?php if($has_girls) { ?>
    <label>
        <input type="radio" name="girls_thumb_id" value="<?php echo $image_id; ?>" <?php echo $is_girls_thumb ? 'checked="checked"' : ''; ?> />
        <?php _ex('Girl\'s thumbnail.', 'templates', 'WPBDM'); ?>
    </label>
    <br/>
    <?php } ?>
    <?php if($has_baby) { ?>
    <label>
        <input type="radio" name="baby_thumb_id" value="<?php echo $image_id; ?>" <?php echo $is_baby_thumb ? 'checked="checked"' : ''; ?> />
        <?php _ex('Baby\'s thumbnail.', 'templates', 'WPBDM'); ?>
    </label>
    <?php } ?>
</div>
