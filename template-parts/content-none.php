<?php
/**
 * No results.
 *
 * @package Concealed1791
 */

?>
<div class="ct-callout">
	<h2><?php esc_html_e( 'Nothing found', 'concealed1791' ); ?></h2>
	<?php if ( is_search() ) : ?>
		<p><?php esc_html_e( 'No results matched your search. Try different words.', 'concealed1791' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'There\'s nothing here yet. Check back soon.', 'concealed1791' ); ?></p>
	<?php endif; ?>
</div>
