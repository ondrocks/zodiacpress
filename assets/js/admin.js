var zpAtlasDB = document.getElementById( 'zodiacpress_settings_atlas_db' );
var zpAtlasGN = document.getElementById( 'zodiacpress_settings_atlas_geonames' );
var zpCreateReportButton = document.getElementById( 'zp-create-new-report' );
var zpCancelCreate = document.getElementById( 'zp-cancel-create' );

console.log('@test 5----------');// @test

/* Displays the "Create new report" form when "Create New Report" button is clicked */

if ( zpCreateReportButton !== null ) {
	zpCreateReportButton.addEventListener( 'click', function (e) {
	    e.preventDefault();

		// Hide 'Create New Custom Report' button
		this.style.display = 'none';

	    /* show the form */
	    document.getElementById( 'zp-create-custom-wrap' ).style.display = 'inline-block';
	});
}

/* Closes the "Create new report" form when Cancel link is clicked. */

if ( zpCancelCreate !== null ) {
	zpCancelCreate.addEventListener( 'click', function(e) {
		e.preventDefault();

	    /* Hide the form */
	    document.getElementById( 'zp-create-custom-wrap' ).style.display = 'none';

		// Restore the 'Create New Custom Report' button
		zpCreateReportButton.style.display = 'inline-block';

	});
}

/**
 * Toggles the Atlas settings
 */
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
