<?php
/**
 * How it works: three numbered steps.
 *
 * @package Concealed1791
 */

if ( ! c1791_mod( 'steps_enable' ) ) {
	return;
}
?>
<section class="ct-section ct-steps">
	<div class="ct-container">
		<?php c1791_section_head( c1791_mod( 'steps_eyebrow' ), c1791_mod( 'steps_title' ) ); ?>
		<ol class="ct-steps__list">
			<?php for ( $c1791_i = 1; $c1791_i <= 3; $c1791_i++ ) : ?>
				<?php
				if ( ! c1791_mod( "step{$c1791_i}_title" ) ) {
					continue;
				}
				?>
				<li class="ct-step">
					<span class="ct-step__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $c1791_i ) ); ?></span>
					<h3 class="ct-step__title"><?php echo esc_html( c1791_mod( "step{$c1791_i}_title" ) ); ?></h3>
					<p><?php echo esc_html( c1791_mod( "step{$c1791_i}_text" ) ); ?></p>
				</li>
			<?php endfor; ?>
		</ol>
	</div>
</section>
