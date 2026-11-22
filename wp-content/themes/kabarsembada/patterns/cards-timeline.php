<?php
/**
 * Title: Kartu: Kronologi
 * Slug: kabarsembada/cards-timeline
 * Categories: kabarsembada
 * Description: Garis waktu dengan jam terbit, untuk liputan berkelanjutan.
 *
 * @package KabarSembada
 */

echo kabarsembada_cards_pattern( 'ks-timeline', __( 'Kronologi', 'kabarsembada' ), 6, 'time' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Block markup built and escaped in functions.php.
