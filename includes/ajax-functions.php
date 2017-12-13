<?php
/**
 * AJAX Functions
 *
 * Process the AJAX actions.
 *
 * @package     ZodiacPress
 * @subpackage  Functions/AJAX
 * @copyright   Copyright (c) 2016-2017, Isabel Castillo
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handles ajax request to get cities list for autocomplete birth place field.
 * 
 * It's done like this rather than from the browswer so that I can make this work
 * on HTTPS pages, otherwise GeoNames only serves http pages.
 */
function zp_ajax_autocomplete_cities() {
	if ( empty( $_POST['name_startsWith'] ) ) {
		return;
	}

	$api_params = array(
		'featureClass'		=> 'P',
		'style'				=> 'full',
		'maxRows'			=> 12,
		'name_startsWith'	=> sanitize_text_field( $_POST['name_startsWith'] ),
		'username'			=> urlencode( sanitize_text_field( $_POST['username'] ) ),
		'lang'				=> ! empty( $_POST['lang'] ) ? sanitize_text_field( $_POST['lang'] ) : '',
	);

	$request = wp_remote_post( 'http://api.geonames.org/searchJSON', array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
	if ( ! is_wp_error( $request ) ) {
		$request = wp_remote_retrieve_body( $request );
	} else {
		$request = false;
	}

	echo $request;
	wp_die();

}
add_action( 'wp_ajax_zp_get_cities_list', 'zp_ajax_autocomplete_cities' );
add_action( 'wp_ajax_nopriv_zp_get_cities_list', 'zp_ajax_autocomplete_cities' );

/**
 * Handles ajax request to calculate timezone offset and send back to form fields
 */
function zp_ajax_get_time_offset() {
	parse_str( $_POST['post_data'], $post_data );

	$offset_geo = null;
	$validated = zp_validate_form( $post_data, true );

	if ( ! is_array( $validated )  ) {

		// We have an error
		echo json_encode( array( 'error' => $validated ) );
		wp_die();

	}

	$dtstamp = strftime("%Y-%m-%d %H:%M:%S", mktime( $validated['hour'], $validated['minute'], 0, $validated['month'], $validated['day'], $validated['year'] ));

	// get time offset
	$offset_geo = $validated['geo_timezone_id'] ? zp_get_timezone_offset( $validated['geo_timezone_id'], $dtstamp, $validated['zp_lat_decimal'], $validated['zp_long_decimal'] ) : null;

	echo json_encode( array( 'offset_geo' => $offset_geo ) );
	wp_die();
}
add_action( 'wp_ajax_zp_tz_offset', 'zp_ajax_get_time_offset' );
add_action( 'wp_ajax_nopriv_zp_tz_offset', 'zp_ajax_get_time_offset' );

/**
 * Handles ajax request to get the Birth Report upon form submission.
 */
function zp_ajax_get_birthreport() {
	$validated = zp_validate_form( $_POST );
	if ( ! is_array( $validated )  ) {
		echo json_encode( array( 'error' => $validated ) );
		wp_die();
	}
	$chart = ZP_Chart::get_instance( $validated );
	if ( empty( $chart->planets_longitude ) ) {
		$report = __( 'Something went wrong.', 'zodiacpress' );
	} else {
		$birth_report = new ZP_Birth_Report( $chart, $validated );
		$report = wp_kses_post( $birth_report->get_report() );
	}

	echo json_encode( array( 'report' => $report ) );
	wp_die();
}
add_action( 'wp_ajax_zp_birthreport', 'zp_ajax_get_birthreport' );
add_action( 'wp_ajax_nopriv_zp_birthreport', 'zp_ajax_get_birthreport' );

/**
 * Handles ajax request to get an updated chartwheel image for the live color preview for customizer.
 */
function zp_ajax_get_customizer_image() {
	$colors = array();

	foreach( $_POST['post_data'] as $k => $color ) {
		$colors[ $k ] = sanitize_hex_color( $color );
	}

	$image = wp_kses_post( zp_get_sample_chart_drawing( $colors ) );
	echo json_encode( array( 'image' => $image ) );
	wp_die();
}
add_action( 'wp_ajax_zp_customize_preview_image', 'zp_ajax_get_customizer_image' );

/****************************************************
* @todo
* @test 
****************************************************/
function zp_ajax_get_block_editor_form() {

	/****************************************************
	* @todo validate incoming data, if i need sidereal and house sys var here.
	* 

	****************************************************/
    
    isa_log('isa @test 333');// @todo remove.

    /****************************************************
    * @todo
    * sidereal and house system attributes must be gotten from the sidebar. @todo update below.
    @todo actually, not here but in the PHP server-side rendering is where those atts are needed.

    ****************************************************/

	$out = zp_render_block_birthreport( array(
		'sidereal'		=> false,
		'house_system'	=> false
	) );

    echo json_encode( array( 'form' => $out ) );

    wp_die();
}
add_action( 'wp_ajax_zp_get_block_form', 'zp_ajax_get_block_editor_form' );
