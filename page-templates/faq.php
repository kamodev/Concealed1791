<?php
/**
 * Template Name: FAQ
 * Template Post Type: page
 *
 * The page content, then every question from FAQs as an accordion (with
 * FAQPage structured data), then a contact prompt.
 *
 * @package Concealed1791
 */

get_header();

while ( have_posts() ) :
	the_post();
	c1791_page_hero( get_the_title(), has_excerpt() ? get_the_excerpt() : '' );
	?>
	<div class="ct-section">
		<div class="ct-container ct-faq-page">
			<div class="ct-faq-page__intro">
				<?php if ( get_the_content() ) : ?>
					<div class="entry-content"><?php the_content(); ?></div>
				<?php endif; ?>
				<div class="ct-callout">
					<h2><?php esc_html_e( 'Still have questions?', 'concealed1791' ); ?></h2>
					<p><?php esc_html_e( 'We\'re happy to help you pick the right class.', 'concealed1791' ); ?></p>
					<?php c1791_phone_link( 'ct-callout__phone' ); ?>
				</div>
			</div>
			<?php c1791_faq_list( c1791_faqs() ); ?>
		</div>
	</div>
	<?php
endwhile;

get_footer();
