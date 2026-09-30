<?php
/**
 * Minimal footer for checkout and funnel steps: copyright and legal links only.
 *
 * @package Concealed1791
 */

?>
<footer class="ct-footer ct-footer--minimal ct-footer--<?php echo esc_attr( c1791_setting( 'footer_style' ) ); ?>">
	<div class="ct-footer__bottom">
		<div class="ct-container">
			<span>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?></span>
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
