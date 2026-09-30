<?php
/**
 * Single post.
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

	$c1791_cats = 'post' === get_post_type() ? get_the_category() : array();
	ob_start();
	if ( 'post' === get_post_type() ) {
		c1791_posted_on();
	}
	c1791_page_hero( get_the_title(), '', $c1791_cats ? $c1791_cats[0]->name : '', ob_get_clean() );
	?>
	<div class="ct-section">
		<div class="ct-container <?php echo esc_attr( c1791_layout_class() ); ?>">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'ct-layout__main' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="ct-featured"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>
				<div class="entry-content">
					<?php
					the_content();
					wp_link_pages();
					the_tags( '<p class="entry-tags">' . esc_html__( 'Tags: ', 'concealed1791' ), ', ', '</p>' );
					?>
				</div>
				<div class="entry-content">
					<?php
					the_post_navigation(
						array(
							'prev_text' => '<span>' . esc_html__( 'Previous', 'concealed1791' ) . '</span>%title',
							'next_text' => '<span>' . esc_html__( 'Next', 'concealed1791' ) . '</span>%title',
						)
					);
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>
				</div>
			</article>
			<?php get_sidebar(); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
