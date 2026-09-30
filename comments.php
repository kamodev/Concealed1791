<?php
/**
 * Comments.
 *
 * @package Concealed1791
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="ct-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="ct-comments__title">
			<?php
			/* translators: %s: number of comments. */
			echo esc_html( sprintf( _n( '%s comment', '%s comments', get_comments_number(), 'concealed1791' ), number_format_i18n( get_comments_number() ) ) );
			?>
		</h2>
		<ol class="ct-comments__list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="ct-comments__closed"><?php esc_html_e( 'Comments are closed.', 'concealed1791' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>
</section>
