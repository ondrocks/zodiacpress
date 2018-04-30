<?php
/**
 * ZodiacPress ZP_Report_IDs class.
 *
 * Manages identifiers for all reports.
 *
 * @package  ZodiacPress
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class ZP_Report_IDs {
	private static $custom_ids;
	private static $zp_settings;

	/**
	 * Gets a list of identifiers for existing custom reports.
	 *
	 * @return array List of IDs
	 */
	public static function get_custom() {
		if ( isset( self::$custom_ids ) ) {
			return self::$custom_ids;
		}

		if ( isset( self::$zp_settings ) ) {
			$options = self::$zp_settings;
		} else {
			$options = get_option( 'zodiacpress_settings' );
			self::$zp_settings = $options;
		}

		// @todo make sure "$options['custom_reports']" is always an array, even before any custom report ids exist, so be sure settings page saves this as an empty array (not a blank or empty string).

		self::$custom_ids = isset( $options['custom_reports'] ) ? array_keys( $options['custom_reports'] ) : array();
		return self::$custom_ids;
	}

	/**
	 * Checks if a report ID exists among all custom and core reports
	 * 
	 * @return bool
	 */
	private static function exists( $id ) {
		$core_ids = array( 'birthreport', 'birthreport_preview', 'drawing', 'house_systems' );
		foreach ( zp_get_planets() as $p ) {
			$core_ids[] = 'planet_lookup_' . $p['id'];
		}
		return ( in_array( $id, self::get_custom() ) || in_array( $id, $core_ids ) );
	}

	/**
	 * Saves a new custom report ID 
	 * 
	 * @return bool
	 */
	public static function add( $id ) {
		$id = sanitize_key( $id );
		$new_id = substr( $id, 0, 13 );// allow max 13 chars

		// If this report ID already exists, create a unique report ID
		if ( self::exists( $new_id ) ) {
			$suffix = 1;
			do {
				$unique_id = $id . $suffix;
				$suffix ++;
			} while ( self::exists( $unique_id ) );
			$new_id = $unique_id;
		}

		// Save the new report id to db
		$options = self::$zp_settings;
		$options['custom_reports'][ $new_id ] = array();
		update_option('zodiacpress_settings', $options);

		// Update class properties with the new report
		self::$zp_settings = $options;
		self::$custom_ids[] = $new_id;
	}

	/**
	 * Deletes a custom report ID 
	 * 
	 * @return bool
	 */
	public static function delete( $id ) {
		// delete the report id from db
		if ( isset( self::$zp_settings ) ) {
			$options = self::$zp_settings;
		} else {
			$options = get_option( 'zodiacpress_settings' );
			self::$zp_settings = $options;
		}
		unset( $options['custom_reports'][ $id ] );
		update_option('zodiacpress_settings', $options);

		// Update class properties to reflect deletion
		self::$zp_settings = $options;
		if ( ( $key = array_search( $id, self::$custom_ids ) ) !== false ) {
			unset( self::$custom_ids[ $key ] );
		}        
	}
}
