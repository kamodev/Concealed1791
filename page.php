<?php
/**
 * Page.
 *
 * @package Concealed1791
 */

get_header();

while ( have_posts() ) :
	the_post();

	// Page-builder layouts and funnel steps render their own full-width content.
	if ( c1791_is_bare_content() ) {
		get_template_part( 'template-parts/content', 'bare' );
		continue;
	}

	c1791_page_hero( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<div class="ct-section">
		<div class="ct-container <?php echo esc_attr( c1791_layout_class() ); ?>">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'ct-layout__main' ); ?>>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
				<?php if ( comments_open() || get_comments_number() ) : ?>
					<div class="entry-content"><?php comments_template(); ?></div>
				<?php endif; ?>
			</article>
			<?php get_sidebar(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
