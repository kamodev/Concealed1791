<?php
/**
 * Full site footer: brand column, up to four configurable columns,
 * disclaimer, copyright and bottom menu (Theme Settings → Footer).
 *
 * @package Concealed1791
 */

$c1791_columns = max( 0, min( 4, (int) c1791_setting( 'footer_columns' ) ) );
$c1791_brand   = c1791_enabled( 'footer_brand' );
$c1791_classes = array(
	'ct-footer',
	'ct-footer--' . sanitize_html_class( c1791_setting( 'footer_style' ) ),
	c1791_art_class( 'footer' ),
);
$c1791_grid    = array(
	'ct-container',
	'ct-footer__grid',
	'ct-footer__grid--' . sanitize_html_class( c1791_setting( 'footer_layout' ) ),
	$c1791_brand ? 'has-brand' : '',
	$c1791_brand && ! $c1791_columns ? 'is-brand-only' : '',
);
$c1791_style   = sprintf( '--ct-footer-cols:%d;--ct-footer-total:%d', max( 1, $c1791_columns ), max( 1, $c1791_columns + ( $c1791_brand ? 1 : 0 ) ) );
?>
<footer class="<?php echo esc_attr( implode( ' ', array_filter( $c1791_classes ) ) ); ?>">
	<?php if ( $c1791_brand || $c1791_columns ) : ?>
		<div class="ct-footer__top">
			<div class="<?php echo esc_attr( implode( ' ', array_filter( $c1791_grid ) ) ); ?>" style="<?php echo esc_attr( $c1791_style ); ?>">
				<?php if ( $c1791_brand ) : ?>
					<div class="ct-footer__brand">
						<?php c1791_brand(); ?>
						<?php if ( c1791_setting( 'footer_about' ) ) : ?>
							<p><?php echo esc_html( c1791_setting( 'footer_about' ) ); ?></p>
						<?php endif; ?>
						<?php c1791_social_links(); ?>
					</div>
				<?php endif; ?>

				<?php for ( $c1791_i = 1; $c1791_i <= $c1791_columns; $c1791_i++ ) : ?>
					<div class="ct-footer__col ct-footer__col--<?php echo esc_attr( c1791_setting( "footer_col{$c1791_i}_type" ) ); ?>">
						<?php c1791_footer_column( $c1791_i ); ?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( c1791_setting( 'footer_disclaimer' ) ) : ?>
		<div class="ct-footer__disclaimer">
			<div class="ct-container"><?php echo esc_html( c1791_setting( 'footer_disclaimer' ) ); ?></div>
		</div>
	<?php endif; ?>

	<div class="ct-footer__bottom">
		<div class="ct-container">
			<span>
				<?php
				$c1791_copyright = c1791_setting( 'footer_copyright' );
				if ( $c1791_copyright ) {
					echo esc_html( str_replace( '{year}', gmdate( 'Y' ), $c1791_copyright ) );
				} else {
					echo '&copy; ' . esc_html( gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) ) . '. ' . esc_html__( 'All rights reserved.', 'concealed1791' );
				}
				?>
			</span>
			<?php
			wp_nav_menu(
				array(
					'theme_location'       => 'footer',
					'container'            => 'nav',
					'container_aria_label' => __( 'Legal', 'concealed1791' ),
					'depth'                => 1,
					'fallback_cb'          => false,
				)
			);
			?>
		</div>
	</div>
</footer>
