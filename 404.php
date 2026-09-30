<?php
/**
 * Not found.
 *
 * @package Concealed1791
 */

get_header();
c1791_page_hero( __( 'Off target', 'concealed1791' ), __( 'We couldn\'t find that page. It may have moved, or the class may have already happened.', 'concealed1791' ), '404' );
?>
<div class="ct-section">
	<div class="ct-container ct-narrow">
		<?php get_search_form(); ?>
		<div class="ct-button-row">
			<a class="ct-btn" href="<?php echo esc_url( get_post_type_archive_link( 'c1791_class' ) ); ?>"><?php esc_html_e( 'See Upcoming Classes', 'concealed1791' ); ?></a>
			<a class="ct-btn ct-btn--outline" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'concealed1791' ); ?></a>
		</div>
	</div>
</div>
<?php
get_footer();
