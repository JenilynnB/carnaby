<?php
	get_header();
?>

<?php
    //after submitting search & filter form once
    $query_tax = get_query_var("tax_query");
    $term = '';
    if(!empty($query_tax)){
        foreach($query_tax as $qt){
            if(isset($qt['taxonomy'])){
                if($qt['taxonomy']==WPBDP_CATEGORY_TAX){
                    $queryterms = array();
                    if(!is_array($qt['terms'])){
                        $queryterms[] = $qt['terms'];
                    }else{
                        $queryterms = $qt['terms'];
                    }
                    foreach($queryterms as $qtt){
                        if(strcasecmp($qtt,'women')==0||
                        strcasecmp($qtt,'men')==0||
                        strcasecmp($qtt,'kids-baby')==0||
                        strcasecmp($qtt,'girls')==0||
                        strcasecmp($qtt,'boys')==0||
                        strcasecmp($qtt,'baby')==0){
                            $term = $qtt;
                            $field = $qt['field'];
                        }
                    }
                }
            }
        }
    }else{
        $term_object = get_queried_object();
    }    
    
    if($term!=''){
        $term_object = get_term_by($field, $term, WPBDP_CATEGORY_TAX);
        if($term_object->parent!=0){
            $parent_term = get_term($term->parent, WPBDP_CATEGORY_TAX);
        }
    }else if($main_query){
        if($term_object->parent!=0){
            $parent_term = get_term($term->parent, WPBDP_CATEGORY_TAX);
        }
    }
    
    $breadcrumbs = '';
    //$category_slug = WPBDP_Settings::get('permalinks-category-slug', WPBDP_CATEGORY_TAX);
    
    if($parent_term->term_id!=""){
        $parent_base_url = site_url("site_categories");
        $parent_url = $parent_base_url."/".$parent_term->slug;
        
        $breadcrumbs .= "<a href='".$parent_url."'>".$parent_term->name."</a>";
        $breadcrumbs .= " > "; 
        
    }
    
    $breadcrumbs .= $term_object->name;
    
    if($parent_term->term_id!=""){
        $term = $parent_term->name;
    }else{
        $term = $term_object->name;
    }
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
					<div class="col-md-3">
                                            <div class="sidebar">
                                                <?php 
                                                if(strcasecmp($term, "women")==0){
                                                    echo do_shortcode( '[searchandfilter id="268"]' ); 
                                                }else if (strcasecmp($term, "men")==0){
                                                    echo do_shortcode( '[searchandfilter id="1065"]' );
                                                }else if (strcasecmp($term,"girls")==0){
                                                    echo do_shortcode( '[searchandfilter id="1147"]' );
                                                }else if (strcasecmp($term, "boys")==0){
                                                    echo do_shortcode( '[searchandfilter id="1148"]' );
                                                }else if (strcasecmp($term,"baby")==0){
                                                    echo do_shortcode( '[searchandfilter id="1149"]' );
                                                }else{
                                                    echo do_shortcode( '[searchandfilter id="1143"]' );
                                                }
                                                
                                                
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-9 pull-right">
						<div class="content">
							<div class="row">
								<div class="col-md-12 single-content">
                                                                    <?php wpbdp_the_listing_sort_options(); ?>
                                                                    <!--<?php echo $GLOBALS['wp_query']->request; ?>-->
                                                                    <div id="listings-results">
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
                                                                    <?php while (have_posts()): the_post(); ?>
                                                                        <?php 
                                                                            echo wpbdp_render_listing(null, 'excerpt'); 
                                                                            //echo do_shortcode('[searchandfilter id="268" show="results"]');
                                                                        ?>
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
                                                                    </div>
                                                                   
								</div>
							</div>
						</div>
					</div>
                                        
                                        
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