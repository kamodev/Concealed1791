<?php
/**
 * Full site header: announcement bar, top bar, logo, menu, phone, search,
 * shop icons and the booking button. Everything here is set in
 * Appearance → Theme Settings → Header & Menus.
 *
 * @package Concealed1791
 */

$c1791_cta_text = c1791_setting( 'header_cta_text' );
$c1791_cta_url  = c1791_link( c1791_setting( 'header_cta_url' ) );
$c1791_mobile   = has_nav_menu( 'mobile' );
?>
<?php if ( c1791_enabled( 'announce_enable' ) && c1791_setting( 'announce_text' ) ) : ?>
	<div class="ct-announcement" id="ct-announcement" data-key="<?php echo esc_attr( md5( c1791_setting( 'announce_text' ) ) ); ?>">
		<div class="ct-container">
			<p>
				<?php c1791_icon( 'bolt' ); ?>
				<span><?php echo esc_html( c1791_setting( 'announce_text' ) ); ?></span>
				<?php if ( c1791_setting( 'announce_link' ) && c1791_setting( 'announce_link_text' ) ) : ?>
					<a href="<?php echo esc_url( c1791_link( c1791_setting( 'announce_link' ) ) ); ?>"><?php echo esc_html( c1791_setting( 'announce_link_text' ) ); ?> <span aria-hidden="true">&rarr;</span></a>
				<?php endif; ?>
			</p>
			<button type="button" class="ct-announcement__close" aria-label="<?php esc_attr_e( 'Dismiss announcement', 'concealed1791' ); ?>"><?php c1791_icon( 'close' ); ?></button>
		</div>
	</div>
<?php endif; ?>

<?php if ( c1791_enabled( 'topbar_enable' ) ) : ?>
	<div class="ct-topbar">
		<div class="ct-container ct-topbar__inner">
			<?php if ( c1791_setting( 'topbar_text' ) ) : ?>
				<p class="ct-topbar__msg"><?php c1791_icon( 'badge' ); ?><span><?php echo esc_html( c1791_setting( 'topbar_text' ) ); ?></span></p>
			<?php endif; ?>
			<div class="ct-topbar__right">
				<?php if ( c1791_enabled( 'topbar_contact' ) ) : ?>
					<?php c1791_phone_link( 'ct-topbar__link' ); ?>
					<?php if ( c1791_setting( 'email' ) ) : ?>
						<a class="ct-topbar__link" href="<?php echo esc_url( 'mailto:' . antispambot( c1791_setting( 'email' ) ) ); ?>"><?php c1791_icon( 'email' ); ?><span><?php echo esc_html( antispambot( c1791_setting( 'email' ) ) ); ?></span></a>
					<?php endif; ?>
				<?php endif; ?>
				<?php
				wp_nav_menu(
					array(
						'theme_location'       => 'topbar',
						'container'            => 'nav',
						'container_class'      => 'ct-topbar__nav',
						'container_aria_label' => __( 'Top bar', 'concealed1791' ),
						'depth'                => 1,
						'fallback_cb'          => false,
					)
				);
				?>
				<?php
				if ( c1791_enabled( 'topbar_social' ) ) {
					c1791_social_links( 'ct-social--sm' );
				}
				?>
			</div>
		</div>
	</div>
<?php endif; ?>

<header class="ct-header" id="masthead">
	<div class="ct-container ct-header__inner">
		<div class="ct-header__brand"><?php c1791_brand(); ?></div>

		<nav class="ct-nav<?php echo $c1791_mobile ? ' has-mobile-menu' : ''; ?>" id="ct-nav" aria-label="<?php esc_attr_e( 'Main', 'concealed1791' ); ?>">
			<div class="ct-nav__head">
				<?php c1791_brand( false ); ?>
				<button type="button" class="ct-icon-btn ct-nav__close" aria-label="<?php esc_attr_e( 'Close menu', 'concealed1791' ); ?>"><?php c1791_icon( 'close' ); ?></button>
			</div>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu ct-nav__primary',
					'fallback_cb'    => 'c1791_menu_fallback',
					'depth'          => 3,
				)
			);
			if ( $c1791_mobile ) {
				wp_nav_menu(
					array(
						'theme_location' => 'mobile',
						'container'      => false,
						'menu_class'     => 'menu ct-nav__mobile',
						'depth'          => 2,
					)
				);
			}
			?>
			<div class="ct-nav__foot">
				<?php if ( $c1791_cta_text ) : ?>
					<a class="ct-btn ct-btn--block" href="<?php echo esc_url( $c1791_cta_url ); ?>"><?php echo esc_html( $c1791_cta_text ); ?></a>
				<?php endif; ?>
				<?php c1791_phone_link( 'ct-nav__phone' ); ?>
			</div>
		</nav>

		<div class="ct-header__actions">
			<?php
			if ( c1791_enabled( 'header_phone' ) ) {
				c1791_phone_link( 'ct-header__phone', __( 'Call or text', 'concealed1791' ) );
			}
			?>
			<?php if ( c1791_enabled( 'header_search' ) ) : ?>
				<button type="button" class="ct-icon-btn ct-search-toggle" aria-controls="ct-search-panel" aria-expanded="false">
					<span class="screen-reader-text"><?php esc_html_e( 'Search', 'concealed1791' ); ?></span>
					<?php c1791_icon( 'search' ); ?>
				</button>
			<?php endif; ?>
			<?php
			/**
			 * Extra header icons (the WooCommerce integration adds account and cart).
			 */
			do_action( 'c1791_header_actions' );
			?>
			<?php if ( $c1791_cta_text ) : ?>
				<a class="ct-btn ct-btn--sm ct-header__cta" href="<?php echo esc_url( $c1791_cta_url ); ?>"><?php echo esc_html( $c1791_cta_text ); ?></a>
			<?php endif; ?>
			<button type="button" class="ct-icon-btn ct-menu-toggle" aria-controls="ct-nav" aria-expanded="false">
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'concealed1791' ); ?></span>
				<?php c1791_icon( 'menu' ); ?>
			</button>
		</div>
	</div>

	<?php if ( c1791_enabled( 'header_search' ) ) : ?>
		<div class="ct-search-panel" id="ct-search-panel" hidden>
			<div class="ct-container"><?php get_search_form(); ?></div>
		</div>
	<?php endif; ?>
</header>
<div class="ct-nav-backdrop" hidden></div>
