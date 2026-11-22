<?php
/**
 * Comments are off site-wide: closed on every post, no pingbacks, and out of
 * the admin. The theme has no comment form either.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 20 );

// No pingback endpoint either.
add_filter(
	'xmlrpc_methods',
	static function ( $methods ) {
		unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );

		return $methods;
	}
);

/**
 * Drop the Comments screens from the admin menu and toolbar.
 *
 * @return void
 */
function kabarsembada_hide_comments_admin() {
	remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}
add_action( 'admin_menu', 'kabarsembada_hide_comments_admin' );

/**
 * Remove the comments bubble from the toolbar.
 *
 * @param WP_Admin_Bar $bar Toolbar.
 * @return void
 */
function kabarsembada_hide_comments_toolbar( $bar ) {
	$bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'kabarsembada_hide_comments_toolbar', 999 );
