<?php
/**
 * Packages: pricing cards with an optional two-way pricing switch (for
 * example "Individual" / "Bring a partner"). Cards come from Packages.
 *
 * @package Concealed1791
 */

if ( ! c1791_mod( 'packages_enable' ) ) {
	return;
}
$c1791_ids = get_posts(
	array(
		'post_type'      => 'c1791_package',
		'posts_per_page' => 6,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);
if ( ! $c1791_ids ) {
	return;
}
$c1791_packages = array_map( 'c1791_package', $c1791_ids );
$c1791_switch   = (bool) array_filter( wp_list_pluck( $c1791_packages, 'price_alt' ) );
?>
<section class="ct-section ct-pricing" id="packages" data-ct-pricing>
	<div class="ct-container">
		<?php c1791_section_head( c1791_mod( 'packages_eyebrow' ), c1791_mod( 'packages_title' ), c1791_mod( 'packages_text' ) ); ?>

		<?php if ( $c1791_switch ) : ?>
			<div class="ct-switch" role="group" aria-label="<?php esc_attr_e( 'Pricing', 'concealed1791' ); ?>">
				<button type="button" class="ct-switch__opt is-active" data-plan="1" aria-pressed="true"><?php echo esc_html( c1791_mod( 'packages_toggle_1' ) ); ?></button>
				<button type="button" class="ct-switch__opt" data-plan="2" aria-pressed="false"><?php echo esc_html( c1791_mod( 'packages_toggle_2' ) ); ?></button>
			</div>
		<?php endif; ?>

		<div class="ct-plans ct-plans--<?php echo (int) min( 3, count( $c1791_packages ) ); ?>">
			<?php
			foreach ( $c1791_packages as $c1791_package ) {
				get_template_part( 'template-parts/package-card', null, array( 'package' => $c1791_package ) );
			}
			?>
		</div>

		<?php if ( c1791_mod( 'packages_note' ) ) : ?>
			<p class="ct-pricing__note"><?php echo esc_html( c1791_mod( 'packages_note' ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
