<?php
/**
 * Our front end tag setup.
 *
 * @package MinimumViableMeta
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Start our engines.
 */
class MinimumViableMeta_Display {

	/**
	 * Call our hooks.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_head',                      array( $this, 'load_meta_tags'          )           );
		add_filter( 'document_title_parts',         array( $this, 'load_title_tag'          ),  88      );
	}

	/**
	 * Load our meta tags.
	 *
	 * @return mixed
	 */
	public function load_meta_tags() {

		// Our before action for everything.
		do_action( 'minshare_meta_before_tag_output' );

		// Handle front and blog pages.
		if ( is_home() || is_front_page() || is_singular( minshare_meta()->supported_types() ) ) {

			// Figure out if I have a post ID.
			$the_id = is_singular() ? get_the_ID() : 0;

			// Get the canonical link.
			$link   = is_singular() ? wp_get_canonical_url( $the_id ) : home_url( '/' );

			// Fetch our tags.
			$tags	= MinimumViableMeta_Helper::get_single_tags( $the_id );

			// First check for a featured image if none was set.
			if ( is_singular() && empty( $tags['image'] ) ) {
				$tags['image']  = get_the_post_thumbnail_url( $the_id, 'medium' );
			}

			// Loop my tags and output as appropriate.
			foreach ( $tags as $type => $value ) {

				// Skip if the value is empty.
				if ( empty( $value ) ) {
					continue;
				}

				// Handle my different tag types.
				switch ( esc_attr( $type ) ) {

					case 'title' :
						echo '<meta property="og:title" content="' . esc_attr( $value ) . '" />' . "\n";
						break;

					case 'desc' :
						echo '<meta name="description" content="' . esc_attr( $value ) . '" />' . "\n";
						echo '<meta property="og:description" content="' . esc_attr( $value ) . '" />' . "\n";
						break;

					case 'image' :
						echo '<meta property="og:image" content="' . esc_url( $value ) . '" />' . "\n";
						break;

					case 'card' :
						echo '<meta name="twitter:card" content="' . esc_attr( $value ) . '" />' . "\n";
						break;
				}

				// End the case switches.
			}

			// Now output the URL.
			echo '<meta property="og:url" content="' . esc_url( $link ) . '" />' . "\n";
		}

		// Our after action for everything.
		do_action( 'minshare_meta_after_tag_output' );
	}

	/**
	 * Filter our title text.
	 *
	 * @param  string $title  Our current page title
	 *
	 * @return string
	 */
	public function load_title_tag( $title ) {

		// Don't mess with the RSS feed.
		if ( is_feed() ) {
			return $title;
		}

		// Handle front, blog, and singular items.
		if ( is_home() || is_front_page() || is_singular( minshare_meta()->supported_types() ) ) {

			// Figure out if I have a post ID.
			$the_id = is_singular() ? get_the_ID() : 0;

			// And set the title.
			$title['title'] = MinimumViableMeta_Helper::get_single_tags( $the_id, 'title' );
		}

		// Return our original item.
		return $title;
	}

	// End our class.
}

// Call our class.
$MinimumViableMeta_Display = new MinimumViableMeta_Display();
$MinimumViableMeta_Display->init();
