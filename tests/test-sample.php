<?php
/**
 * Class SampleTest
 *
 * @package Beteasy_Wordpress_Challenge
 */

/**
 * Sample test case.
 */
class SampleTest extends WP_UnitTestCase {

	/**
	 * Test API issues
	 */
	public function test_api_response() {

		$this->assertInternalType( 'array', get_races() );
	}
}
