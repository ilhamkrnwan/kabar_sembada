<?php
/**
 * Media: modern formats for generated image sizes.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

/**
 * Generate the resized copies of new uploads as AVIF (photos) or WebP.
 *
 * Only the sub-sizes WordPress creates on upload change; the original file is
 * kept as uploaded. Filter `kabarsembada_image_output_formats` to change or
 * disable the mapping (return an empty array).
 *
 * @param array $formats Source mime type => output mime type.
 * @return array
 */
function kabarsembada_image_output_formats( $formats ) {
	$map = array(
		'image/jpeg' => wp_image_editor_supports( array( 'mime_type' => 'image/avif' ) ) ? 'image/avif' : 'image/webp',
		'image/png'  => 'image/webp',
	);

	foreach ( $map as $from => $to ) {
		if ( ! wp_image_editor_supports( array( 'mime_type' => $to ) ) ) {
			unset( $map[ $from ] );
		}
	}

	return array_merge( $formats, apply_filters( 'kabarsembada_image_output_formats', $map ) );
}
add_filter( 'image_editor_output_format', 'kabarsembada_image_output_formats' );
