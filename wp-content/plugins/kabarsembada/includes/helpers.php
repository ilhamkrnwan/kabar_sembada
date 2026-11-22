<?php
/**
 * Shared render helpers for the news blocks.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

/**
 * True the first time it is called with a key during a request.
 *
 * @param string $key Key.
 * @return bool
 */
function kabarsembada_once( $key ) {
	static $seen = array();

	if ( isset( $seen[ $key ] ) ) {
		return false;
	}

	$seen[ $key ] = true;

	return true;
}

/**
 * Whether the featured image file of a post is still present.
 *
 * Only local uploads are checked; offloaded or remote media is assumed present.
 * Imported posts may point at attachments whose files never made it over.
 *
 * @param WP_Post|int $post Post object or ID.
 * @return bool
 */
function kabarsembada_has_thumbnail( $post ) {
	$id  = get_post_thumbnail_id( $post );
	$url = $id ? wp_get_attachment_url( $id ) : '';

	if ( ! $url ) {
		return false;
	}

	$uploads = wp_get_upload_dir();

	if ( 0 !== strpos( $url, $uploads['baseurl'] ) ) {
		return true;
	}

	$file = get_attached_file( $id );

	return $file && file_exists( $file );
}

/**
 * First category of a post, or null.
 *
 * Prefers the Yoast primary category when the old site set one.
 *
 * @param WP_Post|int $post Post object or ID.
 * @return WP_Term|null
 */
function kabarsembada_primary_category( $post ) {
	$post    = get_post( $post );
	$primary = (int) get_post_meta( $post->ID, '_yoast_wpseo_primary_category', true );

	if ( $primary ) {
		$term = get_term( $primary, 'category' );

		if ( $term instanceof WP_Term ) {
			return $term;
		}
	}

	$terms = get_the_category( $post->ID );

	return $terms ? $terms[0] : null;
}

/**
 * Human relative time in Indonesian ("3 jam lalu"), or the date after a week.
 *
 * @param WP_Post|int $post Post object or ID.
 * @return string
 */
function kabarsembada_post_time( $post ) {
	$time = (int) get_post_time( 'U', true, $post );
	$diff = time() - $time;

	if ( $diff >= 0 && $diff < WEEK_IN_SECONDS ) {
		/* translators: %s: human time difference, e.g. "3 jam". */
		return sprintf( __( '%s lalu', 'kabarsembada' ), human_time_diff( $time ) );
	}

	return get_the_date( '', $post );
}

/**
 * Category and time line shown above card titles.
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function kabarsembada_render_meta( $post ) {
	$cat = kabarsembada_primary_category( $post );

	return sprintf(
		'<span class="ks-meta">%s<time datetime="%s">%s</time></span>',
		$cat ? '<span class="ks-meta__cat">' . esc_html( $cat->name ) . '</span><span class="ks-meta__dot" aria-hidden="true"></span>' : '',
		esc_attr( get_post_time( 'c', true, $post ) ),
		esc_html( kabarsembada_post_time( $post ) )
	);
}

/**
 * Featured image markup, or '' when the post has none.
 *
 * @param WP_Post $post Post object.
 * @param string  $size Image size.
 * @return string
 */
function kabarsembada_render_thumb( $post, $size = 'thumbnail' ) {
	if ( ! kabarsembada_has_thumbnail( $post ) ) {
		return '';
	}

	return '<span class="ks-thumb">' . get_the_post_thumbnail(
		$post,
		$size,
		array(
			'loading' => 'lazy',
			'alt'     => '',
		)
	) . '</span>';
}

/**
 * Compact news card: square thumb, meta, title. The whole card is the link.
 *
 * @param WP_Post $post    Post object.
 * @param string  $heading Heading tag (h2|h3|h4).
 * @return string
 */
function kabarsembada_render_mini_card( $post, $heading = 'h3' ) {
	$heading = in_array( $heading, array( 'h2', 'h3', 'h4' ), true ) ? $heading : 'h3';
	$thumb   = kabarsembada_render_thumb( $post );

	return sprintf(
		'<a class="ks-mini%1$s" href="%2$s">%3$s<div class="ks-mini__body">%4$s<%5$s class="ks-title">%6$s</%5$s></div></a>',
		$thumb ? ' has-thumb' : '',
		esc_url( get_permalink( $post ) ),
		$thumb, // Core image markup.
		kabarsembada_render_meta( $post ),
		$heading,
		esc_html( get_the_title( $post ) )
	);
}
