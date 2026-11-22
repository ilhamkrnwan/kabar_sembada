import { InspectorControls } from '@wordpress/block-editor';
import { registerBlockType } from '@wordpress/blocks';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import Preview from '../../shared/preview';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit: ( { attributes, setAttributes } ) => (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pengaturan', 'kabarsembada' ) }>
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Jumlah artikel', 'kabarsembada' ) }
						value={ attributes.number }
						min={ 3 }
						max={ 10 }
						onChange={ ( number ) => setAttributes( { number } ) }
					/>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Rentang', 'kabarsembada' ) }
						value={ String( attributes.days ) }
						options={ [
							{
								label: __( 'Sepanjang waktu', 'kabarsembada' ),
								value: '0',
							},
							{
								label: __( '7 hari terakhir', 'kabarsembada' ),
								value: '7',
							},
							{
								label: __( '30 hari terakhir', 'kabarsembada' ),
								value: '30',
							},
						] }
						onChange={ ( days ) =>
							setAttributes( { days: Number( days ) } )
						}
					/>
					<SelectControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Tingkat judul', 'kabarsembada' ) }
						value={ String( attributes.headingLevel ) }
						options={ [ 2, 3, 4 ].map( ( level ) => ( {
							label: `H${ level }`,
							value: String( level ),
						} ) ) }
						onChange={ ( level ) =>
							setAttributes( { headingLevel: Number( level ) } )
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
