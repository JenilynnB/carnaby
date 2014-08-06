<?php

add_action('admin_enqueue_scripts', 'admin_post_option_render_scripts');

function admin_post_option_render_scripts($hook) {
    if (themeton_admin_post_type() != 'post') {
        return;
    }
    wp_enqueue_style('tt_admin_post_format_style', get_template_directory_uri() . '/framework/post-type/post-styles.css');
    wp_enqueue_script('tt_admin_post_format_script', get_template_directory_uri() . '/framework/post-type/post-scripts.js', false, false, true);
}

add_theme_support('post-formats', array('image', 'gallery', 'link', 'video', 'audio', 'chat', 'status', 'quote', 'aside'));


function get_post_type_options_post(){
    $tmp_arr = array();
    if (!function_exists('the_post_format_audio')) {
        $tmp_arr_format = array(
            'post_format' => array(
                'label' => 'Post Format',
                'post_type' => 'post',
                'items' => array(
                    array(
                        'name' => 'post_format',
                        'realname' => '1',
                        'type' => 'text',
                        'label' => 'Post Format',
                        'default' => ''
                    ),
                    array(
                        'name' => 'format_image',
                        'realname' => '1',
                        'type' => 'textarea',
                        'label' => 'Post Format Image',
                        'default' => ''
                    ),
                    array(
                        'name' => 'format_video_embed',
                        'realname' => '1',
                        'type' => 'textarea',
                        'label' => 'Post Format Video',
                        'default' => ''
                    ),
                    array(
                        'name' => 'format_audio_embed',
                        'realname' => '1',
                        'type' => 'textarea',
                        'label' => 'Post Format Audio',
                        'default' => ''
                    ),
                    array(
                        'name' => 'format_link_url',
                        'realname' => '1',
                        'type' => 'text',
                        'label' => 'Post Format Link',
                        'default' => ''
                    ),
                    array(
                        'name' => 'format_quote_text',
                        'realname' => '1',
                        'type' => 'text',
                        'label' => 'Blockquote',
                        'default' => ''
                    ),
                    array(
                        'name' => 'format_quote_source_name',
                        'realname' => '1',
                        'type' => 'text',
                        'label' => 'Post Format Quote Source',
                        'default' => ''
                    ),
                    array(
                        'name' => 'format_quote_source_url',
                        'realname' => '1',
                        'type' => 'text',
                        'label' => 'Post Format Quote url',
                        'default' => ''
                    ),
                    array(
                        'name' => 'format_gallery_images',
                        'realname' => '1',
                        'type' => 'text',
                        'label' => 'Post Gallery',
                        'default' => ''
                    )
                )
            )
        );
        $tmp_arr = array_merge($tmp_arr, $tmp_arr_format);
    }

    return $tmp_arr;
}



/*
$tmp_arr = array(
    'post' => array(
        'label' => 'Post Options',
        'post_type' => 'post',
        'items' => array(
            array(
                'name' => 'hide_featured_img',
                'type' => 'checkbox',
                'label' => 'Hide Featured Image on Single?',
                'desc' => 'You should turn this option ON if you dont want to show Featured image on single post page.'
            )
        )
    )
);

$tt_post_meta = array_merge($tt_post_meta, $tmp_arr);
*/









function post_images_box() {
    global $post;
    ?>
    <div id="post_images_container">
        <ul class="post_images">
            <?php
                if ( metadata_exists( 'post', $post->ID, '_format_gallery_images' ) ) {
                    $product_image_gallery = get_post_meta( $post->ID, '_format_gallery_images', true );
                } else {
                    // Backwards compat
                    $attachment_ids = get_posts( 'post_parent=' . $post->ID . '&posts_per_page=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=_woocommerce_exclude_image&meta_value=0' );
                    $attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
                    $product_image_gallery = implode( ',', $attachment_ids );
                }

                $attachments = array_filter( explode( ',', $product_image_gallery ) );

                if ( $attachments )
                    foreach ( $attachments as $attachment_id ) {
                        echo '<li class="image" data-attachment_id="' . $attachment_id . '">
                            ' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
                            <ul class="actions">
                                <li><a href="#" class="delete" title="' . __( 'Delete image', 'themeton' ) . '">' . __( 'Delete', 'themeton' ) . '</a></li>
                            </ul>
                        </li>';
                    }
            ?>
        </ul>

    </div>
    <p class="add_post_images hide-if-no-js">
        <a href="#"><?php _e( 'Add post gallery images', 'themeton' ); ?></a>
    </p>
    <?php
}

function post_add_meta_boxes(){
    global $post;
    add_meta_box( 'post-images', __( 'Post Gallery', 'themeton' ), 'post_images_box', 'post', 'side' );
}
add_action( 'add_meta_boxes', 'post_add_meta_boxes' );


?>