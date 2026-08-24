<?php
defined( 'ABSPATH' ) || exit;

/**
 * Render the Projects grid markup: published Project CPT entries plus the
 * static "WordPress Plugins" card (index.html:421-467), which stays
 * hardcoded per the Global Constraints decision.
 *
 * The section wrapper, eyebrow, and heading are NOT part of this block --
 * they live as regular editable core/paragraph and core/heading blocks in
 * templates/front-page.html, alongside this block. This function renders
 * only the CPT-driven <div class="project-grid"> itself.
 *
 * @return string
 */
if ( ! function_exists( 'tajwar_render_projects_grid' ) ) {
	function tajwar_render_projects_grid() {
		$query = new WP_Query( array(
			'post_type'      => 'project',
			'post_status'    => 'publish',
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'posts_per_page' => -1,
		) );

		ob_start();
		?>
		<div class="project-grid">
			<?php while ( $query->have_posts() ) : $query->the_post();
				$tags = get_post_meta( get_the_ID(), '_project_tags', true );
				$tags = array_filter( array_map( 'trim', explode( ',', (string) $tags ) ) );
				?>
				<article class="project-card">
					<h3><?php the_title(); ?></h3>
					<p><?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?></p>
					<?php if ( $tags ) : ?>
						<ul class="tag-list mono">
							<?php foreach ( $tags as $tag ) : ?>
								<li><?php echo esc_html( $tag ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</article>
			<?php endwhile; wp_reset_postdata(); ?>

			<article class="project-card project-card-wide">
				<h3>WordPress Plugins</h3>
				<p class="plugin-intro">A selection of custom plugins built for client and internal use:</p>
				<ul class="plugin-list">
					<li><strong>wp-sleek-admin</strong> — a sleek, modern WordPress admin theme with light/dark mode and full color customization.</li>
					<li><strong>smart-404-redirect</strong> — intelligently redirects 404s to relevant pages, with wildcard URL pattern matching.</li>
					<li><strong>custom-form-builder</strong> — a drag-and-drop interface for creating and managing custom forms.</li>
					<li><strong>portable-vc-addons</strong> — extends WPBakery Page Builder with Post Slider, Single Post, Hover Box and Custom Table elements.</li>
					<li><strong>image-slider</strong> — a custom WordPress image slider.</li>
				</ul>
				<a href="https://github.com/tajwar-t?tab=repositories" target="_blank" rel="noopener noreferrer" class="more-link">
					View more on GitHub
					<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true"><path fill="currentColor" d="M5 12h12.17l-4.88-4.88L14 5l7 7-7 7-1.71-1.12L17.17 13H5v-1Z"/></svg>
				</a>
			</article>
		</div>
		<?php
		return ob_get_clean();
	}
}

// block.json's "render" field requires this file as a template and captures
// whatever it echoes (see WP_Block_Type::render() / wp-includes/blocks.php,
// register_block_type_from_metadata()) — it does not call the function above
// automatically, so we must invoke it here for the block to actually render.
echo tajwar_render_projects_grid();
