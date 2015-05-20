<!DOCTYPE HTML>
<!--[if IE 6]>
<html class="oldie ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html class="oldie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="oldie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    
    <!--[if lt IE 9]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <script src="<?php echo get_template_directory_uri(); ?>/assets/plugins/html5shiv.js"></script>
    <![endif]-->

	<title> <?php wp_title("|",true, 'right'); ?> <?php if (!defined('WPSEO_VERSION')) { bloginfo('name'); } ?></title>
	
	<!-- Favicons -->
	<?php tt_icons(); ?>
   	<?php
   		global $smof_data;

   		$body_classes = $nav_fixed = '';
   		if(get_theme_mod('general-layout') == 'boxed'){ $body_classes .= 'boxed '; }
   		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
   		if(isset($smof_data['fixed_menu']) && $smof_data['fixed_menu'] == 1){ $nav_fixed = 'navbar-fixed-top'; }
   		$header_transparent = isset($smof_data['header_transparent']) && $smof_data['header_transparent']=='1' ? 'header-transparent' : '';
   		$header_opacity = isset($smof_data['header_opacity']) && $header_transparent!='' ? (int)$smof_data['header_opacity'] : 0;
   		$header_opacity = $header_opacity/100;
   		$body_classes .= 'slidemenu-push';
                
   	?>
        
        <meta name="description" content="<?php if ( is_single() && get_post_type(get_the_id()) == WPBDP_POST_TYPE) {
                /**********************TO DO***********************/
                /* Show latest review. If no reviews, show standard description */
                
                $latest_review = BusinessDirectory_RatingsModule::get_reviews_paginated(get_the_id(), 1, 1);
                $num_reviews = BusinessDirectory_RatingsModule::get_total_reviews(get_the_id());
                        
                if(isset($latest_review) && sizeof($latest_review)!=0){
                    $review_text = $latest_review[0]->comment;
                    if(strlen($review_text)>200){
                        $review_text = substr($review_text, 0, 300). "...";
                    }
                    $total_review_string = $num_reviews. ($num_reviews==1? " review": " reviews");
                    echo $total_review_string. " for ". single_post_title("",FALSE)." on Carnaby West: ". $review_text;
                    
                }else{
                    bloginfo('description');
                }
                
               
            } else if (is_archive() || is_category()){
                /* This is the directory categories index page (i.e. "Womens Accessories") */
                
                $term = $wp_query->get_queried_object();
                $term_description = term_description($term->id, WPBDP_CATEGORY_TAX);
                if(isset($term_description) && $term_description != ""){
                    echo $term_description;
                } else {
                    bloginfo('description');
                }
                
            }else if (is_home()){
                
                //This is the results page after filters have been applied
                
                $query_tax = get_query_var("tax_query");
                $qt = $query_tax[0];
                if(isset($qt['taxonomy']) && $qt['taxonomy']==WPBDP_CATEGORY_TAX){
                    $term_names = $qt["terms"];
                    $term = get_term_by("slug", $term_names[0], WPBDP_CATEGORY_TAX);
                    $term_description = term_description($term->term_id, WPBDP_CATEGORY_TAX);
                    if(isset($term_description) && $term_description != ""){
                        echo $term_description;
                    }else{
                        bloginfo('description');
                    }
                } else {
                    bloginfo('description');
                }
                
            } else {
                
                bloginfo('description');
            }
            ?>" 
        />
        
   	<?php wp_head(); ?>

</head>
<body <?php body_class($body_classes); ?>>


	<div class="layout-wrapper">

		<?php
		/* Page Top Slider */
		getPageSlider(true);
		?>

		<div id="header_spacing" class="hidden-xs hidden-sm" style="height: 80px;"></div>
		<!-- Start Header
		================================================== -->
		<header id="header" class="header active-section navbar-inverse <?php echo $nav_fixed.' '.$header_transparent; ?>" role="banner">

			<?php if( isset($smof_data['top_bar']) && $smof_data['top_bar'] == 1 && tt_getmeta('hide_topbar')!='1' && $header_transparent=='' ) : ?>
			<div id="top_bar" class="top-bar">
				<div class="container">
					<div class="row">
						<div class="col-sm-6">
							<div class="top-bar-left">
								<?php tt_bar_content($smof_data['top_bar_left']); ?>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="top-bar-right text-right">
								<?php tt_bar_content($smof_data['top_bar_right']); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php endif; //top bar ?>
			
			<div class="container">
				<div class="row">
					<div class="header-style">
						<div class="hidden-lg hidden-md visible-sm visible-xs">
							<!-- Your Logo -->
							<?php tt_site_logo(); ?>
						</div>
						<!-- Start Navigation -->
						<nav class="main-menu hidden-xs hidden-sm visible-md visible-lg" role="navigation">
							<?php if( isset($smof_data['enable_logo']) && $smof_data['enable_logo']=='0' ): else: ?>
								<!-- Your Logo -->
								<?php tt_site_logo(); ?>
							<?php endif; ?>

							<?php
								$menu_alignment = isset($smof_data['menu_alignment']) && $smof_data['menu_alignment']!='' ? $smof_data['menu_alignment'] : 'right';
							?>
							<div class="navmenu-cell" style="text-align:<?php echo $menu_alignment; ?>;">
								<?php
							  		render_mega_nav();
							  	?>
							</div>
	
						  	<?php 
						  	// Search Box
						  	if( isset($smof_data['search_box']) && $smof_data['search_box'] == 1):
						  		echo '<div class="header-search"><a class="search-icon"><i class="fa fa-search"></i></a>';
						  		get_search_form();
						  		echo '</div>';
						  	endif; ?>
						</nav>
	
						<!-- MOBILE MENU START -->
						<div id="mobile-menu-wrapper" class="visible-xs visible-sm" data-skin="<?php echo isset($smof_data['mobile_menu_dark']) ? $smof_data['mobile_menu_dark'] : '0'; ?>">
							<?php get_mobile_cart_holder(); ?>
							<a class="mobile-menu-icon" href="javascript:;" id="mobile-menu-handler"><i class="fa fa-align-justify"></i></a>
							<div class="mobile-menu-content slidemenu-push">
								<?php wp_nav_menu(array('theme_location' => 'mobile-menu', 
														'fallback_cb' => '',
														'menu_class'=>'list-inline',
														'container_id'=>'mobile-menu',
														'container'=>'nav' )); ?>
							</div>
						</div>
						<!-- MOBILE MENU END -->
	
	
						<!-- WOOCOMMERCE MOBILE CART START -->
						<?php
						if( class_exists( 'woocommerce' ) ):
							ob_start();
			                woocommerce_mini_cart();
			                $mini_cart = ob_get_clean();
						?>
						<div id="mobile-cart-wrapper" class="slidemenu-push">
							<div class="mobile-cart-content">
								<?php echo $mini_cart; ?>
							</div>
							<div class="mobile-cart-tmp">
								<nav id="mobile-cart" class="woocommerce"></nav>
							</div>
						</div>
						<?php endif; ?>
						<!-- WOOCOMMERCE MOBILE CART END -->
					</div>

				</div>
			</div>
		</header>
		<!-- ==================================================
		End Header -->