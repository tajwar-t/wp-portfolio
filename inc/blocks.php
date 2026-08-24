<?php
/**
 * Custom block registrations.
 *
 * Populated by later tasks in the FSE theme conversion plan.
 */

defined( 'ABSPATH' ) || exit;

function tajwar_register_blocks() {
	register_block_type( TAJWAR_THEME_DIR . '/blocks/experience-timeline' );
	register_block_type( TAJWAR_THEME_DIR . '/blocks/projects-grid' );
	register_block_type( TAJWAR_THEME_DIR . '/blocks/work-slider' );
	register_block_type( TAJWAR_THEME_DIR . '/blocks/testimonial-slider' );
	register_block_type( TAJWAR_THEME_DIR . '/blocks/stats-counter' );
	register_block_type( TAJWAR_THEME_DIR . '/blocks/skill-group' );
	register_block_type( TAJWAR_THEME_DIR . '/blocks/skill-pill' );
}
add_action( 'init', 'tajwar_register_blocks' );
