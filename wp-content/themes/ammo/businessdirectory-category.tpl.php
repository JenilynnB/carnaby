<?php


get_header();
        
$module = "";
if(isset($_GET["module"])){	$module = $_GET["module"];	}

$filters = get_field_labels($_GET);

$class = "";

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
<section class="section index-listings primary">
    <div class="container">
        <div class="content">
        <div class="row">
            <div class="col-md-12">                    
                <div class="row">
                    <div class="col-lg-12">
                        <?php if($module!="filters"): ?>
                        <?php wpbdp_the_listing_sort_options(); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    
                    
                    <?php if($module!='filters'): ?>
                        <div class="hidden-lg hidden-md col-sm-12 col-xs-12">
                            <a href="?module=filters" id="filters-button" class="btn btn-secondary filters"><i class="fa fa-filter"></i> Filters</a>
                        </div>
                        <?php $class='col-lg-3 col-md-3 hidden-sm hidden-xs'; ?>
                            
                    <?php else: ?>
                        <?php $class='col-lg-12'; ?>
                                
                    <?php endif; ?>
                        <div class='<?php echo $class;?>'>    
                                <?php if($module=='filters'): ?>
                                <a href="?" id="results-return-link" class="breadcrumb">< Back to Results</a>
                                <?php endif; ?>
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
                    
                        <?php if($module != "filters"):?>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">

                                <!--<?php echo $GLOBALS['wp_query']->request; ?>-->
                                <div id="listings-results">
                                <?php if (!empty($filters) && sizeof($filters)>0): ?>
                                    <div class='filters'>
                                    <?php 
                                        echo implode('  |  ', array_map(function ($v, $k) { return '<label>'.$k.'</label>: ' . $v; }, $filters, array_keys($filters)));
                                        
                                    ?>
                                    </div>
                                <?php endif; ?>
                                    
                                    <?php 
                                    if(have_posts()):
                                        while (have_posts()): the_post(); 
                                            echo wpbdp_render_listing(null, 'excerpt'); 
                                        endwhile;
                                    else:
                                        echo wpautop('Sorry, no sites match your search.');
                                    endif;
                                    ?>

                                    <div class="wpbdp-pagination">

                                        <?php 
                                        $args = array(
                                            'type' => 'array'
                                        );
                                        $paginate =  paginate_links($args);
                                        $pagination_links = "<nav><ul class='pagination'>";
                                        if(isset($paginate)&&sizeof($paginate)>0){
                                            foreach($paginate as $page){
                                                $pagination_links .= "<li>".$page."</li>";
                                            }
                                        }
                                        $pagination_links .= "</ul></nav>";
                                        echo $pagination_links;
                                        ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php endif; ?>

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