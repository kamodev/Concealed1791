<?php
/**
 * Concealed 1791 theme functions.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'C1791_VERSION', '1.0.0' );
define( 'C1791_DIR', get_template_directory() );
define( 'C1791_URI', get_template_directory_uri() );

require C1791_DIR . '/inc/icons.php';
require C1791_DIR . '/inc/settings.php';
require C1791_DIR . '/inc/post-types.php';
require C1791_DIR . '/inc/meta-boxes.php';
require C1791_DIR . '/inc/layout.php';
require C1791_DIR . '/inc/customizer.php';
require C1791_DIR . '/inc/template-tags.php';

// Plugin integrations. Each file checks that its plugin is active.
require C1791_DIR . '/inc/woocommerce/woocommerce.php';
require C1791_DIR . '/inc/integrations/elementor.php';
require C1791_DIR . '/inc/integrations/amelia.php';
require C1791_DIR . '/inc/integrations/mailpoet.php';

if ( is_admin() ) {
	require C1791_DIR . '/inc/admin/settings-page.php';
}

/**
 * Theme setup.
 */
function c1791_setup() {
	load_theme_textdomain( 'concealed1791', C1791_DIR . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 360,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_image_size( 'c1791-card', 720, 450, true );
	add_image_size( 'c1791-portrait', 600, 720, true );
	add_image_size( 'c1791-hero', 1920, 1080, true );

	register_nav_menus( c1791_menu_locations() );

	add_theme_support(
		'editor-color-palette',
		array(
			array( 'name' => __( 'Accent', 'concealed1791' ), 'slug' => 'accent', 'color' => c1791_setting( 'color_accent' ) ),
			array( 'name' => __( 'Secondary', 'concealed1791' ), 'slug' => 'secondary', 'color' => c1791_setting( 'color_secondary' ) ),
			array( 'name' => __( 'Highlight', 'concealed1791' ), 'slug' => 'highlight', 'color' => c1791_setting( 'color_highlight' ) ),
			array( 'name' => __( 'Dark', 'concealed1791' ), 'slug' => 'dark', 'color' => c1791_setting( 'color_dark' ) ),
			array( 'name' => __( 'Light', 'concealed1791' ), 'slug' => 'light', 'color' => c1791_setting( 'color_surface_alt' ) ),
			array( 'name' => __( 'White', 'concealed1791' ), 'slug' => 'white', 'color' => '#ffffff' ),
		)
	);

	add_editor_style(
		array_merge(
			array( c1791_fonts_url() ),
			array_map(
				function ( $part ) {
					return 'assets/css/' . $part . '.css';
				},
				array( 'tokens', 'base', 'buttons', 'content', 'wordpress' )
			)
		)
	);
}
add_action( 'after_setup_theme', 'c1791_setup' );

/**
 * Menu locations: location => label.
 *
 * @return array
 */
function c1791_menu_locations() {
	return array(
		'primary' => __( 'Main Menu', 'concealed1791' ),
		'mobile'  => __( 'Mobile Menu (optional; the Main Menu is used when empty)', 'concealed1791' ),
		'topbar'  => __( 'Top Bar Menu', 'concealed1791' ),
		'footer'  => __( 'Footer Bottom Menu (legal links)', 'concealed1791' ),
	);
}

/**
 * Content width.
 */
function c1791_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'c1791_content_width', 800 );
}
add_action( 'after_setup_theme', 'c1791_content_width', 0 );

/**
 * Google Fonts URL: Barlow Condensed for headings, Barlow for text.
 *
 * @return string
 */
function c1791_fonts_url() {
	return 'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Barlow:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap';
}

/**
 * Widget areas: the sidebars (including custom ones from Theme Settings)
 * and four footer columns.
 */
function c1791_widgets_init() {
	$shared = array(
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	);

	foreach ( c1791_widget_areas() as $id => $area ) {
		register_sidebar(
			array_merge(
				$shared,
				array(
					'name'        => $area['name'],
					'id'          => $id,
					'description' => $area['description'],
				)
			)
		);
	}

	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar(
			array_merge(
				$shared,
				array(
					/* translators: %d: footer column number. */
					'name'          => sprintf( __( 'Footer Column %d', 'concealed1791' ), $i ),
					'id'            => 'footer-' . $i,
					/* translators: %d: footer column number. */
					'description'   => sprintf( __( 'Shown in footer column %d when that column is set to "Widgets" in Appearance → Theme Settings → Footer.', 'concealed1791' ), $i ),
					'before_title'  => '<h2 class="widget-title ct-footer__heading">',
				)
			)
		);
	}
}
add_action( 'widgets_init', 'c1791_widgets_init' );

/**
 * Stylesheet parts in assets/css/, in load order.
 *
 * @return string[]
 */
function c1791_style_parts() {
	return apply_filters(
		'c1791_style_parts',
		array( 'tokens', 'base', 'buttons', 'header', 'hero', 'sections', 'cards', 'schedule', 'pricing', 'faq', 'content', 'footer', 'wordpress' )
	);
}

/**
 * Scripts and styles.
 */
function c1791_scripts() {
	wp_enqueue_style( 'c1791-fonts', c1791_fonts_url(), array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- Google Fonts URL is versioned by its query.

	// Each part depends on the previous one so they print in order.
	$previous = 'c1791-fonts';
	foreach ( c1791_style_parts() as $part ) {
		$handle = 'c1791-' . $part;
		wp_enqueue_style( $handle, C1791_URI . '/assets/css/' . $part . '.css', array( $previous ), C1791_VERSION );
		$previous = $handle;
	}

	// Colors and style choices from Theme Settings override the tokens.
	wp_add_inline_style( 'c1791-tokens', c1791_dynamic_css() );

	// A child theme's style.css loads after the parent's parts.
	if ( is_child_theme() ) {
		wp_enqueue_style( 'c1791-child', get_stylesheet_uri(), array( $previous ), wp_get_theme()->get( 'Version' ) );
	}

	wp_enqueue_script( 'c1791-main', C1791_URI . '/assets/js/main.js', array(), C1791_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'c1791_scripts' );

/**
 * Theme Settings colors inside the block editor.
 */
function c1791_editor_colors() {
	wp_add_inline_style( 'wp-block-library', str_replace( ':root', '.editor-styles-wrapper', c1791_dynamic_css() ) );
}
add_action( 'enqueue_block_editor_assets', 'c1791_editor_colors' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function c1791_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && wp_style_is( 'c1791-fonts', 'queue' ) ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'c1791_resource_hints', 10, 2 );

/**
 * Create the settings option on activation so the first save is a plain update.
 */
function c1791_activate() {
	add_option( 'c1791_settings', array() );
}
add_action( 'after_switch_theme', 'c1791_activate' );

/**
 * Excerpt length and suffix.
 */
add_filter(
	'excerpt_length',
	function () {
		return 24;
	}
);
add_filter(
	'excerpt_more',
	function () {
		return '&hellip;';
	}
);
