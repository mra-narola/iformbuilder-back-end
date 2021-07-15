<?php

if ( $_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']) ) {
    die(header( 'HTTP/1.0 403 Forbidden', TRUE, 403 ));
}

include_once 'constants.php';

if (!function_exists('get_access_token')) {

	/**
	 * Generate new access token for iformbuilder
	 * @return array
	 * @author MRA
	 */
	function get_access_token() {
		$url = API_URL . 'oauth/token';
		$iat = time();
		$exp = time() + 300;
		$header = json_encode(array(
			'typ' => 'JWT', 'alg' => 'HS256'
		));

		$base64_url_header = base64_encode($header);

		$payload_array = array(
		    'iss' => CLIENT_KEY,
		    'aud' => $url,
		    'exp' => $exp,
		    'iat' => $iat
		);

		$payload = json_encode($payload_array);
		$base64_url_payload = base64_encode($payload);

		$signature = hash_hmac('sha256', "$base64_url_header.$base64_url_payload", CLIENT_SECRET, true);
		$base64_url_signature = str_replace(
			array('+', '/', '='),
			array('-', '_', ''),
			base64_encode($signature)
		);

		$assertion = "$base64_url_header.$base64_url_payload.$base64_url_signature";


		$curl_options = array(
		  	CURLOPT_POSTFIELDS => "assertion=$assertion&grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Ajwt-bearer",
		  	CURLOPT_HTTPHEADER => array(
		    	'Content-Type: application/x-www-form-urlencoded',
		    	'Accept: application/json',
		    	'Cookie: X-Mapping-fjhppofk=42CF71E3F1D781C40631C31B440C7D99'
		  	),
		);

		return execute_curl_call( $url, 'POST', $curl_options );
	}
}


if (!function_exists('execute_curl_call')) {

	/**
	 * Execute cURL call
	 * @param  string $url          cURL request URL
	 * @param  string $method       Request method [ GET|POST ]
	 * @param  array  $curl_options extra cURL options
	 * @return array
	 * @author MRA
	 */
	function execute_curl_call( $url, $method = 'GET', $curl_options = [] ) {
		if ( empty( $url ) ) {
			return array(
				'status' => false,
				'message' => 'URL endpoint is missing',
				'data' => array(),
			);
		}

	    $curl = curl_init();
	    $curl_config_options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 50,
            CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
        );

        $curl_config_options = $curl_config_options + $curl_options;
        curl_setopt_array($curl, $curl_config_options);
	    $response = curl_exec($curl);
	    
	    curl_close($curl);

	    return json_decode($response, true);
	}
}