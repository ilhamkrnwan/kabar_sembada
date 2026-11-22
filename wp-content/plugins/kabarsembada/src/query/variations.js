/**
 * Query Loop variations that preset the plugin's query modes
 * (includes/query.php). The editor preview shows the latest posts; the front
 * end applies the real order.
 */
import { registerBlockVariation } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

const base = {
	perPage: 5,
	pages: 0,
	offset: 0,
	postType: 'post',
	order: 'desc',
	orderBy: 'date',
	author: '',
	search: '',
	exclude: [],
	sticky: 'exclude',
	inherit: false,
};

const card = [
	[
		'core/post-template',
		{},
		[
			[ 'core/post-featured-image', { isLink: true } ],
			[ 'core/post-terms', { term: 'category' } ],
			[ 'core/post-title', { isLink: true, level: 3 } ],
			[ 'core/post-date', { displayType: 'date' } ],
		],
	],
];

const variations = [
	{
		name: 'kabarsembada/most-read',
		title: __( 'Paling Dibaca', 'kabarsembada' ),
		description: __( 'Artikel dengan views terbanyak.', 'kabarsembada' ),
		query: { ksOrder: 'views' },
	},
	{
		name: 'kabarsembada/trending',
		title: __( 'Sedang Ramai', 'kabarsembada' ),
		description: __(
			'Views terbanyak dari artikel 7 hari terakhir.',
			'kabarsembada'
		),
		query: { ksOrder: 'views', ksDays: 7 },
	},
	{
		name: 'kabarsembada/related',
		title: __( 'Berita Terkait', 'kabarsembada' ),
		description: __(
			'Artikel sekategori dengan artikel yang sedang dibuka.',
			'kabarsembada'
		),
		query: { ksRelated: true, perPage: 3 },
	},
];

variations.forEach( ( { name, title, description, query } ) =>
	registerBlockVariation( 'core/query', {
		name,
		title,
		description,
		category: 'kabarsembada',
		scope: [ 'inserter' ],
		attributes: {
			namespace: name,
			query: { ...base, ...query },
		},
		innerBlocks: card,
		// Keep the variation recognised after the user edits the query.
		isActive: [ 'namespace' ],
	} )
);
