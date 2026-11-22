/**
 * SEO panel in the post sidebar: meta description and primary category.
 */
import { SelectControl, TextareaControl } from '@wordpress/components';
import { store as coreStore, useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import {
	PluginDocumentSettingPanel,
	store as editorStore,
} from '@wordpress/editor';
import { __, sprintf } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

const LIMIT = 160;

function SeoPanel() {
	const { postType, categoryIds } = useSelect( ( select ) => {
		const editor = select( editorStore );

		return {
			postType: editor.getCurrentPostType(),
			categoryIds: editor.getEditedPostAttribute( 'categories' ) || [],
		};
	}, [] );
	const categories = useSelect(
		( select ) =>
			categoryIds.length
				? select( coreStore ).getEntityRecords(
						'taxonomy',
						'category',
						{
							include: categoryIds,
							_fields: 'id,name',
						}
					) || []
				: [],
		[ categoryIds ]
	);
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	if ( 'post' !== postType || ! meta ) {
		return null;
	}

	const description = meta.kabarsembada_seo_description || '';
	const primary = meta.kabarsembada_primary_category || 0;

	return (
		<PluginDocumentSettingPanel
			name="kabarsembada-seo"
			title={ __( 'SEO', 'kabarsembada' ) }
		>
			<TextareaControl
				__nextHasNoMarginBottom
				label={ __( 'Deskripsi', 'kabarsembada' ) }
				help={ sprintf(
					/* translators: 1: characters used, 2: recommended limit. */
					__(
						'%1$d/%2$d karakter. Kosongkan untuk memakai ringkasan atau awal artikel.',
						'kabarsembada'
					),
					description.length,
					LIMIT
				) }
				value={ description }
				onChange={ ( value ) =>
					setMeta( { ...meta, kabarsembada_seo_description: value } )
				}
			/>
			{ categories.length > 1 && (
				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={ __( 'Kategori utama', 'kabarsembada' ) }
					help={ __(
						'Tampil di kartu berita, breadcrumb, dan data terstruktur.',
						'kabarsembada'
					) }
					value={ String( primary ) }
					options={ [
						{
							label: __( 'Otomatis (pertama)', 'kabarsembada' ),
							value: '0',
						},
						...categories.map( ( term ) => ( {
							label: term.name,
							value: String( term.id ),
						} ) ),
					] }
					onChange={ ( value ) =>
						setMeta( {
							...meta,
							kabarsembada_primary_category: Number( value ),
						} )
					}
				/>
			) }
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'kabarsembada-seo', { render: SeoPanel } );
