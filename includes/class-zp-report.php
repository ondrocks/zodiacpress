<?php
/**
 * ZodiacPress ZP_Report class to build and display a custom report.
 *
 * @package  ZodiacPress
 * @since 1.9
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class used to build and display custom reports.
 */
class ZP_Report {
	/**
	 * The ID for this report.
	 * @var array
	 */
	private $id;

	/**
	 * The items included in this report.
	 */
	private $items;

	/**
	 * The orbs for this report's aspects items, if any.
	 */
	private $orbs;

	/**
	 * The Chart object for this report.
	 */
	private $chart;// @test need?

	/**
	 * The ZP settings.
	 */
	private $options;
	/**
	 * Constructor.
	 *
	 * @param string $_id The ID for this report.
	 * @param object $_chart A ZP_Chart object
	 */
	public function __construct( $_id, $_chart = null ) {
		$this->id = $_id;
		$this->chart = $_chart;// @test need?
		$this->options = get_option( 'zodiacpress_settings', array() );
		$this->items = isset( $this->options['custom_reports'][ $this->id ]['items'] ) ? $this->options['custom_reports'][ $this->id ]['items'] : array();
		$this->orbs = isset( $this->options['custom_reports'][ $this->id ]['orbs'] ) ? $this->options['custom_reports'][ $this->id ]['orbs'] : array();
	}

	/**
	 * Gets all report items.
	 * @return array $items Array of report items.
	 */
	public function get_items() {
		return $this->items;
	}

	/**
	 * Gets all report orbs for aspect items, if any.
	 * @return array $items Array of orbs for each type of aspect.
	 */
	public function get_orbs() {
		return $this->orbs;
	}

	/**
	 * Returns the report items formatted to edit.
	 */
	public function get_edit_markup() {
		$out = '<div id="report-instructions"';
		$out .= ( ! empty( $this->items ) ) ? ' class="report-instructions-inactive">' : '>';
		$out .= '<p>' . __( 'Add report items from the column on the left.', 'zodiacpress' ) . '</p>';
		$out .= '</div> <ul class="report" id="report-to-edit"> ';		

		if ( empty( $this->items ) ) {
			return $out . '</ul>';
		}

		$out .= ZP_Custom_Reports::get_edit_html_items( $this->items );
		$out .= ' </ul> ';
		return $out;
	}

	/**
	 * Updates the items for a custom report
	 * @return bool True if name was updated, false is report id doesn't exist or other error.
	 */
	public function update_items( $items ) {
		if ( ! isset( $this->options['custom_reports'][ $this->id ] ) ) {
			return false;
		}
		if ( ! isset( $items ) ) {
			return false;
		}
		// Update items
		$this->options['custom_reports'][ $this->id ]['items'] = $items;
		$update = update_option( 'zodiacpress_settings', $this->options );
		$this->items = $items;
		return $update;
	}


	/**
	 * Updates the orbs for a custom report
	 * @return bool True if orbs were updated, false is report id doesn't exist or udpated failed.
	 */
	public function update_orbs( $orbs ) {
		if ( ! isset( $this->options['custom_reports'][ $this->id ] ) ) {
			return false;
		}
		if ( ! isset( $orbs ) ) {
			return false;
		}
		// Update orbs
		$this->options['custom_reports'][ $this->id ]['orbs'] = $orbs;
		$update = update_option( 'zodiacpress_settings', $this->options );
		$this->orbs = $orbs;
		return $update;
	}

	/** @test now now
	 * Updates the interpretations for a custom report
	 * @param string $option_key Option key for this set of interpretations
	 * @param array $interps Interpretations text for all items in this set
	 * @return bool True if updated, otherwise false.
	 */
	public function update_interpretations( $option_key, $interps ) {
		if ( ! isset( $option_key ) || ! isset( $interps ) || ! is_array( $interps ) ) {
			return false;
		}
		$option = get_option( $option_key, array() );
		// add the new interps to existing interps, updating text if necessary
		$new = array_replace( $option, $interps );
		$update = update_option( $option_key, $new );
		return $update;
	}
}
