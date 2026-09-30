<?php
/**
 * Content types: Classes (with Class Types), Instructors, Testimonials,
 * Packages and FAQs.
 *
 * Content types normally belong in a plugin. They live here so the theme
 * works out of the box; if you move them to a plugin, keep the same keys.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register post types and taxonomies.
 */
function c1791_register_content_types() {
	register_post_type(
		'c1791_class',
		array(
			'labels'        => array(
				'name'          => __( 'Classes', 'concealed1791' ),
				'singular_name' => __( 'Class', 'concealed1791' ),
				'add_new_item'  => __( 'Add New Class', 'concealed1791' ),
				'edit_item'     => __( 'Edit Class', 'concealed1791' ),
				'all_items'     => __( 'All Classes', 'concealed1791' ),
				'view_item'     => __( 'View Class', 'concealed1791' ),
				'search_items'  => __( 'Search Classes', 'concealed1791' ),
				'menu_name'     => __( 'Classes', 'concealed1791' ),
			),
			'public'        => true,
			'has_archive'   => 'classes',
			'rewrite'       => array(
				'slug'       => 'classes',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-calendar-alt',
			'menu_position' => 5,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
		)
	);

	register_taxonomy(
		'c1791_class_type',
		array( 'c1791_class' ),
		array(
			'labels'            => array(
				'name'          => __( 'Class Types', 'concealed1791' ),
				'singular_name' => __( 'Class Type', 'concealed1791' ),
				'add_new_item'  => __( 'Add New Class Type', 'concealed1791' ),
				'menu_name'     => __( 'Class Types', 'concealed1791' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'class-type',
				'with_front' => false,
			),
		)
	);

	register_post_type(
		'c1791_instructor',
		array(
			'labels'        => array(
				'name'          => __( 'Instructors', 'concealed1791' ),
				'singular_name' => __( 'Instructor', 'concealed1791' ),
				'add_new_item'  => __( 'Add New Instructor', 'concealed1791' ),
				'edit_item'     => __( 'Edit Instructor', 'concealed1791' ),
			),
			'public'        => true,
			'has_archive'   => 'instructors',
			'rewrite'       => array(
				'slug'       => 'instructors',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-id',
			'menu_position' => 6,
			'show_in_rest'  => true,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'c1791_testimonial',
		array(
			'labels'              => array(
				'name'          => __( 'Testimonials', 'concealed1791' ),
				'singular_name' => __( 'Testimonial', 'concealed1791' ),
				'add_new_item'  => __( 'Add New Testimonial', 'concealed1791' ),
				'edit_item'     => __( 'Edit Testimonial', 'concealed1791' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-format-quote',
			'menu_position'       => 7,
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);

	register_post_type(
		'c1791_package',
		array(
			'labels'              => array(
				'name'          => __( 'Packages', 'concealed1791' ),
				'singular_name' => __( 'Package', 'concealed1791' ),
				'add_new_item'  => __( 'Add New Package', 'concealed1791' ),
				'edit_item'     => __( 'Edit Package', 'concealed1791' ),
				'all_items'     => __( 'All Packages', 'concealed1791' ),
			),
			'description'         => __( 'Training packages shown as pricing cards on the front page.', 'concealed1791' ),
			'public'              => false,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-tickets-alt',
			'menu_position'       => 8,
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'excerpt', 'page-attributes' ),
		)
	);

	register_post_type(
		'c1791_faq',
		array(
			'labels'              => array(
				'name'          => __( 'FAQs', 'concealed1791' ),
				'singular_name' => __( 'FAQ', 'concealed1791' ),
				'add_new_item'  => __( 'Add New Question', 'concealed1791' ),
				'edit_item'     => __( 'Edit Question', 'concealed1791' ),
				'all_items'     => __( 'All Questions', 'concealed1791' ),
			),
			'description'         => __( 'The title is the question; the content is the answer.', 'concealed1791' ),
			'public'              => false,
			'show_ui'             => true,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-editor-help',
			'menu_position'       => 9,
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'editor', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'c1791_register_content_types' );

/**
 * Flush rewrite rules when the theme is activated so /classes/ works at once.
 */
function c1791_flush_rewrites() {
	c1791_register_content_types();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'c1791_flush_rewrites' );

/**
 * Class archives list upcoming classes soonest first; ?when=past lists past ones.
 *
 * @param WP_Query $query Main query.
 */
function c1791_class_archive_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( ! $query->is_post_type_archive( 'c1791_class' ) && ! $query->is_tax( 'c1791_class_type' ) ) {
		return;
	}

	$past = isset( $_GET['when'] ) && 'past' === sanitize_key( wp_unslash( $_GET['when'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$query->set( 'meta_key', '_c1791_start_date' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
	$query->set( 'orderby', 'meta_value' );
	$query->set( 'order', $past ? 'DESC' : 'ASC' );
	$query->set( 'posts_per_page', 20 );
	$query->set(
		'meta_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		array(
			array(
				'key'     => '_c1791_start_date',
				'value'   => current_time( 'Y-m-d' ),
				'compare' => $past ? '<' : '>=',
				'type'    => 'CHAR', // Y-m-d strings sort and compare correctly as text.
			),
		)
	);
}
add_action( 'pre_get_posts', 'c1791_class_archive_query' );

/**
 * Query upcoming classes.
 *
 * @param int $count Number of classes.
 * @return WP_Query
 */
function c1791_upcoming_classes( $count = 5 ) {
	return new WP_Query(
		array(
			'post_type'           => 'c1791_class',
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_key'            => '_c1791_start_date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'             => 'meta_value',
			'order'               => 'ASC',
			'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_c1791_start_date',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '>=',
					'type'    => 'CHAR',
				),
			),
		)
	);
}

/**
 * The next upcoming class that still has seats, or null.
 *
 * @return WP_Post|null
 */
function c1791_next_open_class() {
	$query = c1791_upcoming_classes( 10 );
	foreach ( $query->posts as $post ) {
		$class = c1791_class( $post->ID );
		if ( null === $class['seats'] || $class['seats'] > 0 ) {
			return $post;
		}
	}
	return null;
}

/**
 * Admin list columns for classes.
 *
 * @param array $columns Columns.
 * @return array
 */
function c1791_class_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['c1791_date']  = __( 'Class Date', 'concealed1791' );
			$new['c1791_seats'] = __( 'Seats Left', 'concealed1791' );
		}
	}
	return $new;
}
add_filter( 'manage_c1791_class_posts_columns', 'c1791_class_columns' );

/**
 * Render admin list columns for classes.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function c1791_class_column_content( $column, $post_id ) {
	if ( 'c1791_date' === $column ) {
		$date = get_post_meta( $post_id, '_c1791_start_date', true );
		echo $date ? esc_html( date_i18n( get_option( 'date_format' ), strtotime( $date ) ) ) : '&mdash;';
	} elseif ( 'c1791_seats' === $column ) {
		$class = c1791_class( $post_id );
		echo null === $class['seats'] ? '&mdash;' : esc_html( (string) $class['seats'] );
	}
}
add_action( 'manage_c1791_class_posts_custom_column', 'c1791_class_column_content', 10, 2 );

/**
 * Sort packages, FAQs and testimonials by their Order field in the admin list.
 *
 * @param WP_Query $query Query.
 */
function c1791_admin_menu_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || isset( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	if ( in_array( $query->get( 'post_type' ), array( 'c1791_package', 'c1791_faq', 'c1791_testimonial', 'c1791_instructor' ), true ) ) {
		$query->set( 'orderby', 'menu_order title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'c1791_admin_menu_order' );
