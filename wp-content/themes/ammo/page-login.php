<?php
	get_header();
?>


<?php
	global $post, $wp_query;

	/* include header
	============================*/
	if( isset($blank_page) && $blank_page ){  }
	else{
		get_template_part('template', 'header');
	}

	$content_class = 'col-md-12 col-sm-12';
	$layout = tt_getmeta('page_layout');

	if( in_array($layout, array('left', 'right' )) ){
		$content_class = 'col-md-9';
		$content_class .= $layout=='left' ? ' pull-right' : '';
	}
?>
<!-- Start Content
================================================== -->
<section class="primary section">
	<div class="container">
		
            <div class="row">
                    <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="form_wrapper" id="form_wrapper">
                                <div class="flip-form registration-form" rel="login-form">

                                        <?php echo do_shortcode("[usersultra_registration]");?>

                                </div>
                                <div class="flip-form login-form active" rel="registration-form">

                                        <?php echo do_shortcode("[usersultra_login]"); ?>

                                </div>    
                            </div>

                    </div>
                    <div>
                        <?php $image_url = site_url("/wp-content/uploads/iStock_000021272175Large-2.jpg") ?>
                        <img src="<?php echo $image_url?>" class="hidden-md hidden-sm hidden-xs"/>
                    </div>
            </div>

        </div>
</section>
<!-- End Content
================================================== -->


<?php
	get_footer();
?>