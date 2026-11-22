<?php
/**
 * Title: Footer
 * Slug: kabarsembada/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 *
 * @package KabarSembada
 */

?>
<!-- wp:group {"className":"ks-footer","layout":{"type":"constrained"}} -->
<div class="wp-block-group ks-footer"><!-- wp:columns {"align":"wide","className":"ks-footer__grid"} -->
<div class="wp-block-columns alignwide ks-footer__grid"><!-- wp:column {"width":"40%"} -->
<div class="wp-block-column" style="flex-basis:40%"><!-- wp:group {"className":"ks-logo","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group ks-logo"><!-- wp:site-logo {"width":38} /-->

<!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->

<!-- wp:site-tagline /-->

<!-- wp:social-links {"size":"has-small-icon-size","className":"is-style-logos-only ks-socials","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<ul class="wp-block-social-links has-small-icon-size is-style-logos-only ks-socials"><!-- wp:social-link {"url":"","service":"facebook"} /-->

<!-- wp:social-link {"url":"","service":"x"} /-->

<!-- wp:social-link {"url":"","service":"instagram"} /-->

<!-- wp:social-link {"url":"","service":"youtube"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"35%"} -->
<div class="wp-block-column" style="flex-basis:35%"><!-- wp:heading {"level":2,"className":"ks-footer__title"} -->
<h2 class="wp-block-heading ks-footer__title"><?php esc_html_e( 'Kanal', 'kabarsembada' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:categories {"className":"ks-footer__list ks-footer__list--cols"} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"25%"} -->
<div class="wp-block-column" style="flex-basis:25%"><!-- wp:heading {"level":2,"className":"ks-footer__title"} -->
<h2 class="wp-block-heading ks-footer__title"><?php esc_html_e( 'Kabar Sembada', 'kabarsembada' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:page-list {"className":"ks-footer__list"} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:group {"align":"wide","className":"ks-footer__bottom","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide ks-footer__bottom"><!-- wp:paragraph -->
<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Kabar Sembada. <?php esc_html_e( 'Hak cipta dilindungi.', 'kabarsembada' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
