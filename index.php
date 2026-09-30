<?php
/**
 * Main template: blog, archives and search results.
 *
 * @package Concealed1791
 */

get_header();

if ( is_search() ) {
	/* translators: %s: search query. */
	$c1791_title = sprintf( __( 'Search: %s', 'concealed1791' ), get_search_query() );
	$c1791_sub   = '';
} elseif ( is_archive() ) {
	$c1791_title = wp_strip_all_tags( get_the_archive_title() );
	$c1791_sub   = get_the_archive_description();
} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
	$c1791_blog  = (int) get_option( 'page_for_posts' );
	$c1791_title = get_the_title( $c1791_blog );
	$c1791_sub   = has_excerpt( $c1791_blog ) ? get_the_excerpt( $c1791_blog ) : '';
} else {
	$c1791_title = __( 'Articles', 'concealed1791' );
	$c1791_sub   = __( 'Colorado carry law, training tips and news from our instructors.', 'concealed1791' );
}

c1791_page_hero( $c1791_title, $c1791_sub );
?>
<div class="ct-section">
	<div class="ct-container <?php echo esc_attr( c1791_layout_class() ); ?>">
		<div class="ct-layout__main">
			<?php if ( have_posts() ) : ?>
				<div class="ct-grid ct-grid--<?php echo c1791_get_sidebar() ? '2' : '3'; ?>">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					?>
				</div>
				<?php c1791_pagination(); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content', 'none' ); ?>
			<?php endif; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</div>
<?php
get_footer();
