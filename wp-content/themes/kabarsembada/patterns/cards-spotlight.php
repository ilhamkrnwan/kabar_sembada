<?php
/**
 * Title: Kartu: Sorotan
 * Slug: kabarsembada/cards-spotlight
 * Categories: kabarsembada
 * Description: Satu berita lebar dengan ringkasan dan tiga judul pendamping tanpa foto.
 *
 * @package KabarSembada
 */

echo kabarsembada_cards_pattern( 'ks-spotlight', __( 'Sorotan', 'kabarsembada' ), 4, 'photo' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built and escaped in functions.php.
