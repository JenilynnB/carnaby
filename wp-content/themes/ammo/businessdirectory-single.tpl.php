<?php
	get_header();

	global $smof_data;
?>

<?php the_post(); ?>
<section class="primary section">
	<div class="container">
		<!--<div class="row">
			<div class="col-md-12">
				<div class="row">
				     -->                               
                                    <?php // Customize the output of this function using the template "businessdirectory-listing.tpl.php"; ?>
                                    <?php echo wpbdp_render_listing(null, 'single'); ?>
                              
                        <!--    </div>
                        </div>
                </div>-->
        </div>
    
</section>

<?php get_footer(); ?>