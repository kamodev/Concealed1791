<?php
/**
 * Elementor integration.
 *
 * - Elementor is the default editor: "Add New" for pages, posts, classes and
 *   instructors opens Elementor (Theme Settings → Integrations can switch
 *   back to the block editor; "Add New (Block Editor)" stays available).
 * - Pages built with Elementor render full width, without the theme banner,
 *   container or sidebar, so the Elementor layout controls the whole page.
 *   A front page built with Elementor replaces the theme's homepage sections
 *   (or sits inside them; Theme Settings → Integrations).
 * - Elementor Pro Theme Builder can replace the header and footer.
 * - The Elementor global kit's colors and fonts follow Theme Settings.
 *
 * Adapted from the kamodev/PEN theme's Elementor integration.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether Elementor is loaded.
 *
 * @return bool
 */
function c1791_elementor_active() {
	return did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' );
}

/**
 * Whether a post was built with Elementor.
 *
 * @param int|null $post_id Post ID (defaults to the queried object).
 * @return bool
 */
function c1791_is_built_with_elementor( $post_id = null ) {
	if ( ! c1791_elementor_active() ) {
		return false;
	}
	$post_id  = $post_id ? $post_id : get_queried_object_id();
	$document = $post_id ? \Elementor\Plugin::$instance->documents->get( $post_id ) : null;
	return $document && $document->is_built_with_elementor();
}

/**
 * Elementor layouts render bare, full-width content.
 *
 * @param bool $bare Whether to render bare content.
 * @return bool
 */
function c1791_elementor_bare_content( $bare ) {
	return c1791_is_built_with_elementor() ? true : $bare;
}
add_filter( 'c1791_bare_content', 'c1791_elementor_bare_content' );

/**
 * A front page built with Elementor shows only its layout (Theme Settings →
 * Integrations → Front page built with Elementor).
 *
 * @param bool $bare Whether the front page shows only its own content.
 * @return bool
 */
function c1791_elementor_front_page( $bare ) {
	if ( 'on' === c1791_setting( 'elementor_front_page' ) && c1791_is_built_with_elementor( (int) get_option( 'page_on_front' ) ) ) {
		return true;
	}
	return $bare;
}
add_filter( 'c1791_front_page_bare', 'c1791_elementor_front_page' );

/**
 * Register header, footer, single and archive locations for Elementor Pro.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Locations manager.
 */
function c1791_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'c1791_elementor_locations' );

/**
 * Load elementor.css when Elementor is active.
 *
 * @param string[] $parts Stylesheet parts.
 * @return string[]
 */
function c1791_elementor_style_part( $parts ) {
	if ( c1791_elementor_active() ) {
		$parts[] = 'elementor';
	}
	return $parts;
}
add_filter( 'c1791_style_parts', 'c1791_elementor_style_part' );

/**
 * Post types Elementor edits.
 *
 * @return string[]
 */
function c1791_elementor_post_types() {
	return (array) get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
}

/**
 * One-time setup: let Elementor edit classes and instructors.
 */
function c1791_elementor_setup() {
	if ( ! c1791_elementor_active() || get_option( 'c1791_elementor_setup' ) ) {
		return;
	}
	$types = array_values( array_unique( array_merge( c1791_elementor_post_types(), array( 'page', 'post', 'c1791_class', 'c1791_instructor' ) ) ) );
	update_option( 'elementor_cpt_support', $types );
	update_option( 'c1791_elementor_setup', 1 );
}
add_action( 'admin_init', 'c1791_elementor_setup' );

/**
 * Whether Elementor is the default editor for new content.
 *
 * @return bool
 */
function c1791_elementor_is_default_editor() {
	return c1791_elementor_active() && 'elementor' === c1791_setting( 'elementor_default_editor' );
}

/**
 * "Add New" opens Elementor.
 *
 * Adding ?c1791-editor=block to post-new.php opens the block editor instead.
 */
function c1791_elementor_redirect_new_post() {
	global $typenow;
	if ( ! c1791_elementor_is_default_editor() || isset( $_GET['c1791-editor'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$type = $typenow ? $typenow : 'post';
	if ( ! in_array( $type, c1791_elementor_post_types(), true ) ) {
		return;
	}
	$type_object = get_post_type_object( $type );
	if ( ! $type_object || ! current_user_can( $type_object->cap->create_posts ) ) {
		return;
	}
	$url = \Elementor\Plugin::$instance->documents->get_create_new_post_url( $type );
	if ( $url ) {
		wp_safe_redirect( $url );
		exit;
	}
}
add_action( 'load-post-new.php', 'c1791_elementor_redirect_new_post' );

/**
 * Keep the block editor reachable: "Add New (Block Editor)" under each menu.
 */
function c1791_elementor_block_editor_menus() {
	if ( ! c1791_elementor_is_default_editor() ) {
		return;
	}
	foreach ( c1791_elementor_post_types() as $type ) {
		$object = get_post_type_object( $type );
		if ( ! $object || ! $object->show_in_menu || ! use_block_editor_for_post_type( $type ) ) {
			continue;
		}
		$parent = 'post' === $type ? 'edit.php' : 'edit.php?post_type=' . $type;
		add_submenu_page(
			$parent,
			__( 'Add New (Block Editor)', 'concealed1791' ),
			__( 'Add New (Block Editor)', 'concealed1791' ),
			$object->cap->create_posts,
			'post-new.php?post_type=' . $type . '&c1791-editor=block'
		);
	}
}
add_action( 'admin_menu', 'c1791_elementor_block_editor_menus', 20 );

/**
 * Elementor kit values derived from Theme Settings.
 *
 * Elementor's system colors are used as: Primary = header & footer,
 * Secondary = secondary, Text = body text, Accent = buttons.
 *
 * @return array Kit settings to merge.
 */
function c1791_elementor_kit_values() {
	$system_colors = array(
		'primary'   => array( __( 'Primary', 'concealed1791' ), c1791_setting( 'color_dark' ) ),
		'secondary' => array( __( 'Secondary', 'concealed1791' ), c1791_setting( 'color_secondary' ) ),
		'text'      => array( __( 'Text', 'concealed1791' ), c1791_setting( 'color_ink' ) ),
		'accent'    => array( __( 'Accent', 'concealed1791' ), c1791_setting( 'color_accent' ) ),
	);
	$colors        = array();
	foreach ( $system_colors as $id => $color ) {
		$colors[] = array(
			'_id'   => $id,
			'title' => $color[0],
			'color' => strtoupper( $color[1] ),
		);
	}

	// The rest of the theme palette, as custom colors with stable IDs.
	$fields = c1791_color_fields();
	$custom = array();
	foreach ( array( 'color_highlight', 'color_dark_2', 'color_bg', 'color_surface', 'color_surface_alt', 'color_ink_soft', 'color_border', 'color_accent_hover' ) as $key ) {
		$custom[] = array(
			'_id'   => 'c1791' . str_replace( array( 'color_', '_' ), '', $key ),
			/* translators: %s: color name. */
			'title' => sprintf( __( 'Theme %s', 'concealed1791' ), $fields[ $key ][0] ),
			'color' => strtoupper( c1791_setting( $key ) ),
		);
	}

	$font = function ( $id, $title, $family, $weight, $transform = '', $spacing = null ) {
		$typo = array(
			'_id'                    => $id,
			'title'                  => $title,
			'typography_typography'  => 'custom',
			'typography_font_family' => $family,
			'typography_font_weight' => $weight,
		);
		if ( $transform ) {
			$typo['typography_text_transform'] = $transform;
		}
		if ( null !== $spacing ) {
			$typo['typography_letter_spacing'] = array(
				'unit' => 'px',
				'size' => $spacing,
			);
		}
		return $typo;
	};
	$case = 'normal' === c1791_setting( 'heading_case' ) ? '' : 'uppercase';

	return array(
		'system_colors'       => $colors,
		'c1791_custom_colors' => $custom,
		'system_typography'   => array(
			$font( 'primary', __( 'Primary', 'concealed1791' ), 'Barlow Condensed', '700', $case ),
			$font( 'secondary', __( 'Secondary', 'concealed1791' ), 'Barlow Condensed', '600', $case ),
			$font( 'text', __( 'Text', 'concealed1791' ), 'Barlow', '400' ),
			$font( 'accent', __( 'Accent', 'concealed1791' ), 'Barlow Condensed', '600', 'uppercase', 1 ),
		),
		'container_width'     => array(
			'unit' => 'px',
			'size' => 1240,
		),
	);
}

/**
 * Copy Theme Settings colors and fonts into the active Elementor kit.
 *
 * Runs in the admin whenever the resolved values change, and only while the
 * sync setting is on. Custom colors the user added in Elementor are kept.
 *
 * @param bool $force Sync even if nothing changed.
 * @return bool Whether the kit was updated.
 */
function c1791_elementor_sync_kit( $force = false ) {
	if ( ! c1791_elementor_active() || 'on' !== c1791_setting( 'elementor_sync' ) ) {
		return false;
	}
	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( ! $kit_id || 'elementor_library' !== get_post_type( $kit_id ) ) {
		return false;
	}

	$values = c1791_elementor_kit_values();
	$hash   = md5( wp_json_encode( $values ) );
	if ( true !== $force && get_option( 'c1791_elementor_kit_hash' ) === $hash ) {
		return false;
	}

	$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
	$settings = is_array( $settings ) ? $settings : array();

	// Keep the kit's own globals from before the first sync, so they can be restored.
	if ( false === get_option( 'c1791_elementor_kit_backup' ) ) {
		$backup = array( 'kit_id' => $kit_id );
		foreach ( c1791_elementor_kit_keys() as $key ) {
			if ( array_key_exists( $key, $settings ) ) {
				$backup[ $key ] = $settings[ $key ];
			}
		}
		add_option( 'c1791_elementor_kit_backup', $backup, '', false );
	}

	$settings['system_colors']     = $values['system_colors'];
	$settings['system_typography'] = $values['system_typography'];
	$settings['container_width']   = $values['container_width'];

	// Replace this theme's custom colors, keep everyone else's.
	$ours = wp_list_pluck( $values['c1791_custom_colors'], '_id' );
	$kept = array_filter(
		isset( $settings['custom_colors'] ) ? (array) $settings['custom_colors'] : array(),
		function ( $color ) use ( $ours ) {
			return ! in_array( isset( $color['_id'] ) ? $color['_id'] : '', $ours, true );
		}
	);
	$settings['custom_colors'] = array_merge( array_values( $kept ), $values['c1791_custom_colors'] );

	update_post_meta( $kit_id, '_elementor_page_settings', $settings );
	update_option( 'c1791_elementor_kit_hash', $hash );

	if ( isset( \Elementor\Plugin::$instance->files_manager ) ) {
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
	return true;
}
add_action( 'admin_init', 'c1791_elementor_sync_kit', 30 );
add_action( 'customize_save_after', 'c1791_elementor_sync_kit' );

/**
 * Kit settings the theme manages.
 *
 * @return string[]
 */
function c1791_elementor_kit_keys() {
	return array( 'system_colors', 'system_typography', 'custom_colors', 'container_width' );
}

/**
 * Put back the kit globals saved before the first sync, and stop syncing.
 */
function c1791_elementor_restore_kit() {
	check_admin_referer( 'c1791_elementor_restore_kit' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to change these settings.', 'concealed1791' ), 403 );
	}

	$backup = get_option( 'c1791_elementor_kit_backup' );
	if ( is_array( $backup ) && ! empty( $backup['kit_id'] ) ) {
		$settings = get_post_meta( $backup['kit_id'], '_elementor_page_settings', true );
		$settings = is_array( $settings ) ? $settings : array();
		foreach ( c1791_elementor_kit_keys() as $key ) {
			if ( array_key_exists( $key, $backup ) ) {
				$settings[ $key ] = $backup[ $key ];
			} else {
				unset( $settings[ $key ] ); // Elementor falls back to its own default.
			}
		}
		update_post_meta( $backup['kit_id'], '_elementor_page_settings', $settings );
		if ( c1791_elementor_active() && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
	}

	// Stop syncing, or the next admin page load would apply the theme values again.
	$stored                   = get_option( 'c1791_settings', array() );
	$stored                   = is_array( $stored ) ? $stored : array();
	$stored['elementor_sync'] = 'off';
	update_option( 'c1791_settings', $stored );
	delete_option( 'c1791_elementor_kit_hash' );
	delete_option( 'c1791_elementor_kit_backup' );

	wp_safe_redirect( admin_url( 'themes.php?page=c1791-settings&tab=integrations&c1791-kit-restored=1' ) );
	exit;
}
add_action( 'admin_post_c1791_elementor_restore_kit', 'c1791_elementor_restore_kit' );
