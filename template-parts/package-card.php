<?php
/**
 * Package pricing card.
 *
 * Expects $args['package'] from c1791_package(). The second price shows when
 * visitors flip the pricing switch; packages without one keep their price.
 *
 * @package Concealed1791
 */

$c1791_p = isset( $args['package'] ) ? $args['package'] : null;
if ( ! $c1791_p ) {
	return;
}
$c1791_has_alt = '' !== $c1791_p['price_alt'];
?>
<article class="ct-plan<?php echo $c1791_p['featured'] ? ' is-featured' : ''; ?>">
	<?php if ( $c1791_p['featured'] && $c1791_p['badge'] ) : ?>
		<span class="ct-plan__badge"><?php c1791_icon( 'star' ); ?><?php echo esc_html( $c1791_p['badge'] ); ?></span>
	<?php endif; ?>
	<h3 class="ct-plan__title"><?php echo esc_html( $c1791_p['title'] ); ?></h3>
	<?php if ( $c1791_p['summary'] ) : ?>
		<p class="ct-plan__summary"><?php echo esc_html( $c1791_p['summary'] ); ?></p>
	<?php endif; ?>

	<div class="ct-plan__price ct-price">
		<span data-plan-show="1">
			<?php c1791_price_html( $c1791_p['price'] ); ?>
			<?php if ( $c1791_p['period'] ) : ?>
				<span class="ct-price__period"><?php echo esc_html( $c1791_p['period'] ); ?></span>
			<?php endif; ?>
		</span>
		<?php if ( $c1791_has_alt ) : ?>
			<span data-plan-show="2" hidden>
				<?php c1791_price_html( $c1791_p['price_alt'] ); ?>
				<?php if ( $c1791_p['period_alt'] ) : ?>
					<span class="ct-price__period"><?php echo esc_html( $c1791_p['period_alt'] ); ?></span>
				<?php endif; ?>
			</span>
		<?php endif; ?>
	</div>

	<?php if ( $c1791_p['features'] ) : ?>
		<ul class="ct-plan__features">
			<?php foreach ( $c1791_p['features'] as $c1791_feature ) : ?>
				<li class="<?php echo $c1791_feature['included'] ? 'is-in' : 'is-out'; ?>">
					<?php c1791_icon( $c1791_feature['included'] ? 'check-circle' : 'x-circle' ); ?>
					<span><?php echo esc_html( $c1791_feature['text'] ); ?></span>
					<?php if ( ! $c1791_feature['included'] ) : ?>
						<span class="screen-reader-text"><?php esc_html_e( '(not included)', 'concealed1791' ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<a class="ct-btn ct-btn--block<?php echo $c1791_p['featured'] ? '' : ' ct-btn--outline'; ?>" href="<?php echo esc_url( $c1791_p['url'] ); ?>" data-url-1="<?php echo esc_url( $c1791_p['url'] ); ?>" data-url-2="<?php echo esc_url( $c1791_has_alt ? $c1791_p['url_alt'] : $c1791_p['url'] ); ?>"<?php echo $c1791_p['nofollow'] ? ' rel="nofollow"' : ''; ?>><?php echo esc_html( $c1791_p['button'] ); ?></a>
</article>
