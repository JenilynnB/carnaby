<?php



add_action('wp_ajax_get_blox_element_blog', 'get_blox_element_blog_hook');
add_action('wp_ajax_nopriv_get_blox_element_blog', 'get_blox_element_blog_hook');
function get_blox_element_blog_hook() {
    try {
        $filter = $_POST['filter'];
        $value = $_POST['value'];
        $value_array = explode(',', $value);
        $html = '';

        // get categories
        $categories = get_categories();
        foreach ($categories as $cat) {
            $selected = '';
            if( $filter=='categories' && in_array($cat->cat_ID, $value_array) ){
                $selected = 'selected';
            }
            $html .= '<option value="'.$cat->cat_ID.'" '.$selected.'>'.$cat->name.'</option>';
        }
        $html = "<select id='blox_new_cats' multiple>$html</select>";



        // get tags
        $html .= "<select id='blox_new_tags' multiple>";
        $tags = get_tags();
        foreach ($tags as $tag) {
            $selected = '';
            if( $filter=='tags' && in_array($tag->slug, $value_array) ){
                $selected = 'selected';
            }
            $html .= '<option value="'.$tag->slug.'" '.$selected.'>'.$tag->name.'</option>';
        }
        $html .= "</select>";



        // get post formats
        $html .= "<select id='blox_new_formats' multiple>";
        $post_formats = get_theme_support( 'post-formats' );
        foreach ($post_formats[0] as $format) {
            $selected = '';
            if( $filter=='format' && in_array($format, $value_array) ){
                $selected = 'selected';
            }
            $html .= '<option value="'.$format.'" '.$selected.'>'.$format.'</option>';
        }
        $html .= "</select>";



        echo "<div>$html</div>";
    }
    catch (Exception $e) {
        echo "-1";
    }
    exit;
}



function blox_parse_blog_hook($atts) {
    extract(shortcode_atts(array(
                'title' => '',
                'blog_type' => 'default',
                'categories' => 'all',
                'blog_filter' => '',
                'count' => '5',
                'pager' => '1',
                'content' => 'both',
                'readmore' => __('Read more', 'themeton'),
                'ignoresticky' => 'yes',
                'overlay' => 'none',
                'exclude' => '',
                'order' => '',
                'skip' => '0',
                'extra_class' => '',
                'skin' => 'default',
                'visibility' => ''
                    ), $atts));

    $element_style = $skin;
    $visibility = str_replace(',', ' ', $visibility);
    $extra_class .= ' '.$visibility;

    global $query, $post, $paged;
    $temp_qry = $query;
    $temp_post = $post;
	
    wp_reset_query();
    wp_reset_postdata();

    /*
    Build queries
    ===============================================================*/
    if(is_front_page()){
        $paged = get_query_var('page') ? get_query_var('page') : 1;
    }

    $args = array(
        'paged' => $paged,
        'posts_per_page' => (int) $count + (int)$skip,
        'ignore_sticky_posts' => $ignoresticky == 'yes' ? 1 : 0
    );

    if( $categories=='categories' ){
        $args['cat'] = $blog_filter;
    }
    else if( $categories=='tags' ){
        $args['tag'] = $blog_filter;
    }
    else if( $categories=='format' ){
        $format_array = explode(',', $blog_filter);
        $array = array();
        foreach ($format_array as $value) {
            $array[] = 'post-format-'.$value;
        }
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'post_format',
                'field' => 'slug',
                'terms' => $array
            )
        );
    }

    // Exclude posts 
    if ($exclude != '') {
        $args['post__not_in'] = array($exclude);
    }
    if ($order == 'dateasc') {
        $args['orderby'] = 'date';
        $args['order'] = 'ASC';
    } elseif ($order == 'titleasc') {
        $args['orderby'] = 'title';
        $args['order'] = 'ASC';
    } elseif ($order == 'titledes') {
        $args['orderby'] = 'title';
        $args['order'] = 'DESC';
    } elseif ($order == 'comment') {
        $args['orderby'] = 'comment_count';
    } elseif ($order == 'postid') {
        $args['orderby'] = 'ID';
    } elseif ($order == 'random') {
        $args['orderby'] = 'rand';
        add_action('posts_orderby', 'edit_posts_orderby');
    }


    /*
    Build queries
    ===============================================================*/
    $title = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';
    $element_style = $element_style!='default' ? $element_style : '';
    
    $column_str = $blog_type;
    $column_str = str_replace('grid', '', $column_str);
    $column_str = str_replace('masonry', '', $column_str);
    $column = (int)$column_str;

    $result = '';
    $open_grid_pager = $close_grid_pager = '';

    $query = new WP_Query($args);
    if ($query->have_posts()) {

        $cats_filter = array();
        $item_number = 1;

        while ($query->have_posts()) {
            $query->the_post();

            $loop_args = array(
                            'overlay' => $overlay,
                            'excerpt' => $content,
                            'readmore' => $readmore,
                            'grid' => $column,
                            'element_style' => $element_style
                            );

            ob_start();
            $post_format = get_post_format();
            if( function_exists('blox_loop_' . $blog_type ) ){
                call_user_func('blox_loop_' . $blog_type, $loop_args );
            }
            else{
                call_user_func('blox_loop_regular', $loop_args );
            }
            $result .= ob_get_contents();
            
            ob_end_clean();
        }


        $pager_html = '';
        if ($pager == '1') {
            ob_start();
            themeton_pager($query);
            $pager_html .= ob_get_contents();
            ob_end_clean();
        }

        
        // Grid container
        if (strpos($blog_type, 'grid') !== false) {
            $result = '<div class="blox-element blog grid-loop '.$element_style.' '.$extra_class.'">
                            <div class="row">
                                <div class="loop-container">'.$result.'</div>
                            </div>
                            '. $pager_html .'
                        </div>';
        } else if (strpos($blog_type, 'masonry') !== false) {
            $result = '<div class="blox-element blog grid-loop '.$element_style.' '.$extra_class.'">
                            <div class="row">
                                <div class="loop-masonry">'.$result.'</div>
                            </div>
                            '. $pager_html .'
                        </div>';
        }
        else{
            $result ='<div class="blox-element blog medium-loop '.$element_style.' '.$extra_class.'">
                        <div class="row">
                            <div class="col-md-12">'.$result.'</div>
                        </div>
                        '. $pager_html .'
                      </div>';
        }
    }


    wp_reset_query();
    wp_reset_postdata();

    $query = $temp_qry;
    $post = $temp_post;


    return $result;
}

add_shortcode('blox_blog', 'blox_parse_blog_hook');
?>