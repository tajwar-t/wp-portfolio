<?php
class Test_Post_Types extends WP_UnitTestCase {

	public function test_experience_post_type_is_registered() {
		tajwar_register_post_types();
		$this->assertTrue( post_type_exists( 'experience' ) );
	}

	public function test_experience_post_type_supports_title_and_editor() {
		tajwar_register_post_types();
		$pt = get_post_type_object( 'experience' );
		$this->assertTrue( post_type_supports( 'experience', 'title' ) );
		$this->assertFalse( $pt->public ); // admin-managed only, no public single pages
		$this->assertTrue( $pt->show_ui );
	}
}
