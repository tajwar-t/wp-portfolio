<?php
class Test_Render_Blocks extends WP_UnitTestCase {

	public function test_experience_timeline_renders_published_entries_in_menu_order() {
		$older = self::factory()->post->create( array(
			'post_type'   => 'experience',
			'post_title'  => 'AutoMaximizer role',
			'post_status' => 'publish',
			'menu_order'  => 1,
		) );
		update_post_meta( $older, '_experience_role', 'Web Developer' );
		update_post_meta( $older, '_experience_company', 'AutoMaximizer' );
		update_post_meta( $older, '_experience_location', 'Remote' );
		update_post_meta( $older, '_experience_date_start', 'Apr 2019' );
		update_post_meta( $older, '_experience_date_end', 'Jun 2021' );
		update_post_meta( $older, '_experience_bullets', "Built UI/UX-led automotive web experiences." );

		$newer = self::factory()->post->create( array(
			'post_type'   => 'experience',
			'post_title'  => 'bitBirds role',
			'post_status' => 'publish',
			'menu_order'  => 0,
		) );
		update_post_meta( $newer, '_experience_role', 'Web Developer' );
		update_post_meta( $newer, '_experience_company', 'bitBirds Solutions' );
		update_post_meta( $newer, '_experience_location', 'Dhaka, Bangladesh' );
		update_post_meta( $newer, '_experience_date_start', 'Jun 2025' );
		update_post_meta( $newer, '_experience_is_current', true );
		update_post_meta( $newer, '_experience_bullets', "Engineered 50+ sites.\nCreated 30+ plugins." );

		$html = tajwar_render_experience_timeline();

		$this->assertStringContainsString( '<ol class="timeline">', $html );
		$this->assertStringContainsString( 'bitBirds Solutions', $html );
		$this->assertStringContainsString( 'Jun 2025 — Present', $html );
		$this->assertStringContainsString( '<li>Engineered 50+ sites.</li>', $html );
		$this->assertStringContainsString( '<li>Created 30+ plugins.</li>', $html );

		// menu_order 0 (bitBirds, newer) must render before menu_order 1 (AutoMaximizer, older).
		$this->assertLessThan(
			strpos( $html, 'AutoMaximizer' ),
			strpos( $html, 'bitBirds Solutions' )
		);
	}

	public function test_experience_timeline_skips_draft_entries() {
		$draft = self::factory()->post->create( array(
			'post_type'   => 'experience',
			'post_title'  => 'Unpublished role',
			'post_status' => 'draft',
		) );
		update_post_meta( $draft, '_experience_company', 'Should Not Appear Inc' );

		$html = tajwar_render_experience_timeline();

		$this->assertStringNotContainsString( 'Should Not Appear Inc', $html );
	}
}
