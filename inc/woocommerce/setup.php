<?php
/**
 * WooCommerce theme support, image sizes and styles.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Declare WooCommerce support.
 *
 * Image widths and grid defaults are declared here instead of hard-coded
 * in templates, so store owners can still change them under
 * Customize → WooCommerce → Product Images / Product Catalog.
 */
function c1791_wc_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'thumbnail_image_width' => 600,
			'single_image_width'    => 900,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 3,
				'min_columns'     => 1,
				'max_columns'     => 4,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'c1791_wc_setup' );

/**
 * Load woocommerce.css after the theme's other parts.
 *
 * @param string[] $parts Stylesheet parts.
 * @return string[]
 */
function c1791_wc_style_part( $parts ) {
	$parts[] = 'woocommerce';
	return $parts;
}
add_filter( 'c1791_style_parts', 'c1791_wc_style_part' );

/**
 * Body class for store pages.
 *
 * @param array $classes Body classes.
 * @return array
 */
function c1791_wc_body_class( $classes ) {
	if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
		$classes[] = 'ct-store';
	}
	return $classes;
}
add_filter( 'body_class', 'c1791_wc_body_class' );
