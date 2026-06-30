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
 * Sous-menus du drawer : chaque item parent reçoit un bouton chevron
 * qui déplie/replie son sous-menu en accordéon (un seul ouvert à la fois).
 */
( function () {
	'use strict';

	var CHEVRON = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';

	function initSubmenus() {
		var parents = document.querySelectorAll( '.bh-drawer__main .menu-item-has-children' );
		if ( ! parents.length ) {
			return;
		}

		parents.forEach( function ( parent ) {
			var link = parent.querySelector( ':scope > a' );
			var sub = parent.querySelector( ':scope > .sub-menu' );
			if ( ! link || ! sub ) {
				return;
			}

			// enveloppe lien + chevron dans une ligne flex (.bh-row)
			var row = document.createElement( 'div' );
			row.className = 'bh-row';
			link.insertAdjacentElement( 'beforebegin', row );
			row.appendChild( link );

			var btn = document.createElement( 'button' );
			btn.type = 'button';
			btn.className = 'bh-submenu-toggle';
			btn.setAttribute( 'aria-label', 'Afficher le sous-menu' );
			btn.setAttribute( 'aria-expanded', 'false' );
			btn.innerHTML = CHEVRON;
			row.appendChild( btn );

			// referme un item avec l'animation de sortie
			function closeItem( item ) {
				if ( ! item.classList.contains( 'is-open' ) ) {
					return;
				}
				item.classList.remove( 'is-open' );
				item.classList.add( 'is-closing' );
				var tgl = item.querySelector( ':scope > .bh-row .bh-submenu-toggle' );
				if ( tgl ) { tgl.setAttribute( 'aria-expanded', 'false' ); }
				var sub = item.querySelector( ':scope > .sub-menu' );
				var last = sub ? sub.querySelector( ':scope > li:last-child' ) : null;
				var done = function () {
					item.classList.remove( 'is-closing' );
					if ( last ) { last.removeEventListener( 'animationend', done ); }
				};
				if ( last ) {
					last.addEventListener( 'animationend', done );
				} else {
					done();
				}
			}

			btn.addEventListener( 'click', function () {
				var willOpen = ! parent.classList.contains( 'is-open' );

				// accordéon : referme les autres items ouverts de même niveau
				parent.parentElement
					.querySelectorAll( ':scope > .menu-item-has-children.is-open' )
					.forEach( function ( s ) { if ( s !== parent ) { closeItem( s ); } } );

				if ( willOpen ) {
					parent.classList.remove( 'is-closing' );
					parent.classList.add( 'is-open' );
					btn.setAttribute( 'aria-expanded', 'true' );
				} else {
					closeItem( parent );
				}
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initSubmenus );
	} else {
		initSubmenus();
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
			// on enveloppe le contenu (tout sauf le summary) dans .bh-faq__content > .bh-faq__inner.
			// L'animation se fait en pur CSS (grid-template-rows 0fr<->1fr) : aucune mesure de
			// hauteur en JS, donc pas de saccade.
			var content = document.createElement( 'div' );
			content.className = 'bh-faq__content';
			var inner = document.createElement( 'div' );
			inner.className = 'bh-faq__inner';
			var node = summary.nextSibling;
			while ( node ) {
				var next = node.nextSibling;
				inner.appendChild( node );
				node = next;
			}
			content.appendChild( inner );
			item.appendChild( content );

			// le <details> doit rester "open" en permanence pour que le contenu soit dans
			// le flux (sinon le navigateur le masque et l'animation grid ne joue pas).
			// L'ouverture/fermeture visuelle est gérée uniquement par la classe .is-open.
			item.setAttribute( 'open', '' );

			summary.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				item.classList.toggle( 'is-open' );
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initFaq );
	} else {
		initFaq();
	}
} )();
