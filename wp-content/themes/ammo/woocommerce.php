<?php
	get_header();


global $post, $wp_query, $woocommerce, $page, $smof_data;
global $current_sidebar, $woo_term_desc;

$tmp_post = $post;
$temp_query = $wp_query;

ob_start();
woocommerce_page_title();
$page_title = ob_get_clean();


if( is_shop() || is_product_category() || is_product_tag() ){

	/* Category description
	===========================*/
	if( is_product_category() ){
		$woo_term_desc = term_description();
	}

	/* Fixing page titles when not created pages */
	if( empty($page_title) && !empty($post) )
		$post->post_title = __('Shop', 'themeton');
	elseif( !empty($page_title) && !empty($post) )
		$post->post_title = $page_title;


    $woo_page_id = woocommerce_get_page_id('shop');
    
    $the_query = new WP_Query('page_id='. $woo_page_id);
 	if( $the_query->have_posts() ){
        $wp_query = $the_query;
        $wp_query->the_post();
    	$post->post_title = $page_title;
 	}
}

	
/* include header
============================*/
if( !is_product() ){
	get_template_part('template', 'header');
}

$content_class = 'col-md-12 col-sm-12';
$layout = tt_getmeta('page_layout')!='' ? tt_getmeta('page_layout') : $smof_data['woo_layout'];
$current_sidebar = tt_getmeta('sidebar')!='' ? tt_getmeta('sidebar') : $smof_data['woo_sidebar'];

if( is_product() ){
	$sproduct = tt_getmeta('product_layout');
	$sproduct = $sproduct!='' && $sproduct!=$smof_data['product_layout'] ? $sproduct : $smof_data['product_layout'];
	$layout = $sproduct;
	$current_sidebar = $smof_data['product_sidebar'];
}


if( in_array($layout, array('left', 'right' )) ){
	$content_class = 'col-md-9';
	$content_class .= $layout=='left' ? ' pull-right' : '';
}


//change to 3 columsn per row when using sidebar
if (!function_exists('loop_columns')) {
	function loop_columns() {
		return 3;
	}
}

$post = $tmp_post;
$wp_query = $temp_query;

?>


<!-- Start Content
================================================== -->
<section class="primary section">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="row">
					<div class="<?php echo $content_class; ?>">
						<div class="content">
							
							<div class="row">
								<div class="col-md-12">
									<?php

									if( in_array($layout, array('left', 'right' )) ){
										add_filter('loop_shop_columns', 'loop_columns');
									}

									woocommerce_content();
									?>
								</div>
							</div>
						</div>
						
					</div>
					<?php
					if( in_array($layout, array('left', 'right' )) ){
						get_sidebar();
					}
					?>
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