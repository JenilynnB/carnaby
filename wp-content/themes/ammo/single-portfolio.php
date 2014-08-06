<?php
	get_header();

	global $smof_data;
?>

<?php while ( have_posts() ) : the_post(); ?>

<?php
/* Check Slider Selected
==============================*/
if (tt_getmeta('slider') != '' && tt_getmeta('slider') != 'none'):
    ?>
    <!-- Start Slider -->
    <div id="tt-slider" class="tt-slider">
        <?php
        $slider_name = tt_getmeta("slider");
        $slider = explode("_", $slider_name);
        $shortcode = '';
        if (strpos($slider_name, "layerslider") !== false)
            $shortcode = "[" . $slider[0] . " id='" . $slider[1] . "']";
        elseif (strpos($slider_name, "revslider") !== false)
            $shortcode = "[rev_slider " . $slider[1] . "]";
        echo do_shortcode($shortcode); ?>
    </div>
    <!-- End Slider -->
    <?php
endif; //slider
?>


<!-- Start Title Section
================================================== -->
<section class="page-title section">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="single-portfolio-title">
					<h1><?php the_title(); ?></h1>
					<?php if(isset($smof_data['port_next_prev_links']) && $smof_data['port_next_prev_links'] == 1): ?>
					<ul class="portfolio-controls list-inline">
						<li><?php previous_post_link('%link', __('<i class="glyph" data-icon="&#xe07a;"></i>', 'themeton')); ?></li>
						<?php if ($smof_data['portfolio_page'] && $smof_data['portfolio_page'] != 'Select a page:') : ?>
                            <li><a href="<?php echo get_permalink(get_page_by_path($smof_data['portfolio_page'])); ?>"><i class="fa fa-th"></i></a></li>
                        <?php endif; ?>
						<li><?php next_post_link('%link', __('<i class="glyph" data-icon="&#xe079;"></i>', 'themeton')); ?></li>
					<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ================================================== 
End Title -->
<?php
	
	$content_class = 'col-md-12 col-sm-12';
	$layout = isset($smof_data['portfolio_single_layout']) ? $smof_data['portfolio_single_layout'] : 'full';
	$option_layout = tt_getmeta('page_layout');
	if( $layout!=$option_layout && $option_layout!='' ){
		$layout = tt_getmeta('page_layout');
	}

	if( in_array($layout, array('left', 'right' )) ){
		$content_class = 'col-md-9';
		$content_class .= $layout=='left' ? ' pull-right' : '';
	}


function get_portfolio_media( $param ){
	global $post;
	$gallery_field = tt_getmeta('portfolio_gallery');
	$video_field = tt_getmeta('portfolio_video_mp4');

if( $gallery_field!='' ):
?>
<section class="portfolio-slider">
	<div class="swiper-container layout-<?php echo $param['layout']; ?>">
		<div class="swiper-wrapper">
			<?php
			if( $video_field!='' ){
				$fimage = '';
	            if (has_post_thumbnail(get_the_ID())) {
	                $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'blog');
	                $fimage = $image[0];
	            }
				$video_embed = get_video( array('video'=>$video_field, 'poster'=>$fimage) );
				echo "<div class='swiper-slide video'><div class='entry-media video-wrapper' style='display:none;'>$video_embed</div></div>";
			}

			$gimages = explode(',', $gallery_field);
			foreach ($gimages as $img_id) {
				$img = wp_get_attachment_url($img_id);
				echo '<div class="swiper-slide" style="background-image:url('. $img .');"></div>';
			}
			?>
		</div>

		<div class="swiper-control-prev"><i class="fa fa-angle-left"></i></div>
		<div class="swiper-control-next"><i class="fa fa-angle-right"></i></div>
		<div class="swiper-pagination"></div>

	</div>
</section>
<?php
	endif;
}

if( $layout=='full' ){
	get_portfolio_media( array('layout'=>'full') );
}

?>


<!-- Start Content
================================================== -->
<section class="primary section">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="<?php echo $content_class; ?>">
						<div class="content">
							
							<div class="row">
								<div class="col-md-12 single-content">

									<?php
									if( $layout!='full' ){
										get_portfolio_media( array('layout'=>'sidebar') );
									}

									/* Portfolio Single Video or Featured Image
									===========================================*/									
									$gallery_field = tt_getmeta('portfolio_gallery');
									$video_field = tt_getmeta('portfolio_video_mp4');
									$featured_image = '';
						            if (has_post_thumbnail(get_the_ID())) {
						                $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'blog');
						                $featured_image = $image[0];
						            }
									if( $gallery_field=='' && $video_field!='' ){
										$video_embed = get_video( array('video'=>$video_field, 'poster'=>$featured_image) );
										echo "<div class='video-wrapper fit-video'>$video_embed</div>";
									}
									else if( $gallery_field=='' && $video_field=='' && $featured_image!='' && tt_getmeta('hide_featured_image')!='1' ){
										echo "<div class='entry-media'><img class='img-responsive' src='$featured_image' alt='". get_the_title() ."' /></div>";
									}								

									the_content();

									// WP pages
                                    wp_link_pages(array(
                                        'before' => '<div class="page-link"><span>' . __('Pages:', 'themeton') . '</span>',
                                        'after' => '</div>',
                                        'link_before' => '<span>',
                                        'link_after' => '</span>'
                                    ));

                                    if (isset($smof_data['share_visibility']['share_port']) && $smof_data['share_visibility']['share_port'] == 1)
                                        social_share();
                                    ?>

								</div>
							</div>
							
						
							<?php 
							// Related Posts
							if($smof_data['port_related'] == 1 && tt_getmeta('related_post')!='0'):
								$perpage = 4;
								if( in_array($layout, array('left', 'right' )) ){ $perpage = 3; }
								tt_related_posts( array('per_page'=>$perpage) );
							endif;
							?>						
							
							<?php 

							// Post Comment 
							if (isset($smof_data['port_comment']) && $smof_data['port_comment'] == 1 ): ?>
							<div class="row">
								<div class="col-md-12">
									<?php comments_template('', true); ?>
								</div>
							</div>
							<?php endif; ?>

						</div><!-- End .content -->

					</div>
					<?php
					if( in_array($layout, array('left', 'right' )) ){
						global $current_sidebar;
						$current_sidebar = $smof_data['portfolio_single_sidebar'];
						get_sidebar();
					}
					?>
				</div>
		</div>
		</div>
	</div>
</section>
<!-- End Content
================================================== -->

<?php endwhile; ?>

<?php
	get_footer();
?>