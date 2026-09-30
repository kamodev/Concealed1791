<?php
/**
 * Latest articles.
 *
 * @package Concealed1791
 */

if ( ! c1791_mod( 'resources_enable' ) ) {
	return;
}
$c1791_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
if ( ! $c1791_query->have_posts() ) {
	return;
}
$c1791_blog = get_option( 'page_for_posts' );
?>
<section class="ct-section ct-section--alt" id="articles">
	<div class="ct-container">
		<?php c1791_section_head( c1791_mod( 'resources_eyebrow' ), c1791_mod( 'resources_title' ), c1791_mod( 'resources_text' ) ); ?>
		<div class="ct-grid ct-grid--3">
			<?php
			while ( $c1791_query->have_posts() ) :
				$c1791_query->the_post();
				get_template_part( 'template-parts/content', 'card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<?php if ( $c1791_blog ) : ?>
			<div class="ct-section-foot">
				<a class="ct-btn ct-btn--outline" href="<?php echo esc_url( get_permalink( $c1791_blog ) ); ?>"><?php esc_html_e( 'All Articles', 'concealed1791' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
