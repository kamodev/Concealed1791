<?php
/**
 * Content without the theme's page banner or sidebar (Elementor layouts and
 * FunnelKit funnel steps).
 *
 * Elementor layouts run edge to edge because Elementor sets its own widths.
 * Other content (for example a funnel step written in the block editor)
 * keeps a centered column so it doesn't touch the screen edges.
 *
 * @package Concealed1791
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'ct-bare' ); ?>>
	<?php if ( c1791_is_built_with_elementor( get_the_ID() ) ) : ?>
		<?php the_content(); ?>
	<?php else : ?>
		<div class="ct-section">
			<div class="ct-container">
				<div class="entry-content ct-bare__content"><?php the_content(); ?></div>
			</div>
		</div>
	<?php endif; ?>
</article>
