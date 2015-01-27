<?php


/**
 * tests the _GET 
 */
class Tests_The_Magic extends WP_UnitTestCase {

	public function test_get_var() {
		
		$magic = new calderawp\filter\magictag();
		$count = rand(0, 999);
		// _GETS
		$_GET = array(
			'company'	=>	'caldera',
			'name'		=>	'Bob Bitmap',
			'count'		=>	$count
		);
		// REFERR
		$_SERVER['HTTP_REFERER'] = 'http://localhost/apath/?country=ZA&location=office';


		$this->assertSame( 'Company is caldera' 			, $magic->do_magic_tag( 'Company is {_GET:company}' ) );
		$this->assertSame( 'Name is Bob Bitmap' 			, $magic->do_magic_tag( 'Name is {_GET:name}' ) );
		$this->assertSame( 'Total count is ' . $count 		, $magic->do_magic_tag( 'Total count is {_GET:count}' ) );
		$this->assertSame( 'location is office' 			, $magic->do_magic_tag( 'location is {_GET:location}' ) );
		$this->assertSame( 'country is ZA' 					, $magic->do_magic_tag( 'country is {_GET:country}' ) );
		
		$this->assertSame( 'cant find {fake:name} sorry' 	, $magic->do_magic_tag( 'cant find {fake:name} sorry' ) );



	}

	public function test_post_var() {
		
		$magic = new calderawp\filter\magictag();
		$count = rand(0, 999);
		$_POST = array(
			'company'	=>	'caldera',
			'name'		=>	'Bob Bitmap',
			'count'		=>	$count
		);

		$this->assertSame( 'Company is caldera' 			, $magic->do_magic_tag( 'Company is {_POST:company}' ) );
		$this->assertSame( 'Name is Bob Bitmap' 			, $magic->do_magic_tag( 'Name is {_POST:name}' ) );
		$this->assertSame( 'Total count is ' . $count 		, $magic->do_magic_tag( 'Total count is {_POST:count}' ) );

	}

	public function test_request_var() {
		
		$magic = new calderawp\filter\magictag();
		$count = rand(0, 999);

		$_REQUEST = array(
			'company'	=>	'caldera',
			'name'		=>	'Bob Bitmap',
		);

		$this->assertSame( 'Company is caldera' 			, $magic->do_magic_tag( 'Company is {_REQUEST:company}' ) );
		$this->assertSame( 'Name is Bob Bitmap' 			, $magic->do_magic_tag( 'Name is {_REQUEST:name}' ) );

	}

}