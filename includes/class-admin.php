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
		add_action( 'admin_enqueue_scripts',        array( $this, 'load_admin_assets'       ),  10      );
		add_filter( 'admin_footer_text',            array( $this, 'admin_footer_text'            )           );
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
			$file_c = MINSHARE_META_ASSETS_URL . '/css/' . $name . '.css';
			$file_j = MINSHARE_META_ASSETS_URL . '/css/' . $name . '.css';

			// Load our CSS file.
			wp_enqueue_style( 'minshare-meta-admin', esc_url( $file_c ), false, $vers, 'all' );

			// And our JS.
			wp_enqueue_media();
			wp_enqueue_script( 'minshare-meta-admin', esc_url( $file_j ), array( 'jquery' ), $vers, true );
			wp_localize_script( 'minshare-meta-admin', 'minshareMeta', array(
				'maxTitle'  => minshare_meta()->max_title_length(),
				'maxDesc'   => minshare_meta()->max_description_length(),
			));

			// And our action to hook on the end of the script loading.
			do_action( 'minshare_meta_after_admin_assets' );
		}
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
