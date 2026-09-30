<?php
/**
 * Template Name: Full Width (no title banner)
 * Template Post Type: page
 *
 * Edge-to-edge canvas under the site header, for block-built landing pages.
 *
 * @package Concealed1791
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'ct-canvas' ); ?>>
		<div class="entry-content ct-canvas__content"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;

get_footer();
