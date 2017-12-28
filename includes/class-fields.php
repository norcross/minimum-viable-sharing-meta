<?php
/**
 * Our various fields we use on settings and post meta.
 *
 * @package MinimumViableMeta
 */

/**
 * Start our engines.
 */
class MinimumViableMeta_Fields {

	/**
	 * Get our array of fields for the settings.
	 *
	 * @return array
	 */
	public static function get_fields_group() {

		// Our field groupings.
		return array(

			// The title field.
			'title' => array(
				'type'  => 'text',
				'key'   => 'title',
				'id'    => 'minshare-meta-title',
				'name'  => 'minshare_meta_defaults[title]',
				'fname' => __( 'Title', 'minimum-viable-sharing-meta' ),
				'class' => 'minshare-meta-title-field',
			),

			// The description field.
			'desc'  => array(
				'type'  => 'textarea',
				'key'   => 'desc',
				'id'    => 'minshare-meta-desc',
				'name'  => 'minshare_meta_defaults[desc]',
				'fname' => __( 'Description', 'minimum-viable-sharing-meta' ),
				'class' => 'minshare-meta-desc-field',
			),

			// The image field.
			'image' => array(
				'type'  => 'media',
				'key'   => 'image',
				'id'    => 'minshare-meta-image',
				'name'  => 'minshare_meta_defaults[image]',
				'fname' => __( 'Image', 'minimum-viable-sharing-meta' ),
				'class' => 'minshare-meta-image-field',
			),

			// The twitter card field.
			'card'  => array(
				'type'  => 'radio',
				'key'   => 'card',
				'id'    => 'minshare-meta-card',
				'name'  => 'minshare_meta_defaults[card]',
				'fname' => __( 'Twitter Card Size', 'minimum-viable-sharing-meta' ),
				'items' => array(
					'summary'               => __( 'Standard', 'minimum-viable-sharing-meta' ),
					'summary_large_image'   => __( 'Large', 'minimum-viable-sharing-meta' ),
				),
				'class' => 'minshare-meta-card-field',
			),
		);
	}

	/**
	 * A text input.
	 *
	 * @param  array   $args   The field args I passed.
	 * @param  mixed   $value  The current value of the field (if any).
	 * @param  boolean $echo   Whether to echo the field or return it.
	 *
	 * @return  HTML
	 */
	public static function text_field( $args = array(), $value, $echo = false ) {

		// Set my default args.
		$base   = array(
			'id'    => wp_generate_password( 8, false, false ),
			'type'  => 'text',
			'name'  => '',
			'label' => '',
		);

		// Parse my args.
		$args   = wp_parse_args( $args, $base );

		// And filter out any empty.
		$args   = array_filter( $args );

		// Make sure we have a type and value.
		$type   = ! empty( $args['type'] ) ? $args['type'] : '';

		// Set my empty.
		$field  = '';

		// Build the field type.
		$field .= '<input type="' . esc_attr( $type ) . '" class="widefat" value="' . esc_attr( $value ) . '" id="' . esc_attr( $args['id'] ) . '"';

		// Check for name.
		if ( ! empty( $args['name'] ) ) {
			$field .= ' name="' . esc_attr( $args['name'] ) . '"';
		}

		// Check for placeholder.
		if ( ! empty( $args['place'] ) ) {
			$field .= ' placeholder="' . esc_attr( $args['place'] ) . '"';
		}

		// Check for extras.
		if ( ! empty( $args['extra'] ) && is_array( $args['extra'] ) ) {

			// Loop the extras and add them.
			foreach ( $args['extra'] as $n => $v ) {
				$field .= ' ' . esc_attr( $n ) . '="' . esc_attr( $v ) . '"';
			}
		}

		// Close it up.
		$field .= ' />';

		// Output our label if we have one.
		if ( ! empty( $args['label'] ) ) {
			$field .= '<label class="field-label" for="' . esc_attr( $args['id'] ) . '">' . esc_html( $args['label'] ) . '</label>';
		}

		// Echo it if requested.
		if ( ! empty( $echo ) ) {
			echo $field;
		}

		// Just return it.
		return $field;
	}

	/**
	 * A textarea input.
	 *
	 * @param  array   $args   The field args I passed.
	 * @param  mixed   $value  The current value of the field (if any).
	 * @param  boolean $echo   Whether to echo the field or return it.
	 *
	 * @return  HTML
	 */
	public static function textarea_field( $args = array(), $value, $echo = false ) {

		// Set my default args.
		$base   = array(
			'id'    => wp_generate_password( 8, false, false ),
			'name'  => '',
			'label' => '',
		);

		// Parse my args.
		$args   = wp_parse_args( $args, $base );

		// And filter out any empty.
		$args   = array_filter( $args );

		// Set my empty.
		$field  = '';

		// Build the field type.
		$field .= '<textarea class="widefat" id="' . esc_attr( $args['id'] ) . '"';

		// Check for name.
		if ( ! empty( $args['name'] ) ) {
			$field .= ' name="' . esc_attr( $args['name'] ) . '"';
		}

		// Check for extras.
		if ( ! empty( $args['extra'] ) && is_array( $args['extra'] ) ) {

			// Loop the extras and add them.
			foreach ( $args['extra'] as $n => $v ) {
				$field .= ' ' . esc_attr( $n ) . '="' . esc_attr( $v ) . '"';
			}
		}

		// Close it up, with the value.
		$field .= ' />' . esc_attr( $value ) . '</textarea>';

		// Output our label if we have one.
		if ( ! empty( $args['label'] ) ) {
			$field .= '<label class="field-label" for="' . esc_attr( $args['id'] ) . '">' . esc_html( $args['label'] ) . '</label>';
		}

		// Echo it if requested.
		if ( ! empty( $echo ) ) {
			echo $field;
		}

		// Just return it.
		return $field;
	}

	/**
	 * Our upload / media library field.
	 *
	 * @param  array   $args   The field args I passed.
	 * @param  mixed   $value  The current value of the field (if any).
	 * @param  boolean $echo   Whether to echo the field or return it.
	 *
	 * @return  HTML
	 */
	public static function media_upload_field( $args = array(), $value, $echo = false ) {

		// preprint( $args, true );

		// Set my default args.
		$base   = array(
			'id'    => wp_generate_password( 8, false, false ),
			'name'  => '',
			'label' => '',
		);

		// Parse my args.
		$args   = wp_parse_args( $args, $base );

		// And filter out any empty.
		$args   = array_filter( $args );

		// Set my empty.
		$field  = '';

		// Build the field type.
		$field .= '<input type="url" class="regular-text upload-field" value="' . esc_attr( $value ) . '" id="' . esc_attr( $args['id'] ) . '"';

		// Check for name.
		if ( ! empty( $args['name'] ) ) {
			$field .= ' name="' . esc_attr( $args['name'] ) . '"';
		}

		// Check for placeholder.
		if ( ! empty( $args['place'] ) ) {
			$field .= ' placeholder="' . esc_attr( $args['place'] ) . '"';
		}

		// Check for extras.
		if ( ! empty( $args['extra'] ) && is_array( $args['extra'] ) ) {

			// Loop the extras and add them.
			foreach ( $args['extra'] as $n => $v ) {
				$field .= ' ' . esc_attr( $n ) . '="' . esc_attr( $v ) . '"';
			}
		}

		// Close it up.
		$field .= ' />';

		// Now add the button.
		$field .= '<button id="" class="button button-small button-secondary upload-button" type="button">' . esc_html__( 'Upload', 'minimum-viable-sharing-meta' ) . '</button>';

		// Output our label if we have one.
		if ( ! empty( $args['label'] ) ) {
			$field .= '<p class="description">' . esc_html( $args['label'] ) . '</p>';
		}

		// Echo it if requested.
		if ( ! empty( $echo ) ) {
			echo $field;
		}

		// Just return it.
		return $field;
	}

	/**
	 * A radio input.
	 *
	 * @param  array   $args   The field args I passed.
	 * @param  mixed   $value  The current value of the field (if any).
	 * @param  boolean $echo   Whether to echo the field or return it.
	 *
	 * @return  HTML
	 */
	public static function radio_field( $args = array(), $value, $echo = false ) {

		// Check to see if we are wrapping the field.
		$wrap   = ! empty( $args['wrap'] ) ? true : false;

		// Set my default args.
		$base   = array(
			'id'    => wp_generate_password( 8, false, false ),
			'name'  => '',
			'items' => '',
		);

		// Parse my args.
		$args   = wp_parse_args( $args, $base );

		// And filter out any empty.
		$args   = array_filter( $args );

		// If no option data is available, bail.
		if ( empty( $args['items'] ) ) {
			return;
		}

		// Set my empty.
		$field  = '';

		// Set an empty counter.
		$i  = 0;

		// Now loop the data items.
		foreach ( $args['items'] as $k => $l ) {

			// Start the field structure.
			$field .= '<span class="minshare-meta-setting-radio-item">';

				// Set the field type.
				$field .= '<input type="radio" ';

				// Check for ID.
				if ( ! empty( $args['id'] ) ) {
					$field .= ' id="' . esc_attr( $args['id'] ) . '-' . absint( $i ) . '"';
				}

				// Check for name.
				if ( ! empty( $args['name'] ) ) {
					$field .= ' name="' . esc_attr( $args['name'] ) . '"';
				}

				// Output the value.
				$field .= ' value="' . esc_attr( $k ) . '"';

				// Check for extras.
				if ( ! empty( $extra ) ) {

					// Loop the extras and add them.
					foreach ( $extra as $n => $v ) {
						$field .= ' ' . esc_attr( $n ) . '="' . esc_attr( $v ) . '"';
					}
				}

				// Include the checked amount.
				$field .= checked( $value, $k, false );

				// Close it up.
				$field .= ' />';

				// Output the label.
				$field .= '<label for="' . esc_attr( $args['id'] ) . '-' . absint( $i ) . '">' . esc_html( $l ) . '</label>';

			// Close it up.
			$field .= '</span>';

			// Increment my counter.
			$i++;
		}

		// Echo it if requested.
		if ( ! empty( $echo ) ) {
			echo $field;
		}

		// Just return it.
		return $field;
	}

	// End our class.
}

// Call our class.
new MinimumViableMeta_Fields();
