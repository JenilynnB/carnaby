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
                        <h1>Discover the best places to shop online</h1>
                        <div class="search-box">
                            <?php
                            if( isset($smof_data['search_box']) && $smof_data['search_box'] == 1):
                                get_search_form();    
                            endif; 
                            ?>
                        </div>
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
    
    <div class='section-normal posts-panel'>
        <div class="container">
            <div class="row">
                <div class="column-md-12">
                    <div class="row">
                        <div class="content">
                            <h2 class='heading-title'>Recent Trends & Tips</h2>
                            <span class='heading-line'></span>
                            <div class="posts-panel-content text-center">
                                <?php echo do_shortcode('[do_widget "Carnaby Recent Posts Static"]'); ?>
                            </div>
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