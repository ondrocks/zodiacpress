<?php
/**
 * Functions related to the chart drawing
 *
 * @package     ZodiacPress
 * @copyright   Copyright (c) 2016-2017, Isabel Castillo
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns the chart drawing image element
 */
function zp_get_chart_drawing( $chart, $colors = '' ) {
	global $zodiacpress_options;
	$i18n = array(
		'hypothetical'	=> __( 'Hypothetical Time:', 'zodiacpress' ),
		'time'			=> __( '12:00 pm', 'zodiacpress' )
	);

	if ( empty( $colors ) ) {
		$customizer_settings = ZP_Customize::get_settings();
	} else {
		// incorporate live customizer colors
		$customizer_settings = ZP_Customize::merge_settings( $colors );
	}
	
	// Get all orbs settings...
	$orb_settings = array();
	foreach ( $zodiacpress_options as $k => $v ) {
		// No need to pass conjunction orbs
		if ( 0 === strpos( $k, 'orb_' ) && false === strpos( $k, 'orb_conjunction_' ) ) {
			// No need to pass orbs that are the default 8
			if ( ! empty( $v ) || 8 != $v ) {
				$orb_settings[ $k ] = $v;
			}
		}
	}

	$custom = rawurlencode( serialize( $customizer_settings ) );
	$i = rawurlencode( serialize( $i18n ) );
	$l = rawurlencode( serialize( $chart->planets_longitude ) );
	$s = rawurlencode( serialize( $chart->planets_speed ) );
	$c = rawurlencode( serialize( $chart->cusps ) );
	$o =  rawurlencode( serialize( $orb_settings ) );
	$u = urlencode( serialize( $chart->unknown_time ) );

	$url = ZODIACPRESS_URL . 'image.php?zpl=' . $l . '&zps=' . $s . '&zpc=' . $c . '&zpi=' . $i . '&zpo=' . $o . '&zpcustom=' . $custom . '&zpu=' . $u;
	
	$request = wp_remote_post( $url );
	if ( is_wp_error( $request ) ) {
		return false;
	}

	// See http://ottopress.com/2011/a-good-use-for-base-64-encoding-data-uris/
	$image_data = base64_encode( wp_remote_retrieve_body( $request ) );
	$out = '<img src="data:image/png;base64,' . esc_attr( $image_data ) . '" class="zp-chart-drawing" alt="chart drawing" />';

	return $out;
}

/**
 * Get the chart drawing only for the Birth Report, if enabled.
 * @param array $form The validated form data.
 * @param object $chart The chart object.
 * @return string The html for the chart image or empty string if not enabled.
 * @since 1.5.6
 */
function zp_maybe_get_chart_drawing( $form, $chart ) {
	$image = '';

	if ( 'birthreport' == $form['zp-report-variation'] )  {
		$image = zp_get_chart_drawing( $chart );
	}

	return $image;
}

/**
 * Get a test sample chart drawing. Used for the Customizer preview.
 * @param array $colors The current customizer preview color settings
 */
function zp_get_sample_chart_drawing( $colors = false ) {
	// Chart data for Steve Jobs
	$chart = ZP_Chart::get_instance( array(
		'name'					=> 'Steve Jobs',
		'month'					=> '2',
		'day'					=> '24',
		'year'					=> '1955',
		'hour'					=> '19',
		'minute'				=> '15',
		'geo_timezone_id'		=> 'America/Los_Angeles',
		'place'					=> 'San Francisco, California, United States',
		'zp_lat_decimal'		=> '37.77493',
		'zp_long_decimal'		=> '-122.41942',
		'zp_offset_geo'			=> '-8',
		'action'				=> 'zp_birthreport',
		'zp-report-variation'	=> 'birthreport',
		'unknown_time'			=> '',
		'house_system'			=> false,
		'sidereal'				=> false
	) );
	return zp_get_chart_drawing( $chart, $colors );
}
