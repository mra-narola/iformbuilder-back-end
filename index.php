<?php

include_once 'helpers.php';

$post_body = file_get_contents('php://input');
$post_body = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($post_body));
$request_data[] = json_decode($post_body);

$post_data = $request_data[0];

if ( isset($_REQUEST['api']) ) {

	switch ($_REQUEST['api']) {
		case 'get_users':
		case 'add_user':
			include_once './Controllers/UserController.php';
			$user = new UserController;
			$data = $user->call_api( $_REQUEST['api'], $post_data );
			break;
			
		default: 
			$data = [
				'status' => false,
				'message' => 'Request API not found!',
				'data' => array(),
			];
		break;
	}

} else {
	$data = [
		'status' => false,
		'message' => 'Request API not found!',
		'data' => array(),
	];
}

header('Content-type: application/json');
header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
echo json_encode($data);
