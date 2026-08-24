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

	register_post_type( 'project', array(
		'label'        => 'Projects',
		'labels'       => array(
			'name'          => 'Projects',
			'singular_name' => 'Project',
			'add_new_item'  => 'Add Project',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-hammer',
		'supports'     => array( 'title', 'editor' ),
		'show_in_rest' => false,
	) );

	register_post_meta( 'project', '_project_tags', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
	) );

	register_post_type( 'work_site', array(
		'label'        => 'Work Sites',
		'labels'       => array(
			'name'          => 'Work Sites',
			'singular_name' => 'Work Site',
			'add_new_item'  => 'Add Work Site',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-admin-site-alt3',
		'supports'     => array( 'title', 'thumbnail' ),
		'show_in_rest' => false,
	) );

	register_post_meta( 'work_site', '_work_site_url', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'esc_url_raw',
	) );
	register_post_meta( 'work_site', '_work_site_platform', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
	) );
	register_post_meta( 'work_site', '_work_site_preview_blocked', array(
		'type'              => 'boolean',
		'single'            => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
	) );

	register_post_type( 'testimonial', array(
		'label'        => 'Testimonials',
		'labels'       => array(
			'name'          => 'Testimonials',
			'singular_name' => 'Testimonial',
			'add_new_item'  => 'Add Testimonial',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-star-filled',
		'supports'     => array( 'title', 'editor' ),
		'show_in_rest' => false,
	) );

	register_post_meta( 'testimonial', '_testimonial_country', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
	) );
	register_post_meta( 'testimonial', '_testimonial_service', array(
		'type'              => 'string',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
	) );
	register_post_meta( 'testimonial', '_testimonial_rating', array(
		'type'              => 'integer',
		'single'            => true,
		'default'           => 5,
		'sanitize_callback' => 'tajwar_sanitize_testimonial_rating',
	) );
}
add_action( 'init', 'tajwar_register_post_types' );

/**
 * Clamp a testimonial's star rating to the 1-5 range, defaulting to 5
 * for anything missing or out of range.
 *
 * @param mixed $raw Raw rating value.
 * @return int
 */
function tajwar_sanitize_testimonial_rating( $raw ) {
	$rating = absint( $raw );
	return ( $rating >= 1 && $rating <= 5 ) ? $rating : 5;
}

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
