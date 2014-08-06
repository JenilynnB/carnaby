<?php
	get_header();
	
global $smof_data, $layout_sidebar;

$prefix = 'search';


$content_class = 'col-md-9';
$layout_sidebar = 'right';
if ( isset($smof_data[$prefix.'_sidebar_type']) && $smof_data[$prefix.'_sidebar_type'] == 'left') {
    $layout_sidebar = 'left';
    $content_class .= ' pull-right';
} elseif ( isset($smof_data[$prefix.'_sidebar_type']) && $smof_data[$prefix.'_sidebar_type'] == 'full') {
    $layout_sidebar = 'full';
    $content_class = 'col-md-12 col-sm-12';
}

$loop_layout = (isset($smof_data[$prefix.'_layout']) && $smof_data[$prefix.'_layout'] != '') ? $smof_data[$prefix.'_layout'] : 'regular';

?>



<!-- Start Title Section
================================================== -->
<section class="page-title section">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="" data-animate="fadeInUp">
					<h1>
					<?php // Title text
                    	printf( __( 'Search Results for: %s', 'themeton' ), get_search_query() );
                    ?>
                    </h1>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ================================================== 
End Title -->

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
								<div class="col-md-12">
									<?php
		                            if (have_posts()) :
		                            	$columns = 4;
		                            	$tmp = str_replace('grid', '', $loop_layout);
		                            	$tmp = str_replace('masonry', '', $tmp);

		                            	if( $tmp != 'regular') :
		                            		$columns = $tmp;
		                            	endif;
		                            	$loop_args = array(
							                            'overlay' => 'none',
							                            'excerpt' => 'excerpt',
							                            'readmore' => __('Read more', 'themeton'),
							                            'grid' => $columns,
							                            'element_style' => 'default'
						                            );
		                            	$result = '';
		                            	$function = 'blox_loop_regular';
        			                  	if(function_exists('blox_loop_'.$loop_layout))
	                                		$function = 'blox_loop_'.$loop_layout;

		                                while (have_posts()) : the_post();
		                                	ob_start();
		                                	call_user_func($function, $loop_args);
		                                    $result .= ob_get_contents();
		                                    ob_end_clean();
		                                endwhile;

		                                $pager_html = '';
		                                ob_start();
							            themeton_pager();
							            $pager_html .= ob_get_contents();
							            ob_end_clean();

		                                echo '<div class="blox-element blog medium-loop">
						                        <div class="row">
						                            <div class="col-md-12">'.$result.'</div>
						                        </div>
						                        '. $pager_html .'
						                      </div>';
						            else : ?>
						            	<h3><?php _e('Your search term cannot be found', 'themeton'); ?></h3>
						            	<p><?php _e('Sorry, the post you are looking for is not available. Maybe you want to perform a search?', 'themeton'); ?></p>
										<?php get_search_form();?>
										<br>
										<p><?php _e('For best search results, mind the following suggestions:', 'themeton'); ?></p>
										<ul class="borderlist-not">
									        <li><?php _e('Always double check your spelling.', 'themeton'); ?></li>
									        <li><?php _e('Try similar keywords, for example: tablet instead of laptop.', 'themeton'); ?></li>
									        <li><?php _e('Try using more than one keyword.', 'themeton'); ?></li>
									    </ul> <?php
		                            endif;
		                            ?>
								</div>
							</div>
						</div>
					</div>
					<?php
					if( $layout_sidebar != 'full' ) :
						get_sidebar('archive');
					endif;
					?>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End Content
================================================== -->


<?php get_footer(); ?>