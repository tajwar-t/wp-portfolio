<?php
defined( 'ABSPATH' ) || exit;

/**
 * Render the Testimonials slider markup: published Testimonial CPT entries
 * as a responsive 4/3/2-per-view carousel. Reuses the same slider markup
 * conventions (.slider / .slider-viewport / .slider-track / .slider-arrow /
 * .slider-dots) as blocks/work-slider, driven by the generic multi-slider
 * init in assets/js/main.js -- only the --per-view breakpoints differ, via
 * the .slider-testimonials modifier class in style.css.
 *
 * The section wrapper, eyebrow, and heading are NOT part of this block --
 * they live as regular editable core/paragraph and core/heading blocks in
 * templates/front-page.html, alongside this block. This function renders
 * only the CPT-driven <div class="slider slider-testimonials"> itself.
 *
 * @return string
 */
if ( ! function_exists( 'tajwar_render_testimonial_slider' ) ) {
	function tajwar_render_testimonial_slider() {
		$query = new WP_Query( array(
			'post_type'      => 'testimonial',
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
		<div class="slider slider-testimonials" id="testimonialSlider" aria-roledescription="carousel" aria-label="Client testimonials">
			<div class="slider-viewport">
				<div class="slider-track" id="testimonialTrack">
					<?php while ( $query->have_posts() ) : $query->the_post();
						$post_id  = get_the_ID();
						$name     = get_the_title();
						$country  = get_post_meta( $post_id, '_testimonial_country', true );
						$service  = get_post_meta( $post_id, '_testimonial_service', true );
						$rating   = (int) get_post_meta( $post_id, '_testimonial_rating', true );
						$rating   = ( $rating >= 1 && $rating <= 5 ) ? $rating : 5;
						$review   = wp_strip_all_tags( get_the_content() );
						$initials = tajwar_testimonial_initials( $name );
						?>
						<div class="slide">
							<article class="testimonial-card">
								<div class="testimonial-head">
									<span class="testimonial-avatar mono" aria-hidden="true"><?php echo esc_html( $initials ); ?></span>
									<div class="testimonial-who">
										<span class="testimonial-name"><?php echo esc_html( $name ); ?></span>
										<?php if ( $country ) : ?>
											<span class="testimonial-country mono"><?php echo esc_html( $country ); ?></span>
										<?php endif; ?>
									</div>
									<?php if ( $service ) : ?>
										<span class="testimonial-service mono"><?php echo esc_html( $service ); ?></span>
									<?php endif; ?>
								</div>
								<div class="testimonial-rating" aria-label="<?php echo esc_attr( sprintf( '%d out of 5 stars', $rating ) ); ?>">
									<?php echo esc_html( str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating ) ); ?>
								</div>
								<p class="testimonial-quote">&ldquo;<?php echo esc_html( $review ); ?>&rdquo;</p>
							</article>
						</div>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>

			<button class="slider-arrow slider-prev" id="testimonialPrev" aria-label="Previous testimonial">‹</button>
			<button class="slider-arrow slider-next" id="testimonialNext" aria-label="Next testimonial">›</button>

			<div class="slider-dots" id="testimonialDots"></div>
		</div>
		<?php
		return ob_get_clean();
	}
}

/**
 * Derive a 1-2 letter initials mark for a testimonial's avatar badge, e.g.
 * "westermanjezz" -> "W". Kept independent of tajwar_initials() in
 * blocks/work-slider/render.php so this block has no load-order
 * dependency on that one.
 *
 * @param string $name Client display name.
 * @return string
 */
if ( ! function_exists( 'tajwar_testimonial_initials' ) ) {
	function tajwar_testimonial_initials( $name ) {
		$words    = preg_split( '/\s+/', trim( (string) $name ) );
		$initials = array_map( fn( $word ) => mb_strtoupper( mb_substr( $word, 0, 1 ) ), $words );
		return implode( '', array_slice( $initials, 0, 2 ) );
	}
}

// block.json's "render" field requires this file as a template and captures
// whatever it echoes (see WP_Block_Type::render() / wp-includes/blocks.php,
// register_block_type_from_metadata()) — it does not call the function above
// automatically, so we must invoke it here for the block to actually render.
echo tajwar_render_testimonial_slider();
