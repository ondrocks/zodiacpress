/**
 * admin-atlas-install.js is loaded only during the atlas installation in the admin,
 * only if atlas has not been installed and a custom db (via filter) is not being used.
 */

/****************************************************
* @todo make admin-atlas-install.min.js
****************************************************/

// console.log('13.................');// @test

/* Show spinner if the atlas is currently installing */

if ( '1' === zp_atlas_install_strings.installingNow ) {
	zpatlasSpinner();
}

/* Run Installer upon button click */

var zpAtlasInstall = document.getElementById( 'zp-atlas-install' );
if ( zpAtlasInstall !== null ) {
	zpAtlasInstall.addEventListener( 'click', function (e) {
	    e.preventDefault();
		jQuery.post(ajaxurl, 'action=zp_atlas_install&_ajax_nonce=' + zp_atlas_install_strings.nonce, function(response) {
				
				// remove installer
				var el = document.getElementById( 'zp-atlas-installer' );
				if ( el !== null ) { el.parentNode.removeChild( el ); }

				// show the 'installing in background...' admin notice
				zpatlasNotice( zp_atlas_install_strings.installingNotice );

				window.scroll(0,0);

				/* Replace 'none' status with 'installing' */
				document.querySelector( '.zp-atlas-error' ).textContent = zp_atlas_install_strings.installing;

				/* Start spinner */
				zpatlasSpinner();

		});

	});
}

/**
 * Create and show an admin notice
 */
function zpatlasNotice( msg ) {
	/* create notice div */
	var div = document.createElement( 'div' );
	div.classList.add( 'notice', 'zp-atlas-message' );
	/* create paragraph element to hold message */
	var p = document.createElement( 'p' );
	/* Add message heading */
    var strong = document.createElement( 'strong' );
    strong.appendChild( document.createTextNode( zp_atlas_install_strings.statusHeading ) );
	p.appendChild( strong );
    /* Add message text */
    var span = document.createElement( 'span' );
    span.id = 'zpatlas-status';
    span.appendChild( document.createTextNode( ' \u2014 ' + msg ) );
    p.appendChild( span );
    /* Add link to check status if not on status page */
	if ( window.location.href.indexOf( 'page=zodiacpress-settings&tab=misc' ) === -1 ) {
	 	var a = document.createElement( 'a' );
		a.setAttribute( 'href', zp_atlas_install_strings.adminurl + 'admin.php?page=zodiacpress-settings&tab=misc' );
	    a.appendChild( document.createTextNode( zp_atlas_install_strings.checkStatus ) );
	    /* spaces */
		p.appendChild( document.createTextNode( '\xa0 \xa0' ) );
		p.appendChild( a );
	}
	/* Add the whole message to notice div */
    div.appendChild( p );

	/* Add dismiss button */
	var b = document.createElement( 'button' );
	b.setAttribute( 'type', 'button' );
	b.setAttribute( 'class', 'notice-dismiss' );

	/* Add screen reader text to button */
	var bSpan = document.createElement( 'span' );
	bSpan.setAttribute( 'class', 'screen-reader-text' );
	bSpan.appendChild( document.createTextNode( zp_atlas_install_strings.dismiss ) );
	b.appendChild( bSpan );
	div.appendChild( b );
	/* Add notice after the first h1 */
	var h1 = document.getElementsByTagName( 'h1' )[0];
	h1.parentNode.insertBefore( div, h1.nextSibling);

	/* Make the notice dismissible @test that this actually works with heartbeat */

	b.addEventListener( 'click', function () {
		div.parentNode.removeChild( div );
	});

}

/**
 * Start or stop spinner for Status field
 */
function zpatlasSpinner( action = 'start' ) {

	if ( 'stop' === action) {

		/* stop spinner */

		var spinner = document.getElementById( 'zpatlas-spinner' );
		if ( spinner !== null ) {
			spinner.parentNode.removeChild( spinner );
		}


	} else {

		/* start spinner */

		/* status field */
		var el = document.querySelector( '.zp-atlas-error' );
		if ( el !== null ) {

			/* Create span element for spinner */
		    var s = document.createElement( 'span' );
		    s.id = 'zpatlas-spinner';

		    /* make it spin */
		    s.setAttribute( 'class', 'zpatlas-spinner' );

		    /* Prepend it to status field */
			el.insertBefore( s, el.firstChild );

		}

	}
}

/* Dismisses the activation Install notice ("Skip setup") */

var zpSetupDismiss = document.getElementById( 'zp-skip-setup' );
if ( zpSetupDismiss !== null ) {
	zpSetupDismiss.addEventListener( 'click', function () {
		var el = document.querySelector( '.zp-atlas-message' );
		el.parentNode.removeChild( el );
	});
}
