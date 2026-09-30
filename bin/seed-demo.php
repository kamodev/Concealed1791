<?php
/**
 * Seed demo content for previewing the theme.
 *
 * Usage: wp eval-file bin/seed-demo.php
 *
 * Creates class types, classes, instructors, packages, FAQs, testimonials,
 * articles, pages, menus, widgets, business details and (with WooCommerce)
 * store pages and products. Safe to re-run: it skips anything that already
 * exists by title.
 *
 * Instructors, reviews, prices and dates are SAMPLE content for the demo;
 * replace them before going live.
 *
 * @package Concealed1791
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a post unless one with the same title and type exists.
 *
 * @param array $args wp_insert_post args (+ 'meta', 'terms').
 * @return int Post ID.
 */
function c1791_seed_post( $args ) {
	$existing = get_posts(
		array(
			'post_type'      => $args['post_type'],
			'title'          => $args['post_title'],
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $existing ) {
		return $existing[0];
	}

	$meta  = isset( $args['meta'] ) ? $args['meta'] : array();
	$terms = isset( $args['terms'] ) ? $args['terms'] : array();
	unset( $args['meta'], $args['terms'] );

	$id = wp_insert_post( array_merge( array( 'post_status' => 'publish' ), $args ) );
	foreach ( $meta as $key => $value ) {
		update_post_meta( $id, $key, $value );
	}
	foreach ( $terms as $taxonomy => $slugs ) {
		wp_set_object_terms( $id, $slugs, $taxonomy );
	}
	return $id;
}

/**
 * Paragraph blocks from plain paragraphs.
 *
 * @param string ...$paragraphs Paragraphs.
 * @return string
 */
function c1791_seed_blocks( ...$paragraphs ) {
	$out = '';
	foreach ( $paragraphs as $p ) {
		if ( 0 === strpos( $p, '## ' ) ) {
			$out .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . esc_html( substr( $p, 3 ) ) . "</h2>\n<!-- /wp:heading -->\n\n";
		} else {
			$out .= "<!-- wp:paragraph -->\n<p>" . $p . "</p>\n<!-- /wp:paragraph -->\n\n";
		}
	}
	return $out;
}

$today = current_time( 'timestamp' );

// Business details (Theme Settings → General) and a few style choices.
$settings = get_option( 'c1791_settings', array() );
$settings = is_array( $settings ) ? $settings : array();
$settings = array_merge(
	array(
		'phone'           => '(303) 520-0899',
		'email'           => 'cockedandloaded1791@gmail.com',
		'address'         => "11999 Colmans Way\nBroomfield, CO 80020",
		'map_url'         => 'https://www.google.com/maps/search/?api=1&query=11999+Colmans+Way+Broomfield+CO+80020',
		'hours'           => "Group classes: weekends & select evenings\nPrivate lessons: by appointment\nPhone & text: Mon–Sat, 8 AM – 6 PM",
		'social_facebook' => 'https://www.facebook.com/aatacticalco',
		'reviews_url'     => 'https://www.facebook.com/aatacticalco/reviews',
		'topbar_text'     => 'NRA & USCCA certified instructors · Broomfield, Colorado',
	),
	$settings
);
update_option( 'c1791_settings', $settings );

// Class types.
$types = array(
	'ccw-certification'   => array( 'CCW Certification', 'Colorado concealed carry certification classes.' ),
	'defensive-shooting'  => array( 'Defensive Shooting', 'Fundamentals and skill-building for defensive handgun use.' ),
	'home-defense'        => array( 'Home Defense', 'Planning, preparing and practicing to protect your home and family.' ),
	'scenario-training'   => array( 'Scenario Training', 'Decision-making under realistic, time-pressured scenarios.' ),
	'active-threat'       => array( 'Active Threat', 'Countering the mass shooter threat: awareness, response and first aid.' ),
	'cpr-first-aid'       => array( 'CPR & First Aid', 'American Red Cross CPR/AED and First Aid certification.' ),
	'private-lessons'     => array( 'Private Lessons', 'One-on-one and small-group coaching on your schedule.' ),
);
foreach ( $types as $slug => $type ) {
	if ( ! term_exists( $slug, 'c1791_class_type' ) ) {
		wp_insert_term(
			$type[0],
			'c1791_class_type',
			array(
				'slug'        => $slug,
				'description' => $type[1],
			)
		);
	}
}

// Instructors (sample profiles).
$instructors    = array(
	array( 'Alex Carter', 'Lead Firearms Instructor', 'NRA Pistol Instructor · USCCA Certified · RSO', 1 ),
	array( 'Jordan Reyes', 'Defensive Shooting Instructor', 'USCCA Certified · NRA Personal Protection', 2 ),
	array( 'Sam Whitaker', 'CPR & First Aid Instructor', 'American Red Cross Instructor · EMT', 3 ),
);
$instructor_ids = array();
foreach ( $instructors as $i ) {
	$instructor_ids[] = c1791_seed_post(
		array(
			'post_type'    => 'c1791_instructor',
			'post_title'   => $i[0],
			'post_excerpt' => $i[1],
			'post_content' => c1791_seed_blocks(
				'<em>Sample instructor profile — replace it with your instructor\'s real bio, photo and credentials.</em>',
				sprintf( '%s teaches with a safety-first, no-ego approach: short explanations, lots of coached repetitions, and plenty of time for questions. Students of every experience level leave class more confident and more capable.', $i[0] )
			),
			'menu_order'   => $i[3],
			'meta'         => array(
				'_c1791_role'        => $i[1],
				'_c1791_credentials' => $i[2],
			),
		)
	);
}

// Classes, scheduled relative to today so they always appear as upcoming (sample dates and prices).
$classes = array(
	array( 'Colorado Concealed Carry (CCW) Certification', 5, 0, '8:00 AM – 5:00 PM', '8 hours', 'Classroom & range – Broomfield, CO', '$149', 12, 4, 'beginner', 'ccw-certification', array( 0 ) ),
	array( 'Defensive Shooting Fundamentals', 12, 0, '9:00 AM – 1:00 PM', '4 hours', 'Range – Broomfield, CO', '$129', 10, 6, 'beginner', 'defensive-shooting', array( 1 ) ),
	array( 'Home Defense Strategies', 19, 0, '9:00 AM – 3:00 PM', '6 hours', 'Classroom & range – Broomfield, CO', '$139', 14, 2, 'intermediate', 'home-defense', array( 0, 1 ) ),
	array( 'Countering the Mass Shooter Threat', 26, 0, '6:00 PM – 9:00 PM', '3 hours', 'Classroom – Broomfield, CO', '$79', 24, 15, '', 'active-threat', array( 1, 2 ) ),
	array( 'Scenario-Based Defensive Training', 33, 34, '8:00 AM – 4:00 PM', '2 days', 'Training facility – Broomfield, CO', '$279', 8, 0, 'advanced', 'scenario-training', array( 0, 1 ) ),
	array( 'American Red Cross CPR/AED & First Aid', 40, 0, '9:00 AM – 2:00 PM', '5 hours', 'Classroom – Broomfield, CO', '$95', 12, 9, '', 'cpr-first-aid', array( 2 ) ),
	array( 'CCW Certification (Weekday Evening)', 47, 48, '5:30 PM – 9:30 PM', '2 evenings', 'Classroom & range – Broomfield, CO', '$149', 12, 10, 'beginner', 'ccw-certification', array( 0 ) ),
	array( 'CCW Certification (Saturday)', -14, 0, '8:00 AM – 5:00 PM', '8 hours', 'Classroom & range – Broomfield, CO', '$149', 12, 0, 'beginner', 'ccw-certification', array( 0 ) ),
);
$class_ids = array();
foreach ( $classes as $c ) {
	$is_ccw      = 'ccw-certification' === $c[10];
	$class_ids[] = c1791_seed_post(
		array(
			'post_type'    => 'c1791_class',
			'post_title'   => $c[0],
			'post_excerpt' => $is_ccw ? 'Everything you need to carry legally, safely and confidently in Colorado.' : 'Hands-on, instructor-led training in a small group.',
			'post_content' => c1791_seed_blocks(
				'This hands-on class takes you from the fundamentals to confident, safe action. Expect short lessons, lots of coached repetition, and plenty of time for questions.',
				'## What you will learn',
				'Safe handling and storage, the core fundamentals of accurate shooting, and the decision-making that matters before, during and after a defensive encounter. Classes are small, so every student gets individual coaching.',
				'<em>Sample class description — edit it under Classes.</em>'
			),
			'meta'         => array(
				'_c1791_start_date'  => gmdate( 'Y-m-d', $today + $c[1] * DAY_IN_SECONDS ),
				'_c1791_end_date'    => $c[2] ? gmdate( 'Y-m-d', $today + $c[2] * DAY_IN_SECONDS ) : '',
				'_c1791_time'        => $c[3],
				'_c1791_duration'    => $c[4],
				'_c1791_location'    => $c[5],
				'_c1791_price'       => $c[6],
				'_c1791_capacity'    => $c[7],
				'_c1791_seats_left'  => $c[8],
				'_c1791_level'       => $c[9],
				'_c1791_includes'    => $is_ccw
					? "Classroom instruction on Colorado carry law\nLive-fire qualification on the range\nCertificate of completion\nHandouts and reference materials"
					: "Instructor-led classroom session\nCoached hands-on practice\nCertificate of completion",
				'_c1791_bring'       => "Eye and ear protection\nA sturdy belt and holster (if you have one)\nWater and a snack\nNotebook and pen",
				'_c1791_prereqs'     => in_array( $c[9], array( 'intermediate', 'advanced' ), true ) ? 'A CCW or defensive shooting class, or equivalent experience safely drawing from a holster.' : '',
				'_c1791_instructors' => array_map(
					function ( $idx ) use ( $instructor_ids ) {
						return $instructor_ids[ $idx ];
					},
					$c[11]
				),
			),
			'terms'        => array( 'c1791_class_type' => array( $c[10] ) ),
		)
	);
}

// Packages (pricing cards, sample prices).
$ccw_link = get_term_link( 'ccw-certification', 'c1791_class_type' );
$packages = array(
	array(
		'CCW Certification',
		'The class most students start with.',
		'$149',
		'per person',
		'$269',
		'for two people',
		"8-hour Colorado CCW class\nClassroom plus live-fire range time\nColorado self-defense law module\nCertificate of completion\n-Range fees and ammunition",
		'',
		'',
		'Reserve My Seat',
		is_wp_error( $ccw_link ) ? '/classes/' : wp_make_link_relative( $ccw_link ),
		1,
	),
	array(
		'Defensive Pistol Bundle',
		'Certification plus the skills that keep you sharp.',
		'$349',
		'per person',
		'$649',
		'for two people',
		"CCW Certification\nDefensive Shooting Fundamentals\nHome Defense Strategies\nPriority registration for new dates\nSave compared with booking separately",
		'1',
		'Most popular',
		'Get the Bundle',
		'/classes/',
		2,
	),
	array(
		'Private Lessons',
		'One-on-one coaching on your schedule.',
		'$95',
		'per hour',
		'$150',
		'per hour for two',
		"Your pace, your goals\nAny experience level\nGreat for brand-new shooters\nFlexible weekday and weekend times",
		'',
		'',
		'Book a Lesson',
		'/contact-us/',
		3,
	),
);
foreach ( $packages as $p ) {
	c1791_seed_post(
		array(
			'post_type'    => 'c1791_package',
			'post_title'   => $p[0],
			'post_excerpt' => $p[1],
			'menu_order'   => $p[11],
			'meta'         => array(
				'_c1791_price'       => $p[2],
				'_c1791_period'      => $p[3],
				'_c1791_price_alt'   => $p[4],
				'_c1791_period_alt'  => $p[5],
				'_c1791_features'    => $p[6],
				'_c1791_featured'    => $p[7],
				'_c1791_badge'       => $p[8],
				'_c1791_button_text' => $p[9],
				'_c1791_button_url'  => $p[10],
			),
		)
	);
}

// FAQs.
$faqs = array(
	array( 'I\'ve never fired a gun. Is that okay?', 'Absolutely. Our beginner classes start from zero and put safety first. Small class sizes mean you get plenty of one-on-one coaching.' ),
	array( 'Does the CCW class count toward a Colorado concealed handgun permit?', 'Our CCW class is designed for Colorado concealed handgun permit applicants. Your county sheriff\'s office has the final say on what it accepts, so confirm the current requirements before you apply.' ),
	array( 'Do I need to own a handgun to take a class?', 'No. Many students take a class before buying their first handgun. Contact us before class and we\'ll help you plan what to bring.' ),
	array( 'What should I bring?', 'Each class page lists exactly what to bring. For range classes that usually means eye and ear protection, a holster and ammunition; for classroom-only classes, just a notebook.' ),
	array( 'Do you offer private or group training?', 'Yes. We offer one-on-one and small-group lessons, plus classes for churches, businesses and community groups. Call or text to set it up.' ),
	array( 'How do I reschedule?', 'Contact us as soon as you know you can\'t make it and we\'ll help you find another date.' ),
	array( 'Are your instructors certified?', 'Yes. Our instructors are NRA and USCCA certified, and our CPR and First Aid classes follow the American Red Cross program.' ),
);
foreach ( $faqs as $i => $f ) {
	c1791_seed_post(
		array(
			'post_type'    => 'c1791_faq',
			'post_title'   => $f[0],
			'post_content' => c1791_seed_blocks( $f[1] ),
			'menu_order'   => $i + 1,
		)
	);
}

// Testimonials (samples).
$quotes = array(
	array( 'Megan T.', 'I was nervous about my first class and left feeling confident. Patient instructors, clear explanations and a lot of hands-on practice.', 'CCW Certification' ),
	array( 'Chris L.', 'Practical, no-nonsense training. The Colorado law section alone was worth it — I finally understand what I can and can\'t do.', 'CCW Certification' ),
	array( 'Dana & Rob K.', 'We took the home defense class together and came away with an actual plan for our family. Highly recommend it for couples.', 'Home Defense Strategies' ),
	array( 'Marcus W.', 'Small class, lots of reps and individual feedback. My draw and follow-up shots improved more in one morning than in a year on my own.', 'Defensive Shooting Fundamentals' ),
);
foreach ( $quotes as $i => $q ) {
	c1791_seed_post(
		array(
			'post_type'    => 'c1791_testimonial',
			'post_title'   => $q[0],
			'post_content' => $q[1],
			'menu_order'   => $i,
			'meta'         => array(
				'_c1791_rating'      => '5',
				'_c1791_class_taken' => $q[2],
				'_c1791_source'      => 'Sample review',
			),
		)
	);
}
delete_transient( 'c1791_rating_summary' );

// Articles.
$cats = array();
foreach ( array( 'Colorado Law', 'Training Tips', 'Gear' ) as $name ) {
	$term          = term_exists( $name, 'category' );
	$cats[ $name ] = $term ? (int) $term['term_id'] : (int) wp_insert_term( $name, 'category' )['term_id'];
}
$posts = array(
	array( 'What to Expect at Your First CCW Class', 'Training Tips', 'Walking into your first concealed carry class can feel intimidating. Here is how the day usually runs, what to bring, and how to get the most out of it.' ),
	array( '5 Dry-Fire Drills You Can Practice at Home', 'Training Tips', 'Dry fire is the cheapest, most effective way to build a smooth draw and a clean trigger press. Always start by unloading and moving ammunition to another room.' ),
	array( 'Choosing Your First Concealed Carry Holster', 'Gear', 'A good holster covers the trigger guard, holds the gun securely and lets you draw the same way every time. Comfort decides whether you actually carry it.' ),
	array( 'Carrying in Colorado: Questions to Ask Before You Apply', 'Colorado Law', 'Before you apply for a concealed handgun permit, check your county sheriff\'s current requirements and learn where carry is restricted. This article is general information, not legal advice.' ),
);
foreach ( $posts as $i => $p ) {
	c1791_seed_post(
		array(
			'post_type'     => 'post',
			'post_author'   => 1,
			'post_title'    => $p[0],
			'post_date'     => gmdate( 'Y-m-d H:i:s', $today - ( $i + 1 ) * 4 * DAY_IN_SECONDS ),
			'post_category' => array( $cats[ $p[1] ] ),
			'post_excerpt'  => $p[2],
			'post_content'  => c1791_seed_blocks(
				$p[2],
				'## Start with the fundamentals',
				'Safety rules first, every time. Then build skills in small, repeatable steps and practice them until they are automatic.',
				'## Train with a professional',
				'A certified instructor can spot problems you won\'t notice on your own. A few hours of coaching saves months of practicing the wrong thing.'
			),
		)
	);
}

// Pages.
$about   = c1791_seed_post(
	array(
		'post_type'    => 'page',
		'post_title'   => 'About Us',
		'post_name'    => 'about-us',
		'post_excerpt' => 'Firearms training, CCW certification and CPR/First Aid classes in Broomfield, Colorado.',
		'post_content' => c1791_seed_blocks(
			'A & A Tactical provides self-defense training, firearms classes, CCW certification classes and American Red Cross CPR/First Aid certification. All of our instructors are NRA and USCCA certified.',
			'We believe responsible gun ownership starts with good training: safe handling, sound fundamentals and a clear understanding of the law. Whether you are brand new or looking to sharpen your skills, we\'ll meet you where you are.'
		),
	)
);
$contact = c1791_seed_post(
	array(
		'post_type'    => 'page',
		'post_title'   => 'Contact Us',
		'post_name'    => 'contact-us',
		'post_excerpt' => 'Questions, private lessons and group training requests.',
		'post_content' => c1791_seed_blocks(
			'Call or text <a href="tel:+13035200899">(303) 520-0899</a> or email <a href="mailto:cockedandloaded1791@gmail.com">cockedandloaded1791@gmail.com</a>. We usually reply within one business day.',
			'Private lessons and group classes for churches, businesses and community groups are available by appointment.'
		),
	)
);
$faq     = c1791_seed_post(
	array(
		'post_type'    => 'page',
		'post_title'   => 'FAQ',
		'post_name'    => 'faq',
		'post_excerpt' => 'Answers to the questions we hear most.',
		'post_content' => '',
		'meta'         => array( '_wp_page_template' => 'page-templates/faq.php' ),
	)
);
$blog    = c1791_seed_post(
	array(
		'post_type'    => 'page',
		'post_title'   => 'Articles',
		'post_name'    => 'articles',
		'post_excerpt' => 'Colorado carry law, training tips and gear advice from our instructors.',
	)
);
$home    = c1791_seed_post(
	array(
		'post_type'  => 'page',
		'post_title' => 'Home',
		'post_name'  => 'home',
	)
);
$privacy = (int) get_option( 'wp_page_for_privacy_policy' );
if ( $privacy && 'publish' !== get_post_status( $privacy ) ) {
	wp_update_post(
		array(
			'ID'          => $privacy,
			'post_status' => 'publish',
		)
	);
}
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );
update_option( 'page_for_posts', $blog );

// Menus.
$locations = get_theme_mod( 'nav_menu_locations', array() );
if ( ! wp_get_nav_menu_object( 'Main Menu' ) ) {
	$menu_id  = wp_create_nav_menu( 'Main Menu' );
	$add_item = function ( $args ) use ( $menu_id ) {
		return wp_update_nav_menu_item( $menu_id, 0, array_merge( array( 'menu-item-status' => 'publish' ), $args ) );
	};
	$classes_item = $add_item(
		array(
			'menu-item-title' => 'Classes',
			'menu-item-url'   => get_post_type_archive_link( 'c1791_class' ),
			'menu-item-type'  => 'custom',
		)
	);
	foreach ( array_keys( $types ) as $slug ) {
		$term = get_term_by( 'slug', $slug, 'c1791_class_type' );
		$add_item(
			array(
				'menu-item-object'    => 'c1791_class_type',
				'menu-item-object-id' => $term->term_id,
				'menu-item-type'      => 'taxonomy',
				'menu-item-parent-id' => $classes_item,
			)
		);
	}
	$add_item(
		array(
			'menu-item-title' => 'Instructors',
			'menu-item-url'   => get_post_type_archive_link( 'c1791_instructor' ),
			'menu-item-type'  => 'custom',
		)
	);
	foreach ( array( $faq, $blog, $about, $contact ) as $page_id ) {
		$add_item(
			array(
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page_id,
				'menu-item-type'      => 'post_type',
			)
		);
	}
	$locations['primary'] = $menu_id;
}
if ( ! wp_get_nav_menu_object( 'Legal' ) ) {
	$legal_id = wp_create_nav_menu( 'Legal' );
	foreach ( array_filter( array( $privacy, $contact ) ) as $page_id ) {
		wp_update_nav_menu_item(
			$legal_id,
			0,
			array(
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page_id,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
	}
	$locations['footer'] = $legal_id;
}
set_theme_mod( 'nav_menu_locations', $locations );

// Sidebar widgets (block widgets).
$sidebars = get_option( 'sidebars_widgets', array() );
$blocks   = get_option( 'widget_block', array() );
$next     = $blocks ? max( array_filter( array_keys( $blocks ), 'is_int' ) + array( 0 ) ) + 1 : 2;
$widgets  = array(
	'sidebar-blog' => array(
		'<!-- wp:search {"label":"Search articles","buttonText":"Search"} /-->',
		'<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Latest Articles</h3><!-- /wp:heading --><!-- wp:latest-posts {"postsToShow":4} /-->',
		'<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Train With Us</h3><!-- /wp:heading --><!-- wp:paragraph --><p>CCW certification, defensive shooting, home defense and CPR classes in Broomfield.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="' . esc_url( get_post_type_archive_link( 'c1791_class' ) ) . '">View Classes</a></div><!-- /wp:button --></div><!-- /wp:buttons -->',
	),
	'sidebar-page' => array(
		'<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Questions?</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Call or text (303) 520-0899 for private lessons and group training.</p><!-- /wp:paragraph -->',
		'<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Quick Links</h3><!-- /wp:heading --><!-- wp:page-list /-->',
	),
);
// On activation WordPress moves its default widgets into the first sidebar;
// the demo replaces them once (later runs keep whatever you set).
$first_run = ! get_option( 'c1791_demo_widgets' );
foreach ( $widgets as $sidebar_id => $contents ) {
	if ( ! empty( $sidebars[ $sidebar_id ] ) && ! $first_run ) {
		continue;
	}
	$sidebars[ $sidebar_id ] = array();
	foreach ( $contents as $content ) {
		$blocks[ $next ]           = array( 'content' => $content );
		$sidebars[ $sidebar_id ][] = 'block-' . $next;
		++$next;
	}
}
$blocks['_multiwidget'] = 1;
update_option( 'widget_block', $blocks );
update_option( 'sidebars_widgets', $sidebars );
update_option( 'c1791_demo_widgets', 1, false );

// WooCommerce: store pages, class seats, a package bundle and gear (sample products).
if ( class_exists( 'WooCommerce' ) ) {
	if ( class_exists( 'WC_Install' ) ) {
		WC_Install::create_pages();
	}

	$make_product = function ( $name, $price, $sale, $text, $args = array() ) {
		$found = get_posts(
			array(
				'post_type'   => 'product',
				'title'       => $name,
				'post_status' => 'any',
				'fields'      => 'ids',
			)
		);
		if ( $found ) {
			return $found[0];
		}
		$product = new WC_Product_Simple();
		$product->set_name( $name );
		$product->set_status( 'publish' );
		$product->set_regular_price( $price );
		if ( $sale ) {
			$product->set_sale_price( $sale );
		}
		$product->set_short_description( $text );
		$product->set_description( $text . ' Sample product for the demo.' );
		if ( ! empty( $args['virtual'] ) ) {
			$product->set_virtual( true );
			$product->set_sold_individually( true );
		}
		if ( isset( $args['stock'] ) ) {
			$product->set_manage_stock( true );
			$product->set_stock_quantity( $args['stock'] );
		}
		if ( ! empty( $args['featured'] ) ) {
			$product->set_featured( true );
		}
		if ( ! empty( $args['hidden'] ) ) {
			$product->set_catalog_visibility( 'hidden' );
		}
		return $product->save();
	};

	// A class seat product: the class's Register button goes to checkout, and stock is its seats left.
	$seat = $make_product(
		'CCW Certification – Class Seat',
		'149',
		'',
		'One seat in the Colorado Concealed Carry (CCW) Certification class.',
		array(
			'virtual' => true,
			'stock'   => 4,
			'hidden'  => true,
		)
	);
	update_post_meta( $class_ids[0], '_c1791_wc_product', $seat );

	$bundle = get_posts(
		array(
			'post_type' => 'c1791_package',
			'title'     => 'Defensive Pistol Bundle',
			'fields'    => 'ids',
		)
	);
	if ( $bundle ) {
		$bundle_product = $make_product( 'Defensive Pistol Bundle', '349', '', 'CCW Certification, Defensive Shooting Fundamentals and Home Defense Strategies.', array( 'virtual' => true, 'hidden' => true ) );
		update_post_meta( $bundle[0], '_c1791_wc_product', $bundle_product );
	}

	$gear = array(
		array( 'IWB Concealment Holster', '59.00', '49.00', 'Kydex inside-the-waistband holster with adjustable retention and cant.' ),
		array( 'Electronic Hearing Protection', '69.00', '', 'Low-profile electronic earmuffs that amplify speech and block gunshots.' ),
		array( 'Quick-Access Handgun Safe', '129.00', '', 'Biometric and keypad handgun safe for fast, secure bedside storage.' ),
		array( 'Dry-Fire Training Rounds (10-pack)', '19.00', '', 'Inert training rounds for safe dry-fire and malfunction drills.' ),
	);
	foreach ( $gear as $g ) {
		$make_product( $g[0], $g[1], $g[2], $g[3], array( 'featured' => true ) );
	}
}

// Demo newsletter action so the form renders (replace with MailPoet or your provider).
if ( ! get_theme_mod( 'c1791_news_action' ) ) {
	set_theme_mod( 'c1791_news_action', home_url( '/?newsletter=demo' ) );
}

flush_rewrite_rules();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Demo content ready.' );
}
