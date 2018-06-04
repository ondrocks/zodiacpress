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
		// create a custom report
		$name = 'Career Report';
		ZP_Custom_Reports::create($name);
		// check if the created report exists
		$custom_reports = get_option( 'zodiacpress_settings' )['custom_reports']['careerreport'];
		$expected = array('name' => $name);
		$this->assertArraySubset($expected, $custom_reports);
	}

	/**
	 * Test the ZP_Custom_Reports::listitems() method for 'sign' type
	 */
	public function test_custom_reports_listitems_sign() {
		$expected = array(
			'sun_sign'		=> 'Sun Sign',
			'moon_sign'		=> 'Moon Sign',
			'mercury_sign'	=> 'Mercury Sign',
			'venus_sign'	=> 'Venus Sign',
			'mars_sign'		=> 'Mars Sign',
			'jupiter_sign'	=> 'Jupiter Sign',
			'saturn_sign'	=> 'Saturn Sign',
			'uranus_sign'	=> 'Uranus Sign',
			'neptune_sign'	=> 'Neptune Sign',
			'pluto_sign'	=> 'Pluto Sign',
			'chiron_sign'	=> 'Chiron Sign',
			'lilith_sign'	=> 'Black Moon Lilith Sign',
			'nn_sign'		=> 'North Node Sign',
			'pof_sign'		=> 'Part of Fortune Sign',
			'vertex_sign'	=> 'Vertex Sign',
			'asc_sign'		=> 'Ascendant Sign',
			'mc_sign'		=> 'Midheaven Sign'
		);

		$actual = ZP_Custom_Reports::listitems('sign');
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Test the ZP_Custom_Reports::listitems() method for 'house' type
	 */
	public function test_custom_reports_listitems_house() {
		$expected = array(
			'sun_house'		=> 'Sun House',
			'moon_house'	=> 'Moon House',
			'mercury_house'	=> 'Mercury House',
			'venus_house'	=> 'Venus House',
			'mars_house'	=> 'Mars House',
			'jupiter_house'	=> 'Jupiter House',
			'saturn_house'	=> 'Saturn House',
			'uranus_house'	=> 'Uranus House',
			'neptune_house'	=> 'Neptune House',
			'pluto_house'	=> 'Pluto House',
			'chiron_house'	=> 'Chiron House',
			'lilith_house'	=> 'Black Moon Lilith House',
			'nn_house'	=> 'North Node House',
			'pof_house'	=> 'Part of Fortune House',
			'vertex_house'	=> 'Vertex House'
		);

		$actual = ZP_Custom_Reports::listitems('house');
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Test the ZP_Custom_Reports::listitems() method for 'lord' type
	 */
	public function test_custom_reports_listitems_lord() {
		for ($i=1; $i < 13; $i++) {
			$expected[$i . '_lord'] = "House $i Lord";
		}
		$actual = ZP_Custom_Reports::listitems('lord');
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Test the ZP_Custom_Reports::listitems() method for 'residents' type
	 */
	public function test_custom_reports_listitems_residents() {
		for ($i=1; $i < 13; $i++) {
			$expected[$i . '_residents'] = "House $i Residents";
		}
		$actual = ZP_Custom_Reports::listitems('residents');
		$this->assertEquals($expected, $actual);
	}

	/**
	 * Test the ZP_Custom_Reports::listitems() method for 'aspects' type
	 */
	public function test_custom_reports_listitems_aspects() {
		$expected = array(
			'sun_conjunction_aspects'	=> 'Sun Conjunctions',
			'sun_sextile_aspects'		=> 'Sun Sextiles',
			'sun_square_aspects'		=> 'Sun Squares',
			'sun_trine_aspects'			=> 'Sun Trines',
			'sun_quincunx_aspects'		=> 'Sun Quincunxes',
			'sun_opposition_aspects'	=> 'Sun Oppositions',
			'moon_conjunction_aspects'	=> 'Moon Conjunctions',
			'moon_sextile_aspects'		=> 'Moon Sextiles',
			'moon_square_aspects'		=> 'Moon Squares',
			'moon_trine_aspects'		=> 'Moon Trines',
			'moon_quincunx_aspects'		=> 'Moon Quincunxes',
			'moon_opposition_aspects'	=> 'Moon Oppositions',
			'mercury_conjunction_aspects'	=> 'Mercury Conjunctions',
			'mercury_sextile_aspects'	=> 'Mercury Sextiles',
			'mercury_square_aspects'	=> 'Mercury Squares',
			'mercury_trine_aspects'		=> 'Mercury Trines',
			'mercury_quincunx_aspects'	=> 'Mercury Quincunxes',
			'mercury_opposition_aspects'=> 'Mercury Oppositions',
			'venus_conjunction_aspects'	=> 'Venus Conjunctions',
			'venus_sextile_aspects'		=> 'Venus Sextiles',
			'venus_square_aspects'		=> 'Venus Squares',
			'venus_trine_aspects'		=> 'Venus Trines',
			'venus_quincunx_aspects'	=> 'Venus Quincunxes',
			'venus_opposition_aspects'	=> 'Venus Oppositions',
		);
		$actual = ZP_Custom_Reports::listitems('aspects');
		$this->assertArraySubset($expected, $actual);
	}

	/**
	 * Test the ZP_Custom_Reports::get_item_type() method
	 */
	public function test_custom_reports_get_item_type() {
		$expected = array(
			'mars_conjunction_aspects'	=> 'aspects',
			'pof_sign'					=> 'sign',
			'sun_house'					=> 'house',
			'10_lord'					=> 'lord',
			'10_residents'				=> 'residents',
			'1_heading'					=> 'heading',
			'10_subheading'				=> 'subheading',
			'2_text'					=> 'text'
		);
		foreach ($expected as $item_id => $expected_type) {
			$actual = ZP_Custom_Reports::get_item_type($item_id);
			$this->assertEquals($expected_type, $actual);
		}
	}

	/**
	 * Test the ZP_Custom_Reports::tab_sections() method
	 */
	public function test_custom_reports_tab_sections() {
		$expected = array(
		    'edit'		=> 'Edit Report',
		    'technical'	=> 'Technical');
		
		$expected_orbs = $expected + array('orbs' => 'Orbs');

		// Test 1: create a custom report WITHOUT aspects

		$name = 'Work Report';
		$id = 'workreport';
		ZP_Custom_Reports::create($name);
		// add items without aspects
		$options = get_option('zodiacpress_settings');
		$options['custom_reports'][$id]['items'] = array(
			array('saturn_sign',''),
			array('1_heading',''),
			array('10_lord',''),
			array('8_residents','')
		);
		update_option('zodiacpress_settings', $options);

		$actual = ZP_Custom_Reports::tab_sections($id);
		$this->assertEquals($expected, $actual);

		// Test 2: create a new custom report WITH aspects

		$name = 'Love Report';
		$id = 'lovereport';
		ZP_Custom_Reports::create($name);
		$options = get_option('zodiacpress_settings');
		$options['custom_reports'][$id]['items'] = array(
			array('venus_sign',''),
			array('mars_conjunction_aspects','')
		);
		update_option('zodiacpress_settings', $options);

		$actual = ZP_Custom_Reports::tab_sections($id);
		$this->assertEquals($expected_orbs, $actual);
	}
}
