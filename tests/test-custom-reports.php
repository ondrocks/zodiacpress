<?php
class Test_Custom_Reports extends WP_UnitTestCase {
	/**
	 * Test the ZP_Custom_Reports::get_ids() method with 0 reports
	 */
	public function test_get_custom_report_ids_0() {
		$actual = ZP_Custom_Reports::get_ids();
		$this->assertCount(0, $actual);
		$this->assertInternalType('array', $actual);
	}

	/**
	 * Test the ZP_Custom_Reports::create() method
	 */
	public function test_create_custom_report() {
		$report = 'career';
		ZP_Custom_Reports::create($report);
		$actual = get_option( 'zodiacpress_settings' )['custom_reports'];
		$this->assertInternalType('array', $actual);
		$this->assertCount(1, $actual);
		$this->assertArrayHasKey($report, $actual);
	}

}
