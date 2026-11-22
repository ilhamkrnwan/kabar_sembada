/**
 * Motion panel for core/group and core/cover.
 */
import { InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

const BLOCKS = [ 'core/group', 'core/cover' ];

const ATTRIBUTES = {
	ksMotion: { type: 'string', default: 'none' },
	ksDuration: { type: 'number', default: 700 },
	ksDelay: { type: 'number', default: 0 },
	ksTrigger: { type: 'string', default: 'scroll' },
	ksStagger: { type: 'boolean', default: false },
};

const MOTIONS = [
	{ label: __( 'Tanpa animasi', 'kabarsembada' ), value: 'none' },
	{ label: __( 'Muncul', 'kabarsembada' ), value: 'fade' },
	{ label: __( 'Muncul dari bawah', 'kabarsembada' ), value: 'fade-up' },
	{ label: __( 'Muncul dari atas', 'kabarsembada' ), value: 'fade-down' },
	{ label: __( 'Geser dari kanan', 'kabarsembada' ), value: 'slide-left' },
	{ label: __( 'Geser dari kiri', 'kabarsembada' ), value: 'slide-right' },
	{ label: __( 'Perbesar', 'kabarsembada' ), value: 'zoom' },
];

const TRIGGERS = [
	{ label: __( 'Saat digulir', 'kabarsembada' ), value: 'scroll' },
	{ label: __( 'Saat halaman dimuat', 'kabarsembada' ), value: 'load' },
];

addFilter(
	'blocks.registerBlockType',
	'kabarsembada/motion-attributes',
	( settings, name ) =>
		BLOCKS.includes( name )
			? {
					...settings,
					attributes: { ...settings.attributes, ...ATTRIBUTES },
				}
			: settings
);

const withMotionPanel = createHigherOrderComponent(
	( BlockEdit ) => ( props ) => {
		if ( ! BLOCKS.includes( props.name ) ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;
		const motion = attributes.ksMotion || 'none';

		return (
			<>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody
						title={ __( 'Animasi', 'kabarsembada' ) }
						initialOpen={ false }
					>
						<SelectControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom
							label={ __( 'Efek', 'kabarsembada' ) }
							value={ motion }
							options={ MOTIONS }
							onChange={ ( value ) =>
								setAttributes( { ksMotion: value } )
							}
						/>
						{ 'none' !== motion && (
							<>
								<SelectControl
									__next40pxDefaultSize
									__nextHasNoMarginBottom
									label={ __( 'Pemicu', 'kabarsembada' ) }
									value={ attributes.ksTrigger || 'scroll' }
									options={ TRIGGERS }
									onChange={ ( value ) =>
										setAttributes( { ksTrigger: value } )
									}
								/>
								<ToggleControl
									__nextHasNoMarginBottom
									label={ __(
										'Animasikan isi satu per satu',
										'kabarsembada'
									) }
									help={ __(
										'Cocok untuk grid berita: tiap kartu muncul bergantian.',
										'kabarsembada'
									) }
									checked={ !! attributes.ksStagger }
									onChange={ ( value ) =>
										setAttributes( { ksStagger: value } )
									}
								/>
								<RangeControl
									__next40pxDefaultSize
									__nextHasNoMarginBottom
									label={ __(
										'Durasi (ms)',
										'kabarsembada'
									) }
									value={ attributes.ksDuration ?? 700 }
									min={ 100 }
									max={ 2000 }
									step={ 50 }
									onChange={ ( value ) =>
										setAttributes( { ksDuration: value } )
									}
								/>
								<RangeControl
									__next40pxDefaultSize
									__nextHasNoMarginBottom
									label={ __( 'Jeda (ms)', 'kabarsembada' ) }
									value={ attributes.ksDelay ?? 0 }
									min={ 0 }
									max={ 2000 }
									step={ 50 }
									onChange={ ( value ) =>
										setAttributes( { ksDelay: value } )
									}
								/>
							</>
						) }
					</PanelBody>
				</InspectorControls>
			</>
		);
	},
	'withMotionPanel'
);

addFilter( 'editor.BlockEdit', 'kabarsembada/motion-panel', withMotionPanel );
