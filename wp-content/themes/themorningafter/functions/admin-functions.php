<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- woo_image - Get Image from custom field
    - vt_resize - Resize post thumbnail
    - woo_get_youtube_video_image - Get thumbnail from YouTube
- woo_get_embed - Get Video
- Woo Show Page Menu
- Get the style path currently selected
- Get page ID
- Tidy up the image source url
- Show image in RSS feed
- Show analytics code footer
- Browser detection body_class() output
- Twitter's Blogger.js output for Twitter widgets
- Template Detector
- Framework Updater
	- WooFramework Update Page  
 	- WooFramework Update Head
 	- WooFramework Version Getter
- Woo URL shortener
- SEO - woo_title()
- SEO - woo_meta()
- Woo Text Trimmer
- Google Webfonts array 
- Google Fonts Stylesheet Generator 
- Enable Home link in WP Menus
- Buy Themes page
- Detects the Charset of String and Converts it to UTF-8
- WP Login logo 

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* woo_image - Get Image from custom field  */
/*-----------------------------------------------------------------------------------*/

/*
This function retrieves/resizes the image to be used with the post in this order:

1. Image passed through parameter 'src'
2. WP Post Thumbnail (if option activated)
3. Custom field
4. First attached image in post (if option activated)
5. First inline image in post (if option activated)

Resize options (enabled in options panel):
- vt_resize() is used to natively resize #2 and #4
- Thumb.php is used to resize #1, #3, #4 (only if vt_resize is disabled) and #5

Parameters: 
        $key = Custom field key eg. "image"
        $width = Set width manually without using $type
        $height = Set height manually without using $type
        $class = CSS class to use on the img tag eg. "alignleft". Default is "thumbnail"
        $quality = Enter a quality between 80-100. Default is 90
        $id = Assign a custom ID, if alternative is required.
        $link = Echo with anchor ('src'), without anchor ('img') or original image URL ('url').
        $repeat = Auto Img Function. Adjust amount of images to return for the post attachments.
        $offset = Auto Img Function. Offset the $repeat with assigned amount of objects.
        $before = Auto Img Function. Add Syntax before image output.
        $after = Auto Img Function. Add Syntax after image output.
        $single = (true/false) Force thumbnail to link to the post instead of the image.
        $force = Force smaller images to not be effected with image width and height dimentions (proportions fix)
        $return = Return results instead of echoing out.
		$src = A parameter that accepts a img url for resizing. (No anchor)
		$meta = Add a custom meta text to the image and anchor of the image.
		$alignment = Crop alignment for thumb.php (l, r, t, b)
		$size = Custom pre-defined size for WP Thumbnail (string)
*/

function woo_image($args) {

	/* ------------------------------------------------------------------------- */
	/* SET VARIABLES */
	/* ------------------------------------------------------------------------- */

	global $post;
	global $woo_options;
	
	//Defaults
	$key = 'image';
	$width = null;
	$height = null;
	$class = '';
	$quality = 90;
	$id = null;
	$link = 'src';
	$repeat = 1;
	$offset = 0;
	$before = '';
	$after = '';
	$single = false;
	$force = false;
	$return = false;
	$is_auto_image = false;
	$src = '';
	$meta = '';
	$alignment = '';
	$size = '';	

	$alt = '';
	$img_link = '';
	
	$attachment_id = array();
	$attachment_src = array();
		
	if ( !is_array($args) ) 
		parse_str( $args, $args );
	
	extract($args);
	
    // Set post ID
    if ( empty($id) ) {
		$id = $post->ID;
    }

	$thumb_id = get_post_meta($post->ID,'_thumbnail_id',true);
    
	// Set alignment 
	if ( $alignment == '') 
		$alignment = get_post_meta($id, '_image_alignment', true);

	// Get standard sizes
	if ( !$width && !$height ) {
		$width = '100';
		$height = '100';
	}
    
	/* ------------------------------------------------------------------------- */
	/* FIND IMAGE TO USE */
	/* ------------------------------------------------------------------------- */

	// When a custom image is sent through
	if ( $src != '' ) { 
		$custom_field = $src;
		$link = 'img';
	
	// WP 2.9 Post Thumbnail support	
	} elseif ( get_option('woo_post_image_support') == 'true' AND !empty($thumb_id) ) {

		if ( get_option('woo_pis_resize') == "true") {
		
			// Dynamically resize the post thumbnail 
			$vt_crop = get_option('woo_pis_hard_crop');
			if ($vt_crop == "true") $vt_crop = true; else $vt_crop = false;
			$vt_image = vt_resize( $thumb_id, '', $width, $height, $vt_crop );
			
			// Set fields for output
			$custom_field = $vt_image['url'];		
			$width = $vt_image['width'];
			$height = $vt_image['height'];
			
		} else {
			// Use predefined size string
			if ( $size ) 
				$thumb_size = $size;
			else 
				$thumb_size = array($width,$height);
				
			$img_link = get_the_post_thumbnail($id,$thumb_size,array('class' => 'woo-image ' . $class));
		}		
		
	// Grab the image from custom field
	} else {
    	$custom_field = get_post_meta($id, $key, true);
	} 

	// Automatic Image Thumbs - get first image from post attachment
	if ( empty($custom_field) && get_option('woo_auto_img') == 'true' && empty($img_link) && !(is_singular() AND in_the_loop() AND $link == "src") ) { 
	        
        if( $offset >= 1 ) 
			$repeat = $repeat + $offset;
    
        $attachments = get_children( array(	'post_parent' => $id,
											'numberposts' => $repeat,
											'post_type' => 'attachment',
											'post_mime_type' => 'image',
											'order' => 'DESC', 
											'orderby' => 'menu_order date')
											);

		// Search for and get the post attachment
		if ( !empty($attachments) ) { 
       
			$counter = -1;
			$size = 'large';
			foreach ( $attachments as $att_id => $attachment ) {            
				$counter++;
				if ( $counter < $offset ) 
					continue;
			
				if ( get_option('woo_pis_resize') == "true") {
				
					// Dynamically resize the post thumbnail 
					$vt_crop = get_option('woo_pis_hard_crop');
					if ($vt_crop == "true") $vt_crop = true; else $vt_crop = false;
					$vt_image = vt_resize( $att_id, '', $width, $height, $vt_crop );
					
					// Set fields for output
					$custom_field = $vt_image['url'];		
					$width = $vt_image['width'];
					$height = $vt_image['height'];
				
				} else {

					$src = wp_get_attachment_image_src($att_id, $size, true);
					$custom_field = $src[0];
					$attachment_id[] = $att_id;
					$src_arr[] = $custom_field;
						
				}
				$thumb_id = $att_id;
				$is_auto_image = true;
			}

		// Get the first img tag from content
		} else { 

			$first_img = '';
			$post = get_post($id); 
			ob_start();
			ob_end_clean();
			$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
			if ( !empty($matches[1][0]) ) {
				
				// Save Image URL
				$custom_field = $matches[1][0];
				
				// Search for ALT tag
				$output = preg_match_all('/<img.+alt=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
				if ( !empty($matches[1][0]) ) {
					$alt = $matches[1][0];
				}
			}

		}
		
	} 
	
	// Check if there is YouTube embed
	if ( empty($custom_field) && empty($img_link) ) {
		$embed = get_post_meta($id, "embed", true);
		if ( $embed ) 
	    	$custom_field = woo_get_video_image($embed);
	}
				
	// Return if there is no attachment or custom field set
	if ( empty($custom_field) && empty($img_link) ) {
		
		// Check if default placeholder image is uploaded
		$placeholder = get_option('framework_woo_default_image');
		if ( $placeholder && !(is_singular() AND in_the_loop()) ) {
			$custom_field = $placeholder;	

			// Resize the placeholder if
			if ( get_option('woo_pis_resize') == "true") {

				// Dynamically resize the post thumbnail 
				$vt_crop = get_option('woo_pis_hard_crop');
				if ($vt_crop == "true") $vt_crop = true; else $vt_crop = false;
				$vt_image = vt_resize( '', $placeholder, $width, $height, $vt_crop );
				
				// Set fields for output
				$custom_field = $vt_image['url'];		
				$width = $vt_image['width'];
				$height = $vt_image['height'];
			
			}			
			
		} else {
	       return;
	    }
	
	}
	
	if(empty($src_arr) && empty($img_link)){ $src_arr[] = $custom_field; }
	
	/* ------------------------------------------------------------------------- */
	/* BEGIN OUTPUT */
	/* ------------------------------------------------------------------------- */

    $output = '';
	
    // Set output height and width
    $set_width = ' width="' . $width .'" ';
    $set_height = ' height="' . $height .'" '; 
    if($height == null OR $height == '') $set_height = '';
		
	// Set standard class
	if ( $class ) $class = 'woo-image ' . $class; else $class = 'woo-image';

	// Do check to verify if images are smaller then specified.
	if($force == true){ $set_width = ''; $set_height = ''; }

	// WP Post Thumbnail
	if(!empty($img_link) ){
			
		if( $link == 'img' ) {  // Output the image without anchors
			$output .= $before; 
			$output .= $img_link;
			$output .= $after;  
			
		} elseif( $link == 'url' ) {  // Output the large image

			$src = wp_get_attachment_image_src($thumb_id, 'large', true);
			$custom_field = $src[0];
			$output .= $custom_field;

		} else {  // Default - output with link				

			if ( ( is_single() OR is_page() ) AND $single == false ) {
				$rel = 'rel="lightbox"';
				$href = false;  
			} else { 
				$href = get_permalink($id);
				$rel = '';
			}
			
			$title = 'title="' . get_the_title($id) .'"';
		
			$output .= $before; 
			if($href == false){
				$output .= $img_link;
			} else {
				$output .= '<a '.$title.' href="' . $href .'" '.$rel.'>' . $img_link . '</a>';
			}
			
			$output .= $after;  
		}	
	}
	
	// Use thumb.php to resize. Skip if image has been natively resized with vt_resize.
	elseif ( get_option('woo_resize') == 'true' && empty($vt_image['url']) ) { 
		
		foreach($src_arr as $key => $custom_field){
	
			// Clean the image URL
			$href = $custom_field; 		
			$custom_field = cleanSource( $custom_field );

			// Check if WPMU and set correct path AND that image isn't external
			if ( function_exists('get_current_site') && strpos($custom_field,"http://") !== 0 ) {
				get_current_site();
				//global $blog_id; Breaks with WP3 MS
				if ( !$blog_id ) {
					global $current_blog;
					$blog_id = $current_blog->blog_id;				
				}
				if ( isset($blog_id) && $blog_id > 0 ) {
					$imageParts = explode( 'files/', $custom_field );
					if ( isset($imageParts[1]) ) 
						$custom_field = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
				}
			}
			
			//Set the ID to the Attachment's ID if it is an attachment
			if($is_auto_image == true){	
				$quick_id = $attachment_id[$key];
			} else {
			 	$quick_id = $id;
			}
			
			//Set custom meta 
			if ($meta) { 
				$alt = $meta;
				$title = 'title="'. $meta .'"';
			} else { 
				if ($alt == '') $alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
				$title = 'title="'. get_the_title($quick_id) .'"';
			}
			
			// Set alignment parameter
			if ($alignment <> '')
				$alignment = '&amp;a='.$alignment;
											
			$img_link = '<img src="'. get_bloginfo('template_url'). '/thumb.php?src='. $custom_field .'&amp;w='. $width .'&amp;h='. $height .'&amp;zc=1&amp;q='. $quality . $alignment . '" alt="'.$alt.'" class="'. stripslashes($class) .'" '. $set_width . $set_height . ' />';
			
			if( $link == 'img' ) {  // Just output the image
				$output .= $before; 
				$output .= $img_link;
				$output .= $after;  
				
			} elseif( $link == 'url' ) {  // Output the image without anchors
	
				if($is_auto_image == true){	
					$src = wp_get_attachment_image_src($thumb_id, 'large', true);
					$custom_field = $src[0];
				}
				$output .= $custom_field;
				
			} else {  // Default - output with link				

				if ( ( is_single() OR is_page() ) AND $single == false ) {
					$rel = 'rel="lightbox"';
				} else { 
					$href = get_permalink($id);
					$rel = '';
				}
			
				$output .= $before; 
				$output .= '<a '.$title.' href="' . $href .'" '.$rel.'>' . $img_link . '</a>';
				$output .= $after;  
			}
		}
		
	// No dynamic resizing
	} else {  
		
		foreach($src_arr as $key => $custom_field){
				
			//Set the ID to the Attachment's ID if it is an attachment
			if($is_auto_image == true AND isset($attachment_id[$key])){	
				$quick_id = $attachment_id[$key];
			} else {
			 	$quick_id = $id;
			}
			
			//Set custom meta 
			if ($meta) { 
				$alt = $meta;
				$title = 'title="'. $meta .'"';
			} else { 
				if ($alt == '') $alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
				$title = 'title="'. get_the_title($quick_id) .'"';
			}
		
			$img_link =  '<img src="'. $custom_field .'" alt="'. $alt .'" '. $set_width . $set_height . ' class="'. stripslashes($class) .'" />';
		
			if ( $link == 'img' ) {  // Just output the image 
				$output .= $before;                   
				$output .= $img_link; 
				$output .= $after;  
				
			} elseif( $link == 'url' ) {  // Output the URL to original image
				if ( $vt_image['url'] || $is_auto_image ) { 
					$src = wp_get_attachment_image_src($thumb_id, 'full', true);
					$custom_field = $src[0];
				}
				$output .= $custom_field;

			} else {  // Default - output with link
			
				if ( ( is_single() OR is_page() ) AND $single == false ) { 

					// Link to the large image if single post
					if ( $vt_image['url'] || $is_auto_image ) { 
						$src = wp_get_attachment_image_src($thumb_id, 'full', true);
						$custom_field = $src[0];
					}
					
					$href = $custom_field;
					$rel = 'rel="lightbox"';
				} else { 
					$href = get_permalink($id);
					$rel = '';
				}
				 
				$output .= $before;   
				$output .= '<a href="' . $href .'" '. $rel . $title .'>' . $img_link . '</a>';
				$output .= $after;   
			}
		}
	}
	
	// Return or echo the output
	if ( $return == TRUE )
		return $output;
	else 
		echo $output; // Done  

}

/* Get thumbnail from Video Embed code */

if (!function_exists('woo_get_video_image')) { 
	function woo_get_video_image($embed) { 
	
		// YouTube - get the video code if this is an embed code (old embed)
		preg_match('/youtube\.com\/v\/([\w\-]+)/', $embed, $match);
	 
		// YouTube - if old embed returned an empty ID, try capuring the ID from the new iframe embed
		if($match[1] == '')
			preg_match('/youtube\.com\/embed\/([\w\-]+)/', $embed, $match);
	 
		// YouTube - if it is not an embed code, get the video code from the youtube URL	
		if($match[1] == '')
			preg_match('/v\=(.+)&/',$embed ,$match);
	 
		// YouTube - get the corresponding thumbnail images	
		if($match[1] != '')
			$video_thumb = "http://img.youtube.com/vi/".$match[1]."/0.jpg";
	 
		// return whichever thumbnail image you would like to retrieve
		return $video_thumb;		
	}
}


/*-----------------------------------------------------------------------------------*/
/* vt_resize - Resize images dynamically using wp built in functions
/*-----------------------------------------------------------------------------------*/
/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * php 5.2+
 *
 * Exemplo de uso:
 * 
 * <?php 
 * $thumb = get_post_thumbnail_id(); 
 * $image = vt_resize( $thumb, '', 140, 110, true );
 * ?>
 * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */
if ( !function_exists('vt_resize') ) {
	function vt_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {
	
		// this is an attachment, so we have the ID
		if ( $attach_id ) {
		
			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$file_path = get_attached_file( $attach_id );
		
		// this is not an attachment, let's use the image url
		} else if ( $img_url ) {
			
			$file_path = parse_url( $img_url );
			$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];
			
			//$file_path = ltrim( $file_path['path'], '/' );
			//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];
			
			$orig_size = getimagesize( $file_path );
			
			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}
		
		$file_info = pathinfo( $file_path );
	
		// check if file exists
		$base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
		if ( !file_exists($base_file) )
		 return;
		 
		$extension = '.'. $file_info['extension'];
	
		// the image path without the extension
		$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];
		
		
		$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;
	
		// checking if the file size is larger than the target size
		// if it is smaller or the same size, stop right here and return
		if ( $image_src[1] > $width || $image_src[2] > $height ) {
	
			// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
			if ( file_exists( $cropped_img_path ) ) {
	
				$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );
				
				$vt_image = array (
					'url' => $cropped_img_url,
					'width' => $width,
					'height' => $height
				);
				
				return $vt_image;
			}
	
			// $crop = false
			if ( $crop == false ) {
			
				// calculate the size proportionaly
				$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
				$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;			
	
				// checking if the file already exists
				if ( file_exists( $resized_img_path ) ) {
				
					$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );
	
					$vt_image = array (
						'url' => $resized_img_url,
						'width' => $proportional_size[0],
						'height' => $proportional_size[1]
					);
					
					return $vt_image;
				}
			}
	
			// check if image width is smaller than set width
			$img_size = getimagesize( $file_path );
			if ( $img_size[0] <= $width ) $width = $img_size[0]-1;		
	
			// no cache files - let's finally resize it
			$new_img_path = image_resize( $file_path, $width, $height, $crop );
			$new_img_size = getimagesize( $new_img_path );
			$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );
	
			// resized output
			$vt_image = array (
				'url' => $new_img,
				'width' => $new_img_size[0],
				'height' => $new_img_size[1]
			);
			
			return $vt_image;
		}
	
		// default output - without resizing
		$vt_image = array (
			'url' => $image_src[0],
			'width' => $image_src[1],
			'height' => $image_src[2]
		);
		
		return $vt_image;
	}
}


/*-----------------------------------------------------------------------------------*/
/* Depreciated - woo_get_image - Get Image from custom field */
/*-----------------------------------------------------------------------------------*/

// Depreciated
function woo_get_image($key = 'image', $width = null, $height = null, $class = "thumbnail", $quality = 90,$id = null,$link = 'src',$repeat = 1,$offset = 0,$before = '', $after = '',$single = false, $force = false, $return = false) {
	// Run new function
	woo_image( 'key='.$key.'&width='.$width.'&height='.$height.'&class='.$class.'&quality='.$quality.'&id='.$id.'&link='.$link.'&repeat='.$repeat.'&offset='.$offset.'&before='.$before.'&after='.$after.'&single='.$single.'&fore='.$force.'&return='.$return );
	return;

}



/*-----------------------------------------------------------------------------------*/
/* woo_embed - Get Video embed code from custom field */
/*-----------------------------------------------------------------------------------*/

/*
Get Video
This function gets the embed code from the custom field
Parameters: 
        $key = Custom field key eg. "embed"
        $width = Set width manually without using $type
        $height = Set height manually without using $type
		$class = Custom class to apply to wrapping div
		$id = ID from post to pull custom field from
*/

function woo_embed($args) {

	//Defaults
	$key = 'embed';
	$width = null;
	$height = null;
	$class = 'video';
	$id = null;	
	
	if ( !is_array($args) ) 
		parse_str( $args, $args );
	
	extract($args);

  if(empty($id))
    {
    global $post;
    $id = $post->ID;
    } 
    

$custom_field = get_post_meta($id, $key, true);

if ($custom_field) : 

	$custom_field = html_entity_decode( $custom_field ); // Decode HTML entities.

    $org_width = $width;
    $org_height = $height;
    $calculated_height = '';
    
    // Get custom width and height
    $custom_width = get_post_meta($id, 'width', true);
    $custom_height = get_post_meta($id, 'height', true);    
    
    //Dynamic Height Calculation
    if ($org_height == '' && $org_width != '') {
    	$raw_values = explode(" ", $custom_field);
    
    	foreach ($raw_values as $raw) {
    		$embed_params = explode("=",$raw);
    		if ($embed_params[0] == 'width') {
   		 		$embed_width = ereg_replace("[^0-9]", "", $embed_params[1]);
    		}
    		elseif ($embed_params[0] == 'height') {
    			$embed_height = ereg_replace("[^0-9]", "", $embed_params[1]);
    		} 
    	}
    
    	$float_width = floatval($embed_width);
		$float_height = floatval($embed_height);
		$float_ratio = $float_height / $float_width;
		$calculated_height = intval($float_ratio * $width);
    }
    
    // Set values: width="XXX", height="XXX"
    if ( !$custom_width ) $width = 'width="'.$width.'"'; else $width = 'width="'.$custom_width.'"';
    if ( $height == '' ) { $height = 'height="'.$calculated_height.'"'; } else { if ( !$custom_height ) { $height = 'height="'.$height.'"'; } else { $height = 'height="'.$custom_height.'"'; } }
    $custom_field = stripslashes($custom_field);
    $custom_field = preg_replace( '/width="([0-9]*)"/' , $width , $custom_field );
    $custom_field = preg_replace( '/height="([0-9]*)"/' , $height , $custom_field );    

    // Set values: width:XXXpx, height:XXXpx
    if ( !$custom_width ) $width = 'width:'.$org_width.'px'; else $width = 'width:'.$custom_width.'px';
    if (  $height == '' ) { $height = 'height:'.$calculated_height.'px'; } else { if ( !$custom_height ) { $height = 'height:'.$org_height.'px'; } else { $height = 'height:'.$custom_height.'px'; } }
    $custom_field = stripslashes($custom_field);
    $custom_field = preg_replace( '/width:([0-9]*)px/' , $width , $custom_field );
    $custom_field = preg_replace( '/height:([0-9]*)px/' , $height , $custom_field );     

	// Suckerfish menu hack
	$custom_field = str_replace('<embed ','<param name="wmode" value="transparent"></param><embed wmode="transparent" ',$custom_field);

	$output = '';
    $output .= '<div class="'. $class .'">' . $custom_field . '</div>';
    
    return $output; 
	
else :

	return false;
    
endif;

}

/*-----------------------------------------------------------------------------------*/
/* Depreciated - woo_get_embed - Get Video embed code from custom field */
/*-----------------------------------------------------------------------------------*/

// Depreciated
function woo_get_embed($key = 'embed', $width, $height, $class = 'video', $id = null) {
	// Run new function
	return woo_embed( 'key='.$key.'&width='.$width.'&height='.$height.'&class='.$class.'&id='.$id );

}



/*-----------------------------------------------------------------------------------*/
/* Woo Show Page Menu */
/*-----------------------------------------------------------------------------------*/

// Show menu in header.php
// Exlude the pages from the slider
function woo_show_pagemenu( $exclude="" ) {
    // Split the featured pages from the options, and put in an array
    if ( get_option('woo_ex_featpages') ) {
        $menupages = get_option('woo_featpages');
        $exclude = $menupages . ',' . $exclude;
    }
    
    $pages = wp_list_pages('sort_column=menu_order&title_li=&echo=0&depth=1&exclude='.$exclude);
    $pages = preg_replace('%<a ([^>]+)>%U','<a $1><span>', $pages);
    $pages = str_replace('</a>','</span></a>', $pages);
    echo $pages;
}



/*-----------------------------------------------------------------------------------*/
/* Get the style path currently selected */
/*-----------------------------------------------------------------------------------*/

function woo_style_path() {
	
	$return = '';
	
	$style = $_REQUEST['style'];
	
	// Sanitize request input.
	$style = strtolower( trim( strip_tags( $style ) ) );
	
	if ( $style != '' ) {
	
		$style_path = $style;
	
	} else {
	
		$stylesheet = get_option( 'woo_alt_stylesheet' );
		
		// Prevent against an empty return to $stylesheet.
		
		if ( $stylesheet == '' ) {
		
			$stylesheet = 'default.css';
		
		} // End IF Statement
		
		$style_path = str_replace( '.css', '', $stylesheet );
	
	} // End IF Statement
	
	if ( $style_path == 'default' ) {
	
		$return = 'images';
	
	} else {
	
		$return = 'styles/' . $style_path;
	
	} // End IF Statement
	
	echo $return;
	
} // End woo_style_path()


/*-----------------------------------------------------------------------------------*/
/* Get page ID */
/*-----------------------------------------------------------------------------------*/
function get_page_id($page_slug){
	$page_id = get_page_by_path($page_slug);
    if ($page_id) {
        return $page_id->ID;
    } else {
        return null;
    }    
    
}

/*-----------------------------------------------------------------------------------*/
/* Tidy up the image source url */
/*-----------------------------------------------------------------------------------*/
function cleanSource($src) {

	// remove slash from start of string
	if(strpos($src, "/") == 0) {
		$src = substr($src, -(strlen($src) - 1));
	}

	// Check if same domain so it doesn't strip external sites
	$host = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	if ( !strpos($src,$host) )
		return $src;


	$regex = "/^((ht|f)tp(s|):\/\/)(www\.|)" . $host . "/i";
	$src = preg_replace ($regex, '', $src);
	$src = htmlentities ($src);
    
    // remove slash from start of string
    if (strpos($src, '/') === 0) {
        $src = substr ($src, -(strlen($src) - 1));
    }
	
	return $src;
}



/*-----------------------------------------------------------------------------------*/
/* Show image in RSS feed */
/* Original code by Justin Tadlock http://justintadlock.com */
/*-----------------------------------------------------------------------------------*/
if (get_option('woo_rss_thumb') == "true")
	add_filter('the_content', 'add_image_RSS');
	
function add_image_RSS( $content ) {
	
	global $post, $id;
	$blog_key = substr( md5( get_bloginfo('url') ), 0, 16 );
	if ( ! is_feed() ) return $content;

	// Get the "image" from custom field
	$image = get_post_meta($post->ID, 'image', $single = true);
	$image_width = '240';

	// If there's an image, display the image with the content
	if($image !== '') {
		$content = '<p style="float:right; margin:0 0 10px 15px; width:'.$image_width.'px;">
		<img src="'.$image.'" width="'.$image_width.'" />
		</p>' . $content;
		return $content;
	} 

	// If there's not an image, just display the content
	else {
		$content = $content;
		return $content;
	}
} 



/*-----------------------------------------------------------------------------------*/
/* Show analytics code in footer */
/*-----------------------------------------------------------------------------------*/
function woo_analytics(){
	$output = get_option('woo_google_analytics');
	if ( $output <> "" ) 
		echo stripslashes($output) . "\n";
}
add_action('wp_footer','woo_analytics');



/*-----------------------------------------------------------------------------------*/
/* Browser detection body_class() output */
/*-----------------------------------------------------------------------------------*/
add_filter('body_class','browser_body_class');
function browser_body_class($classes) {
	global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

	if($is_lynx) $classes[] = 'lynx';
	elseif($is_gecko) $classes[] = 'gecko';
	elseif($is_opera) $classes[] = 'opera';
	elseif($is_NS4) $classes[] = 'ns4';
	elseif($is_safari) $classes[] = 'safari';
	elseif($is_chrome) $classes[] = 'chrome';
	elseif($is_IE) {
		$browser = $_SERVER['HTTP_USER_AGENT']; 
		$browser = substr("$browser", 25, 8); 
		if ($browser == "MSIE 7.0"  )
			$classes[] = 'ie7';
		elseif ($browser == "MSIE 6.0" )
			$classes[] = 'ie6'; 
		else	
			$classes[] = 'ie';
	}
	else $classes[] = 'unknown';

	if($is_iphone) $classes[] = 'iphone';
	return $classes;
}

/*-----------------------------------------------------------------------------------*/
/* Twitter's Blogger.js output for Twitter widgets */
/*-----------------------------------------------------------------------------------*/

if ( !function_exists('woo_twitter_script') ) {
	function woo_twitter_script($unique_id,$username,$limit) {
	?>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	
	    function twitterCallback2(twitters) {
	      var statusHTML = [];
	      for (var i=0; i<twitters.length; i++){
	        var username = twitters[i].user.screen_name;
	        var status = twitters[i].text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url) {
	          return '<a href="'+url+'">'+url+'</a>';
	        }).replace(/\B@([_a-z0-9]+)/ig, function(reply) {
	          return  reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';
	        });
	        statusHTML.push('<li><span class="content">'+status+'</span> <a style="font-size:85%" class="time" href="http://twitter.com/'+username+'/statuses/'+twitters[i].id+'">'+relative_time(twitters[i].created_at)+'</a></li>');
	      }
	      document.getElementById('twitter_update_list_<?php echo $unique_id; ?>').innerHTML = statusHTML.join('');
	    }
	    
	    function relative_time(time_value) {
	      var values = time_value.split(" ");
	      time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
	      var parsed_date = Date.parse(time_value);
	      var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
	      var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
	      delta = delta + (relative_to.getTimezoneOffset() * 60);
	    
	      if (delta < 60) {
	        return 'less than a minute ago';
	      } else if(delta < 120) {
	        return 'about a minute ago';
	      } else if(delta < (60*60)) {
	        return (parseInt(delta / 60)).toString() + ' minutes ago';
	      } else if(delta < (120*60)) {
	        return 'about an hour ago';
	      } else if(delta < (24*60*60)) {
	        return 'about ' + (parseInt(delta / 3600)).toString() + ' hours ago';
	      } else if(delta < (48*60*60)) {
	        return '1 day ago';
	      } else {
	        return (parseInt(delta / 86400)).toString() + ' days ago';
	      }
	    }
	//-->!]]>
	</script>
	<script type="text/javascript" src="http://api.twitter.com/1/statuses/user_timeline/<?php echo $username; ?>.json?callback=twitterCallback2&amp;count=<?php echo $limit; ?>&amp;include_rts=t"></script>
	<?php
	}
}

/*-----------------------------------------------------------------------------------*/
/* Template Detector */
/*-----------------------------------------------------------------------------------*/
function woo_active_template($filename = null){

	if(isset($filename)){
		
		global $wpdb;
		$query = "SELECT *,count(*) AS used FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = '$filename' GROUP BY meta_value";
		$results = $wpdb->get_row($wpdb->prepare($query),'ARRAY_A'); // Select thrid coloumn accross
				
		if(empty($results))
			return false;
			
		$post_id = $results['post_id'];
		$trash = get_post_status($post_id); // Check for trash
		
		if($trash != 'trash')
			return true;
		else
	 		return false;
	
	} else {
		return false; // No $filename argument was set
	}

}
/*-----------------------------------------------------------------------------------*/
/* WooFramework Update Page */
/*-----------------------------------------------------------------------------------*/

function woothemes_framework_update_page(){
        $method = get_filesystem_method();
        $to = ABSPATH . 'wp-content/themes/' . get_option('template') . "/functions/";
        if(isset($_POST['password'])){
            
            $cred = $_POST;
            $filesystem = WP_Filesystem($cred);
            
        }
        elseif(isset($_POST['woo_ftp_cred'])){
            
             $cred = unserialize(base64_decode($_POST['woo_ftp_cred']));
             $filesystem = WP_Filesystem($cred);  
            
        } else {
            
           $filesystem = WP_Filesystem(); 
            
        };
        $url = admin_url('admin.php?page=woothemes_framework_update');
        ?>
            <div class="wrap themes-page">

            <?php
            if($filesystem == false){
                
            request_filesystem_credentials ( $url );
                
            }  else {
            ?>
            
            <?php 
            $localversion = get_option('woo_framework_version');
            $remoteversion = woo_get_fw_version();
            // Test if new version
            $upd = false;
			$loc = explode('.',$localversion);				
			$rem = explode('.',$remoteversion);	                
			
            if( $loc[0] < $rem[0] )  
            	$upd = true;
            elseif ( $loc[1] < $rem[1] )
            	$upd = true;
            elseif( $loc[2] < $rem[2] )
            	$upd = true;

            ?>
            <div class="icon32" id="icon-tools"><br></div>
            <h2>Framework Update</h2>
            <span style="display:none"><?php echo $method; ?></span>
            <form method="post"  enctype="multipart/form-data" id="wooform" action="<?php /* echo $url; */ ?>">
                
                <?php if( $upd ) { ?>
                <?php wp_nonce_field('update-options'); ?>
                <h3>A new version of WooFramework is available.</h3>
                <p>This updater will collect a file from the WooThemes.com server. It will download and extract the files to your current theme's functions folder. </p>
                <p>We recommend backing up your theme files before updating. Only upgrade the WooFramework if necessary.</p>
                <p>&rarr; <strong>Your version:</strong> <?php echo $localversion; ?></p>
                
                <p>&rarr; <strong>Current Version:</strong> <?php echo $remoteversion; ?></p>
                
                <input type="submit" class="button" value="Update Framework" />
                <?php } else { ?>                
                <h3>You have the latest version of WooFramework</h3>
                <p>&rarr; <strong>Your version:</strong> <?php echo $localversion; ?></p>
                <?php } ?>
                <input type="hidden" name="woo_update_save" value="save" />
                <input type="hidden" name="woo_ftp_cred" value="<?php echo base64_encode(serialize($_POST)); ?>" />

            </form>
            <?php } ?>
            </div>
            <?php
};

/*-----------------------------------------------------------------------------------*/
/* WooFramework Update Head */
/*-----------------------------------------------------------------------------------*/

function woothemes_framework_update_head(){

  if(isset($_REQUEST['page'])){
	
	// Sanitize page being requested.
	$_page = strtolower( strip_tags( trim( $_REQUEST['page'] ) ) );
	
	if( $_page == 'woothemes_framework_update'){
              
		//Setup Filesystem 
		$method = get_filesystem_method(); 
		
		if(isset($_POST['woo_ftp_cred'])){ 
			 
			$cred = unserialize(base64_decode($_POST['woo_ftp_cred']));
			$filesystem = WP_Filesystem($cred);
			
		} else {
			
		   $filesystem = WP_Filesystem(); 
			
		};     
	
		if($filesystem == false && $_POST['upgrade'] != 'Proceed'){
			
			function woothemes_framework_update_filesystem_warning() {
					$method = get_filesystem_method();
					echo "<div id='filesystem-warning' class='updated fade'><p>Failed: Filesystem preventing downloads. (". $method .")</p></div>";
				}
				add_action('admin_notices', 'woothemes_framework_update_filesystem_warning');
				return;
		}
		if(isset($_REQUEST['woo_update_save'])){
		
			// Sanitize action being requested.
			$_action = strtolower( trim( strip_tags( $_REQUEST['woo_update_save'] ) ) );
		
		if( $_action == 'save' ){
		
		$temp_file_addr = download_url('http://www.woothemes.com/updates/framework.zip');
		
		if ( is_wp_error($temp_file_addr) ) {
			
			$error = $temp_file_addr->get_error_code();
		
			if($error == 'http_no_url') {
			//The source file was not found or is invalid
				function woothemes_framework_update_missing_source_warning() {
					echo "<div id='source-warning' class='updated fade'><p>Failed: Invalid URL Provided</p></div>";
				}
				add_action('admin_notices', 'woothemes_framework_update_missing_source_warning');
			} else {
				function woothemes_framework_update_other_upload_warning() {
					echo "<div id='source-warning' class='updated fade'><p>Failed: Upload - $error</p></div>";
				}
				add_action('admin_notices', 'woothemes_framework_update_other_upload_warning');
				
			}
			
			return;
	
		  } 
		//Unzipp it
		global $wp_filesystem;
		$to = $wp_filesystem->wp_content_dir() . "/themes/" . get_option('template') . "/functions/";
		
		$dounzip = unzip_file($temp_file_addr, $to);
		
		unlink($temp_file_addr); // Delete Temp File
		
		if ( is_wp_error($dounzip) ) {
			
			//DEBUG
			$error = $dounzip->get_error_code();
			$data = $dounzip->get_error_data($error);
			//echo $error. ' - ';
			//print_r($data);
							
			if($error == 'incompatible_archive') {
				//The source file was not found or is invalid
				function woothemes_framework_update_no_archive_warning() {
					echo "<div id='woo-no-archive-warning' class='updated fade'><p>Failed: Incompatible archive</p></div>";
				}
				add_action('admin_notices', 'woothemes_framework_update_no_archive_warning');
			} 
			if($error == 'empty_archive') {
				function woothemes_framework_update_empty_archive_warning() {
					echo "<div id='woo-empty-archive-warning' class='updated fade'><p>Failed: Empty Archive</p></div>";
				}
				add_action('admin_notices', 'woothemes_framework_update_empty_archive_warning');
			}
			if($error == 'mkdir_failed') {
				function woothemes_framework_update_mkdir_warning() {
					echo "<div id='woo-mkdir-warning' class='updated fade'><p>Failed: mkdir Failure</p></div>";
				}
				add_action('admin_notices', 'woothemes_framework_update_mkdir_warning');
			}  
			if($error == 'copy_failed') {
				function woothemes_framework_update_copy_fail_warning() {
					echo "<div id='woo-copy-fail-warning' class='updated fade'><p>Failed: Copy Failed</p></div>";
				}
				add_action('admin_notices', 'woothemes_framework_update_copy_fail_warning');
			}
				
			return;
	
		} 
		
		function woothemes_framework_updated_success() {
			echo "<div id='framework-upgraded' class='updated fade'><p>New framework successfully downloaded, extracted and updated.</p></div>";
		}
		add_action('admin_notices', 'woothemes_framework_updated_success');
		
		}
	}
	} //End user input save part of the update
 }
}
                             
add_action('admin_head','woothemes_framework_update_head');

/*-----------------------------------------------------------------------------------*/
/* WooFramework Version Getter */
/*-----------------------------------------------------------------------------------*/

function woo_get_fw_version($url = ''){
	
	if(!empty($url)){
		$fw_url = $url;
	} else {
    	$fw_url = 'http://www.woothemes.com/updates/functions-changelog.txt';
    }
    
	$temp_file_addr = download_url($fw_url);
	if(!is_wp_error($temp_file_addr) && $file_contents = file($temp_file_addr)) {
        foreach ($file_contents as $line_num => $line) {
                            
                $current_line =  $line;
                
                if($line_num > 1){    // Not the first or second... dodgy :P
                    
                    if (preg_match('/^[0-9]/', $line)) {
                                            
                            $current_line = stristr($current_line,"version");
                            $current_line = preg_replace('~[^0-9,.]~','',$current_line);
                            $output = $current_line;
                            break;
                    }
                }     
        }
        unlink($temp_file_addr);
        return $output;

        
    } else {
        return 'Currently Unavailable';
    }

}

/*-----------------------------------------------------------------------------------*/
/* Woo URL shortener */
/*-----------------------------------------------------------------------------------*/

function woo_short_url($url) {
	$service = get_option('woo_url_shorten');
	$bitlyapilogin = get_option('woo_bitly_api_login');;
	$bitlyapikey = get_option('woo_bitly_api_key');;
	if (isset($service)) {
		switch ($service) 
		{
    		case 'TinyURL':
    			$shorturl = getTinyUrl($url);
    			break;
    		case 'Bit.ly':
    			if (isset($bitlyapilogin) && isset($bitlyapikey) && ($bitlyapilogin != '') && ($bitlyapikey != '')) {
    				$shorturl = make_bitly_url($url,$bitlyapilogin,$bitlyapikey,'json');
    			}
    			else {
    				$shorturl = getTinyUrl($url);
    			}
    			break;
    		case 'Off':
    			$shorturl = $url;
    			break;
    		default:
    			$shorturl = $url;
    			break;
    	}
	}
	else {
		$shorturl = $url;
	}
	return $shorturl;
}

//TinyURL
function getTinyUrl($url) {
	$tinyurl = file_get_contents_curl("http://tinyurl.com/api-create.php?url=".$url);
	return $tinyurl;
}

//Bit.ly
function make_bitly_url($url,$login,$appkey,$format = 'xml',$version = '2.0.1')
{
	//create the URL
	$bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;
	
	//get the url
	//could also use cURL here
	$response = file_get_contents_curl($bitly);
	
	//parse depending on desired format
	if(strtolower($format) == 'json')
	{
		$json = @json_decode($response,true);
		return $json['results'][$url]['shortUrl'];
	}
	else //xml
	{
		$xml = simplexml_load_string($response);
		return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
	}
}

//Alternative CURL function
function file_get_contents_curl($url) {
	if (_iscurlinstalled()) {
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url);
	
		$data = curl_exec($ch);
		
		if ($data === FALSE) {
			$data =  "cURL Error: " . curl_error($ch);
		}
	
		curl_close($ch);
	} else {
		$data = $url;
	}
	return $data;
}

// Checks for presence of the cURL extension.
function _iscurlinstalled() {
	if  (in_array  ('curl', get_loaded_extensions())) {
		if (function_exists('curl_init')) {
			return true;
		} else {
			return false;
		}
	}
	else{
		if (function_exists('curl_init')) {
			return true;
		} else {
			return false;
		}
	}
}

/*-----------------------------------------------------------------------------------*/
/* woo_title() */
/*-----------------------------------------------------------------------------------*/

function woo_title(){

	global $post;
	$layout = ''; 
	
	//Taxonomy Details WP 3.0 only
	if ( function_exists('get_taxonomies') ) :
		global $wp_query; 
		$taxonomy_obj = $wp_query->get_queried_object();
		if(!empty($taxonomy_obj->name)) :
			$taxonomy_nice_name = $taxonomy_obj->name;
			$term_id = $taxonomy_obj->term_taxonomy_id;
			$taxonomy_short_name = $taxonomy_obj->taxonomy;
			$taxonomy_top_level_items = get_taxonomies(array('name' => $taxonomy_short_name), 'objects');
			$taxonomy_top_level_item = $taxonomy_top_level_items[$taxonomy_short_name]->label;
		endif;
	endif;
	
	//3rd Party Plugins
	$use_third_party_data = false;
	if(get_option('seo_woo_use_third_party_data') == 'true'){
		$use_third_party_data = true;
	}
		
	if(	(class_exists('All_in_One_SEO_Pack') OR class_exists('Headspace_Plugin')) AND 
		( $use_third_party_data != true )) { wp_title(); return; }

	$sep = get_option('seo_woo_seperator');	
	if(empty($sep)) { $sep = " | ";} else { $sep = ' ' . $sep . ' ';}
	$use_wp_title = get_option('seo_woo_wp_title');
	$home_layout = get_option('seo_woo_home_layout');
	$single_layout = get_option('seo_woo_single_layout');
	$page_layout = get_option('seo_woo_page_layout');
	$archive_layout = get_option('seo_woo_archive_layout');
	
	
	$output = '';
	if($use_wp_title == 'true'){
		
		if(is_home() OR is_front_page()){
			switch ($home_layout){
				case 'a': $output = get_bloginfo('name') . $sep . get_bloginfo('description'); 
				break;
				case 'b': $output = get_bloginfo('name'); 
				break;
				case 'c': $output = get_bloginfo('description'); 
				break;
				}
			if(is_paged()){
				$paged_var = get_query_var('paged');
				if(get_option('seo_woo_paged_var_pos') == 'after'){
				
					$output .= $sep . get_option('seo_woo_paged_var') . ' ' . $paged_var;

				} else {
									
					$output = get_option('seo_woo_paged_var') . ' ' . $paged_var . $sep . $output;

				}
				
			}
			$output = stripslashes($output);
			echo $output;
		}
		else {
		if (is_single()) { $layout = $single_layout; }
		elseif  (is_page()) { $layout = $page_layout; }
		elseif  (is_archive()) { $layout = $archive_layout; }
		elseif  (is_tax()) { $layout = $archive_layout; }
		elseif  (is_search()) { $layout = 'search'; }
		elseif  (is_404()) { $layout = $single_layout; }
		
		
		
		//Check if there is a custom value added to post meta
		$wooseo_title = get_post_meta($post->ID,'seo_title',true); //WooSEO
		$aio_title = get_post_meta($post->ID,'_aioseop_title',true); //All-in-One SEO
		$headspace_title = get_post_meta($post->ID,'_headspace_page_title',true); //Headspace SEO
		
		if(get_option('seo_woo_wp_custom_field_title') != 'true' && is_singular()){
			if(!empty($wooseo_title)){
				$layout = 'wooseo';
			} elseif(!empty($aio_title) AND $use_third_party_data) {
				$layout = 'aioseo';
			} elseif(!empty($headspace_title) AND $use_third_party_data) {
				$layout = 'headspace';
			}
		}
			switch ($layout){
				case 'a': $output = wp_title($sep,false,true) . get_bloginfo('name');
				break;
				case 'b': $output = wp_title('',false,false);
				break;
				case 'c': $output = get_bloginfo('name') . wp_title($sep,false,false);
				break;
				case 'd': $output = wp_title($sep,false,true) . get_bloginfo('description');
				break;
				case 'e': $output = get_bloginfo('name') . $sep . wp_title($sep,false,true) . get_bloginfo('description');
				break;
				case 'search':  $output = get_bloginfo('name') . wp_title($sep,false,false); // Search is hardcoded
				break;
				case 'wooseo':  $output = $wooseo_title; // WooSEO Title
				break;
				case 'aioseo':  $output = $aio_title; // All-in-One Title
				break;
				case 'headspace':  $output = $headspace_title; // Headspace Title
				break;
			}
			if(is_paged()){
				$paged_var = get_query_var('paged');
				if(get_option('seo_woo_paged_var_pos') == 'after'){
					$output .= $sep . get_option('seo_woo_paged_var') . ' ' . $paged_var;
				} else {
					$output = get_option('seo_woo_paged_var') . ' ' . $paged_var . $sep . $output;
				}
			}
			$output = stripslashes($output);
			
			if(empty($output)) { wp_title(); }
			else { echo $output; }
			
		}
	}
	else {

		if ( is_home() ) { echo get_bloginfo('name') . $sep . get_bloginfo('description'); } 
		elseif ( is_search() ) { echo get_bloginfo('name') . $sep . __('Search Results', 'woothemes');  }  
		elseif ( is_author() ) { echo get_bloginfo('name') . $sep . __('Author Archives', 'woothemes');  }  
		elseif ( is_single() ) { echo wp_title($sep,true,true) . get_bloginfo('name');  }
		elseif ( is_page() ) {  echo get_bloginfo('name'); wp_title($sep,true,false);  }
		elseif ( is_category() ) { echo get_bloginfo('name') . $sep . __('Category Archive', 'woothemes') . $sep . single_cat_title('',false);  }
		elseif ( is_tax() ) { echo get_bloginfo('name') . $sep . __($taxonomy_top_level_item.' Archive', 'woothemes') . $sep . $taxonomy_nice_name;  }   
		elseif ( is_day() ) { echo get_bloginfo('name') . $sep . __('Daily Archive', 'woothemes') . $sep . get_the_time('jS F, Y');  }
		elseif ( is_month() ) { echo get_bloginfo('name') . $sep . __('Monthly Archive', 'woothemes') . $sep . get_the_time('F');  }
		elseif ( is_year() ) { echo get_bloginfo('name') . $sep . __('Yearly Archive', 'woothemes') . $sep . get_the_time('Y');  }
		elseif ( is_tag() ) {  echo get_bloginfo('name') . $sep . __('Tag Archive', 'woothemes') . $sep . single_tag_title('',false); }
	
	}
}

/*-----------------------------------------------------------------------------------*/
/* woo_meta() */
/*-----------------------------------------------------------------------------------*/


function woo_meta(){
		global $post;
		global $wpdb;
		if(!empty($post)){
			$post_id = $post->ID;
		}
		
		// Basic Output
		echo '<meta http-equiv="Content-Type" content="'. get_bloginfo('html_type') .'; charset='. get_bloginfo('charset') .'" />' . "\n";
		
		// Under SETTIGNS > PRIVACY in the WordPress backend
		if ( get_option('blog_public') == 0 ) { return; }
		
		//3rd Party Plugins
		$use_third_party_data = false;
		if(get_option('seo_woo_use_third_party_data') == 'true'){
			$use_third_party_data = true;
		}
		
		if(	(class_exists('All_in_One_SEO_Pack') OR class_exists('Headspace_Plugin')) AND 
		( $use_third_party_data == true )) { return; }
		
		// Robots
		if(!class_exists('All_in_One_SEO_Pack') AND !class_exists('Headspace_Plugin'))
		{
			$index = 'index';
			$follow = 'nofollow';
			
			if ( is_category() && get_option('seo_woo_meta_indexing_category') != 'true' ) { $index = 'noindex'; }  
			elseif ( is_tag() && get_option('seo_woo_meta_indexing_tag') != 'true') { $index = 'noindex'; }
			elseif ( is_search() && get_option('seo_woo_meta_indexing_search') != 'true' ) { $index = 'noindex'; }  
			elseif ( is_author() && get_option('seo_woo_meta_indexing_author') != 'true') { $index = 'noindex'; }  
			elseif ( is_date() && get_option('seo_woo_meta_indexing_date') != 'true') { $index = 'noindex'; }
			
			// Set default to follow			
			if ( get_option('seo_woo_meta_single_follow') == 'true' )
				$follow = 'follow';  
	
			// Set individual post/page to follow/unfollow
			if ( is_singular() ) {
				if ( $follow == 'follow' AND get_post_meta($post->ID,'seo_follow',true) == 'true') 
					$follow = 'nofollow';  
				elseif ( $follow == 'nofollow' AND get_post_meta($post->ID,'seo_follow',true) == 'true') 
					$follow = 'follow';  
			}							
						
			if(is_singular() && get_post_meta($post->ID,'seo_noindex',true) == 'true') { $index = 'noindex';  }
			
			echo '<meta name="robots" content="'. $index .', '. $follow .'" />' . "\n";
		}
		
		/* Description */
		$description = '';
		
		$home_desc_option = get_option('seo_woo_meta_home_desc');
		$singular_desc_option = get_option('seo_woo_meta_single_desc');
		
		//Check if there is a custom value added to post meta
		$wooseo_desc = get_post_meta($post->ID,'seo_description',true); //WooSEO
		$aio_desc = get_post_meta($post->ID,'_aioseop_description',true); //All-in-One SEO
		$headspace_desc = get_post_meta($post->ID,'_headspace_description',true); //Headspace SEO
	
		//Singular setup
		if(!empty($aio_desc) AND $use_third_party_data) {
			$singular_desc_option = 'aioseo';
		} elseif(!empty($headspace_desc) AND $use_third_party_data) {
			$singular_desc_option = 'headspace';
		}

		
		if(is_home() OR is_front_page()){
			switch($home_desc_option){
				case 'a': $description = '';
				break;
				case 'b': $description = get_bloginfo('description');
				break;
				case 'c': $description = get_option('seo_woo_meta_home_desc_custom');
				break;
			}
		}
		elseif(is_singular()){
			
			switch($singular_desc_option){
				case 'a': $description = '';
				break;
				case 'b': $description = trim(strip_tags($wooseo_desc));
				break; 
				case 'c': 
	
    				if(is_single()){
    					 $posts = get_posts("p=$post_id");
    				}
    				if(is_page()){
    					 $posts = get_posts("page_id=$post_id&post_type=page");
    				}
					foreach($posts as $post){
   						setup_postdata($post);	
						$post_content =  get_the_excerpt();
						if(empty($post_content)){
							$post_content = get_the_content();
						}
					}
					// $post_content = htmlentities(trim(strip_tags(strip_shortcodes($post_content))), ENT_QUOTES, 'UTF-8'); // Replaced with line below to accommodate special characters. // 2010-11-15.
					// $post_content = html_entity_decode(trim(strip_tags(strip_shortcodes($post_content))), ENT_QUOTES, 'UTF-8'); // Replaced to fix PHP4 compatibility issue. // 2010-12-09.
					// $post_content = utf8_decode( trim( strip_tags( strip_shortcodes( $post_content ) ) ) );
					// $post_content = html_entity_decode( trim( strip_tags( strip_shortcodes( $post_content ) ) ) );
					// $post_content = esc_html( htmlspecialchars ( strip_shortcodes( $post_content ) ) );
					
					$post_content = esc_attr( strip_tags( strip_shortcodes( $post_content ) ) );
					
					$description = woo_text_trim($post_content,30);
					
				break;
				case 'aioseo':  $description = $aio_desc; // All-in-One Title
				break;
				case 'headspace':  $description = $headspace_desc; // Headspace Title
				break;
				
			}			
		}
		
		if(empty($description) AND get_option('seo_woo_meta_single_desc_sitewide') == 'true'){
			$description = get_option('seo_woo_meta_single_desc_custom');
		}
		
		
		// $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); // Replaced with line below to accommodate special characters. // 2010-11-15.
		$description = esc_attr( $description );
		$description = stripslashes($description);
		
		// Faux-htmlentities using an array of key => value pairs.
		// TO DO: Clean-up and move to a re-usable function.
		$faux_htmlentities = array(
								'& ' => '&amp; ', 
								'<' => '&lt;', 
								'>' => '&gt;'
							 );
		
		foreach ( $faux_htmlentities as $old => $new ) {
		
			$description = str_replace( $old, $new, $description );
		
		} // End FOREACH Loop
		
		if(!empty($description)){
			echo '<meta name="description" content="'.$description.'" />' . "\n";
		}
		
		/* Keywords */
		$keywords = '';
		
		$home_key_option = get_option('seo_woo_meta_home_key');
		$singular_key_option = get_option('seo_woo_meta_single_key');
		
		//Check if there is a custom value added to post meta
		$wooseo_keywords = get_post_meta($post->ID,'seo_keywords',true); //WooSEO
		$aio_keywords = get_post_meta($post->ID,'_aioseop_keywords',true); //All-in-One SEO
		$headspace_keywords = get_post_meta($post->ID,'_headspace_keywords',true); //Headspace SEO
		
		//Singular setup
		
		if(!empty($aio_keywords) AND $use_third_party_data) {
			$singular_key_option = 'aioseo';
		} elseif(!empty($headspace_keywords) AND $use_third_party_data) {
			$singular_key_option = 'headspace';
		}	
			
		if(is_home() OR is_front_page()){
			switch($home_key_option){
				case 'a': $keywords = '';
				break;
				case 'c': $keywords = get_option('seo_woo_meta_home_key_custom');
				break;
			}
		}
		elseif(is_singular()){
			
			switch($singular_key_option){
				case 'a': $keywords = '';
				break;
				case 'b': $keywords = $wooseo_keywords;
				break;
				case 'c': 
					
					$the_keywords = array(); 
					//Tags
					if(get_the_tags($post->ID)){ 
						foreach(get_the_tags($post->ID) as $tag) {
							$tag_name = $tag->name; 
							$the_keywords[] = strtolower($tag_name);
						}
					}
					//Cats
					if(get_the_category($post->ID)){ 
						foreach(get_the_category($post->ID) as $cat) {
							$cat_name = $cat->name; 
							$the_keywords[] = strtolower($cat_name);
						}
					}
					//Other Taxonomies
					$all_taxonomies = get_taxonomies();
					$addon_taxonomies = array();
					if(!empty($all_taxonomies)){
						foreach($all_taxonomies as $key => $taxonomies){
							if(	$taxonomies != 'category' AND 
								$taxonomies != 'post_tag' AND 
								$taxonomies != 'nav_menu' AND
								$taxonomies != 'link_category'){
								$addon_taxonomies[] = $taxonomies;
							}
						}
					}
					$addon_terms = array();
					if(!empty($addon_taxonomies)){
						foreach($addon_taxonomies as $taxonomies){
							$addon_terms[] = get_the_terms($post->ID, $taxonomies);
						}
					}
					if(!empty($addon_terms)){
						 foreach($addon_terms as $addon){
						 	if(!empty($addon)){
						 		foreach($addon as $term){
						 			$the_keywords[] = strtolower($term->name);
						 		}
						 	}
						 }
					}
					$keywords = implode(",",$the_keywords);
				break;
				case 'aioseo':  $keywords = $aio_keywords; // All-in-One Title
				break;
				case 'headspace':  $keywords = $headspace_keywords; // Headspace Title
				break;
				}
		}
		
		if(empty($keywords) AND get_option('seo_woo_meta_single_key_sitewide') == 'true'){
			$keywords = get_option('seo_woo_meta_single_key_custom');
		}
		
		$keywords = htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8');
		$keywords = stripslashes($keywords);

		
		if(!empty($keywords)){
			echo '<meta name="keywords" content="'.$keywords.'" />' . "\n";
		}
		
}


//Add Post Custom Settings
add_action('admin_head','seo_add_custom');
		
function seo_add_custom() {

		$seo_template = array();
		
		$seo_woo_wp_title = get_option('seo_woo_wp_title');
		$seo_woo_meta_single_desc = get_option('seo_woo_meta_single_desc');
		$seo_woo_meta_single_key = get_option('seo_woo_meta_single_key');
		
		// a = off
		if( $seo_woo_wp_title != 'true' OR $seo_woo_meta_single_desc == 'a' OR $seo_woo_meta_single_key == 'a') {
			
			$output = "";
			if ( $seo_woo_wp_title != 'true' )
				$output .= "Custom Page Titles, ";
			if ( $seo_woo_meta_single_desc == 'a' )
				$output .= "Custom Descriptions, ";
			if ( $seo_woo_meta_single_key == 'a' )
				$output .= "Custom Keywords";			
				
			$output = rtrim($output, ", ");
			
			$desc = 'Additional SEO custom fields available: <strong>'.$output.'</strong>. Go to <a href="'.admin_url('admin.php?page=woothemes_seo').'">SEO Settings</a> page to activate.';
			
		} else {
			$desc = 'Go to <a href="'.admin_url('admin.php?page=woothemes_seo').'">SEO Settings</a> page for more SEO options.';
		}
		
		$seo_template[] = array (	"name"  => "seo_info_1",
										"std" => "",
										"label" => "SEO ",
										"type" => "info",
										"desc" => $desc);

		// Change checkbox depending on "Add meta for Posts & Pages to 'follow' by default" checkbox value.
		
		$followstatus = get_option( 'seo_woo_meta_single_follow' );

		if ( $followstatus != "true" ) { 

			$seo_template[] = array (	"name"  => "seo_follow", 
											"std" => 'false', 
											"label" => "SEO - Set follow",
											"type" => "checkbox",
											"desc" => "Make links from this post/page <strong>followable</strong> by search engines.");
										
		} else {
		
			$seo_template[] = array (	"name"  => "seo_follow", 
											"std" => 'false', 
											"label" => "SEO - Set nofollow",
											"type" => "checkbox",
											"desc" => "Make links from this post/page <strong>not followable</strong> by search engines.");
		
		} // End IF Statement
		
		$seo_template[] = array (	"name"  => "seo_noindex",
										"std" => "false",
										"label" => "SEO - Noindex",
										"type" => "checkbox",
										"desc" => "Set the Page/Post to not be indexed by a search engines.");

		if( get_option('seo_woo_wp_title') == 'true'){
		$seo_template[] = array (	"name"  => "seo_title",
										"std" => "",
										"label" => "SEO - Custom Page Title",
										"type" => "text",
										"desc" => "Add a custom title for this post/page.");
		}
		
		if( get_option('seo_woo_meta_single_desc') == 'b'){								
		$seo_template[] = array (	"name"  => "seo_description",
										"std" => "",
										"label" => "SEO - Custom Description",
										"type" => "textarea",
										"desc" => "Add a custom meta description for this post/page.");
		}
		
		if( get_option('seo_woo_meta_single_key') == 'b'){			
		$seo_template[] = array (	"name"  => "seo_keywords",
										"std" => "",
										"label" => "SEO - Custom Keywords",
										"type" => "text",
										"desc" => "Add a custom meta keywords for this post/page. (comma seperated)");	
		}
		
		//3rd Party Plugins
		if(get_option('seo_woo_use_third_party_data') == 'true'){
			$use_third_party_data = true;
		} else {
			$use_third_party_data = false;
		}
		
		if(	(class_exists('All_in_One_SEO_Pack') OR class_exists('Headspace_Plugin')) AND 
		( $use_third_party_data == true )) { 
			delete_option('woo_custom_seo_template'); 
		}
		else {

			update_option('woo_custom_seo_template',$seo_template);
			
		}	

}

/*-----------------------------------------------------------------------------------*/
/* Woo Text Trimmer */
/*-----------------------------------------------------------------------------------*/

if ( !function_exists('woo_text_trim') ) {
	function woo_text_trim($text, $words = 50)
	{ 
		$matches = preg_split("/\s+/", $text, $words + 1);
		$sz = count($matches);
		if ($sz > $words) 
		{
			unset($matches[$sz-1]);
			return implode(' ',$matches)." ...";
		}
		return $text;
	}
}

/*-----------------------------------------------------------------------------------*/
/* Google Webfonts Array */
/* Documentation:
/*
/* name: The name of the Google Font.
/* variant: The Google Font API variants available for the font.
/*-----------------------------------------------------------------------------------*/

// Available Google webfont names
$google_fonts = array(	array('name' => "Cantarell", 'variant' => ':r,b,i,bi'),
						array('name' => "Cardo", 'variant' => ''),
						array('name' => "Crimson Text", 'variant' => ''),
						array('name' => "Droid Sans", 'variant' => ':r,b'),
						array('name' => "Droid Sans Mono", 'variant' => ''),
						array('name' => "Droid Serif", 'variant' => ':r,b,i,bi'),
						array('name' => "IM Fell DW Pica", 'variant' => ':r,i'),
						array('name' => "Inconsolata", 'variant' => ''),
						array('name' => "Josefin Sans Std Light", 'variant' => ''),
						array('name' => "Josefin Slab", 'variant' => ':r,b,i,bi'),
						array('name' => "Lobster", 'variant' => ''),
						array('name' => "Molengo", 'variant' => ''),
						array('name' => "Nobile", 'variant' => ':r,b,i,bi'),
						array('name' => "OFL Sorts Mill Goudy TT", 'variant' => ':r,i'),
						array('name' => "Old Standard TT", 'variant' => ':r,b,i'),
						array('name' => "Reenie Beanie", 'variant' => ''),
						array('name' => "Tangerine", 'variant' => ':r,b'),
						array('name' => "Vollkorn", 'variant' => ':r,b'),
						array('name' => "Yanone Kaffeesatz", 'variant' => ':r,b'),
						array('name' => "Cuprum", 'variant' => ''),
						array('name' => "Neucha", 'variant' => ''),
						array('name' => "Neuton", 'variant' => ''),
						array('name' => "PT Sans", 'variant' => ':r,b,i,bi'),
						array('name' => "Philosopher", 'variant' => ''),
						array('name' => "Allerta", 'variant' => ''),	
						array('name' => "Allerta Stencil", 'variant' => ''),	
						array('name' => "Arimo", 'variant' => ':r,b,i,bi'),	
						array('name' => "Arvo", 'variant' => ':r,b,i,bi'),	
						array('name' => "Bentham", 'variant' => ''),	
						array('name' => "Coda", 'variant' => ':800'),	
						array('name' => "Cousine", 'variant' => ''),	
						array('name' => "Covered By Your Grace", 'variant' => ''),	
			 			array('name' => "Geo", 'variant' => ''),	 
						array('name' => "Just Me Again Down Here", 'variant' => ''),	
						array('name' => "Puritan", 'variant' => ':r,b,i,bi'),	
						array('name' => "Raleway", 'variant' => ':100'),	
						array('name' => "Tinos", 'variant' => ':r,b,i,bi'),	
						array('name' => "UnifrakturCook", 'variant' => ':bold'),	
						array('name' => "UnifrakturMaguntia", 'variant' => ''),
						array('name' => "Mountains of Christmas", 'variant' => ''),
						array('name' => "Lato", 'variant' => ''),
						array('name' => "Orbitron", 'variant' => ':r,b,i,bi'),
						array('name' => "Allan", 'variant' => ':bold'),
						array('name' => "Anonymous Pro", 'variant' => ':r,b,i,bi'),
						array('name' => "Copse", 'variant' => ''),
						array('name' => "Kenia", 'variant' => ''),
						array('name' => "Ubuntu", 'variant' => ':r,b,i,bi'),						
						array('name' => "Vibur", 'variant' => ''),
						array('name' => "Sniglet", 'variant' => ':800'),
						array('name' => "Syncopate", 'variant' => ''),
						array('name' => "Cabin", 'variant' => ':b'),						
						array('name' => "Merriweather", 'variant' => ''),						
						array('name' => "Just Another Hand", 'variant' => ''),
						array('name' => "Kristi", 'variant' => ''),						
						array('name' => "Corben", 'variant' => ':b'),						
						array('name' => "Gruppo", 'variant' => ''),						
						array('name' => "Buda", 'variant' => ':light'),						
						array('name' => "Lekton", 'variant' => ''),						
						array('name' => "Luckiest Guy", 'variant' => ''),						
						array('name' => "Crushed", 'variant' => ''),						
						array('name' => "Chewy", 'variant' => ''),						
						array('name' => "Coming Soon", 'variant' => ''),						
						array('name' => "Crafty Girls", 'variant' => ''),						
						array('name' => "Fontdiner Swanky", 'variant' => ''),						
						array('name' => "Permanent Marker", 'variant' => ''),						
						array('name' => "Rock Salt", 'variant' => ''),						
						array('name' => "Sunshiney", 'variant' => ''),						
						array('name' => "Unkempt", 'variant' => ''),						
						array('name' => "Calligraffitti", 'variant' => ''),						
						array('name' => "Cherry Cream Soda", 'variant' => ''),						
						array('name' => "Homemade Apple", 'variant' => ''),						
						array('name' => "Irish Growler", 'variant' => ''),						
						array('name' => "Kranky", 'variant' => ''),						
						array('name' => "Schoolbell", 'variant' => ''),						
						array('name' => "Slackey", 'variant' => ''),						
						array('name' => "Walter Turncoat", 'variant' => ''),				
						array('name' => "Radley", 'variant' => ''),					
						array('name' => "Meddon", 'variant' => ''),					
						array('name' => "Kreon", 'variant' => ':r,b'),					
						array('name' => "Dancing Script", 'variant' => '')					
);


/*-----------------------------------------------------------------------------------*/
/* Google Webfonts Stylesheet Generator */
/*-----------------------------------------------------------------------------------*/
/* 
INSTRUCTIONS: Needs to be loaded for the Google Fonts options to work for font options. Add this to
the specific themes includes/theme-actions.php or functions.php:

add_action('wp_head', 'woo_google_webfonts');				
*/

if (!function_exists("woo_google_webfonts")) {
	function woo_google_webfonts() { 

		global $google_fonts;				
		$fonts = '';
		$output = ''; 

		// Setup Woo Options array
		global $woo_options; 
		
		// Go through the options
		if ( !empty($woo_options) ) {
		
			foreach ( $woo_options as $option ) {
			
				// Check if option has "face" in array
				if ( is_array($option) && isset($option['face']) ) {
									
					// Go through the google font array
					foreach ($google_fonts as $font) {
						
						// Check if the google font name exists in the current "face" option
						if ( $option['face'] == $font['name'] AND !strstr($fonts, $font['name']))
							
							// Add google font to output
							$fonts .= $font['name'].$font['variant']."|";			
					}
				}
			
			}
			
			// Output google font css in header			
			if ( $fonts ) {
				$fonts = str_replace(" ","+",$fonts);	
				$output .= "\n<!-- Google Webfonts -->\n";
				$output .= '<link href="http://fonts.googleapis.com/css?family=' . $fonts .'" rel="stylesheet" type="text/css" />'."\n\n";
				$output = str_replace('|"','"',$output);
				
				echo $output;
			}
		}
				
	}
}


/*-----------------------------------------------------------------------------------*/
/* Enable Home link in WP Menus
/*-----------------------------------------------------------------------------------*/
if ( !function_exists('woo_home_page_menu_args') ) {
	function woo_home_page_menu_args( $args ) {
		$args['show_home'] = true;
		return $args;
	}
	add_filter( 'wp_page_menu_args', 'woo_home_page_menu_args' );
}

/*-----------------------------------------------------------------------------------*/
/* Buy Themes page
/*-----------------------------------------------------------------------------------*/
if ( !function_exists('woothemes_more_themes_page') ) {
	function woothemes_more_themes_page(){
        ?>
        <div class="wrap themes-page">
	        <h2>More WooThemes</h2>
	        
			<?php // Get RSS Feed(s)
	        include_once(ABSPATH . WPINC . '/feed.php');
	        $rss = fetch_feed('http://www.woothemes.com/?feed=more_themes');			
	        // If the RSS is failed somehow.
	        if ( is_wp_error($rss) ) {
	            $error = $rss->get_error_code();
	            if($error == 'simplepie-error') {
	                //Simplepie Error
	                echo "<div class='updated fade'><p>An error has occured with the RSS feed. (<code>". $error ."</code>)</p></div>";
	            }
	            return;
	         } 
	        ?>
	        <div class="info">
		        <a href="http://www.woothemes.com/pricing/">Join the WooThemes Club</a>
		        <a href="http://www.woothemes.com/themes/">Themes Gallery</a>
		        <a href="http://showcase.woothemes.com/">Theme Showcase</a>
	        </div>
	        
	        <?php
	        
	        $maxitems = $rss->get_item_quantity(30); 
	        $items = $rss->get_items(0, 30);
	        
	        ?>
	        <ul class="themes">
	        <?php if (empty($items)) echo '<li>No items</li>';
	        else
	        foreach ( $items as $item ) : ?>
	            <li class="theme">
	                <?php echo $item->get_description();?>
	            </li>
	        <?php 
	        endforeach; ?>
	        </ul>
        </div>
        
        <?php
	}
}

/*---------------------------------------------------------------------------------*/
/* Detects the Charset of String and Converts it to UTF-8 */
/*---------------------------------------------------------------------------------*/
if ( !function_exists('woo_encoding_convert') ) {
	function woo_encoding_convert($str_to_convert) {
		if ( function_exists('mb_detect_encoding') ) {
			$str_lang_encoding = mb_detect_encoding($str_to_convert);
			//if no encoding detected, assume UTF-8
			if (!$str_lang_encoding) {
				//UTF-8 assumed
				$str_lang_converted_utf = $str_to_convert;
			} else {
				//Convert to UTF-8
				$str_lang_converted_utf = mb_convert_encoding($str_to_convert, 'UTF-8', $str_lang_encoding);
			}
		} else {
			$str_lang_converted_utf = $str_to_convert;
		}
	
		return $str_lang_converted_utf;
	}
}

/*---------------------------------------------------------------------------------*/
/* WP Login logo */
/*---------------------------------------------------------------------------------*/
if ( !function_exists('woo_custom_login_logo') ) {
	function woo_custom_login_logo() {
		$logo = get_option('framework_woo_custom_login_logo');
	    $dimensions = getimagesize( $logo );
		echo '<style type="text/css">h1 a { background-image:url('.$logo.'); height: '.$dimensions[1].'px ; }</style>';
	}
	if ( get_option('framework_woo_custom_login_logo') ) 
		add_action('login_head', 'woo_custom_login_logo');
}

/*-----------------------------------------------------------------------------------*/
/* THE END */
/*-----------------------------------------------------------------------------------*/
?>