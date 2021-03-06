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
    $term_raw = '';
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
                            $term_raw = $qtt;
                            $field = $qt['field'];
                        }
                    }
                }
            }
        }
    }
    
    $term_object = get_queried_object();
     
    
    if($term!=''){
        $term_object = get_term_by($field, $term, WPBDP_CATEGORY_TAX);
        if($term_object->parent!=0){
            $parent_term = get_term($term->parent, WPBDP_CATEGORY_TAX);
        }
    }else{
        if($term_object->parent!=0){
            $parent_term = get_term($term_object->parent, WPBDP_CATEGORY_TAX);
        }
    }
    
    $breadcrumbs = '';
    //$category_slug = WPBDP_Settings::get('permalinks-category-slug', WPBDP_CATEGORY_TAX);
    
    if($parent_term->term_id!="" && $parent_term->term_id != 4){
        //$parent_base_url = site_url("site_categories");
        //$parent_url = $parent_base_url."/".$parent_term->slug;
        $parent_url = get_term_link( $parent_term->term_id, WPBDP_CATEGORY_TAX );
        
        $breadcrumbs .= "<a href='".$parent_url."'>".$parent_term->name."</a>";
        $breadcrumbs .= " > "; 
        
    }

    $breadcrumbs .= $term_object->name;
    //If this is Girls, Boys or Baby, we don't want to go up one level to the parent (Kids & Baby)
    if($parent_term->term_id!="" && $parent_term->term_id != 4){
        $term = $parent_term->name;
        $term_raw = $parent_term->slug;
    }else{
        $term = $term_object->name;
        $term_raw = $term_object->slug;
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
                    
                    <!--mobile filter button here-->
                    <?php if($module!='filters'): ?>
                        <div class="hidden-lg hidden-md col-sm-12 col-xs-12">
                            <a href="?module=filters" id="filters-button" class="btn btn-secondary filters"><i class="fa fa-filter"></i> Filters</a>
                        </div>
                        <?php $class='col-lg-3 col-md-3 hidden-sm hidden-xs'; ?>
                            
                    <?php else: ?>
                        <?php $class='col-lg-12'; ?>
                                
                    <?php endif; ?>
                    
                    
                    
                </div>
                <div class="row">
                    
                    

                    <div class='<?php echo $class;?>'> 
                        <?php if($module=='filters'): ?>
                            <a href="?" id="results-return-link" class="breadcrumb">< Back to Results</a>
                        <?php endif; ?>
                            
                            <!--To be moved
                    <div class="col-lg-12">
                        <?php if($module!="filters"): ?>
                            <div class='row'>
                                <div class='col-lg-3 col-md-3 col-sm-12 col-xs-12'>
                                    <h4>Filter Results</h4>
                                </div>
                                <div class='col-lg-9 col-md-9 col-sm-12 col-xs-12'>
                                    <?php wpbdp_the_listing_sort_options(); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    -->
                        <h4>Filter Results</h4>
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

                        <div class="category-side-adslot">
                        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                        <!-- Category Page Sidebar -->
                        <ins class="adsbygoogle"
                             style="display:block"
                             data-ad-client="ca-pub-8149612001508185"
                             data-ad-slot="9833473751"
                             data-ad-format="auto"></ins>
                        <script>
                        (adsbygoogle = window.adsbygoogle || []).push({});
                        </script>
                        </div>
                    </div>
                     

                    <?php if($module != "filters"):?>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                            <?php wpbdp_the_listing_sort_options(); ?>
                            <!--<?php echo $GLOBALS['wp_query']->request; ?>-->
                            <?php if(!(stristr($_SERVER["HTTP_REFERER"], $_SERVER["HTTP_HOST"]))){ ?>
                                <div class="carnaby-info alert alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <?php 
                                    $cat_term = $term_object->name;
                                    $term_description = term_description($cat_term->id, WPBDP_CATEGORY_TAX);
                                    if(isset($term_description) && $term_description != ""){
                                        echo $term_description;
                                    } else {
                                        bloginfo('description');
                                    }?>

                                </div>
                            <?php } ?>
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
                                    $i=0;
                                    while (have_posts()): the_post(); 
                                        $i++;
                                        echo wpbdp_render_listing(null, 'excerpt', FALSE, $term_raw); 
                                        if($i==12){
                                        echo '
                                            <div class="category-main-mid-adslot">
                                                <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                                                    <!-- Category Page - Main mid well -->
                                                    <ins class="adsbygoogle"
                                                         style="display:block"
                                                         data-ad-client="ca-pub-8149612001508185"
                                                         data-ad-slot="5723698152"
                                                         data-ad-format="auto"></ins>
                                                    <script>
                                                    (adsbygoogle = window.adsbygoogle || []).push({});
                                                    </script>
                                            </div>'; 
                                         }   
                                    endwhile;
                                else:
                                    echo wpautop('Sorry, no sites match your search.');
                                endif;
                                ?>
                                </div>
                                <div class="wpbdp-pagination">

                                    <?php 
                                    //PHP is stripping out '+' signs from the URL, so we need to manually parse out args
                                    $parameter_query_string = $_SERVER['QUERY_STRING'];
                                    $arg_array = array();
                                    while($parameter_query_string !=''){
                                        //Find '=' sign
                                        $fp = strpos($parameter_query_string, '=');
                                        if($fp!=FALSE){
                                            $parameter = substr($parameter_query_string, 0, $fp);
                                            $parameter_query_string = substr($parameter_query_string, $fp+1);
                                        }else{
                                            $parameter_query_string = '';
                                        }

                                        $sp = strpos($parameter_query_string, '&');
                                        if($sp != FALSE){
                                            $value = substr($parameter_query_string, 0, $sp);
                                            $parameter_query_string = substr($parameter_query_string, $sp+1);
                                        }else{
                                            $value = substr($parameter_query_string, 0);
                                            $parameter_query_string = '';
                                        }    
                                        $arg_array[$parameter] = $value;

                                    }

                                    $args = array(
                                        'type' => 'array',
                                        'add_args' => $arg_array
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
                                <div class='category-main-bottom-adslot'>
                                    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
                                    <!-- Category Page - Main bottom well -->
                                    <ins class="adsbygoogle"
                                         style="display:block"
                                         data-ad-client="ca-pub-8149612001508185"
                                         data-ad-slot="4886093356"
                                         data-ad-format="auto"></ins>
                                    <script>
                                        (adsbygoogle = window.adsbygoogle || []).push({});
                                    </script>
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