<?php

//define( 'WOOCOMMERCE_USE_CSS', false );

add_theme_support( 'woocommerce' );

if( !function_exists('tt_woocommerce_active') ){
	function tt_woocommerce_active()
	{
		if ( class_exists( 'woocommerce' ) ){ return true; }
		return false;
	}	
}


function get_mobile_cart_holder(){
	if( class_exists( 'woocommerce' ) ){
		global $woocommerce;
		$cart = $woocommerce->cart;
		echo '<a class="mobile-cart-icon" id="mobile-cart-handler" href="javascript:;"><i class="fa fa-shopping-cart"></i><span>'. $cart->cart_contents_count .'</span></a>';
	}
	
}


//if(!tt_woocommerce_active()) { return false; }
//remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);



/* HIDE PAGE TITLE
=============================================*/
function ttwc_page_title() {
	return false;
}
add_filter('woocommerce_show_page_title', 'ttwc_page_title');


remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );





/* SHOP LOOP ITEM TITLE BEFORE
=============================================*/
if( isset($smof_data['woo_overlay']) && $smof_data['woo_overlay']=='1' ){
	add_action( 'woocommerce_before_shop_loop_item_title', 'ttwc_before_shop_loop_item_title', 10);
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

	remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
}
else{
	add_action( 'woocommerce_before_shop_loop_item_title', 'ttwc_st_before_shop_loop_item_title', 10);
	remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
}

function ttwc_st_before_shop_loop_item_title($param){
	global $product;
	
	$id = get_the_ID();
	$size = 'full';
	
	echo "<div class='product-thumbnail'>";
		$first_img = ttwc_gallery_first_thumbnail( $id , $size);
		if( $first_img!='' ){
			$first_img = blox_aq_resize($first_img, 480, 480, true);
			echo '<img itemprop="image" src="'.$first_img.'" class="product-hover" />';
		}
		else{
			$fimage = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');
	        $fimage = blox_aq_resize($fimage[0], 480, 480, true);
	        echo '<img itemprop="image" src="'.$fimage.'" />';
		}
	echo "</div>";
}

function ttwc_before_shop_loop_item_title($param){
	global $product;
	
	$id = get_the_ID();
	$size = 'full';
	
	echo "<div class='product-thumbnail'>";
		echo "<div class='product-image-hover' style='height: 263px;'>";
		echo " <a href='".get_permalink($id)."'>";
			$first_img = ttwc_gallery_first_thumbnail( $id , $size);
			if( $first_img!='' ){
				$first_img = blox_aq_resize($first_img, 480, 480, true);
				echo '<img itemprop="image" src="'.$first_img.'" class="product-hover" />';
			}
			else{
				$fimage = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');
		        $fimage = blox_aq_resize($fimage[0], 480, 480, true);
		        echo '<img itemprop="image" src="'.$fimage.'" />';
			}
			$fimage = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');
			if( isset($fimage[0]) && $fimage[0]!='' && $fimage[0]!=NULL && $fimage[0]!='NULL' ){
				$fimage = blox_aq_resize($fimage[0], 480, 480, true);
	        	echo '<img itemprop="image" src="'.$fimage.'" />';
			}
			else{
				$first_img = blox_aq_resize($first_img, 480, 480, true);
				echo '<img itemprop="image" src="'.$first_img.'" class="product-hover" />';
			}
	        
		echo "</a>";
		
			echo "<div class='cart-and-rating'>";
				echo "<div class='pull-left text-left'>";
					woocommerce_get_template( 'loop/add-to-cart.php' );
				echo "</div>";
				echo "<div class='pull-right text-right'>";
					woocommerce_get_template( 'loop/rating.php' );
				echo "</div>";
				echo "<div class='clearfix'></div>";
			echo '</div>';
		
		echo "</div>";

		if($product->product_type == 'simple') echo "<span class='cart-loading'></span>";
	echo "</div>";
}

function ttwc_gallery_first_thumbnail($id, $size){
	$active_hover = true;//get_post_meta( $id, '_product_hover', true );

	if(!empty($active_hover))
	{
		$product_gallery = get_post_meta( $id, '_product_image_gallery', true );
		
		if(!empty($product_gallery))
		{
			$gallery	= explode(',',$product_gallery);
			$image_id 	= $gallery[0];
			//$image 		= wp_get_attachment_image( $image_id, $size, false, array( 'class' => "attachment-$size product-hover" ));
			$image 		= wp_get_attachment_url($image_id);
			//$image 		= $image!==false ? $image : THEME_NOIMAGE;
			
			if(!empty($image)) return $image;
		}
	}
	return '';
}

/* WOO PAGINATION HOOK
=============================================*/
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
function ttwc_pagination() {
	global $wp_query;
    themeton_pager($wp_query);
}
add_action( 'woocommerce_after_shop_loop', 'ttwc_pagination', 10);




/* WOO BADGE
=============================================*/
add_action( 'woocommerce_before_shop_loop_item_title', 'ttwc__show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
function ttwc__show_product_loop_sale_flash(){
	global $post, $product;
	$availability = $product->get_availability();

	if( $product->is_on_sale() ){
		echo apply_filters('woocommerce_sale_flash', '<span class="onsale">'.__( 'Sale!', 'themeton' ).'</span>', $post, $product);
	}
	if ( !$product->is_in_stock() ){
		echo apply_filters('woocommerce_sale_flash', '<span class="outoffstock">'.__( 'Out Of Stock!', 'themeton' ).'</span>', $post, $product);
	}
}


/*
 *
 * Removes products count after categories name
 * 
 */
add_filter( 'woocommerce_subcategory_count_html', 'woo_remove_category_products_count' );
function woo_remove_category_products_count($count){
	$count = str_replace('(', '', $count);
	$count = str_replace(')', '', $count);
	return $count;
}




add_action( 'woocommerce_before_shop_loop_item', 'ttwc_before_shop_loop_item', 0 );
function ttwc_before_shop_loop_item(){
	echo '<div class="product-container">';
}

add_action( 'woocommerce_after_shop_loop_item', 'ttwc_after_shop_loop_item', 999999 );
function ttwc_after_shop_loop_item(){
	echo '</div>';
}

?>