<?php
/**
 * Magic Tags main class
 *
 * @package caldera\filter
 * @author    David Cramer <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer
 */

namespace calderawp\filter;

/**
 * Class magictag
 *
 * @package caldera\filter
 */
class magictag {

	/**
	 * Constructor for class. Sets up the default filters.
	 */
	function __construct() {

		// GET
		add_filter( 'caldera_magic_tag-_GET', array( $this, 'filter_get_var') );
		// POST
		add_filter( 'caldera_magic_tag-_POST', array( $this, 'filter_post_var') );
		// REQUEST
		add_filter( 'caldera_magic_tag-_REQUEST', array( $this, 'filter_request_var') );
		// post
		add_filter( 'caldera_magic_tag-post', array( $this, 'filter_post') );
		// user
		add_filter( 'caldera_magic_tag-user', array( $this, 'filter_user') );
		// date
		add_filter( 'caldera_magic_tag-date', array( $this, 'filter_date') );
		// date
		add_filter( 'caldera_magic_tag-ip', array( $this, 'filter_ip') );
	
	}

	/**
	 * Renders a magic tag
	 *
	 * @return    string 	converted string with matched tags replaced
	 */
	static public function do_magic_tag($content){

		// check for magics
		preg_match_all("/\{(.+?)\}/", (string) $content, $magics);
		
		// on found tags
		if(!empty($magics[1])){
			foreach($magics[1] as $magic_key=>$magic_tag){

				$params = explode(':', $magic_tag );

				// create the base for arguments to be added as second filter arg
				$magic_tag_base = array_shift( $params );
				// filter the matched tag part
				$magic_tag = apply_filters( "caldera_magic_tag-{$magic_tag_base}", $params);

				// filter a general tag using the second argument as the original tag
				$filter_value = apply_filters( 'caldera_magic_tag', $magic_tag, $magics[0][$magic_key]);				

				// chech the tag changed
				if( $filter_value != $magics[1][$magic_key] ){
					// on a difference in the tag, replace it.
					$content = str_replace($magics[0][$magic_key], $filter_value, $content);
				}
			}
		}
		// return content converted or not.
		return $content;
	}

	/**
	 * filters a GET magic tag
	 *
	 * @return    string 	converted tag value
	 */
	static public function filter_get_var( $params ){
		$magic_tag = null;
		if( isset($_GET[$params[0]])){
			$magic_tag = $_GET[$params[0]];
		}
		return $magic_tag;		
	}

	/**
	 * filters a POST magic tag
	 *
	 * @return    string 	converted tag value
	 */
	static public function filter_post_var( $params ){
		$magic_tag = null;
		if( isset($_POST[$params[0]])){
			$magic_tag = $_POST[$params[0]];
		}
		return $magic_tag;		
	}

	/**
	 * filters a REQUEST magic tag
	 *
	 * @return    string 	converted tag value
	 */
	static public function filter_request_var( $params ){
		$magic_tag = null;
		if( isset($_REQUEST[$params[0]])){
			$magic_tag = $_REQUEST[$params[0]];
		}
		return $magic_tag;		
	}

	/**
	 * filters a post magic tag
	 *
	 * @return    string 	converted tag value
	 */
	static public function filter_post( $params ){
		// set default value.
		$magic_tag = null;

		if( isset( $params[1] ) ){
			// a third e.g {post:24:post_title} indicates post ID 24 value post_title
			$post = get_post( $params[0] );
			$field = $params[1];
		}else{
			// stic to current post
			global $post;
			$field = $params[0];
		}

		if(is_object($post)){
			
			if(isset( $post->{$field} )){
			
				return $post->{$field};
			
			}

			// try meta data
			$post_metavalue = get_post_meta( $post->ID, $field );
			if( false !== strpos($field, ',') ){
				$field = explode(',', $field);
			}
			if(!empty($post_metavalue)){

				$outmagic = array();
				foreach ( (array) $field as $requested_field) {
					foreach( (array) $post_metavalue as $meta_field){
						if(isset($meta_field[$subvalue])){
							$outmagic[] = $post_metavalue;
						}												
					}
				}
				if( !empty( $outmagic ) ){
					$magic_tag = implode(', ', $outmagic);
				}
				
			}
		}
		return $magic_tag;		
	}

	/**
	 * filters a user magic tag
	 *
	 * @return    string 	converted tag value
	 */
	static public function filter_user( $params ){

		if(!is_user_logged_in() || empty( $params[0] ) ){
			return null;
		}
		$user = get_userdata( get_current_user_id() );
		if(isset( $user->data->{$params[0]} )){
			return $user->data->{$params[0]};
		}

		$is_meta = get_user_meta( $user->ID, $params[0], true );
		if( !empty( $is_meta ) ){
			return $is_meta;
		}

		return null;
	}

	/**
	 * filters a date magic tag
	 *
	 * @return    string 	converted tag value
	 */
	static public function filter_date( $params ){
		return date( $params[0] );
	}

	/**
	 * filters a ip magic tag
	 *
	 * @return    string 	converted tag value
	 */
	static public function filter_ip( ){
		return $_SERVER['REMOTE_ADDR'];
	}

} 
