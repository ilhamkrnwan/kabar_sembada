<?php
/**
 * Post tabs.
 *
 * - latest: two tabs, "Berita Terbaru" and "Paling Dibaca", compact cards.
 * - topics: one chip tab per chosen tag; each panel has a lead card, a short
 *   list and a link to the tag archive (Fokus Berita). Without chosen tags it
 *   uses the tags most used in the last 30 days.
 *
 * Tabs with no posts are left out; with none left the block prints nothing.
 *
 * @package KabarSembada
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

$ks_mode   = 'topics' === $attributes['mode'] ? 'topics' : 'latest';
$ks_number = max( 2, min( 6, (int) $attributes['number'] ) );
$ks_tabs   = array();

if ( 'latest' === $ks_mode ) {
	$ks_tabs[] = array(
		'label' => __( 'Berita Terbaru', 'kabarsembada' ),
		'posts' => kabarsembada_get_posts( array( 'number' => $ks_number ) ),
	);
	$ks_tabs[] = array(
		'label' => __( 'Paling Dibaca', 'kabarsembada' ),
		'posts' => kabarsembada_get_posts(
			array(
				'number' => $ks_number,
				'order'  => 'views',
			)
		),
	);
} else {
	$ks_slugs = array_filter( (array) $attributes['tags'] );

	// No topics picked yet: use the tags most used in the last 30 days.
	if ( ! $ks_slugs ) {
		$ks_slugs = kabarsembada_recent_top_tags( 4 );
	}

	foreach ( $ks_slugs as $ks_slug ) {
		$ks_term = get_term_by( 'slug', sanitize_title( $ks_slug ), 'post_tag' );

		if ( ! $ks_term ) {
			continue;
		}

		$ks_link   = get_term_link( $ks_term );
		$ks_tabs[] = array(
			'label' => $ks_term->name,
			'link'  => is_wp_error( $ks_link ) ? '' : $ks_link,
			'posts' => kabarsembada_get_posts(
				array(
					'number' => $ks_number + 1,
					'tag'    => $ks_term->slug,
				)
			),
		);
	}
}

$ks_tabs = array_values(
	array_filter(
		$ks_tabs,
		static function ( $tab ) {
			return ! empty( $tab['posts'] );
		}
	)
);

if ( ! $ks_tabs ) {
	return;
}

$ks_id = wp_unique_id( 'ks-tabs-' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'ks-tabs ks-tabs--' . $ks_mode ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="ks-tabs__list" role="tablist" style="--ks-tabs:<?php echo (int) count( $ks_tabs ); ?>">
		<?php foreach ( $ks_tabs as $ks_i => $ks_tab ) : ?>
			<button type="button" role="tab" class="ks-tabs__tab" id="<?php echo esc_attr( $ks_id . '-t' . $ks_i ); ?>" aria-controls="<?php echo esc_attr( $ks_id . '-p' . $ks_i ); ?>" aria-selected="<?php echo 0 === $ks_i ? 'true' : 'false'; ?>"<?php echo 0 === $ks_i ? '' : ' tabindex="-1"'; ?>><?php echo esc_html( $ks_tab['label'] ); ?></button>
		<?php endforeach; ?>
	</div>
	<div class="ks-tabs__panels">
		<?php foreach ( $ks_tabs as $ks_i => $ks_tab ) : ?>
			<div class="ks-tabs__panel<?php echo 0 === $ks_i ? ' is-active' : ''; ?>" role="tabpanel" id="<?php echo esc_attr( $ks_id . '-p' . $ks_i ); ?>" aria-labelledby="<?php echo esc_attr( $ks_id . '-t' . $ks_i ); ?>"<?php echo 0 === $ks_i ? '' : ' inert'; ?>>
				<?php
				if ( 'latest' === $ks_mode ) {
					foreach ( $ks_tab['posts'] as $ks_post ) {
						echo kabarsembada_render_mini_card( $ks_post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the helper.
					}
				} else {
					$ks_lead = array_shift( $ks_tab['posts'] );
					$ks_desc = has_excerpt( $ks_lead ) ? get_the_excerpt( $ks_lead ) : '';
					?>
					<a class="ks-lead" href="<?php echo esc_url( get_permalink( $ks_lead ) ); ?>">
						<?php echo kabarsembada_render_thumb( $ks_lead, 'medium_large' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo kabarsembada_render_meta( $ks_lead ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<h3 class="ks-title"><?php echo esc_html( get_the_title( $ks_lead ) ); ?></h3>
						<?php if ( '' !== $ks_desc ) : ?>
							<p><?php echo esc_html( wp_trim_words( $ks_desc, 22, '…' ) ); ?></p>
						<?php endif; ?>
					</a>
					<?php if ( $ks_tab['posts'] ) : ?>
						<ul class="ks-tabs__more">
							<?php foreach ( $ks_tab['posts'] as $ks_post ) : ?>
								<li><a href="<?php echo esc_url( get_permalink( $ks_post ) ); ?>"><span class="ks-title"><?php echo esc_html( get_the_title( $ks_post ) ); ?></span><time datetime="<?php echo esc_attr( get_post_time( 'c', true, $ks_post ) ); ?>"><?php echo esc_html( kabarsembada_post_time( $ks_post ) ); ?></time></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<?php if ( $ks_tab['link'] ) : ?>
						<a class="ks-tabs__follow" href="<?php echo esc_url( $ks_tab['link'] ); ?>">
							<?php
							/* translators: %s: topic (tag) name. */
							printf( esc_html__( 'Semua berita %s', 'kabarsembada' ), esc_html( $ks_tab['label'] ) );
							?>
							<span aria-hidden="true">→</span>
						</a>
					<?php endif; ?>
				<?php } ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
