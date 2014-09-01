<div class="col-md-7">
    <div class="content">
        <div class="row">
            <div class="col-md-12 single-content">


                <?php if ($is_sticky): ?>
                    <?php echo $sticky_tag; ?>
                <?php endif; ?>
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
                        <div class="listing-url"><?php echo wpbdp_render_listing_field_html('URL'); ?></div>
                        <?php if (function_exists('wpfp_link')) { wpfp_link(); } ?> 
                    </div>
                </div>   

                <!--<div class="listing-details cf <?php if ($main_image): ?>with-image<?php endif; ?>">
                <div class="listing-details cf">
                    <?php echo $listing_fields; ?>
                </div>-->


                <?php if ($main_image): ?>
                    <div class="main-image"><?php echo $main_image; ?></div>
                <?php endif; ?>

                <?php if ($extra_images): ?>
                <div class="extra-images">
                    <ul>
                    <?php foreach ($extra_images as $image): ?>
                        <li><?php echo $image; ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<div class="col-md-5">
    <div class="listing-side">
        <div class="listing-meta">

        <?php
        /*Price info*/
        $prices = get_field_object('price');  //returns the array of all key-value pairs, along with the selected value
        $price = get_field('price');    //returns an array of all selected values
        
        $n = count($price);
        $price_formatted = $prices['choices'][$price[0]];
        
        if($n > 1):
            $price_formatted .= " - ";
            $price_formatted .= $prices['choices'][$price[$n-1]];
            
        endif;
        
        echo $price_formatted;   
       ?>
        <?php
            
            $shipping = get_field('shipping');
            $shipping_cost = get_field('shipping_cost');
            
            if ( $shipping == "ship_free" ):
                    $shipping_info = 'Free Shipping';
            elseif ( $shipping == "ship_min" ):
                    $shipping_info = 'Free Shipping with orders $' . get_field('free_shipping_minimum_amount') . '+, Standard Shipping: $' . $shipping_cost ;
            elseif ( $shipping == "ship_flat" ):
                    $shipping_info = 'Standard shipping: $' . $shipping_cost ;
            else:
                    $shipping_info = 'Shipping costs increase with order size';
            endif;
            echo '<div itemprop="shipping_info" class="meta-tag">' . $shipping_info . '</div>';
        ?>
            <div class="tag-links">
            <?php
                /*Return Shipping Info*/
                $returns = get_field('return_shipping');
                            
                if ( $returns == "return_free" ):
                    $return_display = 'Free Returns';
                elseif ( $returns =="return_flat" ):
                    $return_display =  'Flat rate return fee $' . get_field('return_shipping_cost');
                else:
                    $return_display = 'Buyer handles return shipping';
                endif;
                echo $return_display;
            ?>
            </div>
            
            <?php echo the_content(); ?>
            
          
            <?php
                echo render_category_info();
            ?>
            
            <br />
            <br />
            <strong>Support Phone:</strong>
            <?php
                echo get_field('support_phone');
            
            ?>
            <br />
            <strong>Support Email:</strong>
            <?php
                echo get_field('support_email');
            ?>
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

