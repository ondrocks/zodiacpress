/* Disable Submit button until form is filled */
var zpButton = document.getElementById( 'zp-fetch-birthreport' );
zpButton.setAttribute( 'disabled', true );

(function( $ ) {
	
		/* Autocomplete city */

		$( '#place' ).autocomplete({
			source: function( request, response ) {

				/* Hide the geonames error message, if any, in case they are trying again */
				zpRemoveError();
				
				$.ajax({
					url: zp_ajax_object.autocomplete_ajaxurl,
					dataType: zp_ajax_object.dataType,
					type: zp_ajax_object.type,
					data: {
						featureClass: "P",
						style: "full",
						maxRows: 12,
						username: zp_ajax_object.geonames_user,
						action: zp_ajax_object.autocomplete_action ? zp_ajax_object.autocomplete_action : undefined,
						name_startsWith: request.term,
						lang: zp_ajax_object.lang
					},
					success: function( data ) {

						/* check for GeoNames exceptions */
						if ( data.status !== undefined ) {
							var msg = $( '<span />' );
							msg.attr( 'class', 'ui-state-error' );
							msg.text( 'ERROR ' + data.status.value + ' - ' + data.status.message );
							$( '#zp-ajax-birth-data' ).append( msg );
						}
						
						response( $.map( data.geonames, function( item ) {
							return {
								value: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName, 
								label: item.name + (item.adminName1 ? ", " + item.adminName1 : "") + ", " + item.countryName,
								lngdeci: item.lng,
								latdeci: item.lat,
								timezoneid: item.timezone.timeZoneId
							}
						}));
					}
				});
			},
			minLength: 2,
			select: function( event, ui ) {

				zpRemoveError();

				/* Insert hidden input with timezone ID and birthplace coordinates */
				var hiddenInputs = {
					'geo_timezone_id': ui.item.timezoneid,
					'zp_lat_decimal': ui.item.latdeci,
					'zp_long_decimal': ui.item.lngdeci
				}
				for ( var elID in hiddenInputs ) {
					/* Remove any previous in case they're changing the city */
					var exists = document.getElementById( elID );
					if ( null !== exists ) {
						exists.remove();
					}
					/* Insert hidden inputs */
					elInput = document.createElement( 'input' );
				    elInput.setAttribute( 'type', 'hidden' );
				    elInput.id = elID;
					elInput.setAttribute( 'name', elID );
					elInput.setAttribute( 'value', hiddenInputs[ elID ] );
					document.getElementById( 'zp-timezone-id' ).appendChild( elInput );
				}

				zpGetOffset();
			}
		});
	
	/**
	 * Ajax request to get time offset
	 */
	function zpGetOffset() {
		const zpFormData = $( '#zp-ajax-birth-data :input' ).serialize() + '&action=zp_tz_offset';
		const xhr = new XMLHttpRequest();
		xhr.open( 'POST', zp_ajax_object.ajaxurl );
		xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
		xhr.responseType = 'json';
		
		xhr.onload = function() {
			var submitButton = document.getElementById( 'zp-fetch-birthreport' );

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
					if ( xhr.response.offset_geo && 'false' != xhr.response.offset_geo ) {

						/* remove previous errors if any */
						zpRemoveError();
								
						/* Display offset. */
						document.getElementById( 'zp-offset-wrap' ).style.display = 'block';
						document.getElementById( 'zp-offset-label' ).textContent = zp_ajax_object.utc + ' ';
						document.getElementById( 'zp_offset_geo' ).value = xhr.response.offset_geo;
						document.getElementById( 'zp-form-tip' ).style.display = 'block';

						/* Enable submit button */
						submitButton.removeAttribute( 'disabled' );

					}

				}

			}
		};

		xhr.send( zpFormData );
	}

		/* Fetch birth report upon clicking submit */

		$( '#zp-fetch-birthreport' ).click(function() {
			$.ajax({
				url: zp_ajax_object.ajaxurl,
				type: "POST",
				data: $( '#zp-birthreport-form' ).serialize(),
				dataType: "json",
				success: function( reportData ) {

					if (reportData.error) {
						zpRemoveError();
						var span = $( '<span />' );
						span.attr( 'class', 'ui-state-error' );
						span.text( reportData.error );
						$( '#zp-offset-wrap' ).after( span );

					} else {

						/* if neither null, blank, nor false */
						if ($.trim(reportData.report) && 'false' != $.trim(reportData.report)) {
							
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

})( jQuery );

/**
 * Check that the fields required to get offset are entered.
 */
function zpFieldsFilled() {
	var ids = ['geo_timezone_id','zp_long_decimal','zp_lat_decimal','place','minute','hour','year','day','month'];

	for ( var i of ids ) {

		var el = document.getElementById(i);

		if ( null === el ) {
			return false;
		}

        if ( el.value.length === 0 || ! el.value.trim() ) {
        	/* fail */
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
