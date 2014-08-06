<?php if ($is_sticky): ?>
    <?php echo $sticky_tag; ?>
<?php endif; ?>

<div class="entry-title">
    <h1 itemprop="name"><?php echo $title; ?></h1>
</div>


<?php if ($actions): ?>
    <?php echo $actions; ?>
<?php endif; ?>


<?php if ($main_image): ?>
    <div class="main-image"><?php echo $main_image; ?></div>
<?php endif; ?>

<?php
/*Displaying shipping info*/
	if ( get_field('free_shipping') ):
		$shipping_info = 'Free Shipping';
	elseif ( get_field('free_shipping_minimum_order') ):
		$shipping_info = 'Free Shipping with orders $' . get_field('free_shipping_minimum_amount') . '+, Standard Shipping: $' . get_field('shipping_cost') ;
        elseif ( get_field('flat_rate_shipping') ):
		$shipping_info = 'Standard shipping: $' . get_field('shipping_cost') ;
	else:
		$shipping_info = 'Shipping costs increase with order size';
        endif;
	echo '<li itemprop="shipping_info" class="meta-tag">' . $shipping_info . '</li>';	
?>


<div class="listing-details cf <?php if ($main_image): ?>with-image<?php endif; ?>">
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