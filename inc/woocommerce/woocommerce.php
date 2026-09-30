<?php
/**
 * WooCommerce and FunnelKit integration loader.
 *
 * Update-safe by design: the theme ships NO WooCommerce template overrides
 * (there is no /woocommerce/ folder). Everything is done with WooCommerce's
 * hooks, filters and CSS, so WooCommerce updates never leave the theme with
 * outdated templates (WooCommerce → Status would list them if there were any).
 *
 * Files:
 *   setup.php              Theme support, image sizes, stylesheet, body class.
 *   hooks.php              Layout wrappers, shop banner and sidebar, breadcrumbs,
 *                          related products, checkout header, override check.
 *   template-functions.php Header cart and account icons, cart count fragment,
 *                          class and package checkout links, front page products.
 *   template-parts/        Theme markup rendered from WooCommerce data (not overrides).
 *   funnelkit.php          FunnelKit funnel steps (loads even without WooCommerce,
 *                          since FunnelKit also builds lead funnels).
 *
 * Adapted from the kamodev/PEN theme's WooCommerce integration.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'C1791_WC_DIR', __DIR__ );

require C1791_WC_DIR . '/funnelkit.php';

if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

require C1791_WC_DIR . '/setup.php';
require C1791_WC_DIR . '/template-functions.php';
require C1791_WC_DIR . '/hooks.php';
