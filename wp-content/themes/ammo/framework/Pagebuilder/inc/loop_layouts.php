<?php


/*
 * Post Permalink
 *************************************************************/
if( !function_exists('permalink') ){
    function permalink($post_obj = null){
        global $post;
        $obj = $post_obj!=null ? $post_obj : $post;
        $posttype = get_post_type($obj);

        if( $posttype == 'post' && get_post_format($obj->ID) == 'link' ){
            $link = tt_getmeta('format_link_url', $obj->ID);
            return $link!='' ? $link : get_permalink($obj->ID);
        }
        
        return get_permalink($obj->ID);
    }
}


/*
 * Loop Excerpt
 *************************************************************/
function blox_post_content( $options ) {
    
    $options = array_merge(array(
                    'button'=>'medium',
                    'excerpt' => 'nocontent',
                    'readmore' => __('Read more', 'themeton')
                    ),
                    $options);

    $content = $options['excerpt'];
    $button = $options['button'];
    $button = $button == 'small' ? 'btn-sm' : '';

    $readmoretext = $options['readmore'];

    global $post;
    if ($content == 'both') { ?>
        <div class="entry-content post-excerpt">
            <p><?php
                if(has_excerpt())
                    the_excerpt();
                else 
                    echo wp_trim_words( wp_strip_all_tags(strip_shortcodes(get_the_content())), 20 );
            ?></p>
            <p><a href="<?php echo permalink(); ?>" class="btn btn-default <?php echo $button; ?>"><?php echo $readmoretext; ?></a></p>
        </div>
    <?php } elseif ($content == 'content') { ?>
        <div class="entry-content post-content">
            <?php
                if(has_excerpt()) {
                    the_excerpt();
                    echo ' <span class="btn btn-default '.$button.'">'. $readmoretext .'</span>';
                } else {
                    global $more;
                    $more = 0;
                    the_content( '<span class="btn btn-default '.$button.'">'. $readmoretext .'</span>' );
                }
                ?>
        </div>
    <?php } elseif ($content == 'excerpt') { ?>
        <div class="entry-content post-excerpt">
            <p><?php
                if(has_excerpt())
                    echo the_excerpt();
                else 
                    echo wp_trim_words( wp_strip_all_tags(strip_shortcodes(get_the_content())), 20 );
            ?></p>
        </div>
    <?php }
}


/*
 * Post format : Video
 *************************************************************/
function blox_format_video() {
    global $post;
    $video_content = get_post_meta($post->ID, '_format_video_embed', true);
    if ($video_content == ''){
        return '';
    }
    
    $html = '';
    $html .= '<div class="entry-media">';
    $embedCheck = array("<embed", "<video", "<ifram", "<objec"); // only checking against the first 6

    $firstSix = substr($video_content, 0, 6); // get the first 6 char 

    if( validateURL($video_content) ){
        $oembed = wp_oembed_get( $video_content, array());
        if($oembed !== false) {
            // Embed convertion
            $html .= $oembed;
        } else {
            
            $fimage = '';
            if (has_post_thumbnail(get_the_ID())) {
                $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'blog');
                $fimage = $image[0];
            }
            // Seems self hosted video
            $html .= get_video_player( array('url'=>$video_content, 'poster'=>$fimage) );
        }
    } else if( in_array($firstSix, $embedCheck) ){
        $html .= $video_content;
    }
    $html .= '</div><!-- .entry-media -->';

    return $html;
}


/*
 * Post format : Audio
 *************************************************************/
function blox_format_audio() {
    global $post;
    $audio_content = tt_getmeta('format_audio_embed');
    if ($audio_content == ''){
        return '';
    }

    $html = '';
    $html .= '<div class="entry-media">';
    $embedCheck = array("<embed", "<video", "<ifram", "<objec"); // only checking against the first 6

    $firstSix = substr($audio_content, 0, 6); // get the first 6 char 

    if ( validateURL($audio_content) ) {
        $html .= get_audio_player( array('url'=>$audio_content) );
    }
    else if( in_array($firstSix, $embedCheck) ){
        $html .= $audio_content;
    }
    $html .= '</div><!-- .entry-media -->';

    return $html;
}


/*
 * Post format : Quote
 *************************************************************/
function blox_format_quote() {
    global $post;
    $quote = tt_getmeta('format_quote_text');
    if( !empty($quote) ){
        return '<div class="entry-media">
                    <blockquote>
                        <p>'. tt_getmeta('format_quote_text') .'</p>
                        <small>'. tt_getmeta('format_quote_source_name') .' <cite title="Source info">'. tt_getmeta('format_quote_source_url') .'</cite></small>
                        <p class="blockquote-line"><span></span></p>
                    </blockquote>
                </div>';
    }

    return '';
}


/*
 * Post format : Image
 *************************************************************/
function blox_format_image() {
    global $post;
    
    if (has_post_thumbnail($post->ID)){
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'blog');
        $image_source = $image[0];
        $image_source = blox_aq_resize($image_source, 600, 350, true);

        $html = '';
        $html .= '<div class="entry-media">';
            $html .= "<a href='". get_permalink($post->ID) ."'><img src='$image_source' alt='Featured Image'/></a>";
        $html .= '</div>';

        return $html;
    }
}

/*
 * Post format : Gallery
 *************************************************************/
function blox_format_gallery() {
    global $post;
    $gallery_field = tt_getmeta('format_gallery_images');
    $images = explode(',', $gallery_field);

    $html = '';
    foreach ( $images as $id ) {
        if( (int)$id > 0 ){
            $attach_url = wp_get_attachment_url($id);
            $attach_url = $attach_url!==false ? $attach_url : THEME_NOIMAGE;
            $img = blox_aq_resize($attach_url, 600, 350, true);
            //$html .= '<li><img itemprop="image" src="'.$img.'" alt="Image" class="img-responsive" /></li>';
            $html .= '<div class="swiper-slide">
                        <img itemprop="image" src="'.$img.'" alt="Image" class="img-responsive" />
                      </div>';
        }
    }

    return '<div class="entry-media">
                <div class="swiper-container swipy-slider">
                    <div class="swiper-wrapper">'. $html .'</div>
                    <div class="swiper-control-prev"><i class="fa fa-angle-left"></i></div>
                    <div class="swiper-control-next"><i class="fa fa-angle-right"></i></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>';
}



function check_youtube_vimeo($url){
    $yt1 = explode('youtube.com/', $url);
    $yt2 = explode('youtu.be/', $url);
    $vimeo = explode('vimeo.com/', $url);

    $video = '';

    if( count($yt1)>1 ){
        $video = $yt1[1];
        $video = str_replace('watch?v=', '', $video);
        $video = str_replace('embed/', '', $video);
        $vd = explode('&', $video);
        $video = $vd[0];
        return array('video'=>$video, 'site'=>'youtube');
    }
    else if( count($yt2)>1 ){
        $video = $yt2[1];
        return array('video'=>$video, 'site'=>'youtube');
    }
    else if( count($vimeo)>1 ){
        $video = $vimeo[1];
        return array('video'=>$video, 'site'=>'vimeo');
    }

    return false;
}


/* Post formats field
==========================================*/
function check_post_format($obj){
    $post_format = get_post_format($obj->ID);

    if( get_post_type($obj) != 'post' ){ return false; }

    switch( $post_format ){
        case 'video':
            $video_content = get_post_meta($obj->ID, '_format_video_embed', true);
            if( validateURL($video_content) && has_post_thumbnail($obj->ID) ){
                return true;
            }
            return $video_content!='' && !has_post_thumbnail($obj->ID) ? true : false;
            break;
        case 'audio':
            $audio_content = get_post_meta($obj->ID, '_format_audio_embed', true);
            return $audio_content!='' ? true : false;
            break;
        case 'gallery':
            $gallery_field = get_post_meta($obj->ID, '_format_gallery_images', true);
            return $gallery_field!='' ? true : false;
            break;
        case 'quote':
            $quote_content = get_post_meta($obj->ID, '_format_quote_text', true);
            return $quote_content!='' ? true : false;
            break;
    }

    return false;
}



//add_filter('embed_defaults','themeton_embed_defaults');
function themeton_embed_defaults($defaults) {
   $defaults['width'] = '100%';
   $defaults['height'] = 350;
   return $defaults;
}

/*
 * Featured Image with Overlay and proper options
 *************************************************************/
function hover_featured_image(array $options = array()) {
    $options = array_merge(array(
                    'overlay'=>'none',
                    'width'=>'600',
                    'height'=>'350'
                    ),
                    $options);

    global $post;
    $tmp_post = get_post($post->ID);
    $result = '';
    $post_format = get_post_format();
    $post_format = $post_format!='' ? $post_format : 'standard';

    if( check_post_format($post) && in_array($post_format, array('video', 'audio', 'gallery', 'quote')) ){
        return call_user_func('blox_format_'. $post_format);
    }

    
    /* Get featured image
    ====================================================*/
    $image = '';
    $image_source = '';

    if (has_post_thumbnail($post->ID)){
        $image_source = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
        $image = blox_aq_resize($image_source, $options['width'], $options['height'], true);
    }


    /* Overlay icons
    ====================================================*/
    $popup_link = $image_source;
    if( get_post_type($post) == 'portfolio' ){
        if( tt_getmeta('portfolio_video_mp4') != '' ){
            $popup_link = tt_getmeta('portfolio_video_mp4');
        }
    }
    else if( get_post_type($post) == 'post' && $post_format == 'video' ){
        if( tt_getmeta('format_video_embed') != '' && validateURL(tt_getmeta('format_video_embed')) ){
            $popup_link = tt_getmeta('format_video_embed');
        }
    }



    $icon_link = '';
    $icon_popup = '';
    if( in_array($options['overlay'], array('permalink', 'both')) ){
        $icon_link = '<div class="hover-icon"><a href="'. permalink() .'" title="" class=""><i class="fa fa-link"></i></a></div>';
    }
    if( in_array($options['overlay'], array('lightbox', 'both')) ){
        $icon_popup = '<div class="hover-icon"><a href="'. $popup_link .'" class="lightbox" title=""><i class="fa fa-expand"></i></a></div>';
    }
    $icon_link .= $icon_popup;
    $icon_link = $icon_link!='' ? '<div class="entry-hover">'.$icon_link.'</div>' : '';

    
    /* Entry media
    ====================================================*/
    if( in_array($options['overlay'], array('permalink', 'lightbox', 'both')) && $image!='' ){
        $result = '<div class="entry-media">
                        '. $icon_link .'
                        <img itemprop="image" src="'. $image .'" alt="Image" class="img-responsive" />
                    </div>';
    }
    else if( $image!='' ){
        $result = '<div class="entry-media">
                        <a href="'. permalink() .'"><img itemprop="image" src="'. $image .'" alt="Image" class="img-responsive" /></a>
                    </div>';
    }

    return $result;
}






/*
 * Regular blog loop
 *************************************************************/
function blox_loop_regular( $options ){

    $options = array_merge(array(
                    'overlay'   => 'none',
                    'excerpt'   => 'nocontent',
                    'readmore'   => '',
                    'grid'      => '4'
                    ),
                    $options);

    global $post, $layout_sidebar;
    
    $crop_width = 795;
    if(isset($layout_sidebar) && $layout_sidebar == 'full') {
        $crop_width = 1070;
    }

    $post_format = get_post_format();
    $post_format = $post_format!='' ? $post_format : 'standard';
    ?>
    <article itemscope itemtype="http://schema.org/BlogPosting" <?php post_class('entry fit-video format-'.$post_format); ?>>
        <?php echo hover_featured_image( array('overlay'=>$options['overlay'], 'width'=>$crop_width, 'height'=>0) ); ?>
        <div class="medium-content">
	        <div class="entry-title">
	            <h2 itemprop="headline">
	                <a itemprop="url" href="<?php echo permalink(); ?>"><?php the_title(); ?></a>
	            </h2>
	        </div>
	        <ul class="entry-meta list-inline">
	            <li itemprop="datePublished" class="meta-date"><?php echo date_i18n(get_option('date_format') , strtotime(get_the_date())); ?></li>
	            <li itemprop="author" class="meta-author"><?php echo __("By ", "themeton") . get_author_posts_link(); ?></li>
	            <li itemprop="keywords" class="meta-category"><?php echo __('In', 'themeton').' '.get_the_category_list(', '); ?></li>
	            <li itemprop="comment" class="meta-comment"><?php echo comment_count_text(); ?></li>
	            <li class="meta-like"><?php echo get_post_like(get_the_ID()); ?></li>
	        </ul>
	        <?php blox_post_content( array('excerpt'=>$options['excerpt'], 'readmore'=>$options['readmore']) ); ?>
        </div>
    </article>
<?php
}




/*
 * Grid functions
 *************************************************************/
function blox_loop_grid2( $options ){ return blox_loop_grid_hook($options); }
function blox_loop_grid3( $options ){ return blox_loop_grid_hook($options); }
function blox_loop_grid4( $options ){ return blox_loop_grid_hook($options); }

/*
 * Maronry functions
 *************************************************************/
function blox_loop_masonry2( $options ){ return blox_loop_grid_hook($options); }
function blox_loop_masonry3( $options ){ return blox_loop_grid_hook($options); }
function blox_loop_masonry4( $options ){ return blox_loop_grid_hook($options); }


/*
 * Grid columns Printing
 *************************************************************/
function blox_loop_grid_hook( $options ) {

    $options = array_merge(array(
                    'overlay'   => 'none',
                    'excerpt'   => 'nocontent',
                    'readmore'   => '',
                    'grid'      => '4',
                    'element_style' => 'default'
                    ),
                    $options);

    global $post, $layout_sidebar;
    $crop_width = 173;
    if($layout_sidebar == 'full'){
        $crop_width = 247;
    }
    
    $class = 'col-md-3 col-sm-6 col-xs-12';

    if( $options['grid'] == '2' ){
        $class = 'col-md-6 col-sm-6 col-xs-12';
    }
    else if( $options['grid'] == '3' ){
        $class = 'col-md-4 col-sm-6 col-xs-12';
    }

    $post_format = get_post_format();
    $post_format = $post_format!='' ? $post_format : 'standard';

    ?>
    <div class="<?php echo $class; ?> loop-item">
        <article itemscope itemtype='http://schema.org/BlogPosting' <?php post_class('entry '. $options['element_style'] .' format-'.$post_format); ?>>
            <?php echo hover_featured_image( array('overlay'=>$options['overlay']) ); ?>
            <div class="relative">
                <div class="entry-title">
                    <h2 itemprop="headline">
                        <a itemprop="url" href="<?php echo permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                </div>
                <?php blox_post_content( array('excerpt'=>$options['excerpt'], 'button'=>'small', 'readmore'=>$options['readmore']) ); ?>
                <ul class="entry-meta list-inline">
                    <li itemprop="datePublished" class="meta-date"><?php echo date_i18n(get_option('date_format'), strtotime(get_the_date())); ?></li>
                    <li itemprop="author" class="meta-author">By <a href="#" title="Author">Admin</a></li>
                    <li itemprop="keywords" class="meta-category"><?php echo __('In', 'themeton').' '.get_the_category_list(', '); ?></li>
                    <li itemprop="comment" class="meta-comment pull-right"><?php echo comment_count(); ?></li>
                    <li class="meta-like pull-right"><?php echo get_post_like(get_the_ID()); ?></li>
                </ul>
            </div>
        </article>
    </div>
    <?php
}



/*
 * Portfolio Template
 *************************************************************/
function blox_loop_portfolio( $options ){
    $options = array_merge(array(
                    'item_style' => 'default',
                    'overlay' => 'none',
                    'thumb' => '',
                    'class' => '',
                    'width' => '',
                    'height' => ''
                ),
                $options);

    global $post;

    $excerpt_html = '';
    if( $options['item_style'] == 'with_excerpt' ){
        $excerpt_html = '<div class="entry-content"><p>'. wp_trim_words( wp_strip_all_tags(do_shortcode(get_the_content())), 20 ) .'</p></div>';
    }

    $result = '';
    if( $options['item_style'] == 'alternative' ){
        $result = '<div class="post_filter_item '. $options['class'] .'">
                        <article itemscope itemtype="http://schema.org/BlogPosting" class="entry hover">
                            <div class="entry-media">
                                <div class="entry-hover">
                                    <div class="relative">
                                        <div class="entry-title">
                                            <h2 itemprop="headline">
                                                <a itemprop="url" href="'. get_permalink() .'">'. get_the_title() .'</a>
                                            </h2>
                                        </div>
                                        <ul class="entry-meta list-inline">
                                            <li itemprop="datePublished" class="meta-date">'. date_i18n(get_option('date_format'), strtotime(get_the_date())) .'</li>
                                            <li class="meta-like">'. get_post_like(get_the_ID()) .'</li>
                                        </ul>
                                    </div>
                                </div>
                                <img itemprop="image" src="'. $options['thumb'] .'" alt="Image" class="img-responsive" />
                            </div>
                        </article>
                    </div>';
    }
    else{
        $hover_args = array( 'overlay'=>$options['overlay'] );
        if( $options['height']!='' ){
            $hover_args = array_merge(array('width'=>$options['width'], 'height'=>$options['height']), $hover_args );
        }
        $entry_media = hover_featured_image( $hover_args );
        $entry_media = tt_getmeta('portfolio_gallery')!='' ? blox_portfolio_gallery($options) : $entry_media;

        $result = '<div class="post_filter_item '. $options['class'] .'">
                        <article itemscope itemtype="http://schema.org/BlogPosting" class="entry">
                            '. $entry_media .'
                            <div class="relative">
                                <div class="entry-title">
                                    <h2 itemprop="headline">
                                        <a itemprop="url" href="'. get_permalink() .'">'.get_the_title().'</a>
                                    </h2>
                                </div>
                                '. $excerpt_html .'
                                <ul class="entry-meta list-inline">
                                    <li itemprop="datePublished" class="meta-date">'. date_i18n(get_option('date_format'), strtotime(get_the_date())) .'</li>
                                    <li class="meta-like">'. get_post_like(get_the_ID()) .'</li>
                                </ul>
                            </div>
                        </article>
                    </div>';
    }

    return $result;

}



/*
 * Get Portfolio Gallery
 *************************************************************/
function blox_portfolio_gallery($options) {
    $options = array_merge(array(
                    'width' => '',
                    'height' => ''
                ),
                $options);

    global $post;
    $gallery_field = tt_getmeta('portfolio_gallery');
    $images = explode(',', $gallery_field);

    $html = '';
    foreach ( $images as $id ) {
        if( (int)$id > 0 ){
            $attach_url = wp_get_attachment_url($id);
            $attach_url = $attach_url!==false ? $attach_url : THEME_NOIMAGE;
            $img = blox_aq_resize($attach_url, 600, 350, true);
            $img = $options['height']!='' && $options['height']!='0' ? blox_aq_resize($attach_url, $options['width'], $options['height'], true) : $img;

            $html .= '<div class="swiper-slide">
                        <img itemprop="image" src="'.$img.'" alt="Image" class="portfolio-image" />
                      </div>';
        }
    }

    return '<div class="entry-media">
                <div class="swiper-container swipy-slider">
                    <div class="swiper-wrapper">'. $html .'</div>
                    <div class="swiper-control-prev"><i class="fa fa-angle-left"></i></div>
                    <div class="swiper-control-next"><i class="fa fa-angle-right"></i></div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>';
}




?>