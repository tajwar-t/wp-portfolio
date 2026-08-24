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

	private function make_project_post() {
		tajwar_register_post_types();
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		return self::factory()->post->create( array( 'post_type' => 'project' ) );
	}

	/**
	 * Real round-trip test: renders the actual meta box markup, extracts the
	 * <input name="..."> attributes from it via DOM parsing, POSTs under those
	 * exact field names, and asserts the save handler persisted the values.
	 * This guards against the render/save field-name mismatch found in Task 7.
	 */
	public function test_save_project_meta_persists_all_fields_matching_rendered_field_names() {
		$post_id = $this->make_project_post();
		$post    = get_post( $post_id );

		ob_start();
		tajwar_render_project_meta_box( $post );
		$html = ob_get_clean();

		preg_match_all( '/<input[^>]*\bname="([^"]+)"/', $html, $matches );
		$rendered_field_names = $matches[1];

		$this->assertContains( 'tajwar_project_tags', $rendered_field_names );
		$this->assertContains( 'tajwar_project_url', $rendered_field_names );

		$_POST['tajwar_project_nonce'] = wp_create_nonce( 'tajwar_save_project' );
		$_POST['tajwar_project_tags']  = 'Laravel, Shopify API, PHP';
		$_POST['tajwar_project_url']   = 'https://example.com/project';

		tajwar_save_project_meta( $post_id );

		$this->assertSame( 'Laravel, Shopify API, PHP', get_post_meta( $post_id, '_project_tags', true ) );
		$this->assertSame( 'https://example.com/project', get_post_meta( $post_id, '_project_url', true ) );

		unset( $_POST['tajwar_project_nonce'], $_POST['tajwar_project_tags'], $_POST['tajwar_project_url'] );
	}

	public function test_save_project_meta_requires_valid_nonce() {
		$post_id = $this->make_project_post();

		$_POST['tajwar_project_nonce'] = 'invalid-nonce';
		$_POST['tajwar_project_tags']  = 'Should Not Save';

		tajwar_save_project_meta( $post_id );

		$this->assertSame( '', get_post_meta( $post_id, '_project_tags', true ) );

		unset( $_POST['tajwar_project_nonce'], $_POST['tajwar_project_tags'] );
	}

	public function test_save_project_meta_sanitizes_url() {
		$post_id = $this->make_project_post();

		$_POST['tajwar_project_nonce'] = wp_create_nonce( 'tajwar_save_project' );
		$_POST['tajwar_project_url']   = 'javascript:alert(1)';

		tajwar_save_project_meta( $post_id );

		$this->assertSame( '', get_post_meta( $post_id, '_project_url', true ) );

		unset( $_POST['tajwar_project_nonce'], $_POST['tajwar_project_url'] );
	}

	private function make_work_site_post() {
		tajwar_register_post_types();
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
		return self::factory()->post->create( array( 'post_type' => 'work_site' ) );
	}

	/**
	 * Real round-trip test: renders the actual meta box markup, extracts the
	 * <input name="..."> attributes from it via DOM parsing, POSTs under those
	 * exact field names, and asserts the save handler persisted the values.
	 * This guards against the render/save field-name mismatch found in Task 7.
	 */
	public function test_save_work_site_meta_persists_all_fields_matching_rendered_field_names() {
		$post_id = $this->make_work_site_post();
		$post    = get_post( $post_id );

		ob_start();
		tajwar_render_work_site_meta_box( $post );
		$html = ob_get_clean();

		preg_match_all( '/<input[^>]*\bname="([^"]+)"/', $html, $input_matches );
		$rendered_field_names = $input_matches[1];
		preg_match_all( '/<select[^>]*\bname="([^"]+)"/', $html, $select_matches );
		$rendered_field_names = array_merge( $rendered_field_names, $select_matches[1] );

		$this->assertContains( 'tajwar_work_site_url', $rendered_field_names );
		$this->assertContains( 'tajwar_work_site_platform', $rendered_field_names );
		$this->assertContains( 'tajwar_work_site_preview_blocked', $rendered_field_names );

		$_POST['tajwar_work_site_nonce']           = wp_create_nonce( 'tajwar_save_work_site' );
		$_POST['tajwar_work_site_url']             = 'https://example.com/work-site';
		$_POST['tajwar_work_site_platform']        = 'Shopify';
		$_POST['tajwar_work_site_preview_blocked'] = '1';

		tajwar_save_work_site_meta( $post_id );

		$this->assertSame( 'https://example.com/work-site', get_post_meta( $post_id, '_work_site_url', true ) );
		$this->assertSame( 'Shopify', get_post_meta( $post_id, '_work_site_platform', true ) );
		$this->assertTrue( (bool) get_post_meta( $post_id, '_work_site_preview_blocked', true ) );

		unset(
			$_POST['tajwar_work_site_nonce'],
			$_POST['tajwar_work_site_url'],
			$_POST['tajwar_work_site_platform'],
			$_POST['tajwar_work_site_preview_blocked']
		);
	}

	public function test_save_work_site_meta_requires_valid_nonce() {
		$post_id = $this->make_work_site_post();

		$_POST['tajwar_work_site_nonce'] = 'invalid-nonce';
		$_POST['tajwar_work_site_url']   = 'https://example.com/should-not-save';

		tajwar_save_work_site_meta( $post_id );

		$this->assertSame( '', get_post_meta( $post_id, '_work_site_url', true ) );

		unset( $_POST['tajwar_work_site_nonce'], $_POST['tajwar_work_site_url'] );
	}

	public function test_save_work_site_meta_sanitizes_url() {
		$post_id = $this->make_work_site_post();

		$_POST['tajwar_work_site_nonce'] = wp_create_nonce( 'tajwar_save_work_site' );
		$_POST['tajwar_work_site_url']   = 'javascript:alert(1)';

		tajwar_save_work_site_meta( $post_id );

		$this->assertSame( '', get_post_meta( $post_id, '_work_site_url', true ) );

		unset( $_POST['tajwar_work_site_nonce'], $_POST['tajwar_work_site_url'] );
	}

	public function test_save_work_site_meta_unchecking_preview_blocked_stores_false() {
		$post_id = $this->make_work_site_post();
		update_post_meta( $post_id, '_work_site_preview_blocked', true );

		$_POST['tajwar_work_site_nonce'] = wp_create_nonce( 'tajwar_save_work_site' );
		// tajwar_work_site_preview_blocked intentionally absent, simulating an unchecked checkbox.

		tajwar_save_work_site_meta( $post_id );

		$this->assertFalse( (bool) get_post_meta( $post_id, '_work_site_preview_blocked', true ) );

		unset( $_POST['tajwar_work_site_nonce'] );
	}
}
