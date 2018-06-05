<?php

/**
 * Our inital setup function when activated.
 *
 * @return void
 */
function minshare_meta_install() {

	// Set our initial default values.
	$setup  = array(
		'title'     => get_bloginfo( 'name' ),
		'desc'      => get_bloginfo( 'description' ),
		'card'      => 'summary',
		'image'     => '',
		'canonical' => '',
	);

	// Filter the initial values.
	$setup  = apply_filters( 'minshare_meta_default_values', $setup );

	// Now store the data.
	update_option( MINSHARE_META_OPTIONKEY, $setup, 'no' );

	// Allow for others to hook in.
	do_action( 'minshare_meta_install_process' );
}
register_activation_hook( MINSHARE_META_FILE, 'minshare_meta_install' );
