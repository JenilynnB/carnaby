<?php
	get_header();
?>

<?php
while ( have_posts() ) : the_post();
	include file_require(dirname(__FILE__).'/template-page.php');
endwhile;
?>


<?php
	get_footer();
?>