<?php
$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}
require_once $_tests_dir . '/includes/functions.php';

function _tajwar_manually_load_theme() {
	switch_theme( 'tajwar-portfolio' );
	require_once dirname( __DIR__ ) . '/inc/post-types.php';
	require_once dirname( __DIR__ ) . '/inc/meta-boxes.php';
	require_once dirname( __DIR__ ) . '/inc/blocks.php';

	// block.json's "render" field lazy-loads render.php only when the block
	// is actually rendered (e.g. via render_block()), not at registration
	// time. Tests call tajwar_render_experience_timeline() directly, so
	// require it explicitly here.
	require_once dirname( __DIR__ ) . '/blocks/experience-timeline/render.php';
	require_once dirname( __DIR__ ) . '/blocks/projects-grid/render.php';
}
tests_add_filter( 'muplugins_loaded', '_tajwar_manually_load_theme' );

require $_tests_dir . '/includes/bootstrap.php';
