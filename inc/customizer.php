<?php
/**
 * Customizer: front page content (hero, trust strip, benefits, classes,
 * steps, packages, instructors, reviews, FAQ, resources, shop, newsletter),
 * with live preview. Site-wide options (menus, sidebars, footer, colors,
 * integrations) live in Appearance → Theme Settings; the colors are also
 * editable here.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default values for every front page theme mod (without the c1791_ prefix).
 *
 * @return array
 */
function c1791_defaults() {
	$d = array(
		'hero_eyebrow'           => __( 'NRA & USCCA Certified Instructors', 'concealed1791' ),
		'hero_title'             => __( 'Train to <em>protect</em> what matters most.', 'concealed1791' ),
		'hero_text'              => __( 'Concealed carry certification, home defense and defensive shooting classes in Broomfield, Colorado — taught by certified instructors who put safety, the law and real-world skill first.', 'concealed1791' ),
		'hero_image'             => '',
		'hero_btn1_text'         => __( 'View Class Schedule', 'concealed1791' ),
		'hero_btn1_url'          => '/classes/',
		'hero_btn2_text'         => __( 'Book a Private Lesson', 'concealed1791' ),
		'hero_btn2_url'          => '/contact/',
		'hero_points'            => __( "Colorado concealed carry (CCW) classes\nSmall classes with hands-on coaching\nBeginner friendly — no experience needed", 'concealed1791' ),
		'hero_card'              => true,
		'hero_card_label'        => __( 'Next class', 'concealed1791' ),
		'hero_rating'            => true,

		'trust_enable'           => true,
		'trust_items'            => __( "NRA Certified Instructors | badge\nUSCCA Certified | shield\nAmerican Red Cross CPR & First Aid | medical\nBroomfield, Colorado | pin", 'concealed1791' ),

		'benefits_enable'        => true,
		'benefits_eyebrow'       => __( 'Why train with us', 'concealed1791' ),
		'benefits_title'         => __( 'Training that holds up when it matters', 'concealed1791' ),
		'benefits_text'          => __( 'Every class is built around safe gun handling, the law you carry under, and skills you can repeat under stress.', 'concealed1791' ),

		'classes_enable'         => true,
		'classes_eyebrow'        => __( 'Class schedule', 'concealed1791' ),
		'classes_title'          => __( 'Upcoming Classes', 'concealed1791' ),
		'classes_text'           => __( 'Reserve your seat online. Classes fill quickly — especially CCW certification weekends.', 'concealed1791' ),
		'classes_count'          => 4,

		'steps_enable'           => true,
		'steps_eyebrow'          => __( 'How it works', 'concealed1791' ),
		'steps_title'            => __( 'Getting certified is simple', 'concealed1791' ),

		'packages_enable'        => true,
		'packages_eyebrow'       => __( 'Training packages', 'concealed1791' ),
		'packages_title'         => __( 'Choose your path', 'concealed1791' ),
		'packages_text'          => __( 'Straightforward pricing with everything you need. Bring a partner and save.', 'concealed1791' ),
		'packages_toggle_1'      => __( 'Individual', 'concealed1791' ),
		'packages_toggle_2'      => __( 'Bring a partner', 'concealed1791' ),
		'packages_note'          => __( 'Group, church and corporate rates are available — just ask.', 'concealed1791' ),

		'instructors_enable'     => true,
		'instructors_eyebrow'    => __( 'Your instructors', 'concealed1791' ),
		'instructors_title'      => __( 'Learn from certified pros', 'concealed1791' ),
		'instructors_text'       => __( 'Patient, experienced and certified — our instructors meet you where you are.', 'concealed1791' ),

		'testimonials_enable'    => true,
		'testimonials_eyebrow'   => __( 'Student reviews', 'concealed1791' ),
		'testimonials_title'     => __( 'What our students say', 'concealed1791' ),

		'shop_enable'            => true,
		'shop_eyebrow'           => __( 'Instructor-approved gear', 'concealed1791' ),
		'shop_title'             => __( 'Gear we trust', 'concealed1791' ),
		'shop_text'              => __( 'Holsters, safes, targets and training aids we use and recommend.', 'concealed1791' ),

		'faq_enable'             => true,
		'faq_eyebrow'            => __( 'FAQ', 'concealed1791' ),
		'faq_title'              => __( 'Questions? We\'ve got answers.', 'concealed1791' ),
		'faq_text'               => __( 'Everything you need to know before your first class. Still unsure? Give us a call.', 'concealed1791' ),
		'faq_count'              => 6,

		'resources_enable'       => true,
		'resources_eyebrow'      => __( 'Know the law', 'concealed1791' ),
		'resources_title'        => __( 'Colorado carry law & training tips', 'concealed1791' ),
		'resources_text'         => __( 'Plain-language articles on Colorado self-defense law, carry rules and practice drills.', 'concealed1791' ),

		'news_enable'            => true,
		'news_title'             => __( 'Get new class dates first', 'concealed1791' ),
		'news_text'              => __( 'Join our list for new class dates, Colorado law updates and training tips. No spam — unsubscribe anytime.', 'concealed1791' ),
		'news_action'            => '',
		'news_field'             => 'email',
		'news_button'            => __( 'Sign Me Up', 'concealed1791' ),
	);

	$benefits = array(
		1 => array( 'badge', __( 'NRA · USCCA', 'concealed1791' ), __( 'Certified instructors', 'concealed1791' ), __( 'Every instructor is NRA and USCCA certified and teaches at the pace of the class.', 'concealed1791' ) ),
		2 => array( 'users', __( 'Small groups', 'concealed1791' ), __( 'Personal coaching', 'concealed1791' ), __( 'Small classes mean more coaching, more repetitions and more time for your questions.', 'concealed1791' ) ),
		3 => array( 'scale', __( 'Colorado law', 'concealed1791' ), __( 'Know the law', 'concealed1791' ), __( 'Understand when force is justified, where you can carry, and what happens after.', 'concealed1791' ) ),
		4 => array( 'target', __( 'Hands-on', 'concealed1791' ), __( 'Real-world skills', 'concealed1791' ), __( 'Coached drills that build safe, repeatable fundamentals from the draw to the follow-through.', 'concealed1791' ) ),
		5 => array( 'home', __( 'Home defense', 'concealed1791' ), __( 'Protect your family', 'concealed1791' ), __( 'Plan, prepare and practice for the place that matters most: your home.', 'concealed1791' ) ),
		6 => array( 'medical', __( 'Red Cross', 'concealed1791' ), __( 'CPR & First Aid', 'concealed1791' ), __( 'American Red Cross CPR and First Aid certification for families, workplaces and groups.', 'concealed1791' ) ),
	);
	foreach ( $benefits as $i => $b ) {
		$d[ "benefit{$i}_icon" ]  = $b[0];
		$d[ "benefit{$i}_tag" ]   = $b[1];
		$d[ "benefit{$i}_title" ] = $b[2];
		$d[ "benefit{$i}_text" ]  = $b[3];
	}

	$steps = array(
		1 => array( __( 'Pick your class', 'concealed1791' ), __( 'Choose CCW certification, defensive shooting, home defense or a private lesson.', 'concealed1791' ) ),
		2 => array( __( 'Reserve your seat', 'concealed1791' ), __( 'Book online in minutes and get a confirmation with everything you need to bring.', 'concealed1791' ) ),
		3 => array( __( 'Train and carry with confidence', 'concealed1791' ), __( 'Learn from certified instructors and leave with the skills — and certificate — you came for.', 'concealed1791' ) ),
	);
	foreach ( $steps as $i => $s ) {
		$d[ "step{$i}_title" ] = $s[0];
		$d[ "step{$i}_text" ]  = $s[1];
	}

	/**
	 * Filter the front page content defaults.
	 *
	 * @param array $d key => default.
	 */
	return apply_filters( 'c1791_defaults', $d );
}

/**
 * Get a front page theme mod with its default.
 *
 * @param string $key Key without the c1791_ prefix.
 * @return mixed
 */
function c1791_mod( $key ) {
	$defaults = c1791_defaults();
	return get_theme_mod( 'c1791_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Sanitize a checkbox.
 *
 * @param mixed $value Value.
 * @return bool
 */
function c1791_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Sanitize an icon choice.
 *
 * @param string $value Value.
 * @return string
 */
function c1791_sanitize_icon( $value ) {
	return array_key_exists( $value, c1791_icon_choices() ) ? $value : 'shield';
}

/**
 * Sanitize a color that may be blank (blank = use the default).
 *
 * @param string $value Value.
 * @return string
 */
function c1791_sanitize_color_or_empty( $value ) {
	$value = sanitize_hex_color( trim( (string) $value ) );
	return $value ? strtolower( $value ) : '';
}

/**
 * Register Customizer sections and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function c1791_customize_register( $wp_customize ) {
	$d = c1791_defaults();

	$add = function ( $key, $section, $label, $type = 'text', $extra = array() ) use ( $wp_customize, $d ) {
		$sanitize = array(
			'text'     => 'sanitize_text_field',
			'textarea' => 'sanitize_textarea_field',
			'html'     => 'wp_kses_post',
			'link'     => 'c1791_sanitize_link',
			'url'      => 'esc_url_raw',
			'checkbox' => 'c1791_sanitize_checkbox',
			'number'   => 'absint',
			'image'    => 'esc_url_raw',
			'icon'     => 'c1791_sanitize_icon',
		);
		$id       = 'c1791_' . $key;

		$wp_customize->add_setting(
			$id,
			array(
				'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
				'sanitize_callback' => $sanitize[ $type ],
			)
		);

		$args = array_merge(
			array(
				'label'   => $label,
				'section' => $section,
			),
			$extra
		);

		if ( 'image' === $type ) {
			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $id, $args ) );
			return;
		}
		$control_type = array(
			'html' => 'textarea',
			'link' => 'text',
			'icon' => 'select',
		);
		$args['type'] = isset( $control_type[ $type ] ) ? $control_type[ $type ] : $type;
		if ( 'icon' === $type ) {
			$args['choices'] = c1791_icon_choices();
		}
		$wp_customize->add_control( $id, $args );
	};

	$wp_customize->add_panel(
		'c1791_home',
		array(
			'title'       => __( 'Concealed 1791: Home Page', 'concealed1791' ),
			'description' => sprintf(
				/* translators: %s: Theme Settings URL. */
				__( 'Content for the front page sections. Menus, sidebars, footer, colors, backgrounds and plugin integrations are in <a href="%s">Appearance → Theme Settings</a>.', 'concealed1791' ),
				esc_url( admin_url( 'themes.php?page=c1791-settings' ) )
			),
			'priority'    => 30,
		)
	);

	$sections = array(
		'c1791_colors'       => __( 'Colors', 'concealed1791' ),
		'c1791_hero'         => __( 'Hero', 'concealed1791' ),
		'c1791_trust'        => __( 'Certification Strip', 'concealed1791' ),
		'c1791_benefits'     => __( 'Benefits', 'concealed1791' ),
		'c1791_classes'      => __( 'Upcoming Classes', 'concealed1791' ),
		'c1791_steps'        => __( 'How It Works', 'concealed1791' ),
		'c1791_packages'     => __( 'Packages & Pricing', 'concealed1791' ),
		'c1791_people'       => __( 'Instructors & Reviews', 'concealed1791' ),
		'c1791_faq'          => __( 'FAQ', 'concealed1791' ),
		'c1791_resources'    => __( 'Articles & Shop', 'concealed1791' ),
		'c1791_newsletter'   => __( 'Newsletter', 'concealed1791' ),
	);
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title' => $title,
				'panel' => 'c1791_home',
			)
		);
	}

	$link_help = array( 'description' => __( 'Relative ("/classes/"), absolute, or an anchor ("#faq").', 'concealed1791' ) );

	// Colors are stored in the c1791_settings option shared with Theme Settings.
	$wp_customize->get_section( 'c1791_colors' )->description = sprintf(
		/* translators: %s: settings page URL. */
		__( 'Leave a color blank to use the default. Presets, readability checks and the flag and target backgrounds are in <a href="%s">Appearance → Theme Settings → Colors & Style</a>.', 'concealed1791' ),
		esc_url( admin_url( 'themes.php?page=c1791-settings&tab=colors' ) )
	);
	$defaults = c1791_setting_defaults();
	foreach ( c1791_color_fields() as $key => $field ) {
		$id = 'c1791_settings[' . $key . ']';
		$wp_customize->add_setting(
			$id,
			array(
				'type'              => 'option',
				'default'           => '',
				'sanitize_callback' => 'c1791_sanitize_color_or_empty',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'c1791_color_' . $key,
				array(
					'label'       => $field[0],
					'section'     => 'c1791_colors',
					'settings'    => $id,
					/* translators: %s: color hex. */
					'description' => sprintf( __( 'Default: %s', 'concealed1791' ), $defaults[ $key ] ),
				)
			)
		);
	}

	// Hero.
	$add( 'hero_eyebrow', 'c1791_hero', __( 'Badge above the headline', 'concealed1791' ) );
	$add( 'hero_title', 'c1791_hero', __( 'Headline (wrap a word in <em> to highlight it)', 'concealed1791' ), 'html' );
	$add( 'hero_text', 'c1791_hero', __( 'Lead text', 'concealed1791' ), 'textarea' );
	$add( 'hero_points', 'c1791_hero', __( 'Checklist (one per line)', 'concealed1791' ), 'textarea' );
	$add( 'hero_btn1_text', 'c1791_hero', __( 'Main button text', 'concealed1791' ) );
	$add( 'hero_btn1_url', 'c1791_hero', __( 'Main button link', 'concealed1791' ), 'link', $link_help );
	$add( 'hero_btn2_text', 'c1791_hero', __( 'Second button text', 'concealed1791' ) );
	$add( 'hero_btn2_url', 'c1791_hero', __( 'Second button link', 'concealed1791' ), 'link', $link_help );
	$add( 'hero_card', 'c1791_hero', __( 'Show the next upcoming class as a card', 'concealed1791' ), 'checkbox' );
	$add( 'hero_card_label', 'c1791_hero', __( 'Card label', 'concealed1791' ) );
	$add( 'hero_rating', 'c1791_hero', __( 'Show the average review rating', 'concealed1791' ), 'checkbox' );
	$add(
		'hero_image',
		'c1791_hero',
		__( 'Hero photo', 'concealed1791' ),
		'image',
		array( 'description' => __( 'Used when Theme Settings → Colors & Style → Front page hero is set to "Hero photo". The flag or target background is used otherwise.', 'concealed1791' ) )
	);

	// Certification strip.
	$add( 'trust_enable', 'c1791_trust', __( 'Show the certification strip', 'concealed1791' ), 'checkbox' );
	$add(
		'trust_items',
		'c1791_trust',
		__( 'Items (one per line)', 'concealed1791' ),
		'textarea',
		array(
			/* translators: %s: list of icon names. */
			'description' => sprintf( __( 'Add " | icon" to choose an icon, for example "USCCA Certified | shield". Icons: %s.', 'concealed1791' ), implode( ', ', array_keys( c1791_icon_choices() ) ) ),
		)
	);

	// Benefits.
	$add( 'benefits_enable', 'c1791_benefits', __( 'Show the benefits section', 'concealed1791' ), 'checkbox' );
	$add( 'benefits_eyebrow', 'c1791_benefits', __( 'Eyebrow', 'concealed1791' ) );
	$add( 'benefits_title', 'c1791_benefits', __( 'Title', 'concealed1791' ) );
	$add( 'benefits_text', 'c1791_benefits', __( 'Intro', 'concealed1791' ), 'textarea' );
	for ( $i = 1; $i <= 6; $i++ ) {
		/* translators: %d: benefit number. */
		$add( "benefit{$i}_title", 'c1791_benefits', sprintf( __( 'Benefit %d title (empty hides it)', 'concealed1791' ), $i ) );
		/* translators: %d: benefit number. */
		$add( "benefit{$i}_tag", 'c1791_benefits', sprintf( __( 'Benefit %d label', 'concealed1791' ), $i ) );
		/* translators: %d: benefit number. */
		$add( "benefit{$i}_text", 'c1791_benefits', sprintf( __( 'Benefit %d text', 'concealed1791' ), $i ), 'textarea' );
		/* translators: %d: benefit number. */
		$add( "benefit{$i}_icon", 'c1791_benefits', sprintf( __( 'Benefit %d icon', 'concealed1791' ), $i ), 'icon' );
	}

	// Classes.
	$add( 'classes_enable', 'c1791_classes', __( 'Show upcoming classes', 'concealed1791' ), 'checkbox' );
	$add( 'classes_eyebrow', 'c1791_classes', __( 'Eyebrow', 'concealed1791' ) );
	$add( 'classes_title', 'c1791_classes', __( 'Title', 'concealed1791' ) );
	$add( 'classes_text', 'c1791_classes', __( 'Intro', 'concealed1791' ), 'textarea' );
	$add(
		'classes_count',
		'c1791_classes',
		__( 'Number of classes', 'concealed1791' ),
		'number',
		array(
			'input_attrs' => array(
				'min' => 1,
				'max' => 12,
			),
		)
	);

	// Steps.
	$add( 'steps_enable', 'c1791_steps', __( 'Show "How it works"', 'concealed1791' ), 'checkbox' );
	$add( 'steps_eyebrow', 'c1791_steps', __( 'Eyebrow', 'concealed1791' ) );
	$add( 'steps_title', 'c1791_steps', __( 'Title', 'concealed1791' ) );
	for ( $i = 1; $i <= 3; $i++ ) {
		/* translators: %d: step number. */
		$add( "step{$i}_title", 'c1791_steps', sprintf( __( 'Step %d title', 'concealed1791' ), $i ) );
		/* translators: %d: step number. */
		$add( "step{$i}_text", 'c1791_steps', sprintf( __( 'Step %d text', 'concealed1791' ), $i ), 'textarea' );
	}

	// Packages.
	$wp_customize->get_section( 'c1791_packages' )->description = sprintf(
		/* translators: %s: packages admin URL. */
		__( 'Add and edit the pricing cards under <a href="%s">Packages</a>.', 'concealed1791' ),
		esc_url( admin_url( 'edit.php?post_type=c1791_package' ) )
	);
	$add( 'packages_enable', 'c1791_packages', __( 'Show packages', 'concealed1791' ), 'checkbox' );
	$add( 'packages_eyebrow', 'c1791_packages', __( 'Eyebrow', 'concealed1791' ) );
	$add( 'packages_title', 'c1791_packages', __( 'Title', 'concealed1791' ) );
	$add( 'packages_text', 'c1791_packages', __( 'Intro', 'concealed1791' ), 'textarea' );
	$add( 'packages_toggle_1', 'c1791_packages', __( 'Pricing switch: first option', 'concealed1791' ) );
	$add( 'packages_toggle_2', 'c1791_packages', __( 'Pricing switch: second option', 'concealed1791' ), 'text', array( 'description' => __( 'The switch appears when at least one package has a second price.', 'concealed1791' ) ) );
	$add( 'packages_note', 'c1791_packages', __( 'Note under the cards', 'concealed1791' ), 'textarea' );

	// Instructors & reviews.
	$add( 'instructors_enable', 'c1791_people', __( 'Show instructors', 'concealed1791' ), 'checkbox' );
	$add( 'instructors_eyebrow', 'c1791_people', __( 'Instructors eyebrow', 'concealed1791' ) );
	$add( 'instructors_title', 'c1791_people', __( 'Instructors title', 'concealed1791' ) );
	$add( 'instructors_text', 'c1791_people', __( 'Instructors intro', 'concealed1791' ), 'textarea' );
	$add( 'testimonials_enable', 'c1791_people', __( 'Show reviews', 'concealed1791' ), 'checkbox' );
	$add( 'testimonials_eyebrow', 'c1791_people', __( 'Reviews eyebrow', 'concealed1791' ) );
	$add( 'testimonials_title', 'c1791_people', __( 'Reviews title', 'concealed1791' ) );

	// FAQ.
	$wp_customize->get_section( 'c1791_faq' )->description = sprintf(
		/* translators: %s: FAQ admin URL. */
		__( 'Add and order questions under <a href="%s">FAQs</a>. For a full FAQ page, create a page with the "FAQ" template.', 'concealed1791' ),
		esc_url( admin_url( 'edit.php?post_type=c1791_faq' ) )
	);
	$add( 'faq_enable', 'c1791_faq', __( 'Show the FAQ', 'concealed1791' ), 'checkbox' );
	$add( 'faq_eyebrow', 'c1791_faq', __( 'Eyebrow', 'concealed1791' ) );
	$add( 'faq_title', 'c1791_faq', __( 'Title', 'concealed1791' ) );
	$add( 'faq_text', 'c1791_faq', __( 'Intro', 'concealed1791' ), 'textarea' );
	$add(
		'faq_count',
		'c1791_faq',
		__( 'Number of questions', 'concealed1791' ),
		'number',
		array(
			'input_attrs' => array(
				'min' => 1,
				'max' => 20,
			),
		)
	);

	// Articles & shop.
	$add( 'resources_enable', 'c1791_resources', __( 'Show latest articles', 'concealed1791' ), 'checkbox' );
	$add( 'resources_eyebrow', 'c1791_resources', __( 'Articles eyebrow', 'concealed1791' ) );
	$add( 'resources_title', 'c1791_resources', __( 'Articles title', 'concealed1791' ) );
	$add( 'resources_text', 'c1791_resources', __( 'Articles intro', 'concealed1791' ), 'textarea' );
	$add( 'shop_enable', 'c1791_resources', __( 'Show featured products (WooCommerce)', 'concealed1791' ), 'checkbox' );
	$add( 'shop_eyebrow', 'c1791_resources', __( 'Shop eyebrow', 'concealed1791' ) );
	$add( 'shop_title', 'c1791_resources', __( 'Shop title', 'concealed1791' ) );
	$add( 'shop_text', 'c1791_resources', __( 'Shop intro', 'concealed1791' ), 'textarea' );

	// Newsletter.
	$add( 'news_enable', 'c1791_newsletter', __( 'Show the newsletter band', 'concealed1791' ), 'checkbox' );
	$add( 'news_title', 'c1791_newsletter', __( 'Title', 'concealed1791' ) );
	$add( 'news_text', 'c1791_newsletter', __( 'Text', 'concealed1791' ), 'textarea' );
	$add( 'news_button', 'c1791_newsletter', __( 'Button text', 'concealed1791' ) );
	$add(
		'news_action',
		'c1791_newsletter',
		__( 'Form action URL (Mailchimp, ConvertKit, etc.)', 'concealed1791' ),
		'url',
		array( 'description' => __( 'Not needed with MailPoet: choose a MailPoet list or form under Appearance → Theme Settings → Integrations instead (that takes priority).', 'concealed1791' ) )
	);
	$add( 'news_field', 'c1791_newsletter', __( 'Email field name', 'concealed1791' ), 'text', array( 'description' => __( 'Mailchimp uses EMAIL; ConvertKit uses email_address.', 'concealed1791' ) ) );
}
add_action( 'customize_register', 'c1791_customize_register' );
