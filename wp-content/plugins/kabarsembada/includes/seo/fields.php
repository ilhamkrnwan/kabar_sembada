<?php
/**
 * Per-post SEO fields, edited in the editor's SEO panel (src/seo/panel.js).
 *
 * - kabarsembada_seo_description: meta description and share text.
 * - kabarsembada_primary_category: category shown on cards, breadcrumbs and
 *   schema when a post has more than one.
 *
 * The old site's Yoast values are copied into these keys by the migration, so
 * Yoast is never needed.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

const KABARSEMBADA_SEO_DESCRIPTION_KEY  = 'kabarsembada_seo_description';
const KABARSEMBADA_PRIMARY_CATEGORY_KEY = 'kabarsembada_primary_category';

/**
 * Register the SEO meta on posts.
 *
 * @return void
 */
function kabarsembada_register_seo_meta() {
	$auth = static function ( $allowed, $meta_key, $post_id ) {
		return current_user_can( 'edit_post', $post_id );
	};

	register_post_meta(
		'post',
		KABARSEMBADA_SEO_DESCRIPTION_KEY,
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => '',
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_textarea_field',
			'auth_callback'     => $auth,
		)
	);

	register_post_meta(
		'post',
		KABARSEMBADA_PRIMARY_CATEGORY_KEY,
		array(
			'type'              => 'integer',
			'single'            => true,
			'default'           => 0,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => $auth,
		)
	);
}
add_action( 'init', 'kabarsembada_register_seo_meta' );
