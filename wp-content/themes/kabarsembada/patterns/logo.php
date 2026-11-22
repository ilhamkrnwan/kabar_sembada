<?php
/**
 * Title: Logo
 * Slug: kabarsembada/logo
 * Categories: kabarsembada
 * Inserter: no
 *
 * The Sembada mark ships with the theme, so the logo works with no upload and
 * survives the content import (which empties the media library).
 *
 * @package KabarSembada
 */

?>
<!-- wp:group {"className":"ks-logo","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group ks-logo"><!-- wp:image {"width":"40px","sizeSlug":"full","linkDestination":"custom","className":"ks-logo__mark"} -->
<figure class="wp-block-image size-full is-resized ks-logo__mark"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/logo.svg' ) ); ?>" alt="<?php esc_attr_e( 'Beranda Kabar Sembada', 'kabarsembada' ); ?>" style="width:40px"/></a></figure>
<!-- /wp:image -->

<!-- wp:site-title {"level":0} /--></div>
<!-- /wp:group -->
