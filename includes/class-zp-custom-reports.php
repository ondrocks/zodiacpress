<?php
/**
 * ZodiacPress ZP_Custom_Report class.
 *
 * Manages identifiers for all reports.
 *
 * @package  ZodiacPress
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class ZP_Custom_Reports {
	private static $custom_ids;
	private static $zp_settings;

	/**
	 * Gets the tab names for the Custom Reports admin page.
	 * @return array
	 */
	public static function get_tabs() {
		$tabs = array( 'custom_reports' => __( 'Custom Reports', 'zodiacpress' ) );
		
		// @todo if this is going to be called twice, make a property and check it first.

		if ( isset( self::$zp_settings ) ) {
			$options = self::$zp_settings;
		} else {
			$options = get_option( 'zodiacpress_settings' );
			self::$zp_settings = $options;
		}

		if ( isset( $options['custom_reports'] ) ) {

			foreach( $options['custom_reports'] as $id => $data ) {
				$tabs[ $id ] = $data['name'];
			}
		}

		return $tabs;
	}

	/**
	 * @test every tab, except for the 1st custom reports tab, will have the same sections::
	 */
	public static function get_tabs_sections() {

		$sections = array(
			'main' => __( 'Edit Report', 'zodiacpress'), // @test if this makes sense

			// @todo Orbs section is added ONLY if this report layout includes any aspects!!

			'technical' => __( 'Technical', 'zodiacpress' )

		);

		return $sections;
	}

	/**
	 * Gets a list of identifiers for existing custom reports.
	 *
	 * @return array List of IDs
	 */
	public static function get_ids() {
		if ( isset( self::$custom_ids ) ) {
			return self::$custom_ids;
		}

		if ( isset( self::$zp_settings ) ) {
			$options = self::$zp_settings;
		} else {
			$options = get_option( 'zodiacpress_settings' );
			self::$zp_settings = $options;
		}

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
		return ( in_array( $id, self::get_ids() ) || in_array( $id, $core_ids ) );
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
