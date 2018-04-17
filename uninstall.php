<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package     ZodiacPress
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( is_multisite() ) {
	global $wpdb;
	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
	if ( $blogs ) {
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog['blog_id'] );
			zp_uninstall();
			restore_current_blog();
		}
	}
}
else {
	zp_uninstall();
}

/**
 * Uninstall function.
 *
 * The uninstall function will only proceed if
 * the user explicitly asks for all data to be removed.
 *
 * @return void
 */
function zp_uninstall() {

	$options = get_option( 'zodiacpress_settings' );

	// Make sure that the user wants to remove all the data.
	if ( isset( $options['remove_data'] ) && '1' == $options['remove_data'] ) {

		global $wpdb;// only delete atlas table from wpdb, not a custom database ($zpdb)

		// Delete options

		$option_keys = array(
			'zodiacpress_settings',
			'zp_atlas_db_installing',
			'zp_atlas_db_notice',
			'zp_atlas_db_pending',
			'zp_atlas_db_previous_notice',
			'zp_atlas_db_version'
		);

		$interpretations = array(
			'zp_natal_planets_in_signs',
			'zp_natal_planets_in_houses',
			'zp_natal_aspects_main',
			'zp_natal_aspects_moon',
			'zp_natal_aspects_mercury',
			'zp_natal_aspects_venus',
			'zp_natal_aspects_mars',
			'zp_natal_aspects_jupiter',
			'zp_natal_aspects_saturn',
			'zp_natal_aspects_uranus',
			'zp_natal_aspects_neptune',
			'zp_natal_aspects_pluto',
			'zp_natal_aspects_chiron',
			'zp_natal_aspects_lilith',
			'zp_natal_aspects_nn',
			'zp_natal_aspects_pof',
			'zp_natal_aspects_vertex',
			'zp_natal_aspects_asc',
			'zp_natal_aspects_mc'
			);

		$keys = array_merge( $option_keys, $interpretations );

		foreach ( $keys as $key ) {
			delete_option( $key );
		}
		
		zp_remove_caps();

		// Delete the zp_atlas database table
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "zp_atlas" );

	}
}

/**
 * Remove zodiacpress capabilities
 */
function zp_remove_caps() {
	global $wp_roles;
	if ( class_exists( 'WP_Roles' ) ) {
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
	}
	if ( is_object( $wp_roles ) ) {
		$wp_roles->remove_cap( 'administrator', 'manage_zodiacpress_settings' );
		$wp_roles->remove_cap( 'administrator', 'manage_zodiacpress_interps' );
	}
}