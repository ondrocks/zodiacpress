/* Disable Submit button until form is filled */
var zpSubmit = document.getElementById( 'zp-fetch-birthreport' );
zpSubmit.setAttribute( 'disabled', true );

/**
 * Ajax request to get time offset
 */
function zpGetOffset() {
	const zpFormData = jQuery( '#zp-ajax-birth-data :input' ).serialize() + '&action=zp_tz_offset';
	const xhr = new XMLHttpRequest();
	xhr.open( 'POST', zp_ajax_object.ajaxurl );
	xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
	xhr.responseType = 'json';
		
	xhr.onload = function() {

		if (xhr.status === 200 && xhr.response) {
			if ( xhr.response.error ) {

				/* remove previous errors if any */
				zpRemoveError();
					
				/* show new error */
				var span = document.createElement( 'span' );
				span.setAttribute( 'class', 'ui-state-error' );
				span.textContent = xhr.response.error;
				document.getElementById( 'zp-ajax-birth-data' ).appendChild( span );

			} else {

				/* if not null, blank, nor false, but 0 is okay  */
				if ( null !== xhr.response.offset_geo && '' !== xhr.response.offset_geo && 'false' != xhr.response.offset_geo) {

					/* remove previous errors if any */
					zpRemoveError();
								
					/* Display offset. */
					document.getElementById( 'zp-offset-wrap' ).style.display = 'block';
					document.getElementById( 'zp-offset-label' ).textContent = zp_ajax_object.utc + ' ';
					document.getElementById( 'zp_offset_geo' ).value = xhr.response.offset_geo;
					document.getElementById( 'zp-form-tip' ).style.display = 'block';

					/* Enable submit button */
					zpSubmit.removeAttribute( 'disabled' );

				}

			}

		}
	};

	xhr.send( zpFormData );
}

(function( $ ) {
	
		/* Fetch birth report upon clicking submit */

		$( '#zp-fetch-birthreport' ).click(function() {
			$.ajax({
				url: zp_ajax_object.ajaxurl,
				type: "POST",
				data: $( '#zp-birthreport-form' ).serialize(),
				dataType: "json",
				success: function( reportData ) {

					if ( reportData.error ) {
						/* remove previous errors if any */
						zpRemoveError();
						var span = $( '<span />' );
						span.attr( 'class', 'ui-state-error' );
						span.text( reportData.error );
						$( '#zp-offset-wrap' ).after( span );

					} else {

						/* if neither null, blank, nor false */
						var zpReport = reportData.report.trim();
						if ( zpReport && 'false' != zpReport ) {
							
							/* remove previous errors if any */
							zpRemoveError();

							/* Display report. */
							$( '#zp-report-wrap' ).show();
							$( '#zp-report-content' ).append(reportData.report);
							$( '#zp-form-wrap' ).hide();

							/* Insert the chart image. */
							switch ( zp_ajax_object.draw ) {
								case 'top':

									/* Show image at top */

									if ( $( '.zp-report-header' ).length ) {
										$( '.zp-report-header' ).after( reportData.image );
									} else {
										$( '#zp-report-content' ).prepend( reportData.image );
									}

								break;
								case 'bottom':

									/* show image at end of report */

									$( '#zp-report-content' ).append( reportData.image );

								break;

							}

							/* Scroll to top of report */
							var distance = $('#zp-report-wrap').offset().top - 70;
							$( 'html,body' ).animate({
								scrollTop: distance
							}, 'slow');
						}
					
					}					

				}
			});
			return false;
		});

		/* Reset the Offset if date or time is changed. */

		$( '#month, #day, #year, #hour, #minute' ).on( 'change', function () {
			var changed = ! this.options[this.selectedIndex].defaultSelected;
			if ( changed ) {

				/* Only do ajax (get offset) if (partial) required fields are entered. */
				
				if ( zpFieldsFilled() ) {
					zpGetOffset();
				}
			}
		} );

		// Get offset after unknown_time checkbox is checked
		$( '#unknown_time' ).on( 'change', function () {
			if ( $( this ).is( ":checked" ) ) {

				/* Only do ajax (get offset) if (partial) required fields are entered. */
				if ( zpFieldsFilled() ) {
					zpGetOffset();
				}
			}
		} );
			
})( jQuery );

/**
 * Check that the fields required to get offset are entered.
 */
function zpFieldsFilled() {
	var ids = ['geo_timezone_id','zp_long_decimal','zp_lat_decimal','place','minute','hour','year','day','month'];

	for ( var i of ids ) {

		var el = document.getElementById( i );

		if ( null === el ) {
			return false;
		}

        if ( el.value.length === 0 || ! el.value.trim() ) {

        	// if minute or hour are blank, pass if unknown time is checked
        	if ( 'minute' === i || 'hour' === i ) {
        		if ( document.getElementById( 'unknown_time' ).checked ) {
        			continue;
        		}
        	}

        	return false;
        }
	}

	return true;
}

/**
 * Remove form error notices
 */
function zpRemoveError() {
	var el = document.querySelector( '.ui-state-error' );
	if ( el !== null ) { el.parentNode.removeChild( el ); }
}
