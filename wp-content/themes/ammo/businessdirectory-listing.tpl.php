<?php

$module = "";

if(isset($_GET["module"])){	$module = $_GET["module"];	}


if($module != "more_info" && $module != "reviews"):

?>
<section class="section listing-header">
   
     <div class="container">
        
        <div class="content">       

            <div class="row">
                <div class="col-md-8">            
                    <div class="wpbdp-listing short-info">
                        <div class="listing-title">
                            
                            <div class="topline-items">
                                
                                <div class="listing-element"><h1 itemprop="name"><?php echo $title; ?></h1></div>
                                <div class="listing-element"><?php echo wpbdp_render_listing_field_html('Rating (average)', $listing_id); ?></div>
                                <div class="listing-element"><?php echo render_price_field(); ?></div>
                            </div>
                            
                            
                        </div>
                        <div class="listing-element"><?php echo the_content();?></div>
                        
                        
                        <!--<div class='listing-element'><?php the_terms($listing_id, WPBDP_TAGS_TAX, '<i class="icon-tag"></i> ');?></div>  -->
                        
                        <!--<div class='listing-element'><?php echo get_top_apparel_categories_html();?></div>-->
                        <!--<div > <?php echo render_listing_highlights(); ?> </div> -->
                            
                    </div> 

                </div>
                <div class="col-md-4 col-sm-12 col-xs-12">
                    <div class="listing-actions edit-trigger">
                        
                        <div class="col-md-12 col-sm-4 col-xs-4 listing-action link"><?php echo get_listing_outbound_link($listing_id, 50); ?></div>
                        <div class="col-md-12 col-sm-4 col-xs-4 listing-action favorite"><?php if (function_exists('wpfp_link')) { wpfp_link(0,"",0); }?></div>
                        <div class="col-md-12 col-sm-4 col-xs-4 listing-action review">
                            <?php if(has_written_review()): ?>
                            <a href="#form_wrapper_edit" id="toplink"><i class="fa icon-bubble"></i>&nbsp;&nbsp;&nbsp;&nbsp;Edit My Review</a>
                            <?php else: ?>
                            <?php if(is_user_logged_in()): ?>
                                <a href="#form_wrapper" id="toplink" rel="write-review-form"><i class="fa icon-bubble"></i>&nbsp;&nbsp;&nbsp;&nbsp;Write a Review</a>
                            <?php else: ?>
                                <a href="" data-toggle="modal" data-target="#registrationModal"><i class="fa icon-bubble"></i>&nbsp;&nbsp;&nbsp;&nbsp;Write a Review</a>
                            <?php endif; ?>
                        <?php endif; ?>
                        </div>
                        <div class="sugggest-edit pull-right"><div class="suggest-edit"><a href="mailto: <?php echo support_email_address();?>"><i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp; Suggest an Edit</a></div></div>
                    </div>
                    
                    <div class="listing-actions-mobile edit-trigger">
                        
                        <div class="col-md-12 col-sm-4 col-xs-4 listing-action link">
                            <a href="<?php echo get_listing_outbound_url($listing_id); ?>" target="_blank"><i class="fa fa-external-link"></i><br/> Visit</a>
                        </div>
                        <div class="col-md-12 col-sm-4 col-xs-4 listing-action favorite"><?php if (function_exists('wpfp_link')) { wpfp_link(0,"",0); }?></div>
                        <div class="col-md-12 col-sm-4 col-xs-4 listing-action review">
                            <?php if(has_written_review()): ?>
                            <a href="#form_wrapper_edit" id="toplink"><i class="fa icon-bubble"></i><br/>Review</a>
                        <?php else: ?>
                            <a href="#form_wrapper" id="toplink"><i class="fa icon-bubble"></i><br/>Review</a>
                        <?php endif; ?>
                        </div>
                        <div class="sugggest-edit pull-right"><div class="suggest-edit"><a href="mailto: <?php echo support_email_address();?>"><i class="fa fa-pencil-square-o"></i>&nbsp;&nbsp; Suggest an Edit</a></div></div>
                    </div>
                    
                </div>
            </div>     
            
         </div>
    </div>
</section>
<section class="primary section">
    <div class="container">
        <div class="content">                
            <div class="row">
                <div class="col-md-12">
                    <div class="row">

                        <div class="col-md-8 col-sm-12 col-xs-12">
                            
                         <?php 
                         $retailer_id = get_field("shopstyle_retailer_id");
                         $products = render_products_slick_slider(); 
                         if ($retailer_id>0 && $products != ""){
                            echo "<h2>Popular ".$title." Products</h2>";
                            
                            echo $products;
                         }else{
                        ?> 

                            <div class='wpbdp-listing-screenshot'>
                            <?php if ($main_image): ?>
                                    <div class="main-image"><?php echo $main_image; ?></div>
                            <?php endif; ?>
                            </div>    
                        <?php 
                         }    
                         ?> 
                    
                            <h2>Site Info</h2>
                            <div class="listing-site-info">
                                <div class="row">
                                    <div class="info-container col-md-6 col-sm-6 col-xs-12">
                                        <label class="element-title"><i class="fa fa-truck"></i> US Shipping</label>
                                        <?php echo render_shipping_info(); ?>
                                    </div>

                                    <div class="info-container col-md-6 col-sm-6 col-xs-12">
                                        <label class="element-title"><i class="fa fa-mail-reply"></i> US Return Shipping:</label>
                                        <?php echo render_return_shipping_info(); ?>
                                    </div> 
                                </div>

                                <div class="row">
                                    <div class="info-container col-md-6 col-sm-6 col-xs-12">
                                        <?php echo render_canada_shipping(); ?>
                                    </div>
                                    <div class="info-container col-md-6 col-sm-6 col-xs-12">
                                        <?php echo render_international_shipping(); ?>
                                    </div>
                                </div>    

                                <div class="row">    
                                    <div class="info-container col-md-6 col-sm-6 col-xs-12">

                                        <?php echo render_customer_support_phone(); ?>
                                    </div>
                                    <div class="info-container col-md-6 col-sm-6 col-xs-12">

                                        <?php echo render_customer_support_email(); ?>
                                    </div>
                                </div>

                            </div>

                        </div>
                    
                        <div class="col-md-4 hidden-sm hidden-xs ">
                            <div class='row'>
                                <div class="carnaby-info">
                                    <p>With Carnaby West, you can find the best places to shop online. This listing lets you know what you'll find at this shop, details on how your purchased items will be shipped to you and reviews from other Carnaby West Members.
                                    If you have shopped at this site, we'd love you to leave a review about your experience!</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 category-info">
                                    <?php echo render_category_info(); ?>
                                </div>       
                            </div>              
                        </div>
                        <div class="hidden-lg hidden-md col-sm-12 col-xs-12">
                            <div class="row">
                            <a href="?module=more_info" class="btn btn-secondary more-info">More <?php echo $title; ?> Info</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</section>

<?php
endif;
?>

<?php
if($module=="more_info"):
?>

<section class="full-category-info section">
    <div class="container">
        <div class="content">                
            <div class="row">
                <a href="?" class="breadcrumb">< Back to <?php echo $title;?> listing</a>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <h2>More <?php echo $title; ?> Info </h2>
                        <div class="category-info">
                            <?php echo render_category_info(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    

<?php
endif;
?>
