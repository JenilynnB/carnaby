<?php
	get_header();
?>

<!-- Start Content
================================================== -->
<section class="primary section">
    <div class="container">
		<div class="row">
			this is the index template
                        <div class="col-md-12">
				<div class="row">
					<div class="col-md-9">
						<div class="content">
							<div class="row">
								<div class="col-md-12">
									<?php
									global $layout_sidebar;
									$layout_sidebar = 'with-sidebar';
		                            if (have_posts()) :
		                            	$loop_args = array(
							                            'overlay' => 'none',
							                            'excerpt' => 'both',
							                            'readmore' => __('Read more', 'themeton'),
							                            'grid' => '1',
							                            'element_style' => 'default'
						                            );
		                            	$result = '';
		                                while (have_posts()) : the_post();
		                                	ob_start();
		                                    blox_loop_regular($loop_args);
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
		                            endif;
		                            ?>
								</div>
							</div>
						</div>
					</div>
					<?php get_sidebar(); ?>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End Content
================================================== -->


<?php
	get_footer();
?>