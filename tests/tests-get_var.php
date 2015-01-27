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


		$this->assertSame( 'Company is caldera' 					, $magic->do_magic_tag( 'Company is {_GET:company}' ) );
		$this->assertSame( 'Name is Bob Bitmap' 					, $magic->do_magic_tag( 'Name is {_GET:name}' ) );
		$this->assertSame( 'Total count is ' . $count 				, $magic->do_magic_tag( 'Total count is {_GET:count}' ) );
		$this->assertSame( 'location is office' 					, $magic->do_magic_tag( 'location is {_GET:location}' ) );
		$this->assertSame( 'country is ZA' 							, $magic->do_magic_tag( 'country is {_GET:country}' ) );
		
		$this->assertSame( 'cant find {_GET:billy} sorry' 			, $magic->do_magic_tag( 'cant find {_GET:billy} sorry' ) );

		$this->assertSame( 'Bob Bitmap works in ZA in his office' 	, $magic->do_magic_tag( '{_GET:name} works in {_GET:country} in his {_GET:location}' ) );


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

		$this->assertSame( 'cant find {_POST:billy} sorry' 	, $magic->do_magic_tag( 'cant find {_POST:billy} sorry' ) );

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

		$this->assertSame( 'cant find {_REQUEST:billy} sorry' 	, $magic->do_magic_tag( 'cant find {_REQUEST:billy} sorry' ) );
	}


	public function test_user() {
		
		$magic = new calderawp\filter\magictag();
		wp_set_current_user( 1 );

		$this->assertSame( 'user name is admin' 			, $magic->do_magic_tag( 'user name is {user:user_login}' ) );
		$this->assertSame( 'welcome 1'			 			, $magic->do_magic_tag( 'welcome {user:show_welcome_panel}' ) );
		$this->assertSame( 'no meta {user:stuff}'			, $magic->do_magic_tag( 'no meta {user:stuff}' ) );


	}

	public function test_ip() {
		
		$magic = new calderawp\filter\magictag();

		// get IP
		$_SERVER['REMOTE_ADDR'] 	=	'127.0.0.1';
		$this->assertSame( 'Remote IP is 127.0.0.1' 			, $magic->do_magic_tag( 'Remote IP is {ip:address}' ) );

		$_SERVER['HTTP_CLIENT_IP']	=	'10.0.0.1';
		$this->assertSame( 'Client IP is 10.0.0.1' 			, $magic->do_magic_tag( 'Client IP is {ip:address}' ) );

		unset( $_SERVER['HTTP_CLIENT_IP'] );
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.0.1';
		$this->assertSame( 'Forward IP is 192.168.0.1' 			, $magic->do_magic_tag( 'Forward IP is {ip:address}' ) );



	}

}