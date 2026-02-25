/**
 * Kennisitems Single Post — Table of Contents + Scrollspy
 *
 * 1. Scans `.ki-entry-content` for h2 and h3 headings.
 * 2. Injects a unique `id` on each heading if one is not already set.
 * 3. Builds a nested <ul> inside `#ki-toc-nav` with smooth-scroll links.
 * 4. Scrollspy: highlights the link whose heading is currently in the viewport
 *    (uses IntersectionObserver where available, rAF-throttled scroll otherwise).
 * 5. Hides `.single-kennisitems-toc` entirely when no headings are found.
 *
 * No jQuery dependency — vanilla JS only.
 *
 * @package Energieburcht
 */

( function () {
	'use strict';

	// ── Helpers ──────────────────────────────────────────────────────────────

	/**
	 * Convert a heading's text content to a URL-safe slug.
	 *
	 * @param  {string} text  Raw heading text.
	 * @param  {number} index Fallback index for empty strings.
	 * @return {string}
	 */
	function slugify( text, index ) {
		var slug = text
			.toLowerCase()
			.replace( /[^\w\s-]/g, '' )   // strip non-word chars
			.replace( /[\s_]+/g, '-' )    // spaces / underscores → dash
			.replace( /^-+|-+$/g, '' );   // trim leading/trailing dashes

		return slug || ( 'ki-heading-' + index );
	}

	/**
	 * Ensure a heading element has a unique id.
	 * Returns the (possibly newly assigned) id.
	 *
	 * @param  {Element} el    Heading element.
	 * @param  {number}  index Position in the headings array.
	 * @return {string}
	 */
	function ensureId( el, index ) {
		if ( el.id ) {
			return el.id;
		}

		var base = slugify( el.textContent, index );
		var id   = base;
		var n    = 1;

		// Guarantee uniqueness within the document.
		while ( document.getElementById( id ) ) {
			id = base + '-' + ( n++ );
		}

		el.id = id;
		return id;
	}

	// ── DOM references ────────────────────────────────────────────────────────

	var content = document.querySelector( '.ki-entry-content' );
	var tocNav  = document.getElementById( 'ki-toc-nav' );
	var tocWrap = document.querySelector( '.single-kennisitems-toc' );

	if ( ! content || ! tocNav ) {
		return;
	}

	// ── Collect headings ──────────────────────────────────────────────────────

	var headings = Array.prototype.slice.call(
		content.querySelectorAll( 'h2, h3' )
	);

	if ( headings.length === 0 ) {
		// Hide the TOC aside if there is nothing to list.
		if ( tocWrap ) {
			tocWrap.style.display = 'none';

			// Also remove the no-toc / grid modifier so content expands.
			var body = document.querySelector( '.single-kennisitems-body' );
			if ( body ) {
				body.classList.add( 'no-toc' );
			}
		}
		return;
	}

	// ── Build TOC markup ──────────────────────────────────────────────────────

	/**
	 * Build a flat <ul> list; h3 items receive an extra indent class.
	 */
	var ul = document.createElement( 'ul' );
	ul.className = 'ki-toc__list';

	var links = []; // keep references for scrollspy

	headings.forEach( function ( heading, idx ) {
		var id   = ensureId( heading, idx );
		var li   = document.createElement( 'li' );
		var a    = document.createElement( 'a' );

		li.className = 'ki-toc__item' +
			( heading.tagName === 'H3' ? ' ki-toc__item--sub' : '' );

		a.href      = '#' + id;
		a.className = 'ki-toc__link';
		a.textContent = heading.textContent;

		// Smooth-scroll on click (fallback for browsers ignoring CSS scroll-behavior).
		a.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			var target = document.getElementById( id );
			if ( target ) {
				target.scrollIntoView( { behavior: 'smooth', block: 'start' } );
				// Move focus for accessibility.
				target.setAttribute( 'tabindex', '-1' );
				target.focus( { preventScroll: true } );
			}
		} );

		li.appendChild( a );
		ul.appendChild( li );
		links.push( a );
	} );

	tocNav.appendChild( ul );

	// ── Scrollspy ─────────────────────────────────────────────────────────────

	var activeLink = null;

	function setActive( link ) {
		if ( activeLink === link ) {
			return;
		}
		if ( activeLink ) {
			activeLink.classList.remove( 'is-active' );
		}
		activeLink = link;
		if ( activeLink ) {
			activeLink.classList.add( 'is-active' );
		}
	}

	// ── IntersectionObserver path (preferred) ─────────────────────────────────

	if ( 'IntersectionObserver' in window ) {

		// Root margin: trigger when a heading enters the top 20% of the viewport.
		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						var idx = headings.indexOf( entry.target );
						if ( idx !== -1 ) {
							setActive( links[ idx ] );
						}
					}
				} );
			},
			{
				rootMargin: '0px 0px -75% 0px',
				threshold: 0,
			}
		);

		headings.forEach( function ( h ) {
			observer.observe( h );
		} );

	} else {

		// ── rAF-throttled scroll fallback ──────────────────────────────────

		var ticking = false;

		function updateActive() {
			var scrollY     = window.pageYOffset || document.documentElement.scrollTop;
			var viewBottom  = scrollY + window.innerHeight * 0.3;
			var bestIdx     = -1;

			headings.forEach( function ( h, i ) {
				var top = h.getBoundingClientRect().top + scrollY;
				if ( top <= viewBottom ) {
					bestIdx = i;
				}
			} );

			setActive( bestIdx >= 0 ? links[ bestIdx ] : null );
			ticking = false;
		}

		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				window.requestAnimationFrame( updateActive );
				ticking = true;
			}
		}, { passive: true } );

		// Run once on load.
		updateActive();
	}

} )();
