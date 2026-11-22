<?php
/**
 * REST endpoint behind the search overlay (kabarsembada/search block).
 *
 * Public and read-only; it only returns published posts.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shape one post for the search overlay.
 *
 * @param WP_Post $post Post object.
 * @return array{id:int,title:string,category:string,url:string}
 */
function kabarsembada_search_item( $post ) {
	$cat = kabarsembada_primary_category( $post );

	return array(
		'id'       => (int) $post->ID,
		'title'    => html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ),
		'category' => $cat ? $cat->name : '',
		'url'      => get_permalink( $post ),
	);
}

/**
 * Live search.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response
 */
function kabarsembada_rest_search( $request ) {
	$posts = get_posts(
		array(
			's'                   => $request->get_param( 'q' ),
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 8,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	return rest_ensure_response(
		array( 'results' => array_map( 'kabarsembada_search_item', $posts ) )
	);
}

/**
 * Register the route.
 *
 * @return void
 */
function kabarsembada_register_search_route() {
	register_rest_route(
		'kabarsembada/v1',
		'/search',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'kabarsembada_rest_search',
			'permission_callback' => '__return_true',
			'args'                => array(
				'q' => array(
					'type'              => 'string',
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => static function ( $value ) {
						return is_string( $value ) && strlen( trim( $value ) ) >= 2 && strlen( $value ) <= 100;
					},
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'kabarsembada_register_search_route' );
