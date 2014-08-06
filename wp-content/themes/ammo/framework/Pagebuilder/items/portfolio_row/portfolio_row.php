<?php


function blox_portfolio_row_parse_hook($atts) {
    extract(shortcode_atts(array(
                'categories' => '',
                'column' => '4',
                'count' => '8',
                'bgcolor' => '#34495e',
                'ratio' => '4x3',
                'filter' => '1',
                'order' => '',
                'exclude' => '',
                'extra_class' => '',
                'visibility' => ''
                    ), $atts));

    $visibility = str_replace(',', ' ', $visibility);

    global $query, $post, $paged;
    $temp_qry = $query;
    $temp_post = $post;

    wp_reset_query();
    wp_reset_postdata();


    /* Build queries
    ================================================*/
    $args = array(
        'post_type' => 'portfolio',
        'post__not_in' => array($exclude),
        'posts_per_page' => (int) $count,
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

    $query = new WP_Query($args);
    if ($query->have_posts()) {

        $item_number = 0;

        while ($query->have_posts()) {
            $query->the_post();
            
            $item_number++;
            $image_width = 539;

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
                    'thumb' => $thumb,
                    'class' => ' '. $current_filter_classes
                );
            $result .= blox_loop_portfolio( $folio_arg );
            
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
                                                    <li><a href="javascript:;" title="All" data-filter="all" class="active">'. __('All', 'themeton') .'</a></li>
                                                   
                                                        '. $cat_filter_html .'
                                                    
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
        $result = '<div class="blox-element grid-loop portfolio fullwidth-portfolio '.$extra_class.' '.$visibility.'" style="opacity:0;" data-column="'. $column .'">
                        '. $filter_html .'
                        <div class="row masonry-container" style="background-color:'. $bgcolor .';">'. $result .'</div>
                        <div class="clearfix"></div>
                    </div>';


    }


    wp_reset_query();
    wp_reset_postdata();

    $query = $temp_qry;
    $post = $temp_post;
    
    return $result;
}

add_shortcode('portfolio_row', 'blox_portfolio_row_parse_hook');

?>