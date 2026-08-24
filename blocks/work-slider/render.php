<?php
defined( 'ABSPATH' ) || exit;

/**
 * Render the Work Slider markup: published Work Site CPT entries as the
 * responsive 3/2/1-per-view carousel (index.html:471-790). The carousel JS
 * in assets/js/main.js selects #workSlider / #sliderTrack / #sliderPrev /
 * #sliderNext / #sliderDots by ID, so this render callback must emit those
 * same IDs once per page.
 *
 * @return string
 */
if ( ! function_exists( 'tajwar_render_work_slider' ) ) {
	function tajwar_render_work_slider() {
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

		ob_start();
		?>
		<section class="section section-wide" id="work">
			<p class="eyebrow mono">// work --live</p>
			<h2 class="section-title">Websites I've built</h2>
			<p class="work-intro">Live client sites spanning WordPress, Shopify and Magento&nbsp;2. Click any preview to visit the site.</p>

			<div class="slider" id="workSlider" aria-roledescription="carousel" aria-label="Websites I've built">
				<div class="slider-viewport">
					<div class="slider-track" id="sliderTrack">
						<?php while ( $query->have_posts() ) : $query->the_post();
							$post_id         = get_the_ID();
							$url             = get_post_meta( $post_id, '_work_site_url', true );
							$platform        = get_post_meta( $post_id, '_work_site_platform', true );
							$preview_blocked = (bool) get_post_meta( $post_id, '_work_site_preview_blocked', true );
							$name            = get_the_title();
							$initials        = tajwar_initials( $name );
							?>
							<div class="slide">
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
				</div>

				<button class="slider-arrow slider-prev" id="sliderPrev" aria-label="Previous website">‹</button>
				<button class="slider-arrow slider-next" id="sliderNext" aria-label="Next website">›</button>

				<div class="slider-dots" id="sliderDots"></div>
			</div>
		</section>
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
echo tajwar_render_work_slider();
