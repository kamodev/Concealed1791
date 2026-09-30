<?php
/**
 * MailPoet integration (https://www.mailpoet.com/).
 *
 * The theme's newsletter forms (the front page band and a footer column set
 * to "Newsletter sign-up") can feed MailPoet in two ways, chosen under
 * Theme Settings → Integrations:
 *
 *   1. MailPoet list: the theme's own one-field form subscribes people to a
 *      MailPoet list through MailPoet's API. MailPoet's sign-up confirmation
 *      (double opt-in) and welcome emails apply as usual.
 *   2. MailPoet form: a form built in MailPoet → Forms (for extra fields such
 *      as a first name) replaces the theme's form.
 *
 * A chosen MailPoet form wins over a list, and either wins over the
 * "Form action URL" in the Customizer. MailPoet forms anywhere on the site
 * also pick up the theme's colors, fonts and buttons (can be turned off).
 *
 * Adapted from the kamodev/PEN theme's MailPoet integration.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether MailPoet is active.
 *
 * @return bool
 */
function c1791_mailpoet_active() {
	return class_exists( '\MailPoet\API\API' );
}

/**
 * MailPoet's public API, or null.
 *
 * @return object|null
 */
function c1791_mailpoet_api() {
	if ( ! c1791_mailpoet_active() ) {
		return null;
	}
	try {
		return \MailPoet\API\API::MP( 'v1' );
	} catch ( \Exception $e ) {
		return null;
	}
}

/**
 * MailPoet lists.
 *
 * @return array id => name
 */
function c1791_mailpoet_lists() {
	$api = c1791_mailpoet_api();
	if ( ! $api ) {
		return array();
	}
	try {
		$lists = array();
		foreach ( $api->getLists() as $list ) {
			$lists[ (int) $list['id'] ] = $list['name'];
		}
		return $lists;
	} catch ( \Exception $e ) {
		return array();
	}
}

/**
 * Enabled MailPoet forms.
 *
 * @return array id => name
 */
function c1791_mailpoet_forms() {
	global $wpdb;
	if ( ! c1791_mailpoet_active() ) {
		return array();
	}
	$table = $wpdb->prefix . 'mailpoet_forms';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- MailPoet has no public API for listing forms; the table name is not user input.
	$rows  = $wpdb->get_results( "SELECT id, name FROM {$table} WHERE deleted_at IS NULL AND status = 'enabled' ORDER BY name" );
	$forms = array();
	foreach ( (array) $rows as $row ) {
		$forms[ (int) $row->id ] = $row->name;
	}
	return $forms;
}

/**
 * How the newsletter forms use MailPoet.
 *
 * @return string 'form', 'list' or '' (not used).
 */
function c1791_mailpoet_mode() {
	if ( ! c1791_mailpoet_active() ) {
		return '';
	}
	if ( c1791_setting( 'mailpoet_form' ) && shortcode_exists( 'mailpoet_form' ) ) {
		return 'form';
	}
	return c1791_setting( 'mailpoet_list' ) ? 'list' : '';
}

/**
 * Whether MailPoet asks new subscribers to confirm by email.
 *
 * @return bool
 */
function c1791_mailpoet_needs_confirmation() {
	if ( class_exists( '\MailPoet\Settings\SettingsController' ) ) {
		try {
			return (bool) \MailPoet\Settings\SettingsController::getInstance()->get( 'signup_confirmation.enabled', true );
		} catch ( \Exception $e ) {
			return true;
		}
	}
	return true;
}

/**
 * Newsletter form markup when MailPoet is in use.
 *
 * Each form on a page gets its own anchor, so the visitor returns to the form
 * they used and only that form shows the result.
 *
 * @param string|null $html Markup from an earlier filter, or null.
 * @return string|null
 */
function c1791_mailpoet_newsletter_form( $html ) {
	$mode = c1791_mailpoet_mode();

	if ( 'form' === $mode ) {
		return '<div class="ct-newsletter ct-newsletter--mailpoet">' . do_shortcode( '[mailpoet_form id="' . absint( c1791_setting( 'mailpoet_form' ) ) . '"]' ) . '</div>';
	}

	if ( 'list' !== $mode ) {
		return $html;
	}

	static $n = 0;
	++$n;
	$anchor = 'ct-newsletter-' . $n;

	ob_start();
	c1791_mailpoet_status_message( $anchor );
	?>
	<form class="ct-newsletter" id="<?php echo esc_attr( $anchor ); ?>" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
		<input type="hidden" name="action" value="c1791_mailpoet_subscribe">
		<input type="hidden" name="c1791_anchor" value="<?php echo esc_attr( $anchor ); ?>">
		<?php wp_referer_field(); ?>
		<?php // Left empty by people; bots that fill every field are ignored. ?>
		<div class="ct-hp" aria-hidden="true">
			<label for="<?php echo esc_attr( $anchor ); ?>-website"><?php esc_html_e( 'Leave this field empty', 'concealed1791' ); ?></label>
			<input id="<?php echo esc_attr( $anchor ); ?>-website" type="text" name="c1791_website" value="" tabindex="-1" autocomplete="off">
		</div>
		<label class="screen-reader-text" for="<?php echo esc_attr( $anchor ); ?>-email"><?php esc_html_e( 'Email address', 'concealed1791' ); ?></label>
		<input id="<?php echo esc_attr( $anchor ); ?>-email" type="email" name="c1791_email" placeholder="<?php esc_attr_e( 'Your email address', 'concealed1791' ); ?>" required autocomplete="email">
		<button type="submit"><?php echo esc_html( c1791_mod( 'news_button' ) ); ?></button>
	</form>
	<?php
	return ob_get_clean();
}
add_filter( 'c1791_newsletter_form_html', 'c1791_mailpoet_newsletter_form' );

/**
 * Result message after the theme form posts to MailPoet.
 *
 * @param string $anchor The form's anchor; the message shows only on that form.
 */
function c1791_mailpoet_status_message( $anchor ) {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display-only status flags.
	$status = isset( $_GET['c1791-subscribed'] ) ? sanitize_key( wp_unslash( $_GET['c1791-subscribed'] ) ) : '';
	$at     = isset( $_GET['c1791-at'] ) ? sanitize_html_class( wp_unslash( $_GET['c1791-at'] ) ) : '';
	// phpcs:enable
	if ( ! $status || $at !== $anchor ) {
		return;
	}
	$messages = array(
		'ok'      => c1791_mailpoet_needs_confirmation()
			? __( 'Almost done! Check your inbox and click the link to confirm your subscription.', 'concealed1791' )
			: __( 'You\'re on the list. Watch your inbox for new class dates.', 'concealed1791' ),
		'invalid' => __( 'That email address doesn\'t look right. Please check it and try again.', 'concealed1791' ),
		'wait'    => __( 'Too many sign-ups from your connection. Please try again in a few minutes.', 'concealed1791' ),
		'error'   => __( 'We couldn\'t sign you up just now. Please try again in a few minutes.', 'concealed1791' ),
	);
	if ( ! isset( $messages[ $status ] ) ) {
		return;
	}
	printf(
		'<p class="ct-newsletter__status ct-newsletter__status--%1$s" role="%2$s">%3$s</p>',
		esc_attr( $status ),
		'ok' === $status ? 'status' : 'alert',
		esc_html( $messages[ $status ] )
	);
}

/**
 * Handle the theme form: add the email to the chosen MailPoet list.
 */
function c1791_mailpoet_subscribe() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- public sign-up form on cacheable pages; a honeypot, a rate limit and MailPoet's own checks guard it.
	$redirect = remove_query_arg( array( 'c1791-subscribed', 'c1791-at' ), wp_get_referer() ? wp_get_referer() : home_url( '/' ) );
	$email    = isset( $_POST['c1791_email'] ) ? sanitize_email( wp_unslash( $_POST['c1791_email'] ) ) : '';
	$anchor   = isset( $_POST['c1791_anchor'] ) ? sanitize_html_class( wp_unslash( $_POST['c1791_anchor'] ) ) : 'newsletter';
	$is_bot   = ! empty( $_POST['c1791_website'] );
	// phpcs:enable

	$list = absint( c1791_setting( 'mailpoet_list' ) );
	$api  = c1791_mailpoet_api();

	if ( $is_bot ) {
		$status = 'ok'; // Don't tell bots they were caught.
	} elseif ( ! c1791_mailpoet_rate_ok() ) {
		$status = 'wait';
	} elseif ( ! is_email( $email ) ) {
		$status = 'invalid';
	} elseif ( ! $api || ! $list ) {
		$status = 'error';
	} else {
		$status = 'ok';
		try {
			$api->addSubscriber( array( 'email' => $email ), array( $list ) );
		} catch ( \Exception $e ) {
			$codes = class_exists( '\MailPoet\API\MP\v1\APIException' ) ? array(
				'exists'       => \MailPoet\API\MP\v1\APIException::SUBSCRIBER_EXISTS,
				'confirmation' => \MailPoet\API\MP\v1\APIException::CONFIRMATION_FAILED_TO_SEND,
				'welcome'      => \MailPoet\API\MP\v1\APIException::WELCOME_FAILED_TO_SEND,
			) : array();

			if ( $codes && in_array( $e->getCode(), array( $codes['confirmation'], $codes['welcome'] ), true ) ) {
				// Saved, but MailPoet couldn't send an email. The visitor is signed up; the site owner should check MailPoet's sending setup.
				error_log( 'Concealed 1791 theme: MailPoet sign-up saved, but an email failed to send: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			} elseif ( $codes && $codes['exists'] === $e->getCode() ) {
				// Existing subscriber: add them to the list instead.
				try {
					$subscriber = $api->getSubscriber( $email );
					$api->subscribeToLists( $subscriber['id'], array( $list ) );
				} catch ( \Exception $inner ) {
					if ( ! in_array( $inner->getCode(), array( $codes['confirmation'], $codes['welcome'] ), true ) ) {
						$status = 'error';
					}
				}
			} else {
				$status = 'error';
			}
		}
	}

	wp_safe_redirect(
		add_query_arg(
			array(
				'c1791-subscribed' => $status,
				'c1791-at'         => $anchor,
			),
			$redirect
		) . '#' . $anchor
	);
	exit;
}
add_action( 'admin_post_c1791_mailpoet_subscribe', 'c1791_mailpoet_subscribe' );
add_action( 'admin_post_nopriv_c1791_mailpoet_subscribe', 'c1791_mailpoet_subscribe' );

/**
 * Limit sign-ups per visitor IP address.
 *
 * The theme form subscribes through MailPoet's API, which skips the CAPTCHA
 * and throttling on MailPoet's own forms. This cap stops a script from making
 * MailPoet send confirmation emails to many addresses. Sites behind a proxy
 * that hides visitor IPs share one counter; raise the limit with the
 * c1791_mailpoet_rate_limit filter if needed.
 *
 * @return bool Whether this sign-up may go ahead.
 */
function c1791_mailpoet_rate_ok() {
	$limit = (int) apply_filters( 'c1791_mailpoet_rate_limit', 5 ); // Sign-ups per IP address ...
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'c1791_mp_rate_' . md5( $ip );
	$count = (int) get_transient( $key );
	if ( $limit > 0 && $count >= $limit ) {
		return false;
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS ); // ... per 10 minutes.
	return true;
}

/**
 * Load mailpoet.css when MailPoet is active and matching is on.
 *
 * @param string[] $parts Stylesheet parts.
 * @return string[]
 */
function c1791_mailpoet_style_part( $parts ) {
	if ( c1791_mailpoet_active() && 'on' === c1791_setting( 'mailpoet_match' ) ) {
		$parts[] = 'mailpoet';
	}
	return $parts;
}
add_filter( 'c1791_style_parts', 'c1791_mailpoet_style_part' );
