<?php
/**
 * Certification strip: short trust signals with icons ("Text | icon" per line).
 *
 * @package Concealed1791
 */

if ( ! c1791_mod( 'trust_enable' ) ) {
	return;
}
$c1791_icons = array_keys( c1791_icon_choices() );
$c1791_items = array();
foreach ( c1791_lines( c1791_mod( 'trust_items' ) ) as $c1791_line ) {
	$c1791_parts   = array_map( 'trim', explode( '|', $c1791_line, 2 ) );
	$c1791_items[] = array(
		'text' => $c1791_parts[0],
		'icon' => isset( $c1791_parts[1] ) && in_array( $c1791_parts[1], $c1791_icons, true ) ? $c1791_parts[1] : 'check-circle',
	);
}
if ( ! $c1791_items ) {
	return;
}
?>
<section class="ct-trust" aria-label="<?php esc_attr_e( 'Certifications', 'concealed1791' ); ?>">
	<div class="ct-container">
		<ul class="ct-trust__list">
			<?php foreach ( $c1791_items as $c1791_item ) : ?>
				<li class="ct-trust__item"><span class="ct-trust__icon"><?php c1791_icon( $c1791_item['icon'] ); ?></span><span><?php echo esc_html( $c1791_item['text'] ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
