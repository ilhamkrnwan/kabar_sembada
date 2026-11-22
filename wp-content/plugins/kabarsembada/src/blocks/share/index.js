import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import Preview from '../../shared/preview';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit: ( { attributes, context } ) => (
		<Preview
			name={ metadata.name }
			attributes={ attributes }
			// The block renderer sets up this post as the global post.
			urlQueryArgs={ { post_id: context.postId } }
			label={ metadata.title }
			empty={ __(
				'Tombol bagikan tampil di halaman artikel.',
				'kabarsembada'
			) }
		/>
	),
} );
