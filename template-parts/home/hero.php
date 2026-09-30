<?php
/**
 * Front page hero: badge, headline, lead, two buttons, checklist, review
 * rating, and a live card for the next class with open seats. The background
 * is the American flag, the target, a photo or plain (Theme Settings →
 * Colors & Style).
 *
 * @package Concealed1791
 */

$c1791_bg      = c1791_setting( 'bg_hero' );
$c1791_image   = c1791_mod( 'hero_image' );
$c1791_classes = array( 'ct-hero' );
$c1791_style   = '';
if ( 'image' === $c1791_bg && $c1791_image ) {
	$c1791_classes[] = 'ct-hero--image';
	$c1791_style     = '--ct-hero-image:url("' . esc_url_raw( $c1791_image ) . '")';
} else {
	$c1791_classes[] = c1791_art_class( 'hero' );
}
$c1791_points = c1791_lines( c1791_mod( 'hero_points' ) );
$c1791_rating = c1791_mod( 'hero_rating' ) ? c1791_rating_summary() : array( 'count' => 0 );
$c1791_title  = wp_kses(
	c1791_mod( 'hero_title' ),
	array(
		'em'     => array(),
		'strong' => array(),
		'br'     => array(),
		'span'   => array( 'class' => true ),
	)
);
?>
<section class="<?php echo esc_attr( implode( ' ', array_filter( $c1791_classes ) ) ); ?>"<?php echo $c1791_style ? ' style="' . esc_attr( $c1791_style ) . '"' : ''; ?>>
	<div class="ct-container ct-hero__inner">
		<div class="ct-hero__copy">
			<?php if ( c1791_mod( 'hero_eyebrow' ) ) : ?>
				<span class="ct-hero__badge"><?php c1791_icon( 'badge' ); ?><?php echo esc_html( c1791_mod( 'hero_eyebrow' ) ); ?></span>
			<?php endif; ?>
			<h1 class="ct-hero__title"><?php echo $c1791_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered by wp_kses above. ?></h1>
			<?php if ( c1791_mod( 'hero_text' ) ) : ?>
				<p class="ct-hero__lead"><?php echo esc_html( c1791_mod( 'hero_text' ) ); ?></p>
			<?php endif; ?>

			<div class="ct-button-row">
				<?php if ( c1791_mod( 'hero_btn1_text' ) ) : ?>
					<a class="ct-btn ct-btn--lg" href="<?php echo esc_url( c1791_link( c1791_mod( 'hero_btn1_url' ) ) ); ?>"><?php echo esc_html( c1791_mod( 'hero_btn1_text' ) ); ?><?php c1791_icon( 'arrow' ); ?></a>
				<?php endif; ?>
				<?php if ( c1791_mod( 'hero_btn2_text' ) ) : ?>
					<a class="ct-btn ct-btn--lg ct-btn--ghost" href="<?php echo esc_url( c1791_link( c1791_mod( 'hero_btn2_url' ) ) ); ?>"><?php echo esc_html( c1791_mod( 'hero_btn2_text' ) ); ?></a>
				<?php endif; ?>
			</div>

			<?php if ( $c1791_points ) : ?>
				<ul class="ct-hero__points">
					<?php foreach ( $c1791_points as $c1791_point ) : ?>
						<li><?php c1791_icon( 'check-circle' ); ?><?php echo esc_html( $c1791_point ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $c1791_rating['count'] ) ) : ?>
				<p class="ct-hero__rating">
					<?php c1791_stars( $c1791_rating['average'] ); ?>
					<span>
						<?php
						printf(
							/* translators: 1: average rating, 2: number of reviews. */
							esc_html( _n( '%1$s average from %2$s student review', '%1$s average from %2$s student reviews', $c1791_rating['count'], 'concealed1791' ) ),
							'<strong>' . esc_html( number_format_i18n( $c1791_rating['average'], 1 ) ) . '</strong>',
							esc_html( number_format_i18n( $c1791_rating['count'] ) )
						);
						?>
					</span>
				</p>
			<?php endif; ?>
		</div>

		<?php if ( c1791_mod( 'hero_card' ) ) : ?>
			<div class="ct-hero__aside">
				<?php get_template_part( 'template-parts/next-class-card' ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
