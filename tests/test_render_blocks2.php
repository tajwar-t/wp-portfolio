<?php
class Test_Render_Blocks2 extends WP_UnitTestCase {

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

	public function test_render_php_survives_being_required_again_without_fatal() {
		$post_id = self::factory()->post->create( array(
			'post_type'   => 'experience',
			'post_title'  => 'Reload Co role',
			'post_status' => 'publish',
		) );
		update_post_meta( $post_id, '_experience_role', 'Engineer' );
		update_post_meta( $post_id, '_experience_company', 'Reload Co' );
		update_post_meta( $post_id, '_experience_date_start', 'Jan 2020' );
		update_post_meta( $post_id, '_experience_date_end', 'Feb 2020' );

		// tests/bootstrap.php already loaded this file once via require_once.
		// WP core's own render_callback for block.json's "render" field does a
		// *plain* `require` (not require_once) every time the block renders
		// (see register_block_type_from_metadata() in wp-includes/blocks.php),
		// so a block appearing twice on one page -- or any second render pass
		// in the same process -- re-requires this file. Without the
		// function_exists() guard around the function declaration, this second
		// require fatals with "Cannot redeclare tajwar_render_experience_timeline()".
		$render_file = dirname( __DIR__ ) . '/blocks/experience-timeline/render.php';

		ob_start();
		require $render_file;
		$direct_require_output = ob_get_clean();

		$this->assertStringContainsString( 'Reload Co', $direct_require_output );

		// Also drive it through the real WP block-rendering pipeline in the
		// same process as the plain require above and a direct function call
		// below -- three separate execution paths that each re-trigger the
		// function declaration, none of which should fatal.
		$blocks   = parse_blocks( '<!-- wp:tajwar/experience-timeline /-->' );
		$rendered = render_block( $blocks[0] );

		$this->assertStringContainsString( 'Reload Co', $rendered );

		$direct_call_output = tajwar_render_experience_timeline();
		$this->assertStringContainsString( 'Reload Co', $direct_call_output );
	}

	public function test_projects_grid_renders_published_projects_with_tags_and_static_plugin_card() {
		$project_id = self::factory()->post->create( array(
			'post_type'    => 'project',
			'post_title'   => 'Shopify Form App',
			'post_content' => 'Collects customer data and subscribes them to email marketing.',
			'post_status'  => 'publish',
		) );
		update_post_meta( $project_id, '_project_tags', 'Laravel, Shopify API, PHP' );

		$html = tajwar_render_projects_grid();

		$this->assertStringContainsString( '<div class="project-grid">', $html );
		$this->assertStringContainsString( 'Shopify Form App', $html );
		$this->assertStringContainsString( 'Collects customer data', $html );
		$this->assertStringContainsString( '<li>Laravel</li>', $html );
		$this->assertStringContainsString( '<li>Shopify API</li>', $html );
		// The static WordPress Plugins card ships in every render regardless of CPT content.
		$this->assertStringContainsString( 'wp-sleek-admin', $html );
	}

	public function test_work_slider_renders_image_slide_and_fallback_slide() {
		$normal_id = self::factory()->post->create( array(
			'post_type'   => 'work_site',
			'post_title'  => 'Danesh Exchange',
			'post_status' => 'publish',
		) );
		update_post_meta( $normal_id, '_work_site_url', 'https://www.daneshexchange.com/' );
		update_post_meta( $normal_id, '_work_site_platform', 'WordPress' );
		$attachment_id = self::factory()->attachment->create_upload_object(
			DIR_TESTDATA . '/images/canola.jpg',
			$normal_id
		);
		set_post_thumbnail( $normal_id, $attachment_id );

		$blocked_id = self::factory()->post->create( array(
			'post_type'   => 'work_site',
			'post_title'  => 'Keith James',
			'post_status' => 'publish',
		) );
		update_post_meta( $blocked_id, '_work_site_url', 'https://keithjames.com/' );
		update_post_meta( $blocked_id, '_work_site_platform', 'Shopify' );
		update_post_meta( $blocked_id, '_work_site_preview_blocked', true );

		$html = tajwar_render_work_slider();

		$this->assertStringContainsString( 'id="workSlider"', $html );
		$this->assertStringContainsString( 'id="sliderTrack"', $html );
		$this->assertStringContainsString( 'Danesh Exchange', $html );
		$this->assertStringContainsString( '<img', $html );
		$this->assertStringContainsString( 'Keith James', $html );
		$this->assertStringContainsString( 'slide-fallback', $html );
		$this->assertStringContainsString( 'Live preview unavailable', $html );
	}

	public function test_stats_counter_renders_all_four_stats_starting_at_zero() {
		$html = tajwar_render_stats_counter();

		$this->assertStringContainsString( '<div class="hero-stats">', $html );
		$this->assertStringContainsString( 'data-count-to="7"', $html );
		$this->assertStringContainsString( 'data-count-to="150"', $html );
		$this->assertStringContainsString( 'data-count-to="190"', $html );
		$this->assertStringContainsString( 'data-count-to="30"', $html );
		$this->assertStringContainsString( 'data-suffix="+"', $html );
		// Every counter starts at 0 in the markup -- view.js animates it upward.
		$this->assertSame( 4, substr_count( $html, '>0+</span>' ) );
		$this->assertStringContainsString( 'Years Experience', $html );
		$this->assertStringContainsString( '5★ Fiverr Reviews', $html );
	}

	public function test_stats_counter_render_php_survives_being_required_again_without_fatal() {
		$render_file = dirname( __DIR__ ) . '/blocks/stats-counter/render.php';

		ob_start();
		require $render_file;
		$direct_require_output = ob_get_clean();
		$this->assertStringContainsString( 'hero-stats', $direct_require_output );

		$blocks   = parse_blocks( '<!-- wp:tajwar/stats-counter /-->' );
		$rendered = render_block( $blocks[0] );
		$this->assertStringContainsString( 'hero-stats', $rendered );

		$direct_call_output = tajwar_render_stats_counter();
		$this->assertStringContainsString( 'hero-stats', $direct_call_output );
	}
}
