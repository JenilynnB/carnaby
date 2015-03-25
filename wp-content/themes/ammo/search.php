<?php


get_header();
        
$module = "";
if(isset($_GET["module"])){	$module = $_GET["module"];	}
//echo print_r($_GET);

get_field_labels($_GET);

$class = "";

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

                                echo do_shortcode( '[searchandfilter id="3158"]' );
                               

                                ?>  
                            
                        </div>
                    
                        <?php if($module != "filters"):?>
                        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">

                                <?php echo $GLOBALS['wp_query']->request; ?>
                                <div id="listings-results">
                                    
                                <?php if (!empty($filters) && sizeof($filters)>0): ?>
                                    <div class='filters'>
                                        <h4>Filters</h4>
                                    <?php 
                                        echo implode('  |  ', array_map(function ($v, $k) { return '<label>'.$k.'</label>: ' . $v; }, $filters, array_keys($filters)));
                                        
                                    ?>
                                    </div>
                                <?php endif; ?>
                                    <div class='listings-excerpts'>
                                    <?php 
                                    if(have_posts()):
                                        while (have_posts()): the_post(); 
                                            echo wpbdp_render_listing(null, 'excerpt'); 
                                        endwhile;
                                    else:
                                        echo wpautop('Sorry, no sites match your search.');
                                    endif;
                                    ?>
                                    </div>
                                    <div class="wpbdp-pagination">

                                        <?php 
                                        $args = array(
                                            'type' => 'array',
                                            'add_args' => $_GET
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