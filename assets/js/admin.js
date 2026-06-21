/* HeadlessWP Admin JavaScript */
/* global headlesswpAdmin, jQuery */

( function ( $ ) {
	'use strict';

	const cfg = window.headlesswpAdmin || {};

	// ── Health check ────────────────────────────────────────────────

	$( '#headlesswp-run-check' ).on( 'click', function () {
		const $btn     = $( this );
		const $results = $( '#headlesswp-health-results' );

		$btn.text( cfg.i18n.checking ).prop( 'disabled', true );

		$.post( cfg.ajaxUrl, {
			action : 'headlesswp_health_check',
			nonce  : cfg.healthNonce,
		} )
		.done( function ( response ) {
			if ( response.success ) {
				$results.html( renderHealthResults( response.data ) );
			} else {
				$results.html( '<p class="headlesswp-status--fail">' + cfg.i18n.error + '</p>' );
			}
		} )
		.fail( function () {
			$results.html( '<p class="headlesswp-status--fail">' + cfg.i18n.error + '</p>' );
		} )
		.always( function () {
			$btn.text( cfg.i18n.runCheck ).prop( 'disabled', false );
		} );
	} );

	// ── Clear cache ─────────────────────────────────────────────────

	$( '#headlesswp-clear-cache' ).on( 'click', function () {
		const $btn = $( this );
		$btn.prop( 'disabled', true );

		$.post( cfg.ajaxUrl, {
			action : 'headlesswp_clear_health_cache',
			nonce  : cfg.healthNonce,
		} )
		.done( function ( response ) {
			if ( response.success ) {
				alert( cfg.i18n.cacheCleared );
			}
		} )
		.always( function () {
			$btn.prop( 'disabled', false );
		} );
	} );

	// ── Render health results HTML ───────────────────────────────────

	function renderHealthResults( data ) {
		const labelMap = {
			wp_api   : 'WordPress REST API',
			graphql  : 'GraphQL Endpoint',
			frontend : 'Frontend Reachability',
			cors     : 'CORS Configuration',
			plugin   : 'Plugin Status',
		};

		let html = '<div class="headlesswp-health-grid">';

		Object.entries( labelMap ).forEach( function ( [ key, label ] ) {
			if ( ! data[ key ] ) return;

			const check  = data[ key ];
			const ok     = check.ok;
			const detail = check.detail || '';

			let statusClass, statusIcon, statusText;
			if ( ok === true ) {
				statusClass = 'headlesswp-status--pass';
				statusIcon  = '✓';
				statusText  = cfg.i18n.pass;
			} else if ( ok === false ) {
				statusClass = 'headlesswp-status--fail';
				statusIcon  = '✗';
				statusText  = cfg.i18n.fail;
			} else {
				statusClass = 'headlesswp-status--info';
				statusIcon  = '●';
				statusText  = cfg.i18n.info;
			}

			html += `
				<div class="headlesswp-health-item">
					<span class="headlesswp-health-label">${ escHtml( label ) }</span>
					<span class="headlesswp-health-status ${ escHtml( statusClass ) }">
						<span class="headlesswp-status-icon" aria-hidden="true">${ escHtml( statusIcon ) }</span>
						${ escHtml( statusText ) }
					</span>
					<span class="headlesswp-health-detail">${ escHtml( detail ) }</span>
				</div>
			`;
		} );

		html += '</div>';

		if ( data.checked_at ) {
			html += `<p class="headlesswp-health-timestamp">Last checked: ${ escHtml( data.checked_at ) }</p>`;
		}

		return html;
	}

	// ── Utility ─────────────────────────────────────────────────────

	function escHtml( str ) {
		return String( str )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /"/g, '&quot;' )
			.replace( /'/g, '&#039;' );
	}

} )( jQuery );
