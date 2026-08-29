<?php
/**
 * Tajwar Portfolio theme bootstrap.
 */

defined( 'ABSPATH' ) || exit;

define( 'TAJWAR_THEME_VERSION', '1.0.0' );
define( 'TAJWAR_THEME_DIR', get_template_directory() );
define( 'TAJWAR_THEME_URI', get_template_directory_uri() );

function tajwar_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'style', 'script', 'search-form', 'gallery', 'caption' ) );
	add_theme_support( 'post-thumbnails', array( 'work_site' ) );
	add_editor_style( 'assets/css/style.css' );
}
add_action( 'after_setup_theme', 'tajwar_setup' );

function tajwar_enqueue_assets() {
	// Swiper is vendored locally under assets/lib/swiper (no CDN) --
	// see assets/lib/swiper/LICENSE. Pin its exact bundled version here
	// since the files carry no query-string versioning of their own.
	wp_enqueue_style(
		'swiper',
		TAJWAR_THEME_URI . '/assets/lib/swiper/swiper-bundle.min.css',
		array(),
		'14.2.0'
	);
	wp_enqueue_script(
		'swiper',
		TAJWAR_THEME_URI . '/assets/lib/swiper/swiper-bundle.min.js',
		array(),
		'14.2.0',
		true
	);

	// filemtime()-based versioning so the enqueue query string changes on
	// every edit -- TAJWAR_THEME_VERSION is a fixed '1.0.0' and never busts
	// the browser cache on its own, which has repeatedly served stale CSS/JS
	// during development.
	wp_enqueue_style(
		'tajwar-style',
		TAJWAR_THEME_URI . '/assets/css/style.css',
		array( 'swiper' ),
		filemtime( TAJWAR_THEME_DIR . '/assets/css/style.css' )
	);
	wp_enqueue_script(
		'tajwar-main',
		TAJWAR_THEME_URI . '/assets/js/main.js',
		array( 'swiper' ),
		filemtime( TAJWAR_THEME_DIR . '/assets/js/main.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tajwar_enqueue_assets' );

require_once TAJWAR_THEME_DIR . '/inc/post-types.php';
require_once TAJWAR_THEME_DIR . '/inc/meta-boxes.php';
require_once TAJWAR_THEME_DIR . '/inc/blocks.php';
