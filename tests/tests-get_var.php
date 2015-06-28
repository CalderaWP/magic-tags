<?php


/**
 * tests the _GET 
 */
class Tests_The_Magic extends WP_UnitTestCase {

	public function test_get_var() {
		
		$magic = new \calderawp\filter\magictag();
		$count = rand(0, 999);
		// _GETS
		$_GET = array(
			'company'	=>	'caldera',
			'name'		=>	'Bob Bitmap',
			'count'		=>	$count
		);

		$this->assertSame( 'Company is caldera' 					, $magic->do_magic_tag( 'Company is {_GET:company}' ) );
		$this->assertSame( 'Name is Bob Bitmap' 					, $magic->do_magic_tag( 'Name is {_GET:name}' ) );
		$this->assertSame( 'Total count is ' . $count 				, $magic->do_magic_tag( 'Total count is {_GET:count}' ) );
		$this->assertSame( 'home is {_GET:home}' 					, $magic->do_magic_tag( 'home is {_GET:home}' ) );

		// REFERR
		$_SERVER['HTTP_REFERER'] = 'http://localhost/apath/?country=ZA&location=office';
		
		$this->assertSame( 'location is office' 					, $magic->do_magic_tag( 'location is {_GET:location}' ) );
		$this->assertSame( 'country is ZA' 							, $magic->do_magic_tag( 'country is {_GET:country}' ) );
		
		$this->assertSame( 'cant find {_GET:billy} sorry' 			, $magic->do_magic_tag( 'cant find {_GET:billy} sorry' ) );

		$this->assertSame( 'Bob Bitmap works in ZA in his office' 	, $magic->do_magic_tag( '{_GET:name} works in {_GET:country} in his {_GET:location}' ) );


	}

	public function test_post_var() {
		
		$magic = new \calderawp\filter\magictag();
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
		
		$magic = new \calderawp\filter\magictag();
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
		
		$magic = new \calderawp\filter\magictag();

		// not logged in
		$this->assertSame( 'user name is {user:user_login}'	, $magic->do_magic_tag( 'user name is {user:user_login}' ) );
		$this->assertSame( 'no meta {user:stuff}'			, $magic->do_magic_tag( 'no meta {user:stuff}' ) );

		wp_set_current_user( 1 );

		$this->assertSame( 'user name is admin' 			, $magic->do_magic_tag( 'user name is {user:user_login}' ) );
		$this->assertSame( 'welcome 1'			 			, $magic->do_magic_tag( 'welcome {user:show_welcome_panel}' ) );
		$this->assertSame( 'no meta {user:stuff}'			, $magic->do_magic_tag( 'no meta {user:stuff}' ) );


	}

	public function test_post() {
		
		global $post;
		
		$magic = new \calderawp\filter\magictag();
		// get post
		$posts = get_posts( array( 'post_status' => 'publish' ) );

		$this->assertSame( 'post_title is sample post'			, $magic->do_magic_tag( 'post_title is {post:' . $posts[0]->ID . ':post_title}' ) );
		$this->assertSame( 'meta data is sample meta value'		, $magic->do_magic_tag( 'meta data is {post:' . $posts[0]->ID . ':sample_meta}' ) );
		$this->assertSame( 'meta data is private meta value'	, $magic->do_magic_tag( 'meta data is {post:' . $posts[0]->ID . ':_sample_meta}' ) );
		$this->assertSame( 'meta data is value one, value two'	, $magic->do_magic_tag( 'meta data is {post:' . $posts[0]->ID . ':array_meta}' ) );
		$this->assertSame( 'the excerpt is sample content'					, $magic->do_magic_tag( 'the excerpt is {post:' . $posts[0]->ID . ':post_excerpt}' ) );

		// set post global
		$post = $posts[0];

		$this->assertSame( 'post_title is sample post'			, $magic->do_magic_tag( 'post_title is {post:post_title}' ) );
		$this->assertSame( 'meta data is sample meta value'		, $magic->do_magic_tag( 'meta data is {post:sample_meta}' ) );
		$this->assertSame( 'meta data is private meta value'	, $magic->do_magic_tag( 'meta data is {post:_sample_meta}' ) );
		$this->assertSame( 'meta data is value one, value two'	, $magic->do_magic_tag( 'meta data is {post:array_meta}' ) );

		// bad data
		$this->assertSame( 'meta data is {post:no_real}'		, $magic->do_magic_tag( 'meta data is {post:no_real}' ) );
		// no post
		$post = null;
		$this->assertSame( 'title is {post:post_title}'			, $magic->do_magic_tag( 'title is {post:post_title}' ) );
		$this->assertSame( 'meta data is {post:sample_meta}'	, $magic->do_magic_tag( 'meta data is {post:sample_meta}' ) );
		$this->assertSame( 'meta data is {post:_sample_meta}'	, $magic->do_magic_tag( 'meta data is {post:_sample_meta}' ) );
		$this->assertSame( 'meta data is {post:array_meta}'		, $magic->do_magic_tag( 'meta data is {post:array_meta}' ) );

	}

	public function test_ip() {
		
		$magic = new \calderawp\filter\magictag();

		// get IP
		$_SERVER['REMOTE_ADDR'] 	=	'127.0.0.1';
		$this->assertSame( 'Remote IP is 127.0.0.1' 			, $magic->do_magic_tag( 'Remote IP is {ip:address}' ) );

		$_SERVER['HTTP_CLIENT_IP']	=	'10.0.0.1';
		$this->assertSame( 'Client IP is 10.0.0.1' 				, $magic->do_magic_tag( 'Client IP is {ip:address}' ) );

		unset( $_SERVER['HTTP_CLIENT_IP'] );
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.0.1';
		$this->assertSame( 'Forward IP is 192.168.0.1' 			, $magic->do_magic_tag( 'Forward IP is {ip:address}' ) );

	}

	public function test_date() {
		
		$magic = new \calderawp\filter\magictag();

		$date = date('Y m m d d m y YY lR');

		$this->assertSame( $date , $magic->do_magic_tag( '{date:Y m m d d m y YY lR}' ) );

	}

	public function test_general() {
		$magic = new \calderawp\filter\magictag();

		$this->assertSame( '{post}'		, $magic->do_magic_tag( '{post}' ) );
	}

	/**
	 * Test post magic tag with a custom defined excerpt
	 *
	 * @since 1.0.1
	 *
	 */
	public function test_custom_excerpt() {
		global $post;

		//test with no post content
		$data = array(
			'post_excerpt' => 'lorem ipsum'
		);
		$id = wp_insert_post( $data, true );

		$post = get_post( $id );
		$this->assertFalse( is_a( $post, 'WP_Error' ) );
		$magic = new \calderawp\filter\magictag();
		$this->assertSame( 'lorem ipsum', $magic->do_magic_tag( '{post:post_excerpt}' ) );

		//test with post content
		$data = array(
			'post_excerpt' => 'lorem ipsum',
			'post_title'   => 'hats!'
		);
		$id = wp_insert_post( $data, true );

		$post = get_post( $id );
		$this->assertFalse( is_a( $post, 'WP_Error' ) );
		$magic = new \calderawp\filter\magictag();
		$this->assertSame( 'lorem ipsum', $magic->do_magic_tag( '{post:post_excerpt}' ) );

	}

	/**
	 * Test post magic tag with an auto-defined excerpt
	 *
	 * @since 1.0.1
	 *
	 */
	public function test_auto_excerpt() {
		global $post;

		$data = array(
			'post_content' => 'A 1 2 3 4 5 6 7 8 9 B 1 2 3 4 5 6 7 8 9 C 1 2 3 4 5 6 7 8 9 D 1 2 3 4 5 6 7 8 9 E 1 2 3 4 5 6 7 8 9 F 1 2 3 4 NOTINEXCERPT Bad do not want to see this.'
		);
		$id = wp_insert_post( $data, true );

		$post = get_post( $id );
		$this->assertFalse( is_a( $post, 'WP_Error' ) );
		$magic = new \calderawp\filter\magictag();
		$this->assertSame( 'A 1 2 3 4 5 6 7 8 9 B 1 2 3 4 5 6 7 8 9 C 1 2 3 4 5 6 7 8 9 D 1 2 3 4 5 6 7 8 9 E 1 2 3 4 5 6 7 8 9 F 1 2 3 4', $magic->do_magic_tag( '{post:post_excerpt}' ) );


	}

	/**
	 * Test post magic tag with a <!--more--> excerpt
	 *
	 * @since 1.0.1
	 *
	 */
	public function test_more_tag_excerpt() {
		global $post;

		$data = array(
			'post_content' => 'lorem ipsum<!--more-->NOTINEXCERPT'
		);
		$id = wp_insert_post( $data, true );

		$post = get_post( $id );
		$this->assertFalse( is_a( $post, 'WP_Error' ) );
		$magic = new \calderawp\filter\magictag();
		$this->assertSame( 'lorem ipsum', $magic->do_magic_tag( '{post:post_excerpt}' ) );

	}

	/**
	 * Test post:post_thumbnail type magic tags
	 *
	 * @covers \calderawp\filter\magictag::maybe_do_post_thumbnail()
	 *
	 * @since 0.0.1
	 */
	public function test_thumbnail() {
		$data = array(
			'post_title' => 'hats'
		);
		$post_id = wp_insert_post( $data, true );
		$filename = ( __DIR__ . '/data/run-action-banner.png' );
		$contents = file_get_contents( $filename );

		$upload = wp_upload_bits( basename( $filename ), null, $contents );
		$this->assertTrue( empty( $upload['error'] ) );
		$attachment_id = $this->_make_attachment( $upload, $post_id, true );
		$thumb = wp_get_attachment_image_src( $attachment_id );
		$medium = wp_get_attachment_image_src( $attachment_id, 'medium' );

		//test default size and size traversal for a specified post
		$magic = new \calderawp\filter\magictag();
		$this->assertSame( $thumb[0], $magic->do_magic_tag( '{post:' . $post_id . ':post_thumbnail}' ) );
		$this->assertSame( $medium[0], $magic->do_magic_tag( '{post:' . $post_id . ':post_thumbnail.medium}' ) );

		//test default size and size traversal for global $post
		global $post;
		$post = get_post( $post_id );
		$this->assertFalse( is_a( $post, 'WP_Error' ) );
		$this->assertSame( $thumb[0], $magic->do_magic_tag( '{post:post_thumbnail}' ) );
		$this->assertSame( $medium[0], $magic->do_magic_tag( '{post:post_thumbnail.medium}' ) );


	}

	/**
	 * Make an attachment to test with.
	 *
	 * @since 0.0.1
	 *
	 * @param null|string $upload File to make attachment with.
	 * @param int $parent_post_id Optional. Post to associate with.
	 * @param bool $make_featured Optional. If true, the default, image will be set as featured of $parent_post_id
	 *
	 * @return int
	 */
	function _make_attachment( $upload, $parent_post_id = 0, $make_featured = true ) {


		$type = '';
		if ( !empty($upload['type']) ) {
			$type = $upload['type'];
		} else {
			$mime = wp_check_filetype( $upload['file'] );
			if ($mime)
				$type = $mime['type'];
		}

		$attachment = array(
			'post_title' => basename( $upload['file'] ),
			'post_content' => '',
			'post_type' => 'attachment',
			'post_parent' => $parent_post_id,
			'post_mime_type' => $type,
			'guid' => $upload[ 'url' ],
		);

		// Save the data
		$id = wp_insert_attachment( $attachment, $upload[ 'file' ], $parent_post_id );
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );

		if ( $make_featured ) {
			set_post_thumbnail( $parent_post_id, $id );
		}

		return $id;

	}

	/**
	 * Test that permalink magic tags work
	 *
	 * @since 1.2.0
	 *
	 * @covers
	 */
	public function test_permalink() {
		$id = wp_insert_post( array( 'post_title' => 'hats' ) );
		global $post;
		$post = get_post( $id );
		$magic = new \calderawp\filter\magictag();
		$this->assertSame( esc_url( get_permalink( $id ) ), $magic->do_magic_tag( '{post:post_permalink}' ) );
		$this->assertSame( esc_url( get_permalink( $id ) ), $magic->do_magic_tag( '{post:permalink}' ) );

	}

}








