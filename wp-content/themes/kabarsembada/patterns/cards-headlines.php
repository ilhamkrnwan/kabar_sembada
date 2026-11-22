<?php
/**
 * Title: Kartu: Headline bernomor
 * Slug: kabarsembada/cards-headlines
 * Categories: kabarsembada
 * Description: Daftar judul bernomor tanpa foto, dua kolom.
 *
 * @package KabarSembada
 */

echo kabarsembada_cards_pattern( 'ks-headlines', __( 'Rangkuman Hari Ini', 'kabarsembada' ), 6, 'text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built and escaped in functions.php.
