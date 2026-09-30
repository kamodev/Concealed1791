<?php
/**
 * Upcoming classes.
 *
 * @package Concealed1791
 */

if ( ! c1791_mod( 'classes_enable' ) ) {
	return;
}
$c1791_query = c1791_upcoming_classes( max( 1, (int) c1791_mod( 'classes_count' ) ) );
?>
<section class="ct-section ct-section--alt" id="classes">
	<div class="ct-container">
		<div class="ct-section-head ct-section-head--split">
			<div>
				<?php if ( c1791_mod( 'classes_eyebrow' ) ) : ?>
					<span class="ct-eyebrow"><?php echo esc_html( c1791_mod( 'classes_eyebrow' ) ); ?></span>
				<?php endif; ?>
				<h2><?php echo esc_html( c1791_mod( 'classes_title' ) ); ?></h2>
				<?php if ( c1791_mod( 'classes_text' ) ) : ?>
					<p><?php echo esc_html( c1791_mod( 'classes_text' ) ); ?></p>
				<?php endif; ?>
			</div>
			<a class="ct-btn ct-btn--outline" href="<?php echo esc_url( get_post_type_archive_link( 'c1791_class' ) ); ?>"><?php esc_html_e( 'Full Schedule', 'concealed1791' ); ?><?php c1791_icon( 'arrow' ); ?></a>
		</div>

		<?php if ( $c1791_query->have_posts() ) : ?>
			<div class="ct-schedule">
				<?php
				while ( $c1791_query->have_posts() ) :
					$c1791_query->the_post();
					get_template_part( 'template-parts/class-row' );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<div class="ct-callout">
				<h3><?php esc_html_e( 'New dates coming soon', 'concealed1791' ); ?></h3>
				<p><?php esc_html_e( 'Join our list below to hear first, or call to book a private lesson.', 'concealed1791' ); ?></p>
				<?php c1791_phone_link( 'ct-callout__phone' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
