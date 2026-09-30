<?php
/**
 * Call-to-action band above the footer (Theme Settings → Footer).
 *
 * @package Concealed1791
 */

$c1791_title = c1791_setting( 'footer_cta_title' );
$c1791_text  = c1791_setting( 'footer_cta_text' );
$c1791_btn   = c1791_setting( 'footer_cta_btn_text' );
if ( ! $c1791_title && ! $c1791_btn ) {
	return;
}
?>
<section class="ct-cta-band <?php echo esc_attr( c1791_art_class( 'cta' ) ); ?>" aria-label="<?php echo esc_attr( $c1791_title ? $c1791_title : $c1791_btn ); ?>">
	<div class="ct-container ct-cta-band__inner">
		<div class="ct-cta-band__copy">
			<?php if ( $c1791_title ) : ?>
				<h2><?php echo esc_html( $c1791_title ); ?></h2>
			<?php endif; ?>
			<?php if ( $c1791_text ) : ?>
				<p><?php echo esc_html( $c1791_text ); ?></p>
			<?php endif; ?>
		</div>
		<div class="ct-cta-band__actions">
			<?php if ( $c1791_btn ) : ?>
				<a class="ct-btn ct-btn--light" href="<?php echo esc_url( c1791_link( c1791_setting( 'footer_cta_btn_url' ) ) ); ?>"><?php echo esc_html( $c1791_btn ); ?><?php c1791_icon( 'arrow' ); ?></a>
			<?php endif; ?>
			<?php
			if ( c1791_enabled( 'footer_cta_phone' ) ) {
				c1791_phone_link( 'ct-cta-band__phone', __( 'Or call', 'concealed1791' ) );
			}
			?>
		</div>
	</div>
</section>
