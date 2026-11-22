/**
 * Search dialog: live results from REST, keyboard navigation, recent searches
 * in localStorage. Ported from the portal prototype (theme-1/search.js).
 */
const dlg = document.querySelector( '.ks-search' );

if ( dlg ) {
	// The block sits inside layout containers (flex groups reset child
	// margins); a dialog belongs at the top of the page.
	document.body.append( dlg );

	const KEY = 'ks-recent';
	const t = JSON.parse( dlg.dataset.i18n || '{}' );
	const $ = ( selector ) => dlg.querySelector( selector );
	const input = $( 'input' );
	const clearBtn = $( '.ks-search__clear' );
	const idle = $( '.ks-search__idle' );
	const results = $( '.ks-search__results' );
	const status = $( '[data-status]' );
	const recentList = $( '.ks-search__recent' );
	const clearAll = $( '[data-clear-all]' );
	const icon = ( name ) =>
		dlg.querySelector( `.ks-search__${ name } .ks-search__i` )?.outerHTML ||
		'';
	let active = -1;
	let controller;
	let timer;

	const esc = ( value ) =>
		String( value ).replace(
			/[&<>"]/g,
			( c ) =>
				( { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' } )[
					c
				]
		);
	const reEsc = ( value ) => value.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' );
	const fill = ( text, value ) => text.replace( /%[sd]/, value );
	const resultsUrl = ( q ) => {
		const url = new URL( dlg.dataset.resultsUrl );
		url.searchParams.set( 's', q );
		return url.toString();
	};

	const getRecent = () => {
		try {
			return JSON.parse( window.localStorage.getItem( KEY ) ) || [];
		} catch {
			return [];
		}
	};
	const renderRecent = () => {
		const list = getRecent();
		clearAll.hidden = ! list.length;
		recentList.innerHTML = list.length
			? list
					.map(
						( r ) =>
							`<li><button type="button" class="ks-search__q" data-q="${ esc(
								r
							) }">${ esc(
								r
							) }</button><button type="button" class="ks-search__x" data-remove="${ esc(
								r
							) }" aria-label="${ esc(
								fill( t.remove, r )
							) }">${ icon( 'clear' ) }</button></li>`
					)
					.join( '' )
			: `<li class="ks-search__empty">${ esc( t.empty ) }</li>`;
	};
	const setRecent = ( list ) => {
		try {
			window.localStorage.setItem(
				KEY,
				JSON.stringify( list.slice( 0, 5 ) )
			);
		} catch {}
		renderRecent();
	};
	const saveRecent = ( q ) =>
		q && setRecent( [ q, ...getRecent().filter( ( r ) => r !== q ) ] );

	const reset = () => {
		active = -1;
		input.removeAttribute( 'aria-activedescendant' );
	};

	const showIdle = () => {
		reset();
		results.hidden = true;
		idle.hidden = false;
		input.setAttribute( 'aria-expanded', 'false' );
		status.textContent = '';
	};

	const showResults = ( q, items ) => {
		const words = q.toLowerCase().split( /\s+/ ).filter( Boolean );
		const mark = ( text ) =>
			esc( text ).replace(
				new RegExp( `(${ words.map( reEsc ).join( '|' ) })`, 'giu' ),
				'<mark>$1</mark>'
			);

		reset();
		results.innerHTML = items.length
			? `<div role="listbox" id="ks-search-listbox">${ items
					.map(
						( item, i ) =>
							`<a href="${ esc(
								item.url
							) }" role="option" id="ks-search-opt-${ i }" class="ks-search__hit">${
								item.category
									? `<span class="ks-search__cat">${ esc(
											item.category
										) }</span>`
									: ''
							}<span class="ks-search__title">${ mark(
								item.title
							) }</span></a>`
					)
					.join( '' ) }</div><a class="ks-search__all" href="${ esc(
					resultsUrl( q )
				) }">${ esc( fill( t.all, q ) ) }</a>`
			: `<div class="ks-search__none"><strong>${ esc(
					fill( t.noneFor, q )
				) }</strong><span>${ esc( t.tip ) }</span></div>`;
		results.hidden = false;
		idle.hidden = true;
		input.setAttribute( 'aria-expanded', String( items.length > 0 ) );
		status.textContent = items.length
			? fill( t.found, items.length )
			: t.none;
	};

	const search = ( value ) => {
		const q = value.trim();

		clearBtn.hidden = ! value;
		window.clearTimeout( timer );
		controller?.abort();

		if ( q.length < 2 ) {
			showIdle();
			return;
		}

		timer = window.setTimeout( async () => {
			controller = new window.AbortController();

			try {
				const url = new URL( dlg.dataset.api );
				url.searchParams.set( 'q', q );
				const response = await window.fetch( url, {
					signal: controller.signal,
				} );
				const data = await response.json();
				showResults( q, data.results || [] );
			} catch ( error ) {
				if ( 'AbortError' !== error.name ) {
					status.textContent = t.error;
				}
			}
		}, 200 );
	};

	const move = ( step ) => {
		const options = [ ...results.querySelectorAll( '[role="option"]' ) ];

		if ( ! options.length ) {
			return;
		}

		options[ active ]?.classList.remove( 'is-active' );
		active = ( active + step + options.length ) % options.length;
		options[ active ].classList.add( 'is-active' );
		options[ active ].scrollIntoView( { block: 'nearest' } );
		input.setAttribute( 'aria-activedescendant', options[ active ].id );
	};

	const open = ( prefill = '' ) => {
		if ( dlg.open ) {
			return;
		}

		renderRecent();
		input.value = prefill;
		search( prefill );
		dlg.showModal();
		input.focus();
	};
	const close = () => dlg.close();

	input.addEventListener( 'input', () => search( input.value ) );
	input.addEventListener( 'keydown', ( event ) => {
		if ( 'ArrowDown' === event.key ) {
			event.preventDefault();
			move( 1 );
		} else if ( 'ArrowUp' === event.key ) {
			event.preventDefault();
			move( -1 );
		} else if ( 'Enter' === event.key && active > -1 ) {
			event.preventDefault();
			saveRecent( input.value.trim() );
			results.querySelectorAll( '[role="option"]' )[ active ].click();
		}
	} );
	// Plain submit goes to the search results page (the form's action).
	$( 'form' ).addEventListener( 'submit', ( event ) => {
		const q = input.value.trim();

		if ( ! q ) {
			event.preventDefault();
			input.focus();
			return;
		}

		saveRecent( q );
	} );
	clearBtn.addEventListener( 'click', () => {
		input.value = '';
		search( '' );
		input.focus();
	} );
	$( '.ks-search__cancel' ).addEventListener( 'click', close );
	// Esc in a search input only clears the text in some browsers.
	dlg.addEventListener( 'keydown', ( event ) => {
		if ( 'Escape' === event.key ) {
			event.preventDefault();
			close();
		}
	} );
	clearAll.addEventListener( 'click', () => {
		setRecent( [] );
		input.focus();
	} );
	dlg.addEventListener( 'click', ( event ) => {
		if ( event.target === dlg ) {
			close(); // Backdrop.
			return;
		}

		const q = event.target.closest( '[data-q]' )?.dataset.q;

		if ( q ) {
			input.value = q;
			search( q );
			input.focus();
			return;
		}

		const remove = event.target.closest( '[data-remove]' )?.dataset.remove;

		if ( remove ) {
			setRecent( getRecent().filter( ( r ) => r !== remove ) );
			input.focus();
			return;
		}

		if ( event.target.closest( '.ks-search__hit, .ks-search__all' ) ) {
			saveRecent( input.value.trim() );
		}
	} );

	document.addEventListener( 'click', ( event ) => {
		const trigger = event.target.closest( '[data-search-open]' );

		if ( trigger ) {
			event.preventDefault();
			open( trigger.value || trigger.dataset.searchOpen || '' );
		}
	} );
	document.addEventListener( 'keydown', ( event ) => {
		if (
			'/' === event.key &&
			! dlg.open &&
			! event.target.closest(
				'input, textarea, select, [contenteditable]'
			)
		) {
			event.preventDefault();
			open();
		}
	} );
}
