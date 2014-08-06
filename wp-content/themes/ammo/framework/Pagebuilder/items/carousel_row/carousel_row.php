<?php

add_action('wp_ajax_get_blox_element_carouselrow', 'get_blox_element_carouselrow_hook');
add_action('wp_ajax_nopriv_get_blox_element_carouselrow', 'get_blox_element_carouselrow_hook');

function get_blox_element_carouselrow_hook() {
    try {

        echo '<label>Post type:</label>';
        echo '<select id="blox_option_post_type" class="select_data_val" data_val="' . (isset($_POST['post_type']) && $_POST['post_type'] != '' ? $_POST['post_type'] : 'post') . '" data_cat="' . (isset($_POST['category']) && $_POST['category'] != '' ? $_POST['category'] : '0') . '">';
        $post_arr = array();
        $post_arr['post'] = get_post_type_object('post');
        $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects', 'and');
        $post_types = array_merge($post_arr, $post_types);
        foreach ($post_types as $type) {
            echo '<option value="' . $type->name . '">' . $type->labels->name . '</option>';
        }
        echo '</select>';


        echo '<label>Category:</label>';
        foreach ($post_types as $type) {
            $ptype = $type->name;
            echo '<select id="blox_option_taxonomy_' . $ptype . '" class="blox_option_taxonomies">';
            echo '<option value="0">All</option>';
            $taxonomies = get_object_taxonomies($ptype);
            if (count($taxonomies) > 0) {
                $terms = get_terms($taxonomies[0]);
                foreach ($terms as $term) {
                    echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                }
            }
            echo '</select>';
        }

        echo '<p>';
        echo '<label>Posts count:</label>';
        echo '<input type="number" step="1" min="1" id="blox_option_posts_count" value="' . (isset($_POST['count']) && $_POST['count'] != '' ? $_POST['count'] : '6') . '" class="small-text">';
        echo '</p>';

        echo '<p class="">
                <label>Portfolio Image Ratio</label>
                <select id="blox_element_ratio" data="' . (isset($_POST['ratio']) ? $_POST['ratio'] : '1x1') . '" class="blox_elem_select">
                    <option value="1x1">1:1 - Image size</option>
                    <option value="4x3">4:3 - Image size</option>
                    <option value="16x9">16:9 - Image size</option>
                </select>
                <span class="clearfix"></span>
            </p>';

        echo '<p>
                <label>Extra Class</label>
                <input type="text" id="blox_option_extra_class" value="' . (isset($_POST['extra_class']) ? $_POST['extra_class'] : '') . '" />
              </p>';


    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}

function blox_parse_carouselrow_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'post_type' => 'post',
                'category' => '0',
                'count' => '6',
                'ratio' => '1x1',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));

    $visibility = str_replace(',', ' ', $visibility);
    $extra_class = $extra_class.' '.$visibility;

    global $post, $product, $woocommerce, $woocommerce_loop;
    global $the_query;
    $temp_post = $post;
    $temp_query = $the_query;

    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => (int) $count,
        'ignore_sticky_posts' => 1
    );

    if ($category != '' && $category != '0') {
        $taxonomies = get_object_taxonomies($post_type);
        $args['tax_query'] = array(array('taxonomy' => $taxonomies[0], 'terms' => $category));
    }
    $i = 0;
    $html = '';


    $the_query = new WP_Query($args);
    while ($the_query->have_posts()):
        $the_query->the_post();
        
        $thumb = wp_get_attachment_url(get_post_thumbnail_id($post->ID));

        //$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        //$thumb = $image[0];

        $iwidth = 600;
        $iheight = 450;
        if( $ratio == '1x1' ){
            $iwidth = 600;
            $iheight = 600;
        }
        else if( $ratio == '16x9' ){
            $iwidth = 600;
            $iheight = 337;
        }

        $thumb = $thumb!='' ? blox_aq_resize($thumb, $iwidth, $iheight, true) : '';

        $folio_arg = array(
                'item_style' => 'alternative',
                'overlay' => '',
                'thumb' => $thumb,
                'class' => 'swiper-slide'
            );
        $html .= blox_loop_portfolio( $folio_arg );
    endwhile;

    $the_query = $temp_query;
    $post = $temp_post;

    return "<div class='fullwidth-carousel swiper-container $extra_class' style='opacity:0;'>
                <div class='blox-element grid-loop portfolio swiper-wrapper'>
                    " . $html . "
                </div>
            </div>";
}

add_shortcode('carousel_fullwidth', 'blox_parse_carouselrow_hook');
?>