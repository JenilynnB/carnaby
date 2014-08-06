<div class="col-md-3">
	<?php
	global $post, $woocommerce, $current_sidebar, $content_class;

	$sidebar_class = '';
	if( !empty($content_class) ){
		if( strpos($content_class, 'col-md-9')!==false ){
			if( strpos($content_class, 'pull-right')!==false ){
				$sidebar_class = 'sidebar-left';
			}
			else{
				$sidebar_class = 'sidebar-right';
			}
		}
	}
	?>
	<div class="sidebar <?php echo $sidebar_class; ?>">
		<?php
		if (function_exists('dynamic_sidebar')){
			$posttype = get_post_type($post);
			
			if( isset($current_sidebar) && !empty($current_sidebar) ){
				dynamic_sidebar($current_sidebar);
			}
			else{
				if( function_exists('is_woocommerce') && is_woocommerce() ){
					dynamic_sidebar('woocommerce-sidebar');
				}
				else if( is_page() ){
					dynamic_sidebar('page-sidebar');
				}
				else if( $posttype=='post' && is_single() ){
					dynamic_sidebar('post-sidebar');
				}
				else if( $posttype=='portfolio' ){
					dynamic_sidebar('portfolio-sidebar');
				}
				else if( $posttype = 'wpbdp_listing'){
                                        dynamic_sidebar('businessdirectory-sidebar');
                                }
                                else{
                                        dynamic_sidebar('businessdirectory-sidebar');
					//dynamic_sidebar('blog-sidebar');
				}
			}
			
		}
		?>
	</div>
</div>