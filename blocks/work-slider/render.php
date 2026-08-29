<?php
defined( 'ABSPATH' ) || exit;

/**
 * Render the Work Slider markup: published Work Site CPT entries as a
 * Swiper carousel (https://swiperjs.com, vendored locally under
 * assets/lib/swiper -- see that directory's LICENSE). The generic
 * Swiper init in assets/js/main.js scans every ".swiper" element on the
 * page and reads its data-per-view-* attributes, so this render callback
 * only needs to emit standard Swiper markup (.swiper > .swiper-wrapper >
 * .swiper-slide) plus those data attributes -- no bespoke carousel JS.
 *
 * The section wrapper, eyebrow, heading, and intro paragraph are NOT part
 * of this block -- they live as regular editable core/paragraph and
 * core/heading blocks in templates/front-page.html, alongside this block.
 * This function renders only the CPT-driven <div class="swiper"> itself.
 *
 * $attributes accepts perViewDesktop/perViewTablet/perViewMobile (set via
 * the block's Inspector Controls) and is passed straight through as
 * data-per-view-desktop/-tablet/-mobile attributes, which main.js reads
 * to configure Swiper's responsive `breakpoints` option.
 *
 * @param array $attributes Block attributes, or empty array outside a real block render.
 * @return string
 */
if ( ! function_exists( 'tajwar_render_work_slider' ) ) {
	function tajwar_render_work_slider( $attributes = array() ) {
		$query = new WP_Query( array(
			'post_type'      => 'work_site',
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
		) );

		if ( ! $query->have_posts() ) {
			return '';
		}

		$per_view_desktop = isset( $attributes['perViewDesktop'] ) ? max( 1, (int) $attributes['perViewDesktop'] ) : 3;
		$per_view_tablet   = isset( $attributes['perViewTablet'] ) ? max( 1, (int) $attributes['perViewTablet'] ) : 2;
		$per_view_mobile   = isset( $attributes['perViewMobile'] ) ? max( 1, (int) $attributes['perViewMobile'] ) : 1;

		ob_start();
		?>
		<div
			class="swiper work-slider"
			id="workSlider"
			data-per-view-desktop="<?php echo esc_attr( $per_view_desktop ); ?>"
			data-per-view-tablet="<?php echo esc_attr( $per_view_tablet ); ?>"
			data-per-view-mobile="<?php echo esc_attr( $per_view_mobile ); ?>"
			aria-roledescription="carousel"
			aria-label="Websites I've built"
		>
			<div class="swiper-wrapper">
				<?php while ( $query->have_posts() ) : $query->the_post();
					$post_id         = get_the_ID();
					$url             = get_post_meta( $post_id, '_work_site_url', true );
					$platform        = get_post_meta( $post_id, '_work_site_platform', true );
					$preview_blocked = (bool) get_post_meta( $post_id, '_work_site_preview_blocked', true );
					$name            = get_the_title();
					$initials        = tajwar_initials( $name );
					?>
					<div class="swiper-slide">
						<div class="slide-bar">
							<span class="slide-name"><?php echo esc_html( $name ); ?></span>
							<span class="slide-badge mono"><?php echo esc_html( $platform ); ?></span>
							<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" class="slide-visit">Visit site ↗</a>
						</div>
						<div class="slide-frame">
							<?php if ( $preview_blocked || ! has_post_thumbnail( $post_id ) ) : ?>
								<div class="slide-fallback">
									<span class="slide-fallback-mark mono"><?php echo esc_html( $initials ); ?></span>
									<span class="slide-fallback-text">Live preview unavailable<br>(site blocks automated screenshots)</span>
								</div>
							<?php else : ?>
								<?php echo get_the_post_thumbnail( $post_id, 'large', array(
									'alt'     => sprintf( 'Screenshot of %s homepage', $name ),
									'loading' => 'lazy',
								) ); ?>
							<?php endif; ?>
							<a class="slide-overlay" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( 'Open %s in a new tab', $name ) ); ?>"></a>
						</div>
					</div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>

			<button class="swiper-button-prev" aria-label="Previous website"></button>
			<button class="swiper-button-next" aria-label="Next website"></button>

			<div class="swiper-pagination"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}

/**
 * Derive a 2-3 letter initials mark for the fallback tile, e.g.
 * "Manage My Groceries" -> "MMG", "Keith James" -> "KJ".
 *
 * @param string $name Site display name.
 * @return string
 */
if ( ! function_exists( 'tajwar_initials' ) ) {
	function tajwar_initials( $name ) {
		$words    = preg_split( '/\s+/', trim( (string) $name ) );
		$initials = array_map( fn( $word ) => mb_strtoupper( mb_substr( $word, 0, 1 ) ), $words );
		return implode( '', array_slice( $initials, 0, 3 ) );
	}
}

// block.json's "render" field requires this file as a template and captures
// whatever it echoes (see WP_Block_Type::render() / wp-includes/blocks.php,
// register_block_type_from_metadata()) — it does not call the function above
// automatically, so we must invoke it here for the block to actually render.
echo tajwar_render_work_slider( $attributes ?? array() );
