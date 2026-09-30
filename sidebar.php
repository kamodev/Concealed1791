<?php
/**
 * Sidebar for posts, pages, archives and the shop.
 *
 * Whether it shows, on which side and with which widget area is decided by
 * c1791_get_sidebar() from Appearance → Theme Settings → Sidebars and the
 * per-post "Sidebar" box.
 *
 * @package Concealed1791
 */

$c1791_sidebar = c1791_get_sidebar();
if ( ! $c1791_sidebar ) {
	return;
}
?>
<aside class="ct-sidebar ct-sidebar--<?php echo esc_attr( $c1791_sidebar['position'] ); ?>" aria-label="<?php esc_attr_e( 'Sidebar', 'concealed1791' ); ?>">
	<div class="ct-sidebar__inner">
		<?php dynamic_sidebar( $c1791_sidebar['id'] ); ?>
	</div>
</aside>
