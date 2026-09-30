<?php
/**
 * WooCommerce layout hooks.
 *
 * Only WooCommerce's public actions and filters are used here; nothing
 * replaces a WooCommerce template file.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Wrap shop, category and product pages in the theme layout.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

/**
 * Title banner on the shop and product category pages.
 */
function c1791_wc_banner() {
	if ( is_shop() || is_product_taxonomy() ) {
		$description = is_product_taxonomy() ? term_description() : '';
		c1791_page_hero( woocommerce_page_title( false ), $description ? wp_strip_all_tags( $description ) : '', __( 'Shop', 'concealed1791' ) );
	}
}
add_action( 'woocommerce_before_main_content', 'c1791_wc_banner', 5 );

/**
 * The banner already shows the title on the shop and category pages.
 *
 * @param bool $show Whether to show the title.
 * @return bool
 */
function c1791_wc_hide_archive_title( $show ) {
	return ( is_shop() || is_product_taxonomy() ) ? false : $show;
}
add_filter( 'woocommerce_show_page_title', 'c1791_wc_hide_archive_title' );

/**
 * Open the theme layout.
 */
function c1791_wc_wrapper_start() {
	echo '<div class="ct-section ct-store-main"><div class="ct-container ' . esc_attr( c1791_layout_class() ) . '"><div class="ct-layout__main">';
}
add_action( 'woocommerce_before_main_content', 'c1791_wc_wrapper_start', 10 );

/**
 * Close the theme layout, with the shop sidebar when it is on.
 */
function c1791_wc_wrapper_end() {
	echo '</div>';
	get_sidebar();
	echo '</div></div>';
}
add_action( 'woocommerce_after_main_content', 'c1791_wc_wrapper_end', 10 );

/**
 * Breadcrumbs styled like the rest of the site.
 *
 * @param array $args Breadcrumb arguments.
 * @return array
 */
function c1791_wc_breadcrumb_args( $args ) {
	$args['delimiter']   = '<span class="ct-wc-crumb-sep" aria-hidden="true"> / </span>';
	$args['wrap_before'] = '<nav class="woocommerce-breadcrumb ct-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'concealed1791' ) . '">';
	return $args;
}
add_filter( 'woocommerce_breadcrumb_defaults', 'c1791_wc_breadcrumb_args' );

/**
 * The shop banner has its own breadcrumbs, so skip WooCommerce's there.
 */
function c1791_wc_archive_breadcrumbs() {
	if ( is_shop() || is_product_taxonomy() ) {
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
	}
}
add_action( 'template_redirect', 'c1791_wc_archive_breadcrumbs' );

/**
 * Three related products in one row.
 *
 * @param array $args Related products arguments.
 * @return array
 */
function c1791_wc_related_args( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'c1791_wc_related_args' );

/**
 * Upsells in one row of three.
 *
 * @return int
 */
function c1791_wc_upsell_columns() {
	return 3;
}
add_filter( 'woocommerce_upsells_columns', 'c1791_wc_upsell_columns' );

/**
 * Sidebar contexts for store pages: the shop and categories use the "Shop"
 * setting; single products, cart, checkout and account have no sidebar.
 *
 * @param string|null $context Sidebar context.
 * @return string|null
 */
function c1791_wc_sidebar_context( $context ) {
	if ( is_shop() || is_product_taxonomy() ) {
		return 'shop';
	}
	if ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
		return null;
	}
	return $context;
}
add_filter( 'c1791_sidebar_context', 'c1791_wc_sidebar_context' );

/**
 * Distraction-free header on checkout (Theme Settings → Integrations).
 *
 * The order-received page keeps the full header so customers can get back
 * to the site after buying.
 *
 * @param bool $minimal Whether to use the minimal header.
 * @return bool
 */
function c1791_wc_minimal_header( $minimal ) {
	if ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) && 'minimal' === c1791_setting( 'wc_checkout_header' ) ) {
		return true;
	}
	return $minimal;
}
add_filter( 'c1791_minimal_header', 'c1791_wc_minimal_header' );

/**
 * Template overrides the theme ships (should always be empty).
 *
 * @return string[] Relative paths of overrides found.
 */
function c1791_wc_template_overrides() {
	$found = array();
	foreach ( array_unique( array( get_template_directory(), get_stylesheet_directory() ) ) as $dir ) {
		$wc_dir = $dir . '/' . WC()->template_path();
		if ( is_dir( $wc_dir ) ) {
			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $wc_dir, FilesystemIterator::SKIP_DOTS ) );
			foreach ( $files as $file ) {
				if ( 'php' === $file->getExtension() ) {
					$found[] = str_replace( $dir . '/', '', $file->getPathname() );
				}
			}
		}
	}
	return $found;
}
