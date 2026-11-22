<?php
/**
 * SEO settings: site-wide fallbacks for the description and share image.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

/**
 * Stored SEO options merged with defaults.
 *
 * @return array{description:string,image_id:int,twitter:string}
 */
function kabarsembada_seo_options() {
	$stored = get_option( 'kabarsembada_seo', array() );

	return wp_parse_args(
		is_array( $stored ) ? $stored : array(),
		array(
			'description' => '',
			'image_id'    => 0,
			'twitter'     => '',
		)
	);
}

/**
 * Sanitize the SEO options on save.
 *
 * @param mixed $input Raw submitted value.
 * @return array
 */
function kabarsembada_seo_sanitize( $input ) {
	$input   = is_array( $input ) ? $input : array();
	$twitter = isset( $input['twitter'] ) ? sanitize_text_field( $input['twitter'] ) : '';

	return array(
		'description' => isset( $input['description'] ) ? sanitize_textarea_field( $input['description'] ) : '',
		'image_id'    => isset( $input['image_id'] ) ? absint( $input['image_id'] ) : 0,
		'twitter'     => ltrim( $twitter, '@' ),
	);
}

/**
 * Register the setting.
 *
 * @return void
 */
function kabarsembada_seo_register_settings() {
	register_setting(
		'kabarsembada_seo',
		'kabarsembada_seo',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'kabarsembada_seo_sanitize',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'kabarsembada_seo_register_settings' );

/**
 * Add the Settings → SEO Kabar Sembada page.
 *
 * @return void
 */
function kabarsembada_seo_menu() {
	add_options_page(
		__( 'SEO Kabar Sembada', 'kabarsembada' ),
		__( 'SEO Kabar Sembada', 'kabarsembada' ),
		'manage_options',
		'kabarsembada-seo',
		'kabarsembada_seo_render_page'
	);
}
add_action( 'admin_menu', 'kabarsembada_seo_menu' );

/**
 * Load the media library on the SEO page only.
 *
 * @param string $hook Current admin page hook.
 * @return void
 */
function kabarsembada_seo_admin_assets( $hook ) {
	if ( 'settings_page_kabarsembada-seo' === $hook ) {
		wp_enqueue_media();
	}
}
add_action( 'admin_enqueue_scripts', 'kabarsembada_seo_admin_assets' );

/**
 * Render the settings page.
 *
 * @return void
 */
function kabarsembada_seo_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$options = kabarsembada_seo_options();
	$preview = $options['image_id'] ? wp_get_attachment_image_url( $options['image_id'], 'medium' ) : '';
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'SEO Kabar Sembada', 'kabarsembada' ); ?></h1>
		<p><?php esc_html_e( 'Cadangan deskripsi dan gambar bagikan bila halaman tidak punya sendiri.', 'kabarsembada' ); ?></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'kabarsembada_seo' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="ks-seo-description"><?php esc_html_e( 'Deskripsi bawaan', 'kabarsembada' ); ?></label></th>
					<td>
						<textarea id="ks-seo-description" name="kabarsembada_seo[description]" rows="3" class="large-text"><?php echo esc_textarea( $options['description'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Kosongkan untuk memakai tagline situs.', 'kabarsembada' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Gambar bagikan bawaan', 'kabarsembada' ); ?></th>
					<td>
						<input type="hidden" id="ks-seo-image" name="kabarsembada_seo[image_id]" value="<?php echo esc_attr( (string) $options['image_id'] ); ?>">
						<p><img id="ks-seo-preview" src="<?php echo esc_url( $preview ); ?>" alt="" style="max-width:320px;height:auto;<?php echo $preview ? '' : 'display:none;'; ?>"></p>
						<button type="button" class="button" id="ks-seo-pick"><?php esc_html_e( 'Pilih gambar', 'kabarsembada' ); ?></button>
						<button type="button" class="button-link" id="ks-seo-clear"><?php esc_html_e( 'Hapus', 'kabarsembada' ); ?></button>
						<p class="description"><?php esc_html_e( 'Disarankan 1200 × 630 px. Bila kosong memakai ikon situs.', 'kabarsembada' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="ks-seo-twitter"><?php esc_html_e( 'Akun X / Twitter', 'kabarsembada' ); ?></label></th>
					<td><input type="text" id="ks-seo-twitter" name="kabarsembada_seo[twitter]" value="<?php echo esc_attr( $options['twitter'] ); ?>" class="regular-text" placeholder="kabarsembada"></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<script>
	( function () {
		var field = document.getElementById( 'ks-seo-image' );
		var preview = document.getElementById( 'ks-seo-preview' );
		var frame;

		document.getElementById( 'ks-seo-pick' ).addEventListener( 'click', function () {
			if ( ! frame ) {
				frame = wp.media( { library: { type: 'image' }, multiple: false } );
				frame.on( 'select', function () {
					var item = frame.state().get( 'selection' ).first().toJSON();
					field.value = item.id;
					preview.src = ( item.sizes && item.sizes.medium ? item.sizes.medium.url : item.url );
					preview.style.display = '';
				} );
			}
			frame.open();
		} );

		document.getElementById( 'ks-seo-clear' ).addEventListener( 'click', function () {
			field.value = '';
			preview.removeAttribute( 'src' );
			preview.style.display = 'none';
		} );
	}() );
	</script>
	<?php
}
