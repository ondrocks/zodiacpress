var zpAtlasDB = document.getElementById( 'zodiacpress_settings_atlas_db' );
var zpAtlasGN = document.getElementById( 'zodiacpress_settings_atlas_geonames' );
var zpCreateReportButton = document.getElementById( 'zp-create-new-report' );
var zpDeleteLink = document.getElementsByClassName( 'zp-custom-reports-delete' );
var zpDeleteLinks = zpDeleteLink.length;

console.log('@test 1----------');// @test


/* Displays the "Create new report" form when "Create New Report" button is clicked */

if ( zpCreateReportButton !== null ) {
	zpCreateReportButton.addEventListener( 'click', function (e) {
	    e.preventDefault();

	    /* show the form */
	    zpCreateReportForm();
	    
		// Hide 'Create New Custom Report' button
		this.style.display = 'none';

	});
}

/* Displays the "Delete report" form when Delete link is clicked
	on the Manage Custom Reports page */

for ( var i = 0; i < zpDeleteLinks; i++) {
    zpDeleteLink[ i ].addEventListener( 'click', function( e ) {

    	e.preventDefault();

    	var d = this;

    	/* Build delete confirmation note */

		/* form wrapper */
		var div = document.createElement( 'div' );
		div.classList.add( 'stuffbox', 'zp-delete-custom-wrap' );

		/* form element */
		var f = document.createElement( 'form' );
		f.setAttribute( 'method', 'post' );	
		f.setAttribute( 'action', zp_admin_strings.adminPost );

		var p = document.createElement( 'p' );
		p.appendChild( document.createTextNode( zp_admin_strings.confirm ) );

		var report = document.createElement( 'input' );
		report.setAttribute( 'type', 'hidden' );
		report.setAttribute( 'name', 'zp-report-id' );
		report.value = this.getAttribute( 'data-report' );

		var hidden = document.createElement( 'input' );
		hidden.setAttribute( 'type', 'hidden' );
		hidden.setAttribute( 'name', 'action' );
		hidden.value = 'zp_delete_report';
		
		var nonce = document.createElement( 'input' );
		nonce.setAttribute( 'type', 'hidden' );
		nonce.setAttribute( 'name', 'zp_admin_nonce' );
		nonce.value = zp_admin_strings.nonceDelete;

		var submit = document.createElement( 'input' );
		submit.setAttribute( 'type', 'submit' );
		submit.setAttribute( 'class', 'button-primary' );
		submit.setAttribute( 'value', zp_admin_strings.confirmSubmit );
		
		/* Cancel link */
		var cancel = document.createElement( 'a' );
		cancel.classList.add( 'zp-cancel', 'zp-error' );
		cancel.href = '#';
		cancel.appendChild( document.createTextNode( zp_admin_strings.cancel ) );	

		div.appendChild( f );
		f.appendChild( p );
		f.appendChild( report );
		f.appendChild( hidden );
		f.appendChild( nonce );
		f.appendChild( submit );
		f.appendChild( cancel );

		/* Insert form before the Delete link */
		this.parentNode.insertBefore( div, this );

		/* Closes the Delete form when Cancel link is clicked. */
		cancel.addEventListener( 'click', function (e) {
			e.preventDefault();
	    	/* Hide the form */
	    	div.style.display = 'none';
			// Restore the Delete link
			d.style.display = 'inline-block';
		}, true );

		/* Hide the Delete link */
		this.style.display = 'none';

	} );
}

/* Toggles the Atlas settings */

if ( null !== zpAtlasDB && null !== zpAtlasGN ) {
  
	if ( zpAtlasDB.checked ) {
		zpAtlasSettingsDB();
	} else if( zpAtlasGN.checked ) {
		zpAtlasSettingsGN();
	}
  
	zpAtlasDB.addEventListener( 'click', zpAtlasSettingsDB );

	zpAtlasGN.addEventListener( 'click', zpAtlasSettingsGN );
}

/**
 * Sets the Atlas setting display state to Database
 */
function zpAtlasSettingsDB() {
	/* hide geonames settings */
	document.querySelector( '.zp-setting-geonames_user' ).style.display = 'none';

	/* show db settings */
	document.querySelector( '.zp-setting-atlas-status' ).style.display = 'table-row';
}

/**
 * Sets the Atlas setting display state to GeoNames
 */
function zpAtlasSettingsGN() {
	/* show Geonames settings */
	document.querySelector( '.zp-setting-geonames_user' ).style.display = 'table-row';

	/* hide db settings */
	document.querySelector( '.zp-setting-atlas-status' ).style.display = 'none';
}

/**
 * Builds the 'Create new report' form
 */
function zpCreateReportForm() {
	/* form wrapper */
	var div = document.createElement( 'div' );
	div.id = 'zp-create-custom-wrap';
	div.setAttribute( 'class', 'stuffbox' );

	/* form element */
	var f = document.createElement( 'form' );
	f.id = 'zp-create-custom-form';
	f.setAttribute( 'method', 'post' );	
	f.setAttribute( 'action', zp_admin_strings.adminPost );

	var label = document.createElement( 'label' );
	label.appendChild( document.createTextNode( zp_admin_strings.label ) );	

	/* Report name field */
	var nameInput = document.createElement( 'input' );
	nameInput.setAttribute( 'type', 'text' );
	nameInput.setAttribute( 'name', 'zp-report-name-field' );
	nameInput.setAttribute( 'class', 'medium-text' );
	nameInput.required = true;
	nameInput.setAttribute( 'minlength', '2' );

	var hidden = document.createElement( 'input' );
	hidden.setAttribute( 'type', 'hidden' );
	hidden.setAttribute( 'name', 'action' );
	hidden.value = 'zp_create_new_report';
	
	var nonce = document.createElement( 'input' );
	nonce.setAttribute( 'type', 'hidden' );
	nonce.setAttribute( 'name', 'zp_admin_nonce' );
	nonce.value = zp_admin_strings.nonceCreate;

	var submit = document.createElement( 'input' );
	submit.setAttribute( 'type', 'submit' );
	submit.setAttribute( 'class', 'button-primary' );
	submit.setAttribute('value', zp_admin_strings.create );
	
	/* Cancel link */
	var cancel = document.createElement( 'a' );
	cancel.classList.add( 'zp-cancel', 'zp-error' );
	cancel.href = '#';
	cancel.appendChild( document.createTextNode( zp_admin_strings.cancel ) );

	div.appendChild( f );
	f.appendChild( label );
	f.appendChild( nameInput );
	f.appendChild( hidden );
	f.appendChild( nonce );
	f.appendChild( submit );
	f.appendChild( cancel );

	/* Insert form before the 'Create New Custom Report' button */
	zpCreateReportButton.parentNode.insertBefore( div, zpCreateReportButton );

	/* Closes the form when Cancel link is clicked. */
	cancel.addEventListener( 'click', function (e) {
		e.preventDefault();
	    /* Hide the form */
	    div.style.display = 'none';
		// Restore the 'Create New Custom Report' button
		zpCreateReportButton.style.display = 'inline-block';
	});

	/* Shows spinner and grays out form on submit */

	f.onsubmit = function() {
		/* Create span element for spinner */
	    var span = document.createElement( 'span' );
	    span.id = 'zp-spinner';
	    span.style.width = submit.clientWidth + 'px';

	    /* Hide submit button */
	    submit.style.display = 'none';

	    /* Make spinner spin */
	    span.setAttribute( 'class', 'zp-spinner' );

	    /* Insert spinner before Cancel link */
		cancel.parentNode.insertBefore( span, cancel );

		/* grays out the form */
		nameInput.style.backgroundColor = "#e9e9e9";
		nameInput.style.opacity = 0.5;

		cancel.style.display = 'none';
	};

}
