<?php
/**
 * Plugin Name:       Kabar Sembada
 * Description:       Blok berita, penghitung views, SEO, pencarian, dan motion untuk tema Kabar Sembada.
 * Version:           0.6.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Kabar Sembada
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kabarsembada
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

define( 'KABARSEMBADA_VERSION', '0.6.0' );
define( 'KABARSEMBADA_FILE', __FILE__ );
define( 'KABARSEMBADA_PATH', plugin_dir_path( __FILE__ ) );
define( 'KABARSEMBADA_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load feature modules. Each module owns its hooks.
 *
 * @return void
 */
function kabarsembada_load_modules() {
	$modules = apply_filters(
		'kabarsembada_modules',
		array(
			'helpers.php',
			'media.php',
			'views.php',
			'query.php',
			'search.php',
			'seo/settings.php',
			'seo/fields.php',
			'seo/meta.php',
			'seo/schema.php',
			'motion.php',
			'redirects.php',
			'newsletter.php',
			'comments.php',
			'blocks.php',
		)
	);

	foreach ( $modules as $module ) {
		$path = KABARSEMBADA_PATH . 'includes/' . ltrim( $module, '/' );

		if ( file_exists( $path ) ) {
			require_once $path;
		}
	}
}
add_action( 'plugins_loaded', 'kabarsembada_load_modules' );

/**
 * Register the Kabar Sembada block category.
 *
 * @param array $categories Existing block categories.
 * @return array
 */
function kabarsembada_register_block_category( $categories ) {
	return array_merge(
		array(
			array(
				'slug'  => 'kabarsembada',
				'title' => __( 'Kabar Sembada', 'kabarsembada' ),
			),
		),
		$categories
	);
}
add_filter( 'block_categories_all', 'kabarsembada_register_block_category' );
