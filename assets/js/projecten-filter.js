/* global jQuery, projectenFilter */
/**
 * Projecten Category Filter
 *
 * Handles click events on the category filter bar, fires an AJAX request to
 * load the matching posts, replaces the grid content, and renders JS-driven
 * numeric pagination for the filtered view.
 *
 * When no filter is active ("Alle" on initial load) the server-rendered
 * pagination remains untouched — only after clicking a filter button does
 * this script take ownership of the pagination area.
 */
( function ( $ ) {
	'use strict';

	var $grid       = null;
	var $pagination = null;
	var activeTermId = 0;
	var isLoading    = false;

	// ── Bootstrap ─────────────────────────────────────────────────────────────

	function init() {
		$grid       = $( '#projecten-grid' );
		$pagination = $( '#projecten-pagination' );

		if ( ! $grid.length ) {
			return;
		}

		// Filter button click.
		$( document ).on( 'click', '.projecten-filter-btn', function () {
			var $btn   = $( this );
			var termId = parseInt( $btn.data( 'term-id' ), 10 ) || 0;

			if ( $btn.hasClass( 'is-active' ) ) {
				return;
			}

			$( '.projecten-filter-btn' ).removeClass( 'is-active' );
			$btn.addClass( 'is-active' );

			activeTermId = termId;
			fetchPosts( termId, 1 );
		} );

		// Delegated click on JS-rendered pagination buttons.
		$( document ).on( 'click', '#projecten-pagination button.page-numbers[data-page]', function ( e ) {
			e.preventDefault();
			var page = parseInt( $( this ).data( 'page' ), 10 ) || 1;
			fetchPosts( activeTermId, page );

			// Scroll back to the top of the grid.
			$( 'html, body' ).animate( { scrollTop: $grid.offset().top - 100 }, 300 );
		} );
	}

	// ── AJAX ──────────────────────────────────────────────────────────────────

	function fetchPosts( termId, page ) {
		if ( isLoading ) {
			return;
		}

		isLoading = true;
		$grid.addClass( 'is-loading' );

		$.post( projectenFilter.ajaxUrl, {
			action:  'projecten_filter',
			nonce:   projectenFilter.nonce,
			term_id: termId,
			paged:   page,
		} )
		.done( function ( response ) {
			if ( ! response.success ) {
				return;
			}
			$grid.html( response.data.items );
			renderPagination( termId, page, response.data.max_pages );
		} )
		.always( function () {
			isLoading = false;
			$grid.removeClass( 'is-loading' );
		} );
	}

	// ── Pagination renderer ───────────────────────────────────────────────────

	function renderPagination( termId, current, total ) {
		if ( total <= 1 ) {
			$pagination.hide().empty();
			return;
		}

		var items = [];

		// Previous button.
		if ( current > 1 ) {
			items.push( makeBtn( termId, current - 1, 'prev',
				'<span class="projecten-pagination__arrow" aria-hidden="true">&larr;</span>' +
				'<span class="screen-reader-text">' + projectenFilter.i18n.previous + '</span>'
			) );
		}

		// Numbered buttons (show up to 7; collapse with ellipsis for large sets).
		var range = buildRange( current, total );
		range.forEach( function ( n ) {
			if ( n === '…' ) {
				var dots = document.createElement( 'span' );
				dots.className   = 'page-numbers dots';
				dots.textContent = '…';
				items.push( dots );
			} else if ( n === current ) {
				var active = document.createElement( 'span' );
				active.className   = 'page-numbers current';
				active.setAttribute( 'aria-current', 'page' );
				active.textContent = n;
				items.push( active );
			} else {
				items.push( makeBtn( termId, n, '', n ) );
			}
		} );

		// Next button.
		if ( current < total ) {
			items.push( makeBtn( termId, current + 1, 'next',
				'<span class="screen-reader-text">' + projectenFilter.i18n.next + '</span>' +
				'<span class="projecten-pagination__arrow" aria-hidden="true">&rarr;</span>'
			) );
		}

		var nav = document.createElement( 'nav' );
		nav.setAttribute( 'aria-label', 'Projecten navigatie' );
		items.forEach( function ( el ) { nav.appendChild( el ); } );

		$pagination.show().html( nav.outerHTML );
	}

	/**
	 * Build a compact page-number range with ellipsis.
	 * Always shows first, last, current ± 1, and fills with '…' in between.
	 */
	function buildRange( current, total ) {
		if ( total <= 7 ) {
			return Array.from( { length: total }, function ( _, i ) { return i + 1; } );
		}

		var delta = 2;
		var left  = current - delta;
		var right = current + delta;
		var range = [];
		var result = [];
		var prev;

		for ( var i = 1; i <= total; i++ ) {
			if ( i === 1 || i === total || ( i >= left && i <= right ) ) {
				range.push( i );
			}
		}

		range.forEach( function ( page ) {
			if ( prev ) {
				if ( page - prev === 2 ) {
					result.push( prev + 1 );
				} else if ( page - prev !== 1 ) {
					result.push( '…' );
				}
			}
			result.push( page );
			prev = page;
		} );

		return result;
	}

	// ── Helpers ───────────────────────────────────────────────────────────────

	function makeBtn( termId, page, extraClass, labelHtml ) {
		var btn = document.createElement( 'button' );
		btn.className = 'page-numbers' + ( extraClass ? ' ' + extraClass : '' );
		btn.setAttribute( 'data-page', page );
		btn.setAttribute( 'data-term', termId );
		btn.setAttribute( 'type', 'button' );
		btn.innerHTML = String( labelHtml );
		return btn;
	}

	// ── Init ──────────────────────────────────────────────────────────────────

	$( document ).ready( init );

}( jQuery ) );
