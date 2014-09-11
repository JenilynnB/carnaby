<?php
	get_header();

	global $smof_data;
?>

<?php the_post(); ?>

		<!--<div class="row">
			<div class="col-md-12">
				<div class="row">
				     -->                               
                                    <?php // Customize the output of this function using the template "businessdirectory-listing.tpl.php"; ?>
                                    <?php echo wpbdp_render_listing(null, 'single'); ?>
                              
                        <!--    </div>
                        </div>
                </div>-->


<?php get_footer(); ?>