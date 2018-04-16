(function( $ ) {
	
	/* Autocomplete city */

	$( '#place' ).autocomplete({
		source: function( request, response ) {

			/* Hide the geonames error message, if any, in case they are trying again */
			zpRemoveError();
				
			$.ajax({
				url: zp_js_strings.autocomplete_ajaxurl,
				dataType: zp_js_strings.dataType,
				type: zp_js_strings.type,
				data: {
					featureClass: "P",
					style: "full",
					maxRows: 12,
					username: zp_js_strings.geonames_user,
					action: zp_js_strings.autocomplete_action ? zp_js_strings.autocomplete_action : undefined,
					name_startsWith: request.term,
					lang: zp_js_strings.lang
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
			    elInput.type = 'hidden';
			    elInput.id = elID;
				elInput.name = elID;
				elInput.value = hiddenInputs[ elID ];
				document.getElementById( 'zp-timezone-id' ).appendChild( elInput );
			}

			zpGetOffset();
		}
	});
	
})( jQuery );
