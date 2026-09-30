<?php
/**
 * Inline SVG icons (24×24, stroke-based).
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icon paths: name => SVG inner markup.
 *
 * @return array
 */
function c1791_icon_paths() {
	return array(
		'menu'      => '<path d="M3 6h18M3 12h18M3 18h18"/>',
		'close'     => '<path d="M6 6l12 12M18 6L6 18"/>',
		'search'    => '<circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>',
		'cart'      => '<path d="M3 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6.2"/><circle cx="10" cy="20" r="1.3"/><circle cx="17" cy="20" r="1.3"/>',
		'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/>',
		'users'     => '<circle cx="9" cy="8" r="3.5"/><path d="M2 20c1-3.5 3.7-5.5 7-5.5s6 2 7 5.5"/><path d="M16 4.5a3.5 3.5 0 0 1 0 7M18 14.5c2 .7 3.3 2.6 4 5.5"/>',
		'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
		'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'pin'       => '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
		'phone'     => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/>',
		'email'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'chevron'   => '<path d="M6 9l6 6 6-6"/>',
		'up'        => '<path d="M12 19V5M6 11l6-6 6 6"/>',
		'check'     => '<path d="M5 12.5l4.5 4.5L19 7.5"/>',
		'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="M8 12.5l2.8 2.8L16.5 9.5"/>',
		'x-circle'  => '<circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/>',
		'star'      => '<path d="M12 3.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9l-5.2 2.7 1-5.8-4.3-4.1 5.9-.9z" fill="currentColor" stroke="none"/>',
		'shield'    => '<path d="M12 3l8 3v6c0 4.5-3.4 8.3-8 9-4.6-.7-8-4.5-8-9V6z"/><path d="M9 12l2 2 4-4"/>',
		'badge'     => '<circle cx="12" cy="9" r="6"/><path d="M8.5 13.9L7 22l5-3 5 3-1.5-8.1"/><path d="M9.8 9l1.5 1.5 3-3"/>',
		'target'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
		'crosshair' => '<circle cx="12" cy="12" r="8"/><path d="M12 2v5M12 17v5M2 12h5M17 12h5"/>',
		'scale'     => '<path d="M12 3v18M7 21h10M5 7h14M5 7l-3 7a3 3 0 0 0 6 0zM19 7l-3 7a3 3 0 0 0 6 0z"/>',
		'home'      => '<path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
		'medical'   => '<rect x="3" y="6" width="18" height="14" rx="2"/><path d="M9 6V4h6v2M12 10v6M9 13h6"/>',
		'heart'     => '<path d="M12 20s-7-4.4-9-9a5 5 0 0 1 9-3 5 5 0 0 1 9 3c-2 4.6-9 9-9 9z"/>',
		'eye'       => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
		'lock'      => '<rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
		'book'      => '<path d="M4 4h6a2 2 0 0 1 2 2v14a2 2 0 0 0-2-2H4zM20 4h-6a2 2 0 0 0-2 2v14a2 2 0 0 1 2-2h6z"/>',
		'certificate' => '<rect x="3" y="4" width="18" height="13" rx="2"/><path d="M7 9h10M7 12h6"/><circle cx="17" cy="17" r="3"/><path d="M15.5 19.5L15 23l2-1 2 1-.5-3.5"/>',
		'quote'     => '<path d="M10 7H6a2 2 0 0 0-2 2v4h5v5H4M20 7h-4a2 2 0 0 0-2 2v4h5v5h-5" />',
		'flag'      => '<path d="M5 21V4M5 4h11l-2 4 2 4H5"/>',
		'bolt'      => '<path d="M13 2L4 14h7l-1 8 9-12h-7z"/>',
		'facebook'  => '<path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v7h4v-7h3l1-4h-4V8z"/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6"/>',
		'youtube'   => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="M10 9l5 3-5 3z"/>',
		'x'         => '<path d="M4 4l16 16M20 4L4 20"/>',
		'tiktok'    => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5M14 3c.5 2.8 2.5 4.5 5 4.8"/>',
		'google'    => '<path d="M20 12.2h-8M20 12.2A8 8 0 1 1 17.7 6.3"/>',
	);
}

/**
 * Return an inline SVG icon.
 *
 * @param string $name Icon name.
 * @return string SVG markup (theme-defined, static).
 */
function c1791_get_icon( $name ) {
	$paths = c1791_icon_paths();
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return '<svg class="ct-icon ct-icon--' . esc_attr( $name ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
}

/**
 * Echo an inline SVG icon.
 *
 * @param string $name Icon name.
 */
function c1791_icon( $name ) {
	echo c1791_get_icon( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme-defined static SVG.
}

/**
 * Icons offered in the Customizer for benefits and trust items.
 *
 * @return array name => label
 */
function c1791_icon_choices() {
	return array(
		'shield'      => __( 'Shield', 'concealed1791' ),
		'badge'       => __( 'Badge (certified)', 'concealed1791' ),
		'certificate' => __( 'Certificate', 'concealed1791' ),
		'target'      => __( 'Target', 'concealed1791' ),
		'crosshair'   => __( 'Crosshair', 'concealed1791' ),
		'scale'       => __( 'Scales (law)', 'concealed1791' ),
		'home'        => __( 'Home', 'concealed1791' ),
		'medical'     => __( 'Medical kit', 'concealed1791' ),
		'heart'       => __( 'Heart', 'concealed1791' ),
		'users'       => __( 'People', 'concealed1791' ),
		'user'        => __( 'Person', 'concealed1791' ),
		'eye'         => __( 'Eye (awareness)', 'concealed1791' ),
		'lock'        => __( 'Lock', 'concealed1791' ),
		'calendar'    => __( 'Calendar', 'concealed1791' ),
		'clock'       => __( 'Clock', 'concealed1791' ),
		'pin'         => __( 'Map pin', 'concealed1791' ),
		'book'        => __( 'Book', 'concealed1791' ),
		'flag'        => __( 'Flag', 'concealed1791' ),
		'bolt'        => __( 'Bolt', 'concealed1791' ),
		'star'        => __( 'Star', 'concealed1791' ),
		'phone'       => __( 'Phone', 'concealed1791' ),
	);
}
