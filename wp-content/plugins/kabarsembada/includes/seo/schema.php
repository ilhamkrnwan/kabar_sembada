<?php
/**
 * JSON-LD structured data: one @graph per page with NewsMediaOrganization and
 * WebSite everywhere, plus NewsArticle and BreadcrumbList on articles.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

/**
 * Keep only non-empty values.
 *
 * @param array $data Node properties.
 * @return array
 */
function kabarsembada_schema_filter( $data ) {
	return array_filter(
		$data,
		static function ( $value ) {
			return ! ( '' === $value || null === $value || array() === $value );
		}
	);
}

/**
 * NewsArticle and BreadcrumbList nodes for an article.
 *
 * @param WP_Post $post   Post object.
 * @param array   $ctx    SEO context.
 * @param string  $org_id Organization node id.
 * @return array[]
 */
function kabarsembada_schema_article( $post, $ctx, $org_id ) {
	$cat   = kabarsembada_primary_category( $post );
	$image = kabarsembada_has_thumbnail( $post ) ? (string) get_the_post_thumbnail_url( $post, 'full' ) : '';
	$crumb = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => __( 'Beranda', 'kabarsembada' ),
			'item'     => home_url( '/' ),
		),
	);

	if ( $cat ) {
		$link    = get_term_link( $cat );
		$crumb[] = array(
			'@type'    => 'ListItem',
			'position' => 2,
			'name'     => $cat->name,
			'item'     => is_wp_error( $link ) ? '' : $link,
		);
	}

	$crumb[] = array(
		'@type'    => 'ListItem',
		'position' => count( $crumb ) + 1,
		'name'     => $ctx['title'],
	);

	return array(
		kabarsembada_schema_filter(
			array(
				'@type'            => 'NewsArticle',
				'@id'              => $ctx['url'] . '#article',
				'mainEntityOfPage' => $ctx['url'],
				'headline'         => wp_strip_all_tags( get_the_title( $post ) ),
				'description'      => $ctx['description'],
				'image'            => $image,
				'datePublished'    => get_post_time( 'c', true, $post ),
				'dateModified'     => get_post_modified_time( 'c', true, $post ),
				'inLanguage'       => get_bloginfo( 'language' ),
				'articleSection'   => $cat ? $cat->name : '',
				'keywords'         => implode( ', ', wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) ) ),
				'author'           => array(
					'@type' => 'Person',
					'name'  => get_the_author_meta( 'display_name', (int) $post->post_author ),
					'url'   => get_author_posts_url( (int) $post->post_author ),
				),
				'publisher'        => array( '@id' => $org_id ),
			)
		),
		array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $ctx['url'] . '#breadcrumb',
			'itemListElement' => $crumb,
		),
	);
}

/**
 * Build the structured-data graph for the current request.
 *
 * @return array Graph nodes.
 */
function kabarsembada_schema_graph() {
	$ctx    = kabarsembada_seo_context();
	$org_id = home_url( '/#organization' );
	$logo   = get_site_icon_url( 512 );
	$graph  = array(
		kabarsembada_schema_filter(
			array(
				'@type' => 'NewsMediaOrganization',
				'@id'   => $org_id,
				'name'  => $ctx['site'],
				'url'   => home_url( '/' ),
				'logo'  => $logo ? array(
					'@type' => 'ImageObject',
					'url'   => $logo,
				) : '',
			)
		),
		array(
			'@type'           => 'WebSite',
			'@id'             => home_url( '/#website' ),
			'url'             => home_url( '/' ),
			'name'            => $ctx['site'],
			'inLanguage'      => get_bloginfo( 'language' ),
			'publisher'       => array( '@id' => $org_id ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		),
	);

	$post = is_singular( 'post' ) ? get_queried_object() : null;

	if ( $post instanceof WP_Post && ! post_password_required( $post ) ) {
		$graph = array_merge( $graph, kabarsembada_schema_article( $post, $ctx, $org_id ) );
	}

	return apply_filters( 'kabarsembada_schema_graph', $graph, $ctx );
}

/**
 * Print the JSON-LD script in the head.
 *
 * @return void
 */
function kabarsembada_schema_print() {
	if ( is_404() || is_feed() || is_embed() || is_search() || ! get_option( 'blog_public' ) ) {
		return;
	}

	$json = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => kabarsembada_schema_graph(),
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
	);

	echo '<script type="application/ld+json">' . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-encoded with HEX_TAG.
}
add_action( 'wp_head', 'kabarsembada_schema_print', 6 );
