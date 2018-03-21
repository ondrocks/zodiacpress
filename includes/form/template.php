<?php
/**
 * Form Template
 *
 * @package     ZodiacPress
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display the form to generate an astrology report
 *
 * @param string $report Identifier to distinguish this type of report.
 * @param array $args 
 */
function zp_form( $report, $args = array() ) {
	global $zodiacpress_options;
	$allow_unknown_bt_key_prefix = $report;
	if ( false !== strpos( $args['report'], 'planet_lookup' ) ) {
		$allow_unknown_bt_key_prefix = 'planet_lookup';
	} elseif ( 'drawing' === $args['report'] ) {
		$allow_unknown_bt_key_prefix = 'drawing';
	}
	$allow_unknown_bt_key = $allow_unknown_bt_key_prefix . '_allow_unknown_bt';
	$allow_unknown_bt = empty( $zodiacpress_options[ $allow_unknown_bt_key ] ) ? false : true;
	?>
	<noscript class="ui-state-highlight"><?php _e( 'This form requires JavaScript. Your browser either does not support JavaScript or has it disabled.', 'zodiacpress' ); ?></noscript>
	<form id="zp-<?php echo esc_attr( $report ); ?>-form" method="post" class="zp-form">
	
		<?php 
		// Show name field only for reports that require it
		if (  apply_filters( 'zp_form_show_name_field', true, $args['report'] ) ) {

			do_action( 'zp_form_above_name', $report, $args );
			zp_name_form_field();
			do_action( 'zp_form_below_name', $report, $args );
	
		}
		?>

		<div id="zp-ajax-birth-data">
			<fieldset class="zp-birthdate">
    			<legend><?php _e( 'Birth Date', 'zodiacpress' ); ?></legend>
				<?php 
				if ( zp_is_month_before_day() ) {
					zp_month_form_field();
					zp_day_form_field();
				} else {
					zp_day_form_field();
					zp_month_form_field();
				}
				?>

				<label for="year" class="screen-reader-text">
					<?php _e( 'Birth Year', 'zodiacpress' ); ?></label>
				<select id="year" name="year" required><?php zp_year_select_options(); ?></select>
			</fieldset>

			<fieldset class="zp-birthtime">
    			<legend><?php _e( 'Exact Birth Time', 'zodiacpress' ); ?></legend>
				<label for="hour" class="screen-reader-text">
					<?php _e( 'Birth Hour', 'zodiacpress' ); ?></label>
				<select id="hour" name="hour"><?php zp_hour_select_options(); ?></select>
				<label for="minute" class="screen-reader-text">
					<?php _e( 'Birth Minute', 'zodiacpress' ); ?></label>
				<select id="minute" name="minute"><?php zp_minute_select_options(); ?></select>
				<?php
				// Show the unknown time checkbox, but not for reports that require a birth time
				if ( ! in_array( $args['report'], apply_filters( 'zp_reports_require_birthtime', array() ) ) ) {
					// ...only if unkown time is allowed in settings
					if ( $allow_unknown_bt ) {
						?>
						<p class="zp-unknown-time-field zp-small">
							<label for="unknown_time" class="screen-reader-text"><?php _e( 'Unknown Birth Time', 'zodiacpress' ); ?></label>
							<input type="checkbox" id="unknown_time" name="unknown_time" /> <?php echo apply_filters( 'zp_unknown_birth_time_checkbox', __( 'If birth time is unknown, check this box.', 'zodiacpress' ) ); ?><strong>*</strong></p>
						<?php
					}

				} else {

					echo apply_filters( 'zp_birth_time_required',
						'<p class="zp-unknown-time-field zp-small">* ' .
						__( 'Birth time is required for this type of report.', 'zodiacpress' ) .
						'</p>',
						$args );
	
				}
				?>
			</fieldset>
			<p class="zp-birthplace">
				<label for="place" class="zp-form-label"><?php _e( 'Birth City', 'zodiacpress' ); ?></label>
				<span class="ui-widget zp-input-text-wrap">
					<input id="place" name="place" value="" class="zp-input-text" />
				</span>
			</p>
			
			<span id="zp-timezone-id"></span>

			<?php do_action( 'zp_form_below_person_one_ajax', $report, $args ); ?>
			<input type="hidden" name="zp-report-variation" value="<?php echo $args['report']; ?>">
			
		</div><!-- end # zp-ajax-birth-data -->

		<label for="zp_offset_geo" class="screen-reader-text" aria-label="<?php _e( 'UTC time offset', 'zodiacpress' ); ?>"></label>
		<p id="zp-offset-wrap" class="zp-clear">
			<span id="zp-offset-label"></span>
			<input id="zp_offset_geo" name="zp_offset_geo" size="47" type="text" tabindex="-1" />
		</p>
		<?php do_action( 'zp_form_below_person_one_offset', $report, $args ); ?>

		<p id="zp-submit-wrap">
			<img id="zp-ajax-loader" src="<?php echo esc_url( ZODIACPRESS_URL . 'assets/ajax-loader.gif' ); ?>" alt="" />
			<button id="zp-fetch-offset" class="zp-button"><?php _e( 'Next', 'zodiacpress' ); ?></button>
			<?php if ( $args['sidereal'] ) { ?>
				<input type="hidden" name="zp_report_sidereal" value="<?php echo $args['sidereal']; ?>">
			<?php }
			if ( $args['house_system'] ) { ?>
				<input type="hidden" name="zp_report_house_system" value="<?php echo $args['house_system']; ?>">
			<?php } ?>
			<input type="hidden" name="action" value="zp_<?php echo esc_attr( $report ); ?>">
			<input type="button" id="zp-fetch-<?php echo esc_attr( $report ); ?>" class="zp-button" value="<?php echo apply_filters( 'zp_form_submit_text', __( 'Submit', 'zodiacpress' ), $args ); ?>" /></p>

		<p id="zp-form-tip" class="zp-small"><?php _e( 'Tip: Make sure the <strong>UTC time offset</strong> is correct. If it\'s wrong, you can change it.', 'zodiacpress' ); ?></p>

		<?php
		// Add note about unknown birth time, but not for reports that require a birth time
		if ( ! in_array( $args['report'], apply_filters( 'zp_reports_require_birthtime', array() ) ) ) {
			// ...only if unkown time is allowed in settings, but not for 'Only Chart Wheel'
			if ( $allow_unknown_bt && 'drawing' !== $args['report'] ) {

				echo apply_filters( 'zp_allow_unknown_time_note',
						'<p class="zp-birth-time-note zp-small">' .
						__( '<strong>* NOTE: </strong> If birth time is unknown, the report will not include positions or aspects for the Moon, Ascendant, Midheaven, Vertex, or Part of Fortune, nor will it include House positions for any planets.', 'zodiacpress' ) .
						'</p>',
						$args );
			}

		}
		do_action( 'zp_form_bottom', $allow_unknown_bt, $report, $args ); ?>
	</form>
	<?php
}
