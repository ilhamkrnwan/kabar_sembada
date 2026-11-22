import { InspectorControls } from '@wordpress/block-editor';
import { registerBlockType } from '@wordpress/blocks';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import Preview from '../../shared/preview';
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
						label={ __( 'Placeholder', 'kabarsembada' ) }
						value={ attributes.placeholder }
						onChange={ ( placeholder ) =>
							setAttributes( { placeholder } )
						}
					/>
					<TextControl
						__next40pxDefaultSize
						__nextHasNoMarginBottom
						label={ __( 'Teks tombol', 'kabarsembada' ) }
						value={ attributes.buttonText }
						onChange={ ( buttonText ) =>
							setAttributes( { buttonText } )
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
