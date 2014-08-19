<?php
	get_header();
?>

<?php
    //after submitting search & filter form once
    $query_tax = get_query_var("tax_query");
    //there is only one term inside the array passed in common-functions.php, if more are passed this should be expanded
    $query_tax = $query_tax[0];    
    $main_query = get_queried_object();
    
    if($query_tax){
        $term = get_term_by($query_tax["field"], $query_tax["terms"], WPBDP_CATEGORY_TAX);
        $parent = get_term($term->parent, WPBDP_CATEGORY_TAX);
    }else if($main_query){
        //$term = get_term_by($query_tax["field"], $query_tax["terms"], WPBDP_CATEGORY_TAX);
        $term = $main_query;
        $parent = get_term($main_query->parent, WPBDP_CATEGORY_TAX);
    }
    
    $breadcrumbs = '';
    //$category_slug = WPBDP_Settings::get('permalinks-category-slug', WPBDP_CATEGORY_TAX);
    
    if($parent->term_id!=""){
        $parent_base_url = site_url("site_categories");
        $parent_url = $parent_base_url."/".$parent->slug;
        
        $breadcrumbs .= "<a href='".$parent_url."'>".$parent->name."</a>";
        $breadcrumbs .= " > ";
    }
    
    $breadcrumbs .= $term->name;
    
    //when starting from a category page
    /*
    if($term){
        $breadcrumbs = $wpbdp_tax_terms;
        $breadcrumbs = $term->name;
    }else if ($main_query){
        $breadcrumbs = $main_query->name;
    }
*/
?>
                            

<section class="page-title section " style="text-align:left;padding-top:20px; padding-bottom:20px;"  >
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <h1><?php echo $breadcrumbs ?></h1>
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
                                                
                                                //after submitting search & filter form once
                                                /*
                                                $query_tax = get_query_var("tax_query");
                                                $wpbdp_tax_terms = $query_tax[0]["terms"];
                                                
                                                //when starting from a category page
                                                $main_query = get_queried_object();
                                                */
                                                if($term->slug == "women"){
                                                    echo do_shortcode( '[searchandfilter id="268"]' ); 
                                                }else if ($term->slug == "men"){
                                                    echo do_shortcode( '[searchandfilter id="1065"]' );
                                                }else if ($term->slug == "girls"){
                                                    echo do_shortcode( '[searchandfilter id="1147"]' );
                                                }else if ($term->slug == "boys"){
                                                    echo do_shortcode( '[searchandfilter id="1148"]' );
                                                }else if ($term->slug == "baby"){
                                                    echo do_shortcode( '[searchandfilter id="1149"]' );
                                                }else{
                                                    echo do_shortcode( '[searchandfilter id="1143"]' );
                                                }
                                                
                                                
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