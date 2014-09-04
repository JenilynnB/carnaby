
 <div class="content">
    <?php 
    if ($is_sticky):
         echo $sticky_tag;
    endif; ?>
                
    <div class="col-md-12">            
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

                        <div class="listing-rating"><?php echo wpbdp_render_listing_field_html('Rating (average)'); ?></div>
                     </div>
                    <div class="wpbdp-listing-subtitle-info">
                        <div class="listing-element"><?php echo wpbdp_render_listing_field_html('URL'); ?></div>
                        <div class="listing-element"><?php echo render_price_field();?></div>
                        <div class="listing-element"><?php if (function_exists('wpfp_link')) { wpfp_link(); }?></div>
                        
                    </div>
                </div> 
    </div>

    <div class="col-md-7">
                <!--<div class="listing-details cf <?php if ($main_image): ?>with-image<?php endif; ?>">
                <div class="listing-details cf">
                    <?php echo $listing_fields; ?>
                </div>
                <?php if ($main_image): ?>
                    <div class="main-image"><?php echo $main_image; ?></div>
                <?php endif; ?>-->
                
                <div class="wpbdp-listing-screenshot">
                <?php
                $listing_url = wpbdp_render_listing_field('URL');
                echo do_shortcode("[screenshot width=600]".$listing_url."[/screenshot]");
                ?>
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
                
                <?php echo render_products(); ?>    
                    
    </div>
</div>
<div class="col-md-5">
        
            <div class="listing-side">
                <div class="listing-meta">

                
                <?php
                    echo the_content();     
                ?>
                
                    <div class="wpbdp-shipping-container">
                    <?php echo render_shipping_info(); ?>
                    <?php echo render_return_shipping_info(); ?>
                    </div>
                    <?php echo render_category_info(); ?>
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

