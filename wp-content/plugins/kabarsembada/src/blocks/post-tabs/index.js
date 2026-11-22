import { InspectorControls } from '@wordpress/block-editor';
import { registerBlockType } from '@wordpress/blocks';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import Preview from '../../shared/preview';
import { TagTokens } from '../../shared/terms';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit: ( { attributes, setAttributes } ) => (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pengaturan', 'kabarsembada' ) }>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Isi tab', 'kabarsembada' ) }
						value={ attributes.mode }
						options={ [
							{
								label: __(
									'Terbaru / Paling Dibaca',
									'kabarsembada'
								),
								value: 'latest',
							},
							{
								label: __(
									'Topik pilihan (Fokus Berita)',
									'kabarsembada'
								),
								value: 'topics',
							},
						] }
						onChange={ ( mode ) => setAttributes( { mode } ) }
					/>
					{ 'topics' === attributes.mode && (
						<TagTokens
							label={ __(
								'Topik (tag), urut sesuai tab',
								'kabarsembada'
							) }
							value={ attributes.tags }
							onChange={ ( tags ) => setAttributes( { tags } ) }
						/>
					) }
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Artikel per tab', 'kabarsembada' ) }
						value={ attributes.number }
						min={ 2 }
						max={ 6 }
						onChange={ ( number ) => setAttributes( { number } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<Preview
				name={ metadata.name }
				attributes={ attributes }
				label={ metadata.title }
				empty={
					'topics' === attributes.mode
						? __(
								'Pilih tag yang sudah punya artikel di panel samping.',
								'kabarsembada'
							)
						: __( 'Belum ada artikel terbit.', 'kabarsembada' )
				}
			/>
		</>
	),
} );
