<?php

/**
 * Delete various options when uninstalling the plugin.
 *
 * @return void
 */
function minshare_meta_uninstall() {

	// First, delete my default settings.
	delete_option( MINSHARE_META_OPTIONKEY );

	// This will be the function to delete any individual post meta.
	minshare_meta()->delete_post_meta();

	// Include our action so that we may add to this later.
	do_action( 'minshare_meta_uninstall_process' );
}
register_uninstall_hook( MINSHARE_META_FILE, 'minshare_meta_uninstall' );

