<?php
/**
 * Sidebar layout for posts, pages, archives and the shop.
 *
 * Each context has its own on/off, side and widget area (Appearance → Theme
 * Settings → Sidebars). Posts and pages can override the default in the
 * "Sidebar" box on their edit screen. Extra widget areas can be added on the
 * same screen and chosen for any context.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sidebar widget areas: id => [ name, description ].
 *
 * The built-in areas plus the "Extra widget areas" from Theme Settings.
 *
 * @return array
 */
function c1791_widget_areas() {
	$areas = array(
		'sidebar-blog' => array(
			'name'        => __( 'Blog Sidebar', 'concealed1791' ),
			'description' => __( 'Shown beside posts, the blog, archives and search results when enabled in Appearance → Theme Settings → Sidebars.', 'concealed1791' ),
		),
		'sidebar-page' => array(
			'name'        => __( 'Page Sidebar', 'concealed1791' ),
			'description' => __( 'Shown beside pages when enabled in Appearance → Theme Settings → Sidebars.', 'concealed1791' ),
		),
	);
	if ( class_exists( 'WooCommerce' ) ) {
		$areas['sidebar-shop'] = array(
			'name'        => __( 'Shop Sidebar', 'concealed1791' ),
			'description' => __( 'Shown beside the shop and product categories when enabled in Appearance → Theme Settings → Sidebars.', 'concealed1791' ),
		);
	}

	foreach ( c1791_custom_sidebar_names() as $name ) {
		$areas[ 'c1791-custom-' . sanitize_title( $name ) ] = array(
			'name'        => $name,
			'description' => __( 'Extra widget area added in Appearance → Theme Settings → Sidebars.', 'concealed1791' ),
		);
	}

	return apply_filters( 'c1791_widget_areas', $areas );
}

/**
 * Names of the extra widget areas from Theme Settings.
 *
 * @return string[]
 */
function c1791_custom_sidebar_names() {
	$names = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) c1791_setting( 'custom_sidebars' ) ) as $line ) {
		$line = trim( wp_strip_all_tags( $line ) );
		if ( '' !== $line && '' !== sanitize_title( $line ) ) {
			$names[ sanitize_title( $line ) ] = $line;
		}
	}
	return array_values( $names );
}

/**
 * Current sidebar context, or null where the theme uses its own layout
 * (front page, classes, instructors, cart/checkout, full-width template).
 *
 * @return string|null
 */
function c1791_sidebar_context() {
	/**
	 * Filter the sidebar context. Return null for no theme sidebar.
	 *
	 * @param string|null $context 'post', 'page', 'archive', 'shop' or null.
	 */
	return apply_filters( 'c1791_sidebar_context', c1791_default_sidebar_context() );
}

/**
 * Sidebar context before integrations adjust it.
 *
 * @return string|null
 */
function c1791_default_sidebar_context() {
	if ( c1791_is_bare_content() || is_front_page() ) {
		return null;
	}
	if ( is_singular( 'post' ) ) {
		return 'post';
	}
	if ( is_page() ) {
		return is_page_template( array( 'page-templates/full-width.php', 'page-templates/faq.php' ) ) ? null : 'page';
	}
	if ( is_post_type_archive( array( 'c1791_class', 'c1791_instructor' ) ) || is_tax( 'c1791_class_type' ) || is_singular( array( 'c1791_class', 'c1791_instructor' ) ) ) {
		return null;
	}
	if ( is_home() || is_archive() || is_search() ) {
		return 'archive';
	}
	return null;
}

/**
 * Sidebar to show on the current view.
 *
 * @return array|null { id, position } or null for no sidebar.
 */
function c1791_get_sidebar() {
	$context = c1791_sidebar_context();
	if ( ! $context ) {
		return null;
	}

	$show     = 'show' === c1791_setting( $context . '_sidebar' );
	$position = 'left' === c1791_setting( $context . '_sidebar_position' ) ? 'left' : 'right';

	if ( is_singular() ) {
		$override = get_post_meta( get_queried_object_id(), '_c1791_sidebar', true );
		if ( 'hide' === $override ) {
			$show = false;
		} elseif ( 'left' === $override || 'right' === $override ) {
			$show     = true;
			$position = $override;
		}
	}

	$area  = c1791_setting( $context . '_sidebar_area' );
	$areas = c1791_widget_areas();
	if ( ! isset( $areas[ $area ] ) ) {
		$defaults = c1791_setting_defaults();
		$area     = isset( $defaults[ $context . '_sidebar_area' ] ) ? $defaults[ $context . '_sidebar_area' ] : 'sidebar-blog';
	}

	if ( ! $show || ! is_active_sidebar( $area ) ) {
		return null;
	}

	return array(
		'id'       => $area,
		'position' => $position,
	);
}

/**
 * Classes for the content/sidebar grid.
 *
 * @return string
 */
function c1791_layout_class() {
	$sidebar = c1791_get_sidebar();
	if ( ! $sidebar ) {
		return 'ct-layout ct-layout--full';
	}
	return 'ct-layout ct-layout--' . $sidebar['position'] . ( c1791_enabled( 'sidebar_sticky' ) ? ' ct-layout--sticky' : '' );
}

/**
 * Body classes for layout state.
 *
 * @param array $classes Body classes.
 * @return array
 */
function c1791_layout_body_class( $classes ) {
	$sidebar   = c1791_get_sidebar();
	$classes[] = $sidebar ? 'has-sidebar sidebar-' . $sidebar['position'] : 'no-sidebar';
	if ( c1791_is_minimal_header() ) {
		$classes[] = 'ct-minimal';
	}
	if ( c1791_enabled( 'header_sticky' ) ) {
		$classes[] = 'ct-sticky-header';
	}
	$classes[] = 'ct-header-' . sanitize_html_class( c1791_setting( 'header_layout' ) );
	$classes[] = 'ct-btn-' . sanitize_html_class( c1791_setting( 'button_shape' ) );
	return $classes;
}
add_filter( 'body_class', 'c1791_layout_body_class' );

/**
 * Per-post "Sidebar" box on posts and pages.
 */
function c1791_add_sidebar_meta_box() {
	add_meta_box( 'c1791-sidebar', __( 'Sidebar', 'concealed1791' ), 'c1791_render_sidebar_meta_box', array( 'post', 'page' ), 'side', 'default' );
}
add_action( 'add_meta_boxes', 'c1791_add_sidebar_meta_box' );

/**
 * Render the "Sidebar" box.
 *
 * @param WP_Post $post Post being edited.
 */
function c1791_render_sidebar_meta_box( $post ) {
	$value   = get_post_meta( $post->ID, '_c1791_sidebar', true );
	$context = 'page' === $post->post_type ? 'page' : 'post';
	$default = 'show' === c1791_setting( $context . '_sidebar' )
		/* translators: %s: left or right. */
		? sprintf( __( 'sidebar on the %s', 'concealed1791' ), 'left' === c1791_setting( $context . '_sidebar_position' ) ? __( 'left', 'concealed1791' ) : __( 'right', 'concealed1791' ) )
		: __( 'no sidebar', 'concealed1791' );

	wp_nonce_field( 'c1791_sidebar_meta', 'c1791_sidebar_nonce' );
	?>
	<p>
		<label class="screen-reader-text" for="c1791-sidebar-choice"><?php esc_html_e( 'Sidebar', 'concealed1791' ); ?></label>
		<select id="c1791-sidebar-choice" name="c1791_sidebar" style="width:100%">
			<option value="" <?php selected( $value, '' ); ?>>
				<?php
				/* translators: %s: the default layout. */
				echo esc_html( sprintf( __( 'Theme default (%s)', 'concealed1791' ), $default ) );
				?>
			</option>
			<option value="right" <?php selected( $value, 'right' ); ?>><?php esc_html_e( 'Sidebar on the right', 'concealed1791' ); ?></option>
			<option value="left" <?php selected( $value, 'left' ); ?>><?php esc_html_e( 'Sidebar on the left', 'concealed1791' ); ?></option>
			<option value="hide" <?php selected( $value, 'hide' ); ?>><?php esc_html_e( 'No sidebar (full width)', 'concealed1791' ); ?></option>
		</select>
	</p>
	<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
		<p class="description">
			<?php
			printf(
				/* translators: 1: settings page link, 2: widgets page link. */
				wp_kses_post( __( 'Change the default in <a href="%1$s">Theme Settings</a>. Add sidebar content in <a href="%2$s">Widgets</a>.', 'concealed1791' ) ),
				esc_url( admin_url( 'themes.php?page=c1791-settings&tab=sidebars' ) ),
				esc_url( admin_url( 'widgets.php' ) )
			);
			?>
		</p>
	<?php endif; ?>
	<?php
}

/**
 * Save the "Sidebar" box.
 *
 * @param int $post_id Post ID.
 */
function c1791_save_sidebar_meta( $post_id ) {
	if ( ! isset( $_POST['c1791_sidebar_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['c1791_sidebar_nonce'] ) ), 'c1791_sidebar_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$value = isset( $_POST['c1791_sidebar'] ) ? sanitize_key( wp_unslash( $_POST['c1791_sidebar'] ) ) : '';
	if ( in_array( $value, array( 'left', 'right', 'hide' ), true ) ) {
		update_post_meta( $post_id, '_c1791_sidebar', $value );
	} else {
		delete_post_meta( $post_id, '_c1791_sidebar' );
	}
}
add_action( 'save_post', 'c1791_save_sidebar_meta' );
