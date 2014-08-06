<?php


function fix_shortcode_paragraph($content){
    if( substr($content, 0, 4)=='</p>' ){
        $content = substr($content, 4);
    }
    if( substr($content, -3)=='<p>' ){
        $content = substr($content, 0, strlen($content)-4);
    }
    return $content;
}



function blox_light_dark($color){
	if( $color=='' || $color=='transparent' || $color=='#fff' || $color=='#ffffff' ){
		return '';
	}
	$brightness = blox_get_brightness($color);
	return $brightness<200 ? 'light' : 'dark';
}

function blox_get_brightness($hex) {
    // returns brightness value from 0 to 255
    $hex = str_replace('#', '', $hex);

    $c_r = hexdec(substr($hex, 0, 2));
    $c_g = hexdec(substr($hex, 2, 2));
    $c_b = hexdec(substr($hex, 4, 2));

    return (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
}


/* HEX to RGBA - blox_hex2rgba($color, 0.7); */
function blox_hex2rgba($color, $opacity = false){

    $default = 'rgb(0,0,0)';

    //Return default if no color provided
    if (empty($color))
        return $default;

    //Sanitize $color if "#" is provided 
    if ($color[0] == '#') {
        $color = substr($color, 1);
    }

    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
        $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
    } elseif (strlen($color) == 3) {
        $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
    } else {
        return $default;
    }

    //Convert hexadec to rgb
    $rgb =  array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if($opacity){
    	if(abs($opacity) > 1)
    		$opacity = 1.0;
    	$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
    } else {
    	$output = 'rgb('.implode(",",$rgb).')';
    }

    //Return rgb(a) color string
    return $output;
}


function get_parallax_class($param){
    return $param=='parallax' ? 'parallax-section' : '';
}
function get_parallax_attr($param){
    return $param['type']=='parallax' ? 'data-stellar-background-ratio="0.5"' : '';
}


function get_post_filter_cats(){
	global $post;
	$filter_classes = '';
    $post_categories = wp_get_post_categories( get_the_ID() );
    foreach($post_categories as $c){
        $cat = get_category( $c );
        $filter_classes .= 'filter-'.$cat->slug.' ';
    }
    return $filter_classes;
}



/*
	Post Like Event
	=================================
*/
add_action('wp_ajax_blox_post_like', 'blox_post_like_hook');
add_action('wp_ajax_nopriv_blox_post_like', 'blox_post_like_hook');
function blox_post_like_hook() {
    try {
        $post_id = (int)$_POST['post_id'];
        $count = (int)blox_getmeta($post_id, 'post_like');
        if( $post_id>0 ){
        	blox_setmeta($post_id, 'post_like', $count+1);
        }
        echo "1";
    } catch (Exception $e) {
        echo "-1";
    }
    exit;
}

function blox_post_liked($post_id){
	$cookie_id = '';
	if( isset($_COOKIE['liked']) ){
		$cookie_id = $_COOKIE['liked'];
		$ids = explode(',', $cookie_id);
		foreach ($ids as $value) {
			if( $value+'' == $post_id+'' ){
				return 'liked';
			}
		}
	}
	return '';
}


add_action('wp_ajax_reset_post_likes', 'reset_post_likes_hook');
add_action('wp_ajax_nopriv_reset_post_likes', 'reset_post_likes_hook');
function reset_post_likes_hook() {
    if( isset($_GET['hook']) && $_GET['hook']=='reset' ){
        if( current_user_can( 'manage_options' ) ){
            $args = array( 'posts_per_page' => -1); 
            $posts = get_posts( $args );
            if ($posts){
                foreach ( $posts as $post ){
                    //setup_postdata($post);
                    blox_setmeta($post->ID, 'post_like', '0');
                }
            }

            $args = array( 'post_type'=>'portfolio', 'posts_per_page' => -1 ); 
            $posts = get_posts( $args );
            if ($posts){
                foreach ( $posts as $post ){
                    blox_setmeta($post->ID, 'post_like', '0');
                }
            }

            $_COOKIE['post_like'] = '';

            echo 'SUCCESS. :)';
        }
    }
    else{
        echo "<form method='get'>
                <input type='hidden' name='action' value='reset_post_likes' />
                <input type='hidden' name='hook' value='reset' />
                <input type='submit' value='Reset Post LIKES' />
              </form>";
    }
    
    exit;
}





function blox_getmeta($post_id, $meta){
	return get_post_meta($post_id, '_'.$meta, true);
}

function blox_setmeta($post_id, $meta, $value){
	if(count(get_post_meta($post_id , '_'.$meta)) == 0){
        add_post_meta($post_id , '_'.$meta, trim($value), true);
    }
    else{
        update_post_meta($post_id , '_'.$meta, trim($value));
    }
}




/* Blox Audio Player
================================================*/
function get_audio_player($options){
    $options = array_merge(array(
                    'title'=>'',
                    'url'=>'',
                    'color'=>'#3a87ad',
                    'extra_class'=>''
                    ),
                    (array)$options);

    $uniqid = uniqid();
    $title = $options['title'];
    $src = $options['url'];
    $color = $options['color'];
    $extra_class = $options['extra_class'];
	
	return  "<div class='blox-element audio $extra_class'>
                $title
				<div id='jquery_jplayer_$uniqid' data-pid='$uniqid' data-src='$src' class='jp-jplayer jplayer-audio'></div>
				<div class='jp-audio-container'>
					".get_media_player_html( array('id'=>$uniqid, 'color'=>$color) )."
				</div>
			</div>";
}


/* Blox Video Player
================================================*/
function get_video_player( $options ){
	$options = array_merge(array(
                    'title'=>'',
                    'url'=>'',
                    'poster'=>'',
                    'color'=>'#3a87ad',
                    'extra_class'=>''
                    ),
                    $options);

    $title = $options['title'];
	$url = $options['url'];
	$poster = $options['poster'];
	$color = $options['color'];
	$extra_class = $options['extra_class'];

	$path_info = pathinfo($url);
	$ext = $path_info['extension'];
	//$ext = substr($url, -3, 3);
	$uniqid = uniqid();
	return "<div class='blox-element video $extra_class'>
                $title
				<div id='jquery_jplayer_$uniqid' data-pid='$uniqid' data-ext='$ext' data-src='$url' data-poster='$poster' class='jp-jplayer jplayer-video jp-video-full'></div>
				<div class='jp-video-container'>
						".get_media_player_html( array('id'=>$uniqid, 'color'=>$color) )."
					</div>
			</div>";
}

function get_media_player_html($options){
	$options = array_merge(array(
                    'id'=>uniqid(),
                    'color'=>'#3a87ad'
                    ),
                    $options);

	$color = $options['color'];
	$uniqid = $options['id'];
	return "<div class='jp-media-player'>
				<div class='jp-type-single'>
					<div id='jp_interface_$uniqid' class='jp-interface'>
						<ul class='jp-controls-play'>
							<li><a href='#' class='jp-play' tabindex='1' style='display: block;'><i class='fa fa-play'></i></a></li>
							<li><a href='#' class='jp-pause' tabindex='1' style='display: none;'><i class='fa fa-pause'></i></a></li>
						</ul>
						<div class='jp-progress-container'>
							<div class='jp-progress'>
								<div class='jp-seek-bar' style='width: 102%;'>
									<div class='jp-play-bar'></div>
								</div>
							</div>
						</div>
						<div class='jp-time-holder'>
							<div class='jp-current-time'>00:00</div>
							<div class='jp-duration'>00:00</div>
						</div>
						<ul class='jp-controls-sound'>
							<li><a href='#' class='jp-mute' tabindex='1'><i class='fa fa-volume-up'></i></a></li>
							<li><a href='#' class='jp-unmute' tabindex='1'><i class='fa fa-volume-off'></i></a></li>
						</ul>
					</div>
				</div>
			</div>";
}



function get_blox_animate_class($animation){
	if( $animation=='top-to-bottom' || $animation=='bottom-to-top' || $animation=='left-to-right' || $animation=='right-to-left' || $animation=='appear' ){
        return "blox_animation_animate_before blox_animation_$animation";
    }
}





function get_video( $options ){
    global $post;

    $featured_image = '';
    if (has_post_thumbnail(get_the_ID())) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'blog');
        $featured_image = $image[0];
    }

    $options = array_merge(array(
                'video'=>'',
                'poster' => $featured_image
                ),
                $options);

    if( validateURL($options['video']) ){
        $oembed = wp_oembed_get( $options['video'], array());
        if($oembed !== false){
            return $oembed;
        }
        else{
            return get_video_player( array('url'=>$options['video'], 'poster'=>$options['poster']) );
        }
    }
    else{
        return $options['video'];
    }
}



?>