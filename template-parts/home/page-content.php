<?php
/**
 * Content of the static front page, when it has any.
 *
 * @package Concealed1791
 */

if ( ! is_page() ) {
	return;
}
$c1791_post = get_queried_object();
if ( ! $c1791_post || '' === trim( (string) $c1791_post->post_content ) ) {
	return;
}
?>
<section class="ct-section ct-home-content">
	<div class="ct-container">
		<div class="entry-content">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			?>
		</div>
	</div>
</section>
