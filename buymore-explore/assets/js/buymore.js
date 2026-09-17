/**
 * BuyMore Explore Dashboard — front-end behaviour.
 *
 * No framework and no build step: one IIFE, delegated listeners, and a single
 * fetch call for the audience filter. Everything degrades to plain links and
 * page loads when JavaScript is unavailable.
 */
( function () {
	'use strict';

	var config = window.buymoreConfig || {};

	/**
	 * Initialise one dashboard instance.
	 *
	 * @param {HTMLElement} app Root .bm-app element.
	 */
	function initApp( app ) {
		var sidebar = app.querySelector( '[data-bm-sidebar]' );
		var scrim = app.querySelector( '[data-bm-scrim]' );
		var toggle = app.querySelector( '[data-bm-nav-toggle]' );
		var products = app.querySelector( '[data-bm-products]' );
		var searchWrap = app.querySelector( '[data-bm-search]' );
		var searchInput = app.querySelector( '[data-bm-search-input]' );

		/* --- Sidebar drawer (tablet and mobile) --------------------------- */

		function setNav( open ) {
			app.classList.toggle( 'is-nav-open', open );

			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			}

			if ( scrim ) {
				scrim.hidden = ! open;
			}

			if ( open && sidebar ) {
				var first = sidebar.querySelector( 'a, button' );

				if ( first ) {
					first.focus();
				}
			} else if ( ! open && toggle && document.activeElement && sidebar && sidebar.contains( document.activeElement ) ) {
				toggle.focus();
			}
		}

		app.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '[data-bm-nav-toggle]' ) ) {
				setNav( ! app.classList.contains( 'is-nav-open' ) );
				return;
			}

			if ( event.target.closest( '[data-bm-nav-close]' ) || event.target.closest( '[data-bm-scrim]' ) ) {
				setNav( false );
			}
		} );

		app.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && app.classList.contains( 'is-nav-open' ) ) {
				setNav( false );
			}
		} );

		/* --- View switch and save buttons --------------------------------- */

		app.addEventListener( 'click', function ( event ) {
			var view = event.target.closest( '[data-bm-view]' );

			if ( view ) {
				Array.prototype.forEach.call( app.querySelectorAll( '[data-bm-view]' ), function ( button ) {
					var active = button === view;
					button.classList.toggle( 'is-active', active );
					button.setAttribute( 'aria-pressed', active ? 'true' : 'false' );
				} );
				return;
			}

			var save = event.target.closest( '[data-bm-save]' );

			if ( save ) {
				var pressed = 'true' === save.getAttribute( 'aria-pressed' );
				save.setAttribute( 'aria-pressed', pressed ? 'false' : 'true' );
				return;
			}

			var filters = event.target.closest( '[data-bm-filters]' );

			if ( filters ) {
				var on = 'true' === filters.getAttribute( 'aria-pressed' );
				filters.setAttribute( 'aria-pressed', on ? 'false' : 'true' );

				// Extension point: listen for this event to open a filter panel.
				app.dispatchEvent( new CustomEvent( 'buymore:filters', { detail: { active: ! on } } ) );
			}
		} );

		/* --- Favourites strip arrows -------------------------------------- */

		app.addEventListener( 'click', function ( event ) {
			var arrow = event.target.closest( '[data-bm-scroll]' );

			if ( ! arrow ) {
				return;
			}

			var strip = arrow.closest( '[data-bm-favourites]' );
			var track = strip ? strip.querySelector( '[data-bm-track]' ) : null;

			if ( track ) {
				track.scrollBy( {
					left: parseInt( arrow.getAttribute( 'data-bm-scroll' ), 10 ) * 132,
					behavior: 'smooth'
				} );
			}
		} );

		/* --- Search: expand, then filter the rendered tiles --------------- */

		if ( searchWrap && searchInput ) {
			searchWrap.addEventListener( 'click', function ( event ) {
				if ( ! event.target.closest( '[data-bm-search-toggle]' ) ) {
					return;
				}

				var open = searchWrap.classList.toggle( 'is-open' );
				event.target.closest( '[data-bm-search-toggle]' ).setAttribute( 'aria-expanded', open ? 'true' : 'false' );

				if ( open ) {
					searchInput.focus();
				} else {
					searchInput.value = '';
					applySearch( '' );
				}
			} );

			searchInput.addEventListener( 'input', function () {
				applySearch( searchInput.value );
			} );
		}

		/**
		 * Hide tiles whose name does not contain the term.
		 *
		 * @param {string} term Raw search term.
		 */
		function applySearch( term ) {
			var needle = term.trim().toLowerCase();

			Array.prototype.forEach.call( app.querySelectorAll( '[data-bm-searchable]' ), function ( tile ) {
				var name = tile.getAttribute( 'data-name' ) || '';
				tile.classList.toggle( 'is-hidden', '' !== needle && -1 === name.indexOf( needle ) );
			} );
		}

		/* --- Audience tabs ------------------------------------------------ */

		if ( ! products || ! config.ajaxUrl ) {
			return;
		}

		app.addEventListener( 'click', function ( event ) {
			var tab = event.target.closest( '[data-bm-tab]' );

			if ( ! tab || event.metaKey || event.ctrlKey ) {
				return;
			}

			event.preventDefault();
			selectTab( tab );
			loadAudience( tab.getAttribute( 'data-bm-tab' ) || '' );

			if ( window.history && window.history.replaceState ) {
				window.history.replaceState( {}, '', tab.href );
			}
		} );

		/**
		 * Move the active state to one tab.
		 *
		 * @param {HTMLElement} tab Clicked tab.
		 */
		function selectTab( tab ) {
			Array.prototype.forEach.call( app.querySelectorAll( '[data-bm-tab]' ), function ( item ) {
				var active = item === tab;
				item.classList.toggle( 'is-active', active );

				if ( active ) {
					item.setAttribute( 'aria-current', 'true' );
				} else {
					item.removeAttribute( 'aria-current' );
				}
			} );
		}

		/**
		 * Swap the product tiles for one audience.
		 *
		 * @param {string} audience Term slug, or '' for everything.
		 */
		function loadAudience( audience ) {
			var body = new URLSearchParams();

			body.append( 'action', config.action );
			body.append( 'nonce', config.nonce );
			body.append( 'audience', audience );

			products.classList.add( 'is-loading' );

			window
				.fetch( config.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
					body: body.toString()
				} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( payload ) {
					if ( ! payload || ! payload.success ) {
						throw new Error( 'Request failed' );
					}

					products.innerHTML = payload.data.html;

					if ( searchInput && searchInput.value ) {
						applySearch( searchInput.value );
					}
				} )
				.catch( function () {
					products.innerHTML =
						'<p class="bm-empty bm-empty--grid">' + ( ( config.strings && config.strings.error ) || '' ) + '</p>';
				} )
				.finally( function () {
					products.classList.remove( 'is-loading' );
				} );
		}
	}

	function boot() {
		Array.prototype.forEach.call( document.querySelectorAll( '[data-bm-app]' ), initApp );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
