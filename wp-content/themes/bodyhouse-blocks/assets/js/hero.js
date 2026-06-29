/**
 * Effet au scroll sur le hero : l'image entière s'agrandit (scale up)
 * proportionnellement à mesure qu'on défile. Le contenu reste identique,
 * juste agrandi. En code propre, sans Elementor.
 */
( function () {
	'use strict';

	function init() {
		var media = document.querySelector( '.bh-hero__media' );
		var hero = document.querySelector( '.bh-hero' );
		if ( ! media || ! hero ) {
			return;
		}

		// L'image part à 1 (repos, taille parfaite) et grandit jusqu'à 1.25 au scroll max.
		var MIN_SCALE = 1;
		var MAX_SCALE = 1.25;

		media.style.willChange = 'transform';
		media.style.transformOrigin = 'center center';

		var ticking = false;

		function update() {
			var rect = hero.getBoundingClientRect();
			var heroHeight = rect.height || 1;
			// progress : 0 quand le hero est en haut du viewport, 1 quand il est sorti par le haut.
			var progress = Math.max( 0, Math.min( 1, -rect.top / heroHeight ) );

			var scale = MIN_SCALE + ( MAX_SCALE - MIN_SCALE ) * progress;
			media.style.transform = 'scale(' + scale.toFixed( 4 ) + ')';
			ticking = false;
		}

		function onScroll() {
			if ( ! ticking ) {
				window.requestAnimationFrame( update );
				ticking = true;
			}
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', onScroll, { passive: true } );
		update();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();

/**
 * Menu off-canvas : ouverture/fermeture du panneau latéral.
 */
( function () {
	'use strict';

	function initDrawer() {
		var burger = document.querySelector( '.bh-burger' );
		var drawer = document.getElementById( 'bh-drawer' );
		var overlay = document.querySelector( '.bh-drawer-overlay' );
		var closeBtn = document.querySelector( '.bh-drawer__close' );
		if ( ! burger || ! drawer || ! overlay ) {
			return;
		}

		// cale le drawer + overlay juste sous le header (hauteur réelle mesurée)
		function syncHeaderHeight() {
			var ann = document.querySelector( '.bh-announce' );
			var nav = document.querySelector( '.bh-nav' );
			var h = 0;
			if ( ann ) { h += ann.getBoundingClientRect().height; }
			if ( nav ) { h += nav.getBoundingClientRect().height; }
			if ( h > 0 ) {
				document.documentElement.style.setProperty( '--bh-header-h', Math.round( h ) + 'px' );
			}
		}

		function open() {
			syncHeaderHeight();
			overlay.hidden = false;
			// force le reflow pour que la transition d'opacité joue
			void overlay.offsetWidth;
			drawer.classList.add( 'is-open' );
			overlay.classList.add( 'is-open' );
			drawer.setAttribute( 'aria-hidden', 'false' );
			burger.setAttribute( 'aria-expanded', 'true' );
			document.body.classList.add( 'bh-no-scroll' );
		}

		function close() {
			drawer.classList.remove( 'is-open' );
			overlay.classList.remove( 'is-open' );
			drawer.setAttribute( 'aria-hidden', 'true' );
			burger.setAttribute( 'aria-expanded', 'false' );
			document.body.classList.remove( 'bh-no-scroll' );
			window.setTimeout( function () {
				overlay.hidden = true;
			}, 320 );
		}

		burger.addEventListener( 'click', function () {
			if ( drawer.classList.contains( 'is-open' ) ) {
				close();
			} else {
				open();
			}
		} );
		overlay.addEventListener( 'click', close );
		if ( closeBtn ) {
			closeBtn.addEventListener( 'click', close );
		}
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && drawer.classList.contains( 'is-open' ) ) {
				close();
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initDrawer );
	} else {
		initDrawer();
	}
} )();

/**
 * FAQ : ouverture/fermeture animée (smooth) des <details>.
 * Anime la hauteur du contenu et ferme les autres (accordéon).
 */
( function () {
	'use strict';

	function initFaq() {
		var items = document.querySelectorAll( '.bh-faq__item' );
		if ( ! items.length ) {
			return;
		}

		items.forEach( function ( item ) {
			var summary = item.querySelector( 'summary' );
			if ( ! summary ) {
				return;
			}
			// conteneur du contenu (tout sauf le summary) — on l'enveloppe
			var content = document.createElement( 'div' );
			content.className = 'bh-faq__content';
			var node = summary.nextSibling;
			while ( node ) {
				var next = node.nextSibling;
				content.appendChild( node );
				node = next;
			}
			item.appendChild( content );

			summary.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				var isOpen = item.classList.contains( 'is-open' );

				// plusieurs questions peuvent rester ouvertes simultanément
				// (pas de fermeture automatique des autres)

				if ( isOpen ) {
					// fermeture : le chevron tourne immédiatement (classe retirée tout de suite)
					item.classList.remove( 'is-open' );
					content.style.height = content.scrollHeight + 'px';
					requestAnimationFrame( function () { content.style.height = '0px'; } );
					content.addEventListener( 'transitionend', function te() {
						item.removeAttribute( 'open' );
						content.removeEventListener( 'transitionend', te );
					} );
				} else {
					item.setAttribute( 'open', '' );
					item.classList.add( 'is-open' );        // chevron tourne tout de suite
					content.style.height = '0px';
					requestAnimationFrame( function () {
						content.style.height = content.scrollHeight + 'px';
					} );
					content.addEventListener( 'transitionend', function te() {
						content.style.height = 'auto';
						content.removeEventListener( 'transitionend', te );
					} );
				}
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initFaq );
	} else {
		initFaq();
	}
} )();
