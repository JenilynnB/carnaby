<?php


add_action('wp_ajax_get_blox_element_portfolio', 'blox_element_portfolio_hook');
add_action('wp_ajax_nopriv_blox_element_portfolio', 'blox_element_portfolio_hook');
function blox_element_portfolio_hook() {
    try {
        $value = isset($_POST['value']) ? $_POST['value'] : '';
        $value_array = explode(',', $value);
        $html = '';

        $taxonomies = get_object_taxonomies('portfolio');
        if (count($taxonomies) > 0) {
            $terms = get_terms($taxonomies[0]);
            foreach ($terms as $term) {
                $selected = '';
                if( in_array($term->term_id, $value_array) ){
                    $selected = 'selected';
                }
                $html .= '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
            }
        }
        $html = "<select id='blox_new_cats' multiple>$html</select>";

        echo "<div>$html</div>";
    }
    catch (Exception $e) {
        echo "-1";
    }
    exit;
}



function blox_portfolio_parse_hook($atts) {
    extract(shortcode_atts(array(
                'title' => '',
                'style' => 'default',
                'categories' => '',
                'count' => '5',
                'pager' => '0',
                'height' => '',
                'readmore' => __('Read more', 'themeton'),
                'ignoresticky' => 'yes',
                'filter' => '1',
                'content_type' => 'default',
                'overlay' => 'none',
                'exclude' => '',
                'order' => '',
                'extra_class' => '',
                'element_style' => 'default',
                'skin' => '',
                'visibility' => ''
                    ), $atts));

    $visibility = str_replace(',', ' ', $visibility);

    global $query, $post, $paged;
    $temp_qry = $query;
    $temp_post = $post;

    wp_reset_query();
    wp_reset_postdata();

	if(is_front_page()){
        $paged = get_query_var('page') ? get_query_var('page') : 1;
    }


    /* Build queries
    ================================================*/
    $args = array(
        'post_type' => 'portfolio',
        'post__not_in' => array($exclude),
        'posts_per_page' => (int) $count,
        'paged' => $paged,
        'ignore_sticky_posts' => $ignoresticky == 'yes' ? 1 : 0
    );

	$cats_filter = array();
    if( $categories!='' ){
        $format_array = explode(',', $categories);
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'portfolio_entries',
                'field' => 'id',
                'terms' => $format_array
            )
        );
        foreach($format_array as $term) {
	        $the_term = get_term( $term, 'portfolio_entries' );
	        
            $temp_cat = array(
                'id' => $the_term->term_id,
                'title' => $the_term->name,
                'slug' => $the_term->slug
            );
            if (!in_array($temp_cat, $cats_filter)) {
                $cats_filter[] = $temp_cat;
            }
    	}
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


    $result = '';
    $title = $title != '' ? '<h3 class="element-title">' . $title . '</h3>' : '';

    $query = new WP_Query($args);
    if ($query->have_posts()) {

        $item_number = 0;

        while ($query->have_posts()) {
            $query->the_post();

            // If it is grid style, there don't need image scaled cropping
            $image_height = 0;
            if(strpos($style, 'grid') !== false) {
                $image_height = $height != '' ? (int) $height : 0;
            }
            
            $item_number++;
            $class = 'col-md-3 col-sm-6 col-xs-12';
            $image_width = 539;
            if ($style == 'masonry3' || $style == 'grid3') {
                $class = 'col-md-4 col-sm-6 col-xs-12';
                $image_width = 344;
            } else if ($style == 'masonry4' || $style == 'grid4') {
                $class = 'col-md-3 col-sm-6 col-xs-12';
                $image_width = 247;
            } else if ($style == 'masonry2' || $style == 'grid2') {
                $class = 'col-md-6 col-sm-6 col-xs-12';
                $image_width = 0;
            }
            
            
            // If it is centered style, there don't need image overlay
            if($style == 'centered') {
                $overlay = 'nothing';
            }

            $current_filter_classes = '';
            
            $terms = wp_get_post_terms( get_the_ID(), 'portfolio_entries' );
            foreach($terms as $term){
                $current_filter_classes .= 'filter-'.$term->slug.' ';
				if(empty($format_array)) {
	                $temp_cat = array(
	                    'id' => $term->term_id,
	                    'title' => $term->name,
	                    'slug' => $term->slug
	                );
	                if (!in_array($temp_cat, $cats_filter)) {
	                    $cats_filter[] = $temp_cat;
	                }
	            }
        	}

            $thumb = '';
            if( has_post_thumbnail(get_the_ID()) ){
                $fimage = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) );
                $thumb = $fimage!==false ? $fimage : THEME_NOIMAGE;
            }
            else{
                $gallery_field = tt_getmeta('portfolio_gallery');
                $gimages = explode(',', $gallery_field);
                if( isset($gimages[0]) && $gimages[0]!='' ){
                    $thumb = wp_get_attachment_url($gimages[0]);
                    $thumb = $thumb!==false ? $thumb : THEME_NOIMAGE;
                }
            }

            $cols = (int)(str_replace('grid', '', str_replace('masonry', '', $style) ));
            $img_w = 520;
            $img_w = $cols==3 ? 337 : $img_w;
            $img_w = $cols==4 ? 245 : $img_w;
            $height = $height!='' ? $height : '0';
            $thumb = $thumb!='' ? blox_aq_resize($thumb, $img_w, $height, true) : '';

            $folio_arg = array(
                    'item_style' => $content_type,
                    'overlay' => $overlay,
                    'thumb' => $thumb,
                    'class' => $class .' '. $current_filter_classes,
                    'width' => $img_w,
                    'height' => $height
                );
            $result .= blox_loop_portfolio( $folio_arg );
            
        }


        $pager_html = '';
        if ($pager == '1') {
            ob_start();
            themeton_pager($query);
            $pager_html .= ob_get_contents();
            ob_end_clean();
        }


        $filter_html = '';
        if ($filter == '1') {
            $cat_filter_html = '';
            foreach ($cats_filter as $cat) {
                $cat_filter_html .= '<li><a href="javascript:;" title="' . $cat['title'] . '" data-filter="filter-' . $cat['slug'] . '">' . $cat['title'] . '</a></li>';
            }

            if ($cat_filter_html != '') {
                $filter_html = '<div class="row">
                                    <div class="col-md-12">
                                        <div class="portfolio-filter">
                                            <div class="pull-left">
                                                <h3>'. __('All', 'themeton') .'</h3>
                                            </div>
                                            <div class="pull-right">
                                                <ul class="nav nav-pills">
                                                    <li class="dropdown active">
                                                        <a class="dropdown-toggle navInst" data-toggle="dropdown" href="#">
                                                            '. __('Sort Portfolio', 'themeton') .' <span class="caret"></span>
                                                        </a>
                                                        <ul class="dropdown-menu" role="menu">
                                                            <li><a href="javascript:;" title="All" data-filter="all" class="active">'. __('All', 'themeton') .'</a></li>
                                                            <li class="divider"></li>
                                                            '. $cat_filter_html .'
                                                        </ul>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>';
            }
        }

        // Grid container
        $masonry_class = '';
        if(strpos($style, 'masonry') !== false){ $masonry_class = 'portfolio-masonry'; }
        $result = '<div class="blox-element grid-loop portfolio '. $element_style .' '. $masonry_class .' '.$extra_class.' '.$visibility.'">
                        '. $filter_html .'
                        <div class="row masonry-container">'. $result .'</div>
                        '. $pager_html .'
                    </div>';


    }


    wp_reset_query();
    wp_reset_postdata();

    $query = $temp_qry;
    $post = $temp_post;
    
    return $result;
}

add_shortcode('blox_portfolio', 'blox_portfolio_parse_hook');

?>