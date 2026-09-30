<?php
/**
 * Single class: description, what's included, what to bring, instructors,
 * and a sticky booking box. The Amelia integration adds its booking form.
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

	$c1791_c        = c1791_class();
	$c1791_types    = get_the_terms( get_the_ID(), 'c1791_class_type' );
	$c1791_includes = c1791_lines( $c1791_c['includes'] );
	$c1791_bring    = c1791_lines( $c1791_c['bring'] );
	$c1791_meta     = esc_html( c1791_class_dates( $c1791_c ) ) . ( $c1791_c['location'] ? ' &middot; ' . esc_html( $c1791_c['location'] ) : '' );

	c1791_page_hero( get_the_title(), has_excerpt() ? get_the_excerpt() : '', $c1791_types && ! is_wp_error( $c1791_types ) ? $c1791_types[0]->name : __( 'Class', 'concealed1791' ), $c1791_meta );
	?>
	<div class="ct-section">
		<div class="ct-container ct-layout ct-layout--right ct-layout--sticky ct-layout--class">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'ct-layout__main' ); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="ct-featured"><?php the_post_thumbnail( 'large' ); ?></figure>
				<?php endif; ?>

				<div class="entry-content">
					<?php the_content(); ?>

					<?php if ( $c1791_includes ) : ?>
						<h2><?php esc_html_e( 'What\'s Included', 'concealed1791' ); ?></h2>
						<ul class="ct-checklist ct-checklist--cols">
							<?php foreach ( $c1791_includes as $c1791_item ) : ?>
								<li><?php c1791_icon( 'check-circle' ); ?><span><?php echo esc_html( $c1791_item ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $c1791_c['prereqs'] ) : ?>
						<div class="ct-callout ct-callout--note">
							<h3><?php esc_html_e( 'Prerequisites', 'concealed1791' ); ?></h3>
							<?php echo wp_kses_post( wpautop( esc_html( $c1791_c['prereqs'] ) ) ); ?>
						</div>
					<?php endif; ?>

					<?php if ( $c1791_bring ) : ?>
						<h2><?php esc_html_e( 'What to Bring', 'concealed1791' ); ?></h2>
						<ul class="ct-checklist">
							<?php foreach ( $c1791_bring as $c1791_item ) : ?>
								<li><?php c1791_icon( 'check' ); ?><span><?php echo esc_html( $c1791_item ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>

				<?php
				/**
				 * After the class description (the Amelia integration adds its booking form here).
				 *
				 * @param array $c1791_c Class details.
				 */
				do_action( 'c1791_class_after_content', $c1791_c );
				?>

				<?php if ( $c1791_c['instructors'] ) : ?>
					<section class="ct-class-instructors" aria-labelledby="ct-instructors-title">
						<h2 id="ct-instructors-title"><?php esc_html_e( 'Your Instructors', 'concealed1791' ); ?></h2>
						<div class="ct-grid ct-grid--3">
							<?php
							$c1791_instructors = new WP_Query(
								array(
									'post_type'      => 'c1791_instructor',
									'post__in'       => $c1791_c['instructors'],
									'orderby'        => 'post__in',
									'posts_per_page' => count( $c1791_c['instructors'] ),
									'no_found_rows'  => true,
								)
							);
							while ( $c1791_instructors->have_posts() ) :
								$c1791_instructors->the_post();
								get_template_part( 'template-parts/instructor-card' );
							endwhile;
							wp_reset_postdata();
							?>
						</div>
					</section>
				<?php endif; ?>
			</article>

			<aside class="ct-sidebar" aria-label="<?php esc_attr_e( 'Class details', 'concealed1791' ); ?>">
				<div class="ct-sidebar__inner">
					<div class="ct-booking-box">
						<?php if ( $c1791_c['price'] ) : ?>
							<div class="ct-booking-box__price ct-price"><?php c1791_price_html( $c1791_c['price'] ); ?></div>
						<?php endif; ?>
						<dl>
							<dt><?php c1791_icon( 'calendar' ); ?><?php esc_html_e( 'Date', 'concealed1791' ); ?></dt>
							<dd><?php echo esc_html( c1791_class_dates( $c1791_c ) ); ?></dd>
							<?php if ( $c1791_c['time'] ) : ?>
								<dt><?php c1791_icon( 'clock' ); ?><?php esc_html_e( 'Time', 'concealed1791' ); ?></dt>
								<dd><?php echo esc_html( $c1791_c['time'] ); ?></dd>
							<?php endif; ?>
							<?php if ( $c1791_c['duration'] ) : ?>
								<dt><?php c1791_icon( 'clock' ); ?><?php esc_html_e( 'Length', 'concealed1791' ); ?></dt>
								<dd><?php echo esc_html( $c1791_c['duration'] ); ?></dd>
							<?php endif; ?>
							<?php if ( $c1791_c['location'] ) : ?>
								<dt><?php c1791_icon( 'pin' ); ?><?php esc_html_e( 'Where', 'concealed1791' ); ?></dt>
								<dd><?php echo esc_html( $c1791_c['location'] ); ?></dd>
							<?php endif; ?>
							<dt><?php c1791_icon( 'target' ); ?><?php esc_html_e( 'Level', 'concealed1791' ); ?></dt>
							<dd><?php c1791_level_badge( $c1791_c ); ?></dd>
							<?php $c1791_names = c1791_class_instructor_names( $c1791_c ); ?>
							<?php if ( $c1791_names ) : ?>
								<dt><?php c1791_icon( 'user' ); ?><?php esc_html_e( 'Instructor', 'concealed1791' ); ?></dt>
								<dd><?php echo esc_html( $c1791_names ); ?></dd>
							<?php endif; ?>
						</dl>
						<?php if ( null !== $c1791_c['seats'] ) : ?>
							<div class="ct-booking-box__seats">
								<?php c1791_seats_badge( $c1791_c ); ?>
								<?php c1791_seats_meter( $c1791_c ); ?>
							</div>
						<?php endif; ?>
						<?php c1791_register_button( $c1791_c, 'ct-btn--block', __( 'Reserve My Seat', 'concealed1791' ) ); ?>
						<p class="ct-booking-box__note"><?php c1791_icon( 'lock' ); ?><?php esc_html_e( 'Secure registration · Instant confirmation', 'concealed1791' ); ?></p>
						<?php c1791_phone_link( 'ct-booking-box__phone', __( 'Questions? Call', 'concealed1791' ) ); ?>
					</div>
				</div>
			</aside>
		</div>
	</div>
	<?php
endwhile;

get_footer();
