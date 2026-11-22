/**
 * Term pickers for block inspectors. Terms are stored by slug, so blocks keep
 * pointing at the right category or tag after the content import.
 */
import { FormTokenField, SelectControl } from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const useTerms = ( taxonomy ) =>
	useSelect(
		( select ) =>
			select( coreStore ).getEntityRecords( 'taxonomy', taxonomy, {
				per_page: 100,
				orderby: 'count',
				order: 'desc',
				_fields: 'id,name,slug',
			} ) || [],
		[ taxonomy ]
	);

export function CategorySelect( { label, value, onChange } ) {
	const terms = useTerms( 'category' );

	return (
		<SelectControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={ label }
			value={ value }
			options={ [
				{ label: __( 'Semua kategori', 'kabarsembada' ), value: '' },
				...terms.map( ( term ) => ( {
					label: term.name,
					value: term.slug,
				} ) ),
			] }
			onChange={ onChange }
		/>
	);
}

export function TagTokens( { label, value, onChange } ) {
	const terms = useTerms( 'post_tag' );
	const nameOf = ( slug ) =>
		terms.find( ( term ) => term.slug === slug )?.name || slug;
	const slugOf = ( name ) =>
		terms.find( ( term ) => term.name === name )?.slug;

	return (
		<FormTokenField
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			__experimentalExpandOnFocus
			label={ label }
			value={ value.map( nameOf ) }
			suggestions={ terms.map( ( term ) => term.name ) }
			onChange={ ( names ) =>
				onChange( names.map( slugOf ).filter( Boolean ) )
			}
		/>
	);
}
