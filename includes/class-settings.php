<?php
/**
 * Our settings API functions to store new defaults.
 *
 * @package MinimumViableMeta
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Start our engines.
 */
class MinimumViableMeta_Settings {

	/**
	 * The slugs being used for the menus.
	 */
	public static $menu_slug = 'minshare-meta-settings';

	/**
	 * Call our hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_init',                   array( $this, 'load_default_settings'   )           );
		add_action( 'admin_menu',                   array( $this, 'load_admin_menu'         )           );
	}

	/**
	 * Register the settings for our default values.
	 *
	 * @return void
	 */
	public function load_default_settings() {

		// Fetch my group of fields, and bail without them.
		if ( false === $fields = MinimumViableMeta_Fields::get_fields_group( false ) ) {
			return;
		}

		// Create the general settings section.
		add_settings_section(
			'minshare-meta-default-settings-section',
			'',
			array( __class__, 'settings_header' ),
			self::$menu_slug
		);

		// Loop my fields.
		foreach ( $fields as $id => $field ) {

			// Load each individual setting.
			add_settings_field(
				'minshare-meta-default-settings-' . esc_attr( $id ),
				$field['fname'],
				array( __class__, 'settings_field_callback' ),
				self::$menu_slug,
				'minshare-meta-default-settings-section',
				$field
			);
		}

		// Register the from name setting.
		register_setting(
			'minshare-meta-default-settings-section',
			'minshare_meta_defaults',
			array( 'sanitize_callback' => array( 'MinimumViableMeta_Helper', 'array_sanitize' ) )
		);

		// Include our action before it all fires.
		do_action( 'minshare_meta_register_default_settings' );
	}

	/**
	 * Load our settings menu under Tools.
	 *
	 * @return void
	 */
	public function load_admin_menu() {
		add_theme_page( __( 'Minimum Viable Sharing Meta', 'minimum-viable-sharing-meta' ), __( 'Sharing Meta Tags', 'minimum-viable-sharing-meta' ), minshare_meta()->role_cap(), self::$menu_slug, array( __class__, 'view_default_settings' ) );
	}

	/**
	 * Our actual settings page.
	 *
	 * @return HTML
	 */
	public static function view_default_settings() {

		// Fire the before action.
		do_action( 'minshare_meta_before_settings_page' );

		// Handle the form wrap.
		echo '<div class="wrap minshare-meta-data-input-wrap minshare-meta-settings-page-wrap">';

			// Output the title.
			echo '<h1 class="minshare-meta-settings-title">' . get_admin_page_title() . '</h1>';

			// The error handler.
			settings_errors();

			// Handle the before action on the form itself.
			do_action( 'minshare_meta_before_settings_form' );

			// And the actual form.
			echo '<form method="post" action="options.php">';

				// Output the constructed settings fields.
				settings_fields( 'minshare-meta-default-settings-section' );

				// Do said sections.
				do_settings_sections( self::$menu_slug );

				// Add our submit button.
				submit_button();

				// Handle the after action inside the form itself.
				do_action( 'minshare_meta_after_settings_submit' );

			// Close the form.
			echo '</form>';

			// Handle the after action for the form itself.
			do_action( 'minshare_meta_after_settings_form' );

		// Close the div.
		echo '</div>';

		// Fire the after action.
		do_action( 'minshare_meta_after_settings_page' );
	}

	/**
	 * Our input fields for the API settings.
	 *
	 * @param  array $args  The args I passed.
	 *
	 * @return  HTML
	 */
	public static function settings_field_callback( $args ) {

		// Force a field type.
		$ftype  = ! empty( $args['type'] ) ? $args['type'] : 'text';
		$fkey   = ! empty( $args['key'] ) ? $args['key'] : '';

		// Check the values that may have been stored as well.
		$values = MinimumViableMeta_Helper::get_saved_settings();

		// Check for a value.
		$value  = ! empty( $values[ $fkey ] ) ? $values[ $fkey ] : false;

		// Handle my different field types.
		switch ( esc_attr( $ftype ) ) {

			case 'text' :
			case 'url' :
				echo MinimumViableMeta_Fields::text_field( $args, $value );
				break;

			case 'textarea' :
				echo MinimumViableMeta_Fields::textarea_field( $args, $value );
				break;

			case 'media' :
				echo MinimumViableMeta_Fields::media_upload_field( $args, $value );
				break;

			case 'radio' :
				echo MinimumViableMeta_Fields::radio_field( $args, $value );
				break;
		}
	}

	/**
	 * Output the small amount of text in the settings area.
	 */
	public static function settings_header() {
		echo '<p>' . esc_html__( 'Enter the values to be used as defaults when individual items are not available.', 'minimum-viable-sharing-meta' ) . '</p>';
	}

	// End our class.
}

// Call our class.
$MinimumViableMeta_Settings = new MinimumViableMeta_Settings();
$MinimumViableMeta_Settings->init();
