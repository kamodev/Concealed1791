<?php
/**
 * FunnelKit (Funnel Builder) alignment.
 *
 * FunnelKit builds each funnel step as its own post type and renders it with
 * its own templates ("Canvas" or "Boxed") or with a page builder such as
 * Elementor. On those steps the theme stays out of the way:
 *   - no page banner, container or sidebar around the step's content,
 *   - a minimal logo-only header and footer (or the full site header),
 *   - no announcement bar, top bar, menu or call-to-action band.
 *
 * FunnelKit's own checkout styling wins over the theme's WooCommerce form
 * styles on funnel steps (see body.ct-funnel-step in woocommerce.css).
 *
 * Adapted from the kamodev/PEN theme's FunnelKit integration.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether FunnelKit Funnel Builder is active.
 *
 * @return bool
 */
function c1791_funnelkit_active() {
	return defined( 'WFFN_VERSION' ) || class_exists( 'WFFN_Core' );
}

/**
 * FunnelKit post types used for funnel steps.
 *
 * @return string[]
 */
function c1791_funnel_post_types() {
	return apply_filters(
		'c1791_funnel_post_types',
		array(
			'wffn_landing',   // Sales / landing page.
			'wffn_optin',     // Opt-in page.
			'wffn_oty',       // Opt-in confirmation.
			'wfacp_checkout', // Checkout.
			'wfocu_offer',    // One-click upsell / downsell (Pro).
			'wffn_ty',        // Thank-you page.
		)
	);
}

/**
 * Whether the current view is a FunnelKit funnel step.
 *
 * @return bool
 */
function c1791_is_funnel_step() {
	return c1791_funnelkit_active() && is_singular( c1791_funnel_post_types() );
}

/**
 * Funnel steps render their own full-width content.
 *
 * @param bool $bare Whether to render bare content.
 * @return bool
 */
function c1791_funnel_bare_content( $bare ) {
	return c1791_is_funnel_step() ? true : $bare;
}
add_filter( 'c1791_bare_content', 'c1791_funnel_bare_content' );

/**
 * Minimal header and footer on funnel steps (Theme Settings → Integrations).
 *
 * @param bool $minimal Whether to use the minimal header.
 * @return bool
 */
function c1791_funnel_minimal_header( $minimal ) {
	if ( c1791_is_funnel_step() && 'minimal' === c1791_setting( 'funnel_header' ) ) {
		return true;
	}
	return $minimal;
}
add_filter( 'c1791_minimal_header', 'c1791_funnel_minimal_header' );

/**
 * Body class on funnel steps.
 *
 * @param array $classes Body classes.
 * @return array
 */
function c1791_funnel_body_class( $classes ) {
	if ( c1791_is_funnel_step() ) {
		$classes[] = 'ct-funnel-step';
	}
	return $classes;
}
add_filter( 'body_class', 'c1791_funnel_body_class' );
