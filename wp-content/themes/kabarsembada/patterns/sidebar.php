<?php
/**
 * Title: Samping: Terpopuler dan tag
 * Slug: kabarsembada/sidebar
 * Categories: kabarsembada
 * Description: Kotak Terpopuler (views) dan Tag Terpopuler.
 *
 * @package KabarSembada
 */

?>
<!-- wp:group {"className":"is-style-ks-box ks-section","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-ks-box ks-section"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Terpopuler', 'kabarsembada' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:kabarsembada/popular {"headingLevel":4} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"is-style-ks-box ks-section","layout":{"type":"default"}} -->
<div class="wp-block-group is-style-ks-box ks-section"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Tag Terpopuler', 'kabarsembada' ); ?></h3>
<!-- /wp:heading -->

<!-- wp:tag-cloud {"numberOfTags":12,"smallestFontSize":"0.875rem","largestFontSize":"0.875rem","className":"is-style-ks-pills"} /--></div>
<!-- /wp:group -->
