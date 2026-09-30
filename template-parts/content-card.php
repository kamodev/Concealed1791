<?php
/**
 * Post card (blog, archives, search, front page articles).
 *
 * @package Concealed1791
 */

$c1791_terms = 'post' === get_post_type() ? get_the_category() : array();
?>
<article <?php post_class( 'ct-card ct-post-card' ); ?>>
	<a class="ct-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'c1791-card', array( 'alt' => '' ) );
		} else {
			echo '<span class="ct-placeholder ct-art ct-art--target">' . c1791_get_icon( 'book' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		}
		?>
	</a>
	<div class="ct-card__body">
		<?php if ( $c1791_terms ) : ?>
			<span class="ct-card__tag"><?php echo esc_html( $c1791_terms[0]->name ); ?></span>
		<?php endif; ?>
		<h3 class="ct-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="ct-card__meta"><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></div>
		<?php endif; ?>
		<div class="ct-card__excerpt"><?php the_excerpt(); ?></div>
		<a class="ct-more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'concealed1791' ); ?><?php c1791_icon( 'arrow' ); ?><span class="screen-reader-text"> <?php the_title(); ?></span></a>
	</div>
</article>
