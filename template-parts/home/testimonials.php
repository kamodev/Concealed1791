<?php
/**
 * Reviews: a rating summary next to star-rated student quotes.
 *
 * @package Concealed1791
 */

if ( ! c1791_mod( 'testimonials_enable' ) ) {
	return;
}
$c1791_query = new WP_Query(
	array(
		'post_type'      => 'c1791_testimonial',
		'posts_per_page' => 3,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);
if ( ! $c1791_query->have_posts() ) {
	return;
}
$c1791_summary = c1791_rating_summary();
$c1791_reviews = c1791_setting( 'reviews_url' );
?>
<section class="ct-section ct-reviews" id="reviews">
	<div class="ct-container">
		<?php c1791_section_head( c1791_mod( 'testimonials_eyebrow' ), c1791_mod( 'testimonials_title' ) ); ?>
		<div class="ct-reviews__grid">
			<div class="ct-reviews__summary">
				<span class="ct-reviews__score"><?php echo esc_html( number_format_i18n( $c1791_summary['average'], 1 ) ); ?></span>
				<?php c1791_stars( $c1791_summary['average'] ); ?>
				<p>
					<?php
					/* translators: %s: number of reviews. */
					echo esc_html( sprintf( _n( 'Based on %s student review', 'Based on %s student reviews', $c1791_summary['count'], 'concealed1791' ), number_format_i18n( $c1791_summary['count'] ) ) );
					?>
				</p>
				<?php if ( $c1791_reviews ) : ?>
					<a class="ct-btn ct-btn--sm ct-btn--light" href="<?php echo esc_url( c1791_link( $c1791_reviews ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Read all reviews', 'concealed1791' ); ?></a>
				<?php endif; ?>
			</div>
			<?php
			while ( $c1791_query->have_posts() ) :
				$c1791_query->the_post();
				$c1791_rating = (int) get_post_meta( get_the_ID(), '_c1791_rating', true );
				$c1791_class  = get_post_meta( get_the_ID(), '_c1791_class_taken', true );
				$c1791_town   = get_post_meta( get_the_ID(), '_c1791_location', true );
				$c1791_source = get_post_meta( get_the_ID(), '_c1791_source', true );
				?>
				<figure class="ct-review">
					<?php c1791_stars( $c1791_rating ? $c1791_rating : 5 ); ?>
					<blockquote class="ct-review__quote"><?php echo wp_kses_post( wpautop( get_the_content() ) ); ?></blockquote>
					<figcaption class="ct-review__who">
						<span class="ct-review__avatar" aria-hidden="true"><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span>
						<span>
							<strong><?php the_title(); ?></strong>
							<?php $c1791_line = implode( ' · ', array_filter( array( $c1791_class, $c1791_town, $c1791_source ) ) ); ?>
							<?php if ( $c1791_line ) : ?>
								<small><?php echo esc_html( $c1791_line ); ?></small>
							<?php endif; ?>
						</span>
					</figcaption>
				</figure>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>
