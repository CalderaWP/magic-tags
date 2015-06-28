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

		// post
		add_filter( 'caldera_magic_tag-post', array( $this, 'filter_post') );
		// GET
		add_filter( 'caldera_magic_tag-_GET', array( $this, 'filter_get_var') );
		// POST
		add_filter( 'caldera_magic_tag-_POST', array( $this, 'filter_post_var') );
		// REQUEST
		add_filter( 'caldera_magic_tag-_REQUEST', array( $this, 'filter_request_var') );
		// user
		add_filter( 'caldera_magic_tag-user', array( $this, 'filter_user') );
		// date
		add_filter( 'caldera_magic_tag-date', array( $this, 'filter_date') );
		// ip
		add_filter( 'caldera_magic_tag-ip', array( $this, 'filter_ip') );
		
	}

	/**
	 * Renders a magic tag
	 *
	 * @return    string 	converted string with matched tags replaced
	 */
	public function do_magic_tag($content){

		// check for magics
		preg_match_all("/\{(.+?)\}/", (string) $content, $magics);
		
		// on found tags
		if(!empty($magics[1])){
			foreach($magics[1] as $magic_key=>$magic_tag){
				$params = explode(':', $magic_tag, 2 );
				if( empty( $params[1] ) ){ continue; }
				// filter a general tag using the second argument as the original tag
				$filter_value = apply_filters( 'caldera_magic_tag', apply_filters( "caldera_magic_tag-{$params[0]}", $params[1] ) , $magics[0][$magic_key]);
				// chech the tag changed
				if( $filter_value !== $params[1] ){
					// on a difference in the tag, replace it.
					$content = str_replace( $magics[0][$magic_key], $filter_value, $content );
				}
			}
		}

		// return content converted or not.
		return $content;

	}

	/**
	 * Gets a posts meta value
	 *
	 * @since 0.0.1
	 *
	 * @return    string 	post field value
	 */
	private function get_post_value( $field, $in_params, $post ){

		if( !is_object( $post ) ){
			return $in_params;
		}

		if ( 'permalink' == $field || 'post_permalink' == $field ) {
			return esc_url( get_permalink( $post->ID ) );

		}
		
		//handle auto-generated and <!--more--> tag excerpts @since 1.1.0
		if ( 'post_excerpt' == $field && '' == $post->post_excerpt ) {
			if ( 0 < strpos( $post->post_content, '<!--more-->' ) ){
				$excerpt = substr( $post->post_content, 0, strpos( $post->post_content, '<!--more-->') );
			} else {
				/**
				 * This filer is duplicated from WordPress core to respect core setting for excerpt length.
				 *
				 * It is documented in wp-includes/formatting.php
				 */
				$excerpt_length = apply_filters( 'excerpt_length', 55 );
				$excerpt        = wp_trim_words( $post->post_content, $excerpt_length, '' );
			}

			return $excerpt;

		}

		//possibly do a post_thumbnail magic tag @since 1.1.0
		$maybe_thumbnail = $this->maybe_do_post_thumbnail( $field, $post );
		if ( filter_var( $maybe_thumbnail, FILTER_VALIDATE_URL ) ) {
			return $maybe_thumbnail;

		}

		if( isset( $post->{$field} ) ){
			return implode( ', ', (array) $post->{$field} );
		}


		return $in_params;

	}
	
	/**
	 * Filters a post magic tag
	 *
	 * @since 0.0.1
	 *
	 * @return    string 	converted tag value
	 */
	public function filter_post( $in_params ){

		// check if there is a third
		$params = explode( ':', $in_params );

		if( isset( $params[1] ) ){
			// a third e.g {post:24:post_title} indicates post ID 24 value post_title
			$post = get_post( $params[0] );
			$field = $params[1];
			
		}else{
			// stic to current post
			global $post;
			$field = $params[0];

		}

		// try object	
		return $this->get_post_value( $field, $in_params, $post );

	}
	/**
	 * Filters a GET magic tag
	 *
	 * @since 0.0.1
	 *
	 * @return    string 	converted tag value
	 */
	public function filter_get_var( $params ){

		if( isset($_GET[$params])){
			return wp_slash( $_GET[$params] );
		}elseif( !empty( $_SERVER['HTTP_REFERER'] ) ){
			return $this->filter_get_referr_var( $params );
		}

		return $params;
	}

	/**
	 * Filters a GET from a referrer
	 *
	 * @since 0.0.1
	 *
	 * @return    string 	converted tag value
	 */
	private function filter_get_referr_var( $params ){

		$referer = parse_url( $_SERVER['HTTP_REFERER'] );
		if( !empty( $referer['query'] ) ){
			parse_str( $referer['query'], $get_vars );
			if( isset( $get_vars[$params] ) ){
				return wp_slash( $get_vars[$params] );
			}
		}
		return $params;
	}	

	/**
	 * Filters a POST magic tag
	 *
	 * @since 0.0.1
	 *
	 * @return    string 	converted tag value
	 */
	public function filter_post_var( $params ){

		if( isset($_POST[$params])){
			return wp_slash( $_POST[$params] );
		}

		return $params;		
	}

	/**
	 * Filters a REQUEST magic tag
	 *
	 * @since 0.0.1
	 *
	 * @return    string 	converted tag value
	 */
	public function filter_request_var( $params ){

		if( isset($_REQUEST[$params])){
			return wp_slash( $_REQUEST[$params] );
		}

		return $params;
	}

	/**
	 * Filters a user magic tag
	 *
	 * @since 0.0.1
	 *
	 * @return    string 	converted tag value
	 */
	public function filter_user( $params ){

		if(!is_user_logged_in() ){
			return $params;
		}
		$user = get_userdata( get_current_user_id() );
		if(isset( $user->data->{$params} )){
			$params = $user->data->{$params};
		}

		$is_meta = get_user_meta( $user->ID, $params, true );
		if( !empty( $is_meta ) ){
			$params = implode( ', ', (array) $is_meta );
		}

		return $params;
	}

	/**
	 * Filters a date magic tag
	 *
	 * @since 0.0.1
	 *
	 * @return    string 	converted tag value
	 */
	public function filter_date( $params ){

		return date( $params );

	}

	/**
	 * Filters a ip magic tag
	 *
	 * @since 0.0.1
	 *
	 * @return    string 	converted tag value
	 */
	public function filter_ip( ){
		
		// get IP
		$ip = $_SERVER['REMOTE_ADDR'];
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		return $ip;

	}

	/**
	 * If field is "post_thumbnail" return image source URL
	 *
	 * Supports .size traversal
	 *
	 * @param string $field
	 * @param \WP_Post|object $post Post object
	 *
	 * @since 1.1.0
	 *
	 * @return bool
	 */
	private function maybe_do_post_thumbnail( $field, $post ) {
		if ( 'post_thumbnail' == $field || false !== strpos( $field, 'post_thumbnail.' ) ) {
			$size = 'thumbnail';
			if ( false !== ( $pos = strpos( $field, '.' ) ) ) {
				$size = substr( $field, $pos + 1);
			}

			$id = get_post_thumbnail_id( $post->ID );
			if ( 0 < absint( $id ) ) {
				$img = wp_get_attachment_image_src( $id, $size);
				if ( is_array( $img ) ) {
					return $img[0];

				}

			}

		}

		return false;

	}

} 
