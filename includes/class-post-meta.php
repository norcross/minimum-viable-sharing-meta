<?php
/**
 * Our individual post meta setup.
 *
 * @package MinimumViableMeta
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Start our engines.
 */
class MinimumViableMeta_PostMeta {

	/**
	 * Call our hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'add_meta_boxes',               array( $this, 'load_metabox'            ),  11      );
		add_action( 'save_post',                    array( $this, 'save_post_meta'          )           );
	}

	/**
	 * Call our metabox setup for content.
	 *
	 * @param  string $post_type  The post type we're on.
	 *
	 * @return void
	 */
	public function load_metabox( $post_type ) {

		// Bail if we are not in an approved post type.
		if ( ! in_array( $post_type, minshare_meta()->supported_types() ) ) {
			return;
		}

		// Now add the box.
		add_meta_box( 'minshare-meta-metabox', __( 'Sharing Meta Tags', 'minimum-viable-sharing-meta' ), array( __class__, 'display_metabox' ), $post_type, 'advanced', 'high' );
	}

	/**
	 * Display the metabox for the invividual data.
	 *
	 * @param  object $post  The global post object.
	 *
	 * @return mixed
	 */
	public static function display_metabox( $post ) {

		// Fetch my group of fields, and bail without them.
		if ( false === $fields = MinimumViableMeta_Fields::get_fields_group() ) {
			return;
		}

		// Fetch whatever meta we may have.
		$meta   = get_post_meta( $post->ID, MINSHARE_META_POSTKEY, true );

		// Our before fields action.
		do_action( 'minshare_meta_before_metabox_fields', $post );

		// Wrap our meta in a table.
		echo '<table class="form-table minshare-meta-data-input-wrap minshare-meta-table-wrap">';
		echo '<tbody>';

		// Loop my fields.
		foreach ( $fields as $id => $field ) {

			// Our before action on the single field.
			do_action( 'minshare_meta_before_metabox_field_{$id}', $post, $id, $field );

			// Force a field type.
			$ftype  = ! empty( $field['type'] ) ? $field['type'] : 'text';
			$fkey   = ! empty( $field['key'] ) ? $field['key'] : '';

			// Parse out the field class and label.
			$class  = ! empty( $field['class'] ) ? $field['class'] : 'single-minshare-meta-field';
			$fname  = ! empty( $field['fname'] ) ? $field['fname'] : '';

			// Check for a value.
			$value  = ! empty( $meta[ $fkey ] ) ? $meta[ $fkey ] : '';

			// And echo out our field.
			echo '<tr class="' . esc_attr( $class ) . '">';

				// Handle the labeling.
				echo '<th>' . esc_attr( $fname ) . '</th>';

				// And our field.
				echo '<td>' . self::single_meta_field( $ftype, $field, $value ) . '</td>';

			// Close the row.
			echo '</tr>';

			// Our after action on the single field.
			do_action( 'minshare_meta_after_metabox_field_{$id}', $post, $id, $field );
		}

		// And our nonce field.
		echo wp_nonce_field( 'minshare_nonce_action', 'minshare_nonce_field', false, false );

		// Close our table wrap.
		echo '</tbody>';
		echo '</table>';

		// Our after fields action.
		do_action( 'minshare_meta_after_metabox_fields', $post );
	}

	/**
	 * Load a single meta field, which also involves us adding more.
	 *
	 * @param  string $type   The field type.
	 * @param  array  $field  The individual field args.
	 * @param  mixed  $value  The possible value of the field.
	 *
	 * @return HTML
	 */
	public static function single_meta_field( $type = 'text', $field = array(), $value ) {

		// Handle my different field types.
		switch ( esc_attr( $type ) ) {

			case 'text' :
			case 'url' :
				return MinimumViableMeta_Fields::text_field( $field, $value );
				break;

			case 'textarea' :
				return MinimumViableMeta_Fields::textarea_field( $field, $value );
				break;

			case 'media' :
				return MinimumViableMeta_Fields::media_upload_field( $field, $value );
				break;

			case 'radio' :
				return MinimumViableMeta_Fields::radio_field( $field, $value );
				break;
		}

		// Our after fields action.
		do_action( 'minshare_meta_single_meta_field', $type, $field, $value );
	}

	/**
	 * Store the metadata being passed.
	 *
	 * @param  integer $post_id  The post ID we're saving.
	 *
	 * @return void
	 */
	public function save_post_meta( $post_id ) {

		// Bail out if running an autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Bail out if running an ajax.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Bail out if running a cron, unless we've skipped that.
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		// Bail out if user does not have permissions.
		if ( false === $check = current_user_can( minshare_meta()->role_cap(), $post_id ) ) {
			return;
		}

		// Bail if we are not in an approved post type.
		if ( ! in_array( get_post_type( $post_id ), minshare_meta()->supported_types() ) ) {
			return;
		}

		// Do the nonce check. ALWAYS CHECK YOUR NONCES.
		if ( ! isset( $_POST['minshare_nonce_field'] ) || ! wp_verify_nonce( $_POST['minshare_nonce_field'], 'minshare_nonce_action' ) ) {
			return;
		}

		// Sanitize the data if we have it.
		$meta   = ! empty( $_POST['minshare_meta_defaults'] ) ? MinimumViableMeta_Helper::array_sanitize( $_POST['minshare_meta_defaults'] ) : '';

		// And update the meta.
		update_post_meta( $post_id, MINSHARE_META_POSTKEY, $meta );
	}

	// End our class.
}

// Call our class.
$MinimumViableMeta_PostMeta = new MinimumViableMeta_PostMeta();
$MinimumViableMeta_PostMeta->init();
