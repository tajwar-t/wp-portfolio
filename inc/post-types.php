<?php
/**
 * Custom post type and post meta registrations.
 *
 * Populated by later tasks in the FSE theme conversion plan.
 */

defined( 'ABSPATH' ) || exit;

function tajwar_register_post_types() {
	register_post_type( 'experience', array(
		'label'        => 'Experience',
		'labels'       => array(
			'name'          => 'Experience',
			'singular_name' => 'Experience Entry',
			'add_new_item'  => 'Add Experience Entry',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-businessman',
		'supports'     => array( 'title' ),
		'show_in_rest' => false,
	) );

	register_post_meta( 'experience', '_experience_company', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
	) );
	register_post_meta( 'experience', '_experience_role', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
	) );
	register_post_meta( 'experience', '_experience_location', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
	) );
	register_post_meta( 'experience', '_experience_date_start', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
	) );
	register_post_meta( 'experience', '_experience_date_end', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
	) );
	register_post_meta( 'experience', '_experience_is_current', array(
		'type'              => 'boolean',
		'single'            => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	) );
	register_post_meta( 'experience', '_experience_bullets', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
}
add_action( 'init', 'tajwar_register_post_types' );

/**
 * Split a newline-separated bullets textarea into a clean array.
 *
 * @param string $raw Raw textarea value, one bullet per line.
 * @return string[] Non-empty, trimmed bullet lines.
 */
function tajwar_sanitize_experience_bullets( $raw ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $raw );
	$lines = array_map( 'trim', $lines );
	$lines = array_map( 'wp_strip_all_tags', $lines );
	return array_values( array_filter( $lines, fn( $line ) => $line !== '' ) );
}
