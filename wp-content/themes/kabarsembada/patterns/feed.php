<?php
/**
 * Title: Daftar berita
 * Slug: kabarsembada/feed
 * Categories: kabarsembada
 * Description: Daftar berita terbaru dengan foto di kiri dan nomor halaman.
 *
 * @package KabarSembada
 */

?>
<!-- wp:query {"queryId":21,"query":{"perPage":12,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"ignore","inherit":false},"enhancedPagination":true} -->
<div class="wp-block-query"><!-- wp:post-template {"className":"is-style-ks-feed"} -->
<!-- wp:post-featured-image {"isLink":true,"sizeSlug":"medium"} /-->

<!-- wp:group {"className":"ks-card__body","layout":{"type":"default"}} -->
<div class="wp-block-group ks-card__body"><!-- wp:post-terms {"term":"category","className":"ks-card-term"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-date {"format":"human-diff"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"className":"ks-pagination","layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous {"label":"Sebelumnya"} /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next {"label":"Berikutnya"} /-->
<!-- /wp:query-pagination --></div>
<!-- /wp:query -->
