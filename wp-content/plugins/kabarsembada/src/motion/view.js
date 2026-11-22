/**
 * GSAP runtime for the motion panel.
 *
 * Elements are visible by default. Each one is hidden only by the tween that
 * reveals it (gsap.from), so a failed script or reduced motion leaves content
 * as it is.
 */
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin( ScrollTrigger );

const FROM = {
	fade: {},
	'fade-up': { y: 24 },
	'fade-down': { y: -24 },
	'slide-left': { x: 36 },
	'slide-right': { x: -36 },
	zoom: { scale: 0.94 },
};

function animate( element ) {
	const { ksMotion, ksTrigger, ksDuration, ksDelay, ksStagger } =
		element.dataset;
	// Query Loops wrap their cards in one <ul>; stagger the cards, not the list.
	const list = element.querySelector( '.wp-block-post-template' );
	const targets = ksStagger ? [ ...( list || element ).children ] : element;

	gsap.from( targets, {
		...( FROM[ ksMotion ] || {} ),
		opacity: 0,
		duration: Number( ksDuration || 700 ) / 1000,
		delay: Number( ksDelay || 0 ) / 1000,
		ease: 'expo.out',
		stagger: ksStagger ? 0.08 : 0,
		clearProps: 'transform,opacity',
		scrollTrigger:
			'load' === ksTrigger
				? undefined
				: { trigger: element, start: 'top 88%', once: true },
	} );
}

function init() {
	gsap.matchMedia().add( '(prefers-reduced-motion: no-preference)', () => {
		document.querySelectorAll( '.ks-motion' ).forEach( animate );
	} );
}

if ( 'loading' === document.readyState ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
