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

				$magic = explode(':', $magic_tag );

				// colon split tags using :
				if(count($magic) == 2){
					switch ( $magic[0] ) {
						case '_GET':
							$magic_tag = null;
							if( isset($_GET[$magic[1]])){
								$magic_tag = $_GET[$magic[1]];
							}
							break;
						case '_POST':
							$magic_tag = null;
							if( isset($_POST[$magic[1]])){
								$magic_tag = $_POST[$magic[1]];
							}
							break;
						case '_REQUEST':
							$magic_tag = null;
							if( isset($_REQUEST[$magic[1]])){
								$magic_tag = $_REQUEST[$magic[1]];
							}
							break;
						case 'date':
							$magic_tag = date($magic[1]);
							break;
						case 'user':
							$magic_tag = null;
							if(is_user_logged_in()){
								$user = get_userdata( get_current_user_id() );								
								if(isset( $user->data->{$magic[1]} )){
									$magic_tag = $user->data->{$magic[1]};
								}else{
									if(strtolower($magic[1]) == 'id'){
										$magic_tag = $user->ID;
									}else{										
										$magic_tag = get_user_meta( $user->ID, $magic[1], true );
									}
								}
							}
							break;
						case 'post':
							// set default value.
							$magic_tag = null;

							if( isset( $magic[2] ) ){
								// a third e.g {post:24:post_title} indicates post ID 24 value post_title
								$post = get_post( $magic[1] );
								$field = $magic[2];
							}else{
								// stic to current post
								global $post;
								$field = $magic[1];
							}

							if(is_object($post)){
								if(isset( $post->{$field} )){
								
									$magic_tag = $post->{$field};
								
								}else{
									// extra post data
									switch ($field) {
										case 'permalink':
											$magic_tag = get_permalink( $post->ID );
											break;
										default:											
											$post_metavalue = get_post_meta( $post->ID, $field );
											if( false !== strpos($field, ',') ){
												$field = explode(',', $field);
											}
											if(!empty($post_metavalue)){
												if( !is_array( $field ) ){
													$magic_tag = implode(', ', $post_metavalue);
												}else{
													$outmagic = array();
													foreach ($field as $requested_field) {
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
										break;
									}

								}
							}
							break;
					}

					// create the base for arguments to be added as second filter arg
					$magic_tag_base = array_shift( $magic );
					// filter the matched tag part
					$magic_tag = apply_filters( "caldera_magic_tag-{$magic_tag_base}", $magic_tag, $magic);

				}else{
					// single non split tags
					switch ($magic_tag) {
						case 'ip':
							$magic_tag = $_SERVER['REMOTE_ADDR'];
							break;
					}
					
					// filter for sinlge tag
					$magic_tag = apply_filters( "caldera_magic_tag-{$magic[0]}", $magic_tag);
				}

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
} 
