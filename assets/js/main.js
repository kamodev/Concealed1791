/**
 * Concealed 1791 theme scripts: mobile menu, search panel, dismissible
 * announcement bar, header shadow on scroll, pricing switch, one-open FAQ
 * accordion and back-to-top button.
 */
( function () {
	'use strict';

	var body = document.body;

	/* Mobile menu */
	var nav = document.getElementById( 'ct-nav' );
	var menuToggle = document.querySelector( '.ct-menu-toggle' );
	var navClose = document.querySelector( '.ct-nav__close' );
	var backdrop = document.querySelector( '.ct-nav-backdrop' );

	function setNav( open ) {
		if ( ! nav || ! menuToggle ) {
			return;
		}
		nav.classList.toggle( 'is-open', open );
		body.classList.toggle( 'ct-nav-open', open );
		menuToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		if ( backdrop ) {
			backdrop.hidden = ! open;
		}
		if ( open && navClose ) {
			navClose.focus();
		}
	}

	if ( menuToggle && nav ) {
		menuToggle.addEventListener( 'click', function () {
			setNav( ! nav.classList.contains( 'is-open' ) );
		} );
		nav.addEventListener( 'click', function ( e ) {
			var link = e.target.closest( 'a' );
			if ( link && '#' !== link.getAttribute( 'href' ) ) {
				setNav( false );
			}
		} );
		if ( navClose ) {
			navClose.addEventListener( 'click', function () {
				setNav( false );
				menuToggle.focus();
			} );
		}
		if ( backdrop ) {
			backdrop.addEventListener( 'click', function () {
				setNav( false );
			} );
		}
	}

	/* Search panel */
	var searchToggle = document.querySelector( '.ct-search-toggle' );
	var searchPanel = document.getElementById( 'ct-search-panel' );

	if ( searchToggle && searchPanel ) {
		searchToggle.addEventListener( 'click', function () {
			var open = searchPanel.hasAttribute( 'hidden' );
			searchPanel.toggleAttribute( 'hidden', ! open );
			searchToggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( open ) {
				var input = searchPanel.querySelector( 'input[type="search"]' );
				if ( input ) {
					input.focus();
				}
			}
		} );
	}

	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' !== e.key ) {
			return;
		}
		if ( nav && nav.classList.contains( 'is-open' ) ) {
			setNav( false );
			menuToggle.focus();
		}
		if ( searchPanel && ! searchPanel.hasAttribute( 'hidden' ) ) {
			searchPanel.setAttribute( 'hidden', '' );
			searchToggle.setAttribute( 'aria-expanded', 'false' );
			searchToggle.focus();
		}
	} );

	/* Dismissible announcement bar (remembered per message). */
	var bar = document.getElementById( 'ct-announcement' );
	if ( bar ) {
		var storageKey = 'ct-announce-' + bar.getAttribute( 'data-key' );
		try {
			if ( window.localStorage.getItem( storageKey ) ) {
				bar.classList.add( 'is-hidden' );
			}
		} catch ( err ) {}

		var close = bar.querySelector( '.ct-announcement__close' );
		if ( close ) {
			close.addEventListener( 'click', function () {
				bar.classList.add( 'is-hidden' );
				try {
					window.localStorage.setItem( storageKey, '1' );
				} catch ( err ) {}
			} );
		}
	}

	/* Header shadow once the page scrolls; back-to-top button. */
	var header = document.getElementById( 'masthead' );
	var toTop = document.querySelector( '.ct-to-top' );
	var ticking = false;

	function onScroll() {
		ticking = false;
		var y = window.scrollY;
		if ( header ) {
			header.classList.toggle( 'is-scrolled', y > 8 );
		}
		if ( toTop ) {
			toTop.classList.toggle( 'is-visible', y > 600 );
		}
	}
	window.addEventListener(
		'scroll',
		function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( onScroll );
			}
		},
		{ passive: true }
	);
	onScroll();

	if ( toTop ) {
		toTop.addEventListener( 'click', function () {
			window.scrollTo( { top: 0 } );
		} );
	}

	/* Pricing switch: shows each package's first or second price and link. */
	document.querySelectorAll( '[data-ct-pricing]' ).forEach( function ( section ) {
		var options = section.querySelectorAll( '.ct-switch__opt' );
		options.forEach( function ( option ) {
			option.addEventListener( 'click', function () {
				var plan = option.getAttribute( 'data-plan' );
				options.forEach( function ( other ) {
					var active = other === option;
					other.classList.toggle( 'is-active', active );
					other.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
				} );
				section.querySelectorAll( '.ct-plan' ).forEach( function ( card ) {
					var prices = card.querySelectorAll( '[data-plan-show]' );
					var hasPlan = card.querySelector( '[data-plan-show="' + plan + '"]' );
					prices.forEach( function ( price ) {
						// Packages without a second price keep showing their first price.
						price.hidden = hasPlan ? price.getAttribute( 'data-plan-show' ) !== plan : '1' !== price.getAttribute( 'data-plan-show' );
					} );
					var button = card.querySelector( '[data-url-' + plan + ']' );
					if ( button ) {
						button.setAttribute( 'href', button.getAttribute( 'data-url-' + plan ) );
					}
				} );
			} );
		} );
	} );

	/* FAQ accordion: opening one question closes the others in that list. */
	document.querySelectorAll( '[data-ct-accordion]' ).forEach( function ( list ) {
		var items = list.querySelectorAll( 'details' );
		items.forEach( function ( item ) {
			item.addEventListener( 'toggle', function () {
				if ( ! item.open ) {
					return;
				}
				items.forEach( function ( other ) {
					if ( other !== item && other.open ) {
						other.open = false;
					}
				} );
			} );
		} );
	} );
} )();
