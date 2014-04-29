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
	function mumf_slider_initiate () {

		// Get the script. 
		wp_register_script( 'mumf-slider-script', plugins_url( 'assets/mumf-slider/js/mumf-slider.js', __FILE__ ));
		// Get the script to initiate sliders. 
		wp_register_script( 'mumf-slider-start-script', plugins_url( 'assets/js/start-slider.js', __FILE__ ));

		wp_enqueue_script('mumf-slider-script');
		wp_enqueue_script('mumf-slider-start-script');		


		// Get the styles.
		wp_register_style('mumf-slider-styles', plugins_url('assets/mumf-slider/themes/gayles/css/styles.css', __FILE__ ));
		wp_enqueue_style('mumf-slider-styles');
		
	}

	// Called when plugin is activated.
	function mumfd_slider_activation()
	{
	    
	}

	// Called when plugin is deactivated.
	function mumf_slider_deactivation()
	{
	    
	}


	// Create custom post type for slider.
	function create_slider_post_type()
	{
		// Array holding custom post attributes.
	    $args = array(
	        'description' => 'Slider Post Type',
	        'show_ui' => true,
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
	            'view_item' => 'View Slider',
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
	            'title',
	            'editor',
	            'thumbnail',
	            'comments'
	        )
	    );
	
		// Register the new post type.
	    register_post_type('mumf_slider', $args);
	}

	// Function to add meta data to post type.
	function fwds_slider_meta_box()
	{
	    
	    add_meta_box("mumf-slider-images", "Slider Images", 'mumf_view_slider_images_box', "mumf_slider", "normal");
	    
	}

	// Display the images.
	function mumf_view_slider_images_box()
	{
	    global $post;
	    
	    $gallery_images = get_post_meta($post->ID, "_mumf_gallery_images", true);

	    $gallery_images = ($gallery_images != '') ? json_decode($gallery_images) : array();

	    // Use nonce for verification
	    $html = '<input type="hidden" name="mumf_slider_box_nonce" value="' . wp_create_nonce(basename(__FILE__)) . '" />';
	    
	    $html .= '';
	    $html .= "
	                <table class=\"form-table\">
	                <tbody>
	                <tr>
	                <th><label for=\"Upload Images\">Slide 1</label></th>
	                <td><input type=\"text\" name=\"gallery_img[]\" value=\"" . $gallery_images[0]->image . "\" placeholder=\"The image url\" /></td>
	                <td><input type=\"text\" name=\"gallery_link[]\" value=\"" . $gallery_images[0]->link . "\" placeholder=\"The link url\" /></td>
	                </tr>
	                <tr>
	                <th><label for=\"Upload Images\">Slide 2</label></th>
	                <td><input type=\"text\" name=\"gallery_img[]\" value=\"" . $gallery_images[1]->image . "\" placeholder=\"The image url\" /></td>
	                <td><input type=\"text\" name=\"gallery_link[]\" value=\"" . $gallery_images[1]->link . "\" placeholder=\"The link url\" /></td>
	                </tr>
	                <tr>
	                <th><label for=\"Upload Images\">Slide 3</label></th>
	                <td><input type=\"text\" name=\"gallery_img[]\" value=\"" . $gallery_images[2]->image . "\" placeholder=\"The image url\" /></td>
	                <td><input type=\"text\" name=\"gallery_link[]\" value=\"" . $gallery_images[2]->link . "\" placeholder=\"The link url\" /></td>
	                </tr>
	                <tr>
	                <th><label for=\"Upload Images\">Slide 4</label></th>
	                <td><input type=\"text\" name=\"gallery_img[]\" value=\"" . $gallery_images[3]->image . "\" placeholder=\"The image url\" /></td>
	                <td><input type=\"text\" name=\"gallery_link[]\" value=\"" . $gallery_images[3]->link . "\" placeholder=\"The link url\" /></td>
	                </tr>
	                <tr>
	                <th><label for=\"Upload Images\">Slide 5</label></th>
	                <td><input type=\"text\" name=\"gallery_img[]\" value=\"" . $gallery_images[4]->image . "\" placeholder=\"The image url\" /></td>
	                <td><input type=\"text\" name=\"gallery_link[]\" value=\"" . $gallery_images[4]->link . "\" placeholder=\"The link url\" /></td>
	                </tr>
	                </tbody>
	                </table>
	    ";
	    
	    echo $html;
	    
	}

	// Adds meta data to mumf_slider post type.
	function mumf_slider_meta_box() {

	    add_meta_box("mumf-slider-images", "Slider Images", 'mumf_view_slider_images_box', "mumf_slider", "normal");

	}
	

	// Saves the image data to the post meta data.
	function mumf_save_slider_info($post_id) {

	    // verify nonce
	    if (!wp_verify_nonce($_POST['mumf_slider_box_nonce'], basename(__FILE__))) {

	       return $post_id;

	    }

	    // check autosave

	    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {

	       return $post_id;

	    }

	    // check permissions

	    if ('mumf_slider' == $_POST['post_type'] && current_user_can('edit_post', $post_id)) {

			/* Save Slider Images */

			$gallery_images = (isset($_POST['gallery_img']) ? $_POST['gallery_img'] : '');

			$gallery_links = (isset($_POST['gallery_link']) ? $_POST['gallery_link'] : '');	       

			// Create new array to hold combined data.
			$gallery_items = array();
			// Loop through each gallery image.
			foreach($gallery_images as $key => $val)
			{
				// Create new index with array containing image and link.
				$gallery_items[$key] = array('image' => $gallery_images[$key], 'link' => $gallery_links[$key]);
			}

	       $gallery_items = strip_tags(json_encode($gallery_items));

	       update_post_meta($post_id, "_mumf_gallery_images", $gallery_items);

	    } else {

	       return $post_id;

	    }

	}

	// Function to display the slider.
	function mumf_display_slider($attr,$content) {

	    extract(shortcode_atts(array(

	               'id' => ''

	                   ), $attr));

				$gallery_images = get_post_meta($id, "_mumf_gallery_images", true);

				$gallery_images = ($gallery_images != '') ? json_decode($gallery_images) : array();

				$plugins_url = plugins_url();

				// Initial container html.
				$html = '<div class="mumf-slider">
				         	<ul>';

				// Loop each of the images.
				foreach ($gallery_images as $gal_img) {
					// If the image is not an empty string.
					if($gal_img != ""){

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
					if($gal_img != ""){

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


			do_action('mumf_slider_initiate');

	     return $html;

	}


	// Add the shortcode which will be used to display our slider.
	add_shortcode("mumf_slider", "mumf_display_slider");

	// call function to initiate javscript on slider.
	add_action('mumf_slider_initiate', 'mumf_slider_initiate');

	// Call function to save images on post save.
	add_action('save_post', 'mumf_save_slider_info');

	// Call function to add post meta data.
	add_action('add_meta_boxes', 'mumf_slider_meta_box');

	// Create custom post type after init.
	add_action('init', 'create_slider_post_type');

	// Register the activation function.
	register_activation_hook(__FILE__, 'mumfd_slider_activation');

	// Register the deactivation function.
	register_deactivation_hook(__FILE__, 'mumf_slider_deactivation');



?>