<?php
	get_header();
	
	
	
global $smof_data, $layout_sidebar, $current_sidebar;

$prefix = 'archive';
if(is_tax('portfolio_entries'))
	$prefix = 'portfolio';
elseif (is_category())
    $prefix = 'category';
elseif (is_tag())
    $prefix = 'tag';
elseif(is_author())
    $prefix = 'author';


$current_sidebar = isset($smof_data[$prefix.'_sidebar']) ? $smof_data[$prefix.'_sidebar'] : 'blog-sidebar';
$content_class = 'col-md-9';
$layout_sidebar = 'right';
if ($smof_data[$prefix.'_sidebar_type'] == 'left') {
    $layout_sidebar = 'left';
    $content_class .= ' pull-right';
} elseif ($smof_data[$prefix.'_sidebar_type'] == 'full') {
    $layout_sidebar = 'full';
    $content_class = 'col-md-12 col-sm-12';
}

$loop_layout = (isset($smof_data[$prefix.'_layout']) && $smof_data[$prefix.'_layout'] != '') ? $smof_data[$prefix.'_layout'] : 'regular';

?>



<!-- Start Title Section
================================================== -->
<section class="page-title section">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="">
					<?php
					if (is_author()) {
                        echo '<div class="item-author clearfix">';
                        $author_email = get_the_author_meta('email');
                        echo get_avatar($author_email, $size = '60');
                        if(have_posts()) { 
                            the_post();
                            echo '<h3>'.__("Written by ", "themeton") . get_the_author().'</h3>';
                   
                            rewind_posts();
                        }
                        
                        $description = get_the_author_meta('description');
                        echo '<div class="author-title-line"></div><p>';
                        if ($description != '') {
                            echo $description;
                        } else {
                            _e('The author didnt add any information to his profile yet', 'themeton');
                        }
                        echo '</p></div>';
                    } else { ?>
						<h1>
						<?php
						// Title text
						if(is_tax('portfolio_entries'))
							printf(__('Portfolio Entries : %s', 'themeton'), single_cat_title('', false));
	                    elseif (is_category()) {
	                        printf(__('Category : %s', 'themeton'), single_cat_title('', false));
	                    } elseif (is_tag()) {
	                        printf(__('Tag Archives: %s', 'themeton'), single_tag_title('', false));
	                    } elseif (is_archive()) {
	                        if (is_day()) :
	                            printf(__('Daily Archives: %s', 'themeton'), get_the_date());
	                        elseif (is_month()) :
	                            printf(__('Monthly Archives: %s', 'themeton'), get_the_date(_x('F Y', 'monthly archives date format', 'themeton')));
	                        elseif (is_year()) :
	                            printf(__('Yearly Archives: %s', 'themeton'), get_the_date(_x('Y', 'yearly archives date format', 'themeton')));
	                        else :
	                            _e('Archives', 'themeton');
	                        endif;
	                    }
	                    ?>
	                    </h1>
                    <?php
                	} // is_author()

                    // Description
                    if (is_category() && category_description()) : ?>
	                    <?php echo category_description(); ?>
	                <?php elseif (is_tag() && tag_description()) : ?>
	                    <?php echo tag_description(); ?>
	                <?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- ================================================== 
End Title -->

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
		                            if (have_posts()) :
		                            	$columns = 4;
		                            	$tmp = str_replace('grid', '', $loop_layout);
		                            	$tmp = str_replace('masonry', '', $tmp);

		                            	if( $tmp != 'regular') :
		                            		$columns = $tmp;
		                            	endif;
		                            	$loop_args = array(
							                            'overlay' => 'none',
							                            'excerpt' => 'excerpt',
							                            'readmore' => __('Read more', 'themeton'),
							                            'grid' => $columns,
							                            'element_style' => 'default'
						                            );
		                            	$result = '';
		                            	$function = 'blox_loop_regular';
        			                  	if(function_exists('blox_loop_'.$loop_layout))
	                                		$function = 'blox_loop_'.$loop_layout;


		                                while (have_posts()) : the_post();
		                                	ob_start();
		                                	call_user_func($function, $loop_args);
		                                    $result .= ob_get_contents();
		                                    ob_end_clean();
		                                endwhile;

		                                $pager_html = '';
		                                ob_start();
							            themeton_pager();
							            $pager_html .= ob_get_contents();
							            ob_end_clean();


				                      	// Grid container
								        if (strpos($loop_layout, 'grid') !== false) {
								            $result = '<div class="blox-element blog grid-loop">
								                            <div class="row">
								                                <div class="loop-container">'.$result.'</div>
								                            </div>
								                            '. $pager_html .'
								                        </div>';
								        } else if (strpos($loop_layout, 'masonry') !== false) {
								            $result = '<div class="blox-element blog grid-loop">
								                            <div class="row">
								                                <div class="loop-masonry">'.$result.'</div>
								                            </div>
								                            '. $pager_html .'
								                        </div>';
								        }
								        else{
								            $result ='<div class="blox-element blog medium-loop">
								                        <div class="row">
								                            <div class="col-md-12">'.$result.'</div>
								                        </div>
								                        '. $pager_html .'
								                      </div>';
								        }

								        echo $result;
		                            endif;
		                            ?>
								</div>
							</div>
						</div>
					</div>
					<?php
					if( $layout_sidebar != 'full' ) :
						get_sidebar();
					endif;
					?>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- End Content
================================================== -->


<?php get_footer(); ?>