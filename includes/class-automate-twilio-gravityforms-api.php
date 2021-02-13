<?php
/**
 * This class handles API call to ActionKit.
 *
 * @package    Gravityforms ActionKit Automation
 * @author     Third Bear Solutions
 */

	class Actionkit_Api {
		
		function __construct( $base_url ) {
			$this->api_url = $base_url . '/rest/v1/';
		}
		
		/**
		 * Make API request.
		 * 
		 * @access public
		 * @param string $path
		 * @param array $options
		 * @param bool $return_status (default: false)
		 * @param string $method (default: 'POST')
		 * @return void
		 */
		function make_request( $path, $options = array(), $method = 'POST' ) {
			
			$request_url = $this->api_url . $path;
						
			/* Execute request based on method. */

			switch ( $method ) {
				
				case 'POST':
					$args = array(
						'body' => $options	
					);
					$response = wp_remote_post( $request_url, $args );
					break;
					
				case 'GET':
					$response = wp_remote_get( $request_url );
					break;
				
			}

			/* If WP_Error, die. Otherwise, return decoded JSON. */
			if ( is_wp_error( $response ) ) {
				
				die( 'Request failed. '. $response->get_error_messages() );
				
			} else {
                $info = array(
                    'response' => json_decode( $response['body'], true ),
                    'url' => $request_url
                );
				return $info;
				
			}
			
		}
		
		/**
		 * Test authenticated API.
		 * 
		 * @access public
		 * @return bool
		 */
		function auth_test() {
			
			// @@TODO
			
		}
		/**
		 * Create an ActionKit action.
		 *
		 * @access public
		 * @return void
		 */
		function act( $payload ) {

			return $this->make_request( 'action/', $payload );

		}
	}
