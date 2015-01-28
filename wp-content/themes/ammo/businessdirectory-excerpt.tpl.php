<?php echo $thumbnail; ?>

<div class="wpbdp-listing-excerpt-details">
    <div class="entry-title">
        <div class="favorite-icon">
            <?php if (function_exists('wpfp_link')) { wpfp_link(); } ?> 
        </div>
        <?php echo wpbdp_render_listing_field_html('Business Name'); ?>
    </div>
    <div class="listing-url">
        <?php if((get_shopstyle_retailer_id($listing_id))!=''): ?>
            <div class="listing-element"><a href="<?php echo get_shopstyle_retailer_url($listing_id); ?>" target="_blank"><?php echo wpbdp_render_listing_field('URL'); ?></a></div>
        <?php else: 
            $url = wpbdp_render_listing_field('URL');
            if (strcmp(substr($url, 0, 7), "http://") !=0){
                $url = "http://" . $url;
            }
          ?>
            <div class="listing-element"><a href="<?php echo $url; ?>" target="_blank"><?php echo wpbdp_render_listing_field('URL'); ?></a></div>

        <?php endif; ?>
    </div>
    
    <div class="listing-rating"><?php echo wpbdp_render_listing_field_html('Rating (average)'); ?></div>
    <div class="price-field">
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
    </div>
    
    
    <br/>
    <div class="shipping-field">
        <?php
                render_shipping_info($listing_id, 'highlight');
                /*
                $shipping_cost = get_field('shipping_cost');

                if ( $shipping == "ship_free" ):
                        $shipping_info = 'Free Shipping';
                elseif ( $shipping == "ship_min" ):
                        $shipping_info = 'Free Shipping with orders $' . get_field('free_shipping_minimum_amount') . '+';
                elseif ( $shipping == "ship_flat" ):
                        $shipping_info = 'Standard shipping: $' . $shipping_cost ;
                else:
                        $shipping_info = 'Shipping costs increase with order size';
                endif;
                echo '<div itemprop="shipping_info" class="meta-tag">' . $shipping_info . '</div>';
                 * 
                 */
        ?>
    </div>
    <div class="return-shipping-field">
                <?php
                    /*Return Shipping Info*/
                    $returns = get_field('return_shipping');

                    if ( $returns == "return_free" ):
                        $return_display = 'Free Returns';
                    elseif ( $returns =="return_flat" ):
                        $return_display =  '$' . get_field('return_shipping_cost') . ' return shipping' ;
                    else:
                        $return_display = 'Buyer handles return shipping';
                    endif;
                    echo $return_display;
                ?>
                </div>
</div>
<!--
<div class="listing-details">
    <?php echo $listing_fields; ?>
</div>
-->
