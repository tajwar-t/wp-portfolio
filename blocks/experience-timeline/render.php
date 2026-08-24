<?php
defined( 'ABSPATH' ) || exit;

/**
 * Render the Experience timeline markup, identical to the ported static
 * site's `.timeline` structure (assets/css/style.css:436-494).
 *
 * The section wrapper, eyebrow, and heading are NOT part of this block --
 * they live as regular editable core/paragraph and core/heading blocks in
 * templates/front-page.html, alongside this block. This function renders
 * only the CPT-driven <ol class="timeline"> itself.
 *
 * @return string
 */
if ( ! function_exists( 'tajwar_render_experience_timeline' ) ) {
	function tajwar_render_experience_timeline() {
		$query = new WP_Query( array(
			'post_type'      => 'experience',
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
		<ol class="timeline">
			<?php while ( $query->have_posts() ) : $query->the_post();
				$post_id    = get_the_ID();
				$role       = get_post_meta( $post_id, '_experience_role', true );
				$company    = get_post_meta( $post_id, '_experience_company', true );
				$location   = get_post_meta( $post_id, '_experience_location', true );
				$date_start = get_post_meta( $post_id, '_experience_date_start', true );
				$date_end   = get_post_meta( $post_id, '_experience_date_end', true );
				$is_current = (bool) get_post_meta( $post_id, '_experience_is_current', true );
				$bullets    = tajwar_sanitize_experience_bullets( get_post_meta( $post_id, '_experience_bullets', true ) );
				$date_label = $date_start . ' — ' . ( $is_current ? 'Present' : $date_end );
				?>
				<li class="timeline-item">
					<span class="timeline-date mono"><?php echo esc_html( $date_label ); ?></span>
					<h3 class="timeline-role"><?php echo esc_html( $role ); ?> <span class="timeline-co">· <?php echo esc_html( $company ); ?></span></h3>
					<p class="timeline-loc"><?php echo esc_html( $location ); ?></p>
					<ul>
						<?php foreach ( $bullets as $bullet ) : ?>
							<li><?php echo esc_html( $bullet ); ?></li>
						<?php endforeach; ?>
					</ul>
				</li>
			<?php endwhile; ?>
		</ol>
		<?php
		wp_reset_postdata();
		return ob_get_clean();
	}
}

// block.json's "render" field requires this file as a template and captures
// whatever it echoes (see WP_Block_Type::render() / wp-includes/blocks.php,
// register_block_type_from_metadata()) — it does not call the function above
// automatically, so we must invoke it here for the block to actually render.
echo tajwar_render_experience_timeline();
