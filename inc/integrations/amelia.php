<?php
/**
 * Amelia booking integration (https://wpamelia.com/).
 *
 * - Classes: set an "Amelia event ID" on a class and its page shows Amelia's
 *   event booking form under "Reserve Your Seat"; every Register button for
 *   that class jumps to it.
 * - Instructors: set an "Amelia employee ID" (and optionally a service ID) and
 *   the instructor's page shows a "Book a Private Lesson" form.
 * - Booking forms pick up the theme's colors and fonts (Theme Settings →
 *   Integrations → Amelia), and the Integrations tab lists the matching values
 *   for Amelia → Customize.
 *
 * Amelia can take payment through WooCommerce, which routes bookings through
 * the WooCommerce (and FunnelKit) checkout.
 *
 * Adapted from the kamodev/PEN theme's Amelia integration.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether Amelia is active.
 *
 * @return bool
 */
function c1791_amelia_active() {
	return defined( 'AMELIA_VERSION' ) || class_exists( '\\AmeliaBooking\\Plugin' ) || shortcode_exists( 'ameliabooking' ) || shortcode_exists( 'ameliastepbooking' );
}

/**
 * Render an Amelia shortcode, preferring the newer 2.x forms.
 *
 * @param string $kind 'booking' or 'events'.
 * @param array  $atts Shortcode attributes.
 * @return string HTML, or '' when no Amelia shortcode is registered.
 */
function c1791_amelia_shortcode( $kind, $atts = array() ) {
	$candidates = apply_filters(
		'c1791_amelia_shortcodes',
		array(
			'booking' => array( 'ameliastepbooking', 'ameliabooking' ),
			'events'  => array( 'ameliaeventslistbooking', 'ameliaevents' ),
		)
	);
	foreach ( isset( $candidates[ $kind ] ) ? $candidates[ $kind ] : array() as $tag ) {
		if ( shortcode_exists( $tag ) ) {
			$pairs = '';
			foreach ( array_filter( $atts ) as $name => $value ) {
				$pairs .= sprintf( ' %s="%s"', sanitize_key( $name ), esc_attr( $value ) );
			}
			return do_shortcode( '[' . $tag . $pairs . ']' );
		}
	}
	return '';
}

/**
 * Amelia ID fields on classes and instructors.
 *
 * @param array $fields Detail box fields.
 * @return array
 */
function c1791_amelia_meta_fields( $fields ) {
	if ( ! c1791_amelia_active() ) {
		return $fields;
	}
	$fields['c1791_class']['fields']['_c1791_amelia_event']          = array(
		'label' => __( 'Amelia event ID (shows Amelia\'s booking form on this class)', 'concealed1791' ),
		'type'  => 'number',
	);
	$fields['c1791_instructor']['fields']['_c1791_amelia_employee'] = array(
		'label' => __( 'Amelia employee ID (shows a private-lesson booking form)', 'concealed1791' ),
		'type'  => 'number',
	);
	$fields['c1791_instructor']['fields']['_c1791_amelia_service']  = array(
		'label' => __( 'Amelia service ID (optional: limit the form to one service)', 'concealed1791' ),
		'type'  => 'number',
	);
	return $fields;
}
add_filter( 'c1791_meta_fields', 'c1791_amelia_meta_fields' );

/**
 * Point Register at the class's Amelia booking form. Runs after the
 * WooCommerce integration, so an Amelia event wins over a linked product.
 *
 * @param array $data    Class details.
 * @param int   $post_id Class post ID.
 * @return array
 */
function c1791_amelia_class_data( $data, $post_id ) {
	$event                = (int) get_post_meta( $post_id, '_c1791_amelia_event', true );
	$data['amelia_event'] = $event;
	if ( $event && c1791_amelia_active() ) {
		$data['register'] = get_permalink( $post_id ) . '#register';
		$data['nofollow'] = false;
	}
	return $data;
}
add_filter( 'c1791_class_data', 'c1791_amelia_class_data', 20, 2 );

/**
 * "Reserve Your Seat" section on class pages.
 *
 * @param array $c Class details.
 */
function c1791_amelia_class_booking( $c ) {
	if ( empty( $c['amelia_event'] ) || ! c1791_amelia_active() ) {
		return;
	}
	$form = c1791_amelia_shortcode( 'events', array( 'event' => $c['amelia_event'] ) );
	if ( '' === $form ) {
		return;
	}
	?>
	<section class="ct-booking" id="register" aria-labelledby="ct-booking-title">
		<h2 id="ct-booking-title"><?php esc_html_e( 'Reserve Your Seat', 'concealed1791' ); ?></h2>
		<div class="ct-booking__form"><?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Amelia shortcode output. ?></div>
	</section>
	<?php
}
add_action( 'c1791_class_after_content', 'c1791_amelia_class_booking' );

/**
 * "Book a Private Lesson" section on instructor pages.
 *
 * @param int $instructor_id Instructor post ID.
 */
function c1791_amelia_instructor_booking( $instructor_id ) {
	$employee = (int) get_post_meta( $instructor_id, '_c1791_amelia_employee', true );
	if ( ! $employee || ! c1791_amelia_active() ) {
		return;
	}
	$form = c1791_amelia_shortcode(
		'booking',
		array(
			'employee' => $employee,
			'service'  => (int) get_post_meta( $instructor_id, '_c1791_amelia_service', true ),
		)
	);
	if ( '' === $form ) {
		return;
	}
	?>
	<section class="ct-section" id="book">
		<div class="ct-container ct-booking">
			<h2>
				<?php
				/* translators: %s: instructor name. */
				echo esc_html( sprintf( __( 'Book a Private Lesson with %s', 'concealed1791' ), get_the_title( $instructor_id ) ) );
				?>
			</h2>
			<div class="ct-booking__form"><?php echo $form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Amelia shortcode output. ?></div>
		</div>
	</section>
	<?php
}
add_action( 'c1791_instructor_after_content', 'c1791_amelia_instructor_booking' );

/**
 * Load amelia.css when Amelia is active and matching is on.
 *
 * @param string[] $parts Stylesheet parts.
 * @return string[]
 */
function c1791_amelia_style_part( $parts ) {
	if ( c1791_amelia_active() && 'on' === c1791_setting( 'amelia_match' ) ) {
		$parts[] = 'amelia';
	}
	return $parts;
}
add_filter( 'c1791_style_parts', 'c1791_amelia_style_part' );

/**
 * Theme values to enter in Amelia → Customize (shown on the Integrations tab).
 *
 * @return array label => value
 */
function c1791_amelia_color_map() {
	return array(
		__( 'Primary color', 'concealed1791' )       => c1791_setting( 'color_accent' ),
		__( 'Success color', 'concealed1791' )       => '#2f7d4a',
		__( 'Error color', 'concealed1791' )         => '#b3261e',
		__( 'Form background', 'concealed1791' )     => c1791_setting( 'color_surface' ),
		__( 'Heading text', 'concealed1791' )        => c1791_setting( 'color_ink' ),
		__( 'Content text', 'concealed1791' )        => c1791_setting( 'color_ink_soft' ),
		__( 'Sidebar background', 'concealed1791' )  => c1791_setting( 'color_dark' ),
		__( 'Input border', 'concealed1791' )        => c1791_setting( 'color_border' ),
		__( 'Primary button text', 'concealed1791' ) => c1791_setting( 'color_accent_ink' ),
		__( 'Font', 'concealed1791' )                => 'Barlow',
	);
}
