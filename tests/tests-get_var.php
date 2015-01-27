<?php


/**
 * tests the _GET 
 */
class Tests_Get_var extends WP_UnitTestCase {

	public function test_the_content() {
		
		$magic = new calderawp\filter\magictag();

		$this->assertSame( 'the date is ' . date('Y-m') , $magic->do_magic_tag( 'the date is {date:Y-m}' ) );

	}

}