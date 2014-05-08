<?php
	/*
	Plugin Name: Mumfslider
	Plugin URI: http://www.github.com/bombadillo/mumf-slider-for-wordpress
	Description: a slider plugin.
	Version: 0.0.0
	Author: Chris Mumford
	Author URI: http://www.chrisandlaura.co.uk/chris
	License: GPL2
	*/

	// Initiate the plugin (get the script, styles, etc).
	function mumf_slider_initiate ($aSliderOptions) 
	{
		// Output some JavaScript so that we can define a global variable to hold our slider options.
		?>

		<script> mumfSliderOptions = {}; </script>

		<?php

		// Loop the slider options array.
		foreach ($aSliderOptions as $key => $value) 
		{
			?>

			<script> mumfSliderOptions.<?php echo $key ?> = <?php echo json_encode($value); ?></script>

			<?php
		}
		// END loop.

		// Register the plugin script. 
		wp_register_script( 'mumf-slider-script', plugins_url( 'assets/mumf-slider/js/mumf-slider.js', __FILE__ ));
		// Register the script to initiate sliders. 
		wp_register_script( 'mumf-slider-start-script', plugins_url( 'assets/js/start-slider.js', __FILE__ ));

		// Get the script.
		wp_enqueue_script('mumf-slider-script');
		// Get the script.
		wp_enqueue_script('mumf-slider-start-script');		


		// Register the style.
		wp_register_style('mumf-slider-styles', plugins_url('assets/mumf-slider/themes/default/css/styles.css', __FILE__ ));
		wp_enqueue_style('mumf-slider-styles');
		
	}
	/**************************************************************/

	// Called when plugin is activated.
	function mumfd_slider_activation()
	{
	    
	}
	/**************************************************************/

	// Called when plugin is deactivated.
	function mumf_slider_deactivation()
	{
	    
	}
	/**************************************************************/


	// Create custom post type for slider.
	function mumf_slider_create_post_type()
	{
		// Array holding custom post attributes.
	    $aArgs = array(
	        'description' => 'Slider Post Type',
	        'show_ui' => true,
	        'menu_icon' => 'dashicons-slides',
	        'menu_position' => 4,
	        'exclude_from_search' => true,
	        'labels' => array(
	            'name' => 'Sliders',
	            'singular_name' => 'Sliders',
	            'add_new' => 'Add New Slider',
	            'add_new_item' => 'Add New Slider',
	            'edit' => 'Edit Sliders',
	            'edit_item' => 'Edit Slider',
	            'new-item' => 'New Slider',
	            'view' => 'View Sliders',
	            'view_item' => null,
	            'search_items' => 'Search Sliders',
	            'not_found' => 'No Sliders Found',
	            'not_found_in_trash' => 'No Sliders Found in Trash',
	            'parent' => 'Parent Slider'
	        ),
	        'public' => true,
	        'capability_type' => 'post',
	        'hierarchical' => false,
	        'rewrite' => true,
	        'supports' => array(
	            'title'	            
	        )
	    );
	
		// Register the new post type.
	    register_post_type('mumf_slider', $aArgs);
	}
	/**************************************************************/

	// Display the images form.
	function mumf_view_slider_images_box()
	{
	    global $post;
	    // Count for the loop.
	    $iCount = 0;
	    
	    // Get the slider images from the post meta data.
	    $aSlides = get_post_meta($post->ID, "_mumf_gallery_images", true);

	    $aSlides = ($aSlides != '') ? json_decode($aSlides) : array();

	    // Use nonce for verification
	    $html = '<input type="hidden" name="mumf_slider_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';
	    
	    $html .= '';
	    $html .= "<table class=\"form-table mumf-slider\">
	             <tbody>
	             <tr><td><a href=\"javascript:void(0)\" class=\"button button-primary mumf-slider-add-slide\">Add Slide</a></td></tr>";


	    
	    // If there are slides.
	    if (count($aSlides) > 0) 
	    {	    	
			// Loop each of the slides.
		    foreach ($aSlides as $slide) 
		    {	    
		    	// Increment the count.
		    	$iCount++;

		        $html .= "<tr class=\"slide-header\">              
		                <td class=\"title\"><label for=\"Upload Images\">Slide ". $iCount ."</label></td>	                
		                <td class=\"buttons\"><a href=\"javascript:void(0)\" class=\"button mumf-slider-clear-slide\">Clear Slide</a>
		                <a href=\"javascript:void(0)\" class=\"button mumf-slider-delete-slide\">Delete Slide</a></td>      
		                </tr>
		                <tr>
		                <td class=\"image\">
		                	<img src=\"" . $slide->image . "\" alt=\"Slide Image\" class=\"mumf-slider-image-upload\" />
		                	<input class=\"mumf-slider-image-upload ghost\" type=\"text\" name=\"gallery_img[]\" value=\"" . $slide->image . "\" placeholder=\"The image url\" /></td>	           
		                <td><input type=\"text\" name=\"gallery_link[]\" value=\"" . $slide->link . "\" placeholder=\"The link url\" /></td>		                
		                </tr>";
		    }
		    // END loop.
		}
		// Otherwise there are no slides.
		else 
		{

		        $html .= "<tr class=\"slide-header\">              
		                <td class=\"title\"><label for=\"Upload Images\">Slide 1</label></td>	         
		                <td class=\"buttons\"><a href=\"javascript:void(0)\" class=\"button mumf-slider-clear-slide\">Clear Slide</a>    
		                <a href=\"javascript:void(0)\" class=\"button mumf-slider-delete-slide\">Delete Slide</a></td>       
		                </tr>
		                <tr>
		                <td class=\"image\">
		                	<img src=\"\" alt=\"Slide Image\" class=\"mumf-slider-image-upload\" />
		                	<input class=\"mumf-slider-image-upload ghost\" type=\"text\" name=\"gallery_img[]\" value=\"\" placeholder=\"The image url\" /></td>	           
		                <td><input type=\"text\" name=\"gallery_link[]\" value=\"\" placeholder=\"The link url\" /></td>
		                </tr>";			                		                		
		}

	    $html .= "  </tbody>
	                </table>";	    
	
	    echo $html;
	    
	}
	/**************************************************************/

	// Display the featured form.
	function mumf_view_slider_featured_box()
	{
		// Get global variable.
	    global $post;
	    
	    // Get the featured slider value from the post data.
	    $iFeatured = get_post_meta($post->ID, "_mumf_gallery_featured", true);

	    // Set value depending on $iFeatured value.
	    $sChecked = $iFeatured == 1 ? 'checked="checked"' : '';

	    $html .= "  
	                <table class=\"form-table\">
	                <tbody>
	                <tr>
	                <td><label for=\"mumf-slider-gallery-featured\">Set/Unset as featured slider on homepage.</label></td>
	                <td><input id=\"mumf-slider-gallery-featured\" type=\"checkbox\" name=\"gallery_featured\" value=\"". $iFeatured ."\" ". $sChecked ." /></td>
	                </tr>
	                </tbody>
	                </table>
	    ";
	    
	    echo $html;
	    
	}
	/**************************************************************/	

	// Display the slider options form.
	function mumf_view_slider_slider_options()
	{
		// Get global variable.
	    global $post;
	    
	    // Get the featured slider value from the post data, convert from JSON into object.
	    $aOptions = json_decode(get_post_meta($post->ID, "_mumf_gallery_options", true));

	    // If there are no options, it must be a new post so let's make default options.
	    if (!$aOptions) 
	    {
	    	$aOptions = new stdClass();
	        $aOptions->transition = 'fade-concurrent';
	        $aOptions->transitionSpeed = 500;
	        $aOptions->autoRotate = true;
	        $aOptions->rotateDelay = 4000;
	        $aOptions->showNavigation = true;
	        $aOptions->navigationThumbnails = true;
	        $aOptions->pauseOnHover = true;	    
	    }

		// Determine whether autoRotate checkbox should be checked.
		$sRotateChecked = $aOptions->autoRotate == 1 ? 'checked="checked"' : '';
		// Determine whether showNavigation checkbox should be checked.
		$sNavigationChecked = $aOptions->showNavigation == 1 ? 'checked="checked"' : '';
		// Determine whether navigationThumbnails checkbox should be checked.
		$sNavigationThumbnailChecked = $aOptions->navigationThumbnails == 1 ? 'checked="checked"' : '';		
		// Determine whether pauseOnHover checkbox should be checked.
		$sPauseOnHoverChecked = $aOptions->pauseOnHover == 1 ? 'checked="checked"' : '';						

	    $html .= "  
	                <table class=\"form-table\">
	                <tbody>
	                <tr>
	                <td><label for=\"mumf-slider-gallery-transition\">Transition Type</label></td>
	                <td>
	                	<select id=\"mumf-slider-gallery-transition\" name=\"gallery_transition\" >
							<option value=\"slide\" ". (($aOptions->transition == 'slide') ? 'selected' : '') .">Slide</option>
							<option value=\"fade\" ". (($aOptions->transition == 'fade') ? 'selected' : '') .">Fade</option>
							<option value=\"fade-concurrent\" ". (($aOptions->transition == 'fade-concurrent')? 'selected' : '') .">Fade concurrent</option>
	                	</select>
	                </td>
	                </tr>
	                <tr>
	                <td><label for=\"mumf-slider-gallery-transition-speed\">Slide Transition Speed (in seconds)</label></td>
	                <td><input id=\"mumf-slider-gallery-transition-speed\" type=\"text\" name=\"gallery_transition_speed\" value=\"". $aOptions->transitionSpeed / 1000 ."\" /></td>
	                </tr>
	                <tr>
	                <td><label for=\"mumf-slider-gallery-auto-rotate\">Auto Rotate Slides</label></td>
	                <td><input id=\"mumf-slider-gallery-auto-rotate\" type=\"checkbox\" name=\"gallery_auto_rotate\" value=\"". $aOptions->autoRotate ."\" ". $sRotateChecked ." /></td>
	                </tr>	     
	                <tr class=\"". (($aOptions->autoRotate) ? '' : 'ghost') ."\">
	                <td><label for=\"mumf-slider-gallery-rotate-delay\">Slide Rotate Delay (in seconds)</label></td>
	                <td><input id=\"mumf-slider-gallery-rotate-delay\" type=\"text\" name=\"gallery_rotate_delay\" value=\"". $aOptions->rotateDelay / 1000 ."\" /></td>
	                </tr>	  
	                <tr>
	                <td><label for=\"mumf-slider-gallery-show-navigation\">Show Slide Navigation</label></td>
	                <td><input id=\"mumf-slider-gallery-show-navigation\" type=\"checkbox\" name=\"gallery_show_navigation\" value=\"". $aOptions->showNavigation ."\" ". $sNavigationChecked ." /></td>
	                </tr>	 	
	                <tr>
	                <td><label for=\"mumf-slider-gallery-navigation-thumbnails\">Show Navigation Thumbnails</label></td>
	                <td><input id=\"mumf-slider-gallery-navigation-thumbnails\" type=\"checkbox\" name=\"gallery_navigation_thumbnails\" value=\"". $aOptions->navigationThumbnails ."\" ". $sNavigationThumbnailChecked ." /></td>
	                </tr>	
	                <tr>
	                <td><label for=\"mumf-slider-gallery-hover-pause\">Pause Rotation on Mouse Hover</label></td>
	                <td><input id=\"mumf-slider-gallery-hover-pause\" type=\"checkbox\" name=\"gallery_hover_pause\" value=\"". $aOptions->pauseOnHover ."\" ". $sPauseOnHoverChecked ." /></td>
	                </tr>		                	                                                         	                
	                </tbody>
	                </table>
	    ";

	    echo $html;
	    
	}
	/**************************************************************/	

	// Display a help section.
	function mumf_slider_help_section() 
	{
		// Get global variable.
	    global $post;
	    
	    $html .= '';
	    $html .= "  
	                <table class=\"form-table\">
	                <tbody>
	                <tr>
	                <td>To use this slider on a page, use the shortcode <code>[mumf_slider id=". $post->ID ." /]</code> and add to the page would want it to be displayed on.</td>	                
	                </tr>
	                </tbody>
	                </table>
	    ";
	    
	    echo $html;
	}
	/**************************************************************/	

	// Adds meta data boxes for mumf_slider post type to form.
	function mumf_slider_meta_box() 
	{
		add_meta_box("mumf-slider-help", "Tips", 'mumf_slider_help_section', "mumf_slider", "normal");
		add_meta_box("mumf-slider-options", "Options", 'mumf_view_slider_slider_options', "mumf_slider", "normal");
		add_meta_box("mumf-slider-featured-slider", "Featured Slider", 'mumf_view_slider_featured_box', "mumf_slider", "normal");		
	    add_meta_box("mumf-slider-images", "Slider Images", 'mumf_view_slider_images_box', "mumf_slider", "normal");	  	      

	}
	/**************************************************************/
	

	// Saves the image data to the post meta data.
	function mumf_save_slider_info($post_id) 
	{

	    // verify nonce
	    if (!wp_verify_nonce($_POST['mumf_slider_box_nonce'], basename(__FILE__))) 
	    {
	       return $post_id;
	    }

	    // check autosave
	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) 
	    {
	       return $post_id;
	    }

	    // check permissions
	    if ('mumf_slider' == $_POST['post_type'] && current_user_can('edit_post', $post_id)) 
	    {
			// Get the slider images.
			$aImages = (isset($_POST['gallery_img']) ? $_POST['gallery_img'] : '');
			// Get the slider links.
			$aLinks = (isset($_POST['gallery_link']) ? $_POST['gallery_link'] : '');	   
			// Get the featured checkbox value.
			$iFeatured = (isset($_POST['gallery_featured']) && $_POST['gallery_featured'] != '') ? $_POST['gallery_featured'] : 0;   		
			
			// Set array to hold options.
			$aOptions = array();
			// Set transition type.
			$aOptions['transition'] = (isset($_POST['gallery_transition']) ? $_POST['gallery_transition'] : 'slide');		
			// Set transition speed.
			$aOptions['transitionSpeed'] = (isset($_POST['gallery_transition_speed']) && $_POST['gallery_transition_speed'] != '' ) ? $_POST['gallery_transition_speed'] * 1000 : 250;		
			// Set rotate boolean.
			$aOptions['autoRotate'] = (isset($_POST['gallery_auto_rotate']) && $_POST['gallery_auto_rotate'] != '') ? $_POST['gallery_auto_rotate'] : 0;  		
			// Set rotate delay.
			$aOptions['rotateDelay'] = (isset($_POST['gallery_rotate_delay']) && $_POST['gallery_rotate_delay'] != '' ) ? $_POST['gallery_rotate_delay'] * 1000 : 4000;	 					
			// Set show navigation value.
			$aOptions['showNavigation'] = (isset($_POST['gallery_show_navigation']) && $_POST['gallery_show_navigation'] != '') ? $_POST['gallery_show_navigation'] : 0;  
			// Set navigation thumbnails value.
			$aOptions['navigationThumbnails'] = (isset($_POST['gallery_navigation_thumbnails']) && $_POST['gallery_navigation_thumbnails'] != '') ? $_POST['gallery_navigation_thumbnails'] : 0;  
			// Set pause on hover value.
			$aOptions['pauseOnHover'] = (isset($_POST['gallery_hover_pause']) && $_POST['gallery_hover_pause'] != '') ? $_POST['gallery_hover_pause'] : 0;  			

			// Convert options to JSON.
			$sOptions = json_encode($aOptions);

			// Create new array to hold combined data.
			$aSlides = array();
			// Loop through each gallery image.
			foreach($aImages as $key => $val)
			{
				// If the image field is not empty.
				if ($aImages[$key] != '')
				{					
					// Create new index with array containing image and link.
					$aSlides[$key] = array('image' => $aImages[$key], 'link' => $aLinks[$key]);
				}
			}

			$aSlides = strip_tags(json_encode($aSlides));


			// If $iFeatured is 1.
			if ($iFeatured == 1) 
			{
				// Get all posts that have the featured value of 1 (there only should be 1).
				$oFeaturedQuery = new WP_Query( "post_type=mumf_slider&meta_key=_mumf_gallery_featured&meta_value=1" ); 

				// If there are any posts.
				if ($oFeaturedQuery->post_count > 0) 
				{
					// Loop the posts array.
					foreach ($oFeaturedQuery->posts as $post) 
					{
							// Update the post meta data for the featured slider value.
							update_post_meta($post->ID, "_mumf_gallery_featured", 0);	
					}
					// END loop.
				}
				// END if.				
			}
			// END if.

			// Update the post meta data for the slider images.
			update_post_meta($post_id, "_mumf_gallery_images", $aSlides);
			// Update the post meta data for the featured slider value.
			update_post_meta($post_id, "_mumf_gallery_featured", $iFeatured);	 
			// Update the post meta data for the slider options.
			update_post_meta($post_id, "_mumf_gallery_options", $sOptions);	

	    } else {

	       return $post_id;

	    }

	}
	/**************************************************************/

	// Function to display the slider.
	function mumf_slider_display_slider($attr,$content) 
	{

		// Import variables.
	    extract(shortcode_atts(array('id' => ''), $attr));

		// If there's no ID.
		if (!$id) 
		{
			// Get all posts that have the featured value of 1 (there only should be 1).
			$oFeaturedQuery = new WP_Query( "post_type=mumf_slider&meta_key=_mumf_gallery_featured&meta_value=1" ); 
			// Get the first post in the array.
			$id = $oFeaturedQuery->posts[0]->ID;
		}
		// END if.
		
		// Get the images from the post meta data using the id.
		$gallery_images = get_post_meta($id, "_mumf_gallery_images", true);

		if (!isset($id)) 
		{
			return;
		}

		// Decode the JSON into an array if it is not an empty string.
		$gallery_images = ($gallery_images != '') ? json_decode($gallery_images) : array();

		$plugins_url = plugins_url();

		// Initial container html.
		$html = '<div class="mumf-slider">
		         	<ul>';

		// Loop each of the images.
		foreach ($gallery_images as $gal_img) 
		{			
			// If the image is not an empty string.
			if($gal_img->image != ""){

				$html .= '<li class="slide">';

				// If there's a link.
				$html .= ($gal_img->link != '') ? '<a href="'. $gal_img->link .'">' : '<div>' ;						

				$html .= "<img alt=\"\" src=\"".$gal_img->image."\" />";
                $html .= ($gal_img->link != '') ? '</a>' : '</div>';
                $html .= '</li>'; 					
			}
			// END if.
		}  
		// END loop.

		// END containing ul.
		$html .= '</ul>';                        
             

		// Thumbnail container html.
		$html .= '<div class="thumbnails">
		         	<ul>';			

		// Loop each of the images.
		foreach ($gallery_images as $gal_img) {
			// If the image is not an empty string.
			if($gal_img->image != ""){

				$html .= '<li>
                        	<div>';
				$html .= "<img alt=\"\" src=\"".$gal_img->image."\" />";
                $html .= '  </div>
                          </li>'; 					
			}
			// END if.
		}  
		// END loop.

		// END containing ul and divs.
		$html .= '</ul>
		       </div>
		    </div>';  				         	

		// Get slider options from post meta, converting it from JSON into object.		
		$aOptions = json_decode(get_post_meta($id, "_mumf_gallery_options", true));		    

		// Perform action, passing the options array.
		do_action('mumf_slider_initiate', $aOptions);

	    return $html;

	}
	/**************************************************************/


	// Function to remove row actions if the post type is mumf_slider.
	function mumf_slider_remove_row_actions( $actions, $post )
	{
		// Get global variable.
		global $current_screen;

		// If the current post type is not mumf_slider then return from function.
		if( $current_screen->post_type != 'mumf_slider' ) return $actions;		

		// Unset the view and JS actions.
		unset( $actions['view'] );
		unset( $actions['inline hide-if-no-js'] );

		// Return the array.
		return $actions;
	}
	/**************************************************************/

	// Function to load the admin js script.
	function mumf_slider_load_admin_scripts($hook) 
	{
		// Get global variable.
		global $post;

	    if( 'mumf_slider' != $post->post_type || !preg_match('/post-new.php|post.php/', $hook) )
	        return;

		// Get the styles.
		wp_register_style('mumf-slider-admin-styles', plugins_url('/assets/css/admin.css', __FILE__ ));
		wp_enqueue_style('mumf-slider-admin-styles');

	    // Register script to handle admin section.
		wp_register_script( 'mumf-slider-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ));
		// Load script.
		wp_enqueue_script('mumf-slider-admin-script');	 
		// Load media scripts/styles.
		wp_enqueue_media();		
	}
	/**************************************************************/


	// Call function when admin page is loading scripts.
	add_action('admin_enqueue_scripts', 'mumf_slider_load_admin_scripts');

	// Add filter to remove row actions from custom post type.
	add_filter('post_row_actions', 'mumf_slider_remove_row_actions', 10, 2);

	// Add the shortcode which will be used to display our slider.
	add_shortcode("mumf_slider", "mumf_slider_display_slider");

	// call function to initiate javscript on slider.
	add_action('mumf_slider_initiate', 'mumf_slider_initiate');

	// Call function to save images on post save.
	add_action('save_post', 'mumf_save_slider_info');

	// Call function to add post meta data.
	add_action('add_meta_boxes', 'mumf_slider_meta_box');

	// Create custom post type after init.
	add_action('init', 'mumf_slider_create_post_type');

	// Register the activation function.
	register_activation_hook(__FILE__, 'mumfd_slider_activation');

	// Register the deactivation function.
	register_deactivation_hook(__FILE__, 'mumf_slider_deactivation');



?>
