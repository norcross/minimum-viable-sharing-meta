//********************************************************
// Setup the file loader.
//********************************************************
function imageUploader( event, settingBlock, settingField, imgPreview ) {

	var file_frame;

	event.preventDefault();

	// If the media frame already exists, reopen it.
	if ( file_frame ) {
		file_frame.open();
		return;
	}

	// Create the media frame.
	file_frame = wp.media.frames.file_frame = wp.media({
		multiple: false
	});

	// run the callback when selected
	file_frame.on( 'select', function() {

		// make sure to only deal with the first item
		attachment = file_frame.state().get( 'selection' ).first().toJSON();

		// run check on MIME type to ensure image file
		if ( attachment.type !== 'image' ) {
			return false;
		}

		// Populate the field with the URL
		jQuery( settingBlock ).find( settingField ).val( attachment.url );

		// and populate our image preview if called
		if ( imgPreview === true ) {
			uploadPreview( attachment.url );
		}

	});

	// Finally, open the modal
	file_frame.open();
}

//********************************************************
// Now fire up the engines.
//********************************************************
jQuery(document).ready( function($) {


	//********************************************************
	// Trigger the file upload on button.
	//********************************************************
	$( 'tr.minshare-meta-image-field' ).on( 'click', '.upload-button', function( event ) {
		imageUploader( event, 'tr.minshare-meta-image-field td', 'input#minshare-meta-image', false );
	});

//********************************************************
// You're still here? It's over. Go home.
//********************************************************
});
