<?php
/**
 * Extra Query Loop modes, set as keys on the core/query `query` attribute.
 *
 * - ksOrder: "views"  → most read first (meta kabarsembada_views).
 * - ksDays: N         → only posts published in the last N days.
 * - ksRelated: true   → same category as the current article, never itself;
 *                       falls back to the latest when too few share a category.
 *
 * The editor registers block variations that preset these keys
 * (src/query/variations.js). The editor preview uses core REST and ignores
 * them, so it shows latest posts; the front end is exact.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

/**
 * Order query vars by views, most read first. Posts never viewed (no meta row)
 * stay in the list, after the viewed ones, newest first.
 *
 * @param array $query Query vars.
 * @return array
 */
function kabarsembada_order_by_views( $query ) {
	$query['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		'relation' => 'OR',
		'ks_views' => array(
			'key'     => KABARSEMBADA_VIEWS_KEY,
			'type'    => 'NUMERIC',
			'compare' => 'EXISTS',
		),
		array(
			'key'     => KABARSEMBADA_VIEWS_KEY,
			'compare' => 'NOT EXISTS',
		),
	);
	$query['orderby']    = array(
		'ks_views' => 'DESC',
		'date'     => 'DESC',
	);

	return $query;
}

/**
 * Apply the custom modes to Query Loop query vars.
 *
 * @param array    $query Query vars.
 * @param WP_Block $block Post Template block (receives the query as context).
 * @return array
 */
function kabarsembada_query_loop_vars( $query, $block ) {
	$attrs = isset( $block->context['query'] ) ? (array) $block->context['query'] : array();

	if ( isset( $attrs['ksOrder'] ) && 'views' === $attrs['ksOrder'] ) {
		$query = kabarsembada_order_by_views( $query );
	}

	$days = isset( $attrs['ksDays'] ) ? absint( $attrs['ksDays'] ) : 0;

	if ( $days ) {
		$query['date_query'] = array(
			array(
				'after'     => $days . ' days ago',
				'inclusive' => true,
			),
		);
	}

	if ( ! empty( $attrs['ksRelated'] ) && is_singular() ) {
		$post                         = get_queried_object();
		$query['post__not_in']        = array_merge( isset( $query['post__not_in'] ) ? (array) $query['post__not_in'] : array(), array( $post->ID ) );
		$query['ignore_sticky_posts'] = true;
		$categories            = wp_get_post_categories( $post->ID );

		if ( $categories ) {
			$wanted = isset( $query['posts_per_page'] ) ? (int) $query['posts_per_page'] : 3;
			$same   = get_posts(
				array(
					'post_type'      => $post->post_type,
					'category__in'   => $categories,
					'post__not_in'   => array( $post->ID ),
					'posts_per_page' => $wanted,
					'fields'         => 'ids',
				)
			);

			if ( count( $same ) >= $wanted ) {
				$query['category__in'] = $categories;
			}
		}
	}

	return $query;
}
add_filter( 'query_loop_block_query_vars', 'kabarsembada_query_loop_vars', 10, 2 );

/**
 * Shared WP_Query args for the plugin's own list blocks.
 *
 * @param array $args Overrides: number, order ('date'|'views'), days, category (slug), tag (slug), exclude (IDs).
 * @return WP_Post[]
 */
function kabarsembada_get_posts( $args = array() ) {
	$args  = wp_parse_args(
		$args,
		array(
			'number'   => 5,
			'order'    => 'date',
			'days'     => 0,
			'category' => '',
			'tag'      => '',
			'exclude'  => array(),
		)
	);
	$query = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, min( 20, (int) $args['number'] ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'post__not_in'        => array_map( 'intval', (array) $args['exclude'] ),
	);

	if ( 'views' === $args['order'] ) {
		$query = kabarsembada_order_by_views( $query );
	}

	if ( $args['days'] ) {
		$query['date_query'] = array(
			array(
				'after'     => absint( $args['days'] ) . ' days ago',
				'inclusive' => true,
			),
		);
	}

	// Terms are matched by slug: slugs survive the import, IDs do not.
	if ( '' !== (string) $args['category'] ) {
		$query['category_name'] = sanitize_title( $args['category'] );
	}

	if ( '' !== (string) $args['tag'] ) {
		$query['tag'] = sanitize_title( $args['tag'] );
	}

	return get_posts( $query );
}

/**
 * Tags that are hot right now: the tags of the most-read articles published in
 * the last N days, ranked by the views those articles got. Widens to 30 days,
 * then to all-time tag counts, when a quiet week has too few.
 *
 * Cached for an hour.
 *
 * @param int $number How many tags.
 * @param int $days   Window in days.
 * @return WP_Term[]
 */
function kabarsembada_trending_tags( $number = 5, $days = 7 ) {
	$key  = 'kabarsembada_trending_' . (int) $number . '_' . (int) $days;
	$ids  = get_transient( $key );
	$from = static function ( $ids ) {
		return array_values( array_filter( array_map( static function ( $id ) {
			$term = get_term( (int) $id, 'post_tag' );

			return $term instanceof WP_Term ? $term : null;
		}, (array) $ids ) ) );
	};

	if ( is_array( $ids ) ) {
		return $from( $ids );
	}

	$score = array();

	foreach ( array( $days, 30 ) as $window ) {
		$posts = get_posts(
			kabarsembada_order_by_views(
				array(
					'post_type'           => 'post',
					'post_status'         => 'publish',
					'posts_per_page'      => 60,
					'fields'              => 'ids',
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
					'date_query'          => array( array( 'after' => absint( $window ) . ' days ago' ) ),
				)
			)
		);

		foreach ( $posts ? wp_get_object_terms( $posts, 'post_tag', array( 'fields' => 'all_with_object_id' ) ) : array() as $term ) {
			// +1 so a brand-new article with no views yet still counts.
			$score[ $term->term_id ] = ( isset( $score[ $term->term_id ] ) ? $score[ $term->term_id ] : 0 ) + kabarsembada_get_views( $term->object_id ) + 1;
		}

		if ( count( $score ) >= $number ) {
			break;
		}
	}

	arsort( $score );
	$ids = array_slice( array_keys( $score ), 0, $number );

	if ( count( $ids ) < $number ) {
		$more = get_terms(
			array(
				'taxonomy'   => 'post_tag',
				'orderby'    => 'count',
				'order'      => 'DESC',
				'number'     => $number,
				'hide_empty' => true,
				'exclude'    => $ids,
				'fields'     => 'ids',
			)
		);
		$ids  = array_slice( array_merge( $ids, is_wp_error( $more ) ? array() : $more ), 0, $number );
	}

	set_transient( $key, $ids, HOUR_IN_SECONDS );

	return $from( $ids );
}
