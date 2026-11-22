<?php
/**
 * Permanent redirects for URLs the migration retired.
 *
 * Old post slugs are handled by core (_wp_old_slug). This covers what core
 * cannot: merged categories.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

/**
 * Old path prefix => new path prefix. Anything after the prefix (pagination,
 * feeds) is kept.
 *
 * @return array<string,string>
 */
function kabarsembada_redirect_map() {
	return apply_filters(
		'kabarsembada_redirect_map',
		array(
			// "Politik" (politik-2) was merged into "Politik Hukum" (politik).
			'/category/politik-2/' => '/category/politik/',
		)
	);
}

/**
 * Redirect requests for retired paths. Runs only on 404s, so a live page with
 * the same path always wins.
 *
 * @return void
 */
function kabarsembada_redirect_retired() {
	if ( ! is_404() ) {
		return;
	}

	$path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Only compared and rebuilt below.
	$path = trailingslashit( $path );

	foreach ( kabarsembada_redirect_map() as $from => $to ) {
		if ( 0 === strpos( $path, $from ) ) {
			wp_safe_redirect( home_url( $to . substr( $path, strlen( $from ) ) ), 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'kabarsembada_redirect_retired' );
