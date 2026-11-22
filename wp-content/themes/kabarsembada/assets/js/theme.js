/**
 * Kabar Sembada — small front-end behaviours. No dependencies; every feature
 * is an enhancement over markup that already works without it.
 */
( () => {
	const reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	const transition = ( fn ) =>
		document.startViewTransition && ! reduced ? document.startViewTransition( fn ) : fn();

	// Header border once the page scrolls.
	const header = document.querySelector( '.ks-site-header' );

	if ( header ) {
		const sync = () => header.classList.toggle( 'is-scrolled', window.scrollY > 8 );
		window.addEventListener( 'scroll', sync, { passive: true } );
		sync();
	}

	// Carousel arrows for rail lists, added to the section heading row.
	const icon = ( d ) =>
		`<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="${ d }"/></svg>`;

	document.querySelectorAll( '.is-style-ks-rail' ).forEach( ( rail ) => {
		const head = rail.closest( '.ks-section' )?.querySelector( '.ks-section__head' );

		if ( ! head || rail.children.length < 2 ) {
			return;
		}

		const wrap = document.createElement( 'div' );
		wrap.className = 'ks-arrows';
		wrap.innerHTML =
			`<button type="button" class="ks-arrow" data-dir="-1" aria-label="Sebelumnya">${ icon( 'm15 6-6 6 6 6' ) }</button>` +
			`<button type="button" class="ks-arrow" data-dir="1" aria-label="Berikutnya">${ icon( 'm9 6 6 6-6 6' ) }</button>`;
		head.append( wrap );

		const [ prev, next ] = wrap.children;
		const sync = () => {
			prev.disabled = rail.scrollLeft < 4;
			next.disabled = rail.scrollLeft + rail.clientWidth > rail.scrollWidth - 4;
		};

		wrap.addEventListener( 'click', ( event ) => {
			const button = event.target.closest( '.ks-arrow' );

			if ( button ) {
				rail.scrollBy( {
					left: Number( button.dataset.dir ) * rail.clientWidth * 0.7,
					behavior: reduced ? 'auto' : 'smooth',
				} );
			}
		} );
		rail.addEventListener( 'scroll', sync, { passive: true } );
		window.addEventListener( 'resize', sync );
		sync();
	} );

	// Category chips for the featured list, built from the cards' own
	// category classes. The first visible card becomes the big one.
	document.querySelectorAll( '.is-style-ks-featured' ).forEach( ( list ) => {
		const items = [ ...list.children ];
		const head = list.closest( '.ks-section' )?.querySelector( '.ks-section__head' );
		const cats = new Map();

		items.forEach( ( item ) => {
			const link = item.querySelector( '.ks-card-term a' );
			const slug = [ ...item.classList ].find( ( c ) => c.startsWith( 'category-' ) );

			if ( link && slug && ! cats.has( slug ) ) {
				cats.set( slug, link.textContent.trim() );
			}
		} );

		if ( ! head || cats.size < 2 ) {
			return;
		}

		const chips = document.createElement( 'div' );
		chips.className = 'ks-chips';
		chips.setAttribute( 'role', 'group' );
		chips.setAttribute( 'aria-label', 'Saring kategori' );
		chips.innerHTML = [ [ '', 'Semua' ], ...cats ]
			.map(
				( [ slug, name ], i ) =>
					`<button type="button" class="ks-chip" data-cat="${ slug }" aria-pressed="${ 0 === i }"></button>`
			)
			.join( '' );
		[ ...chips.children ].forEach( ( chip, i ) => {
			chip.textContent = 0 === i ? 'Semua' : [ ...cats.values() ][ i - 1 ];
		} );
		head.append( chips );

		chips.addEventListener( 'click', ( event ) => {
			const chip = event.target.closest( '.ks-chip' );

			if ( ! chip ) {
				return;
			}

			[ ...chips.children ].forEach( ( c ) => c.setAttribute( 'aria-pressed', String( c === chip ) ) );
			transition( () => {
				const shown = items.filter( ( item ) => ! chip.dataset.cat || item.classList.contains( chip.dataset.cat ) );
				items.forEach( ( item ) => ( item.hidden = ! shown.includes( item ) ) );
				// The big card is always the first visible one.
				shown.forEach( ( item ) => list.append( item ) );
				items.filter( ( item ) => item.hidden ).forEach( ( item ) => list.append( item ) );
			} );
		} );
	} );
} )();
