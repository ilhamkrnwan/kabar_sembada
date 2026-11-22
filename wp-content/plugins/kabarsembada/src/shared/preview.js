/**
 * Server-rendered editor preview with an honest empty state.
 */
import { useBlockProps } from '@wordpress/block-editor';
import { Placeholder } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

export default function Preview( {
	name,
	attributes,
	label,
	empty,
	urlQueryArgs,
} ) {
	return (
		<div { ...useBlockProps() }>
			<ServerSideRender
				block={ name }
				attributes={ attributes }
				urlQueryArgs={ urlQueryArgs }
				EmptyResponsePlaceholder={ () => (
					<Placeholder label={ label } instructions={ empty } />
				) }
			/>
		</div>
	);
}
