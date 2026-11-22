<?php
/**
 * Newsletter form. Posts to kabarsembada/v1/subscribe (includes/newsletter.php).
 *
 * @package KabarSembada
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$ks_id = wp_unique_id( 'ks-sub-' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'ks-subscribe' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<form class="ks-subscribe__form" action="<?php echo esc_url( rest_url( 'kabarsembada/v1/subscribe' ) ); ?>" method="post" novalidate data-invalid="<?php esc_attr_e( 'Alamat surel belum benar.', 'kabarsembada' ); ?>" data-error="<?php esc_attr_e( 'Gagal mendaftar. Coba lagi sebentar.', 'kabarsembada' ); ?>">
		<label for="<?php echo esc_attr( $ks_id ); ?>" class="screen-reader-text"><?php esc_html_e( 'Alamat surel', 'kabarsembada' ); ?></label>
		<input id="<?php echo esc_attr( $ks_id ); ?>" type="email" name="email" placeholder="<?php echo esc_attr( $attributes['placeholder'] ); ?>" autocomplete="email" required>
		<input type="text" name="website" class="ks-subscribe__trap" tabindex="-1" autocomplete="off" aria-hidden="true">
		<button type="submit"><?php echo esc_html( $attributes['buttonText'] ); ?></button>
	</form>
	<p class="ks-subscribe__note" role="status" aria-live="polite"></p>
</div>
