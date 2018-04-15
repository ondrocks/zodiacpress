<?php
/**
 * Atlas Functions
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets the `$zpdb` global which is an abstraction for $wpdb.
 *
 * Allows atlas to reside in separate database rather than in the WordPress database.
 *
 * @global wpdb $wpdb The WordPress database class.
 * @global zpdb $zpdb The ZP_Atlas database abstraction.
 */
function zp_abstract_atlas_db() {
	global $wpdb, $zpdb;
	if ( isset( $zpdb ) ) {
		return;
	}

	/*
	 * Filters $wpdb to allow atlas to reside in a separate database
	 */
	$zpdb = apply_filters( 'zp_atlas_db', $wpdb );

}
add_action( 'plugins_loaded', 'zp_abstract_atlas_db' );

/**
 * Gets the atlas option which denotes whether to use the atlas db or GeoNames.org
 */
function zp_atlas_option() {
	static $option;
	if ( isset( $option ) ) {
		return $option;
	}
	$settings = get_option( 'zodiacpress_settings' );
	$option = ( isset( $settings['atlas'] ) ? $settings['atlas'] : false );
	return $option;
}

/**
 * Handles ajax request to Run Atlas Installer
 */
function zp_atlas_ajax_install() {
	check_ajax_referer( 'zp_atlas_install' );
	
	/**
	 * Temporary flag to not show the Atlas Installer button during background installation.
	 */
	update_option( 'zp_atlas_db_installing', true );// @todo THIS OPTION MUST BE ALSO DELETED WHEN ATLAS INSTALL IS COMPLETE


	// zp_atlas_create_table();// @todo PUT BACK IN AFTER TESTING (UNCOMMENT)
	// Trigger the first async task: to download the cities datafile
	// do_action('zp_atlas_import');// @todo PUT BACK IN AFTER TESTING (UNCOMMENT)
	
	wp_die();

}
add_action( 'wp_ajax_zp_atlas_install', 'zp_atlas_ajax_install' );
