(function ($) {

	// Set global variables.
	var custom_uploader;

    // When the document is ready.
    $(document).ready(function() {
        // Hide the options meta box.
        $('#mumf-slider-options, #mumf-slider-help').addClass('closed');

        // Loop each image. 
        $('.mumf-slider img').each(function() {
            // Get the current image.
            var image = $(this);

            // If the source is blank, replace.
            if (image.attr('src') === '') {
                image.attr('src', '/wp-content/plugins/mumf-slider/assets/mumf-slider/themes/default/img/placeholder-image.png');
            }
            // END if.
        });
        // END loop.
    });

	// Listen for check box changing.
	$(document).on('change', '#mumf-slider-gallery-featured, #mumf-slider-gallery-show-navigation, #mumf-slider-gallery-navigation-thumbnails, #mumf-slider-gallery-hover-pause', function(){
	    // Toggle it's value depending on if it's checked or not.
	    $(this).val($(this).attr('checked') ? '1' : '0');
	});    

    // Listen for check box changing.
    $(document).on('change', '#mumf-slider-gallery-auto-rotate', function(){
        // Toggle it's value depending on if it's checked or not.
        $(this).val($(this).attr('checked') ? '1' : '0');
        // If the value is 1.
        if ($(this).val() === '1') {
            // Show the rotate delay form.
            $('#mumf-slider-gallery-rotate-delay').closest('tr').fadeIn('fast');
        // Otherwise.
        } else {
            // Hide the rotate delay form.
            $('#mumf-slider-gallery-rotate-delay').closest('tr').fadeOut('fast');
        }   
        // END if.     
    });        
  

 	/* This listener was modified from the following example: http://www.webmaster-source.com/2013/02/06/using-the-wordpress-3-5-media-uploader-in-your-plugin-or-theme/ */
 	// Listen for image input being clicked.
    $(document).on('click', '.mumf-slider-image-upload', function(e) {
 
 		// Prevent default operation.
        e.preventDefault();

        // Get the element clicked.
        var imageInput = $(this).next('input.mumf-slider-image-upload')
        ,   image = $(this);
 
        //If the uploader object has already been created, reopen the dialog.
        if (custom_uploader) {
            // Remove any previous listeners.
            custom_uploader.off('select');
	        // This function needs updated to use new imageInput.
	        custom_uploader.on('select', function() {
	            attachment = custom_uploader.state().get('selection').first().toJSON();
	            imageInput.val(attachment.url);
                // Change the source of the image.
                image.attr('src', attachment.url);
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

        // Listener for when an image is selected.
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            imageInput.val(attachment.url);
            // Change the source of the image.
            image.attr('src', attachment.url);
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
        // Replace the image src.
        parentRow.find('img').attr('src', '/wp-content/plugins/mumf-slider/assets/mumf-slider/themes/default/img/placeholder-image.png');
	});

}(jQuery));
