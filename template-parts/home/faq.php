<?php
/**
 * FAQ: heading and contact prompt beside an accordion.
 *
 * @package Concealed1791
 */

if ( ! c1791_mod( 'faq_enable' ) ) {
	return;
}
$c1791_faqs = c1791_faqs( max( 1, (int) c1791_mod( 'faq_count' ) ) );
if ( ! $c1791_faqs ) {
	return;
}
$c1791_page = get_pages(
	array(
		'meta_key'   => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value' => 'page-templates/faq.php', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		'number'     => 1,
	)
);
?>
<section class="ct-section" id="faq">
	<div class="ct-container ct-faq-page">
		<div class="ct-faq-page__intro">
			<?php c1791_section_head( c1791_mod( 'faq_eyebrow' ), c1791_mod( 'faq_title' ), c1791_mod( 'faq_text' ), false ); ?>
			<div class="ct-button-row">
				<?php if ( $c1791_page ) : ?>
					<a class="ct-btn ct-btn--outline" href="<?php echo esc_url( get_permalink( $c1791_page[0] ) ); ?>"><?php esc_html_e( 'All Questions', 'concealed1791' ); ?></a>
				<?php endif; ?>
				<?php c1791_phone_link( 'ct-callout__phone' ); ?>
			</div>
		</div>
		<?php c1791_faq_list( $c1791_faqs ); ?>
	</div>
</section>
