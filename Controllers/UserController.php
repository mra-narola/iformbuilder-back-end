<?php

if ( $_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die(header( 'HTTP/1.0 403 Forbidden', TRUE, 403 ));
}

class UserController {

	public $response = array();

	/**
	 * Call API
	 * @param  string 		$request_api 	Requested API name
	 * @param  array|null 	$post_data   	Post data
	 * @return array
	 * @author MRA
	 */
	public function call_api( $request_api, $post_data ) {

		switch ( $request_api ) {
			case 'get_users':
				if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
					return $this->get_users_list($post_data);
				} else {
					return $this->response = array(
						'status' => false,
						'message' => 'Something went wrong!',
						'data' => array(
							'error' => $_SERVER['REQUEST_METHOD'] . ' request not allowed, supported method is GET.'
						),
					);
				}
				break;

			case 'add_user':
				if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
					return $this->add_user($post_data);
				} else {
					return $this->response = array(
						'status' => false,
						'message' => 'Something went wrong!',
						'data' => array(
							'error' => $_SERVER['REQUEST_METHOD'] . ' request not allowed, supported method is POST.'
						),
					);
				}
				break;

			default:
				$data = array(
					'flag' => false,
					'message' => 'Requested API not found!',
					'data' => array(),
				);
			    return $data;
				break;
		}

	}

	/**
	 * Get users list
	 * @param  array|null $post_data POST data
	 * @return array
	 * @author MRA
	 */
	public function get_users_list( $post_data ) {

		$token = get_access_token();

		if ( !empty( $token ) && !empty( $token['access_token'] ) ) {

			$user_table_fields = user_table_fields();

			# return if the table fields are not define
			if ( empty( $user_table_fields ) ) {
				$this->response = array(
					'status' => false,
					'messages' => 'Table fields are not available',
					'data' => array(),
				);
				return $this->response;
			}

			$fields = implode(',', $user_table_fields);

			$url = API_URL . VERSION . '/profiles/' . PROFILE_ID . '/pages/' . PAGE_ID . '/records?fields=' . $fields . '&limit=' . PAGINATION_PER_PAGE_LIMIT . '&offset=' . PAGINATION_OFFSET . '&subform_order=' . PAGINATION_DEFAULT_ORDER;

			$curl_options = array(
	            CURLOPT_HTTPHEADER => array(
	            	'Authorization: ' . $token['token_type'] .' ' . $token['access_token']
	            )
	        );

			$result = execute_curl_call($url, 'GET', $curl_options);

			if ( isset($result['error_message']) ) {
				$this->response = array(
					'status' => false,
					'message' => 'Something went wrong!',
					'data' => array(
						'error' => $result['error_message'],
					),
				);
			} else {
				$this->response = array(
					'status' => true,
					'message' => 'User fetched successfully',
					'data' => $result,
				);
			}


		} else {
			$this->response = array(
									'status' => false,
									'message' => 'Invalid token',
									'data' => array(),
									);
		}

		return $this->response;
	}

	/**
	 * Add new user
	 * @param array|null $post_data POST data
	 * @author MRA
	 */
	public function add_user( $post_data ) {
		$token = get_access_token();

		if ( empty( $post_data ) ) {
			return $this->response = array(
				'status' => false,
				'message' => 'Required fields are missing',
				'data' => array(),
			);
		}

		if ( !empty($token) && !empty($token['access_token']) ) {

			# return if the table fields are not define
			$user_table_fields = user_table_fields();
			if ( empty($user_table_fields) ) {
				$this->response = array(
					'status' => false,
					'messages' => 'Table fields are not available',
					'data' => array(),
				);
				return $this->response;
			}

			$url = API_URL . VERSION . '/profiles/' . PROFILE_ID . '/pages/' . PAGE_ID . '/records';
			
			$curl_options = array(
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $post_data,
	            CURLOPT_HTTPHEADER => array(
	            	'Content-Type: application/json',
	            	'Authorization: '. $token['token_type'] .' ' . $token['access_token']
	            ),
	        );

			$result = execute_curl_call( $url, 'POST', $curl_options );
			if ( isset( $result['error_message'] ) ) {
				$this->response = array(
					'status' => false,
					'message' => 'Something went wrong!',
					'data' => array(
						'error' => $result['error_message'],
					),
				);
			} else {
				$this->response = array(
					'status' => true,
					'message' => 'User has been added successfully.',
					'data' => $result,
				);
			}

		} else {
			$this->response = array(
				'status' => false,
				'message' => 'Invalid token',
				'data' => array(),
			);
		}

		return $this->response;
	}

}