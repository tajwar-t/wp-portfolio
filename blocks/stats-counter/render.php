<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'tajwar_render_stats_counter' ) ) {
	/**
	 * Render the hero stats row. Each .stat-num starts at 0 and carries the
	 * data attributes blocks/stats-counter/view.js reads to animate the
	 * count-up when the element scrolls into view.
	 *
	 * @return string
	 */
	function tajwar_render_stats_counter() {
		$stats = array(
			array(
				'target' => 7,
				'suffix' => '+',
				'label'  => 'Years Experience',
			),
			array(
				'target' => 150,
				'suffix' => '+',
				'label'  => 'Projects Delivered',
			),
			array(
				'target' => 190,
				'suffix' => '+',
				'label'  => '5★ Fiverr Reviews',
			),
			array(
				'target' => 30,
				'suffix' => '+',
				'label'  => 'Custom Plugins Built',
			),
		);

		ob_start();
		?>
		<div class="hero-stats">
			<?php foreach ( $stats as $stat ) : ?>
				<div class="stat">
					<span
						class="stat-num"
						data-count-to="<?php echo esc_attr( $stat['target'] ); ?>"
						data-suffix="<?php echo esc_attr( $stat['suffix'] ); ?>"
					>0<?php echo esc_html( $stat['suffix'] ); ?></span>
					<span class="stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}

echo tajwar_render_stats_counter();
