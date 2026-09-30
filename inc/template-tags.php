<?php
/**
 * Template helpers.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether WooCommerce is active.
 *
 * @return bool
 */
function c1791_has_woo() {
	return class_exists( 'WooCommerce' );
}

/**
 * Whether the current singular view outputs its content without the theme's
 * page banner, container or sidebar (page-builder layouts, funnel steps).
 *
 * @return bool
 */
function c1791_is_bare_content() {
	/**
	 * Filter whether to render bare content.
	 *
	 * @param bool $bare Default false.
	 */
	return is_singular() && (bool) apply_filters( 'c1791_bare_content', false );
}

/**
 * Whether to use the minimal, distraction-free header and footer
 * (logo only; used for checkout and funnel steps).
 *
 * @return bool
 */
function c1791_is_minimal_header() {
	/**
	 * Filter whether to use the minimal header and footer.
	 *
	 * @param bool $minimal Default false.
	 */
	return (bool) apply_filters( 'c1791_minimal_header', false );
}

/**
 * Non-empty, trimmed lines of a text.
 *
 * @param string $text Text.
 * @return string[]
 */
function c1791_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' ) );
}

/**
 * Class details as an array.
 *
 * @param int|null $post_id Post ID.
 * @return array
 */
function c1791_class( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$get     = function ( $key ) use ( $post_id ) {
		return get_post_meta( $post_id, $key, true );
	};

	$seats    = $get( '_c1791_seats_left' );
	$register = $get( '_c1791_register_url' );
	$capacity = $get( '_c1791_capacity' );

	$data = array(
		'id'          => $post_id,
		'start'       => $get( '_c1791_start_date' ),
		'end'         => $get( '_c1791_end_date' ),
		'time'        => $get( '_c1791_time' ),
		'duration'    => $get( '_c1791_duration' ),
		'location'    => $get( '_c1791_location' ),
		'price'       => $get( '_c1791_price' ),
		'capacity'    => '' === $capacity ? null : (int) $capacity,
		'seats'       => '' === $seats ? null : (int) $seats,
		'level'       => $get( '_c1791_level' ),
		'register'    => $register ? c1791_link( $register ) : get_permalink( $post_id ),
		'external'    => false,
		'includes'    => $get( '_c1791_includes' ),
		'bring'       => $get( '_c1791_bring' ),
		'prereqs'     => $get( '_c1791_prereqs' ),
		'instructors' => array_filter( array_map( 'absint', (array) $get( '_c1791_instructors' ) ) ),
	);

	/**
	 * Filter class details (integrations point Register at a booking form or
	 * checkout, and read seats from product stock).
	 *
	 * @param array $data    Class details.
	 * @param int   $post_id Class post ID.
	 */
	return apply_filters( 'c1791_class_data', $data, $post_id );
}

/**
 * Human date range for a class.
 *
 * @param array $c Class data from c1791_class().
 * @return string
 */
function c1791_class_dates( $c ) {
	if ( ! $c['start'] ) {
		return __( 'Date TBA', 'concealed1791' );
	}
	$start = strtotime( $c['start'] );
	if ( $c['end'] && $c['end'] !== $c['start'] ) {
		return date_i18n( 'M j', $start ) . ' – ' . date_i18n( 'M j, Y', strtotime( $c['end'] ) );
	}
	return date_i18n( 'D, M j, Y', $start );
}

/**
 * Whether a class is sold out.
 *
 * @param array $c Class data.
 * @return bool
 */
function c1791_is_sold_out( $c ) {
	return null !== $c['seats'] && $c['seats'] <= 0;
}

/**
 * Seats remaining badge.
 *
 * @param array $c Class data.
 */
function c1791_seats_badge( $c ) {
	if ( null === $c['seats'] ) {
		return;
	}
	if ( $c['seats'] <= 0 ) {
		echo '<span class="ct-seats ct-seats--full">' . esc_html__( 'Sold out', 'concealed1791' ) . '</span>';
	} elseif ( $c['seats'] <= 3 ) {
		/* translators: %d: seats left. */
		echo '<span class="ct-seats ct-seats--low">' . esc_html( sprintf( _n( 'Only %d seat left', 'Only %d seats left', $c['seats'], 'concealed1791' ), $c['seats'] ) ) . '</span>';
	} else {
		/* translators: %d: seats left. */
		echo '<span class="ct-seats ct-seats--open">' . esc_html( sprintf( __( '%d seats open', 'concealed1791' ), $c['seats'] ) ) . '</span>';
	}
}

/**
 * Seats meter (filled share of the class), when capacity and seats are known.
 *
 * @param array $c Class data.
 */
function c1791_seats_meter( $c ) {
	if ( null === $c['seats'] || ! $c['capacity'] ) {
		return;
	}
	$taken = max( 0, min( $c['capacity'], $c['capacity'] - $c['seats'] ) );
	$pct   = (int) round( $taken / $c['capacity'] * 100 );
	?>
	<div class="ct-meter" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: percent. */ __( '%d%% of seats taken', 'concealed1791' ), $pct ) ); ?>">
		<span class="ct-meter__bar" style="width:<?php echo esc_attr( $pct ); ?>%"></span>
	</div>
	<?php
}

/**
 * Skill level label.
 *
 * @param array $c Class data.
 */
function c1791_level_badge( $c ) {
	$levels = c1791_levels();
	$label  = isset( $levels[ $c['level'] ] ) ? $levels[ $c['level'] ] : $levels[''];
	echo '<span class="ct-level">' . esc_html( $label ) . '</span>';
}

/**
 * Register button for a class.
 *
 * @param array  $c     Class data.
 * @param string $extra Extra classes.
 * @param string $label Button text (default "Register").
 */
function c1791_register_button( $c, $extra = '', $label = '' ) {
	$full = c1791_is_sold_out( $c );
	printf(
		'<a class="ct-btn %1$s" href="%2$s"%3$s%4$s>%5$s</a>',
		esc_attr( $extra ),
		esc_url( $c['register'] ),
		$full ? ' aria-disabled="true"' : '',
		! empty( $c['nofollow'] ) ? ' rel="nofollow"' : '',
		$full ? esc_html__( 'Sold Out', 'concealed1791' ) : esc_html( $label ? $label : __( 'Register', 'concealed1791' ) )
	);
}

/**
 * Instructor names for a class.
 *
 * @param array $c Class data.
 * @return string
 */
function c1791_class_instructor_names( $c ) {
	$names = array();
	foreach ( $c['instructors'] as $id ) {
		if ( 'publish' === get_post_status( $id ) ) {
			$names[] = get_the_title( $id );
		}
	}
	return implode( ', ', $names );
}

/**
 * Package details as an array.
 *
 * @param int|null $post_id Package post ID.
 * @return array
 */
function c1791_package( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$get     = function ( $key ) use ( $post_id ) {
		return (string) get_post_meta( $post_id, $key, true );
	};

	$features = array();
	foreach ( c1791_lines( $get( '_c1791_features' ) ) as $line ) {
		$excluded   = 0 === strpos( $line, '-' );
		$features[] = array(
			'text'     => $excluded ? ltrim( substr( $line, 1 ) ) : $line,
			'included' => ! $excluded,
		);
	}

	$url     = $get( '_c1791_button_url' );
	$url_alt = $get( '_c1791_button_url_alt' );

	$data = array(
		'id'          => $post_id,
		'title'       => get_the_title( $post_id ),
		'summary'     => get_post_field( 'post_excerpt', $post_id ),
		'price'       => $get( '_c1791_price' ),
		'period'      => $get( '_c1791_period' ),
		'price_alt'   => $get( '_c1791_price_alt' ),
		'period_alt'  => $get( '_c1791_period_alt' ),
		'features'    => $features,
		'featured'    => '1' === $get( '_c1791_featured' ),
		'badge'       => $get( '_c1791_badge' ),
		'button'      => $get( '_c1791_button_text' ) ? $get( '_c1791_button_text' ) : __( 'Get Started', 'concealed1791' ),
		'url'         => $url ? c1791_link( $url ) : c1791_link( '/classes/' ),
		'url_alt'     => $url_alt ? c1791_link( $url_alt ) : '',
		'nofollow'    => false,
	);
	if ( ! $data['url_alt'] ) {
		$data['url_alt'] = $data['url'];
	}

	/**
	 * Filter package details (the WooCommerce integration links products).
	 *
	 * @param array $data    Package details.
	 * @param int   $post_id Package post ID.
	 */
	return apply_filters( 'c1791_package_data', $data, $post_id );
}

/**
 * Split a price like "$149" into currency, amount and cents for display.
 *
 * @param string $price Price text.
 * @return array|null [ symbol, whole, cents ] or null when it isn't a number.
 */
function c1791_split_price( $price ) {
	if ( preg_match( '/^\s*([^\d\s.,]*)\s*(\d[\d,]*)(?:\.(\d{2}))?\s*$/u', (string) $price, $m ) ) {
		return array( $m[1], $m[2], isset( $m[3] ) ? $m[3] : '' );
	}
	return null;
}

/**
 * Print a price with a small currency symbol and cents.
 *
 * @param string $price Price text.
 */
function c1791_price_html( $price ) {
	$parts = c1791_split_price( $price );
	if ( ! $parts ) {
		echo '<span class="ct-price__text">' . esc_html( $price ) . '</span>';
		return;
	}
	if ( '' !== $parts[0] ) {
		echo '<span class="ct-price__cur">' . esc_html( $parts[0] ) . '</span>';
	}
	echo '<span class="ct-price__num">' . esc_html( $parts[1] ) . '</span>';
	if ( '' !== $parts[2] && '00' !== $parts[2] ) {
		echo '<span class="ct-price__cents">.' . esc_html( $parts[2] ) . '</span>';
	}
}

/**
 * Posted-on meta for posts.
 */
function c1791_posted_on() {
	printf( '<time datetime="%1$s">%2$s</time>', esc_attr( get_the_date( DATE_W3C ) ), esc_html( get_the_date() ) );
	if ( get_the_author() ) {
		echo ' &middot; ' . esc_html( get_the_author() );
	}
	$cats = get_the_category_list( ', ' );
	if ( $cats && 'post' === get_post_type() ) {
		echo ' &middot; ' . wp_kses_post( $cats );
	}
}

/**
 * Breadcrumbs.
 */
function c1791_breadcrumbs() {
	// No way-out links on the front page or on distraction-free checkout and funnel steps.
	if ( is_front_page() || c1791_is_minimal_header() ) {
		return;
	}
	$crumbs = array( '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'concealed1791' ) . '</a>' );

	if ( is_singular( 'c1791_class' ) || is_tax( 'c1791_class_type' ) ) {
		$crumbs[] = '<a href="' . esc_url( get_post_type_archive_link( 'c1791_class' ) ) . '">' . esc_html__( 'Classes', 'concealed1791' ) . '</a>';
	} elseif ( is_singular( 'c1791_instructor' ) ) {
		$crumbs[] = '<a href="' . esc_url( get_post_type_archive_link( 'c1791_instructor' ) ) . '">' . esc_html__( 'Instructors', 'concealed1791' ) . '</a>';
	} elseif ( is_singular( 'post' ) ) {
		$blog = get_option( 'page_for_posts' );
		if ( $blog ) {
			$crumbs[] = '<a href="' . esc_url( get_permalink( $blog ) ) . '">' . esc_html( get_the_title( $blog ) ) . '</a>';
		}
	}

	echo '<nav class="ct-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'concealed1791' ) . '">' . implode( '<span aria-hidden="true"> / </span>', $crumbs ) . '</nav>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
}

/**
 * Page title banner.
 *
 * @param string $title    Title (plain text).
 * @param string $subtitle Optional subtitle (HTML allowed).
 * @param string $eyebrow  Optional small label above the title.
 * @param string $meta     Optional meta line (HTML allowed).
 */
function c1791_page_hero( $title, $subtitle = '', $eyebrow = '', $meta = '' ) {
	?>
	<header class="ct-page-hero <?php echo esc_attr( c1791_art_class( 'banner' ) ); ?>">
		<div class="ct-container">
			<?php c1791_breadcrumbs(); ?>
			<?php if ( $eyebrow ) : ?>
				<span class="ct-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<h1><?php echo esc_html( $title ); ?></h1>
			<?php if ( $meta ) : ?>
				<p class="ct-page-hero__meta"><?php echo wp_kses_post( $meta ); ?></p>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?>
				<div class="ct-page-hero__sub"><?php echo wp_kses_post( wpautop( $subtitle ) ); ?></div>
			<?php endif; ?>
		</div>
	</header>
	<?php
}

/**
 * Section heading block.
 *
 * @param string $eyebrow Eyebrow.
 * @param string $title   Title.
 * @param string $text    Intro.
 * @param bool   $center  Center it.
 * @param string $id      Optional heading ID.
 */
function c1791_section_head( $eyebrow, $title, $text = '', $center = true, $id = '' ) {
	if ( ! $eyebrow && ! $title && ! $text ) {
		return;
	}
	?>
	<div class="ct-section-head<?php echo $center ? ' ct-section-head--center' : ''; ?>">
		<?php if ( $eyebrow ) : ?>
			<span class="ct-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
		<?php endif; ?>
		<?php if ( $title ) : ?>
			<h2<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $text ) : ?>
			<p><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Site logo, or the shield mark with site name and tagline.
 *
 * @param bool $tagline Show the tagline.
 */
function c1791_brand( $tagline = true ) {
	?>
	<a class="ct-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
		<?php if ( has_custom_logo() ) : ?>
			<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'custom-logo', 'alt' => get_bloginfo( 'name' ) ) ); ?>
		<?php else : ?>
			<span class="ct-brand__mark" aria-hidden="true">
				<svg viewBox="0 0 48 56"><path d="M24 2 45 9.5V26c0 13.4-9 24.4-21 28C12 50.4 3 39.4 3 26V9.5Z" fill="currentColor"/><path d="M24 8.5 39 14v12.2c0 9.9-6.3 18.2-15 21.3C15.3 44.4 9 36.1 9 26.2V14Z" fill="none" stroke="#fff" stroke-opacity=".35" stroke-width="1.5"/><text x="24" y="33" text-anchor="middle" font-family="'Barlow Condensed',Arial,sans-serif" font-weight="700" font-size="15" fill="#fff">1791</text></svg>
			</span>
			<span class="ct-brand__text">
				<span class="ct-brand__name"><?php bloginfo( 'name' ); ?></span>
				<?php if ( $tagline && c1791_setting( 'tagline' ) ) : ?>
					<span class="ct-brand__tag"><?php echo esc_html( c1791_setting( 'tagline' ) ); ?></span>
				<?php endif; ?>
			</span>
		<?php endif; ?>
	</a>
	<?php
}

/**
 * A tel: URL for a phone number.
 *
 * @param string $phone Phone number as typed.
 * @return string
 */
function c1791_phone_href( $phone ) {
	$digits = preg_replace( '/[^\d+]/', '', (string) $phone );
	if ( '' === $digits ) {
		return '';
	}
	// Ten-digit US numbers get the +1 country code.
	if ( '+' !== $digits[0] && 10 === strlen( $digits ) ) {
		$digits = '+1' . $digits;
	}
	return 'tel:' . $digits;
}

/**
 * Tap-to-call link for the business phone.
 *
 * @param string $class CSS class.
 * @param string $label Optional text before the number (visually hidden when empty).
 */
function c1791_phone_link( $class = '', $label = '' ) {
	$phone = c1791_setting( 'phone' );
	if ( ! $phone ) {
		return;
	}
	printf(
		'<a class="%1$s" href="%2$s">%3$s<span>%4$s%5$s</span></a>',
		esc_attr( trim( 'ct-phone ' . $class ) ),
		esc_url( c1791_phone_href( $phone ), array( 'tel' ) ),
		c1791_get_icon( 'phone' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		$label ? '<small>' . esc_html( $label ) . '</small>' : '',
		esc_html( $phone )
	);
}

/**
 * Social icon links.
 *
 * @param string $class Extra class.
 */
function c1791_social_links( $class = '' ) {
	$links = array();
	foreach ( c1791_social_networks() as $network => $label ) {
		$url = c1791_setting( 'social_' . $network );
		if ( $url ) {
			$links[ $network ] = array( $url, $label );
		}
	}
	if ( ! $links ) {
		return;
	}
	echo '<div class="' . esc_attr( trim( 'ct-social ' . $class ) ) . '">';
	foreach ( $links as $network => $link ) {
		printf(
			'<a href="%1$s" target="_blank" rel="noopener"><span class="screen-reader-text">%2$s</span>%3$s</a>',
			esc_url( $link[0] ),
			esc_html( $link[1] ),
			c1791_get_icon( $network ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
		);
	}
	echo '</div>';
}

/**
 * Five-star rating.
 *
 * @param float $rating Rating 0–5.
 */
function c1791_stars( $rating ) {
	$rating = max( 0, min( 5, (float) $rating ) );
	/* translators: %s: rating out of 5. */
	echo '<span class="ct-stars" role="img" aria-label="' . esc_attr( sprintf( __( 'Rated %s out of 5', 'concealed1791' ), number_format_i18n( $rating, 1 ) ) ) . '">';
	for ( $i = 1; $i <= 5; $i++ ) {
		echo '<span class="ct-stars__star' . ( $rating >= $i - 0.25 ? ' is-on' : '' ) . '">' . c1791_get_icon( 'star' ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	}
	echo '</span>';
}

/**
 * Average rating and count of published testimonials.
 *
 * @return array { average, count }
 */
function c1791_rating_summary() {
	$cached = get_transient( 'c1791_rating_summary' );
	if ( is_array( $cached ) ) {
		return $cached;
	}
	$ids   = get_posts(
		array(
			'post_type'      => 'c1791_testimonial',
			'posts_per_page' => 500,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);
	$total = 0;
	foreach ( $ids as $id ) {
		$rating = (int) get_post_meta( $id, '_c1791_rating', true );
		$total += $rating ? $rating : 5;
	}
	$summary = array(
		'average' => $ids ? round( $total / count( $ids ), 1 ) : 0,
		'count'   => count( $ids ),
	);
	set_transient( 'c1791_rating_summary', $summary, DAY_IN_SECONDS );
	return $summary;
}

/**
 * Clear the rating summary when a testimonial changes.
 */
function c1791_clear_rating_summary() {
	delete_transient( 'c1791_rating_summary' );
}
add_action( 'save_post_c1791_testimonial', 'c1791_clear_rating_summary' );
add_action( 'deleted_post', 'c1791_clear_rating_summary' );

/**
 * Newsletter form (or a setup note for admins).
 */
function c1791_newsletter_form() {
	/**
	 * Replace the newsletter form (the MailPoet integration uses this).
	 *
	 * @param string|null $html Form markup, or null for the theme's default form.
	 */
	$html = apply_filters( 'c1791_newsletter_form_html', null );
	if ( null !== $html ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts or plugin shortcode output.
		return;
	}

	$action = c1791_mod( 'news_action' );
	$field  = c1791_mod( 'news_field' );

	if ( ! $action ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			echo '<p class="ct-newsletter__note">' . esc_html__( 'Admins: choose a MailPoet list under Appearance → Theme Settings → Integrations, or add your email provider\'s form URL under Customize → Concealed 1791: Home Page → Newsletter.', 'concealed1791' ) . '</p>';
		}
		return;
	}
	static $n = 0;
	++$n;
	?>
	<form class="ct-newsletter" action="<?php echo esc_url( $action ); ?>" method="post" target="_blank">
		<label class="screen-reader-text" for="ct-news-email-<?php echo (int) $n; ?>"><?php esc_html_e( 'Email address', 'concealed1791' ); ?></label>
		<input id="ct-news-email-<?php echo (int) $n; ?>" type="email" name="<?php echo esc_attr( $field ? $field : 'email' ); ?>" placeholder="<?php esc_attr_e( 'Your email address', 'concealed1791' ); ?>" required autocomplete="email">
		<button type="submit"><?php echo esc_html( c1791_mod( 'news_button' ) ); ?></button>
	</form>
	<?php
}

/**
 * Main menu fallback until a menu is assigned.
 */
function c1791_menu_fallback() {
	$items = array(
		get_post_type_archive_link( 'c1791_class' )      => __( 'Classes', 'concealed1791' ),
		get_post_type_archive_link( 'c1791_instructor' ) => __( 'Instructors', 'concealed1791' ),
	);
	$blog  = get_option( 'page_for_posts' );
	if ( $blog ) {
		$items[ get_permalink( $blog ) ] = get_the_title( $blog );
	}
	if ( c1791_has_woo() ) {
		$items[ wc_get_page_permalink( 'shop' ) ] = __( 'Shop', 'concealed1791' );
	}
	foreach ( array( 'about', 'about-us', 'faq', 'contact', 'contact-us' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$items[ get_permalink( $page ) ] = get_the_title( $page );
		}
	}

	echo '<ul class="menu">';
	foreach ( $items as $url => $label ) {
		if ( $url ) {
			echo '<li class="menu-item"><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		}
	}
	echo '</ul>';
}

/**
 * Default heading for a footer column type.
 *
 * @param string $type Column type.
 * @return string
 */
function c1791_footer_column_default_title( $type ) {
	$titles = array(
		'classes'    => __( 'Training', 'concealed1791' ),
		'posts'      => __( 'Latest Articles', 'concealed1791' ),
		'menu'       => __( 'Links', 'concealed1791' ),
		'contact'    => __( 'Contact Us', 'concealed1791' ),
		'hours'      => __( 'Hours', 'concealed1791' ),
		'newsletter' => __( 'Stay in the Loop', 'concealed1791' ),
		'text'       => '',
		'widgets'    => '',
	);
	return isset( $titles[ $type ] ) ? $titles[ $type ] : '';
}

/**
 * Render one footer column from its Theme Settings.
 *
 * @param int $column Column number (1–4).
 */
function c1791_footer_column( $column ) {
	$type  = c1791_setting( "footer_col{$column}_type" );
	$title = c1791_setting( "footer_col{$column}_title" );
	$title = '' !== $title ? $title : c1791_footer_column_default_title( $type );

	if ( 'widgets' === $type ) {
		if ( is_active_sidebar( 'footer-' . $column ) ) {
			dynamic_sidebar( 'footer-' . $column );
		} elseif ( current_user_can( 'edit_theme_options' ) ) {
			/* translators: %d: column number. */
			echo '<p class="ct-footer__note">' . esc_html( sprintf( __( 'Add widgets to "Footer Column %d" under Appearance → Widgets.', 'concealed1791' ), $column ) ) . '</p>';
		}
		return;
	}

	if ( $title ) {
		echo '<h2 class="ct-footer__heading">' . esc_html( $title ) . '</h2>';
	}

	switch ( $type ) {
		case 'classes':
			echo '<ul class="ct-footer__links">';
			echo '<li><a href="' . esc_url( get_post_type_archive_link( 'c1791_class' ) ) . '">' . esc_html__( 'Class Schedule', 'concealed1791' ) . '</a></li>';
			$types = get_terms(
				array(
					'taxonomy'   => 'c1791_class_type',
					'hide_empty' => false,
					'number'     => 6,
				)
			);
			if ( ! is_wp_error( $types ) ) {
				foreach ( $types as $term ) {
					echo '<li><a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
				}
			}
			echo '<li><a href="' . esc_url( get_post_type_archive_link( 'c1791_instructor' ) ) . '">' . esc_html__( 'Instructors', 'concealed1791' ) . '</a></li>';
			echo '</ul>';
			break;

		case 'posts':
			$posts = get_posts(
				array(
					'posts_per_page'      => 4,
					'ignore_sticky_posts' => true,
				)
			);
			echo '<ul class="ct-footer__links">';
			foreach ( $posts as $item ) {
				echo '<li><a href="' . esc_url( get_permalink( $item ) ) . '">' . esc_html( get_the_title( $item ) ) . '</a></li>';
			}
			echo '</ul>';
			break;

		case 'menu':
			$menu = (int) c1791_setting( "footer_col{$column}_menu" );
			if ( $menu && is_nav_menu( $menu ) ) {
				wp_nav_menu(
					array(
						'menu'        => $menu,
						'container'   => false,
						'menu_class'  => 'ct-footer__links',
						'depth'       => 1,
						'fallback_cb' => false,
					)
				);
			} elseif ( current_user_can( 'edit_theme_options' ) ) {
				echo '<p class="ct-footer__note">' . esc_html__( 'Choose a menu for this column in Appearance → Theme Settings → Footer.', 'concealed1791' ) . '</p>';
			}
			break;

		case 'contact':
			c1791_contact_list();
			break;

		case 'hours':
			$lines = c1791_lines( c1791_setting( 'hours' ) );
			if ( $lines ) {
				echo '<ul class="ct-footer__hours">';
				foreach ( $lines as $line ) {
					echo '<li>' . esc_html( $line ) . '</li>';
				}
				echo '</ul>';
			}
			break;

		case 'newsletter':
			echo '<p>' . esc_html__( 'New class dates and training tips, straight to your inbox.', 'concealed1791' ) . '</p>';
			c1791_newsletter_form();
			break;

		case 'text':
			echo wp_kses_post( wpautop( c1791_setting( "footer_col{$column}_text" ) ) );
			break;
	}
}

/**
 * Contact details list (address, phone, email, hours).
 */
function c1791_contact_list() {
	$address = c1791_setting( 'address' );
	$phone   = c1791_setting( 'phone' );
	$email   = c1791_setting( 'email' );
	if ( ! $address && ! $phone && ! $email ) {
		if ( current_user_can( 'edit_theme_options' ) ) {
			echo '<p class="ct-footer__note">' . esc_html__( 'Add your phone, email and address in Appearance → Theme Settings → General.', 'concealed1791' ) . '</p>';
		}
		return;
	}
	echo '<ul class="ct-contact">';
	if ( $address ) {
		$map  = c1791_setting( 'map_url' );
		$text = nl2br( esc_html( $address ) );
		echo '<li>' . c1791_get_icon( 'pin' ) . '<span>' . ( $map ? '<a href="' . esc_url( c1791_link( $map ) ) . '" target="_blank" rel="noopener">' . $text . '</a>' : $text ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
	if ( $phone ) {
		echo '<li>' . c1791_get_icon( 'phone' ) . '<a href="' . esc_url( c1791_phone_href( $phone ), array( 'tel' ) ) . '">' . esc_html( $phone ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	}
	if ( $email ) {
		echo '<li>' . c1791_get_icon( 'email' ) . '<a href="' . esc_url( 'mailto:' . antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
	}
	echo '</ul>';
}

/**
 * Pagination.
 */
function c1791_pagination() {
	the_posts_pagination(
		array(
			'class'     => 'ct-pagination',
			'mid_size'  => 1,
			'prev_text' => '&larr;<span class="screen-reader-text">' . esc_html__( 'Previous', 'concealed1791' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next', 'concealed1791' ) . '</span>&rarr;',
		)
	);
}

/**
 * Query FAQs in their set order.
 *
 * @param int $count Number of questions (-1 for all).
 * @return WP_Post[]
 */
function c1791_faqs( $count = -1 ) {
	return get_posts(
		array(
			'post_type'      => 'c1791_faq',
			'posts_per_page' => $count,
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
}

/**
 * FAQ accordion. Questions shown on the page are added to FAQPage schema.
 *
 * @param WP_Post[] $faqs Questions.
 */
function c1791_faq_list( $faqs ) {
	if ( ! $faqs ) {
		return;
	}
	echo '<div class="ct-faq" data-ct-accordion>';
	foreach ( $faqs as $i => $faq ) {
		$answer = apply_filters( 'the_content', $faq->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core filter.
		c1791_faq_schema_add( get_the_title( $faq ), $answer );
		?>
		<details class="ct-faq__item"<?php echo 0 === $i ? ' open' : ''; ?>>
			<summary class="ct-faq__q"><span><?php echo esc_html( get_the_title( $faq ) ); ?></span><?php c1791_icon( 'chevron' ); ?></summary>
			<div class="ct-faq__a"><?php echo wp_kses_post( $answer ); ?></div>
		</details>
		<?php
	}
	echo '</div>';
}

/**
 * Collect questions for FAQPage schema (or return them).
 *
 * @param string|null $question Question, or null to read the list.
 * @param string      $answer   Answer HTML.
 * @return array
 */
function c1791_faq_schema_add( $question = null, $answer = '' ) {
	static $items = array();
	if ( null !== $question ) {
		$items[ md5( $question ) ] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $question ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => trim( wp_strip_all_tags( $answer ) ),
			),
		);
	}
	return $items;
}

/**
 * Print FAQPage and LocalBusiness structured data.
 */
function c1791_structured_data() {
	$graph = array();
	$faqs  = c1791_faq_schema_add();
	if ( $faqs ) {
		$graph[] = array(
			'@type'      => 'FAQPage',
			'mainEntity' => array_values( $faqs ),
		);
	}
	if ( is_front_page() && ( c1791_setting( 'phone' ) || c1791_setting( 'address' ) ) ) {
		$business = array(
			'@type' => 'LocalBusiness',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		);
		if ( c1791_setting( 'phone' ) ) {
			$business['telephone'] = c1791_setting( 'phone' );
		}
		if ( c1791_setting( 'email' ) ) {
			$business['email'] = c1791_setting( 'email' );
		}
		if ( c1791_setting( 'address' ) ) {
			$business['address'] = implode( ', ', c1791_lines( c1791_setting( 'address' ) ) );
		}
		$same_as = array_values( array_filter( array_map( 'c1791_setting', array_map( function ( $n ) { return 'social_' . $n; }, array_keys( c1791_social_networks() ) ) ) ) );
		if ( $same_as ) {
			$business['sameAs'] = $same_as;
		}
		// No aggregateRating: search engines treat reviews a business hosts about itself as self-serving.
		$graph[] = $business;
	}
	if ( ! $graph ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		),
		JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP
	) . '</script>' . "\n";
}
add_action( 'wp_footer', 'c1791_structured_data', 20 );
