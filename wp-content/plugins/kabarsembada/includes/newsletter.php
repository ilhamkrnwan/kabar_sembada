<?php
/**
 * Newsletter sign-ups, stored in WordPress.
 *
 * Each address is a private `ks_subscriber` post (title = email). Editors see
 * the list under "Pelanggan" and export it as CSV. Nothing is sent yet: the
 * list waits for a mailing service.
 *
 * ponytail: no double opt-in yet; add a confirmation email when sending starts,
 * before the first mailing.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

const KABARSEMBADA_SUBSCRIBER_TYPE = 'ks_subscriber';

/**
 * Register the subscriber post type (admin only, never public).
 *
 * @return void
 */
function kabarsembada_register_subscriber_type() {
	register_post_type(
		KABARSEMBADA_SUBSCRIBER_TYPE,
		array(
			'labels'          => array(
				'name'          => __( 'Pelanggan', 'kabarsembada' ),
				'singular_name' => __( 'Pelanggan', 'kabarsembada' ),
				'menu_name'     => __( 'Pelanggan', 'kabarsembada' ),
				'all_items'     => __( 'Semua pelanggan', 'kabarsembada' ),
				'search_items'  => __( 'Cari pelanggan', 'kabarsembada' ),
				'not_found'     => __( 'Belum ada pelanggan.', 'kabarsembada' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_rest'    => false,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 26,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			// Sign-ups only come from the form.
			'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'kabarsembada_register_subscriber_type' );

/**
 * Whether an address is already on the list.
 *
 * @param string $email Email.
 * @return bool
 */
function kabarsembada_subscriber_exists( $email ) {
	global $wpdb;

	return (bool) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_title = %s LIMIT 1",
			KABARSEMBADA_SUBSCRIBER_TYPE,
			$email
		)
	);
}

/**
 * Handle a sign-up.
 *
 * Always answers the same way for new and existing addresses, so the form
 * cannot be used to probe who is subscribed.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function kabarsembada_rest_subscribe( $request ) {
	// Honeypot: people never see this field, bots fill it.
	if ( '' !== trim( (string) $request->get_param( 'website' ) ) ) {
		return rest_ensure_response( array( 'message' => __( 'Terima kasih, surelmu sudah terdaftar.', 'kabarsembada' ) ) );
	}

	$email = sanitize_email( (string) $request->get_param( 'email' ) );

	if ( ! is_email( $email ) ) {
		return new WP_Error( 'kabarsembada_email', __( 'Alamat surel belum benar.', 'kabarsembada' ), array( 'status' => 400 ) );
	}

	// ponytail: per-IP throttle in a transient; move to the object cache if
	// sign-ups ever get heavy.
	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key  = 'ks_sub_' . md5( $ip );
	$hits = (int) get_transient( $key );

	if ( $hits >= 5 ) {
		return new WP_Error( 'kabarsembada_slow_down', __( 'Terlalu banyak percobaan. Coba lagi nanti.', 'kabarsembada' ), array( 'status' => 429 ) );
	}

	set_transient( $key, $hits + 1, HOUR_IN_SECONDS );

	$email = strtolower( $email );

	if ( ! kabarsembada_subscriber_exists( $email ) ) {
		wp_insert_post(
			array(
				'post_type'   => KABARSEMBADA_SUBSCRIBER_TYPE,
				'post_status' => 'publish',
				'post_title'  => $email,
				'post_author' => 0,
			)
		);
	}

	return rest_ensure_response( array( 'message' => __( 'Terima kasih, surelmu sudah terdaftar.', 'kabarsembada' ) ) );
}

/**
 * Register the sign-up route.
 *
 * @return void
 */
function kabarsembada_register_subscribe_route() {
	register_rest_route(
		'kabarsembada/v1',
		'/subscribe',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'kabarsembada_rest_subscribe',
			'permission_callback' => '__return_true',
			'args'                => array(
				'email'   => array(
					'type'     => 'string',
					'required' => true,
				),
				'website' => array( 'type' => 'string' ),
			),
		)
	);
}
add_action( 'rest_api_init', 'kabarsembada_register_subscribe_route' );

/**
 * "Ekspor CSV" button on the subscriber list.
 *
 * @param string $which Top or bottom table nav.
 * @return void
 */
function kabarsembada_subscriber_export_button( $which ) {
	if ( 'top' !== $which || KABARSEMBADA_SUBSCRIBER_TYPE !== get_current_screen()->post_type ) {
		return;
	}

	printf(
		'<a class="button" href="%s">%s</a>',
		esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=kabarsembada_export_subscribers' ), 'kabarsembada_export_subscribers' ) ),
		esc_html__( 'Ekspor CSV', 'kabarsembada' )
	);
}
add_action( 'manage_posts_extra_tablenav', 'kabarsembada_subscriber_export_button' );

/**
 * Stream the list as CSV.
 *
 * @return void
 */
function kabarsembada_export_subscribers() {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die( esc_html__( 'Tidak diizinkan.', 'kabarsembada' ), 403 );
	}

	check_admin_referer( 'kabarsembada_export_subscribers' );

	$rows = get_posts(
		array(
			'post_type'      => KABARSEMBADA_SUBSCRIBER_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
		)
	);

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=pelanggan-kabarsembada-' . wp_date( 'Y-m-d' ) . '.csv' );

	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	fputcsv( $out, array( 'email', 'terdaftar' ) );

	foreach ( $rows as $row ) {
		$email = $row->post_title;

		// Spreadsheets run cells that start with these as formulas.
		if ( in_array( substr( $email, 0, 1 ), array( '=', '+', '-', '@' ), true ) ) {
			$email = "'" . $email;
		}

		fputcsv( $out, array( $email, get_post_time( 'Y-m-d H:i', false, $row ) ) );
	}

	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_kabarsembada_export_subscribers', 'kabarsembada_export_subscribers' );
