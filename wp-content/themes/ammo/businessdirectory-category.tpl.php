<?php
	get_header();
?>

<!-- Start Content
================================================== -->
<section class="primary section">
    <div class="container">
		<div class="row">
			<div class="col-md-12">
                                <div class="row">
					<div class="col-md-9 pull-right">
						<div class="content">
							<div class="row">
								<div class="col-md-12 single-content">
                                                                    <?php wpbdp_the_listing_sort_options(); ?>
                                                                    <?php while (have_posts()): the_post(); ?>
                                                                        <?php echo wpbdp_render_listing(null, 'excerpt'); ?>
                                                                    <?php endwhile; ?>
                                                                    <div class="wpbdp-pagination">
                                                                    <?php if (function_exists('wp_pagenavi')) : ?>
                                                                            <?php wp_pagenavi(); ?>
                                                                    <?php elseif (function_exists('wp_paginate')): ?>
                                                                            <?php wp_paginate(); ?>
                                                                    <?php else: ?>
                                                                        <span class="prev"><?php previous_posts_link(_x('&laquo; Previous ', 'templates', 'WPBDM')); ?></span>
                                                                        <span class="next"><?php next_posts_link(_x('Next &raquo;', 'templates', 'WPBDM')); ?></span>
                                                                    <?php endif; ?>
                                                                    </div>
                                                                    <?php
                                                                        
									//global $layout_sidebar;
                                            				//$layout_sidebar = 'with-sidebar';
                                                                        //if (have_posts()) :
                                                                            /*$loop_args = array(
							                            'overlay' => 'none',
							                            'excerpt' => 'both',
							                            'readmore' => __('Read more', 'themeton'),
							                            'grid' => '1',
							                            'element_style' => 'default'
						                            );*/
                                                                        
                                                                        /*
                                                                        $result = '';
                                                
                                                                        while (have_posts()) : the_post();

                                                                                ob_start();
                                                                            //blox_loop_regular($loop_args);
                                                                            //$result .= ob_get_contents();
                                                                            $result .= wpbdp_render_listing($this, "excerpt");
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
                                                                        //endif;
                                                                         * 
                                                                         */
                                                                        ?>
								</div>
							</div>
						</div>
					</div>
                                        <div class="col-md-3">
                                            <div class="sidebar">
                                                <?php 
                                                //put some code here to show the correct side menu on each category page
                                                echo do_shortcode( '[searchandfilter id="268"]' ) 
                                                
                                                ?>
                                            </div>
                                        </div>
                                        <!--
                                        <div class="col-md-3">
						<div class="content">
                                        <div class="blox-element blox-accordion">
                                            <div id="price-filter" class="accordion panel-group">
                                                <div class="acc-panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a data-toggle="collapse" href="price-content" data-parent="price-filter" class="collapsed">Price</a>
                                                        </h4>    
                                                    </div>
                                                    <div id="price-content" class="panel-collapse in">
                                                        <div class="panel-body">
                                                            <div class="blox-element blox-element-text" data-animate="none">
                                                                <p>some stuff goes in here</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                        </div>
                                                </div>
                                        </div>
					<?php get_sidebar(); ?>-->
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