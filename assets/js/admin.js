(function ($) {

	// Listen for check box changing.
	$(document).on('change', '#mumf-slider-gallery-featured', function(){
	    // Toggle it's value depending on if it's checked or not.
	    $(this).val($(this).attr('checked') ? '1' : '0');
	});

}(jQuery));
