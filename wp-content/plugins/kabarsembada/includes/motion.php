<?php
/**
 * Motion for core/group and core/cover, run by GSAP on the front end.
 *
 * Saved markup never changes: classes and data attributes are added at render
 * time, so disabling the plugin leaves core content intact. Content is visible
 * by default; the runtime hides an element only right before animating it, so
 * nothing stays hidden if the script fails or motion is reduced.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

/**
 * Blocks that support motion.
 *
 * @return string[]
 */
function kabarsembada_motion_blocks() {
	return array( 'core/group', 'core/cover' );
}

/**
 * Allowed motion slugs.
 *
 * @return string[]
 */
function kabarsembada_motion_allowed() {
	return array( 'fade', 'fade-up', 'fade-down', 'slide-left', 'slide-right', 'zoom' );
}

/**
 * Enqueue the editor entry (motion panel, query variations).
 *
 * @return void
 */
function kabarsembada_enqueue_editor_assets() {
	$asset_file = KABARSEMBADA_PATH . 'build/index.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = require $asset_file;

	wp_enqueue_script(
		'kabarsembada-editor',
		KABARSEMBADA_URL . 'build/index.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'kabarsembada_enqueue_editor_assets' );

/**
 * Enqueue the GSAP runtime only on pages that use motion.
 *
 * @return void
 */
function kabarsembada_motion_enqueue() {
	$asset_file = KABARSEMBADA_PATH . 'build/motion-view.asset.php';

	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = require $asset_file;

	wp_enqueue_script(
		'kabarsembada-motion',
		KABARSEMBADA_URL . 'build/motion-view.js',
		$asset['dependencies'],
		$asset['version'],
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}

/**
 * Add motion data attributes at render time.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Parsed block.
 * @return string
 */
function kabarsembada_motion_render_block( $block_content, $block ) {
	if ( empty( $block['blockName'] ) || ! in_array( $block['blockName'], kabarsembada_motion_blocks(), true ) ) {
		return $block_content;
	}

	$attrs  = isset( $block['attrs'] ) ? $block['attrs'] : array();
	$motion = isset( $attrs['ksMotion'] ) ? sanitize_key( $attrs['ksMotion'] ) : '';

	if ( ! in_array( $motion, kabarsembada_motion_allowed(), true ) ) {
		return $block_content;
	}

	$duration = isset( $attrs['ksDuration'] ) ? max( 100, min( 2000, (int) $attrs['ksDuration'] ) ) : 700;
	$delay    = isset( $attrs['ksDelay'] ) ? max( 0, min( 2000, (int) $attrs['ksDelay'] ) ) : 0;
	$trigger  = isset( $attrs['ksTrigger'] ) && 'load' === $attrs['ksTrigger'] ? 'load' : 'scroll';

	$processor = new WP_HTML_Tag_Processor( $block_content );

	if ( ! $processor->next_tag() ) {
		return $block_content;
	}

	$processor->add_class( 'ks-motion' );
	$processor->set_attribute( 'data-ks-motion', $motion );
	$processor->set_attribute( 'data-ks-trigger', $trigger );
	$processor->set_attribute( 'data-ks-duration', (string) $duration );
	$processor->set_attribute( 'data-ks-delay', (string) $delay );

	if ( ! empty( $attrs['ksStagger'] ) ) {
		$processor->set_attribute( 'data-ks-stagger', '1' );
	}

	kabarsembada_motion_enqueue();

	return $processor->get_updated_html();
}
add_filter( 'render_block', 'kabarsembada_motion_render_block', 10, 2 );
