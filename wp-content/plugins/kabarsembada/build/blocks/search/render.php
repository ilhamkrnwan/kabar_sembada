<?php
/**
 * Search: a trigger button plus, once per page, the search dialog.
 *
 * Any element with [data-search-open] opens the dialog (its value or
 * data-search-open text prefills the query); "/" opens it from anywhere.
 *
 * @package KabarSembada
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$ks_icon = static function ( $name ) {
	$paths = array(
		'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'close'  => '<path d="M6 6l12 12M18 6 6 18"/>',
		'arrow'  => '<path d="M7 17 17 7M8 7h9v9"/>',
		'trend'  => '<path d="m3 17 6-6 4 4 8-8"/><path d="M15 7h6v6"/>',
	);

	return '<svg class="ks-search__i" viewBox="0 0 24 24" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>';
};
?>
<button type="button" <?php echo get_block_wrapper_attributes( array( 'class' => 'ks-search-open' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-search-open aria-label="<?php esc_attr_e( 'Cari berita (tekan /)', 'kabarsembada' ); ?>">
	<?php echo $ks_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static markup. ?>
	<?php if ( ! empty( $attributes['showLabel'] ) ) : ?>
		<span><?php esc_html_e( 'Cari', 'kabarsembada' ); ?></span>
	<?php endif; ?>
</button>
<?php
// One dialog per page, and none in the editor preview.
if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! kabarsembada_once( 'search-dialog' ) ) {
	return;
}

$ks_topics   = $attributes['topics'] > 0 ? kabarsembada_trending_tags( (int) $attributes['topics'] ) : array();
$ks_channels = $attributes['channels'] > 0 ? get_terms(
	array(
		'taxonomy'   => 'category',
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => (int) $attributes['channels'],
		'hide_empty' => true,
	)
) : array();
$ks_channels = is_wp_error( $ks_channels ) ? array() : $ks_channels;
$ks_i18n     = array(
	/* translators: %d: number of results. */
	'found'   => __( '%d berita ditemukan', 'kabarsembada' ),
	'none'    => __( 'Tidak ada hasil', 'kabarsembada' ),
	/* translators: %s: search query. */
	'noneFor' => __( 'Tidak ada berita untuk “%s”.', 'kabarsembada' ),
	'tip'     => __( 'Periksa ejaan atau coba kata lain.', 'kabarsembada' ),
	/* translators: %s: search query. */
	'all'     => __( 'Lihat semua hasil untuk “%s”', 'kabarsembada' ),
	'empty'   => __( 'Belum ada pencarian.', 'kabarsembada' ),
	/* translators: %s: past search query. */
	'remove'  => __( 'Hapus %s dari riwayat', 'kabarsembada' ),
	'error'   => __( 'Pencarian gagal dimuat. Tekan Enter untuk membuka halaman hasil.', 'kabarsembada' ),
);
?>
<dialog class="ks-search" aria-label="<?php esc_attr_e( 'Pencarian berita', 'kabarsembada' ); ?>" data-api="<?php echo esc_url( rest_url( 'kabarsembada/v1/search' ) ); ?>" data-results-url="<?php echo esc_url( home_url( '/' ) ); ?>" data-i18n="<?php echo esc_attr( wp_json_encode( $ks_i18n ) ); ?>">
	<div class="ks-search__panel">
		<form class="ks-search__bar" role="search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="ks-search__field">
				<?php echo $ks_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Cari berita', 'kabarsembada' ); ?></span>
				<input type="search" name="s" placeholder="<?php esc_attr_e( 'Cari berita di Kabar Sembada', 'kabarsembada' ); ?>" autocomplete="off" enterkeyhint="search" role="combobox" aria-expanded="false" aria-controls="ks-search-listbox" aria-autocomplete="list">
				<button type="button" class="ks-search__clear" aria-label="<?php esc_attr_e( 'Hapus teks', 'kabarsembada' ); ?>" hidden><?php echo $ks_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			</label>
			<button type="submit" class="ks-search__go" aria-label="<?php esc_attr_e( 'Cari', 'kabarsembada' ); ?>"><?php echo $ks_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			<button type="button" class="ks-search__cancel"><?php esc_html_e( 'Batal', 'kabarsembada' ); ?></button>
		</form>
		<div class="ks-search__body">
			<div class="ks-search__idle">
				<?php if ( $ks_topics ) : ?>
					<section>
						<h2><?php esc_html_e( 'Sedang ramai minggu ini', 'kabarsembada' ); ?></h2>
						<ol class="ks-search__trend">
							<?php foreach ( $ks_topics as $ks_i => $ks_term ) : ?>
								<li><button type="button" data-q="<?php echo esc_attr( $ks_term->name ); ?>"><b>#<?php echo (int) $ks_i + 1; ?></b> <?php echo esc_html( $ks_term->name ); ?><?php echo $ks_i < 3 ? '<span class="ks-search__up">' . $ks_icon( 'trend' ) . '</span>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button></li>
							<?php endforeach; ?>
						</ol>
					</section>
				<?php endif; ?>
				<?php if ( $ks_channels ) : ?>
					<section>
						<h2><?php esc_html_e( 'Kanal', 'kabarsembada' ); ?></h2>
						<ul class="ks-search__channels">
							<?php foreach ( $ks_channels as $ks_term ) : ?>
								<li><a href="<?php echo esc_url( get_term_link( $ks_term ) ); ?>"><span><?php echo esc_html( $ks_term->name ); ?></span><?php echo $ks_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>
				<section>
					<div class="ks-search__head">
						<h2><?php esc_html_e( 'Terakhir dicari', 'kabarsembada' ); ?></h2>
						<button type="button" class="ks-search__link" data-clear-all hidden><?php esc_html_e( 'Hapus semua', 'kabarsembada' ); ?></button>
					</div>
					<ul class="ks-search__recent"></ul>
				</section>
			</div>
			<div class="ks-search__results" hidden></div>
			<p class="screen-reader-text" aria-live="polite" data-status></p>
		</div>
	</div>
</dialog>
