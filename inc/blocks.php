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
}
add_action( 'init', 'tajwar_register_blocks' );
