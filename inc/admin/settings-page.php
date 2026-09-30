<?php
/**
 * Appearance → Theme Settings.
 *
 * Tabs: General, Header & Menus, Sidebars, Footer, Colors & Style,
 * Integrations and Import / Export. Fields come from the schema in
 * inc/settings.php; this file renders them and handles the tools.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the admin page.
 */
function c1791_add_settings_page() {
	$GLOBALS['c1791_settings_hook'] = add_theme_page(
		__( 'Theme Settings', 'concealed1791' ),
		__( 'Theme Settings', 'concealed1791' ),
		'edit_theme_options',
		'c1791-settings',
		'c1791_render_settings_page'
	);
}
add_action( 'admin_menu', 'c1791_add_settings_page' );

/**
 * Let anyone who can open the page save it (options.php asks for
 * manage_options by default).
 *
 * @return string
 */
function c1791_settings_capability() {
	return 'edit_theme_options';
}
add_filter( 'option_page_capability_c1791_settings', 'c1791_settings_capability' );

/**
 * Link to the settings page from the Themes screen.
 *
 * @param array $links Action links.
 * @return array
 */
function c1791_settings_action_link( $links ) {
	$links[] = '<a href="' . esc_url( admin_url( 'themes.php?page=c1791-settings' ) ) . '">' . esc_html__( 'Theme Settings', 'concealed1791' ) . '</a>';
	return $links;
}
add_filter( 'theme_action_links_' . get_template(), 'c1791_settings_action_link' );

/**
 * Assets for the settings screen.
 *
 * @param string $hook Current admin page hook.
 */
function c1791_settings_assets( $hook ) {
	if ( empty( $GLOBALS['c1791_settings_hook'] ) || $hook !== $GLOBALS['c1791_settings_hook'] ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'c1791-admin-settings', C1791_URI . '/assets/css/admin/settings.css', array( 'wp-color-picker' ), C1791_VERSION );
	wp_enqueue_script( 'c1791-admin-settings', C1791_URI . '/assets/js/admin-settings.js', array( 'jquery', 'wp-color-picker' ), C1791_VERSION, true );
	wp_localize_script(
		'c1791-admin-settings',
		'c1791Settings',
		array(
			'low'         => __( 'Low contrast: below 4.5:1, hard to read for many people.', 'concealed1791' ),
			'ok'          => __( 'Readable', 'concealed1791' ),
			'chooseImage' => __( 'Choose an image', 'concealed1791' ),
			'useImage'    => __( 'Use this image', 'concealed1791' ),
			'confirmReset' => __( 'Reset all Theme Settings to their defaults? This cannot be undone (export first if you might want them back).', 'concealed1791' ),
			'art'         => array(
				'flag'   => C1791_URI . '/assets/images/flag.svg',
				'target' => C1791_URI . '/assets/images/target.svg',
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'c1791_settings_assets' );

/**
 * The input name for a setting.
 *
 * @param string $key Setting key.
 * @return string
 */
function c1791_field_name( $key ) {
	return 'c1791_settings[' . $key . ']';
}

/**
 * The input ID for a setting.
 *
 * @param string $key Setting key.
 * @return string
 */
function c1791_field_id( $key ) {
	return 'c1791-' . str_replace( '_', '-', $key );
}

/**
 * Current value to show in a field: saved value, else the default. Colors
 * show the saved value only (blank means "use the default").
 *
 * @param string $key Setting key.
 * @return string
 */
function c1791_field_value( $key ) {
	$fields = c1791_settings_fields();
	if ( 'color' === $fields[ $key ]['type'] ) {
		$saved = get_option( 'c1791_settings', array() );
		return is_array( $saved ) && isset( $saved[ $key ] ) ? (string) $saved[ $key ] : '';
	}
	return (string) c1791_setting( $key );
}

/**
 * Render the control for one setting (no label).
 *
 * @param string $key   Setting key.
 * @param array  $extra Optional: 'options' (id => label) for id fields, 'none' label.
 */
function c1791_field_control( $key, $extra = array() ) {
	$fields = c1791_settings_fields();
	if ( ! isset( $fields[ $key ] ) ) {
		return;
	}
	$field = $fields[ $key ];
	$name  = c1791_field_name( $key );
	$id    = c1791_field_id( $key );
	$value = c1791_field_value( $key );

	switch ( $field['type'] ) {
		case 'toggle':
			printf( '<input type="hidden" name="%s" value="0">', esc_attr( $name ) );
			printf(
				'<label class="c1791-toggle" for="%1$s"><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s><span class="c1791-toggle__ui" aria-hidden="true"></span><span>%4$s</span></label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( '1', $value, false ),
				esc_html( $field['label'] )
			);
			break;

		case 'choice':
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $field['choices'] as $choice => $label ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $choice ), selected( $value, (string) $choice, false ), esc_html( $label ) );
			}
			echo '</select>';
			break;

		case 'textarea':
			printf( '<textarea id="%1$s" name="%2$s" rows="3" class="large-text">%3$s</textarea>', esc_attr( $id ), esc_attr( $name ), esc_textarea( $value ) );
			break;

		case 'number':
			printf(
				'<input type="number" id="%1$s" name="%2$s" value="%3$s" class="small-text" min="%4$d" max="%5$d" step="1">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				isset( $field['min'] ) ? (int) $field['min'] : 0,
				isset( $field['max'] ) ? (int) $field['max'] : 99
			);
			break;

		case 'menu':
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			echo '<option value="">' . esc_html__( '— Choose a menu —', 'concealed1791' ) . '</option>';
			foreach ( wp_get_nav_menus() as $menu ) {
				printf( '<option value="%1$d" %2$s>%3$s</option>', (int) $menu->term_id, selected( $value, (string) $menu->term_id, false ), esc_html( $menu->name ) );
			}
			echo '</select>';
			break;

		case 'area':
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( c1791_widget_areas() as $area_id => $area ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $area_id ), selected( $value, $area_id, false ), esc_html( $area['name'] ) );
			}
			echo '</select>';
			break;

		case 'id':
			$options = isset( $extra['options'] ) ? $extra['options'] : array();
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			echo '<option value="" ' . selected( $value, '', false ) . '>' . esc_html( isset( $extra['none'] ) ? $extra['none'] : __( '— None —', 'concealed1791' ) ) . '</option>';
			foreach ( $options as $option_id => $option_name ) {
				printf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $option_id ), selected( $value, (string) $option_id, false ), esc_html( $option_name ) );
			}
			if ( $value && ! isset( $options[ (int) $value ] ) ) {
				/* translators: %s: ID. */
				echo '<option value="' . esc_attr( $value ) . '" selected>' . esc_html( sprintf( __( 'Missing (ID %s): deleted or disabled', 'concealed1791' ), $value ) ) . '</option>';
			}
			echo '</select>';
			break;

		case 'image':
			?>
			<div class="c1791-image" data-c1791-image>
				<input type="url" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="regular-text" placeholder="https://">
				<button type="button" class="button c1791-image__pick"><?php esc_html_e( 'Choose image', 'concealed1791' ); ?></button>
				<button type="button" class="button-link c1791-image__clear"<?php echo $value ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove', 'concealed1791' ); ?></button>
				<span class="c1791-image__preview"<?php echo $value ? ' style="background-image:url(' . esc_url( $value ) . ')"' : ' hidden'; ?>></span>
			</div>
			<?php
			break;

		case 'color':
			$defaults = c1791_setting_defaults();
			printf(
				'<input type="text" class="c1791-color-field" id="%1$s" name="%2$s" value="%3$s" data-key="%4$s" data-var="%5$s" data-default="%6$s">',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				esc_attr( $key ),
				esc_attr( $field['var'] ),
				esc_attr( $defaults[ $key ] )
			);
			break;

		default:
			$types = array(
				'email' => 'email',
				'url'   => 'url',
			);
			printf(
				'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="regular-text"%5$s>',
				esc_attr( isset( $types[ $field['type'] ] ) ? $types[ $field['type'] ] : 'text' ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				'link' === $field['type'] ? ' placeholder="/classes/ or https://…"' : ''
			);
	}

	if ( ! empty( $field['help'] ) && 'color' !== $field['type'] ) {
		echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
	}
}

/**
 * Render a form-table row for one setting.
 *
 * @param string $key   Setting key.
 * @param array  $extra Passed to c1791_field_control().
 */
function c1791_field_row( $key, $extra = array() ) {
	$fields = c1791_settings_fields();
	if ( ! isset( $fields[ $key ] ) ) {
		return;
	}
	$field = $fields[ $key ];
	$attrs = isset( $extra['row'] ) ? $extra['row'] : '';
	echo '<tr' . $attrs . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts by callers.
	if ( 'toggle' === $field['type'] ) {
		echo '<th scope="row"></th><td>';
	} else {
		echo '<th scope="row"><label for="' . esc_attr( c1791_field_id( $key ) ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
	}
	c1791_field_control( $key, $extra );
	echo '</td></tr>';
}

/**
 * A titled group of rows.
 *
 * @param string   $title Group title.
 * @param string[] $keys  Setting keys.
 * @param string   $intro Optional intro text.
 */
function c1791_field_group( $title, $keys, $intro = '' ) {
	echo '<div class="c1791-group"><h2>' . esc_html( $title ) . '</h2>';
	if ( $intro ) {
		echo '<p class="description">' . wp_kses_post( $intro ) . '</p>';
	}
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $keys as $key ) {
		c1791_field_row( $key );
	}
	echo '</tbody></table></div>';
}

/**
 * Appearance → Theme Settings.
 */
function c1791_render_settings_page() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	$tabs = c1791_settings_tabs();
	$tab  = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tab  = isset( $tabs[ $tab ] ) ? $tab : 'general';
	?>
	<div class="wrap c1791-settings">
		<h1 class="c1791-settings__title">
			<span class="c1791-settings__mark" aria-hidden="true">1791</span>
			<?php esc_html_e( 'Theme Settings', 'concealed1791' ); ?>
			<a class="page-title-action" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[panel]=c1791_home' ) ); ?>"><?php esc_html_e( 'Edit Home Page Content', 'concealed1791' ); ?></a>
			<a class="page-title-action" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'View Site', 'concealed1791' ); ?></a>
		</h1>
		<?php settings_errors(); ?>
		<?php c1791_settings_notices(); ?>

		<nav class="nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Settings sections', 'concealed1791' ); ?>">
			<?php foreach ( $tabs as $slug => $label ) : ?>
				<a href="<?php echo esc_url( admin_url( 'themes.php?page=c1791-settings&tab=' . $slug ) ); ?>" class="nav-tab<?php echo $tab === $slug ? ' nav-tab-active' : ''; ?>"<?php echo $tab === $slug ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>

		<?php if ( 'tools' === $tab ) : ?>
			<?php c1791_render_tools_tab(); ?>
		<?php else : ?>
			<form method="post" action="options.php" class="c1791-form">
				<?php
				settings_fields( 'c1791_settings' );
				// Marks this save as one tab's fields, to merge with the other tabs' stored values.
				echo '<input type="hidden" name="c1791_settings[_tab]" value="' . esc_attr( $tab ) . '">';
				$renderer = 'c1791_render_tab_' . $tab;
				if ( function_exists( $renderer ) ) {
					call_user_func( $renderer );
				}
				/**
				 * Add fields to a Theme Settings tab.
				 *
				 * @param string $tab Tab slug.
				 */
				do_action( 'c1791_settings_tab', $tab );
				submit_button();
				?>
			</form>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Notices from the tools and the Elementor kit restore.
 */
function c1791_settings_notices() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display-only flags set by our own redirects.
	$messages = array(
		'c1791-kit-restored' => array( 'success', __( 'Elementor\'s previous colors and fonts are back, and matching is off.', 'concealed1791' ) ),
		'c1791-imported'     => array( 'success', __( 'Settings imported.', 'concealed1791' ) ),
		'c1791-import-error' => array( 'error', __( 'That file or text isn\'t a Concealed 1791 settings export.', 'concealed1791' ) ),
		'c1791-reset'        => array( 'success', __( 'Theme Settings are back to their defaults.', 'concealed1791' ) ),
	);
	foreach ( $messages as $flag => $message ) {
		if ( isset( $_GET[ $flag ] ) ) {
			printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>', esc_attr( $message[0] ), esc_html( $message[1] ) );
		}
	}
	// phpcs:enable
}

/**
 * General tab: business details and social profiles.
 */
function c1791_render_tab_general() {
	c1791_field_group(
		__( 'Business details', 'concealed1791' ),
		array( 'phone', 'email', 'address', 'map_url', 'hours', 'tagline', 'reviews_url' ),
		__( 'Used in the top bar, header, call-to-action band, footer and search-engine data. Leave any of them empty to hide it.', 'concealed1791' )
	);
	c1791_field_group(
		__( 'Social profiles', 'concealed1791' ),
		array_map(
			function ( $network ) {
				return 'social_' . $network;
			},
			array_keys( c1791_social_networks() )
		),
		__( 'Icons appear in the top bar and the footer brand column for the profiles you fill in.', 'concealed1791' )
	);
}

/**
 * Header & Menus tab.
 */
function c1791_render_tab_header() {
	c1791_field_group( __( 'Announcement bar', 'concealed1791' ), array( 'announce_enable', 'announce_text', 'announce_link', 'announce_link_text' ), __( 'A dismissible promo line at the very top of every page.', 'concealed1791' ) );
	c1791_field_group( __( 'Top bar', 'concealed1791' ), array( 'topbar_enable', 'topbar_text', 'topbar_contact', 'topbar_social' ), __( 'A slim bar above the header with a short message, contact details, the Top Bar Menu and social icons.', 'concealed1791' ) );
	c1791_field_group( __( 'Header', 'concealed1791' ), array( 'header_layout', 'header_sticky', 'header_phone', 'header_search', 'header_cta_text', 'header_cta_url', 'menu_case' ) );

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$menus     = wp_get_nav_menus();
	?>
	<div class="c1791-group">
		<h2><?php esc_html_e( 'Menus', 'concealed1791' ); ?></h2>
		<p class="description">
			<?php
			printf(
				/* translators: 1: menus screen URL, 2: new menu URL. */
				wp_kses_post( __( 'Choose which menu shows in each place. Build and reorder menus under <a href="%1$s">Appearance → Menus</a>, or <a href="%2$s">create a new menu</a>. The Main Menu supports drop-downs.', 'concealed1791' ) ),
				esc_url( admin_url( 'nav-menus.php' ) ),
				esc_url( admin_url( 'nav-menus.php?action=edit&menu=0' ) )
			);
			?>
		</p>
		<table class="widefat striped c1791-menus">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Location', 'concealed1791' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Menu', 'concealed1791' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Items', 'concealed1791' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( c1791_menu_locations() as $location => $label ) : ?>
				<?php
				$current = isset( $locations[ $location ] ) ? (int) $locations[ $location ] : 0;
				$id      = 'c1791-menu-' . $location;
				?>
				<tr>
					<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label></th>
					<td>
						<select id="<?php echo esc_attr( $id ); ?>" name="c1791_settings[_menu_locations][<?php echo esc_attr( $location ); ?>]">
							<option value="0"><?php esc_html_e( '— None —', 'concealed1791' ); ?></option>
							<?php foreach ( $menus as $menu ) : ?>
								<option value="<?php echo (int) $menu->term_id; ?>" <?php selected( $current, (int) $menu->term_id ); ?>><?php echo esc_html( $menu->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<?php if ( $current && is_nav_menu( $current ) ) : ?>
							<?php $count = count( (array) wp_get_nav_menu_items( $current ) ); ?>
							<?php
							/* translators: %d: number of menu items. */
							echo esc_html( sprintf( _n( '%d item', '%d items', $count, 'concealed1791' ), $count ) );
							?>
							· <a href="<?php echo esc_url( admin_url( 'nav-menus.php?action=edit&menu=' . $current ) ); ?>"><?php esc_html_e( 'Edit', 'concealed1791' ); ?></a>
						<?php elseif ( 'primary' === $location ) : ?>
							<?php esc_html_e( 'Not set: an automatic menu of classes, instructors and key pages is shown.', 'concealed1791' ); ?>
						<?php else : ?>
							&mdash;
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php if ( ! $menus ) : ?>
			<p class="c1791-check is-warn"><?php esc_html_e( 'You have no menus yet. Create one under Appearance → Menus, then choose it here.', 'concealed1791' ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Sidebars tab.
 */
function c1791_render_tab_sidebars() {
	$areas = c1791_widget_areas();
	?>
	<div class="c1791-group">
		<h2><?php esc_html_e( 'Where sidebars appear', 'concealed1791' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Each kind of page has its own sidebar setting. A sidebar only shows when its widget area has widgets. Single posts and pages can override this in the "Sidebar" box on their edit screen. The front page, class schedule, instructors, cart and checkout always use their own layouts.', 'concealed1791' ); ?></p>
		<table class="widefat c1791-sidebars">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Pages', 'concealed1791' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Sidebar', 'concealed1791' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Position', 'concealed1791' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Widget area', 'concealed1791' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( c1791_sidebar_contexts() as $context => $label ) : ?>
				<?php $area = c1791_setting( $context . '_sidebar_area' ); ?>
				<tr data-c1791-sidebar-row>
					<th scope="row"><?php echo esc_html( $label ); ?></th>
					<td><label class="screen-reader-text" for="<?php echo esc_attr( c1791_field_id( $context . '_sidebar' ) ); ?>"><?php esc_html_e( 'Sidebar', 'concealed1791' ); ?></label><?php c1791_field_control( $context . '_sidebar' ); ?></td>
					<td><label class="screen-reader-text" for="<?php echo esc_attr( c1791_field_id( $context . '_sidebar_position' ) ); ?>"><?php esc_html_e( 'Position', 'concealed1791' ); ?></label><?php c1791_field_control( $context . '_sidebar_position' ); ?></td>
					<td>
						<label class="screen-reader-text" for="<?php echo esc_attr( c1791_field_id( $context . '_sidebar_area' ) ); ?>"><?php esc_html_e( 'Widget area', 'concealed1791' ); ?></label>
						<?php c1791_field_control( $context . '_sidebar_area' ); ?>
						<?php if ( isset( $areas[ $area ] ) && ! is_active_sidebar( $area ) ) : ?>
							<p class="description c1791-warn"><?php esc_html_e( 'This area has no widgets yet, so no sidebar shows until you add some.', 'concealed1791' ); ?></p>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<?php c1791_field_group( __( 'Sidebar style', 'concealed1791' ), array( 'sidebar_width', 'sidebar_sticky' ) ); ?>

	<div class="c1791-group">
		<h2><?php esc_html_e( 'Widget areas', 'concealed1791' ); ?></h2>
		<table class="form-table" role="presentation"><tbody><?php c1791_field_row( 'custom_sidebars' ); ?></tbody></table>
		<table class="widefat striped c1791-areas">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Widget area', 'concealed1791' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Widgets', 'concealed1791' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Used for', 'concealed1791' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$widgets = wp_get_sidebars_widgets();
			foreach ( $areas as $area_id => $area ) :
				$used = array();
				foreach ( c1791_sidebar_contexts() as $context => $label ) {
					if ( c1791_setting( $context . '_sidebar_area' ) === $area_id && 'show' === c1791_setting( $context . '_sidebar' ) ) {
						$used[] = $label;
					}
				}
				$count = isset( $widgets[ $area_id ] ) ? count( (array) $widgets[ $area_id ] ) : 0;
				?>
				<tr>
					<td><strong><?php echo esc_html( $area['name'] ); ?></strong></td>
					<td><?php echo esc_html( (string) $count ); ?></td>
					<td><?php echo $used ? esc_html( implode( ', ', $used ) ) : '&mdash;'; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<p><a class="button" href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>"><?php esc_html_e( 'Manage Widgets', 'concealed1791' ); ?></a></p>
	</div>
	<?php
}

/**
 * Footer tab.
 */
function c1791_render_tab_footer() {
	?>
	<div class="c1791-group">
		<h2><?php esc_html_e( 'Layout', 'concealed1791' ); ?></h2>
		<div class="c1791-footer-layout">
			<table class="form-table" role="presentation">
				<tbody>
					<?php
					foreach ( array( 'footer_style', 'footer_brand', 'footer_about', 'footer_columns', 'footer_layout' ) as $key ) {
						c1791_field_row( $key );
					}
					?>
				</tbody>
			</table>
			<figure class="c1791-footer-diagram" aria-hidden="true">
				<div class="c1791-footer-diagram__grid" data-c1791-footer-diagram>
					<span class="is-brand"><?php esc_html_e( 'Brand', 'concealed1791' ); ?></span>
					<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
						<span data-col="<?php echo (int) $i; ?>"><?php echo esc_html( (string) $i ); ?></span>
					<?php endfor; ?>
				</div>
				<figcaption><?php esc_html_e( 'Footer preview', 'concealed1791' ); ?></figcaption>
			</figure>
		</div>
	</div>

	<div class="c1791-group">
		<h2><?php esc_html_e( 'Columns', 'concealed1791' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Pick what each column shows. Columns past the number set above are hidden.', 'concealed1791' ); ?></p>
		<div class="c1791-columns">
			<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
				<section class="c1791-column" data-c1791-column="<?php echo (int) $i; ?>">
					<h3>
						<?php
						/* translators: %d: column number. */
						echo esc_html( sprintf( __( 'Column %d', 'concealed1791' ), $i ) );
						?>
					</h3>
					<p class="c1791-field">
						<label for="<?php echo esc_attr( c1791_field_id( "footer_col{$i}_type" ) ); ?>"><?php esc_html_e( 'Shows', 'concealed1791' ); ?></label>
						<?php c1791_field_control( "footer_col{$i}_type" ); ?>
					</p>
					<div class="c1791-field" data-hide-for="widgets">
						<label for="<?php echo esc_attr( c1791_field_id( "footer_col{$i}_title" ) ); ?>"><?php esc_html_e( 'Heading', 'concealed1791' ); ?></label>
						<?php c1791_field_control( "footer_col{$i}_title" ); ?>
					</div>
					<div class="c1791-field" data-show-for="menu">
						<label for="<?php echo esc_attr( c1791_field_id( "footer_col{$i}_menu" ) ); ?>"><?php esc_html_e( 'Menu', 'concealed1791' ); ?></label>
						<?php c1791_field_control( "footer_col{$i}_menu" ); ?>
					</div>
					<div class="c1791-field" data-show-for="text">
						<label for="<?php echo esc_attr( c1791_field_id( "footer_col{$i}_text" ) ); ?>"><?php esc_html_e( 'Text', 'concealed1791' ); ?></label>
						<?php c1791_field_control( "footer_col{$i}_text" ); ?>
					</div>
					<p class="description" data-show-for="widgets">
						<?php
						printf(
							/* translators: 1: column number, 2: widgets URL. */
							wp_kses_post( __( 'Shows the "Footer Column %1$d" widget area. <a href="%2$s">Add widgets</a>.', 'concealed1791' ) ),
							(int) $i,
							esc_url( admin_url( 'widgets.php' ) )
						);
						?>
					</p>
					<p class="description" data-show-for="contact hours"><?php esc_html_e( 'Uses the details from the General tab.', 'concealed1791' ); ?></p>
					<p class="description" data-show-for="newsletter"><?php esc_html_e( 'Uses the newsletter setup (MailPoet or your form URL).', 'concealed1791' ); ?></p>
				</section>
			<?php endfor; ?>
		</div>
	</div>

	<?php
	c1791_field_group( __( 'Call-to-action band', 'concealed1791' ), array( 'footer_cta', 'footer_cta_title', 'footer_cta_text', 'footer_cta_btn_text', 'footer_cta_btn_url', 'footer_cta_phone' ), __( 'A bold band above the footer on every page (hidden on checkout and funnel steps).', 'concealed1791' ) );
	c1791_field_group( __( 'Bottom of the footer', 'concealed1791' ), array( 'footer_disclaimer', 'footer_copyright', 'back_to_top' ), __( 'The legal links next to the copyright come from the "Footer Bottom Menu" (Header & Menus tab).', 'concealed1791' ) );
}

/**
 * Colors & Style tab: presets, colors, live preview, contrast checks, button
 * shape, headings and background artwork.
 */
function c1791_render_tab_colors() {
	?>
	<p>
		<?php esc_html_e( 'Set the colors used across the site. Leave a color blank to use the default.', 'concealed1791' ); ?>
		<a href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=c1791_colors' ) ); ?>"><?php esc_html_e( 'Or change colors with a live preview in the Customizer.', 'concealed1791' ); ?></a>
	</p>
	<div class="c1791-colors">
		<div class="c1791-colors__main">
			<div class="c1791-presets">
				<h2><?php esc_html_e( 'Start from a preset', 'concealed1791' ); ?></h2>
				<p class="description"><?php esc_html_e( 'A preset fills in every color below. Nothing changes on the site until you save.', 'concealed1791' ); ?></p>
				<div class="c1791-presets__list">
					<?php foreach ( c1791_color_presets() as $preset ) : ?>
						<button type="button" class="button c1791-preset" data-colors="<?php echo esc_attr( wp_json_encode( $preset[1] ) ); ?>">
							<span class="c1791-preset__swatches" aria-hidden="true">
								<?php foreach ( array( 'color_dark', 'color_secondary', 'color_accent', 'color_highlight', 'color_bg' ) as $k ) : ?>
									<span style="background:<?php echo esc_attr( $preset[1][ $k ] ); ?>"></span>
								<?php endforeach; ?>
							</span>
							<?php echo esc_html( $preset[0] ); ?>
						</button>
					<?php endforeach; ?>
					<button type="button" class="button-link c1791-clear-colors"><?php esc_html_e( 'Clear all (use defaults)', 'concealed1791' ); ?></button>
				</div>
			</div>

			<table class="form-table c1791-color-table" role="presentation">
				<tbody>
				<?php
				$fields = c1791_settings_fields();
				foreach ( c1791_color_fields() as $key => $color ) :
					?>
					<tr>
						<th scope="row">
							<label for="<?php echo esc_attr( c1791_field_id( $key ) ); ?>"><?php echo esc_html( $color[0] ); ?></label>
							<?php if ( $color[3] ) : ?>
								<p class="description"><?php echo esc_html( $color[3] ); ?></p>
							<?php endif; ?>
						</th>
						<td>
							<?php c1791_field_control( $key ); ?>
							<p class="c1791-inherit">
								<span class="c1791-swatch" style="background:<?php echo esc_attr( $fields[ $key ]['default'] ); ?>" aria-hidden="true"></span>
								<?php
								/* translators: %s: color hex. */
								echo esc_html( sprintf( __( 'Blank uses %s', 'concealed1791' ), $fields[ $key ]['default'] ) );
								?>
							</p>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<aside class="c1791-colors__side" aria-label="<?php esc_attr_e( 'Preview', 'concealed1791' ); ?>">
			<h2><?php esc_html_e( 'Preview', 'concealed1791' ); ?></h2>
			<div class="c1791-preview" data-c1791-preview>
				<div class="c1791-preview__bar"><?php esc_html_e( 'New CCW dates just added', 'concealed1791' ); ?></div>
				<div class="c1791-preview__top"><?php esc_html_e( 'NRA & USCCA certified', 'concealed1791' ); ?></div>
				<div class="c1791-preview__header">
					<span class="c1791-preview__logo">1791</span>
					<span class="c1791-preview__nav"><?php esc_html_e( 'Classes', 'concealed1791' ); ?> · <span><?php esc_html_e( 'FAQ', 'concealed1791' ); ?></span></span>
					<span class="c1791-preview__btn"><?php esc_html_e( 'Book', 'concealed1791' ); ?></span>
				</div>
				<div class="c1791-preview__hero">
					<span class="c1791-preview__eyebrow"><?php esc_html_e( 'Certified instructors', 'concealed1791' ); ?></span>
					<strong><?php esc_html_e( 'Train to protect', 'concealed1791' ); ?></strong>
				</div>
				<div class="c1791-preview__body">
					<div class="c1791-preview__card">
						<span class="c1791-preview__date">OCT<br>12</span>
						<span>
							<strong><?php esc_html_e( 'CCW Certification', 'concealed1791' ); ?></strong>
							<small><?php esc_html_e( 'Sat · Broomfield, CO', 'concealed1791' ); ?></small>
							<a href="#" onclick="return false"><?php esc_html_e( 'Class details', 'concealed1791' ); ?></a>
						</span>
						<span class="c1791-preview__btn"><?php esc_html_e( 'Register', 'concealed1791' ); ?></span>
					</div>
					<div class="c1791-preview__alt"><span class="c1791-preview__stars">★★★★★</span> <?php esc_html_e( 'Alternate section', 'concealed1791' ); ?></div>
				</div>
				<div class="c1791-preview__footer"><?php esc_html_e( 'Footer', 'concealed1791' ); ?> · <span><?php esc_html_e( 'Concealed Carry & Defensive Training', 'concealed1791' ); ?></span></div>
			</div>
			<h3><?php esc_html_e( 'Readability', 'concealed1791' ); ?></h3>
			<ul class="c1791-contrast" data-pairs="<?php echo esc_attr( wp_json_encode( c1791_contrast_pairs() ) ); ?>"></ul>
		</aside>
	</div>

	<?php c1791_field_group( __( 'Style', 'concealed1791' ), array( 'button_shape', 'heading_case' ) ); ?>

	<div class="c1791-group">
		<h2><?php esc_html_e( 'Background artwork', 'concealed1791' ); ?></h2>
		<p class="description"><?php esc_html_e( 'Choose where the American flag and the target backgrounds appear. Both are built in; you can swap in your own photos below.', 'concealed1791' ); ?></p>
		<div class="c1791-art">
			<div class="c1791-art__sample c1791-art__sample--flag" data-c1791-art="flag" style="background-image:url(<?php echo esc_url( c1791_art_url( 'flag' ) ); ?>)"><span><?php esc_html_e( 'American flag', 'concealed1791' ); ?></span></div>
			<div class="c1791-art__sample c1791-art__sample--target" data-c1791-art="target" style="background-image:url(<?php echo esc_url( c1791_art_url( 'target' ) ); ?>)"><span><?php esc_html_e( 'Target', 'concealed1791' ); ?></span></div>
		</div>
		<table class="form-table" role="presentation">
			<tbody>
			<?php
			foreach ( array( 'bg_hero', 'bg_banner', 'bg_cta', 'bg_footer', 'bg_overlay', 'bg_flag_image', 'bg_target_image' ) as $key ) {
				c1791_field_row( $key );
			}
			?>
			</tbody>
		</table>
	</div>
	<?php
}

/**
 * Text/background pairs checked for contrast.
 *
 * @return array
 */
function c1791_contrast_pairs() {
	return array(
		array( __( 'Button text on accent', 'concealed1791' ), 'color_accent_ink', 'color_accent' ),
		array( __( 'Text on page background', 'concealed1791' ), 'color_ink', 'color_bg' ),
		array( __( 'Secondary text on cards', 'concealed1791' ), 'color_ink_soft', 'color_surface' ),
		array( __( 'Links on page background', 'concealed1791' ), 'color_secondary', 'color_bg' ),
		array( __( 'White text on header & footer', 'concealed1791' ), '#ffffff', 'color_dark' ),
		array( __( 'White text on secondary', 'concealed1791' ), '#ffffff', 'color_secondary' ),
		array( __( 'Highlight on header & footer', 'concealed1791' ), 'color_highlight', 'color_dark' ),
	);
}

/**
 * Plugin status with an install or activate link.
 *
 * @param string $file   Plugin file relative to the plugins folder.
 * @param bool   $active Whether the plugin is active.
 * @param string $search Search term for the plugin installer.
 */
function c1791_render_plugin_status( $file, $active, $search ) {
	if ( $active ) {
		echo '<span class="c1791-status c1791-status--on">' . esc_html__( 'Active', 'concealed1791' ) . '</span>';
		return;
	}
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( array_key_exists( $file, get_plugins() ) ) {
		echo '<span class="c1791-status">' . esc_html__( 'Installed, not active', 'concealed1791' ) . '</span>';
		if ( current_user_can( 'activate_plugins' ) ) {
			$url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $file ) ), 'activate-plugin_' . $file );
			echo ' <a class="button button-small" href="' . esc_url( $url ) . '">' . esc_html__( 'Activate', 'concealed1791' ) . '</a>';
		}
		return;
	}
	echo '<span class="c1791-status">' . esc_html__( 'Not installed', 'concealed1791' ) . '</span>';
	if ( current_user_can( 'install_plugins' ) ) {
		$url = self_admin_url( 'plugin-install.php?tab=search&type=term&s=' . rawurlencode( $search ) );
		echo ' <a class="button button-small" href="' . esc_url( $url ) . '">' . esc_html__( 'Install', 'concealed1791' ) . '</a>';
	}
}

/**
 * A labeled setting inside an integration card.
 *
 * @param string $key   Setting key.
 * @param array  $extra Passed to c1791_field_control().
 */
function c1791_integration_field( $key, $extra = array() ) {
	$fields = c1791_settings_fields();
	echo '<div class="c1791-field">';
	if ( 'toggle' !== $fields[ $key ]['type'] ) {
		echo '<label for="' . esc_attr( c1791_field_id( $key ) ) . '">' . esc_html( isset( $extra['label'] ) ? $extra['label'] : $fields[ $key ]['label'] ) . '</label>';
	}
	c1791_field_control( $key, $extra );
	echo '</div>';
}

/**
 * Integrations tab: WooCommerce, FunnelKit, Elementor, Amelia, MailPoet.
 */
function c1791_render_tab_integrations() {
	$wc_active = class_exists( 'WooCommerce' );
	echo '<p>' . esc_html__( 'The theme is built to work with these plugins. Each one is optional; its settings apply once it is active.', 'concealed1791' ) . '</p>';
	?>
	<div class="c1791-integrations">
		<section class="c1791-integration">
			<header><h2>WooCommerce</h2><?php c1791_render_plugin_status( 'woocommerce/woocommerce.php', $wc_active, 'woocommerce' ); ?></header>
			<p><?php esc_html_e( 'Shop, product, cart, checkout and account pages use the theme layout, colors and buttons, including the block-based cart and checkout. Link a class or package to a product and its button adds the product to the cart and opens checkout; product stock becomes the class\'s seats left.', 'concealed1791' ); ?></p>
			<?php if ( $wc_active ) : ?>
				<?php $overrides = c1791_wc_template_overrides(); ?>
				<p class="c1791-check <?php echo $overrides ? 'is-warn' : 'is-ok'; ?>">
					<?php
					if ( $overrides ) {
						/* translators: %s: list of template files. */
						echo esc_html( sprintf( __( 'Template overrides found: %s. These can go out of date when WooCommerce updates; check WooCommerce → Status.', 'concealed1791' ), implode( ', ', $overrides ) ) );
					} else {
						/* translators: %s: WooCommerce version. */
						echo esc_html( sprintf( __( 'Update-safe: the theme overrides no WooCommerce templates, so WooCommerce updates (now %s) never leave theme files out of date.', 'concealed1791' ), WC()->version ) );
					}
					?>
				</p>
			<?php endif; ?>
			<?php
			c1791_integration_field( 'wc_checkout_header' );
			c1791_integration_field( 'wc_direct_checkout' );
			c1791_integration_field( 'wc_header_cart' );
			c1791_integration_field( 'wc_header_account' );
			?>
			<p class="description"><?php esc_html_e( 'The shop sidebar is set on the Sidebars tab. The front page products section is turned on or off in Customize → Concealed 1791: Home Page → Articles & Shop.', 'concealed1791' ); ?></p>
		</section>

		<section class="c1791-integration">
			<header><h2>FunnelKit</h2><?php c1791_render_plugin_status( 'funnel-builder/funnel-builder.php', c1791_funnelkit_active(), 'funnelkit' ); ?></header>
			<p><?php esc_html_e( 'Funnel steps (sales, opt-in, checkout, upsell and thank-you pages) show only their own content: no page banner, sidebar, menu, announcement bar or call-to-action band. FunnelKit\'s checkout designs keep their own form styles.', 'concealed1791' ); ?></p>
			<?php c1791_integration_field( 'funnel_header' ); ?>
			<p class="description"><?php esc_html_e( 'For a completely blank step, choose FunnelKit\'s "Canvas" template on that step instead.', 'concealed1791' ); ?></p>
		</section>

		<section class="c1791-integration">
			<header><h2>Elementor</h2><?php c1791_render_plugin_status( 'elementor/elementor.php', c1791_elementor_active(), 'elementor' ); ?></header>
			<p><?php esc_html_e( 'Pages built with Elementor run full width under the theme header. Elementor Pro\'s Theme Builder can replace the header and footer.', 'concealed1791' ); ?></p>
			<?php c1791_integration_field( 'elementor_default_editor' ); ?>
			<p class="description"><?php esc_html_e( 'With Elementor as the default, "Add New" opens Elementor. "Add New (Block Editor)" stays in each menu.', 'concealed1791' ); ?></p>
			<?php c1791_integration_field( 'elementor_front_page' ); ?>
			<?php c1791_integration_field( 'elementor_sync' ); ?>
			<p class="description"><?php esc_html_e( 'When matched, Elementor\'s global colors follow the Colors & Style tab (Primary = Header & footer, Secondary = Secondary, Text = Text, Accent = Accent) and its global fonts use Barlow Condensed and Barlow. Your own custom colors in Elementor are kept.', 'concealed1791' ); ?></p>
			<?php if ( get_option( 'c1791_elementor_kit_backup' ) ) : ?>
				<p><a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=c1791_elementor_restore_kit' ), 'c1791_elementor_restore_kit' ) ); ?>"><?php esc_html_e( 'Restore Elementor\'s previous colors and fonts', 'concealed1791' ); ?></a></p>
				<p class="description"><?php esc_html_e( 'Puts back the global colors, fonts and content width Elementor had before the theme first matched them, and turns matching off.', 'concealed1791' ); ?></p>
			<?php endif; ?>
		</section>

		<section class="c1791-integration">
			<header><h2>Amelia</h2><?php c1791_render_plugin_status( 'ameliabooking/ameliabooking.php', c1791_amelia_active(), 'amelia booking' ); ?></header>
			<p><?php esc_html_e( 'Link a class to an Amelia event (Class Details → "Amelia event ID") to show Amelia\'s booking form on the class page and point its Register buttons there. Add an "Amelia employee ID" to an instructor to offer private-lesson booking on their page.', 'concealed1791' ); ?></p>
			<?php c1791_integration_field( 'amelia_match' ); ?>
			<details class="c1791-amelia-colors">
				<summary><?php esc_html_e( 'Matching values for Amelia → Customize', 'concealed1791' ); ?></summary>
				<p class="description"><?php esc_html_e( 'Entering these in Amelia keeps its emails, customer panel and any form the theme style does not reach consistent with the site.', 'concealed1791' ); ?></p>
				<table class="widefat striped">
					<tbody>
					<?php foreach ( c1791_amelia_color_map() as $label => $value ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $label ); ?></th>
							<td>
								<?php if ( '#' === substr( $value, 0, 1 ) ) : ?>
									<span class="c1791-swatch" style="background:<?php echo esc_attr( $value ); ?>" aria-hidden="true"></span>
								<?php endif; ?>
								<code><?php echo esc_html( $value ); ?></code>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</details>
		</section>

		<section class="c1791-integration">
			<header><h2>MailPoet</h2><?php c1791_render_plugin_status( 'mailpoet/mailpoet.php', c1791_mailpoet_active(), 'mailpoet' ); ?></header>
			<p><?php esc_html_e( 'Send sign-ups from the front page newsletter band and any footer "Newsletter sign-up" column straight into MailPoet. MailPoet\'s confirmation (double opt-in) and welcome emails apply as usual.', 'concealed1791' ); ?></p>
			<?php if ( c1791_mailpoet_active() ) : ?>
				<?php
				c1791_integration_field(
					'mailpoet_list',
					array(
						'label'   => __( 'Add sign-ups from the theme\'s email form to this list', 'concealed1791' ),
						'options' => c1791_mailpoet_lists(),
						'none'    => __( 'Don\'t use MailPoet for the theme form', 'concealed1791' ),
					)
				);
				c1791_integration_field(
					'mailpoet_form',
					array(
						'label'   => __( 'Or show this MailPoet form instead (for extra fields)', 'concealed1791' ),
						'options' => c1791_mailpoet_forms(),
						'none'    => __( 'No MailPoet form (use the theme\'s form)', 'concealed1791' ),
					)
				);
				?>
				<p class="description">
					<?php
					printf(
						/* translators: 1: MailPoet lists URL, 2: MailPoet forms URL. */
						wp_kses_post( __( 'A form overrides the list. Manage <a href="%1$s">lists</a> and <a href="%2$s">forms</a> in MailPoet; a form sets its own list.', 'concealed1791' ) ),
						esc_url( admin_url( 'admin.php?page=mailpoet-segments' ) ),
						esc_url( admin_url( 'admin.php?page=mailpoet-forms' ) )
					);
					?>
				</p>
			<?php endif; ?>
			<?php c1791_integration_field( 'mailpoet_match' ); ?>
		</section>
	</div>
	<?php
}

/**
 * Import / Export tab.
 */
function c1791_render_tools_tab() {
	?>
	<div class="c1791-tools">
		<section class="c1791-integration">
			<header><h2><?php esc_html_e( 'Export', 'concealed1791' ); ?></h2></header>
			<p><?php esc_html_e( 'Download every Theme Setting and the home page content as a file, to back them up or move them from a demo or staging site to the live site.', 'concealed1791' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="c1791_export_settings">
				<?php wp_nonce_field( 'c1791_export_settings' ); ?>
				<?php submit_button( __( 'Download Settings', 'concealed1791' ), 'secondary', 'submit', false ); ?>
			</form>
		</section>

		<section class="c1791-integration">
			<header><h2><?php esc_html_e( 'Import', 'concealed1791' ); ?></h2></header>
			<p><?php esc_html_e( 'Upload a settings file (or paste its contents). It replaces the current Theme Settings and home page content. Menus, widgets and posts are not included.', 'concealed1791' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
				<input type="hidden" name="action" value="c1791_import_settings">
				<?php wp_nonce_field( 'c1791_import_settings' ); ?>
				<p><label for="c1791-import-file"><?php esc_html_e( 'Settings file (.json)', 'concealed1791' ); ?></label><br><input type="file" id="c1791-import-file" name="c1791_import_file" accept=".json,application/json"></p>
				<p><label for="c1791-import-text"><?php esc_html_e( 'Or paste', 'concealed1791' ); ?></label><br><textarea id="c1791-import-text" name="c1791_import_text" rows="4" class="large-text code"></textarea></p>
				<?php submit_button( __( 'Import Settings', 'concealed1791' ), 'secondary', 'submit', false ); ?>
			</form>
		</section>

		<section class="c1791-integration">
			<header><h2><?php esc_html_e( 'Reset', 'concealed1791' ); ?></h2></header>
			<p><?php esc_html_e( 'Put every Theme Setting back to its default. Colors, sidebars, footer and integration choices are reset; your content is not touched.', 'concealed1791' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-c1791-confirm>
				<input type="hidden" name="action" value="c1791_reset_settings">
				<?php wp_nonce_field( 'c1791_reset_settings' ); ?>
				<p><label><input type="checkbox" name="c1791_reset_mods" value="1"> <?php esc_html_e( 'Also reset the home page content (Customizer)', 'concealed1791' ); ?></label></p>
				<?php submit_button( __( 'Reset to Defaults', 'concealed1791' ), 'delete', 'submit', false ); ?>
			</form>
		</section>
	</div>
	<?php
}

/**
 * Home page content theme mods (without the c1791_ prefix) that are set.
 *
 * @return array
 */
function c1791_export_mods() {
	$mods = array();
	foreach ( array_keys( c1791_defaults() ) as $key ) {
		$value = get_theme_mod( 'c1791_' . $key, null );
		if ( null !== $value ) {
			$mods[ $key ] = $value;
		}
	}
	return $mods;
}

/**
 * Download the settings as JSON.
 */
function c1791_export_settings() {
	check_admin_referer( 'c1791_export_settings' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to export these settings.', 'concealed1791' ), 403 );
	}
	$data = array(
		'theme'    => 'concealed1791',
		'version'  => C1791_VERSION,
		'exported' => gmdate( 'c' ),
		'settings' => (array) get_option( 'c1791_settings', array() ),
		'mods'     => c1791_export_mods(),
	);
	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=concealed1791-settings-' . gmdate( 'Y-m-d' ) . '.json' );
	echo wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}
add_action( 'admin_post_c1791_export_settings', 'c1791_export_settings' );

/**
 * Sanitize one imported home page content value by its default's type.
 *
 * @param string $key   Key without prefix.
 * @param mixed  $value Value.
 * @return mixed|null Null to skip.
 */
function c1791_sanitize_import_mod( $key, $value ) {
	$defaults = c1791_defaults();
	if ( ! array_key_exists( $key, $defaults ) || is_array( $value ) || is_object( $value ) ) {
		return null;
	}
	$default = $defaults[ $key ];
	if ( is_bool( $default ) ) {
		return (bool) $value;
	}
	if ( is_int( $default ) ) {
		return absint( $value );
	}
	if ( 'hero_title' === $key ) {
		return wp_kses_post( $value );
	}
	if ( 'hero_image' === $key || 'news_action' === $key ) {
		return esc_url_raw( $value );
	}
	if ( preg_match( '/_url$/', $key ) ) {
		return c1791_sanitize_link( $value );
	}
	if ( preg_match( '/_icon$/', $key ) ) {
		return c1791_sanitize_icon( $value );
	}
	return sanitize_textarea_field( $value );
}

/**
 * Import settings from a file or pasted JSON.
 */
function c1791_import_settings() {
	check_admin_referer( 'c1791_import_settings' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to import these settings.', 'concealed1791' ), 403 );
	}

	$json = '';
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- the file is read, decoded and every value sanitized below.
	if ( ! empty( $_FILES['c1791_import_file']['tmp_name'] ) && isset( $_FILES['c1791_import_file']['size'] ) && is_uploaded_file( $_FILES['c1791_import_file']['tmp_name'] ) && (int) $_FILES['c1791_import_file']['size'] <= MB_IN_BYTES ) {
		$json = (string) file_get_contents( $_FILES['c1791_import_file']['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	} elseif ( ! empty( $_POST['c1791_import_text'] ) ) {
		$json = (string) wp_unslash( $_POST['c1791_import_text'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- decoded and sanitized below.
	}

	$data = json_decode( $json, true );
	if ( ! is_array( $data ) || ! isset( $data['theme'] ) || 'concealed1791' !== $data['theme'] ) {
		wp_safe_redirect( admin_url( 'themes.php?page=c1791-settings&tab=tools&c1791-import-error=1' ) );
		exit;
	}

	// The option's sanitize callback cleans every value.
	update_option( 'c1791_settings', isset( $data['settings'] ) && is_array( $data['settings'] ) ? $data['settings'] : array() );

	if ( isset( $data['mods'] ) && is_array( $data['mods'] ) ) {
		foreach ( $data['mods'] as $key => $value ) {
			$clean = c1791_sanitize_import_mod( (string) $key, $value );
			if ( null !== $clean ) {
				set_theme_mod( 'c1791_' . $key, $clean );
			}
		}
	}

	wp_safe_redirect( admin_url( 'themes.php?page=c1791-settings&tab=tools&c1791-imported=1' ) );
	exit;
}
add_action( 'admin_post_c1791_import_settings', 'c1791_import_settings' );

/**
 * Reset settings (and optionally home page content) to defaults.
 */
function c1791_reset_settings() {
	check_admin_referer( 'c1791_reset_settings' );
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'Sorry, you are not allowed to reset these settings.', 'concealed1791' ), 403 );
	}
	update_option( 'c1791_settings', array() );
	if ( ! empty( $_POST['c1791_reset_mods'] ) ) {
		foreach ( array_keys( c1791_defaults() ) as $key ) {
			remove_theme_mod( 'c1791_' . $key );
		}
	}
	wp_safe_redirect( admin_url( 'themes.php?page=c1791-settings&tab=tools&c1791-reset=1' ) );
	exit;
}
add_action( 'admin_post_c1791_reset_settings', 'c1791_reset_settings' );
