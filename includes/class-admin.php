<?php
/**
 * Our generic admin setup.
 *
 * @package MinimumViableMeta
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Start our engines.
 */
class MinimumViableMeta_Admin {

	/**
	 * Call our hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_notices',                        array( $this, 'meta_convert_results'    )           );
		add_action( 'admin_init',                           array( $this, 'call_meta_conversion'    )           );
		add_action( 'admin_enqueue_scripts',                array( $this, 'load_admin_assets'       ),  10      );
		add_action( 'minshare_meta_after_settings_form',    array( $this, 'add_convert_options'     )           );
		add_filter( 'admin_footer_text',                    array( $this, 'admin_footer_text'       )           );
	}

	/**
	 * Display the message based on our fetch result.
	 *
	 * @return void
	 */
	public function meta_convert_results() {

		// Dont show it if we don't have the flag.
		if ( empty( $_GET['minshare-meta-convert-success'] ) ) {
			return;
		}

		// And handle the notice.
		echo '<div class="notice notice-success is-dismissible">';
			echo '<p>' . esc_html__( 'Success! The existing meta keys have been converted.', 'minimum-viable-sharing-meta' ) . '</p>';
		echo '</div>';

		// And bail.
		return;
	}

	/**
	 * Run our actual meta conversion.
	 *
	 * @return
	 */
	public function call_meta_conversion() {

		// Make sure we're on the correct page.
		if ( empty( $_GET['minshare-meta-convert'] ) || empty( $_GET['page'] ) || 'minshare-meta-settings' !== esc_attr( $_GET['page'] ) ) {
			return;
		}

		// Do our nonce check. ALWAYS A NONCE CHECK.
		if ( empty( $_GET['minshare-meta-nonce'] ) || ! wp_verify_nonce( $_GET['minshare-meta-nonce'], 'minshare-meta-nonce' ) ) {
			return;
		}

		// Set my source.
		$source = ! empty( $_GET['minshare-meta-convert-source'] ) ? esc_attr( $_GET['minshare-meta-convert-source'] ) : '';

		// Handle checking the source.
		if ( empty( $source ) || ! in_array( $source, minshare_meta()->plugin_convert_keys( true ) ) ) {
			return;
		}

		// Run our conversion.
		minshare_meta()->convert_post_meta( $source );

		// Create our link to trigger the conversion.
		$link   = add_query_arg( 'minshare-meta-convert-success', 1, menu_page_url( 'minshare-meta-settings', false ) );

		// And redirect.
		wp_redirect( $link );
		exit();
	}

	/**
	 * Load our admin side JS and CSS.
	 *
	 * @return void
	 */
	public function load_admin_assets( $hook ) {

		// Run my screen check function first.
		if ( false === $screen = MinimumViableMeta_Helper::check_screen_base( array( 'post', 'appearance_page_minshare-meta-settings' ) ) ) {
			return;
		}

		// Set my post type.
		$ptype  = ! empty( $screen->post_type ) ? $screen->post_type : false;

		// We fit the requirement, so let's do it.
		if ( 'appearance_page_minshare-meta-settings' === $screen->base || 'post' === $screen->base && in_array( $ptype, minshare_meta()->supported_types() ) ) {

			// Set a file structure based on whether or not we want a minified version.
			$name   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'minshare-meta-admin' : 'minshare-meta-admin.min';

			// Set a version for whether or not we're debugging.
			$vers   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : MINSHARE_META_VERS;

			// Set our two file names.
			$file_c = MINSHARE_META_ASSETS_URL . '/css/' . esc_attr( $name ) . '.css';
			$file_j = MINSHARE_META_ASSETS_URL . '/js/' . esc_attr( $name ) . '.js';

			// Load our CSS file.
			wp_enqueue_style( 'minshare-meta-admin', esc_url( $file_c ), false, $vers, 'all' );

			// Set our array of localized values.
			$local_user = apply_filters( 'minshare_meta_localized_js_args', array() );
			$localized  = wp_parse_args( $local_user, array( 'maxTitle' => minshare_meta()->max_title_length(), 'maxDesc' => minshare_meta()->max_description_length() ) );

			// And our JS.
			wp_enqueue_media();
			wp_enqueue_script( 'minshare-meta-admin', esc_url( $file_j ), array( 'jquery' ), $vers, true );
			wp_localize_script( 'minshare-meta-admin', 'minshareMeta', $localized );

			// And our action to hook on the end of the script loading.
			do_action( 'minshare_meta_after_admin_assets' );
		}
	}

	/**
	 * Add the button to convert other plugins.
	 *
	 * @return void
	 */
	public function add_convert_options() {

		// Bail if we have no keys to convert.
		if ( ! minshare_meta()->plugin_convert_keys() ) {
			return;
		}

		// Set the args.
		$args   = array(
			'minshare-meta-convert' => 1,
			'minshare-meta-nonce'   => wp_create_nonce( 'minshare-meta-nonce' ),
		);

		// Create our base link to trigger the conversion.
		$base   = add_query_arg( $args, menu_page_url( 'minshare-meta-settings', false ) );

		// First add a clean line.
		echo '<hr>';

		// Now a div to separate it all.
		echo '<div class="minshare-meta-convert-wrap">';

			// Add a header and an intro.
			echo '<h3>' . esc_html__( 'Convert Data', 'minimum-viable-sharing-meta' ) . '</h3>';
			echo '<p>' . esc_html__( 'Convert existing data from popular plugins like Yoast SEO and All In One. This will not delete any data from those plugins.', 'minimum-viable-sharing-meta' ) . '</p>';

			// Make our convert link buttons.
			echo '<p class="minshare-meta-convert-buttons-wrap">';

			// Now loop the convert key data.
			foreach ( minshare_meta()->plugin_convert_keys() as $key => $values ) {

				// Create our link to include which one we wanna convert.
				$link   = add_query_arg( 'minshare-meta-convert-source', $key, $base );

				// Set my label.
				$label  = sprintf( __( 'Convert From <strong>%s</strong>', 'minimum-viable-sharing-meta' ), esc_attr( $values['name'] ) );

				// And echo out the button.
				echo '<a class="button button-secondary minshare-meta-convert-button" href="' . esc_url( $link ) . '">' . $label . '</a>';
			}

			// Close the paragraph.
			echo '</p>';

		// Close our div.
		echo '</div>';
	}

	/**
	 * Add attribution link to settings page.
	 *
	 * @param  string $text  The existing footer text.
	 *
	 * @return string $text  The modified footer text.
	 */
	public function admin_footer_text( $text ) {

		// Run my screen check function first.
		if ( false === $screen = MinimumViableMeta_Helper::check_screen_base( 'appearance_page_minshare-meta-settings' ) ) {
			return $text;
		}

		// Set my GitHub text.
		$ghtext = sprintf( __( 'You can view this plugin and submit issues on <a target="_blank" href="%s">GitHub</a>.', 'minimum-viable-sharing-meta' ), esc_url( 'https://github.com/norcross/minimum-viable-sharing-meta' ) );

		// Set our footer link with GA campaign tracker.
		return $text . '&nbsp;&nbsp;<span id="footer-ghlink">' . $ghtext . '</span>';
	}

	// End our class.
}

// Call our class.
$MinimumViableMeta_Admin = new MinimumViableMeta_Admin();
$MinimumViableMeta_Admin->init();
