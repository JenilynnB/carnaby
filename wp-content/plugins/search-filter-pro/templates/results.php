<?php
/**
 * Search & Filter Pro 
 *
 * Sample Results Template
 * 
 * @package   Search_Filter
 * @author    Ross Morsali
 * @link      http://www.designsandcode.com/
 * @copyright 2014 Designs & Code
 * 
 * Note: these templates are not full page templates, rather 
 * just an encaspulation of the your results loop which should
 * be inserted in to other pages by using a shortcode
 * 
 * This template is an absolute base example showing you what
 * you can do, for more customisation see the WordPress docs 
 * and using template tags - 
 * 
 * http://codex.wordpress.org/Template_Tags
 *
 */

if ( $query->have_posts() )
{
	?>
	Found <?php echo $query->found_posts; ?> Results<br />
	<?php
	while ($query->have_posts())
	{
		$query->the_post();
		
		?>
		<div>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			
			<p><br /><?php the_excerpt(); ?><p>
			<?php 
				if ( has_post_thumbnail() ) {
					echo '<p>';
					the_post_thumbnail("small");
					echo '</p>';
				}
			?>
			<p><?php the_category(); ?><p>
			<p><?php the_tags(); ?><p>
			<p><small><?php the_date(); ?></small><p>
			
		</div>
		
		<hr />
		<?php
	}
	?>
	Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?><br />
	<a href="#" class="pagi-prev">Previous</a> | <a href="#" class="pagi-next">Next</a>
	<?php
}
else
{
	echo "No Results Found";
}
