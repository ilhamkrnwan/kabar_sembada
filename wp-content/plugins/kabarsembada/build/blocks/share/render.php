<?php
/**
 * Share buttons: plain share links (no third-party scripts) and copy link.
 *
 * @package KabarSembada
 *
 * @var WP_Block $block Block instance.
 */

defined( 'ABSPATH' ) || exit;

$ks_post_id = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : (int) get_the_ID();

if ( ! $ks_post_id || 'publish' !== get_post_status( $ks_post_id ) ) {
	return;
}

$ks_url   = get_permalink( $ks_post_id );
$ks_title = html_entity_decode( get_the_title( $ks_post_id ), ENT_QUOTES, 'UTF-8' );
$ks_links = array(
	array(
		'label' => __( 'Bagikan ke WhatsApp', 'kabarsembada' ),
		'href'  => 'https://wa.me/?text=' . rawurlencode( $ks_title . ' ' . $ks_url ),
		'icon'  => '<path d="M4 20l1.2-3.6A8 8 0 1 1 8 19.1z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1-1.5-1.8-1-1 .8a4 4 0 0 1-2-2l.8-1-1-1.8z"/>',
	),
	array(
		'label' => __( 'Bagikan ke Facebook', 'kabarsembada' ),
		'href'  => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $ks_url ),
		'icon'  => '<path d="M14 8h2V4.5h-2.5A3.5 3.5 0 0 0 10 8v2.5H8V14h2v6.5h3.5V14H16l.5-3.5h-3V8.5A.5.5 0 0 1 14 8z"/>',
	),
	array(
		'label' => __( 'Bagikan ke X', 'kabarsembada' ),
		'href'  => 'https://x.com/intent/post?url=' . rawurlencode( $ks_url ) . '&text=' . rawurlencode( $ks_title ),
		'icon'  => '<path d="M5 4l14 16M19 4L5 20"/>',
	),
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'ks-share' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="group" aria-label="<?php esc_attr_e( 'Bagikan', 'kabarsembada' ); ?>">
	<?php foreach ( $ks_links as $ks_link ) : ?>
		<a href="<?php echo esc_url( $ks_link['href'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $ks_link['label'] ); ?>">
			<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><?php echo $ks_link['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static markup. ?></svg>
		</a>
	<?php endforeach; ?>
	<button type="button" class="ks-share__copy" data-url="<?php echo esc_url( $ks_url ); ?>" aria-label="<?php esc_attr_e( 'Salin tautan', 'kabarsembada' ); ?>">
		<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1"/><path d="M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1"/></svg>
	</button>
	<span class="ks-share__note" role="status" data-done="<?php esc_attr_e( 'Tautan disalin', 'kabarsembada' ); ?>" data-fail="<?php esc_attr_e( 'Gagal menyalin, salin dari bilah alamat', 'kabarsembada' ); ?>"></span>
</div>
