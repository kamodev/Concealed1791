<?php
/**
 * Newsletter band (MailPoet list or form, or any provider's form URL).
 *
 * @package Concealed1791
 */

if ( ! c1791_mod( 'news_enable' ) ) {
	return;
}
?>
<section class="ct-section ct-newsband" id="newsletter">
	<div class="ct-container ct-newsband__inner">
		<div class="ct-newsband__copy">
			<span class="ct-newsband__icon"><?php c1791_icon( 'email' ); ?></span>
			<div>
				<h2><?php echo esc_html( c1791_mod( 'news_title' ) ); ?></h2>
				<p><?php echo esc_html( c1791_mod( 'news_text' ) ); ?></p>
			</div>
		</div>
		<div class="ct-newsband__form"><?php c1791_newsletter_form(); ?></div>
	</div>
</section>
