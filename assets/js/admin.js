(function ($) {

	// Set global variables.
	var custom_uploader;

	// Listen for check box changing.
	$(document).on('change', '#mumf-slider-gallery-featured', function(){
	    // Toggle it's value depending on if it's checked or not.
	    $(this).val($(this).attr('checked') ? '1' : '0');
	});    
 

 	/* This listener was modified from the following example: http://www.webmaster-source.com/2013/02/06/using-the-wordpress-3-5-media-uploader-in-your-plugin-or-theme/ */
 	// Listen for image input being clicked.
    $(document).on('click', '.mumf-slider-image-upload', function(e) {
 
 		// Prevent default operation.
        e.preventDefault();

        // Get the element clicked.
        var imageInput = $(this);
 
        //If the uploader object has already been created, reopen the dialog.
        if (custom_uploader) {
	        // This function needs updated to use new imageInput.
	        custom_uploader.on('select', function() {
	            attachment = custom_uploader.state().get('selection').first().toJSON();
	            imageInput.val(attachment.url);
	        });        	
            custom_uploader.open();
            // Return from function.
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image for Slide',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //Open the uploader dialog.
        custom_uploader.open();
    });

	// Listen for clear-slide button being clicked.
	$(document).on('click', '.mumf-slider-clear-slide', function(e) {
		// Get the clicked element, closest table row.
		var el = $(e.currentTarget)
		,   parentRow = el.closest('tr');

		// Remove the values of each input within the table row.
		parentRow.find('input').val('');
	});

}(jQuery));
