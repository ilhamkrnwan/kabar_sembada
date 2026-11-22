/**
 * Accessible tabs: click, arrow keys, Home/End. Inactive panels are inert.
 */
document.querySelectorAll( '.ks-tabs' ).forEach( ( root ) => {
	const list = root.querySelector( '[role="tablist"]' );
	const tabs = [ ...list.querySelectorAll( '[role="tab"]' ) ];
	const panels = tabs.map( ( tab ) =>
		root.querySelector( `#${ tab.getAttribute( 'aria-controls' ) }` )
	);

	const select = ( index, focus ) => {
		tabs.forEach( ( tab, i ) => {
			const on = i === index;

			tab.setAttribute( 'aria-selected', String( on ) );
			tab.tabIndex = on ? 0 : -1;
			panels[ i ].classList.toggle( 'is-active', on );
			panels[ i ].inert = ! on;
		} );
		list.style.setProperty( '--ks-active', index );

		if ( focus ) {
			tabs[ index ].focus();
		}
	};

	tabs.forEach( ( tab, i ) => {
		tab.addEventListener( 'click', () => select( i ) );
		tab.addEventListener( 'keydown', ( event ) => {
			const last = tabs.length - 1;
			const next = {
				ArrowRight: i === last ? 0 : i + 1,
				ArrowLeft: i === 0 ? last : i - 1,
				Home: 0,
				End: last,
			}[ event.key ];

			if ( undefined !== next ) {
				event.preventDefault();
				select( next, true );
			}
		} );
	} );
} );
