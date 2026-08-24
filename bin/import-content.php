<?php
/**
 * One-time content migration from the static tajwar-port site.
 * Run with: wp eval-file bin/import-content.php
 */

if ( ! defined( 'WP_CLI' ) ) {
	die( "Run this via: wp eval-file bin/import-content.php\n" );
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$experience_entries = array(
	array(
		'role' => 'Web Developer', 'company' => 'bitBirds Solutions', 'location' => 'Dhaka, Bangladesh',
		'date_start' => 'Jun 2025', 'date_end' => '', 'is_current' => true,
		'bullets' => "Engineered and launched 50+ custom WordPress and Laravel websites, cutting average load times by 30% and elevating client SEO rankings.\nCreated 30+ custom WordPress plugins and themes, extending platform capabilities with tailored, client-specific functionality.\nServed as a go-to resource for debugging, performance profiling and feature scoping within an agile, cross-functional workflow.",
	),
	array(
		'role' => 'Freelance Web Developer', 'company' => 'Fiverr', 'location' => 'Remote, Worldwide',
		'date_start' => 'Aug 2018', 'date_end' => '', 'is_current' => true,
		'bullets' => "Independently deliver end-to-end web solutions — scoping, development and post-launch support — for clients across 20+ countries.\nSpecialise in Magento 2, Shopify, WordPress and PHP; known for pixel-perfect execution and SEO-conscious architecture.\nSustained a 5\xe2\x98\x85 rating across 190+ verified reviews, with 170+ repeat and referred clients.",
	),
	array(
		'role' => 'Web Developer', 'company' => 'Turning Point Zone', 'location' => 'Dhaka, Bangladesh',
		'date_start' => 'Jun 2024', 'date_end' => 'Apr 2025', 'is_current' => false,
		'bullets' => "Architected custom WooCommerce and WordPress stores from scratch, focused on speed, accessibility and conversion optimisation.\nCrafted original themes aligned tightly with each brand's visual identity and strategic goals.\nExtended store functionality through bespoke plugin development, reducing reliance on third-party tools.",
	),
	array(
		'role' => 'Chief Technology Officer', 'company' => 'Manage My Groceries', 'location' => 'Canada (Remote)',
		'date_start' => 'Jan 2020', 'date_end' => 'May 2022', 'is_current' => false,
		'bullets' => "Owned the full technology roadmap — infrastructure, team leadership and software delivery — across a grocery e-commerce platform.\nStreamlined helpdesk and internal tooling, measurably improving support response times and customer satisfaction.\nChampioned code quality standards, security audits and performance benchmarking; recruited and mentored junior developers.",
	),
	array(
		'role' => 'Web Developer', 'company' => 'AutoMaximizer', 'location' => 'Remote',
		'date_start' => 'Apr 2019', 'date_end' => 'Jun 2021', 'is_current' => false,
		'bullets' => "Built UI/UX-led automotive web experiences prioritising intuitive navigation and mobile-first responsiveness.\nRefined layouts, animations and interactive elements to reinforce brand identity and boost engagement.",
	),
);

foreach ( $experience_entries as $i => $entry ) {
	$post_id = wp_insert_post( array(
		'post_type'   => 'experience',
		'post_title'  => $entry['role'] . ' · ' . $entry['company'],
		'post_status' => 'publish',
		'menu_order'  => $i,
	) );
	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed to create experience {$entry['company']}: " . $post_id->get_error_message() );
		continue;
	}
	update_post_meta( $post_id, '_experience_role', $entry['role'] );
	update_post_meta( $post_id, '_experience_company', $entry['company'] );
	update_post_meta( $post_id, '_experience_location', $entry['location'] );
	update_post_meta( $post_id, '_experience_date_start', $entry['date_start'] );
	update_post_meta( $post_id, '_experience_date_end', $entry['date_end'] );
	update_post_meta( $post_id, '_experience_is_current', $entry['is_current'] );
	update_post_meta( $post_id, '_experience_bullets', $entry['bullets'] );
	WP_CLI::log( "Created experience: {$entry['company']}" );
}

$projects = array(
	array( 'title' => 'Shopify Form App', 'content' => 'A custom Shopify app built with Laravel that collects customer data, automatically captures location via IP geolocation, creates Shopify customers, and subscribes them to email marketing.', 'tags' => 'Laravel, Shopify API, PHP' ),
	array( 'title' => 'Work Log Generator', 'content' => 'An interactive work log generator built with Laravel 12, Tailwind CSS and JavaScript. Generates and saves daily work logs, fetches previous entries, and copies styled templates via AJAX.', 'tags' => 'Laravel 12, Tailwind CSS, AJAX' ),
	array( 'title' => 'FinanceHub', 'content' => 'A secure, feature-rich personal finance management app built with Laravel 11, Livewire 3 and Tailwind CSS — designed for tracking wealth, automating bills and reaching financial goals.', 'tags' => 'Laravel 11, Livewire 3, Tailwind CSS' ),
	array( 'title' => 'Planty — Shopify Chat Widget', 'content' => 'A lightweight Shopify chatbot widget integrating with OpenAI via a secure Vercel serverless proxy. Supports quick replies, reset, auto-scroll and a custom welcome message.', 'tags' => 'OpenAI API, Vercel, Shopify' ),
);

foreach ( $projects as $i => $project ) {
	$post_id = wp_insert_post( array(
		'post_type'    => 'project',
		'post_title'   => $project['title'],
		'post_content' => $project['content'],
		'post_status'  => 'publish',
		'menu_order'   => $i,
	) );
	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed to create project {$project['title']}: " . $post_id->get_error_message() );
		continue;
	}
	update_post_meta( $post_id, '_project_tags', $project['tags'] );
	WP_CLI::log( "Created project: {$project['title']}" );
}

$work_sites = array(
	array( 'title' => 'Danesh Exchange', 'url' => 'https://www.daneshexchange.com/', 'platform' => 'WordPress', 'image' => 'daneshexchange.jpg', 'blocked' => false ),
	array( 'title' => 'Castle Hill Speech Pathology', 'url' => 'https://castlehillspeech.com.au/', 'platform' => 'WordPress', 'image' => 'castlehillspeech.jpg', 'blocked' => false ),
	array( 'title' => 'Petunia Woof', 'url' => 'https://petuniawoof.com/', 'platform' => 'Shopify', 'image' => 'petuniawoof.jpg', 'blocked' => false ),
	array( 'title' => 'JustEmail', 'url' => 'https://justemail.io/', 'platform' => 'Shopify', 'image' => 'justemail.jpg', 'blocked' => false ),
	array( 'title' => 'Keith James', 'url' => 'https://keithjames.com/', 'platform' => 'Shopify', 'image' => null, 'blocked' => true ),
	array( 'title' => 'Icemob', 'url' => 'https://icemob.co/', 'platform' => 'Shopify', 'image' => 'icemob.jpg', 'blocked' => false ),
	array( 'title' => 'Manage My Groceries', 'url' => 'https://managemygroceries.ca/', 'platform' => 'Magento 2', 'image' => null, 'blocked' => true ),
	array( 'title' => 'Hyrem', 'url' => 'https://hyrem.com/', 'platform' => 'Magento 2', 'image' => 'hyrem.jpg', 'blocked' => false ),
	array( 'title' => 'Matihaat', 'url' => 'https://matihaat.com/', 'platform' => 'Shopify', 'image' => 'matihaat.jpg', 'blocked' => false ),
	array( 'title' => 'Amplified Boosts', 'url' => 'https://www.amplifiedboosts.com/', 'platform' => 'Shopify', 'image' => 'amplifiedboosts.jpg', 'blocked' => false ),
);

// Path to the static site's already-captured screenshots. These live in a
// sibling project directory (tajwar-port), NOT inside this theme — the theme
// itself only ships code, not this one-time migration data. Override with
// TAJWAR_SCREENSHOTS_DIR if the screenshots live somewhere else on this machine.
$screenshots_dir = getenv( 'TAJWAR_SCREENSHOTS_DIR' ) ?: 'E:\\laragon\\www\\tajwar-port\\assets\\screenshots';

foreach ( $work_sites as $i => $site ) {
	$post_id = wp_insert_post( array(
		'post_type'   => 'work_site',
		'post_title'  => $site['title'],
		'post_status' => 'publish',
		'menu_order'  => $i,
	) );
	if ( is_wp_error( $post_id ) ) {
		WP_CLI::warning( "Failed to create work_site {$site['title']}: " . $post_id->get_error_message() );
		continue;
	}
	update_post_meta( $post_id, '_work_site_url', $site['url'] );
	update_post_meta( $post_id, '_work_site_platform', $site['platform'] );
	update_post_meta( $post_id, '_work_site_preview_blocked', $site['blocked'] );

	if ( $site['image'] ) {
		$file_path = trailingslashit( $screenshots_dir ) . $site['image'];
		if ( file_exists( $file_path ) ) {
			$attachment_id = media_handle_sideload( array(
				'name'     => $site['image'],
				'tmp_name' => $file_path,
			), $post_id );
			if ( ! is_wp_error( $attachment_id ) ) {
				set_post_thumbnail( $post_id, $attachment_id );
			} else {
				WP_CLI::warning( "Failed to attach screenshot for {$site['title']}: " . $attachment_id->get_error_message() );
			}
		} else {
			WP_CLI::warning( "Screenshot file not found for {$site['title']}: {$file_path}" );
		}
	}
	WP_CLI::log( "Created work_site: {$site['title']}" );
}

WP_CLI::success( 'Content migration complete: 5 experience, 4 project, 10 work_site entries.' );
