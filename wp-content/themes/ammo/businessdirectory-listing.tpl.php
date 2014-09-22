<section class="primary section">
   
     <div class="container">
        
        <div class="content">       
            <?php 
            if ($is_sticky):
                 echo $sticky_tag;
            endif; ?>
            <div class="row">
                <div class="col-md-8">            
                    <div class="wpbdp_listing_heading">
                        <div class="wpbdp-listing-title-info">
                            <div class="entry-title">
                                <h1 itemprop="name"><?php echo $title; ?></h1>
                            </div>

                            <!--
                            <?php if ($actions): ?>
                                <?php echo $actions; ?>
                            <?php endif; ?>
                            -->
                            
                            <?php if((get_shopstyle_retailer_id($listing_id))!=''): ?>
                                <div class="listing-element"><a href="<?php echo get_shopstyle_retailer_url($listing_id); ?>"><?php echo wpbdp_render_listing_field('URL'); ?></a></div>
                            <?php else: ?>
                                <div class="listing-element"><?php echo wpbdp_render_listing_field_html('URL'); ?></div>
                            <?php endif; ?>
                            <div class="listing-element"></div>
                            <div class="listing-rating"><?php echo wpbdp_render_listing_field_html('Rating (average)'); ?></div>
                         </div>
                        <div class="row">
                            <div class="col-lg-7 col-md-8 col col-sm-9 col-xs-12">
                                <div class='listing-element'><?php echo get_top_apparel_categories_html();?></div>
                                <div > <?php echo render_listing_highlights(); ?> </div>
                            </div>
                        </div>
                    </div> 

                </div>
                <div class="col-md-4 pull-right">
                    <table class="listing-actions edit-trigger">
                    <tr>
                        <td class="listing-action favorite"><?php if (function_exists('wpfp_link')) { wpfp_link(0,"",0); }?></td>
                        <?php if(has_written_review()): ?>
                            <td class="listing-action review"><a href="#form_wrapper_edit" id="toplink"><i class="fa fa-plus-square-o"></i> Edit My Review</a></td>
                        <?php else: ?>
                            <td class="listing-action review"><a href="#form_wrapper" id="toplink"><i class="fa fa-plus-square-o"></i>Write a Review</a></td>
                        <?php endif; ?>
                    </tr>
                    <tr>
                        <td></td><td class="pull-right"><div ><a href=""><i class="fa fa-pencil-square-o"></i> Suggest an Edit</a></div></td>
                    </tr>
                    </table>
                </div>
            </div>     
            
         </div>
    </div>
</section>
<section class="products">
    <div class="container">
        <div class="content">
            <div class="row">     
                <div class="col-md-12">
                    
                    
                     <?php 
                     $retailer_id = get_field("shopstyle_retailer_id");
                     if ($retailer_id>0){
                        echo render_products(); 
                     }else{
                        $listing_url = wpbdp_render_listing_field('URL');
                    ?> 
                        
                        <div class='wpbdp-listing-screenshot'>
                        <?php echo do_shortcode("[screenshot width=600]".$listing_url."[/screenshot]"); ?>
                        </div>    
                    <?php 
                     }    
                     ?> 
                    
                    
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
                    <div class="col-md-8">

                            <!--<div class="listing-details cf <?php if ($main_image): ?>with-image<?php endif; ?>">
                            <div class="listing-details cf">
                                <?php echo $listing_fields; ?>
                            </div>
                            <?php if ($extra_images): ?>
                            <div class="extra-images">
                                <ul>
                                <?php foreach ($extra_images as $image): ?>
                                    <li><?php echo $image; ?></li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if ($main_image): ?>
                                <div class="main-image"><?php echo $main_image; ?></div>
                            <?php endif; ?>-->

                            



                        <div class="row">
                            <div class="col-md-12">
                                <?php echo render_category_info(); ?>
                            </div>       
                        </div>              
                    </div>

                    <div class="col-md-4">
                        <div class="row">
                            <div class="listing-side-container col-md-12">
                                <?php echo the_content();?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="listing-side-container col-md-12">
                                <?php echo render_shipping_info(); ?>
                            </div> 
                        </div>
                        <div class="row">
                            <div class="listing-side-container col-md-12">
                                <?php echo render_return_shipping_info(); ?>
                            </div>
                        </div>


                                <?php echo render_customer_support_info(); ?>
                                <br />
                                <br />

                                <br />
                                <!--
                                Women:
                                Categories
                                Styles

                                Men:

                                Kids & Baby:


                                Good For
                                -->



                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


