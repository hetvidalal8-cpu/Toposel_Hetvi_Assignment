/**
 * Keeps the hex text field and the colour picker in step on edit screens.
 */
( function () {
	'use strict';

	document.addEventListener( 'input', function ( event ) {
		var field = event.target;

		if ( field.classList.contains( 'buymore-hex' ) ) {
			var picker = document.getElementById( field.getAttribute( 'data-for' ) );

			if ( picker && /^#[0-9a-f]{6}$/i.test( field.value.trim() ) ) {
				picker.value = field.value.trim();
			}

			return;
		}

		if ( 'color' === field.type ) {
			var text = document.querySelector( '.buymore-hex[data-for="' + field.id + '"]' );

			if ( text ) {
				text.value = field.value;
			}
		}
	} );
}() );
