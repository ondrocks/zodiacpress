<?php
/**
 * ZodiacPress ZP_Report class.
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
	 * The Chart object for this report.
	 */
	private $chart;// @test need?

	/**
	 * The ZP settings.
	 */
	private $options = array();
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
	}

	/**
	 * Gets all report items.
	 * @return array $items Array of report items.
	 */
	public function get_items() {
		return $this->items;
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
	 * @return mixed|bool|string True if name was updated, false is report id doesn't exist or other error.
	 */
	public function update_items( $items ) {
		if ( ! isset( $this->options['custom_reports'][ $this->id ] ) ) {
			return false;
		}

		if ( ! isset( $items ) ) {
			return false;
		}

		// Update items
		$_name = $this->options['custom_reports'][ $this->id ]['name'];
		$this->options['custom_reports'][ $this->id ] = array( 'name' => $_name, 'items' => $items );
		$update = update_option( 'zodiacpress_settings', $this->options );
		$this->items = $items;

		return $update;
	}

}
