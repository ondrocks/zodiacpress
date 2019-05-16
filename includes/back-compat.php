<?php
/**
 * Backwards compatibility
 * 
 * @package     ZodiacPress
 * @since       1.8
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Deprecated: Get data for wp_localize_script
 * @todo To be removed in a future version
 */
function zp_get_script_localization_data() {
	// Temporarily add old data for back compatibility with addons
	return array_merge( zp_script_localization_data(), zp_geonames_js_strings() );
}

/**
 * Back compatibility for extensions: load new script for updated single-step form
 * @todo To be removed in a future version
 */
function zp_back_compat_scripts( $report_atts ) {
	if ( isset( $report_atts['sell'] ) && 'woocommerce' === $report_atts['sell'] ) {
		if ( defined( 'ZP_SELL_REPORTS_VERSION' ) && version_compare( ZP_SELL_REPORTS_VERSION, '1.2', '<' ) ) {
			// swap the 'zp-sell-reports-form' script with back-compat.js
			wp_dequeue_script( 'zp-sell-reports-form' );
			wp_enqueue_script( 'zp-back-compat' );
		}
	}
}
add_action( 'zp_report_shortcode_before', 'zp_back_compat_scripts', 99 );
/**
 * Registers new cron schedule
 * @todo deprecated. will be removed in next update, after event using this schedule is removed first.
 *
 * @param array $schedules
 * @return array
 */
add_filter( 'cron_schedules', 'zp_add_cron_schedule' );
// @todo deprecated. will be removed in next update
function zp_add_cron_schedule( $schedules = array() ) {
	// Adds once weekly to the existing schedules.
	$schedules['weekly'] = array(
		'interval' => 604800,
		'display'  => __( 'Once Weekly', 'zodiacpress' )
	);
	return $schedules;
}
/**
 * Delete deprecated options
 * @todo remove in next update, and delete option zp_cleanup_deprecated_options_v19
 * @since 1.9
 */
add_action( 'admin_init', function() {
	if ( get_option( 'zp_cleanup_deprecated_options_v19' ) != 'completed' ) {
		$keys = array( 'house_systems', 'sell_reports_with_woocommerce', 'windows_server', 'planet_lookup' );
        foreach( $keys as $k ) {
			delete_option( 'zodiacpress_' . $k . '_license_key' );
			delete_option( 'zodiacpress_' . $k . '_license_active' );
		}

		// Clear event which is no longer needed since we removed need for licenses
		wp_clear_scheduled_hook('zp_weekly_scheduled_events');

        update_option( 'zp_cleanup_deprecated_options_v19', 'completed' );
    }
} );