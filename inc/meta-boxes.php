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
	if ( ! isset( $_POST['tajwar_experience_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tajwar_experience_nonce'] ?? '' ) ), 'tajwar_save_experience' ) ) {
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

function tajwar_add_project_meta_box() {
	add_meta_box( 'tajwar_project_details', 'Project Details', 'tajwar_render_project_meta_box', 'project', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'tajwar_add_project_meta_box' );

function tajwar_render_project_meta_box( $post ) {
	wp_nonce_field( 'tajwar_save_project', 'tajwar_project_nonce' );
	$tags = get_post_meta( $post->ID, '_project_tags', true );
	?>
	<p>
		<label for="tajwar_project_tags"><strong>Tech tags</strong> (comma-separated)</label><br>
		<input type="text" id="tajwar_project_tags" name="tajwar_project_tags" class="widefat" value="<?php echo esc_attr( $tags ); ?>" placeholder="Laravel, Shopify API, PHP">
	</p>
	<?php
}

function tajwar_save_project_meta( $post_id ) {
	if ( ! isset( $_POST['tajwar_project_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tajwar_project_nonce'] ?? '' ) ), 'tajwar_save_project' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Field names below are 'tajwar' . $meta_key, matching the <input name="..."> attributes
	// rendered in tajwar_render_project_meta_box() above (tajwar_project_tags).
	if ( isset( $_POST['tajwar_project_tags'] ) ) {
		update_post_meta( $post_id, '_project_tags', sanitize_text_field( wp_unslash( $_POST['tajwar_project_tags'] ) ) );
	}
}
add_action( 'save_post_project', 'tajwar_save_project_meta' );

function tajwar_add_work_site_meta_box() {
	add_meta_box( 'tajwar_work_site_details', 'Work Site Details', 'tajwar_render_work_site_meta_box', 'work_site', 'side', 'default' );
}
add_action( 'add_meta_boxes', 'tajwar_add_work_site_meta_box' );

function tajwar_render_work_site_meta_box( $post ) {
	wp_nonce_field( 'tajwar_save_work_site', 'tajwar_work_site_nonce' );
	$url             = get_post_meta( $post->ID, '_work_site_url', true );
	$platform        = get_post_meta( $post->ID, '_work_site_platform', true );
	$preview_blocked = (bool) get_post_meta( $post->ID, '_work_site_preview_blocked', true );
	$platforms       = array( 'WordPress', 'Shopify', 'Magento 2' );
	?>
	<p>
		<label for="tajwar_work_site_url"><strong>Live URL</strong></label><br>
		<input type="url" id="tajwar_work_site_url" name="tajwar_work_site_url" class="widefat" value="<?php echo esc_attr( $url ); ?>" required>
	</p>
	<p>
		<label for="tajwar_work_site_platform"><strong>Platform</strong></label><br>
		<select id="tajwar_work_site_platform" name="tajwar_work_site_platform" class="widefat">
			<?php foreach ( $platforms as $option ) : ?>
				<option value="<?php echo esc_attr( $option ); ?>" <?php selected( $platform, $option ); ?>><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label><input type="checkbox" name="tajwar_work_site_preview_blocked" value="1" <?php checked( $preview_blocked ); ?>> This site blocks automated screenshots (show the fallback tile instead of the featured image)</label>
	</p>
	<p>Set the screenshot as this post's <strong>Featured Image</strong> in the panel on the right.</p>
	<?php
}

function tajwar_save_work_site_meta( $post_id ) {
	if ( ! isset( $_POST['tajwar_work_site_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tajwar_work_site_nonce'] ?? '' ) ), 'tajwar_save_work_site' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Field names below are 'tajwar' . $meta_key, matching the <input name="..."> attributes
	// rendered in tajwar_render_work_site_meta_box() above (tajwar_work_site_url, tajwar_work_site_platform).
	if ( isset( $_POST['tajwar_work_site_url'] ) ) {
		update_post_meta( $post_id, '_work_site_url', esc_url_raw( wp_unslash( $_POST['tajwar_work_site_url'] ) ) );
	}
	if ( isset( $_POST['tajwar_work_site_platform'] ) ) {
		update_post_meta( $post_id, '_work_site_platform', sanitize_text_field( wp_unslash( $_POST['tajwar_work_site_platform'] ) ) );
	}
	update_post_meta( $post_id, '_work_site_preview_blocked', isset( $_POST['tajwar_work_site_preview_blocked'] ) );
}
add_action( 'save_post_work_site', 'tajwar_save_work_site_meta' );
