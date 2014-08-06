<?php

add_action('wp_ajax_get_blox_element_carousel', 'get_blox_element_carousel_hook');
add_action('wp_ajax_nopriv_get_blox_element_carousel', 'get_blox_element_carousel_hook');

function get_blox_element_carousel_hook() {
    try {

        echo '<p>
                <label>Item Title</label>
                <input type="text" id="blox_el_option_title" value="' . (isset($_POST['title']) ? $_POST['title'] : '') . '" />
              </p>';

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

        echo '<p>';
        echo '<label>Item Style:</label>';
        echo '<select id="blox_carousel_item_style" class="select_data_val" data_val="' . (isset($_POST['item_style']) && $_POST['item_style'] != '' ? $_POST['item_style'] : 'default') . '">
                    <option value="default">Default</option>
                    <option value="with_excerpt">With Excerpt</option>
                    <option value="alternative">Alternative Style</option>
                </select>';
        echo '</p>';

        echo '<p>';
        echo '<label>Item Overlay:</label>';
        echo '<select id="blox_carousel_overlay" class="select_data_val" data_val="' . (isset($_POST['overlay']) && $_POST['overlay'] != '' ? $_POST['overlay'] : 'none') . '">
                    <option value="none">None (image with link)</option>
                    <option value="permalink">Permalink</option>
                    <option value="lightbox">Lightbox</option>
                    <option value="both">Permalink & Lightbox</option>
                </select>';
        echo '</p>';


    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}

function blox_parse_carousel_hook($atts, $content = null) {
    extract(shortcode_atts(array(
                'title' => '',
                'post_type' => 'post',
                'category' => '0',
                'count' => '6',
                'item_style' => 'default',
                'overlay' => 'none',
                'animation' => '',
                'extra_class' => '',
                'skin' => '',
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

    $title = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';


    $the_query = new WP_Query($args);
    while ($the_query->have_posts()):
        $the_query->the_post();
        
        if( $post_type == 'product' ){
            $product = get_product(get_the_Id());

            ob_start();
            woocommerce_get_template_part( 'content', 'product' );
            $html .= ob_get_clean();

        }
        else{
            $thumb = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
            $image = $thumb != '' ? blox_aq_resize($thumb, 400, 300, true) : '';
            $i++;

            $folio_arg = array(
                    'item_style' => $item_style,
                    'overlay' => $overlay,
                    'thumb' => $image,
                    'class' => 'col-md-3 col-sm-6 col-xs-12 swiper-slide',
                    'width' => '400',
                    'height' => '300'
                );
            $html .= blox_loop_portfolio( $folio_arg );

        }
    endwhile;

    $the_query = $temp_query;
    $post = $temp_post;
    
    if( $post_type == 'product' ){
        return "<div class='blox-element blox-carousel woocommerce swiper-container'>
                    $title
                    <ul class='blox-element products swiper-wrapper'>
                        " . $html . "
                    </ul>
                    <div class='carousel-control-prev'><i class='fa-angle-left'></i></div>
                    <div class='carousel-control-next'><i class='fa-angle-right'></i></div>
                </div>";
    }

    $skin = $item_style!='alternative' ? $skin : '';
    return "<div class='blox-element blox-carousel swiper-container $extra_class'>
                $title
                <div class='blox-element grid-loop portfolio swiper-wrapper $skin'>
                    " . $html . "
                </div>
                <div class='carousel-control-prev'><i class='fa-angle-left'></i></div>
                <div class='carousel-control-next'><i class='fa-angle-right'></i></div>
            </div>";
}

add_shortcode('blox_carousel', 'blox_parse_carousel_hook');
?>