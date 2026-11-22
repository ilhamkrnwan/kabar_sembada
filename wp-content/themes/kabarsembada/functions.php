<?php
/**
 * Kabar Sembada theme setup.
 *
 * Pure block theme: layout lives in templates, parts and patterns. This file
 * only loads the stylesheet and script, registers block styles, and filters a
 * few core blocks.
 *
 * @package KabarSembada
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme supports and editor styles.
 *
 * @return void
 */
function kabarsembada_theme_setup() {
	remove_theme_support( 'core-block-patterns' );
	add_editor_style( 'assets/css/theme.css' );
}
add_action( 'after_setup_theme', 'kabarsembada_theme_setup' );

/**
 * Front-end stylesheet and script, versioned by file time.
 *
 * @return void
 */
function kabarsembada_theme_assets() {
	$css = get_theme_file_path( 'assets/css/theme.css' );
	$js  = get_theme_file_path( 'assets/js/theme.js' );

	wp_enqueue_style( 'kabarsembada-theme', get_theme_file_uri( 'assets/css/theme.css' ), array(), (string) filemtime( $css ) );
	wp_enqueue_script(
		'kabarsembada-theme',
		get_theme_file_uri( 'assets/js/theme.js' ),
		array(),
		(string) filemtime( $js ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'kabarsembada_theme_assets' );

/**
 * Block styles. Their CSS lives in assets/css/theme.css.
 *
 * @return void
 */
function kabarsembada_register_block_styles() {
	$styles = array(
		'core/post-template' => array(
			'ks-hero'      => __( 'Berita utama', 'kabarsembada' ),
			'ks-rail'      => __( 'Carousel kartu', 'kabarsembada' ),
			'ks-featured'  => __( 'Pilihan (1 besar + daftar)', 'kabarsembada' ),
			'ks-feed'      => __( 'Daftar berita', 'kabarsembada' ),
			'ks-grid'      => __( 'Grid 3 kolom', 'kabarsembada' ),
			'ks-grid-4'    => __( 'Grid 4 ringkas', 'kabarsembada' ),
			'ks-bento'     => __( 'Bento (1 besar + 4)', 'kabarsembada' ),
			'ks-bento-alt' => __( 'Bento asimetris', 'kabarsembada' ),
			'ks-overlay'   => __( 'Overlay potret', 'kabarsembada' ),
			'ks-spotlight' => __( 'Sorotan', 'kabarsembada' ),
			'ks-headlines' => __( 'Headline bernomor', 'kabarsembada' ),
			'ks-timeline'  => __( 'Kronologi', 'kabarsembada' ),
		),
		'core/post-terms'    => array(
			'ks-pill' => __( 'Pil', 'kabarsembada' ),
			'ks-tags' => __( 'Tag', 'kabarsembada' ),
		),
		'core/tag-cloud'     => array(
			'ks-pills' => __( 'Pil', 'kabarsembada' ),
		),
		'core/group'         => array(
			'ks-box'    => __( 'Kotak samping', 'kabarsembada' ),
			'ks-action' => __( 'Kotak aksi warga', 'kabarsembada' ),
		),
	);

	foreach ( $styles as $block => $variants ) {
		foreach ( $variants as $name => $label ) {
			register_block_style(
				$block,
				array(
					'name'  => $name,
					'label' => $label,
				)
			);
		}
	}
}
add_action( 'init', 'kabarsembada_register_block_styles' );

/**
 * Pattern category for the theme's sections.
 *
 * @return void
 */
function kabarsembada_register_pattern_category() {
	register_block_pattern_category( 'kabarsembada', array( 'label' => __( 'Kabar Sembada', 'kabarsembada' ) ) );
}
add_action( 'init', 'kabarsembada_register_pattern_category' );

/**
 * The article deck (an excerpt with the class ks-deck) shows only a
 * hand-written excerpt; an automatic one would repeat the first paragraph.
 *
 * @param string   $block_content Rendered block.
 * @param array    $block         Parsed block.
 * @param WP_Block $instance      Block instance.
 * @return string
 */
function kabarsembada_manual_deck_only( $block_content, $block, $instance ) {
	$class = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';

	if ( false === strpos( $class, 'ks-deck' ) ) {
		return $block_content;
	}

	$post_id = isset( $instance->context['postId'] ) ? (int) $instance->context['postId'] : (int) get_the_ID();

	return has_excerpt( $post_id ) ? $block_content : '';
}
add_filter( 'render_block_core/post-excerpt', 'kabarsembada_manual_deck_only', 10, 3 );

/**
 * A paragraph with the class ks-today always shows today's date, whatever text
 * it was saved with, so the top bar never goes stale.
 *
 * @param string $block_content Rendered paragraph.
 * @param array  $block         Parsed block.
 * @return string
 */
function kabarsembada_today_paragraph( $block_content, $block ) {
	$class = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';

	if ( false === strpos( $class, 'ks-today' ) ) {
		return $block_content;
	}

	return (string) preg_replace(
		'#(<p\b[^>]*>).*?(</p>)#s',
		'$1<time datetime="' . esc_attr( wp_date( 'Y-m-d' ) ) . '">' . esc_html( wp_date( 'j M Y' ) ) . '</time>$2',
		$block_content,
		1
	);
}
add_filter( 'render_block_core/paragraph', 'kabarsembada_today_paragraph', 10, 2 );

/**
 * Above-the-fold photos (class ks-priority: the home hero, the article's main
 * photo) load eagerly with high priority instead of lazily.
 *
 * @param string $block_content Rendered featured image.
 * @param array  $block         Parsed block.
 * @return string
 */
function kabarsembada_priority_image( $block_content, $block ) {
	$class = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';

	if ( false === strpos( $class, 'ks-priority' ) ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );

	if ( $processor->next_tag( 'img' ) ) {
		$processor->remove_attribute( 'loading' );
		$processor->set_attribute( 'fetchpriority', 'high' );
	}

	return $processor->get_updated_html();
}
add_filter( 'render_block_core/post-featured-image', 'kabarsembada_priority_image', 10, 2 );

/**
 * Account button (a core Login/out block with the class ks-account): shown
 * only to signed-in users, as an icon that opens the dashboard. Visitors have
 * no account on a news site, so they see nothing.
 *
 * @param string $block_content Rendered block.
 * @param array  $block         Parsed block.
 * @return string
 */
function kabarsembada_account_button( $block_content, $block ) {
	$class = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';

	if ( false === strpos( $class, 'ks-account' ) ) {
		return $block_content;
	}

	if ( ! is_user_logged_in() ) {
		return '';
	}

	return sprintf(
		'<div class="wp-block-loginout ks-account"><a href="%s" aria-label="%s"><svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></a></div>',
		esc_url( admin_url() ),
		esc_attr__( 'Dasbor', 'kabarsembada' )
	);
}
add_filter( 'render_block_core/loginout', 'kabarsembada_account_button', 10, 2 );

/**
 * Show one category on cards: the primary one when the post has several.
 *
 * Only for post-terms blocks inside a .ks-meta group (cards); the article
 * header and tag lists keep every term.
 *
 * @param WP_Term[]|false|WP_Error $terms    Terms.
 * @param int                      $post_id  Post ID.
 * @param string                   $taxonomy Taxonomy.
 * @return WP_Term[]|false|WP_Error
 */
function kabarsembada_card_primary_term( $terms, $post_id, $taxonomy ) {
	if ( 'category' !== $taxonomy || ! is_array( $terms ) || count( $terms ) < 2 || ! kabarsembada_card_terms_active() || ! function_exists( 'kabarsembada_primary_category' ) ) {
		return $terms;
	}

	// The lookup reads categories through this same filter; switch it off meanwhile.
	kabarsembada_card_terms_active( false );
	$primary = kabarsembada_primary_category( $post_id );
	kabarsembada_card_terms_active( true );

	return $primary ? array( $primary ) : array( $terms[0] );
}
add_filter( 'get_the_terms', 'kabarsembada_card_primary_term', 10, 3 );

/**
 * Whether a card's post-terms block is rendering right now.
 *
 * @param bool|null $set New state, or null to read.
 * @return bool
 */
function kabarsembada_card_terms_active( $set = null ) {
	static $active = false;

	if ( null !== $set ) {
		$active = $set;
	}

	return $active;
}

/**
 * Flag card post-terms blocks (attribute className contains ks-card-term)
 * while they render.
 *
 * @param string|null $pre   Short-circuit content.
 * @param array       $block Parsed block.
 * @return string|null
 */
function kabarsembada_card_terms_start( $pre, $block ) {
	if ( 'core/post-terms' === $block['blockName'] ) {
		$class = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
		kabarsembada_card_terms_active( false !== strpos( $class, 'ks-card-term' ) );
	}

	return $pre;
}
add_filter( 'pre_render_block', 'kabarsembada_card_terms_start', 10, 2 );

/**
 * Reset the card flag after the post-terms block.
 *
 * @param string $block_content Rendered block.
 * @return string
 */
function kabarsembada_card_terms_end( $block_content ) {
	kabarsembada_card_terms_active( false );

	return $block_content;
}
add_filter( 'render_block_core/post-terms', 'kabarsembada_card_terms_end' );

/**
 * Markup of a section with a heading and a Query Loop in one of the card
 * styles. Shared by patterns/cards-*.php so every variant uses the same card
 * blocks.
 *
 * @param string $style    Post template style slug (ks-grid-4, ks-bento, ...).
 * @param string $heading  Section heading.
 * @param int    $per_page Posts to show.
 * @param string $card     Card kind: photo, overlay, text or time.
 * @return string Block markup.
 */
function kabarsembada_cards_pattern( $style, $heading, $per_page, $card = 'photo' ) {
	$meta = '<!-- wp:group {"className":"ks-meta","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group ks-meta"><!-- wp:post-terms {"term":"category","className":"ks-card-term"} /-->

<!-- wp:post-date {"format":"human-diff"} /--></div>
<!-- /wp:group -->';

	$cards = array(
		'photo'   => '<!-- wp:post-featured-image {"isLink":true,"sizeSlug":"medium_large"} /-->

<!-- wp:group {"className":"ks-card__body","layout":{"type":"default"}} -->
<div class="wp-block-group ks-card__body">' . $meta . '

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":22} /--></div>
<!-- /wp:group -->',
		'overlay' => '<!-- wp:post-featured-image {"sizeSlug":"large"} /-->

<!-- wp:group {"className":"ks-card__body","layout":{"type":"default"}} -->
<div class="wp-block-group ks-card__body"><!-- wp:post-terms {"term":"category","className":"is-style-ks-pill ks-card-term"} /-->

<!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-date {"format":"human-diff"} /--></div>
<!-- /wp:group -->',
		'text'    => '<!-- wp:group {"className":"ks-card__body","layout":{"type":"default"}} -->
<div class="wp-block-group ks-card__body">' . $meta . '

<!-- wp:post-title {"level":3,"isLink":true} /--></div>
<!-- /wp:group -->',
		'time'    => '<!-- wp:post-date {"format":"H.i","className":"ks-time"} /-->

<!-- wp:group {"className":"ks-card__body","layout":{"type":"default"}} -->
<div class="wp-block-group ks-card__body"><!-- wp:post-title {"level":3,"isLink":true} /-->

<!-- wp:post-excerpt {"excerptLength":24} /--></div>
<!-- /wp:group -->',
	);

	$query = array(
		'queryId' => (int) wp_unique_id(),
		'query'   => array(
			'perPage'  => (int) $per_page,
			'pages'    => 0,
			'offset'   => 0,
			'postType' => 'post',
			'order'    => 'desc',
			'orderBy'  => 'date',
			'author'   => '',
			'search'   => '',
			'exclude'  => array(),
			'sticky'   => 'ignore',
			'inherit'  => false,
		),
	);

	return '<!-- wp:group {"align":"wide","className":"ks-section","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide ks-section"><!-- wp:group {"className":"ks-section__head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group ks-section__head"><!-- wp:heading -->
<h2 class="wp-block-heading">' . esc_html( $heading ) . '</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:query ' . wp_json_encode( $query ) . ' -->
<div class="wp-block-query"><!-- wp:post-template {"className":"is-style-' . esc_attr( $style ) . '"} -->
' . $cards[ $card ] . '
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->';
}
