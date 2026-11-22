<?php
/**
 * Title: Kartu: Bento
 * Slug: kabarsembada/cards-bento
 * Categories: kabarsembada
 * Description: Satu berita besar dengan ringkasan, empat kartu kecil di sampingnya.
 *
 * @package KabarSembada
 */

echo kabarsembada_cards_pattern( 'ks-bento', __( 'Pilihan Kanal', 'kabarsembada' ), 5, 'photo' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built and escaped in functions.php.
