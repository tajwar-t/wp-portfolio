<?php
class Test_Meta_Sanitization2 extends WP_UnitTestCase {

	public function test_sanitize_experience_bullets_splits_and_trims_lines() {
		$raw = "  Shipped 50+ sites.  \n\nCut load times by 30%.\n<script>alert(1)</script>Reviewed code.\n";
		$result = tajwar_sanitize_experience_bullets( $raw );

		$this->assertSame(
			array(
				'Shipped 50+ sites.',
				'Cut load times by 30%.',
				'Reviewed code.',
			),
			$result
		);
	}

	public function test_sanitize_experience_bullets_handles_empty_string() {
		$this->assertSame( array(), tajwar_sanitize_experience_bullets( '' ) );
	}
}
