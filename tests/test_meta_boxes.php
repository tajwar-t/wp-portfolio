<?php
class Test_Meta_Boxes extends WP_UnitTestCase {

	private function make_experience_post() {
		tajwar_register_post_types();
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		return self::factory()->post->create( array( 'post_type' => 'experience' ) );
	}

	public function test_save_experience_meta_persists_all_fields() {
		$post_id = $this->make_experience_post();

		$_POST['tajwar_experience_nonce']      = wp_create_nonce( 'tajwar_save_experience' );
		$_POST['tajwar_experience_role']       = 'Lead Engineer';
		$_POST['tajwar_experience_company']    = 'Acme Co';
		$_POST['tajwar_experience_location']   = 'Remote';
		$_POST['tajwar_experience_date_start'] = 'Jun 2025';
		$_POST['tajwar_experience_date_end']   = 'Present';
		$_POST['tajwar_experience_is_current'] = '1';
		$_POST['tajwar_experience_bullets']    = "Shipped 50+ sites.\nCut load times by 30%.";

		tajwar_save_experience_meta( $post_id );

		$this->assertSame( 'Lead Engineer', get_post_meta( $post_id, '_experience_role', true ) );
		$this->assertSame( 'Acme Co', get_post_meta( $post_id, '_experience_company', true ) );
		$this->assertSame( 'Remote', get_post_meta( $post_id, '_experience_location', true ) );
		$this->assertSame( 'Jun 2025', get_post_meta( $post_id, '_experience_date_start', true ) );
		$this->assertSame( 'Present', get_post_meta( $post_id, '_experience_date_end', true ) );
		$this->assertTrue( (bool) get_post_meta( $post_id, '_experience_is_current', true ) );
		$this->assertSame(
			"Shipped 50+ sites.\nCut load times by 30%.",
			get_post_meta( $post_id, '_experience_bullets', true )
		);

		unset(
			$_POST['tajwar_experience_nonce'],
			$_POST['tajwar_experience_role'],
			$_POST['tajwar_experience_company'],
			$_POST['tajwar_experience_location'],
			$_POST['tajwar_experience_date_start'],
			$_POST['tajwar_experience_date_end'],
			$_POST['tajwar_experience_is_current'],
			$_POST['tajwar_experience_bullets']
		);
	}

	public function test_save_experience_meta_requires_valid_nonce() {
		$post_id = $this->make_experience_post();

		$_POST['tajwar_experience_nonce'] = 'invalid-nonce';
		$_POST['tajwar_experience_role']  = 'Should Not Save';

		tajwar_save_experience_meta( $post_id );

		$this->assertSame( '', get_post_meta( $post_id, '_experience_role', true ) );

		unset( $_POST['tajwar_experience_nonce'], $_POST['tajwar_experience_role'] );
	}

	public function test_save_experience_meta_unchecking_is_current_stores_false() {
		$post_id = $this->make_experience_post();
		update_post_meta( $post_id, '_experience_is_current', true );

		$_POST['tajwar_experience_nonce'] = wp_create_nonce( 'tajwar_save_experience' );
		// tajwar_experience_is_current intentionally absent, simulating an unchecked checkbox.

		tajwar_save_experience_meta( $post_id );

		$this->assertFalse( (bool) get_post_meta( $post_id, '_experience_is_current', true ) );

		unset( $_POST['tajwar_experience_nonce'] );
	}
}
