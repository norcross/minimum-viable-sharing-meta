<?php
/**
 * Plugin Name: Minimum Viable Sharing Meta
 * Plugin URI:  https://github.com/norcross/minimum-viable-sharing-meta
 * Description: Just the minimum required meta tags to work.
 * Version:     0.0.4
 * Author:      Andrew Norcross
 * Author URI:  http://andrewnorcross.com
 * Text Domain: minimum-viable-sharing-meta
 * Domain Path: /languages
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 *
 * @package MinimumViableMeta
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Call our class.
 */
final class MinimumViableMeta_Core {

	/**
	 * MinimumViableMeta_Core instance.
	 *
	 * @access private
	 * @since  1.0
	 * @var    MinimumViableMeta_Core The one true MinimumViableMeta_Core
	 */
	private static $instance;

	/**
	 * The version number of MinimumViableMeta_Core.
	 *
	 * @access private
	 * @since  1.0
	 * @var    string
	 */
	private $version = '0.0.5';

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return $instance
	 */
	public static function instance() {

		// Run the check to see if we have the instance yet.
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof MinimumViableMeta_Core ) ) {

			// Set our instance.
			self::$instance = new MinimumViableMeta_Core;

			// Set my plugin constants.
			self::$instance->setup_constants();

			// Run our version compare.
			if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {

				// Deactivate the plugin.
				deactivate_plugins( MINSHARE_META_BASE );

				// And display the notice.
				wp_die( sprintf( __( 'Your current version of PHP is below the minimum version required by the plugin. Please contact your host and request that your version be upgraded to 5.6 or later. <a href="%s">Click here</a> to return to the plugins page.', 'minimum-viable-sharing-meta' ), admin_url( '/plugins.php' ) ) );
			}

			// Set my file includes.
			self::$instance->includes();

			// Load our textdomain.
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		// And return the instance.
		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'minimum-viable-sharing-meta' ), '0.0.1' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0
	 * @access protected
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'minimum-viable-sharing-meta' ), '0.0.1' );
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {

		// Define our file base.
		if ( ! defined( 'MINSHARE_META_BASE' ) ) {
			define( 'MINSHARE_META_BASE', plugin_basename( __FILE__ ) );
		}

		// Set our base directory constant.
		if ( ! defined( 'MINSHARE_META_DIR' ) ) {
			define( 'MINSHARE_META_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'MINSHARE_META_URL' ) ) {
			define( 'MINSHARE_META_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin root file.
		if( ! defined( 'MINSHARE_META_FILE' ) ) {
			define( 'MINSHARE_META_FILE', __FILE__ );
		}

		// Set our includes directory constant.
		if ( ! defined( 'MINSHARE_META_INCLS' ) ) {
			define( 'MINSHARE_META_INCLS', __DIR__ . '/includes' );
		}

		// Set our assets directory constant.
		if ( ! defined( 'MINSHARE_META_ASSETS' ) ) {
			define( 'MINSHARE_META_ASSETS', __DIR__ . '/assets' );
		}

		// Set our assets directory constant.
		if ( ! defined( 'MINSHARE_META_ASSETS_URL' ) ) {
			define( 'MINSHARE_META_ASSETS_URL', MINSHARE_META_URL . 'assets' );
		}

		// Set what our options table key will be.
		if ( ! defined( 'MINSHARE_META_OPTIONKEY' ) ) {
			define( 'MINSHARE_META_OPTIONKEY', 'minshare_meta_defaults' );
		}

		// Set what our post meta key will be.
		if ( ! defined( 'MINSHARE_META_POSTKEY' ) ) {
			define( 'MINSHARE_META_POSTKEY', '_minshare_meta_single' );
		}

		// Set our version constant.
		if ( ! defined( 'MINSHARE_META_VERS' ) ) {
			define( 'MINSHARE_META_VERS', $this->version );
		}
	}

	/**
	 * Load our actual files in the places they belong.
	 *
	 * @return void
	 */
	public function includes() {

		// Load our various classes.
		require_once MINSHARE_META_INCLS . '/class-helper.php';

		// Load the classes that are only accessible via admin.
		if ( is_admin() ) {
			require_once MINSHARE_META_INCLS . '/class-admin.php';
			require_once MINSHARE_META_INCLS . '/class-fields.php';
			require_once MINSHARE_META_INCLS . '/class-settings.php';
			require_once MINSHARE_META_INCLS . '/class-post-meta.php';
		}

		// Handle our front-end only items.
		if ( ! is_admin() ) {
			require_once MINSHARE_META_INCLS . '/class-display.php';
		}

		// And our install script.
		require_once MINSHARE_META_INCLS . '/install.php';
		require_once MINSHARE_META_INCLS . '/uninstall.php';
	}

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function load_textdomain() {

		// Set filter for plugin's languages directory.
		$lang_dir = dirname( plugin_basename( MINSHARE_META_FILE ) ) . '/languages/';

		/**
		 * Filters the languages directory path to use for MinimumViableMeta.
		 *
		 * @param string $lang_dir The languages directory path.
		 */
		$lang_dir = apply_filters( 'minshare_meta_languages_dir', $lang_dir );

		// Traditional WordPress plugin locale filter.

		global $wp_version;

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {
			$get_locale = get_user_locale();
		}

		/**
		 * Defines the plugin language locale used in MinimumViableMeta.
		 *
		 * @var $get_locale The locale to use. Uses get_user_locale()` in WordPress 4.7 or greater,
		 *                  otherwise uses `get_locale()`.
		 */
		$locale = apply_filters( 'plugin_locale', $get_locale, 'minimum-viable-sharing-meta' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'minimum-viable-sharing-meta', $locale );

		// Setup paths to current locale file.
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/minimum-viable-sharing-meta/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/minimum-viable-sharing-meta/ folder
			load_textdomain( 'minimum-viable-sharing-meta', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/minimum-viable-sharing-meta/languages/ folder
			load_textdomain( 'minimum-viable-sharing-meta', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'minimum-viable-sharing-meta', false, $lang_dir );
		}
	}

	/**
	 * Handle our fancy meta key conversion.
	 *
	 * @param  string $source  The source plugin we want to convert.
	 *
	 * @return boolean
	 */
	public function convert_post_meta( $source = '', $allmeta = array() ) {

		// Bail without a source.
		if ( empty( $source ) ) {
			return false;
		}

		// Fetch my items related to this source.
		if ( false === $items = $this->plugin_convert_keys( 0, $source ) ) {
			return false;
		}

		// Remove the name key.
		unset( $items['name'] );

		// Call global DB class.
		global $wpdb;

		// Set our table.
		$table  = $wpdb->postmeta;

		// Confirm the table exists before running any updates.
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
			return;
		}

		// Now loop the items.
		foreach ( $items as $type => $metakey ) {

			// Prepare my query.
			$query  = $wpdb->prepare("
				SELECT post_id
				FROM $table
				WHERE meta_key = %s
			", esc_sql( $metakey ) );

			// Run SQL query.
			$theids = $wpdb->get_col( $query );

			// Bail if nothing comes back.
			if ( empty( $theids ) ) {
				continue;
			}

			// Loop my IDs.
			foreach ( $theids as $id ) {

				// Pull my value out.
				$value  = get_post_meta( $id, $metakey, true );

				// And now add our ID'd array.
				$allmeta[ $id ][ $type ] = $value;
			}
		}

		// Bail with no meta data.
		if ( empty( $allmeta ) ) {
			return false;
		}

		// Now, one more loop and updating some meta.
		foreach ( $allmeta as $post_id => $metadata ) {

			// Sanitize our data.
			$update = MinimumViableMeta_Helper::array_sanitize( $metadata );

			// Update the post meta.
			update_post_meta( $post_id, MINSHARE_META_POSTKEY, $update );
		}
	}

	/**
	 * Delete all the post meta related to the plugin.
	 *
	 * @return void
	 */
	public function delete_post_meta() {

		// Call global DB class.
		global $wpdb;

		// Set our table.
		$table  = $wpdb->postmeta;

		// Confirm the table exists before running any updates.
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) !== $table ) {
			return false;
		}

		// Prepare my query.
		$setup  = $wpdb->prepare("
			DELETE FROM $table
			WHERE meta_key = %s
		", esc_sql( MINSHARE_META_POSTKEY ) );

		// Run SQL query.
		$query = $wpdb->query( $setup );

		// And be done.
		return;
	}

	/**
	 * Sets our user role capability.
	 *
	 * @return string
	 */
	public function role_cap() {
		return apply_filters( 'minshare_meta_menu_item_cap', 'publish_posts' );
	}

	/**
	 * Sets our supported post types.
	 *
	 * @return string
	 */
	public function supported_types() {
		return apply_filters( 'minshare_meta_supported_types', array( 'post', 'page' ) );
	}

	/**
	 * Returns the maximum character limit for titles.
	 *
	 * @return integer
	 */
	public function max_title_length() {
		return apply_filters( 'minshare_meta_max_title_length', 50 );
	}

	/**
	 * Returns the maximum character limit for description.
	 *
	 * @return integer
	 */
	public function max_description_length() {
		return apply_filters( 'minshare_meta_max_description_length', 300 );
	}

	/**
	 * Returns the maximum character limit for one of the fields.
	 *
	 * @return integer
	 */
	public function max_field_length( $field = '' ) {

		// Handle my different field types.
		switch ( esc_attr( $field ) ) {

			case 'title' :
				return $this->max_title_length();
				break;

			case 'desc' :
				return $this->max_description_length();
				break;
		}

		// Include the action to send something else back.
		do_action( 'minshare_meta_max_field_length', $field );
	}

	/**
	 * Set the keys from the plugins we wanna convert.
	 *
	 * @param  boolean $keys    Whether we want the array keys or everything.
	 * @param  string  $source  A single source.
	 *
	 * @return array
	 */
	public function plugin_convert_keys( $keys = false, $source = '' ) {

		// Set my array of plugins and their keys.
		$items  = array(
			'yoast'     => array(
				'name'      => __( 'Yoast SEO', 'minimum-viable-sharing-meta' ),
				'title'     => '_yoast_wpseo_title',
				'desc'      => '_yoast_wpseo_metadesc',
				'canonical' => '_yoast_wpseo_canonical',
				'image'     => '_yoast_wpseo_opengraph-image',
			),
			'aioseo'    => array(
				'name'      => __( 'All In One SEO Pack', 'minimum-viable-sharing-meta' ),
				'title'     => '_aioseop_title',
				'desc'      => '_aioseop_description',
				'canonical' => '_aioseop_custom_link',
			),
			'genesis'   => array(
				'name'      => __( 'Genesis Theme Framework', 'minimum-viable-sharing-meta' ),
				'title'     => '_genesis_title',
				'desc'      => '_genesis_description',
				'canonical' => '_genesis_canonical_uri',
			),
		);

		// Return our setup.
		$items  = apply_filters( 'minshare_meta_plugin_convert_keys', $items );

		// We requested a single source.
		if ( ! empty( $source ) ) {
			return isset( $items[ $source ] ) ? $items[ $source ] : false;
		}

		// Return the keys, or the entire thing.
		return ! empty( $keys ) ? array_keys( $items ) : $items;
	}

	// End our class.
}

/**
 * The main function responsible for returning the one true MinimumViableMeta_Core
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $minshare_meta = minshare_meta(); ?>
 *
 * @since 1.0
 * @return MinimumViableMeta_Core The one true MinimumViableMeta_Core Instance
 */
function minshare_meta() {
	return MinimumViableMeta_Core::instance();
}
minshare_meta();
