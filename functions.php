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
	wp_enqueue_style(
		'tajwar-style',
		TAJWAR_THEME_URI . '/assets/css/style.css',
		array(),
		TAJWAR_THEME_VERSION
	);
	wp_enqueue_script(
		'tajwar-main',
		TAJWAR_THEME_URI . '/assets/js/main.js',
		array(),
		TAJWAR_THEME_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tajwar_enqueue_assets' );

require_once TAJWAR_THEME_DIR . '/inc/post-types.php';
require_once TAJWAR_THEME_DIR . '/inc/meta-boxes.php';
require_once TAJWAR_THEME_DIR . '/inc/blocks.php';
