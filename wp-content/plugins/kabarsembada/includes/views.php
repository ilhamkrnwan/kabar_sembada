<?php
/**
 * Post views counter.
 *
 * Each article page pings a REST route once per browser session; the count
 * lives in post meta so Query Loop can order by it. The ping runs from the
 * browser, so full-page caches (LiteSpeed) do not hide visits.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

const KABARSEMBADA_VIEWS_KEY = 'kabarsembada_views';

/**
 * Register the views meta. Readable in REST, editable only by editors (the
 * importer seeds it from the old site's counter).
 *
 * @return void
 */
function kabarsembada_register_views_meta() {
	register_post_meta(
		'post',
		KABARSEMBADA_VIEWS_KEY,
		array(
			'type'              => 'integer',
			'single'            => true,
			'default'           => 0,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => static function ( $allowed, $meta_key, $post_id ) {
				return current_user_can( 'edit_post', $post_id );
			},
		)
	);
}
add_action( 'init', 'kabarsembada_register_views_meta' );

/**
 * Views of a post.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function kabarsembada_get_views( $post_id ) {
	return (int) get_post_meta( $post_id, KABARSEMBADA_VIEWS_KEY, true );
}

/**
 * Count one view.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function kabarsembada_rest_count_view( $request ) {
	$post = get_post( (int) $request['id'] );

	if ( ! $post || 'post' !== $post->post_type || 'publish' !== $post->post_status ) {
		return new WP_Error( 'kabarsembada_not_found', __( 'Artikel tidak ditemukan.', 'kabarsembada' ), array( 'status' => 404 ) );
	}

	// ponytail: read-then-write, so two pings in the same millisecond can count
	// once. Fine for a popularity ranking; move to an atomic UPDATE if exact
	// numbers ever matter.
	$views = kabarsembada_get_views( $post->ID ) + 1;
	update_post_meta( $post->ID, KABARSEMBADA_VIEWS_KEY, $views );

	return rest_ensure_response( array( 'views' => $views ) );
}

/**
 * Register the ping route.
 *
 * @return void
 */
function kabarsembada_register_views_route() {
	register_rest_route(
		'kabarsembada/v1',
		'/views/(?P<id>\d+)',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'kabarsembada_rest_count_view',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'kabarsembada_register_views_route' );

/**
 * Ping on article pages, once per post per browser session.
 *
 * ponytail: dedupe is client-side (sessionStorage), so a script can inflate a
 * count. Add a server-side throttle if the ranking starts getting gamed.
 *
 * @return void
 */
function kabarsembada_enqueue_views_ping() {
	if ( ! is_singular( 'post' ) || is_preview() ) {
		return;
	}

	$url = rest_url( 'kabarsembada/v1/views/' . get_queried_object_id() );

	wp_register_script( 'kabarsembada-views', false, array(), KABARSEMBADA_VERSION, true );
	wp_enqueue_script( 'kabarsembada-views' );
	wp_add_inline_script(
		'kabarsembada-views',
		sprintf(
			'(function(u){var k="ks-v:"+u;try{if(sessionStorage.getItem(k))return;sessionStorage.setItem(k,1)}catch(e){}fetch(u,{method:"POST",keepalive:true}).catch(function(){})})(%s);',
			wp_json_encode( esc_url_raw( $url ) )
		)
	);
}
add_action( 'wp_enqueue_scripts', 'kabarsembada_enqueue_views_ping' );
