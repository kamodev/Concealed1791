<?php
/**
 * Front page: hero, certification strip, benefits, upcoming classes, how it
 * works, packages, page content, instructors, reviews, articles, FAQ and
 * newsletter. The WooCommerce integration adds a featured products section.
 * Section content is edited in Customize → Concealed 1791: Home Page.
 *
 * When the front page is built with Elementor (and Theme Settings →
 * Integrations says so), only the Elementor layout is shown.
 *
 * @package Concealed1791
 */

get_header();

/**
 * Filter whether the front page shows only its own content (page builders).
 *
 * @param bool $bare Default false.
 */
if ( is_page() && apply_filters( 'c1791_front_page_bare', false ) ) {
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'bare' );
	endwhile;
} else {
	$c1791_sections = apply_filters(
		'c1791_front_page_sections',
		array( 'hero', 'trust', 'benefits', 'classes', 'steps', 'packages', 'page-content', 'instructors', 'testimonials', 'resources', 'faq', 'newsletter' )
	);

	foreach ( $c1791_sections as $c1791_section ) {
		// Integrations render their own sections (for example the WooCommerce products grid).
		if ( has_action( 'c1791_home_section_' . $c1791_section ) ) {
			do_action( 'c1791_home_section_' . $c1791_section );
		} else {
			get_template_part( 'template-parts/home/' . $c1791_section );
		}
	}
}

get_footer();
