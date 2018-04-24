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
		if ( defined( 'ZP_SELL_REPORTS_VERSION' ) && version_compare( ZP_SELL_REPORTS_VERSION,  '1.2', '<' ) ) {
			// swap the 'zp-sell-reports-form' script with back-compat.js
			wp_dequeue_script( 'zp-sell-reports-form' );
			wp_enqueue_script( 'zp-back-compat' );
		}
	}
}
add_action( 'zp_report_shortcode_before', 'zp_back_compat_scripts', 99 );
