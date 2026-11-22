<?php
/**
 * Register the custom blocks built to build/blocks/<name>.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

/**
 * Block directories (under build/blocks) registered by this module.
 *
 * @return string[]
 */
function kabarsembada_blocks() {
	return array( 'ticker', 'post-tabs', 'popular', 'share', 'search' );
}

/**
 * Register the blocks from their build metadata.
 *
 * @return void
 */
function kabarsembada_register_blocks() {
	foreach ( kabarsembada_blocks() as $name ) {
		$dir = KABARSEMBADA_PATH . 'build/blocks/' . $name;

		if ( file_exists( $dir . '/block.json' ) ) {
			register_block_type( $dir );
		}
	}
}
add_action( 'init', 'kabarsembada_register_blocks' );

/**
 * Version a block's assets by build time, so a rebuilt stylesheet is never
 * hidden by a cached copy of the same block.json version.
 *
 * @param array $metadata Block metadata.
 * @return array
 */
function kabarsembada_block_asset_version( $metadata ) {
	if ( isset( $metadata['name'], $metadata['file'] ) && 0 === strpos( $metadata['name'], 'kabarsembada/' ) ) {
		$built = (int) @filemtime( $metadata['file'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged

		if ( $built ) {
			$metadata['version'] = KABARSEMBADA_VERSION . '.' . $built;
		}
	}

	return $metadata;
}
add_filter( 'block_type_metadata', 'kabarsembada_block_asset_version' );
