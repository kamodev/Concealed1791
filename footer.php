<?php
/**
 * Site footer.
 *
 * @package Concealed1791
 */

?>
</main>

<?php
// An Elementor Pro Theme Builder footer replaces the theme footer when one applies.
if ( ! ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) ) {
	if ( c1791_is_minimal_header() ) {
		get_template_part( 'template-parts/footer/minimal' );
	} else {
		if ( c1791_enabled( 'footer_cta' ) ) {
			get_template_part( 'template-parts/footer/cta-band' );
		}
		get_template_part( 'template-parts/footer/site-footer' );
	}
}
?>

<?php if ( c1791_enabled( 'back_to_top' ) ) : ?>
	<button type="button" class="ct-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'concealed1791' ); ?>"><?php c1791_icon( 'up' ); ?></button>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
