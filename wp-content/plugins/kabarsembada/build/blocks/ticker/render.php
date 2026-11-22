<?php
/**
 * Ticker: latest headlines scrolling in a loop.
 *
 * The list is printed twice so the CSS loop is seamless; the copy is hidden
 * from assistive tech and keyboard focus.
 *
 * @package KabarSembada
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$ks_posts = kabarsembada_get_posts(
	array(
		'number'   => $attributes['number'],
		'category' => $attributes['category'],
	)
);

if ( ! $ks_posts ) {
	return;
}

$ks_links = static function ( $hidden ) use ( $ks_posts ) {
	$out = '';

	foreach ( $ks_posts as $post ) {
		$out .= sprintf(
			'<a href="%s"%s>%s</a>',
			esc_url( get_permalink( $post ) ),
			$hidden ? ' aria-hidden="true" tabindex="-1"' : '',
			esc_html( get_the_title( $post ) )
		);
	}

	return $out;
};
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'ks-ticker' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> role="region" aria-label="<?php esc_attr_e( 'Berita terkini', 'kabarsembada' ); ?>">
	<?php if ( '' !== trim( $attributes['label'] ) ) : ?>
		<span class="ks-ticker__label"><?php echo esc_html( $attributes['label'] ); ?></span>
	<?php endif; ?>
	<div class="ks-ticker__viewport">
		<div class="ks-ticker__track">
			<?php echo $ks_links( false ) . $ks_links( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>
		</div>
	</div>
	<button type="button" class="ks-ticker__toggle" aria-pressed="false" aria-label="<?php esc_attr_e( 'Jeda teks berjalan', 'kabarsembada' ); ?>">
		<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path class="ks-ticker__pause" d="M9 6v12M15 6v12"/><path class="ks-ticker__play" d="M8 5.5v13l10-6.5z"/></svg>
	</button>
</div>
