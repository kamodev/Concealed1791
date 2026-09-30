<?php
/**
 * Search form.
 *
 * @package Concealed1791
 */

$c1791_id = wp_unique_id( 'ct-search-' );
?>
<form role="search" method="get" class="ct-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $c1791_id ); ?>"><?php esc_html_e( 'Search for:', 'concealed1791' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $c1791_id ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search classes, articles…', 'concealed1791' ); ?>">
	<button type="submit"><?php c1791_icon( 'search' ); ?><span class="screen-reader-text"><?php esc_html_e( 'Search', 'concealed1791' ); ?></span></button>
</form>
