<?php
$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}
require_once $_tests_dir . '/includes/functions.php';

function _tajwar_manually_load_theme() {
	switch_theme( 'tajwar-portfolio' );
	require dirname( __DIR__ ) . '/inc/post-types.php';
	require dirname( __DIR__ ) . '/inc/meta-boxes.php';
}
tests_add_filter( 'muplugins_loaded', '_tajwar_manually_load_theme' );

require $_tests_dir . '/includes/bootstrap.php';
