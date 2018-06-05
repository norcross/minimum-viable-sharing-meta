<?php
/**
 * Our Helper functions.
 *
 * Random things to use wherever.
 *
 * @package MinimumViableMeta
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Start our engines.
 */
class MinimumViableMeta_Helper {

	/**
	 * Get our saved settings, returning an optional key.
	 *
	 * @param  string $key  An optional key.
	 *
	 * @return mixed
	 */
	public static function get_saved_settings( $key = '' ) {

		// First get our overall options.
		$saved  = get_option( MINSHARE_META_OPTIONKEY, array() );

		// Bail if no saved data, or no data for the requested key.
		if ( empty( $saved ) || ! empty( $key ) && empty( $saved[ $key ] ) ) {
			return false;
		}

		// Return one or the other.
		return ! empty( $key ) ? $saved[ $key ] : $saved;
	}

	/**
	 * Get our saved post meta, returning an optional key.
	 *
	 * @param  integer $post_id  The post ID we are checking.
	 * @param  string  $key      An optional key.
	 *
	 * @return mixed
	 */
	public static function get_single_tags( $post_id = 0, $key = '' ) {

		// If no post ID was passed, return the defaults right away.
		if ( empty( $post_id ) ) {
			return self::get_saved_settings( $key );
		}

		// Fetch whatever meta we may have.
		$meta   = get_post_meta( $post_id, MINSHARE_META_POSTKEY, true );

		// Bail if no saved meta, or no meta for the requested key.
		if ( empty( $meta ) || ! empty( $key ) && empty( $meta[ $key ] )  ) {

			// Add the check for using the default values.
			return false !== apply_filters( 'minshare_meta_use_default_tags', false ) ? self::get_saved_settings( $key ) : false;
		}

		// Return one or the other.
		return ! empty( $key ) ? $meta[ $key ] : $meta;
	}

	/**
	 * Get the base from the current screen.
	 *
	 * @param  mixed $names  A string or array of screen base names.
	 *
	 * @return string
	 */
	public static function check_screen_base( $names ) {

		// Bail without the screen object function or not admin.
		if ( ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		// Fetch current screen object.
		$screen = get_current_screen();

		// Bail if we aren't on the right part, or the screen object is fucked.
		if ( ! is_object( $screen ) || empty( $screen->base ) ) {
			return false;
		}

		// Do the check for an array of names.
		return in_array( $screen->base, (array) $names ) ? $screen : false;
	}

	/**
	 * Sanitize the array based data inputs.
	 *
	 * @param  array $input  The data entered in a settings field.
	 *
	 * @return array $input  The sanitized data.
	 */
	public static function array_sanitize( $input ) {

		// Set an empty.
		$output = array();

		// Now loop the input data.
		foreach ( $input as $k => $v ) {

			// Handle my different field.
			switch ( esc_attr( $k ) ) {

				case 'title' :
				case 'card' :
					$output[ $k ] = ! empty( $v ) ? sanitize_text_field( $v ) : '';
					break;

				case 'desc' :
					$output[ $k ] = ! empty( $v ) ? sanitize_textarea_field( $v ) : '';
					break;

				case 'image' :
				case 'canonical' :
					$output[ $k ] = ! empty( $v ) ? esc_url( $v ) : '';
					break;

				default :
					$output[ $k ] = ! empty( $v ) ? sanitize_text_field( $v ) : '';
			}
		}

		// Now return it.
		return array_filter( $output, 'strlen' );
	}

	// End our class.
}

// Call our class.
new MinimumViableMeta_Helper();
