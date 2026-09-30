<?php
/**
 * WooCommerce template functions: header icons, cart count, class and
 * package checkout links, and the front page products section.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Header cart count badge.
 */
function c1791_cart_count() {
	$count = WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	printf( '<span class="ct-cart-count"%s>%d</span>', $count ? '' : ' hidden', (int) $count );
}

/**
 * Account and cart icons in the header (Theme Settings → Integrations).
 */
function c1791_wc_header_icons() {
	if ( c1791_enabled( 'wc_header_account' ) ) :
		?>
		<a class="ct-icon-btn ct-header__account" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
			<span class="screen-reader-text"><?php esc_html_e( 'My account', 'concealed1791' ); ?></span>
			<?php c1791_icon( 'user' ); ?>
		</a>
		<?php
	endif;
	if ( c1791_enabled( 'wc_header_cart' ) ) :
		?>
		<a class="ct-icon-btn ct-header__cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>">
			<span class="screen-reader-text"><?php esc_html_e( 'Cart', 'concealed1791' ); ?></span>
			<?php c1791_icon( 'cart' ); ?>
			<?php c1791_cart_count(); ?>
		</a>
		<?php
	endif;
}
add_action( 'c1791_header_actions', 'c1791_wc_header_icons' );

/**
 * Keep the header cart count fresh after AJAX add-to-cart.
 *
 * @param array $fragments Cart fragments.
 * @return array
 */
function c1791_cart_fragment( $fragments ) {
	ob_start();
	c1791_cart_count();
	$fragments['.ct-cart-count'] = ob_get_clean();
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'c1791_cart_fragment' );

/**
 * Product fields on classes and packages.
 *
 * @param array $fields Detail box fields.
 * @return array
 */
function c1791_wc_meta_fields( $fields ) {
	$help = __( 'The button adds this product to the cart and opens checkout (Theme Settings → Integrations). Stock sets the seats left when the product manages stock. A blank price uses the product price.', 'concealed1791' );

	$fields['c1791_class']['fields']['_c1791_wc_product']     = array(
		'label'     => __( 'WooCommerce product (registration)', 'concealed1791' ),
		'type'      => 'post_select',
		'post_type' => 'product',
		'help'      => $help,
	);
	$fields['c1791_package']['fields']['_c1791_wc_product']     = array(
		'label'     => __( 'WooCommerce product', 'concealed1791' ),
		'type'      => 'post_select',
		'post_type' => 'product',
		'help'      => $help,
	);
	$fields['c1791_package']['fields']['_c1791_wc_product_alt'] = array(
		'label'     => __( 'WooCommerce product for the second price', 'concealed1791' ),
		'type'      => 'post_select',
		'post_type' => 'product',
	);
	return $fields;
}
add_filter( 'c1791_meta_fields', 'c1791_wc_meta_fields' );

/**
 * A purchasable product by ID, or null.
 *
 * @param int $product_id Product ID.
 * @return WC_Product|null
 */
function c1791_wc_product( $product_id ) {
	$product = $product_id ? wc_get_product( $product_id ) : null;
	if ( ! $product || 'publish' !== $product->get_status() ) {
		return null;
	}
	return $product;
}

/**
 * Where a "buy" button for a product should go.
 *
 * Simple products go straight to checkout with the product in the cart
 * (unless turned off in Theme Settings → Integrations). Products that need
 * options chosen, such as variable products, open their product page.
 *
 * @param WC_Product $product Product.
 * @return string
 */
function c1791_wc_buy_url( $product ) {
	if ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && c1791_enabled( 'wc_direct_checkout' ) ) {
		return add_query_arg(
			array(
				'add-to-cart'    => $product->get_id(),
				'c1791-checkout' => 1,
			),
			wc_get_checkout_url()
		);
	}
	return $product->get_permalink();
}

/**
 * Plain-text price of a product, for example "$149.00".
 *
 * @param WC_Product $product Product.
 * @return string
 */
function c1791_wc_price_text( $product ) {
	if ( '' === $product->get_price() ) {
		return '';
	}
	return html_entity_decode( wp_strip_all_tags( wc_price( wc_get_price_to_display( $product ) ) ), ENT_QUOTES, get_bloginfo( 'charset' ) );
}

/**
 * After a direct-checkout link adds its product, reload checkout without the
 * add-to-cart query so refreshing the page doesn't add it again.
 *
 * @param string|false $url Redirect URL.
 * @return string|false
 */
function c1791_wc_direct_checkout_redirect( $url ) {
	if ( isset( $_GET['c1791-checkout'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only flag on WooCommerce's own add-to-cart link.
		return wc_get_checkout_url();
	}
	return $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'c1791_wc_direct_checkout_redirect' );

/**
 * Classes linked to a product: register through checkout, show the product
 * price when the class has none, and read seats from stock.
 *
 * @param array $data    Class details.
 * @param int   $post_id Class post ID.
 * @return array
 */
function c1791_wc_class_data( $data, $post_id ) {
	$product = c1791_wc_product( (int) get_post_meta( $post_id, '_c1791_wc_product', true ) );
	if ( ! $product ) {
		return $data;
	}
	$data['product']  = $product->get_id();
	$data['register'] = c1791_wc_buy_url( $product );
	$data['nofollow'] = true;
	if ( '' === (string) $data['price'] ) {
		$data['price'] = c1791_wc_price_text( $product );
	}
	if ( ! $product->is_in_stock() ) {
		$data['seats'] = 0;
	} elseif ( $product->managing_stock() && null !== $product->get_stock_quantity() ) {
		$data['seats'] = max( 0, (int) $product->get_stock_quantity() );
	}
	return $data;
}
add_filter( 'c1791_class_data', 'c1791_wc_class_data', 10, 2 );

/**
 * Packages linked to products: buttons go to checkout and blank prices use
 * the product prices.
 *
 * @param array $data    Package details.
 * @param int   $post_id Package post ID.
 * @return array
 */
function c1791_wc_package_data( $data, $post_id ) {
	$product = c1791_wc_product( (int) get_post_meta( $post_id, '_c1791_wc_product', true ) );
	$alt     = c1791_wc_product( (int) get_post_meta( $post_id, '_c1791_wc_product_alt', true ) );
	if ( $product ) {
		$data['url']      = c1791_wc_buy_url( $product );
		$data['nofollow'] = true;
		if ( '' === $data['price'] ) {
			$data['price'] = c1791_wc_price_text( $product );
		}
		if ( ! $alt && '' === (string) get_post_meta( $post_id, '_c1791_button_url_alt', true ) ) {
			$data['url_alt'] = $data['url'];
		}
	}
	if ( $alt ) {
		$data['url_alt']  = c1791_wc_buy_url( $alt );
		$data['nofollow'] = true;
		if ( '' === $data['price_alt'] ) {
			$data['price_alt'] = c1791_wc_price_text( $alt );
		}
	}
	return $data;
}
add_filter( 'c1791_package_data', 'c1791_wc_package_data', 10, 2 );

/**
 * Add the products section to the front page, before the articles.
 *
 * @param string[] $sections Front page sections.
 * @return string[]
 */
function c1791_wc_home_sections( $sections ) {
	$at = array_search( 'resources', $sections, true );
	array_splice( $sections, false === $at ? count( $sections ) : $at, 0, array( 'shop' ) );
	return $sections;
}
add_filter( 'c1791_front_page_sections', 'c1791_wc_home_sections' );

/**
 * Render the front page products section.
 */
function c1791_wc_home_section() {
	if ( c1791_mod( 'shop_enable' ) ) {
		load_template( C1791_WC_DIR . '/template-parts/home-shop.php', false );
	}
}
add_action( 'c1791_home_section_shop', 'c1791_wc_home_section' );
