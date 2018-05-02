<?php
class Test_Custom_Reports extends WP_UnitTestCase {
	protected $reports = array('career', 'love', 'family', 'friends');

	/**
	 * Test the ZP_Custom_Reports::get_ids() method
	 */
	public function test_get_custom_report_ids() {
		// Test with 0 reports
		$actual = ZP_Custom_Reports::get_ids();
		$this->assertCount(0, $actual);
		$this->assertInternalType('array', $actual);

		// create some custom reports
		foreach($this->reports as $id) {
			ZP_Custom_Reports::create($id);
		}

		// Test again, but with 4 existing reports
		$actual = ZP_Custom_Reports::get_ids();
		$this->assertInternalType('array', $actual);
		$this->assertCount(4, $actual);
		foreach ($this->reports as $expected) {
			$this->assertContains($expected, $actual);
		}

		// Delete a report
		ZP_Custom_Reports::delete('friends');
		
		$expected_reports = array_slice($this->reports, 0, 3);// remove 'friends' element

		// Test again after deleting report
		$actual = ZP_Custom_Reports::get_ids();
		$this->assertInternalType('array', $actual);
		$this->assertCount(3, $actual);
		foreach ($expected_reports as $expected) {
			$this->assertContains($expected, $actual);
		}

	}

}
