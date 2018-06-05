<?php
/**
 * Our various fields we use on settings and post meta.
 *
 * @package MinimumViableMeta
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Start our engines.
 */
class MinimumViableMeta_Fields {

	/**
	 * Get our array of fields for the settings.
	 *
	 * @param  boolean $canonical  Whether or not to show the canonical field.
	 *
	 * @return array
	 */
	public static function get_fields_group( $canonical = true ) {

		// Our field groupings.
		$fields = array(

			// The title field.
			'title' => array(
				'type'  => 'text',
				'key'   => 'title',
				'id'    => 'minshare-meta-title',
				'name'  => 'minshare_meta_defaults[title]',
				'fname' => __( 'Title', 'minimum-viable-sharing-meta' ),
				'class' => 'minshare-meta-title-field',
				'more'  => 'https://moz.com/learn/seo/title-tag',
				'count' => true,
			),

			// The description field.
			'desc'  => array(
				'type'  => 'textarea',
				'key'   => 'desc',
				'id'    => 'minshare-meta-desc',
				'name'  => 'minshare_meta_defaults[desc]',
				'fname' => __( 'Description', 'minimum-viable-sharing-meta' ),
				'class' => 'minshare-meta-desc-field',
				'more'  => 'https://moz.com/learn/seo/meta-description',
				'count' => true,
			),

			// The image field.
			'image' => array(
				'type'  => 'media',
				'key'   => 'image',
				'id'    => 'minshare-meta-image',
				'name'  => 'minshare_meta_defaults[image]',
				'fname' => __( 'Image', 'minimum-viable-sharing-meta' ),
				'class' => 'minshare-meta-image-field',
				'help'  => __( 'Upload an image or select an existing.', 'minimum-viable-sharing-meta' ),
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

			// The canonical field.
			'canonical' => array(
				'type'  => 'url',
				'key'   => 'canonical',
				'id'    => 'minshare-meta-canonical',
				'name'  => 'minshare_meta_defaults[canonical]',
				'fname' => __( 'Canonical URL', 'minimum-viable-sharing-meta' ),
				'class' => 'minshare-meta-canonical-field',
				'help'  => sprintf( __( '<a href="%s">Click here</a> to learn more about canonical tags and duplicate content.', 'minimum-viable-sharing-meta' ), 'https://moz.com/learn/seo/duplicate-content' )
			),
		);

		// Unset the canonical if requested.
		if ( ! $canonical ) {
			unset( $fields['canonical'] );
		}

		// Return our array.
		return apply_filters( 'minshare_meta_field_groups', $fields );
	}

	/**
	 * A text input.
	 *
	 * @param  array   $args   The field args I passed.
	 * @param  mixed   $value  The current value of the field (if any).
	 * @param  boolean $echo   Whether to echo the field or return it.
	 *
	 * @return HTML
	 */
	public static function text_field( $args = array(), $value, $echo = false ) {

		// Set my default args.
		$base   = array(
			'id'    => wp_generate_password( 8, false, false ),
			'type'  => 'text',
			'name'  => '',
			'label' => '',
			'count' => false
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
		$field .= '<input type="' . esc_attr( $type ) . '" class="widefat" value="' . esc_attr( $value ) . '" id="' . esc_attr( $args['id'] ) . '" autocomplete="off" ';

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

		// If we called it, output the character counter.
		if ( ! empty( $args['count'] ) ) {

			// Check for the "more" item.
			$more   = ! empty( $args['more'] ) ? $args['more'] : false;

			// Show the field.
			$field .= self::show_character_count( $value, 'title', $more );
		}

		// Output our help text if we have one.
		if ( ! empty( $args['help'] ) ) {
			$field .= '<p class="description">' . wp_kses_post( $args['help'] ) . '</p>';
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
	 * @return HTML
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
		$field .= '<textarea class="widefat field-textarea" id="' . esc_attr( $args['id'] ) . '"';

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

		// If we called it, output the character counter.
		if ( ! empty( $args['count'] ) ) {

			// Check for the "more" item.
			$more   = ! empty( $args['more'] ) ? $args['more'] : false;

			// And output the field.
			$field .= self::show_character_count( $value, 'desc', $more );
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
	 * @return HTML
	 */
	public static function media_upload_field( $args = array(), $value, $echo = false ) {

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

		// Output our help text if we have one.
		if ( ! empty( $args['help'] ) ) {
			$field .= '<p class="description">' . esc_html( $args['help'] ) . '</p>';
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
	 * @return HTML
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

	/**
	 * Add the character count item to a field.
	 *
	 * @param  string  $value  The current field value.
	 * @param  string  $field  Which field we are on (for classes).
	 * @param  boolean $more   Whether to show the "more" link.
	 *
	 * @return HTML
	 */
	public static function show_character_count( $value = '', $field = 'title', $more = false ) {

		// Get my current character count.
		$count  = strlen( $value );

		// Fetch my limit.
		$limit  = minshare_meta()->max_field_length( $field );

		// Get my count class.
		$class  = 'current-count current-' . esc_attr( $field ) . '-count';
		$class .= absint( $count ) > absint( $limit ) ? ' current-count-over' : '';

		// Set my empty.
		$build  = '';

		// Open the paragraph.
		$build .= '<p class="description field-character-count-wrap ' . esc_attr( $field ) . '-character-count-wrap">';

			// And output the amount.
			$build .= sprintf( __( 'You have used %s of the maximum recommended %d characters.', 'minimum-viable-sharing-meta' ), '<span class="' . esc_attr( $class ) . '">' . absint( $count ) . '</span>', absint( $limit ) );

			// Add a "learn more" if asked.
			if ( ! empty( $more ) ) {
				$build .= '<span class="field-learn-more">' . sprintf( __( '<a target="_blank" href="%s">Click here</a> to learn more.', 'minimum-viable-sharing-meta' ), esc_url( $more ) ) . '</span>';
			}

		// Close the paragraph.
		$build .= '</p>';

		// Return the field.
		return $build;
	}

	// End our class.
}

// Call our class.
new MinimumViableMeta_Fields();
