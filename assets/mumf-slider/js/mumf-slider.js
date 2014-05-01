(function ($) {

    $.fn.mumfSlider = function (options) {

        // Iterate each slider.
        return this.each(function() {
            // Get the current element.
            var elem = $( this );   

            // Extend our default options with those provided.
            // Note that the first argument to extend is an empty
            // object – this is to keep from overriding our "defaults" object.
            elem.mumfSlider = $.extend({}, $.fn.mumfSlider.defaults, options);            

            // Add class.
            elem.addClass('mumf-slider');

            // Check to see if thumbnails exist.
            if (elem.find('.thumbnails').length > 0) { 
                // Call function to set them up.
                $.fn.mumfSlider.setupThumbnails(elem);
            // Otherwise check if navigationThumbnails has been set.
            } else if (elem.mumfSlider.navigationThumbnails) {
                // Call function to add default navigation thumbs.
                $.fn.mumfSlider.addDefaultThumbnails(elem);
            }
            // END if.

            // Add next/prev buttons if navigation is on.
            if (elem.mumfSlider.showNavigation) $.fn.mumfSlider.addNavButtons(elem);            

            // Setup slides depending on transition type.
            $.fn.mumfSlider.setupSlideType(elem);           

            // If autoRotate is set to true, call function to rotate slider.
            if (elem.mumfSlider.autoRotate) $.fn.mumfSlider.autoRotate(elem);   

            // Call function to add event listeners.        
            $.fn.mumfSlider.addEventListeners(elem);        

            // Set to the first slide.
            elem.mumfSlider.nextSlide = elem.find('li.active').length > 0 ? elem.find('li.active') : elem.find('li:first');

            // Call function to transition slide.
            $.fn.mumfSlider.transitionSlide(elem);          

        });     

    };

    /* Name      addNavButtons
     * Purpose   Adds navigation buttons to the slider.
     * Params    slider      The slider to change the slide for.     
    */   
    $.fn.mumfSlider.addNavButtons = function (slider) {
        // Append next/prev buttons to slider.
        slider.append(slider.mumfSlider.navButtonsHtml);
    };

    /* Name      addDefaultThumbnails
     * Purpose   Adds default thumbnail buttons to the slider.
     * Params    slider      The slider to change the slide for.     
    */   
    $.fn.mumfSlider.addDefaultThumbnails = function (slider) {
        
        // Append thumbnails element to slider.
        slider.append('<div class="thumbnails"><ul></ul></div>');

        // Set thumbnail element to variable.
        var elThumbnails = slider.find('.thumbnails ul');

        // Loop each of the slides.
        slider.find('ul:first li').each(function() {
            // Append a list item.
            elThumbnails.append('<li class="default"><span class="nav-pill"></span></li>');
        });

    };
    

    /* Name      addEventListeners
     * Purpose   Adds event listeners to the slider.
     * Params    slider      The slider to change the slide for.     
    */   
    $.fn.mumfSlider.addEventListeners = function (slider) {
        // Add listener for direction button click.
        slider.on('click', '.direction', function() {
            // Get the direction.
            var direction = $(this).data('direction');
            // Call function to change slide.
            $.fn.mumfSlider.changeSlide(slider, direction);
        });

        // Add listener for thumbnail click.
        slider.on('click', '.thumbnails li', function () {
            // Call function to change slide based on thumbnail.
            $.fn.mumfSlider.onThumbClick(slider, $(this));
        });

        // Add listener for custom resize event.
        $(window).on('resizeEnd', function () {      
    
            // Call function to animate to current slide.
            switch (slider.mumfSlider.transition) {
                case 'slide':
                    // Call function to fade next slide.
                    $.fn.mumfSlider.slideNextSlide(slider);
                    // Call function to set active thumbnail.
                    $.fn.mumfSlider.setActiveThumbnail(slider);                        
                    break;              
            }
            // END switch.            
        });

        // Add listener for slider resizing.
        $(window).on('resize', function () {
            // Clear the timeout if resizeTO exists.
            if($.fn.mumfSlider.resizeTO) clearTimeout($.fn.mumfSlider.resizeTO);

            // Set the timeout.
            $.fn.mumfSlider.resizeTO = setTimeout(function() {
                // Trigger after 500 ms.
                $(window).trigger('resizeEnd');
            }, 100);
        });

    };

    /* Name      resizeDelay
     * Purpose   Performs function which passes back a ms time.     
    */   
    $.fn.mumfSlider.resizeDelay = function () {
        var timer = 0;
        return function(callback, ms) {
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };

    };

    /* Name      onThumbClick
     * Purpose   To change the slide based on the index of the thumbnail.
     * Params    slider      The slider to change the slide for.     
     *           thumb       The element of the thumbnail clicked.
    */  
    $.fn.mumfSlider.onThumbClick = function (slider, thumb) {
        // Set global variables to defaults.                        
        slider.mumfSlider.isFirstSlide = false;
        slider.mumfSlider.isLastSlide = false;

        // Get the index, use index to get the slide.
        var index = thumb.index() + 1
        ,   nextSlide = slider.find('ul:first li:nth-child('+ index +')');
        
        // Set the next slide to the slider.        
        slider.mumfSlider.nextSlide = nextSlide;  


        // If autoRotate is set.
        if (slider.mumfSlider.autoRotate) {            
            // Clear the autoRotate interval.
            clearInterval(slider.autoRotateInterval);
            // Call function to restart.
            $.fn.mumfSlider.autoRotate(slider);
        }
        // END if autoRotate.

        // Call function to transition slide.
        $.fn.mumfSlider.transitionSlide(slider);      
    };

    /* Name      setupSlideType
     * Purpose   Sets up each slide depending on the slide transition type.
     * Params    slider      The slider to change the slide for.     
    */  
    $.fn.mumfSlider.setupSlideType = function (slider) {

        // Switch the transition type.
        switch (slider.mumfSlider.transition) {
            // Slide type.
            case 'slide': 
                // Set ul CSS attributes.
                slider.find('ul:first').css({ 'overflow': 'hidden', 'white-space': 'nowrap' });
                // Set li attributes. 
                slider.find('li.slide').css({ 'width': '100%', 'display': 'inline-block' });            
                break;

            // Concurrent fade type.
            case 'fade-concurrent': 
                // Add the class so that the CSS classes kick in.
                slider.addClass('concurrent-fade');
                break;
        }
    };

    /* Name      setupThumbnails
     * Purpose   Sets up the thumbnails for the slider.
     * Params    slider      The slider to change the thumbnails for.     
    */  
    $.fn.mumfSlider.setupThumbnails = function (slider) {
        // Get the number of thumbnails, set width.
        var numThumbs = slider.find('.thumbnails li').length
        ,   width = (100 / numThumbs) - 3;

        // Set the width of each thumbnail to 100 / numThumbs.
        slider.find('.thumbnails li').css('width', width + '%');
    };

    /* Name      autoRotate
     * Purpose   Sets a timeout to operate every x milliseconds. 
     * Params    slider      The slider to change the slide for.     
    */          
    $.fn.mumfSlider.autoRotate = function (slider) {
        // Set an interval.
        slider.autoRotateInterval = setInterval(function() {
            // Call function to change slide.
            $.fn.mumfSlider.changeSlide(slider, 'next', true);          
        }, slider.mumfSlider.rotateDelay);

    };

    /* Name      changeSlide
     * Purpose   To change the slide depending on the direction.
     * Params    slider       The slider to change the slide for.
     *           direction    The direction to determine which slide to transition to.
     *           isAutoRotate Boolean of wheter is is an auto rotate change or not.
    */ 
    $.fn.mumfSlider.changeSlide = function (slider, direction, isAutoRotate) {

        // If it's an auto rotate change.
        if (isAutoRotate && slider.mumfSlider.pauseOnHover) {
            // Check to see if slider is currently being hovered over.
            if (slider.is(':hover')) return false; 
        }

        // Get the current slide.
        var currentSlide = slider.find('ul:first li.active');

        // Set global variables to defaults.                        
        slider.mumfSlider.isFirstSlide = false;
        slider.mumfSlider.isLastSlide = false;

        // If there is no current slide.
        if (!currentSlide.length) {
            // Set to the first slide.
            slider.mumfSlider.nextSlide = slider.find('ul:first li:first');
        }
        // Otherwise there is a slide.
        else {      
            // Set the next slide to transtion to.
            slider.mumfSlider.nextSlide = direction === 'previous' ? currentSlide.prev() : currentSlide.next();     

            // If there is no next.
            if (direction === 'next' && !slider.mumfSlider.nextSlide.length) {
                // Set to the first slide.
                slider.mumfSlider.nextSlide = slider.find('ul:first li:first');
                // Set global variable.
                slider.mumfSlider.isFirstSlide = true;
            }
            // If there's no previous.
            if (direction === 'previous' && !slider.mumfSlider.nextSlide.length) {
                // Set to the first slide.
                slider.mumfSlider.nextSlide = slider.find('ul:first li:last');
                // Set global variable.
                slider.mumfSlider.isLastSlide = true;
            }
            // END if.          
        }
        // END if current slide.

        // Call function to transition slide.
        $.fn.mumfSlider.transitionSlide(slider);

    };


    /* Name      transitionSlide
     * Purpose   To check the transition type and call the appropriate function
     * Params    slider      The slider to change the slide for.
    */ 
    $.fn.mumfSlider.transitionSlide = function (slider) {

        // If the slider is currently being hovered over.


        // Switch the transition option.
        switch (slider.mumfSlider.transition) {
            case 'fade':
                // Call function to fade next slide.
                $.fn.mumfSlider.fadeNextSlide(slider);
                break;
            case 'fade-concurrent': 
                // Call function to fade next slide concurrently.
                $.fn.mumfSlider.fadeNextSlideConcurrent(slider);
                break;
            case 'slide':
                // Call function to fade next slide.
                $.fn.mumfSlider.slideNextSlide(slider);
                break;              
        }
        // END switch.    

        // Call function to set active thumbnail.
        $.fn.mumfSlider.setActiveThumbnail(slider);          
    };
 
    /* Name      fadeNextSlide
     * Purpose   To fade in the next slide.
     * Params    slider      The slider to change the slide for.
    */ 
    $.fn.mumfSlider.fadeNextSlide = function (slider) {
        // Get the current active.
        var currentActive = slider.find('ul:first li.active');

        // Set the height of the container to the current height.
        slider.find('ul:first').height(slider.find('li.active').height() +'px');

        // Remove all instances of active class.
        slider.find('li.active').removeClass('active');

        // If there's a current active.
        if (slider.mumfSlider.loaded) {
            // Fade out the current active slide.
            currentActive.fadeOut(slider.mumfSlider.transitionSpeed, function() {
                // Fade in the slide and add active class.
                slider.mumfSlider.nextSlide.fadeIn(slider.mumfSlider.transitionSpeed)
                                .addClass('active');
                // Call function to resize slider container.
                $.fn.mumfSlider.resizeSliderContainer(slider);                                
            });               
        } else {
            // Fade in the slide and add active class.
            slider.mumfSlider.nextSlide.css('display', 'block')
                             .addClass('active');
            // Call function to resize slider container.
            $.fn.mumfSlider.resizeSliderContainer(slider);                                 
        }
        // END if.
        
    };

    /* Name      fadeNextSlideConcurrent
     * Purpose   To fade in the next slide concurrently whilst fading out current slide.
     * Params    slider      The slider to change the slide for.
    */ 
    $.fn.mumfSlider.fadeNextSlideConcurrent = function (slider) {
        // Get the current active.
        var currentActive = slider.find('ul:first li.active');

        // Set the height of the container to the current height.
        slider.find('ul:first').height(slider.find('li.active').height() +'px');

        // Remove all instances of active class.
        slider.find('li.active').removeClass('active');

        // If there's a current active.
        if (slider.mumfSlider.loaded) {

            // Fade out the current active slide.
            currentActive.fadeOut(slider.mumfSlider.transitionSpeed);

            // Fade in the slide and add active class.
            slider.mumfSlider.nextSlide.fadeIn(slider.mumfSlider.transitionSpeed)
                            .addClass('active');  
            // Call function to resize slider container.
            $.fn.mumfSlider.resizeSliderContainer(slider);                                                                            
        } else {

            // Fade in the slide and add active class.
            slider.mumfSlider.nextSlide.addClass('active');

            // Set a timeout to ensure all elements have been sized.
            setTimeout(function () {
                // Call function to resize slider container.
                $.fn.mumfSlider.resizeSliderContainer(slider); 
            }, 500);            
                      
        }
        // END if.
        
    };      

    /* Name      slideNextSlide
     * Purpose   To slide in the next slide.
     * Params    slider      The slider to change the slide for.
    */ 
    $.fn.mumfSlider.slideNextSlide = function (slider) {   

        // If the slider has not loaded.
        if (!slider.mumfSlider.loaded) 
        {
            // Slide down the element.
            slider.find('ul:first').slideDown(200);
        }

        // Get the current active, index of current slide, scrollDistance for distance to scroll to.
        var currentActive = slider.find('ul:first li.active')
        ,   nextIndex = slider.mumfSlider.nextSlide.index()
        ,   scrollDistance = undefined;

        // Remove all instances of active class.
        slider.find('li.active').removeClass('active');

        // Stop any animations.
        slider.find('ul:first').stop();

        // If it's the first slide.
        if (slider.mumfSlider.isFirstSlide || nextIndex === 0) {
            // Animate the scroll to the first slide.
            slider.find('ul:first').animate({scrollLeft: 0}, slider.mumfSlider.transitionSpeed);
        }
        else {            
            // Get the width of a li.slide and multiply by next index.
            scrollDistance = slider.find('ul:first').width() * nextIndex;       
            // Animate the scroll using scrollDistance.
            slider.find('ul:first').animate({ scrollLeft: scrollDistance }, slider.mumfSlider.transitionSpeed);    
        }

        // Add the active class.        
        slider.mumfSlider.nextSlide.addClass('active');               

        // Call function to resize slider container.
        $.fn.mumfSlider.resizeSliderContainer(slider); 
    };

    /* Name      setActiveThumbnail
     * Purpose   To set the active thumbnail
     * Params    slider      The slider to change the slide for.
    */ 
    $.fn.mumfSlider.setActiveThumbnail = function (slider) {
        // Get the index of the next slide, get the thumbnail using index.
        var index = slider.mumfSlider.nextSlide.index() + 1
        ,   thumb = slider.find('.thumbnails li:nth-child('+ index +')')

        // Add the active class to the thumbnail.
        thumb.addClass('active');
    };


    /* Name      resizeSliderContainer
     * Purpose   To resize the slider container to the new slide height.
     * Params    slider    The slider to change the height of.
    */ 
    $.fn.mumfSlider.resizeSliderContainer = function (slider) {
        // If the slider has not loaded.
        if (!slider.mumfSlider.loaded)
        {
            // Slide down the element.
            slider.find('ul:first').slideDown(200);
        }

        // Get the height of the current slide.
        var height = slider.find('ul:first li.active').height();
        // Animate the slider container to the height.
        slider.find('ul:first').animate({height: height +'px'});

        slider.loaded = true;
    };

    /* Name      actualHeight
     * Purpose   To get the actual height of an element.
     * Params    element    The element to find the height of.
    */ 
    $.fn.mumfSlider.actualHeight = function (element) {
        // find the closest visible parent and get it's hidden children
        var visibleParent = element.closest(':visible').children(),
            thisHeight;

        // set a temporary class on the hidden parent of the element
        visibleParent.addClass('temp-show');

        // get the height
        thisHeight = element.width();

        // remove the temporary class
        visibleParent.removeClass('temp-show');

        return thisHeight;
     };    

    // Plugin defaults – added as a property on our plugin function.
    $.fn.mumfSlider.defaults = {
        theme: 'default',
        transition: 'fade',
        transitionSpeed: 500,
        autoRotate: true,
        rotateDelay: 4000,
        showNavigation: true,
        navButtonsHtml: '<div class="next direction" data-direction="next"></div><div class="prev direction" data-direction="previous"></div>',
        navigationThumbnails: true,
        pauseOnHover: true     
    };    


}(jQuery));

