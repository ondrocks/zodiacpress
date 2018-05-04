<?php
/**
 * Scripts
 *
 * @package     ZodiacPress
 */
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Register styles and scripts
 */
function zp_register_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	
	/* If atlas db option is selected and if the atlas is installed, use autocomplete-db.js instead of the regular autocomplete.js. */

	if ( ZP_Atlas_DB::use_db() ) {
		$autocomplete_js = 'zp-autocomplete-db';
		$strings = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
	} else {
		$autocomplete_js = 'zp-autocomplete';
		$strings = zp_geonames_js_strings();
	}

	wp_register_style( 'zp', ZODIACPRESS_URL . 'assets/css/zp' . $suffix . '.css', array(), ZODIACPRESS_VERSION );
	// for RTL languages
	wp_register_style( 'zp-rtl', ZODIACPRESS_URL . 'assets/css/zp-rtl' . $suffix . '.css', array(), ZODIACPRESS_VERSION );
	// autocomplete script
	wp_register_script( 'zp-autocomplete', ZODIACPRESS_URL . 'assets/js/' . $autocomplete_js . $suffix . '.js', array( 'jquery-ui-autocomplete', 'jquery' ), ZODIACPRESS_VERSION );
	wp_localize_script( 'zp-autocomplete', 'zp_js_strings', $strings );	
	// core script
	wp_register_script( 'zp', ZODIACPRESS_URL . 'assets/js/zp' . $suffix . '.js', array( 'jquery' ), ZODIACPRESS_VERSION );
	wp_localize_script( 'zp', 'zp_ajax_object', zp_script_localization_data() );
	// back compatibility @todo to be removed in future version
	wp_register_script( 'zp-back-compat', ZODIACPRESS_URL . 'assets/js/back-compat' . $suffix . '.js', array( 'jquery' ), ZODIACPRESS_VERSION );
	wp_localize_script( 'zp-back-compat', 'zp_ajax_object', zp_script_localization_data() );
}
	
add_action( 'wp_enqueue_scripts', 'zp_register_scripts' );

/**
 * Load admin-specific scripts and styles.
 */
function zp_admin_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	wp_register_style( 'zp-admin', ZODIACPRESS_URL . 'assets/css/zp-admin' . $suffix . '.css', array(), ZODIACPRESS_VERSION );
	wp_enqueue_style( 'zp-admin' );
	wp_register_script( 'zp-atlas-install', ZODIACPRESS_URL . '/assets/js/admin-atlas-install' . $suffix . '.js', array( 'jquery' ), ZODIACPRESS_VERSION, true );
	wp_localize_script( 'zp-atlas-install', 'zpAtlasStrings',
		array(
			'adminurl'		=> admin_url(),
			'checkStatus'	=> __( 'Check the status.', 'zodiacpress' ),
			'creatingKeys'	=> zp_string( 'creating' ),
			'dismiss'		=> __( 'Dismiss this notice.', 'zodiacpress' ),
			'inserting'		=> zp_string( 'inserting' ),
			'installing'	=> zp_string( 'installing' ),
   			'installingNotice'	=> zp_string( 'installing_notice' ),
   			'installingNow' => get_option( 'zp_atlas_db_installing' ),
			'nonce'			=> wp_create_nonce( 'zp_atlas_install' ),
			'statusHeading'	=> __( 'ZodiacPress Status Message', 'zodiacpress' )
		)
	);
	
	// add install script only if atlas has not been installed and a custom db is not being used.
	if ( ! ZP_Atlas_DB::is_installed() && ! ZP_Atlas_DB::is_separate_db() ) {
		wp_enqueue_script( 'zp-atlas-install' );
	}

	if ( zp_is_admin_page() ) {
		wp_register_script( 'zp-admin', ZODIACPRESS_URL . 'assets/js/admin' . $suffix . '.js', array(), ZODIACPRESS_VERSION, true );
		wp_localize_script( 'zp-admin', 'zp_admin_strings', array(
			'adminPost' => admin_url( 'admin-post.php' ),
			'cancel'	=> __( 'Cancel', 'zodiacpress' ),
			'create'	=> __( 'Create', 'zodiacpress' ),
			'label'		=> __( 'Report Name', 'zodiacpress' ),
			'nonce'		=> wp_create_nonce( 'zp_create_new_report' )
		) );
		wp_enqueue_script( 'zp-admin' );
	}
}
add_action( 'admin_enqueue_scripts', 'zp_admin_scripts', 100 );

/**
 * Get data strings for the ZP core script.
 * @since 1.8
 */
function zp_script_localization_data() {
	global $zodiacpress_options;
	$draw = isset( $zodiacpress_options['add_drawing_to_birthreport'] ) ? $zodiacpress_options['add_drawing_to_birthreport'] : '';
	$data = array(
		'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
		'utc'		=> __( 'UTC time offset:', 'zodiacpress' ),
		'draw'		=> $draw
	);
	return $data;
}

/**
 * Get data strings for the zp-autocomplete.js script.
 */
function zp_geonames_js_strings() {
	global $zodiacpress_options;
	// If language is other than English, get lang code to tranlsate Autocomplete cities.
	$wplang = get_locale();
	$langcode = substr( $wplang, 0, 2 );
	$city_list_lang = ( 'en' != $langcode ) ? $langcode : '';
	$geonames_username = empty( $zodiacpress_options[ 'geonames_user' ] ) ? 'demo' : trim( $zodiacpress_options[ 'geonames_user' ] );
	$data = array(
		'autocomplete_ajaxurl'	=> apply_filters( 'zp_autocomplete_ajaxurl', admin_url( 'admin-ajax.php' ) ),
		'autocomplete_action'	=> apply_filters( 'zp_ajax_geonames_action', 'zp_get_cities_list' ),
		'dataType'				=> apply_filters( 'zp_ajax_datatype', 'json' ),
		'type'					=> apply_filters( 'zp_ajax_type', 'POST' ),			
		'lang'					=> $city_list_lang,
		'geonames_user'			=> $geonames_username
	);
	return $data;
}
