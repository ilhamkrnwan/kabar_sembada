import { InspectorControls } from '@wordpress/block-editor';
import { registerBlockType } from '@wordpress/blocks';
import { PanelBody, RangeControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import Preview from '../../shared/preview';
import { CategorySelect } from '../../shared/terms';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit: ( { attributes, setAttributes } ) => (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pengaturan', 'kabarsembada' ) }>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Label', 'kabarsembada' ) }
						value={ attributes.label }
						onChange={ ( label ) => setAttributes( { label } ) }
					/>
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Jumlah judul', 'kabarsembada' ) }
						value={ attributes.number }
						min={ 3 }
						max={ 12 }
						onChange={ ( number ) => setAttributes( { number } ) }
					/>
					<CategorySelect
						label={ __( 'Kategori', 'kabarsembada' ) }
						value={ attributes.category }
						onChange={ ( category ) =>
							setAttributes( { category } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<Preview
				name={ metadata.name }
				attributes={ attributes }
				label={ metadata.title }
				empty={ __( 'Belum ada artikel terbit.', 'kabarsembada' ) }
			/>
		</>
	),
} );
