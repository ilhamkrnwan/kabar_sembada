<?php
/**
 * Title: Beranda
 * Slug: kabarsembada/home
 * Categories: kabarsembada
 * Description: Hero + tab berita, Sedang Ramai, Pilihan Redaksi, Fokus Berita, dan Berita Terkini dengan Terpopuler.
 * Inserter: no
 *
 * Sections hide themselves when they have nothing to show (assets/css/theme.css).
 *
 * @package KabarSembada
 */

?>
<!-- wp:columns {"align":"wide","className":"ks-hero"} -->
<div class="wp-block-columns alignwide ks-hero"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:query {"queryId":11,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"ignore","inherit":false}} -->
<div class="wp-block-query"><!-- wp:post-template {"className":"is-style-ks-hero"} -->
<!-- wp:post-featured-image {"isLink":true,"sizeSlug":"large","className":"ks-priority"} /-->

<!-- wp:group {"className":"ks-hero__body","layout":{"type":"default"}} -->
<div class="wp-block-group ks-hero__body"><!-- wp:group {"className":"ks-meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group ks-meta"><!-- wp:post-terms {"term":"category","className":"is-style-ks-pill ks-card-term"} /-->

<!-- wp:post-date {"format":"human-diff"} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"level":2,"isLink":true} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:kabarsembada/post-tabs /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"align":"wide","className":"ks-columns"} -->
<div class="wp-block-columns alignwide ks-columns"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:group {"className":"ks-section","layout":{"type":"default"},"ksMotion":"fade-up"} -->
<div class="wp-block-group ks-section"><!-- wp:group {"className":"ks-section__head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group ks-section__head"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Sedang Ramai', 'kabarsembada' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":12,"query":{"perPage":8,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false,"ksOrder":"views","ksDays":7},"namespace":"kabarsembada/trending"} -->
<div class="wp-block-query"><!-- wp:post-template {"className":"is-style-ks-rail"} -->
<!-- wp:post-featured-image {"isLink":true,"sizeSlug":"medium_large"} /-->

<!-- wp:group {"className":"ks-meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group ks-meta"><!-- wp:post-terms {"term":"category","className":"ks-card-term"} /-->

<!-- wp:post-date {"format":"human-diff"} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"level":3,"isLink":true} /-->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"ks-section","layout":{"type":"default"},"ksMotion":"fade-up","ksStagger":true} -->
<div class="wp-block-group ks-section"><!-- wp:group {"className":"ks-section__head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group ks-section__head"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Pilihan Redaksi', 'kabarsembada' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:query {"queryId":13,"query":{"perPage":6,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"only","inherit":false}} -->
<div class="wp-block-query"><!-- wp:post-template {"className":"is-style-ks-featured"} -->
<!-- wp:post-featured-image {"isLink":true,"sizeSlug":"medium_large"} /-->

<!-- wp:group {"className":"ks-card__body","layout":{"type":"default"}} -->
<div class="wp-block-group ks-card__body"><!-- wp:group {"className":"ks-meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group ks-meta"><!-- wp:post-terms {"term":"category","className":"ks-card-term"} /-->

<!-- wp:post-date {"format":"human-diff"} /--></div>
<!-- /wp:group -->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":22} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:group {"className":"ks-section","layout":{"type":"default"}} -->
<div class="wp-block-group ks-section"><!-- wp:group {"className":"ks-section__head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group ks-section__head"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Fokus Berita', 'kabarsembada' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:kabarsembada/post-tabs {"mode":"topics","number":3} /--></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"ks-section","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ks-section"><!-- wp:group {"className":"ks-section__head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group ks-section__head"><!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Berita Terkini', 'kabarsembada' ); ?></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:columns {"className":"ks-columns"} -->
<div class="wp-block-columns ks-columns"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:pattern {"slug":"kabarsembada/feed"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%","className":"ks-sticky"} -->
<div class="wp-block-column ks-sticky" style="flex-basis:33.33%"><!-- wp:pattern {"slug":"kabarsembada/sidebar"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
