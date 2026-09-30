<?php
/**
 * Instructor directory.
 *
 * @package Concealed1791
 */

get_header();
c1791_page_hero( __( 'Our Instructors', 'concealed1791' ), __( 'Certified, experienced and patient. Meet the people who will coach you.', 'concealed1791' ), __( 'Meet the team', 'concealed1791' ) );
?>
<div class="ct-section">
	<div class="ct-container">
		<?php if ( have_posts() ) : ?>
			<div class="ct-grid ct-grid--3">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/instructor-card' );
				endwhile;
				?>
			</div>
			<?php c1791_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
