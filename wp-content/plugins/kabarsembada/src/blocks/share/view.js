document.querySelectorAll( '.ks-share__copy' ).forEach( ( button ) => {
	const note = button.parentElement.querySelector( '.ks-share__note' );

	button.addEventListener( 'click', async () => {
		try {
			await window.navigator.clipboard.writeText( button.dataset.url );
			button.dataset.copied = '';
			note.textContent = note.dataset.done;
		} catch {
			note.textContent = note.dataset.fail;
		}

		window.setTimeout( () => {
			delete button.dataset.copied;
			note.textContent = '';
		}, 2500 );
	} );
} );
