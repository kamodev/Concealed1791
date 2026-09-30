<?php
/**
 * Instructors.
 *
 * @package Concealed1791
 */

if ( ! c1791_mod( 'instructors_enable' ) ) {
	return;
}
$c1791_query = new WP_Query(
	array(
		'post_type'      => 'c1791_instructor',
		'posts_per_page' => 4,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);
if ( ! $c1791_query->have_posts() ) {
	return;
}
?>
<section class="ct-section ct-section--alt" id="instructors">
	<div class="ct-container">
		<?php c1791_section_head( c1791_mod( 'instructors_eyebrow' ), c1791_mod( 'instructors_title' ), c1791_mod( 'instructors_text' ) ); ?>
		<div class="ct-grid ct-grid--<?php echo (int) min( 4, max( 2, $c1791_query->post_count ) ); ?>">
			<?php
			while ( $c1791_query->have_posts() ) :
				$c1791_query->the_post();
				get_template_part( 'template-parts/instructor-card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<div class="ct-section-foot">
			<a class="ct-btn ct-btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'c1791_instructor' ) ); ?>"><?php esc_html_e( 'Meet All Instructors', 'concealed1791' ); ?></a>
		</div>
	</div>
</section>
