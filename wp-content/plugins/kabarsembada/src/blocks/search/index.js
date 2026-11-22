import { InspectorControls } from '@wordpress/block-editor';
import { registerBlockType } from '@wordpress/blocks';
import { PanelBody, RangeControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import Preview from '../../shared/preview';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit: ( { attributes, setAttributes } ) => (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pengaturan', 'kabarsembada' ) }>
					<ToggleControl
						__nextHasNoMarginBottom
						label={ __( 'Tampilkan teks "Cari"', 'kabarsembada' ) }
						checked={ attributes.showLabel }
						onChange={ ( showLabel ) =>
							setAttributes( { showLabel } )
						}
					/>
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Topik populer (tag)', 'kabarsembada' ) }
						value={ attributes.topics }
						min={ 0 }
						max={ 10 }
						onChange={ ( topics ) => setAttributes( { topics } ) }
					/>
					<RangeControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Kanal (kategori)', 'kabarsembada' ) }
						value={ attributes.channels }
						min={ 0 }
						max={ 8 }
						onChange={ ( channels ) =>
							setAttributes( { channels } )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<Preview
				name={ metadata.name }
				attributes={ attributes }
				label={ metadata.title }
			/>
		</>
	),
} );
