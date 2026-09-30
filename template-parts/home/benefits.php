<?php
/**
 * Benefits grid: icon, label, title and text for up to six reasons to train here.
 *
 * @package Concealed1791
 */

if ( ! c1791_mod( 'benefits_enable' ) ) {
	return;
}
$c1791_benefits = array();
for ( $c1791_i = 1; $c1791_i <= 6; $c1791_i++ ) {
	if ( c1791_mod( "benefit{$c1791_i}_title" ) ) {
		$c1791_benefits[] = $c1791_i;
	}
}
if ( ! $c1791_benefits ) {
	return;
}
?>
<section class="ct-section ct-benefits" id="why-us">
	<div class="ct-container">
		<?php c1791_section_head( c1791_mod( 'benefits_eyebrow' ), c1791_mod( 'benefits_title' ), c1791_mod( 'benefits_text' ) ); ?>
		<div class="ct-grid ct-grid--3">
			<?php foreach ( $c1791_benefits as $c1791_i ) : ?>
				<article class="ct-benefit">
					<div class="ct-benefit__head">
						<span class="ct-benefit__icon"><?php c1791_icon( c1791_sanitize_icon( c1791_mod( "benefit{$c1791_i}_icon" ) ) ); ?></span>
						<?php if ( c1791_mod( "benefit{$c1791_i}_tag" ) ) : ?>
							<span class="ct-benefit__tag"><?php echo esc_html( c1791_mod( "benefit{$c1791_i}_tag" ) ); ?></span>
						<?php endif; ?>
					</div>
					<h3 class="ct-benefit__title"><?php echo esc_html( c1791_mod( "benefit{$c1791_i}_title" ) ); ?></h3>
					<p><?php echo esc_html( c1791_mod( "benefit{$c1791_i}_text" ) ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
