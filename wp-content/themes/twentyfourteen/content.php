<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php twentyfourteen_post_thumbnail(); ?>

	<header class="entry-header">
		<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && twentyfourteen_categorized_blog() ) : ?>
		<div class="entry-meta">
<!--			<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); ?></span>
-->

		</div>

		<?php
			endif;

			if ( is_single() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
			endif;
		?>
<!--		<div class="entry-meta">
			<?php
				if ( 'post' == get_post_type() )
					twentyfourteen_posted_on();

				if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
			?>

			<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyfourteen' ), __( '1 Comment', 'twentyfourteen' ), __( '% Comments', 'twentyfourteen' ) ); ?></span>
			<?php
				endif;

				edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
			?>
		</div><!-- .entry-meta -->

	</header><!-- .entry-header -->

	<?php if ( is_search() ) : ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
		<?php
			the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ) );
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );

		?>
	</div><!-- .entry-content -->
	<?php endif; ?>

<!--	<?php the_meta( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
-->

<!--displaying the custom tags-->
	<footer class="entry-meta">
		<span class="tag-links">
			/*
                        <?php 
				/*Shipping Info*/
				if ( get_field('free_shipping') ):
					echo 'Free Shipping';
				elseif ( get_field('free_shipping_minimum_order') ):
					echo 'Free Shipping with orders $' . get_field('free_shipping_minimum_amount') . '+ <br/>';
					echo 'Standard shipping: $' . get_field('shipping_cost') ;
				elseif ( get_field('flat_rate_shipping') ):
					echo 'Standard shipping: $' . get_field('shipping_cost') ;
				else:
					echo 'Shipping costs increase with order size';
				endif;
			?>
			<br/>
			<?php
				/*Return Shipping Info*/
				if ( get_field('free_returns') ):
					echo 'Free Returns';
				elseif ( get_field('handles_return_shipping') ):
					echo 'Flat rate return fee $' . get_field('return_shipping_cost');
				else:
					echo 'Buyer handles return shipping';
				endif;				
			?>
			<br/>
			<?php
				/*Price info*/
				echo get_field('price')[0];
				if (count(get_field('price'))>1) : 
					echo ' - ' . end(get_field('price'));
				endif;	
			?>
		</span>
	</footer>


	<?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
</article><!-- #post-## -->