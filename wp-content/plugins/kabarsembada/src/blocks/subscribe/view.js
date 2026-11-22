document.querySelectorAll( '.ks-subscribe__form' ).forEach( ( form ) => {
	const note = form.parentElement.querySelector( '.ks-subscribe__note' );
	const input = form.querySelector( 'input[type="email"]' );
	const button = form.querySelector( 'button' );

	form.addEventListener( 'submit', async ( event ) => {
		event.preventDefault();

		if ( ! input.checkValidity() ) {
			note.textContent = form.dataset.invalid;
			input.focus();
			return;
		}

		button.disabled = true;

		try {
			const response = await window.fetch( form.action, {
				method: 'POST',
				body: new window.FormData( form ),
			} );
			const data = await response.json();

			note.textContent = data.message || form.dataset.error;

			if ( response.ok ) {
				form.reset();
			}
		} catch {
			note.textContent = form.dataset.error;
		}

		button.disabled = false;
	} );
} );
