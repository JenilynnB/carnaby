<?php
    get_header();

    global $smof_data;
    
    if(isset($_GET["cat"])){	$category = $_GET["cat"];	}
?>

<?php the_post(); ?>

		                
        <?php // Customize the output of this function using the template "businessdirectory-listing.tpl.php"; ?>
        <?php echo wpbdp_render_listing(null, 'single', false, $category); ?>
                              
                        

<?php get_footer(); ?>