<?php
/**
 * Single instructor: bio, credentials and their upcoming classes. The Amelia
 * integration adds a private-session booking form.
 *
 * @package Concealed1791
 */

get_header();

while ( have_posts() ) :
	the_post();

	if ( c1791_is_bare_content() ) {
		get_template_part( 'template-parts/content', 'bare' );
		continue;
	}

	$c1791_id    = get_the_ID();
	$c1791_role  = get_post_meta( $c1791_id, '_c1791_role', true );
	$c1791_creds = array_filter( array_map( 'trim', preg_split( '/[·|,]/u', (string) get_post_meta( $c1791_id, '_c1791_credentials', true ) ) ) );

	c1791_page_hero( get_the_title(), '', $c1791_role );
	?>
	<div class="ct-section">
		<div class="ct-container ct-instructor-profile">
			<div class="ct-instructor-profile__photo">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'c1791-portrait' );
				} else {
					echo '<span class="ct-placeholder ct-art ct-art--target">' . c1791_get_icon( 'user' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
				}
				?>
				<?php if ( $c1791_creds ) : ?>
					<ul class="ct-chips">
						<?php foreach ( $c1791_creds as $c1791_cred ) : ?>
							<li><?php echo esc_html( $c1791_cred ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div class="entry-content"><?php the_content(); ?></div>
		</div>
	</div>

	<?php
	// Upcoming classes taught by this instructor. The instructor list is stored
	// serialized, so match the ID as an integer (i:12;) or a string ("12").
	$c1791_classes = new WP_Query(
		array(
			'post_type'      => 'c1791_class',
			'posts_per_page' => 6,
			'no_found_rows'  => true,
			'meta_key'       => '_c1791_start_date', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				'relation' => 'AND',
				array(
					'key'     => '_c1791_start_date',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '>=',
					'type'    => 'CHAR',
				),
				array(
					'relation' => 'OR',
					array(
						'key'     => '_c1791_instructors',
						'value'   => 'i:' . $c1791_id . ';',
						'compare' => 'LIKE',
					),
					array(
						'key'     => '_c1791_instructors',
						'value'   => '"' . $c1791_id . '"',
						'compare' => 'LIKE',
					),
				),
			),
		)
	);
	?>
	<?php if ( $c1791_classes->have_posts() ) : ?>
		<section class="ct-section ct-section--alt">
			<div class="ct-container">
				<?php
				/* translators: %s: instructor name. */
				c1791_section_head( __( 'On the schedule', 'concealed1791' ), sprintf( __( 'Upcoming classes with %s', 'concealed1791' ), get_the_title() ), '', false );
				?>
				<div class="ct-schedule">
					<?php
					while ( $c1791_classes->have_posts() ) :
						$c1791_classes->the_post();
						get_template_part( 'template-parts/class-row' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	/**
	 * After the instructor profile (the Amelia integration adds private-session booking).
	 *
	 * @param int $c1791_id Instructor post ID.
	 */
	do_action( 'c1791_instructor_after_content', $c1791_id );
endwhile;

get_footer();
