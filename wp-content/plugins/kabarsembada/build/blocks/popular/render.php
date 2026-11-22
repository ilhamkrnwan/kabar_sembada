<?php
/**
 * Most-read list, numbered. On an article page the article itself is left out.
 *
 * @package KabarSembada
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$ks_current = isset( $block->context['postId'] ) ? (int) $block->context['postId'] : 0;
$ks_posts   = kabarsembada_get_posts(
	array(
		'number'  => max( 3, min( 10, (int) $attributes['number'] ) ),
		'order'   => 'views',
		'days'    => (int) $attributes['days'],
		'exclude' => $ks_current ? array( $ks_current ) : array(),
	)
);

if ( ! $ks_posts ) {
	return;
}

$ks_heading = 'h' . max( 2, min( 4, (int) $attributes['headingLevel'] ) );
?>
<ol <?php echo get_block_wrapper_attributes( array( 'class' => 'ks-popular' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php foreach ( $ks_posts as $ks_i => $ks_post ) : ?>
		<li>
			<a href="<?php echo esc_url( get_permalink( $ks_post ) ); ?>">
				<span class="ks-popular__rank" aria-hidden="true"><?php echo (int) $ks_i + 1; ?></span>
				<<?php echo esc_html( $ks_heading ); ?> class="ks-popular__title"><?php echo esc_html( get_the_title( $ks_post ) ); ?></<?php echo esc_html( $ks_heading ); ?>>
			</a>
		</li>
	<?php endforeach; ?>
</ol>
