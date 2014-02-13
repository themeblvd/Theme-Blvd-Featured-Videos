<?php
/*
Plugin Name: Theme Blvd Featured Videos
Description: This plugins adds a post option to switch out featured images with a video. Note that your theme must be running Theme Blvd framework version 2.0.5+ for this plugin to work properly.
Version: 1.0.2
Author: Jason Bobich
Author URI: http://jasonbobich.com
License: GPL2
*/

/*
Copyright 2012 JASON BOBICH

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Add javascript to Edit Post page */

function themeblvd_fv_scripts() {
	global $pagenow;
	if( $pagenow == 'post-new.php' || $pagenow == 'post.php' )
		wp_enqueue_script( 'themeblvd_fv-scripts', plugins_url( 'assets/scripts.js' , __FILE__ ), array('jquery') );
}
add_action( 'admin_enqueue_scripts', 'themeblvd_fv_scripts' );

/* Add option to framework's Post Options meta box.  */

function themeblvd_fv_post_meta( $setup ) {
	$setup['options'][] = array(
		'id'		=> '_tb_fv_replace',
		'name' 		=> __( 'Replace featured image with video?', 'themeblvd' ),
		'desc'		=> __( 'In places where a featured image is supposed to show, it will be replaced with a video.', 'themeblvd' ),
		'type' 		=> 'radio',
		'std'		=> 'false',
		'options'	=> array(
			'true' => 'Replace featured image with Video.',
			'false' => 'No, leave it alone!'
		)
	);
	$setup['options'][] = array(
		'id'		=> '_tb_fv_url',
		'name' 		=> __( 'Video URL', 'themeblvd' ),
		'desc'		=> __( 'Enter in the URL to the video. The URL must be compatible with <a href="http://codex.wordpress.org/Embeds" target="_blank">WordPress\'s Embed guidelines</a>.', 'themeblvd' ),
		'type' 		=> 'text',
		'std'		=> ''
	);
	return $setup;
}
add_filter( 'themeblvd_post_meta', 'themeblvd_fv_post_meta' );

/* Add filter to framework's featured image output */

function themeblvd_fv_display_video( $input, $location, $size ) {

	global $post;
	global $_wp_additional_image_sizes;

	// Check for mini thumbnail
	if ( false !== strpos( $size, 'square_' ) ) {
		return $input;
	}

	$output = '';
	$style = '';
	$replace = get_post_meta( $post->ID, '_tb_fv_replace', true );

	if( $replace == 'true' ) {

		// Vieo URL
		$video_url = get_post_meta( $post->ID, '_tb_fv_url', true );

		if( $video_url ) {

			// For smaller, floated thumbnail sizes
			if ( $size == 'tb_small' ) {

				if ( isset( $_wp_additional_image_sizes[$size] ) ) {
					$width = $_wp_additional_image_sizes[$size]['width'];
					$height = $_wp_additional_image_sizes[$size]['height'];
				}

				if ( $width && $height ) {
					$style = sprintf( 'width: %spx; height: %spx;', $width, $height );
				}

			}

			// Attributes
			$size_class = $size;

			if ( $size_class == 'tb_small' ) {
				$size_class = 'small';
			}

			$output .= '<div class="featured-image-wrapper attachment-'.$size_class.' wp-post-image">';
			$output .= '<div class="featured-image">';
			$output .= '<div class="featured-image-inner" style="'.$style.'">';
			$output .= wp_oembed_get( $video_url );
			$output .= '</div><!-- .featured-image-inner (end) -->';
			$output .= '</div><!-- .featured-image (end) -->';
			$output .= '</div><!-- .featured-image-wrapper (end) -->';

		} else {

			$output = '<p class="warning">'.__( 'Oops! You forgot to input a video URL to replace the featured image with.', 'themeblvd' ).'</p>';

		}
	} else {

		// Return original input if video option wasn't selected.
		return $input;

	}

	return $output;
}
add_filter( 'themeblvd_post_thumbnail', 'themeblvd_fv_display_video', 10, 3 );