<?php
/**
 * Theme Settings: schema, defaults, getters, sanitization and dynamic CSS.
 *
 * Everything on Appearance → Theme Settings is stored in one option,
 * c1791_settings. Read a value with c1791_setting( 'key' ). A key that was
 * never saved uses its default; a blank color also uses its default.
 *
 * Menu locations are saved to WordPress's own nav_menu_locations theme mod,
 * so Appearance → Menus and the Customizer always agree with this screen.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings tabs: slug => label.
 *
 * @return array
 */
function c1791_settings_tabs() {
	return apply_filters(
		'c1791_settings_tabs',
		array(
			'general'      => __( 'General', 'concealed1791' ),
			'header'       => __( 'Header & Menus', 'concealed1791' ),
			'sidebars'     => __( 'Sidebars', 'concealed1791' ),
			'footer'       => __( 'Footer', 'concealed1791' ),
			'colors'       => __( 'Colors & Style', 'concealed1791' ),
			'integrations' => __( 'Integrations', 'concealed1791' ),
			'tools'        => __( 'Import / Export', 'concealed1791' ),
		)
	);
}

/**
 * Editable colors: key => [ label, default, CSS custom property, help ].
 *
 * The defaults are the "Concealed 1791" palette: flag navy, flag red and
 * brass, on a cool light-gray page.
 *
 * @return array
 */
function c1791_color_fields() {
	return array(
		'color_accent'       => array( __( 'Accent', 'concealed1791' ), '#b31b2c', '--ct-accent', __( 'Main buttons, announcement bar, badges.', 'concealed1791' ) ),
		'color_accent_hover' => array( __( 'Accent hover', 'concealed1791' ), '#8e1422', '--ct-accent-hover', __( 'Buttons when hovered.', 'concealed1791' ) ),
		'color_accent_ink'   => array( __( 'Text on accent', 'concealed1791' ), '#ffffff', '--ct-accent-ink', __( 'Button and announcement text.', 'concealed1791' ) ),
		'color_secondary'    => array( __( 'Secondary', 'concealed1791' ), '#1f3a68', '--ct-secondary', __( 'Links, date blocks, featured package, active filters.', 'concealed1791' ) ),
		'color_highlight'    => array( __( 'Highlight', 'concealed1791' ), '#c9a34e', '--ct-highlight', __( 'Stars, eyebrows and hover color on dark areas.', 'concealed1791' ) ),
		'color_dark'         => array( __( 'Header & footer', 'concealed1791' ), '#0b1628', '--ct-dark', __( 'Header, footer, hero and page banners.', 'concealed1791' ) ),
		'color_dark_2'       => array( __( 'Dark panels', 'concealed1791' ), '#14243d', '--ct-dark-2', __( 'Top bar, dropdowns, image placeholders.', 'concealed1791' ) ),
		'color_bg'           => array( __( 'Page background', 'concealed1791' ), '#f4f6f9', '--ct-bg', '' ),
		'color_surface'      => array( __( 'Cards & panels', 'concealed1791' ), '#ffffff', '--ct-surface', '' ),
		'color_surface_alt'  => array( __( 'Alternate sections', 'concealed1791' ), '#e8edf4', '--ct-surface-alt', __( 'Banded sections and callouts.', 'concealed1791' ) ),
		'color_ink'          => array( __( 'Text', 'concealed1791' ), '#0f172a', '--ct-ink', '' ),
		'color_ink_soft'     => array( __( 'Secondary text', 'concealed1791' ), '#475569', '--ct-ink-soft', __( 'Intros, meta and excerpts.', 'concealed1791' ) ),
		'color_border'       => array( __( 'Borders', 'concealed1791' ), '#d5dde8', '--ct-border', '' ),
	);
}

/**
 * Sidebar contexts: key => label.
 *
 * @return array
 */
function c1791_sidebar_contexts() {
	$contexts = array(
		'post'    => __( 'Single posts', 'concealed1791' ),
		'page'    => __( 'Pages', 'concealed1791' ),
		'archive' => __( 'Blog, archives & search', 'concealed1791' ),
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$contexts['shop'] = __( 'Shop & product categories', 'concealed1791' );
	}
	return apply_filters( 'c1791_sidebar_contexts', $contexts );
}

/**
 * Footer column content types: key => label.
 *
 * @return array
 */
function c1791_footer_column_types() {
	return array(
		'classes'    => __( 'Class types & schedule links', 'concealed1791' ),
		'posts'      => __( 'Latest posts', 'concealed1791' ),
		'menu'       => __( 'A menu', 'concealed1791' ),
		'contact'    => __( 'Contact details', 'concealed1791' ),
		'hours'      => __( 'Hours', 'concealed1791' ),
		'newsletter' => __( 'Newsletter sign-up', 'concealed1791' ),
		'text'       => __( 'Custom text', 'concealed1791' ),
		'widgets'    => __( 'Widgets (Appearance → Widgets)', 'concealed1791' ),
	);
}

/**
 * The settings schema: key => field.
 *
 * Each field has a tab, a type, a default and a label, plus optional
 * 'choices', 'help', 'min' and 'max'. Types: toggle, choice, text, textarea,
 * link, url, email, color, number, id, menu, area.
 *
 * @return array
 */
function c1791_settings_fields() {
	$f = function ( $tab, $type, $default, $label, $extra = array() ) {
		return array_merge(
			array(
				'tab'     => $tab,
				'type'    => $type,
				'default' => $default,
				'label'   => $label,
			),
			$extra
		);
	};

	$on_off = function ( $on, $off ) {
		return array(
			'on'  => $on,
			'off' => $off,
		);
	};

	$fields = array();

	/*
	 * General: business details used by the top bar, header, footer and schema.
	 */
	$fields['phone']       = $f( 'general', 'text', '', __( 'Phone', 'concealed1791' ), array( 'help' => __( 'Shown as a tap-to-call link in the top bar, header, call-to-action band and footer.', 'concealed1791' ) ) );
	$fields['email']       = $f( 'general', 'email', '', __( 'Email', 'concealed1791' ) );
	$fields['address']     = $f( 'general', 'textarea', '', __( 'Address', 'concealed1791' ) );
	$fields['map_url']     = $f( 'general', 'link', '', __( 'Map link', 'concealed1791' ), array( 'help' => __( 'A Google Maps (or other) link for the address.', 'concealed1791' ) ) );
	$fields['hours']       = $f( 'general', 'textarea', '', __( 'Hours', 'concealed1791' ), array( 'help' => __( 'One line per day or range, for example "Sat–Sun: Class days".', 'concealed1791' ) ) );
	$fields['tagline']     = $f( 'general', 'text', __( 'Concealed Carry & Defensive Training', 'concealed1791' ), __( 'Tagline under the site name', 'concealed1791' ), array( 'help' => __( 'Shown next to the shield mark when no logo is set (Appearance → Customize → Site Identity).', 'concealed1791' ) ) );
	$fields['reviews_url'] = $f( 'general', 'link', '', __( 'Reviews link', 'concealed1791' ), array( 'help' => __( 'Your Google, Yelp or Facebook reviews page. Linked from the reviews summary.', 'concealed1791' ) ) );
	foreach ( c1791_social_networks() as $network => $label ) {
		$fields[ 'social_' . $network ] = $f( 'general', 'url', '', $label );
	}

	/*
	 * Header & Menus.
	 */
	$fields['announce_enable']    = $f( 'header', 'toggle', '1', __( 'Show the announcement bar', 'concealed1791' ) );
	$fields['announce_text']      = $f( 'header', 'text', __( 'New CCW certification dates just added — seats are limited.', 'concealed1791' ), __( 'Announcement', 'concealed1791' ) );
	$fields['announce_link']      = $f( 'header', 'link', '/classes/', __( 'Announcement link', 'concealed1791' ) );
	$fields['announce_link_text'] = $f( 'header', 'text', __( 'View schedule', 'concealed1791' ), __( 'Announcement link text', 'concealed1791' ) );

	$fields['topbar_enable']  = $f( 'header', 'toggle', '1', __( 'Show the top bar', 'concealed1791' ) );
	$fields['topbar_text']    = $f( 'header', 'text', __( 'NRA & USCCA certified instructors', 'concealed1791' ), __( 'Top bar message', 'concealed1791' ) );
	$fields['topbar_contact'] = $f( 'header', 'toggle', '1', __( 'Show phone and email in the top bar', 'concealed1791' ) );
	$fields['topbar_social']  = $f( 'header', 'toggle', '1', __( 'Show social icons in the top bar', 'concealed1791' ) );

	$fields['header_layout'] = $f(
		'header',
		'choice',
		'standard',
		__( 'Header layout', 'concealed1791' ),
		array(
			'choices' => array(
				'standard' => __( 'Logo left, menu right', 'concealed1791' ),
				'centered' => __( 'Logo centered, menu below', 'concealed1791' ),
			),
		)
	);
	$fields['header_sticky']   = $f( 'header', 'toggle', '1', __( 'Keep the header visible while scrolling (sticky)', 'concealed1791' ) );
	$fields['header_phone']    = $f( 'header', 'toggle', '1', __( 'Show the phone number in the header', 'concealed1791' ) );
	$fields['header_search']   = $f( 'header', 'toggle', '1', __( 'Show the search button', 'concealed1791' ) );
	$fields['header_cta_text'] = $f( 'header', 'text', __( 'Book a Class', 'concealed1791' ), __( 'Header button text', 'concealed1791' ), array( 'help' => __( 'Leave empty to hide the button.', 'concealed1791' ) ) );
	$fields['header_cta_url']  = $f( 'header', 'link', '/classes/', __( 'Header button link', 'concealed1791' ) );
	$fields['menu_case']       = $f(
		'header',
		'choice',
		'upper',
		__( 'Menu lettering', 'concealed1791' ),
		array(
			'choices' => array(
				'upper'  => __( 'UPPERCASE', 'concealed1791' ),
				'normal' => __( 'As typed', 'concealed1791' ),
			),
		)
	);

	/*
	 * Sidebars: per context on/off, side and widget area.
	 */
	$sidebar_defaults = array(
		'post'    => array( 'show', 'sidebar-blog' ),
		'page'    => array( 'hide', 'sidebar-page' ),
		'archive' => array( 'show', 'sidebar-blog' ),
		'shop'    => array( 'hide', 'sidebar-shop' ),
	);
	foreach ( $sidebar_defaults as $context => $default ) {
		$fields[ $context . '_sidebar' ]          = $f(
			'sidebars',
			'choice',
			$default[0],
			__( 'Sidebar', 'concealed1791' ),
			array(
				'choices' => array(
					'show' => __( 'Show sidebar', 'concealed1791' ),
					'hide' => __( 'No sidebar (full width)', 'concealed1791' ),
				),
			)
		);
		$fields[ $context . '_sidebar_position' ] = $f(
			'sidebars',
			'choice',
			'right',
			__( 'Position', 'concealed1791' ),
			array(
				'choices' => array(
					'right' => __( 'Right', 'concealed1791' ),
					'left'  => __( 'Left', 'concealed1791' ),
				),
			)
		);
		$fields[ $context . '_sidebar_area' ]     = $f( 'sidebars', 'area', $default[1], __( 'Widget area', 'concealed1791' ) );
	}
	$fields['sidebar_width']   = $f(
		'sidebars',
		'choice',
		'standard',
		__( 'Sidebar width', 'concealed1791' ),
		array(
			'choices' => array(
				'narrow'   => __( 'Narrow (280px)', 'concealed1791' ),
				'standard' => __( 'Standard (340px)', 'concealed1791' ),
				'wide'     => __( 'Wide (400px)', 'concealed1791' ),
			),
		)
	);
	$fields['sidebar_sticky']  = $f( 'sidebars', 'toggle', '1', __( 'Keep the sidebar in view while scrolling long pages', 'concealed1791' ) );
	$fields['custom_sidebars'] = $f( 'sidebars', 'textarea', '', __( 'Extra widget areas', 'concealed1791' ), array( 'help' => __( 'One name per line, for example "Class Sidebar". Each becomes a widget area you can fill under Appearance → Widgets and choose above.', 'concealed1791' ) ) );

	/*
	 * Footer.
	 */
	$fields['footer_style']   = $f(
		'footer',
		'choice',
		'dark',
		__( 'Footer background', 'concealed1791' ),
		array(
			'choices' => array(
				'dark'      => __( 'Dark (Header & footer color)', 'concealed1791' ),
				'secondary' => __( 'Secondary color', 'concealed1791' ),
				'light'     => __( 'Light (Alternate sections color)', 'concealed1791' ),
			),
		)
	);
	$fields['footer_brand']   = $f( 'footer', 'toggle', '1', __( 'Show the brand column (logo, about text and social icons)', 'concealed1791' ) );
	$fields['footer_about']   = $f( 'footer', 'textarea', __( 'Concealed carry, home defense and defensive shooting training in Colorado, taught by NRA and USCCA certified instructors.', 'concealed1791' ), __( 'About text', 'concealed1791' ) );
	$fields['footer_columns'] = $f(
		'footer',
		'number',
		3,
		__( 'Footer columns', 'concealed1791' ),
		array(
			'min'  => 0,
			'max'  => 4,
			'help' => __( 'Columns next to the brand column (0–4).', 'concealed1791' ),
		)
	);
	$fields['footer_layout']  = $f(
		'footer',
		'choice',
		'brand-wide',
		__( 'Column widths', 'concealed1791' ),
		array(
			'choices' => array(
				'brand-wide' => __( 'Wider brand column', 'concealed1791' ),
				'equal'      => __( 'All columns equal', 'concealed1791' ),
			),
		)
	);
	$column_defaults = array( 1 => 'classes', 2 => 'posts', 3 => 'contact', 4 => 'newsletter' );
	foreach ( $column_defaults as $i => $type ) {
		/* translators: %d: footer column number. */
		$fields[ "footer_col{$i}_type" ]  = $f( 'footer', 'choice', $type, sprintf( __( 'Column %d shows', 'concealed1791' ), $i ), array( 'choices' => c1791_footer_column_types() ) );
		$fields[ "footer_col{$i}_title" ] = $f( 'footer', 'text', '', __( 'Heading', 'concealed1791' ), array( 'help' => __( 'Leave empty for the default heading.', 'concealed1791' ) ) );
		$fields[ "footer_col{$i}_menu" ]  = $f( 'footer', 'menu', '', __( 'Menu', 'concealed1791' ) );
		$fields[ "footer_col{$i}_text" ]  = $f( 'footer', 'textarea', '', __( 'Text', 'concealed1791' ) );
	}
	$fields['footer_cta']          = $f( 'footer', 'toggle', '1', __( 'Show the call-to-action band above the footer', 'concealed1791' ) );
	$fields['footer_cta_title']    = $f( 'footer', 'text', __( 'Ready to carry with confidence?', 'concealed1791' ), __( 'Call-to-action heading', 'concealed1791' ) );
	$fields['footer_cta_text']     = $f( 'footer', 'textarea', __( 'Reserve your seat in an upcoming class, or book a private lesson on your schedule.', 'concealed1791' ), __( 'Call-to-action text', 'concealed1791' ) );
	$fields['footer_cta_btn_text'] = $f( 'footer', 'text', __( 'See Upcoming Classes', 'concealed1791' ), __( 'Call-to-action button text', 'concealed1791' ) );
	$fields['footer_cta_btn_url']  = $f( 'footer', 'link', '/classes/', __( 'Call-to-action button link', 'concealed1791' ) );
	$fields['footer_cta_phone']    = $f( 'footer', 'toggle', '1', __( 'Show the phone number in the call-to-action band', 'concealed1791' ) );
	$fields['footer_disclaimer']   = $f( 'footer', 'textarea', __( 'Training is for educational purposes only and is not legal advice. Know and follow all federal, state and local firearms laws.', 'concealed1791' ), __( 'Disclaimer', 'concealed1791' ) );
	$fields['footer_copyright']    = $f( 'footer', 'text', '', __( 'Copyright line', 'concealed1791' ), array( 'help' => __( 'Leave empty for "© Year Site name. All rights reserved."', 'concealed1791' ) ) );
	$fields['back_to_top']         = $f( 'footer', 'toggle', '1', __( 'Show a back-to-top button', 'concealed1791' ) );

	/*
	 * Colors & Style.
	 */
	foreach ( c1791_color_fields() as $key => $color ) {
		$fields[ $key ] = $f(
			'colors',
			'color',
			$color[1],
			$color[0],
			array(
				'var'  => $color[2],
				'help' => $color[3],
			)
		);
	}
	$fields['button_shape'] = $f(
		'colors',
		'choice',
		'rounded',
		__( 'Button shape', 'concealed1791' ),
		array(
			'choices' => array(
				'rounded' => __( 'Rounded corners', 'concealed1791' ),
				'pill'    => __( 'Pill', 'concealed1791' ),
				'square'  => __( 'Square', 'concealed1791' ),
				'blade'   => __( 'Blade cut (angled corners)', 'concealed1791' ),
			),
		)
	);
	$fields['heading_case'] = $f(
		'colors',
		'choice',
		'upper',
		__( 'Headings', 'concealed1791' ),
		array(
			'choices' => array(
				'upper'  => __( 'UPPERCASE', 'concealed1791' ),
				'normal' => __( 'As typed', 'concealed1791' ),
			),
		)
	);

	// Background artwork: the American flag and the shooting target.
	$art = array(
		'flag'   => __( 'American flag', 'concealed1791' ),
		'target' => __( 'Target', 'concealed1791' ),
		'plain'  => __( 'Plain color', 'concealed1791' ),
	);
	$fields['bg_hero']         = $f( 'colors', 'choice', 'flag', __( 'Front page hero', 'concealed1791' ), array( 'choices' => array_merge( $art, array( 'image' => __( 'Hero photo (Customize → Home: Hero)', 'concealed1791' ) ) ) ) );
	$fields['bg_banner']       = $f( 'colors', 'choice', 'target', __( 'Page title banners', 'concealed1791' ), array( 'choices' => $art ) );
	$fields['bg_cta']          = $f( 'colors', 'choice', 'target', __( 'Call-to-action band', 'concealed1791' ), array( 'choices' => $art ) );
	$fields['bg_footer']       = $f( 'colors', 'choice', 'plain', __( 'Footer', 'concealed1791' ), array( 'choices' => $art ) );
	$fields['bg_overlay']      = $f(
		'colors',
		'choice',
		'medium',
		__( 'Shade over the artwork', 'concealed1791' ),
		array(
			'choices' => array(
				'light'  => __( 'Light (artwork stands out)', 'concealed1791' ),
				'medium' => __( 'Medium', 'concealed1791' ),
				'strong' => __( 'Strong (easiest to read)', 'concealed1791' ),
			),
			'help'    => __( 'Text sits on top of the artwork; a stronger shade keeps it readable.', 'concealed1791' ),
		)
	);
	$fields['bg_flag_image']   = $f( 'colors', 'image', '', __( 'Flag image', 'concealed1791' ), array( 'help' => __( 'Optional: use your own flag photo instead of the built-in flag.', 'concealed1791' ) ) );
	$fields['bg_target_image'] = $f( 'colors', 'image', '', __( 'Target image', 'concealed1791' ), array( 'help' => __( 'Optional: use your own target photo instead of the built-in target.', 'concealed1791' ) ) );

	/*
	 * Integrations. These only take effect while their plugin is active.
	 */
	$minimal_full = array(
		'minimal' => __( 'Minimal header (logo only, distraction-free)', 'concealed1791' ),
		'full'    => __( 'Full site header', 'concealed1791' ),
	);
	$fields['wc_checkout_header'] = $f( 'integrations', 'choice', 'minimal', __( 'Checkout page header', 'concealed1791' ), array( 'choices' => $minimal_full ) );
	$fields['wc_header_cart']     = $f( 'integrations', 'toggle', '1', __( 'Show the cart icon in the header', 'concealed1791' ) );
	$fields['wc_header_account']  = $f( 'integrations', 'toggle', '1', __( 'Show the account icon in the header', 'concealed1791' ) );
	$fields['wc_direct_checkout'] = $f( 'integrations', 'toggle', '1', __( 'Class and package buttons linked to a product go straight to checkout', 'concealed1791' ) );
	$fields['funnel_header']      = $f( 'integrations', 'choice', 'minimal', __( 'Funnel step header', 'concealed1791' ), array( 'choices' => $minimal_full ) );

	$fields['elementor_default_editor'] = $f(
		'integrations',
		'choice',
		'elementor',
		__( 'Editor for new pages, posts, classes and instructors', 'concealed1791' ),
		array(
			'choices' => array(
				'elementor' => __( 'Elementor', 'concealed1791' ),
				'block'     => __( 'Block editor', 'concealed1791' ),
			),
		)
	);
	$fields['elementor_sync']       = $f( 'integrations', 'choice', 'on', __( 'Elementor global colors and fonts', 'concealed1791' ), array( 'choices' => $on_off( __( 'Keep Elementor global colors and fonts matched to the theme', 'concealed1791' ), __( 'Manage Elementor global colors and fonts separately', 'concealed1791' ) ) ) );
	$fields['elementor_front_page'] = $f( 'integrations', 'choice', 'on', __( 'Front page built with Elementor', 'concealed1791' ), array( 'choices' => $on_off( __( 'Show only the Elementor layout (replaces the theme\'s homepage sections)', 'concealed1791' ), __( 'Show the theme\'s homepage sections with the Elementor layout inside', 'concealed1791' ) ) ) );

	$fields['amelia_match'] = $f( 'integrations', 'choice', 'on', __( 'Booking form style', 'concealed1791' ), array( 'choices' => $on_off( __( 'Style Amelia booking forms with the theme colors and fonts', 'concealed1791' ), __( 'Use Amelia\'s own colors', 'concealed1791' ) ) ) );

	$fields['mailpoet_list']  = $f( 'integrations', 'id', '', __( 'MailPoet list', 'concealed1791' ) );
	$fields['mailpoet_form']  = $f( 'integrations', 'id', '', __( 'MailPoet form', 'concealed1791' ) );
	$fields['mailpoet_match'] = $f( 'integrations', 'choice', 'on', __( 'MailPoet form style', 'concealed1791' ), array( 'choices' => $on_off( __( 'Style MailPoet forms with the theme colors, fonts and buttons', 'concealed1791' ), __( 'Use MailPoet\'s own form styles', 'concealed1791' ) ) ) );

	/**
	 * Filter the Theme Settings schema.
	 *
	 * @param array $fields key => field.
	 */
	return apply_filters( 'c1791_settings_fields', $fields );
}

/**
 * Social networks: key => label.
 *
 * @return array
 */
function c1791_social_networks() {
	return array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'x'         => 'X (Twitter)',
		'tiktok'    => 'TikTok',
		'google'    => __( 'Google Business Profile', 'concealed1791' ),
	);
}

/**
 * Theme defaults for every setting.
 *
 * @return array
 */
function c1791_setting_defaults() {
	return wp_list_pluck( c1791_settings_fields(), 'default' );
}

/**
 * All resolved settings: saved values over defaults.
 *
 * Cached per request, except in the Customizer preview (which filters the
 * option while it runs). The cache resets whenever the option changes.
 *
 * @param bool $reset Clear the cache.
 * @return array
 */
function c1791_settings( $reset = false ) {
	static $cache = null;
	if ( $reset ) {
		$cache = null;
		return array();
	}
	if ( null !== $cache && ! is_customize_preview() ) {
		return $cache;
	}

	$saved    = get_option( 'c1791_settings', array() );
	$saved    = is_array( $saved ) ? $saved : array();
	$fields   = c1791_settings_fields();
	$resolved = array();
	foreach ( $fields as $key => $field ) {
		$value = array_key_exists( $key, $saved ) ? $saved[ $key ] : $field['default'];
		// A blank color means "use the default".
		if ( 'color' === $field['type'] && ! sanitize_hex_color( (string) $value ) ) {
			$value = $field['default'];
		}
		$resolved[ $key ] = $value;
	}

	$cache = $resolved;
	return $resolved;
}

/**
 * Reset the settings cache after the option changes.
 */
function c1791_flush_settings_cache() {
	c1791_settings( true );
}
add_action( 'add_option_c1791_settings', 'c1791_flush_settings_cache' );
add_action( 'update_option_c1791_settings', 'c1791_flush_settings_cache' );
add_action( 'delete_option_c1791_settings', 'c1791_flush_settings_cache' );

/**
 * Resolved value of one setting.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function c1791_setting( $key ) {
	$all = c1791_settings();
	return isset( $all[ $key ] ) ? $all[ $key ] : '';
}

/**
 * Whether a toggle setting is on.
 *
 * @param string $key Setting key.
 * @return bool
 */
function c1791_enabled( $key ) {
	return '1' === (string) c1791_setting( $key );
}

/**
 * Sanitize a link that may be relative ("/classes/"), an anchor ("#faq"),
 * absolute, or a tel:/mailto: link.
 *
 * @param string $value Value.
 * @return string
 */
function c1791_sanitize_link( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	if ( 0 === strpos( $value, '#' ) ) {
		return '#' . sanitize_html_class( substr( $value, 1 ) );
	}
	if ( 0 === strpos( $value, '/' ) ) {
		// Site-relative path; a leading "//" (protocol-relative URL) is collapsed.
		return esc_url_raw( '/' . ltrim( $value, '/' ) );
	}
	$url = esc_url_raw( $value, array( 'http', 'https', 'mailto', 'tel' ) );
	return $url ? $url : '';
}

/**
 * Resolve a stored link to an absolute URL for output.
 *
 * Relative links are resolved against the home URL, so "/classes/" works on
 * a site installed in a subfolder too.
 *
 * @param string $value Stored link.
 * @return string
 */
function c1791_link( $value ) {
	$value = (string) $value;
	if ( '' === $value ) {
		return '';
	}
	if ( 0 === strpos( $value, '#' ) || preg_match( '#^(https?:|mailto:|tel:)#i', $value ) ) {
		return $value;
	}
	$path = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	$path = $path ? rtrim( $path, '/' ) : '';
	if ( $path && 0 === strpos( $value, $path . '/' ) ) {
		$value = substr( $value, strlen( $path ) );
	}
	return home_url( $value );
}

/**
 * Sanitize one value for its field.
 *
 * @param array $field Field definition.
 * @param mixed $value Raw value.
 * @return mixed Sanitized value, or null to drop it (use the default).
 */
function c1791_sanitize_value( $field, $value ) {
	if ( is_array( $value ) || is_object( $value ) ) {
		return null;
	}
	$value = (string) $value;

	switch ( $field['type'] ) {
		case 'toggle':
			return '1' === $value ? '1' : '0';
		case 'choice':
			return array_key_exists( $value, $field['choices'] ) ? $value : null;
		case 'textarea':
			return sanitize_textarea_field( $value );
		case 'link':
			return c1791_sanitize_link( $value );
		case 'url':
		case 'image':
			return esc_url_raw( trim( $value ), array( 'http', 'https' ) );
		case 'email':
			return sanitize_email( $value );
		case 'color':
			$color = sanitize_hex_color( trim( $value ) );
			return $color ? strtolower( $color ) : null;
		case 'number':
			if ( '' === trim( $value ) || ! is_numeric( $value ) ) {
				return null;
			}
			$number = (int) $value;
			if ( isset( $field['min'] ) ) {
				$number = max( (int) $field['min'], $number );
			}
			if ( isset( $field['max'] ) ) {
				$number = min( (int) $field['max'], $number );
			}
			return $number;
		case 'id':
		case 'menu':
			return absint( $value ) ? (string) absint( $value ) : '';
		case 'area':
			$value = sanitize_key( $value );
			return '' === $value ? null : $value;
		default:
			return sanitize_text_field( $value );
	}
}

/**
 * Sanitize the settings option.
 *
 * A Theme Settings tab posts only its own fields plus a "_tab" marker; that
 * save is merged into the stored values of the other tabs. Any other write
 * (Customizer, import, WP-CLI, code) is taken as the complete value.
 *
 * Menu locations posted from the Header & Menus tab ("_menu_locations") are
 * written to the nav_menu_locations theme mod and not stored here.
 *
 * @param mixed $input Raw input.
 * @return array
 */
function c1791_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();

	if ( isset( $input['_menu_locations'] ) && is_array( $input['_menu_locations'] ) && current_user_can( 'edit_theme_options' ) ) {
		c1791_save_menu_locations( $input['_menu_locations'] );
	}

	$merge = isset( $input['_tab'] );
	unset( $input['_tab'], $input['_menu_locations'] );

	$existing = $merge ? get_option( 'c1791_settings', array() ) : array();
	$existing = is_array( $existing ) ? $existing : array();

	$out = array();
	foreach ( c1791_settings_fields() as $key => $field ) {
		if ( array_key_exists( $key, $input ) ) {
			$value = c1791_sanitize_value( $field, $input[ $key ] );
		} elseif ( array_key_exists( $key, $existing ) ) {
			$value = c1791_sanitize_value( $field, $existing[ $key ] );
		} else {
			continue;
		}
		if ( null !== $value ) {
			$out[ $key ] = $value;
		}
	}
	return $out;
}

/**
 * Save menu location assignments.
 *
 * @param array $locations location => menu term ID.
 */
function c1791_save_menu_locations( $locations ) {
	$current = get_theme_mod( 'nav_menu_locations', array() );
	$current = is_array( $current ) ? $current : array();
	foreach ( array_keys( c1791_menu_locations() ) as $location ) {
		if ( ! array_key_exists( $location, $locations ) ) {
			continue;
		}
		$menu_id = absint( $locations[ $location ] );
		if ( $menu_id && is_nav_menu( $menu_id ) ) {
			$current[ $location ] = $menu_id;
		} else {
			unset( $current[ $location ] );
		}
	}
	set_theme_mod( 'nav_menu_locations', $current );
}

/**
 * Register the option.
 */
function c1791_register_settings() {
	register_setting(
		'c1791_settings',
		'c1791_settings',
		array(
			'type'              => 'object',
			'sanitize_callback' => 'c1791_sanitize_settings',
			'default'           => array(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'c1791_register_settings' );
add_action( 'rest_api_init', 'c1791_register_settings' );

/**
 * CSS custom properties and style switches from Theme Settings.
 *
 * @return string
 */
function c1791_dynamic_css() {
	$css = ':root{';
	foreach ( c1791_color_fields() as $key => $field ) {
		$value = sanitize_hex_color( c1791_setting( $key ) );
		if ( $value ) {
			$css .= $field[2] . ':' . $value . ';';
		}
	}

	$radius = array(
		'rounded' => '8px',
		'pill'    => '999px',
		'square'  => '0px',
		'blade'   => '0px',
	);
	$shape  = c1791_setting( 'button_shape' );
	$css   .= '--ct-btn-radius:' . ( isset( $radius[ $shape ] ) ? $radius[ $shape ] : '8px' ) . ';';
	$css   .= '--ct-btn-cut:' . ( 'blade' === $shape ? '10px' : '0px' ) . ';';

	$widths = array(
		'narrow'   => '280px',
		'standard' => '340px',
		'wide'     => '400px',
	);
	$width  = c1791_setting( 'sidebar_width' );
	$css   .= '--ct-sidebar-w:' . ( isset( $widths[ $width ] ) ? $widths[ $width ] : '340px' ) . ';';
	$css   .= '--ct-heading-case:' . ( 'normal' === c1791_setting( 'heading_case' ) ? 'none' : 'uppercase' ) . ';';
	$css   .= '--ct-menu-case:' . ( 'normal' === c1791_setting( 'menu_case' ) ? 'none' : 'uppercase' ) . ';';

	// Background artwork (built-in SVGs unless replaced with an uploaded image).
	// esc_url_raw() strips quotes and angle brackets, so the URL can't end the url("") or <style>.
	$css .= '--ct-art-flag:url("' . esc_url_raw( c1791_art_url( 'flag' ) ) . '");';
	$css .= '--ct-art-target:url("' . esc_url_raw( c1791_art_url( 'target' ) ) . '");';
	$shade = array(
		'light'  => '.55',
		'medium' => '.72',
		'strong' => '.86',
	);
	$level = c1791_setting( 'bg_overlay' );
	$css  .= '--ct-art-shade:' . ( isset( $shade[ $level ] ) ? $shade[ $level ] : '.72' ) . ';';

	return $css . '}';
}

/**
 * URL of a background artwork: the uploaded replacement or the built-in SVG.
 *
 * @param string $art 'flag' or 'target'.
 * @return string
 */
function c1791_art_url( $art ) {
	$custom = c1791_setting( 'bg_' . $art . '_image' );
	if ( $custom ) {
		return $custom;
	}
	return C1791_URI . '/assets/images/' . ( 'flag' === $art ? 'flag' : 'target' ) . '.svg';
}

/**
 * CSS classes that put a background artwork on an area.
 *
 * @param string $area Setting suffix: 'hero', 'banner', 'cta' or 'footer'.
 * @return string Classes such as "ct-art ct-art--flag", or "" for plain.
 */
function c1791_art_class( $area ) {
	$art = c1791_setting( 'bg_' . $area );
	if ( ! in_array( $art, array( 'flag', 'target' ), true ) ) {
		return '';
	}
	return 'ct-art ct-art--' . $art;
}

/**
 * Color presets for the settings screen: slug => [ label, colors ].
 *
 * @return array
 */
function c1791_color_presets() {
	$presets = array(
		'concealed' => array( __( 'Concealed 1791 (default)', 'concealed1791' ), array() ),
		'stealth'   => array(
			__( 'Stealth Black', 'concealed1791' ),
			array(
				'color_accent'       => '#c8102e',
				'color_accent_hover' => '#a00d25',
				'color_accent_ink'   => '#ffffff',
				'color_secondary'    => '#262626',
				'color_highlight'    => '#d4d4d4',
				'color_dark'         => '#0a0a0a',
				'color_dark_2'       => '#1a1a1a',
				'color_bg'           => '#f5f5f5',
				'color_surface'      => '#ffffff',
				'color_surface_alt'  => '#e7e7e7',
				'color_ink'          => '#111111',
				'color_ink_soft'     => '#525252',
				'color_border'       => '#d9d9d9',
			),
		),
		'guardian'  => array(
			__( 'Guardian Blue & Gold', 'concealed1791' ),
			array(
				'color_accent'       => '#e8a317',
				'color_accent_hover' => '#c98a0c',
				'color_accent_ink'   => '#0b1220',
				'color_secondary'    => '#1d4ed8',
				'color_highlight'    => '#f5c451',
				'color_dark'         => '#0b1220',
				'color_dark_2'       => '#16213a',
				'color_bg'           => '#f6f8fc',
				'color_surface'      => '#ffffff',
				'color_surface_alt'  => '#e9eef8',
				'color_ink'          => '#0b1220',
				'color_ink_soft'     => '#4b5563',
				'color_border'       => '#d6deeb',
			),
		),
		'ranger'    => array(
			__( 'Ranger Green', 'concealed1791' ),
			array(
				'color_accent'       => '#b4541a',
				'color_accent_hover' => '#934312',
				'color_accent_ink'   => '#ffffff',
				'color_secondary'    => '#3f5230',
				'color_highlight'    => '#c9b98a',
				'color_dark'         => '#151a12',
				'color_dark_2'       => '#232b1e',
				'color_bg'           => '#f3f4ef',
				'color_surface'      => '#ffffff',
				'color_surface_alt'  => '#e3e7dc',
				'color_ink'          => '#1a1f16',
				'color_ink_soft'     => '#4c5545',
				'color_border'       => '#d2d8c8',
			),
		),
	);

	// The default preset is the theme defaults.
	foreach ( c1791_color_fields() as $key => $field ) {
		$presets['concealed'][1][ $key ] = $field[1];
	}

	return apply_filters( 'c1791_color_presets', $presets );
}
