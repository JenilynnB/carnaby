<?php
	get_header();
        
    
?>

<!-- Display Page Title
================================================== -->
<section class="page-title section " style="text-align:left;padding-top:20px; padding-bottom:20px;"  >
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <h1>Your Shopping Guide</h1>
                </div>
            </div>
        </div>
    </div>
</section>    
    
<!-- Start Content
================================================== -->
<section class="primary section">
    <div class="container">
		<div class="row">
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
		                            	/*
                                                $loop_args = array(
							                            'overlay' => 'none',
							                            'excerpt' => 'both',
							                            'readmore' => __('Read more', 'themeton'),
							                            'grid' => '1',
							                            'element_style' => 'default'
						                            );
                                                 * 
                                                 */
                                                
                                                $loop_args = array(
							                            'overlay' => 'permalink',
							                            'excerpt' => 'both',
							                            'readmore' => __('Read more', 'themeton'),
							                            'grid' => '2',
							                            'element_style' => 'dgrid4'
						                            );
                                                
		                            	$result = '';
		                                while (have_posts()) : the_post();
		                                	ob_start();
		                                    //blox_loop_regular($loop_args);
                                                    blox_loop_grid2($loop_args);
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