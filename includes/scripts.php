<?php
/**
 * Scripts
 *
 * @package     ZodiacPress
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register styles
 */
function zp_register_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	wp_register_style( 'zp', ZODIACPRESS_URL . 'assets/css/zp' . $suffix . '.css', array(), ZODIACPRESS_VERSION );
	// for RTL languages
	wp_register_style( 'zp-rtl', ZODIACPRESS_URL . 'assets/css/zp-rtl' . $suffix . '.css', array(), ZODIACPRESS_VERSION );
	wp_register_script( 'zp', ZODIACPRESS_URL . 'assets/js/zp' . $suffix . '.js', array( 'jquery-ui-autocomplete', 'jquery' ), ZODIACPRESS_VERSION );
	wp_localize_script( 'zp', 'zp_ajax_object', zp_get_script_localization_data() );
}
	
add_action( 'wp_enqueue_scripts', 'zp_register_scripts' );

/**
 * Load admin-specific styles.
 */
function zp_load_admin_scripts() {
	if ( ! zp_is_admin_page() ) {
		return;
	}
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_register_style( 'zp-admin', ZODIACPRESS_URL . 'assets/css/zp-admin' . $suffix . '.css', array(), ZODIACPRESS_VERSION );
	wp_enqueue_style( 'zp-admin' );
	wp_register_script( 'zp-admin', ZODIACPRESS_URL . 'assets/js/admin' . $suffix . '.js', array(), ZODIACPRESS_VERSION, true );
	wp_enqueue_script( 'zp-admin' );

	wp_register_script( 'zp-atlas-install', ZODIACPRESS_URL . '/assets/js/admin-atlas-install' . $suffix . '.js', array( 'jquery' ), ZODIACPRESS_VERSION, true );

	wp_localize_script( 'zp-atlas-install', 'zp_atlas_install_strings',
		array(
			// @todo check what is still needed...remove what is not used
			'active'		=> zp_string( 'active' ),
			'adminurl'		=> admin_url(),
			'checkStatus'	=> zp_string( 'check_status' ),
			'complete'		=> zp_string( 'complete' ),
			'dismiss'		=> __( 'Dismiss this notice.', 'zodiacpress' ),
			'inserting'		=> zp_string( 'inserting' ),
   			'installingNotice'	=> __( 'The atlas is being installed in the background. This will take a few minutes.', 'zodiacpress' ),
   			'installing'	=> zp_string( 'installing' ),
   			'installingNow' => get_option( 'zp_atlas_db_installing' ),
			'nonce'			=> wp_create_nonce( 'zp_atlas_install' ),
			'none'			=> zp_string( 'none' ),
			// @todo maybe need this in zp_string() since may be used more than once
			'statusHeading'	=> __( 'ZodiacPress Status Message', 'zodiacpress' )
			
		)
	);
	
	// add install script only if atlas has not been installed and a custom db is not being used.
	if ( ! ZP_Atlas_DB::is_installed() && ! ZP_Atlas_DB::is_separate_db() ) {
		wp_enqueue_script( 'zp-atlas-install' );
	}
}
add_action( 'admin_enqueue_scripts', 'zp_load_admin_scripts', 100 );

/**
 * Get data for wp_localize_script
 */
function zp_get_script_localization_data() {
	global $zodiacpress_options;
	// If language is other than English, get lang code to tranlsate Autocomplete cities.
	$wplang = get_locale();
	$langcode = substr( $wplang, 0, 2 );
	$city_list_lang = ( 'en' != $langcode ) ? $langcode : '';

	$geonames_username = empty( $zodiacpress_options[ 'geonames_user' ] ) ? 'demo' : trim( $zodiacpress_options[ 'geonames_user' ] );

	$draw = isset( $zodiacpress_options['add_drawing_to_birthreport'] ) ? $zodiacpress_options['add_drawing_to_birthreport'] : '';

	$data = array(
			'ajaxurl'				=> admin_url( 'admin-ajax.php' ),
			'autocomplete_ajaxurl'	=> apply_filters( 'zp_autocomplete_ajaxurl', admin_url( 'admin-ajax.php' ) ),
			'autocomplete_action'	=> apply_filters( 'zp_ajax_geonames_action', 'zp_get_cities_list' ),
			'dataType'				=> apply_filters( 'zp_ajax_datatype', 'json' ),
			'type'					=> apply_filters( 'zp_ajax_type', 'POST' ),			
			'utc'					=> __( 'UTC time offset:', 'zodiacpress' ),
			'lang'					=> $city_list_lang,
			'geonames_user'			=> $geonames_username,
			'draw'					=> $draw
		);

	return $data;
}
