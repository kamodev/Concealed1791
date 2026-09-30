<?php
/**
 * Class schedule row.
 *
 * @package Concealed1791
 */

$c1791_c     = c1791_class();
$c1791_start = $c1791_c['start'] ? strtotime( $c1791_c['start'] ) : 0;
$c1791_types = get_the_terms( get_the_ID(), 'c1791_class_type' );
?>
<article <?php post_class( 'ct-class' . ( c1791_is_sold_out( $c1791_c ) ? ' is-sold-out' : '' ) ); ?>>
	<div class="ct-class__date">
		<?php if ( $c1791_start ) : ?>
			<span class="ct-class__month"><?php echo esc_html( date_i18n( 'M', $c1791_start ) ); ?></span>
			<span class="ct-class__day"><?php echo esc_html( date_i18n( 'j', $c1791_start ) ); ?></span>
			<span class="ct-class__dow"><?php echo esc_html( date_i18n( 'D', $c1791_start ) ); ?></span>
		<?php else : ?>
			<span class="ct-class__month"><?php esc_html_e( 'TBA', 'concealed1791' ); ?></span>
		<?php endif; ?>
	</div>
	<div class="ct-class__body">
		<?php if ( $c1791_types && ! is_wp_error( $c1791_types ) ) : ?>
			<span class="ct-class__type"><?php echo esc_html( $c1791_types[0]->name ); ?></span>
		<?php endif; ?>
		<h3 class="ct-class__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="ct-class__meta">
			<?php if ( $c1791_c['end'] && $c1791_c['end'] !== $c1791_c['start'] ) : ?>
				<span><?php c1791_icon( 'calendar' ); ?><?php echo esc_html( c1791_class_dates( $c1791_c ) ); ?></span>
			<?php endif; ?>
			<?php if ( $c1791_c['time'] ) : ?>
				<span><?php c1791_icon( 'clock' ); ?><?php echo esc_html( $c1791_c['time'] ); ?></span>
			<?php endif; ?>
			<?php if ( $c1791_c['location'] ) : ?>
				<span><?php c1791_icon( 'pin' ); ?><?php echo esc_html( $c1791_c['location'] ); ?></span>
			<?php endif; ?>
			<span><?php c1791_level_badge( $c1791_c ); ?></span>
		</div>
	</div>
	<div class="ct-class__action">
		<?php if ( $c1791_c['price'] ) : ?>
			<span class="ct-class__price"><?php echo esc_html( $c1791_c['price'] ); ?></span>
		<?php endif; ?>
		<?php c1791_seats_badge( $c1791_c ); ?>
		<?php c1791_register_button( $c1791_c, 'ct-btn--sm' ); ?>
	</div>
</article>
