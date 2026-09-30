<?php
/**
 * Minimal, distraction-free header: logo only, no menu or announcement bar.
 * Used on checkout and FunnelKit funnel steps (Theme Settings → Integrations).
 *
 * @package Concealed1791
 */

?>
<header class="ct-header ct-header--minimal" id="masthead">
	<div class="ct-container ct-header__inner">
		<?php c1791_brand( false ); ?>
		<?php if ( function_exists( 'is_checkout' ) && is_checkout() ) : ?>
			<span class="ct-header__secure"><?php c1791_icon( 'lock' ); ?><?php esc_html_e( 'Secure checkout', 'concealed1791' ); ?></span>
		<?php else : ?>
			<?php c1791_phone_link( 'ct-header__phone' ); ?>
		<?php endif; ?>
	</div>
</header>
