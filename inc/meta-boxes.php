<?php
/**
 * Detail boxes for classes, instructors, testimonials and packages.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Skill level labels.
 *
 * @return array
 */
function c1791_levels() {
	return array(
		''             => __( 'All levels', 'concealed1791' ),
		'beginner'     => __( 'Beginner', 'concealed1791' ),
		'intermediate' => __( 'Intermediate', 'concealed1791' ),
		'advanced'     => __( 'Advanced', 'concealed1791' ),
	);
}

/**
 * Field definitions per post type.
 *
 * @return array
 */
function c1791_meta_fields() {
	/**
	 * Filter the detail box fields per post type (integrations add theirs).
	 *
	 * @param array $fields post type => [ title, fields ].
	 */
	return apply_filters( 'c1791_meta_fields', c1791_default_meta_fields() );
}

/**
 * Built-in fields.
 *
 * @return array
 */
function c1791_default_meta_fields() {
	return array(
		'c1791_class'       => array(
			'title'  => __( 'Class Details', 'concealed1791' ),
			'fields' => array(
				'_c1791_start_date'   => array( 'label' => __( 'Start date', 'concealed1791' ), 'type' => 'date' ),
				'_c1791_end_date'     => array( 'label' => __( 'End date (multi-day classes)', 'concealed1791' ), 'type' => 'date' ),
				'_c1791_time'         => array( 'label' => __( 'Time', 'concealed1791' ), 'type' => 'text', 'placeholder' => '8:00 AM – 5:00 PM' ),
				'_c1791_duration'     => array( 'label' => __( 'Duration', 'concealed1791' ), 'type' => 'text', 'placeholder' => '8 hours' ),
				'_c1791_location'     => array( 'label' => __( 'Location', 'concealed1791' ), 'type' => 'text', 'placeholder' => 'Classroom & range – Broomfield, CO' ),
				'_c1791_price'        => array( 'label' => __( 'Price', 'concealed1791' ), 'type' => 'text', 'placeholder' => '$149' ),
				'_c1791_capacity'     => array( 'label' => __( 'Capacity', 'concealed1791' ), 'type' => 'number' ),
				'_c1791_seats_left'   => array( 'label' => __( 'Seats left (0 = sold out; blank hides the count)', 'concealed1791' ), 'type' => 'number' ),
				'_c1791_level'        => array( 'label' => __( 'Skill level', 'concealed1791' ), 'type' => 'select', 'options' => c1791_levels() ),
				'_c1791_register_url' => array( 'label' => __( 'Registration link (booking page, product or form)', 'concealed1791' ), 'type' => 'link' ),
				'_c1791_includes'     => array( 'label' => __( 'What\'s included (one per line)', 'concealed1791' ), 'type' => 'textarea' ),
				'_c1791_bring'        => array( 'label' => __( 'What to bring (one per line)', 'concealed1791' ), 'type' => 'textarea' ),
				'_c1791_prereqs'      => array( 'label' => __( 'Prerequisites', 'concealed1791' ), 'type' => 'textarea' ),
				'_c1791_instructors'  => array( 'label' => __( 'Instructors', 'concealed1791' ), 'type' => 'instructors' ),
			),
		),
		'c1791_instructor'  => array(
			'title'  => __( 'Instructor Details', 'concealed1791' ),
			'fields' => array(
				'_c1791_role'        => array( 'label' => __( 'Role / title', 'concealed1791' ), 'type' => 'text', 'placeholder' => 'Lead Firearms Instructor' ),
				'_c1791_credentials' => array( 'label' => __( 'Credentials (separate with ·, commas or |)', 'concealed1791' ), 'type' => 'text', 'placeholder' => 'NRA Pistol Instructor · USCCA Certified · RSO' ),
			),
		),
		'c1791_testimonial' => array(
			'title'  => __( 'Testimonial Details', 'concealed1791' ),
			'fields' => array(
				'_c1791_rating'      => array(
					'label'   => __( 'Rating', 'concealed1791' ),
					'type'    => 'select',
					'options' => array(
						'5' => '★★★★★',
						'4' => '★★★★',
						'3' => '★★★',
						'2' => '★★',
						'1' => '★',
					),
				),
				'_c1791_class_taken' => array( 'label' => __( 'Class taken', 'concealed1791' ), 'type' => 'text', 'placeholder' => 'CCW Certification' ),
				'_c1791_location'    => array( 'label' => __( 'Hometown', 'concealed1791' ), 'type' => 'text' ),
				'_c1791_source'      => array( 'label' => __( 'Source (for example "Google review")', 'concealed1791' ), 'type' => 'text' ),
			),
		),
		'c1791_package'     => array(
			'title'  => __( 'Package Details', 'concealed1791' ),
			'fields' => array(
				'_c1791_price'          => array( 'label' => __( 'Price', 'concealed1791' ), 'type' => 'text', 'placeholder' => '$149' ),
				'_c1791_period'         => array( 'label' => __( 'Price note', 'concealed1791' ), 'type' => 'text', 'placeholder' => 'per person' ),
				'_c1791_price_alt'      => array( 'label' => __( 'Second price (shown when visitors flip the pricing switch)', 'concealed1791' ), 'type' => 'text', 'placeholder' => '$269' ),
				'_c1791_period_alt'     => array( 'label' => __( 'Second price note', 'concealed1791' ), 'type' => 'text', 'placeholder' => 'for two' ),
				'_c1791_features'       => array( 'label' => __( 'Features (one per line; start a line with "-" for not included)', 'concealed1791' ), 'type' => 'textarea' ),
				'_c1791_featured'       => array(
					'label'   => __( 'Highlight this package', 'concealed1791' ),
					'type'    => 'select',
					'options' => array(
						''  => __( 'No', 'concealed1791' ),
						'1' => __( 'Yes (raised card with a badge)', 'concealed1791' ),
					),
				),
				'_c1791_badge'          => array( 'label' => __( 'Badge text', 'concealed1791' ), 'type' => 'text', 'placeholder' => 'Most popular' ),
				'_c1791_button_text'    => array( 'label' => __( 'Button text', 'concealed1791' ), 'type' => 'text', 'placeholder' => 'Reserve my seat' ),
				'_c1791_button_url'     => array( 'label' => __( 'Button link', 'concealed1791' ), 'type' => 'link' ),
				'_c1791_button_url_alt' => array( 'label' => __( 'Button link for the second price (optional)', 'concealed1791' ), 'type' => 'link' ),
			),
		),
	);
}

/**
 * Register the detail boxes.
 */
function c1791_add_meta_boxes() {
	foreach ( c1791_meta_fields() as $post_type => $box ) {
		add_meta_box( 'c1791-details', $box['title'], 'c1791_render_meta_box', $post_type, 'normal', 'high' );
	}
}
add_action( 'add_meta_boxes', 'c1791_add_meta_boxes' );

/**
 * Render a detail box.
 *
 * @param WP_Post $post Current post.
 */
function c1791_render_meta_box( $post ) {
	$defs = c1791_meta_fields();
	if ( empty( $defs[ $post->post_type ] ) ) {
		return;
	}

	wp_nonce_field( 'c1791_save_meta', 'c1791_meta_nonce' );

	echo '<table class="form-table" role="presentation"><tbody>';

	foreach ( $defs[ $post->post_type ]['fields'] as $key => $field ) {
		$value = get_post_meta( $post->ID, $key, true );
		$id    = 'c1791-field-' . sanitize_html_class( $key );

		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';

		switch ( $field['type'] ) {
			case 'textarea':
				printf(
					'<textarea id="%1$s" name="%2$s" rows="4" class="large-text">%3$s</textarea>',
					esc_attr( $id ),
					esc_attr( $key ),
					esc_textarea( $value )
				);
				break;

			case 'select':
				printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $key ) );
				foreach ( $field['options'] as $opt_value => $opt_label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $opt_value ),
						selected( (string) $value, (string) $opt_value, false ),
						esc_html( $opt_label )
					);
				}
				echo '</select>';
				break;

			case 'post_select':
				$choices = get_posts(
					array(
						'post_type'      => $field['post_type'],
						'post_status'    => array( 'publish', 'private' ),
						'posts_per_page' => 300,
						'orderby'        => 'title',
						'order'          => 'ASC',
						'no_found_rows'  => true,
					)
				);
				printf( '<select id="%1$s" name="%2$s" style="max-width:100%%">', esc_attr( $id ), esc_attr( $key ) );
				echo '<option value="">' . esc_html( isset( $field['none'] ) ? $field['none'] : __( '— None —', 'concealed1791' ) ) . '</option>';
				foreach ( $choices as $choice ) {
					printf(
						'<option value="%1$d" %2$s>%3$s (#%1$d)</option>',
						(int) $choice->ID,
						selected( (int) $value, (int) $choice->ID, false ),
						esc_html( get_the_title( $choice ) )
					);
				}
				echo '</select>';
				if ( ! empty( $field['help'] ) ) {
					echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
				}
				break;

			case 'instructors':
				$selected    = array_map( 'absint', (array) $value );
				$instructors = get_posts(
					array(
						'post_type'      => 'c1791_instructor',
						'posts_per_page' => 100,
						'orderby'        => 'title',
						'order'          => 'ASC',
					)
				);
				if ( ! $instructors ) {
					esc_html_e( 'Add instructors under Instructors → Add New.', 'concealed1791' );
					break;
				}
				echo '<fieldset id="' . esc_attr( $id ) . '">';
				foreach ( $instructors as $instructor ) {
					printf(
						'<label style="display:block;margin-bottom:4px"><input type="checkbox" name="%1$s[]" value="%2$d" %3$s> %4$s</label>',
						esc_attr( $key ),
						(int) $instructor->ID,
						checked( in_array( $instructor->ID, $selected, true ), true, false ),
						esc_html( get_the_title( $instructor ) )
					);
				}
				echo '</fieldset>';
				break;

			default:
				$type = 'link' === $field['type'] ? 'text' : $field['type'];
				printf(
					'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" placeholder="%5$s" class="%6$s" %7$s>',
					esc_attr( $type ),
					esc_attr( $id ),
					esc_attr( $key ),
					esc_attr( $value ),
					esc_attr( isset( $field['placeholder'] ) ? $field['placeholder'] : ( 'link' === $field['type'] ? '/classes/ or https://…' : '' ) ),
					'number' === $type ? 'small-text' : 'regular-text',
					'number' === $type ? 'min="0" step="1"' : ''
				);
				if ( ! empty( $field['help'] ) ) {
					echo '<p class="description">' . esc_html( $field['help'] ) . '</p>';
				}
		}

		echo '</td></tr>';
	}

	echo '</tbody></table>';
}

/**
 * Save detail box values.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function c1791_save_meta( $post_id, $post ) {
	if ( ! isset( $_POST['c1791_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['c1791_meta_nonce'] ) ), 'c1791_save_meta' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$defs = c1791_meta_fields();
	if ( empty( $defs[ $post->post_type ] ) ) {
		return;
	}

	foreach ( $defs[ $post->post_type ]['fields'] as $key => $field ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per type below.
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';

		switch ( $field['type'] ) {
			case 'date':
				$raw   = sanitize_text_field( $raw );
				$value = preg_match( '/^\d{4}-\d{2}-\d{2}$/', $raw ) ? $raw : '';
				break;
			case 'number':
				$value = '' === $raw ? '' : absint( $raw );
				break;
			case 'post_select':
				$value = absint( $raw ) && get_post_type( absint( $raw ) ) === $field['post_type'] ? absint( $raw ) : '';
				break;
			case 'url':
				$value = esc_url_raw( $raw );
				break;
			case 'link':
				$value = c1791_sanitize_link( $raw );
				break;
			case 'textarea':
				$value = sanitize_textarea_field( $raw );
				break;
			case 'select':
				$value = array_key_exists( $raw, $field['options'] ) ? $raw : '';
				break;
			case 'instructors':
				$value = array_values( array_filter( array_map( 'absint', (array) $raw ) ) );
				break;
			default:
				$value = sanitize_text_field( $raw );
		}

		if ( '' === $value || array() === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post', 'c1791_save_meta', 10, 2 );
