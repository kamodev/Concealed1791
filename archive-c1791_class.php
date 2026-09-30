<?php
/**
 * Class schedule (also used for class type archives).
 *
 * @package Concealed1791
 */

get_header();

$c1791_past    = isset( $_GET['when'] ) && 'past' === sanitize_key( wp_unslash( $_GET['when'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$c1791_current = is_tax( 'c1791_class_type' ) ? get_queried_object() : null;
$c1791_archive = get_post_type_archive_link( 'c1791_class' );

if ( $c1791_current ) {
	c1791_page_hero( $c1791_current->name, $c1791_current->description, __( 'Class schedule', 'concealed1791' ) );
} else {
	c1791_page_hero( __( 'Class Schedule', 'concealed1791' ), __( 'Concealed carry certification, defensive shooting, home defense and CPR classes. Seats are limited — reserve yours early.', 'concealed1791' ), __( 'Train with us', 'concealed1791' ) );
}

$c1791_types = get_terms(
	array(
		'taxonomy'   => 'c1791_class_type',
		'hide_empty' => true,
	)
);
?>
<div class="ct-section">
	<div class="ct-container">
		<nav class="ct-filters" aria-label="<?php esc_attr_e( 'Filter classes', 'concealed1791' ); ?>">
			<a class="ct-filter<?php echo ! $c1791_current && ! $c1791_past ? ' is-active' : ''; ?>" href="<?php echo esc_url( $c1791_archive ); ?>"<?php echo ! $c1791_current && ! $c1791_past ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'All upcoming', 'concealed1791' ); ?></a>
			<?php if ( ! is_wp_error( $c1791_types ) ) : ?>
				<?php foreach ( $c1791_types as $c1791_type ) : ?>
					<?php $c1791_on = $c1791_current && $c1791_current->term_id === $c1791_type->term_id; ?>
					<a class="ct-filter<?php echo $c1791_on ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $c1791_type ) ); ?>"<?php echo $c1791_on ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $c1791_type->name ); ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
			<a class="ct-filter<?php echo $c1791_past ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'when', 'past', $c1791_archive ) ); ?>"<?php echo $c1791_past ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Past classes', 'concealed1791' ); ?></a>
		</nav>

		<?php if ( have_posts() ) : ?>
			<div class="ct-schedule">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/class-row' );
				endwhile;
				?>
			</div>
			<?php c1791_pagination(); ?>
		<?php else : ?>
			<div class="ct-callout">
				<h2><?php esc_html_e( 'No classes scheduled right now', 'concealed1791' ); ?></h2>
				<p><?php esc_html_e( 'New dates are added regularly. Join our list to hear first, or call to set up a private lesson.', 'concealed1791' ); ?></p>
				<?php c1791_phone_link( 'ct-callout__phone' ); ?>
			</div>
		<?php endif; ?>

		<div class="ct-callout ct-callout--split">
			<div>
				<h2><?php esc_html_e( 'Private & group training', 'concealed1791' ); ?></h2>
				<p><?php esc_html_e( 'One-on-one lessons, couples, churches, businesses and groups: we\'ll build a class around your schedule and goals.', 'concealed1791' ); ?></p>
			</div>
			<div class="ct-button-row">
				<?php $c1791_contact = get_page_by_path( 'contact' ); ?>
				<?php if ( $c1791_contact ) : ?>
					<a class="ct-btn ct-btn--dark" href="<?php echo esc_url( get_permalink( $c1791_contact ) ); ?>"><?php esc_html_e( 'Request Private Training', 'concealed1791' ); ?></a>
				<?php endif; ?>
				<?php c1791_phone_link( 'ct-callout__phone' ); ?>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();
