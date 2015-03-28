<?php

$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['SERVER_NAME'] = '';
$PHP_SELF = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';


$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	//include class
	require dirname( __FILE__ ) . '/../src/magictag.php';	
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';

global $post;

$sample_post = array(
	'post_title'	=>	'sample post',
	'post_content'	=>	'sample content',
	'post_status'	=>	'publish',
	'post_excerpt'	=>	''
);

$post_id = wp_insert_post( $sample_post );
delete_post_meta( $post_id, 'sample_meta' );
delete_post_meta( $post_id, '_sample_meta' );
delete_post_meta( $post_id, 'array_meta' );

add_post_meta( $post_id, 'sample_meta', 'sample meta value' );
add_post_meta( $post_id, '_sample_meta', 'private meta value' );
add_post_meta( $post_id, 'array_meta', array('value one', 'value two') );
