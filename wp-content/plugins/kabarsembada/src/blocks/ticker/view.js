document.querySelectorAll( '.ks-ticker' ).forEach( ( ticker ) => {
	const toggle = ticker.querySelector( '.ks-ticker__toggle' );

	toggle?.addEventListener( 'click', () => {
		const paused = ticker.classList.toggle( 'is-paused' );

		toggle.setAttribute( 'aria-pressed', String( paused ) );
	} );
} );
