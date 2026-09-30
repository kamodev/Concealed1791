<?php
/**
 * Instructor card.
 *
 * @package Concealed1791
 */

$c1791_role    = get_post_meta( get_the_ID(), '_c1791_role', true );
$c1791_creds   = array_filter( array_map( 'trim', preg_split( '/[·|,]/u', (string) get_post_meta( get_the_ID(), '_c1791_credentials', true ) ) ) );
?>
<article <?php post_class( 'ct-card ct-instructor' ); ?>>
	<a class="ct-instructor__photo" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'c1791-portrait', array( 'alt' => '' ) );
		} else {
			echo '<span class="ct-placeholder ct-art ct-art--target">' . c1791_get_icon( 'user' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		}
		?>
	</a>
	<div class="ct-card__body">
		<h3 class="ct-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $c1791_role ) : ?>
			<p class="ct-instructor__role"><?php echo esc_html( $c1791_role ); ?></p>
		<?php endif; ?>
		<?php if ( $c1791_creds ) : ?>
			<ul class="ct-chips">
				<?php foreach ( $c1791_creds as $c1791_cred ) : ?>
					<li><?php echo esc_html( $c1791_cred ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</article>
