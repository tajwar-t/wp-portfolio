<?php
/**
 * Custom meta boxes for the theme's custom post types.
 *
 * Populated by later tasks in the FSE theme conversion plan.
 */

defined( 'ABSPATH' ) || exit;

function tajwar_add_experience_meta_box() {
	add_meta_box(
		'tajwar_experience_details',
		'Experience Details',
		'tajwar_render_experience_meta_box',
		'experience',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'tajwar_add_experience_meta_box' );

function tajwar_render_experience_meta_box( $post ) {
	wp_nonce_field( 'tajwar_save_experience', 'tajwar_experience_nonce' );

	$company    = get_post_meta( $post->ID, '_experience_company', true );
	$role       = get_post_meta( $post->ID, '_experience_role', true );
	$location   = get_post_meta( $post->ID, '_experience_location', true );
	$date_start = get_post_meta( $post->ID, '_experience_date_start', true );
	$date_end   = get_post_meta( $post->ID, '_experience_date_end', true );
	$is_current = (bool) get_post_meta( $post->ID, '_experience_is_current', true );
	$bullets    = get_post_meta( $post->ID, '_experience_bullets', true );
	?>
	<p>
		<label for="tajwar_experience_role"><strong>Role</strong></label><br>
		<input type="text" id="tajwar_experience_role" name="tajwar_experience_role" class="widefat" value="<?php echo esc_attr( $role ); ?>">
	</p>
	<p>
		<label for="tajwar_experience_company"><strong>Company</strong></label><br>
		<input type="text" id="tajwar_experience_company" name="tajwar_experience_company" class="widefat" value="<?php echo esc_attr( $company ); ?>">
	</p>
	<p>
		<label for="tajwar_experience_location"><strong>Location</strong></label><br>
		<input type="text" id="tajwar_experience_location" name="tajwar_experience_location" class="widefat" value="<?php echo esc_attr( $location ); ?>">
	</p>
	<p>
		<label for="tajwar_experience_date_start"><strong>Start (e.g. "Jun 2025")</strong></label><br>
		<input type="text" id="tajwar_experience_date_start" name="tajwar_experience_date_start" value="<?php echo esc_attr( $date_start ); ?>">
		&nbsp;&nbsp;
		<label for="tajwar_experience_date_end"><strong>End (e.g. "Apr 2025")</strong></label><br>
		<input type="text" id="tajwar_experience_date_end" name="tajwar_experience_date_end" value="<?php echo esc_attr( $date_end ); ?>">
	</p>
	<p>
		<label><input type="checkbox" name="tajwar_experience_is_current" value="1" <?php checked( $is_current ); ?>> Currently working here (shows "Present" instead of the end date)</label>
	</p>
	<p>
		<label for="tajwar_experience_bullets"><strong>Highlights (one per line)</strong></label><br>
		<textarea id="tajwar_experience_bullets" name="tajwar_experience_bullets" class="widefat" rows="5"><?php echo esc_textarea( $bullets ); ?></textarea>
	</p>
	<?php
}

function tajwar_save_experience_meta( $post_id ) {
	if ( ! isset( $_POST['tajwar_experience_nonce'] ) || ! wp_verify_nonce( $_POST['tajwar_experience_nonce'], 'tajwar_save_experience' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'_experience_company'    => 'sanitize_text_field',
		'_experience_role'       => 'sanitize_text_field',
		'_experience_location'   => 'sanitize_text_field',
		'_experience_date_start' => 'sanitize_text_field',
		'_experience_date_end'   => 'sanitize_text_field',
	);
	foreach ( $fields as $meta_key => $sanitizer ) {
		// e.g. '_experience_company' -> 'tajwar_experience_company', matching the render callback's field names.
		$field_name = 'tajwar' . $meta_key;
		if ( isset( $_POST[ $field_name ] ) ) {
			update_post_meta( $post_id, $meta_key, call_user_func( $sanitizer, wp_unslash( $_POST[ $field_name ] ) ) );
		}
	}

	update_post_meta( $post_id, '_experience_is_current', isset( $_POST['tajwar_experience_is_current'] ) );

	if ( isset( $_POST['tajwar_experience_bullets'] ) ) {
		$bullets = tajwar_sanitize_experience_bullets( wp_unslash( $_POST['tajwar_experience_bullets'] ) );
		update_post_meta( $post_id, '_experience_bullets', implode( "\n", $bullets ) );
	}
}
add_action( 'save_post_experience', 'tajwar_save_experience_meta' );
