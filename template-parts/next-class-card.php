<?php
/**
 * "Next class" card: the soonest upcoming class with open seats, or a
 * private-lesson prompt when nothing is scheduled.
 *
 * @package Concealed1791
 */

$c1791_post = c1791_next_open_class();
?>
<aside class="ct-offer-card" aria-label="<?php echo esc_attr( c1791_mod( 'hero_card_label' ) ); ?>">
	<?php
	if ( $c1791_post ) :
		$c1791_c     = c1791_class( $c1791_post->ID );
		$c1791_types = get_the_terms( $c1791_post->ID, 'c1791_class_type' );
		$c1791_names = c1791_class_instructor_names( $c1791_c );
		?>
		<div class="ct-offer-card__top">
			<span class="ct-pill ct-pill--accent"><?php echo esc_html( c1791_mod( 'hero_card_label' ) ); ?></span>
			<?php if ( $c1791_types && ! is_wp_error( $c1791_types ) ) : ?>
				<span class="ct-offer-card__type"><?php echo esc_html( $c1791_types[0]->name ); ?></span>
			<?php endif; ?>
		</div>
		<h2 class="ct-offer-card__title"><a href="<?php echo esc_url( get_permalink( $c1791_post ) ); ?>"><?php echo esc_html( get_the_title( $c1791_post ) ); ?></a></h2>
		<ul class="ct-offer-card__meta">
			<li><?php c1791_icon( 'calendar' ); ?><?php echo esc_html( c1791_class_dates( $c1791_c ) ); ?></li>
			<?php if ( $c1791_c['time'] ) : ?>
				<li><?php c1791_icon( 'clock' ); ?><?php echo esc_html( $c1791_c['time'] ); ?></li>
			<?php endif; ?>
			<?php if ( $c1791_c['location'] ) : ?>
				<li><?php c1791_icon( 'pin' ); ?><?php echo esc_html( $c1791_c['location'] ); ?></li>
			<?php endif; ?>
			<?php if ( $c1791_names ) : ?>
				<li><?php c1791_icon( 'user' ); ?><?php echo esc_html( $c1791_names ); ?></li>
			<?php endif; ?>
		</ul>
		<div class="ct-offer-card__buy">
			<?php if ( $c1791_c['price'] ) : ?>
				<div class="ct-price"><?php c1791_price_html( $c1791_c['price'] ); ?></div>
			<?php endif; ?>
			<?php c1791_seats_badge( $c1791_c ); ?>
		</div>
		<?php c1791_seats_meter( $c1791_c ); ?>
		<?php c1791_register_button( $c1791_c, 'ct-btn--block ct-btn--lg', __( 'Reserve My Seat', 'concealed1791' ) ); ?>
		<p class="ct-offer-card__note"><?php c1791_icon( 'lock' ); ?><?php esc_html_e( 'Secure online registration', 'concealed1791' ); ?></p>
		<a class="ct-more" href="<?php echo esc_url( get_post_type_archive_link( 'c1791_class' ) ); ?>"><?php esc_html_e( 'See all upcoming classes', 'concealed1791' ); ?><?php c1791_icon( 'arrow' ); ?></a>
	<?php else : ?>
		<div class="ct-offer-card__top">
			<span class="ct-pill ct-pill--accent"><?php esc_html_e( 'Private lessons', 'concealed1791' ); ?></span>
		</div>
		<h2 class="ct-offer-card__title"><?php esc_html_e( 'Train on your schedule', 'concealed1791' ); ?></h2>
		<p><?php esc_html_e( 'No open class dates right now — book a one-on-one or small-group lesson instead.', 'concealed1791' ); ?></p>
		<a class="ct-btn ct-btn--block" href="<?php echo esc_url( c1791_link( c1791_setting( 'header_cta_url' ) ) ); ?>"><?php esc_html_e( 'Request a Lesson', 'concealed1791' ); ?></a>
		<?php c1791_phone_link( 'ct-offer-card__phone', __( 'Or call', 'concealed1791' ) ); ?>
	<?php endif; ?>
</aside>
