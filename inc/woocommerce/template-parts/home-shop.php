<?php
/**
 * Front page: featured products.
 *
 * Uses WooCommerce's [products] shortcode, so product cards always come from
 * WooCommerce's own current templates. Featured products are shown when there
 * are any; otherwise the most popular.
 *
 * @package Concealed1791
 */

$c1791_featured  = wc_get_featured_product_ids();
$c1791_shortcode = $c1791_featured
	? '[products limit="4" columns="4" visibility="featured"]'
	: '[products limit="4" columns="4" orderby="popularity"]';
$c1791_products  = do_shortcode( $c1791_shortcode );
if ( false === strpos( $c1791_products, 'product' ) ) {
	return;
}
?>
<section class="ct-section" id="shop">
	<div class="ct-container">
		<?php c1791_section_head( c1791_mod( 'shop_eyebrow' ), c1791_mod( 'shop_title' ), c1791_mod( 'shop_text' ) ); ?>
		<?php echo $c1791_products; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WooCommerce shortcode output. ?>
		<div class="ct-section-foot">
			<a class="ct-btn ct-btn--outline" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop All Gear', 'concealed1791' ); ?></a>
		</div>
	</div>
</section>
