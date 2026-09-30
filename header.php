<?php
/**
 * Site header.
 *
 * @package Concealed1791
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'concealed1791' ); ?></a>

<?php
// An Elementor Pro Theme Builder header replaces the theme header when one applies.
if ( ! ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'header' ) ) ) {
	get_template_part( 'template-parts/header/' . ( c1791_is_minimal_header() ? 'minimal' : 'site-header' ) );
}
?>

<main id="main" class="ct-main">
