
<div id="comments" class="comments-container">
<?php if ( post_password_required() ) : ?>
	<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'themeton' ); ?></p>
</div>
<?php
		return;
	endif;
?>

<?php // You can start editing here -- including this comment! ?>

<?php if ( have_comments() ) : ?>

	<h2 class="comment-title">
		<?php
			printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'themeton' ),
				number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
		?>
	</h2>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
	<nav id="comment-nav-above">
		<h1 class="assistive-text"><?php _e( 'Comment navigation', 'themeton' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'themeton' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'themeton' ) ); ?></div>
	</nav>
	<?php endif; // check for comment navigation ?>

	<ol class="comment-list">
		<?php
			wp_list_comments( array( 'callback' => 'themeton_theme_comment' ) );
		?>
	</ol>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
	<nav id="comment-nav-below">
		<h1 class="assistive-text"><?php _e( 'Comment navigation', 'themeton' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'themeton' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'themeton' ) ); ?></div>
	</nav>
	<?php endif; // check for comment navigation ?>

	<?php
	if ( ! comments_open() && get_comments_number() ) : ?>
		<div class="alert alert-warning">
			<p class="nocomments"><?php _e( 'Comments are closed.' , 'themeton' ); ?></p>
		</div>
	<?php endif; ?>
<?php endif; // have_comments() ?>

<?php comment_form(); ?>
</div>