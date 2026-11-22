/**
 * Kabar Sembada build config.
 *
 * Entry points:
 * - blocks: every src/blocks/<name>/block.json (picked up by wp-scripts)
 * - index: editor extension (motion panel, Query Loop variations)
 * - motion-view: GSAP front-end runtime, enqueued only where motion is used
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const { getWebpackEntryPoints } = require( '@wordpress/scripts/utils/config' );

module.exports = {
	...defaultConfig,
	entry: {
		...getWebpackEntryPoints( 'script' )(),
		index: './src/index.js',
		'motion-view': './src/motion/view.js',
	},
};
