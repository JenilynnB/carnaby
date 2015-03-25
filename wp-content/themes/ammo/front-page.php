<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


get_header();
?>
<section class="primary homepage">
    
    <div class="section-fullwidth main-panel" style="background-image: 
         url(<?php echo site_url('/wp-content/uploads/Carnaby-Homepage1.jpg');?>);">
        <div class="container">
            <div class="row">
                <div class="column-md-12">
                    <div>
                        <h1>Discover the best places to shop for clothing online</h1>
                        <!--
                        <div class="main-panel-categories">
                            <a href="/site_categories/women">
                                <div class="quick-start-container">
                                    <div class="quick-start-outline">
                                        Women
                                    </div>
                                </div>
                            </a>
                            <a href="/site_categories/men">
                                <div class="quick-start-container">
                                    <div class="quick-start-outline">
                                        Men
                                    </div>
                                </div>
                            </a>
                            <a href="/site-categories/kids-baby">
                                <div class="quick-start-container">
                                    <div class="quick-start-outline">
                                        Kids & Baby
                                    </div>
                                </div>
                            </a>
                        </div>
                        -->
                        <!--
                        <div class="search-box">
                            <?php
                            if( isset($smof_data['search_box']) && $smof_data['search_box'] == 1):
                                get_search_form();
                            endif; 
                            ?>
  
                        </div>
                        -->
                        
                        
                    </div>
                </div>
            </div>
        </div> 
    </div>
    <div class="section-normal info-panel">
        <div class="divider-space"></div>
        <div class="container">
            <div class="row">
                <div class="column-md-12">
                    <div class="row blox-row">
                        <div class='info-panel-content'>
                            <div class="blox-column-content info-box" data-animate="fadeInUp" style="-webkit-animation: 0s;">
                                <div class="blox-element service-block text-center" data-animate="fadeInUp" style="-webkit-animation: 0s;">
                                    <span class='block-icon'><i class="icon-handbag"></i></span>
                                    <h4>Largest directory of fashion online</h4>
                                    <p>We've collected everything you need to know about online clothing stores, from sizes and styles to shipping costs and return policies.</p>
                                </div>
                            </div>
                            <div class="blox-column-content info-box">
                                <div class="service-block text-center" data-animate="fadeInUp" style="-webkit-animation: 0s;">
                                    <i class="icon-like"></i>
                                    <h4>Read reviews from savvy shoppers</h4>
                                    <p>Get all the inside knowledge about stores you haven't shopped at yet and discover hidden gems.</p>
                                </div>
                            </div>
                            <div class="blox-column-content info-box">
                                <div class="service-block text-center" data-animate="fadeInUp" style="-webkit-animation: 0s;">
                                    <i class="icon-bubbles"></i>
                                    <h4>Share your experience</h4>
                                    <p>Tell the world about your favorite stores, from their great customer service to their perfectly tailored cuts.</p>
                                </div>
                            </div>
                            <div class="blox-column-content info-box">
                                <div class="service-block text-center" data-animate="fadeInUp" style="-webkit-animation: 0s;">
                                    <i class="icon-basket"></i>
                                    <h4>Shop with confidence</h4>
                                    <p>Don't know what will fit or how fabrics will feel? Shop only at stores that offer free shipping and free returns.</p>
                                </div>
                            </div>
                            <div class="blox-column-content info-box">
                                <div class="service-block text-center" data-animate="fadeInUp" style="-webkit-animation: 0s;">
                                    <i class="icon-present"></i>
                                    <h4>Discover new ways to shop</h4>
                                    <p>Find the best new sites to shop in different ways, with rentals, subscriptions and personally styled shipments. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class='section-normal listings-panel'>
        <div class="container">
            <div class="row">
                <div class="column-md-12">
                    <div class="row">
                        <div class="content">
                            <h2 class='heading-title'>Top Rated Stores</h2>
                            <span class='heading-line'></span>
                            <div class='categories-panel-content text-center'>
                                <?php 
                                $top_posts = BusinessDirectory_RatingsModule::get_highest_rated_listings("4");
                                foreach ($top_posts as $post) {
                                    wpbdp_render_listing($post, 'excerpt', true);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class='section-normal categories-panel'>
        <div class="container">
            <div class="row">
                <div class="column-md-12">
                    <div class="row">
                        <div class="content">
                            <h2 class='heading-title'>Categories</h2>
                            <span class='heading-line'></span>
                            <div class='categories-panel-content text-center'>

                                <div class="category-content col-sm-12 col-xs-12 col-md-4" id="category-women" style="background-image: 
                                     url(<?php echo site_url('/wp-content/uploads/Carnaby-Women.png');?> );">

                                    <div class="category-overlay" id="overlay-women">
                                        <div class="btn btn-secondary category-btn"><a href="<?php echo site_url().'/site_categories/Women';?>">VIEW</a></div>
                                    </div>
                                    <div class="category-label" id="women-category-title"><a href="<?php echo site_url().'/site_categories/Women';?>">Women</a></div>
                                </div>
                                <div class="category-content col-sm-12 col-xs-12 col-md-4" id="category-men" style="background-image: 
                                     url(<?php echo site_url('/wp-content/uploads/Carnaby-Men.png');?> );">

                                    <div class="category-overlay" id="overlay-men">
                                        <div class="btn btn-secondary category-btn"><a href="<?php echo site_url().'/site_categories/Men';?>">VIEW</a></div>
                                    </div>
                                    <div class="category-label" id="men-category-title"><a href="<?php echo site_url().'/site_categories/Men';?>">Men</a></div>
                                </div>
                                <div class="category-content col-sm-12 col-xs-12 col-md-4" id="category-kids" style="background-image: 
                                     url(<?php echo site_url('/wp-content/uploads/Carnaby-Kids.png');?> );">

                                    <div class="category-overlay" id="overlay-kids">
                                        <div class="btn btn-secondary category-btn"><a href="<?php echo site_url().'/site_categories/kids-baby';?>">VIEW</a></div>
                                    </div>
                                    <div class="category-label" id="kids-category-title"><a href="<?php echo site_url().'/site_categories/kids-baby';?>">Kids & Babies</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class='section-normal categories-panel'>
        <div class="container">
            <div class="row">
                <div class="column-md-12">
                    <div class="row">
                        <div class="content">
                            <h2 class='heading-title'>Quick Searches</h2>
                            <span class='heading-line'></span>
                            <div class='text-center'>
                                <div class='row quick-search-panel-content '>
                                    <div class='col-sm-12 col-md-6 quick-search-img-lg' style='background-image:url("<?php echo site_url("/wp-content/uploads/women.jpg");?>")'>
                                        <a href='<?php echo site_url("/women/?_sfm_shipping=ship_free&_sfm_return_shipping=return_free");?>'>
                                            <div class='quick-search-overlay-lg'>Womens Apparel, Free Shipping & Free Returns</div>
                                        </a>
                                        
                                    </div>
                                    <div class='col-sm-12 col-md-6 quick-search-img-lg' style='background-image:url("<?php echo site_url("/wp-content/uploads/men.jpg");?>")'>
                                        <a href='<?php echo site_url("/men/?_sfm_shipping=ship_free&_sfm_return_shipping=return_free");?>'>
                                            <div class='quick-search-overlay-lg'>Mens Apparel, Free Shipping & Free Returns</div>
                                        </a>
                                    </div>
                                </div>
                                <div class='row quick-search-panel-content '>
                                    
                                    <div class='col-md-3 col-sm-6 col-xs-12 quick-search-img' style='background-image:url("<?php echo site_url("/wp-content/uploads/work.jpg");?>")'>
                                        <a href='<?php echo site_url("/men/?_sfm_good_for=wear_to_work");?>'>
                                            <div class='quick-search-overlay'>Office Attire for Guys</div>
                                        </a>
                                    </div>
                                    <div class='col-md-3 col-sm-6 col-xs-12 quick-search-img' style='background-image:url("<?php echo site_url("/wp-content/uploads/bump.jpg");?>")'>
                                        <a href='<?php echo site_url("/women/?_sfm_good_for=maternity");?>'>
                                            <div class='quick-search-overlay'>Maternity Clothes</div>
                                        </a>
                                    </div>
                                    <div class='col-md-3 col-sm-6 col-xs-12 quick-search-img' style='background-image:url("<?php echo site_url("/wp-content/uploads/bridal.jpg");?>")'>
                                        <a href='<?php echo site_url("/women/?_sfm_good_for=weddings");?>'>
                                            <div class='quick-search-overlay'>Bridal Fashions</div>
                                        </a>
                                    </div>
                                    <div class='col-md-3 col-sm-6 col-xs-12 quick-search-img ' style='background-image:url("<?php echo site_url("/wp-content/uploads/swim.jpg");?>")'>
                                       <a href='<?php echo site_url("/women/?_sfm_good_for=beach_vacation");?>'>
                                           <div class='quick-search-overlay'>Swimsuits & Coverups</div>
                                       </a> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class='section-normal'>
        <div class="container">
            <div class="row">
                <div class="column-md-12">
                    <div class="row">
                        <div class="text-center">
                            <h2 class='heading-title'>Recent Reviews</h2>
                            <span class='heading-line'></span>
                            <div class='review-panel-content text-left'>
                                <?php 
                                    $reviews = BusinessDirectory_RatingsModule::get_recent_reviews(0, 6);
                                    foreach($reviews as $review){
                                        $vars = array();
                                        $vars['rating'] = $review;

                                        $template_path = WPBDP_RATINGS_TEMPLATES_PATH . '/single-review.tpl.php';
                                        ?>
                                        <div class="col-md-6">
                                        <?php
                                        echo wpbdp_render_page($template_path, $vars);
                                        ?>
                                        </div>
                                        <?php
                                    }
                                
                                ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    
    
    <?php
        
        $args = array(
            'posts_per_page' => 3,
            'orderby' => 'post_date',
            'order' => 'DESC',
            'post_type' => 'post',
            'post_status' => 'publish'
        );
        $posts = get_posts($args);
        
    
    ?>
    
    <div class='section-normal posts-panel'>
        <div class="container">
            <div class="row">
                <div class="column-md-12">
                    <div class="row">
                        <div class="content">
                            <h2 class='heading-title'>Recent Trends & Tips</h2>
                            <span class='heading-line'></span>
                            
                            <div class='posts-panel-content text-center'>
                                <?php
                                foreach($posts as $post){
                                    $post_thumbnail_url = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
                                    $post_thumbnail_url = $post_thumbnail_url[0];
                                    $post_title = $post->post_title;
                                    $post_permalink = $post->guid;
                                    
                                ?>
                                    <div class="posts-content col-sm-12 col-xs-12 col-md-4">
                                        <div class="post-thumb-container">
                                            <a href="<?php echo $post_permalink;?>">
                                            <img src="<?php echo $post_thumbnail_url;?>" class="post-thumb-img">
                                            </a>
                                        </div>
                                        <div class="post-title">
                                            <a href="<?php echo $post_permalink;?>"><?php echo $post_title;?></a>
                                            <span class="link-icon"><i class="fa fa-arrow-right"></i></span>
                                        </div>
                                    </div>
                                
                                <?php
                                }?>
                                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</section>



<?php
get_footer();


?>