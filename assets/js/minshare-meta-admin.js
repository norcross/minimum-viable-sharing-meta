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
// Handle our counting and updating for titles and desc.
//********************************************************
function updateFieldCount( fieldBlock, countBlock, fieldLimit ) {

	// Get my character count.
	var fieldCount = jQuery( fieldBlock ).val().length;

	// Update the field value.
	jQuery( countBlock ).text( fieldCount );

	// Add or remove the class.
	if ( parseInt( fieldCount, 10 ) > parseInt( fieldLimit, 10 ) ) {
		jQuery( countBlock ).addClass( 'current-count-over' );
	} else {
		jQuery( countBlock ).removeClass( 'current-count-over' );
	}
}

//********************************************************
// Now fire up the engines.
//********************************************************
jQuery(document).ready( function($) {

//********************************************************
// Quick helper to check for an existance of an element.
//********************************************************
	$.fn.divExists = function(callback) {

		// Slice some args.
		var args = [].slice.call( arguments, 1 );

		// Check for length.
		if ( this.length ) {
			callback.call( this, args );
		}

		// Return it.
		return this;
	};

//********************************************************
// Set some basic vars for later.
//********************************************************
	var maxTitle = minshareMeta.maxTitle;
	var maxDesc  = minshareMeta.maxDesc;

//********************************************************
// Handle character counts for titles and descriptions.
//********************************************************
	$( '.minshare-meta-data-input-wrap' ).divExists( function() {

		// Check for keyup() and fire accordingly.
		$( 'input#minshare-meta-title' ).keyup( function() {
			updateFieldCount( 'input#minshare-meta-title', 'span.current-title-count', maxTitle );
		});

		// Check for keyup() and fire accordingly.
		$( 'textarea#minshare-meta-desc' ).keyup( function() {
			updateFieldCount( 'textarea#minshare-meta-desc', 'span.current-desc-count', maxDesc );
		});

	});

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
