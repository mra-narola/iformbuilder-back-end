<?php

if ( $_SERVER['REQUEST_METHOD'] == 'GET' && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME']) ) {
    die(header( 'HTTP/1.0 403 Forbidden', TRUE, 403 ));

}

define('API_URL', 'https://app.iformbuilder.com/exzact/api/');
define('VERSION', 'v60');
define('PROFILE_ID', '502831');
define('PAGE_ID', '3838495');
define('CLIENT_KEY', 'b4e86ec70d9c6bd73f007bb5fa6c6f383c7aa7d5');
define('CLIENT_SECRET', '8c4114d9fba6b5e4aace296d9076bfce249e5029');

# PAGINATION CONFIG
define('PAGINATION_PER_PAGE_LIMIT', 100);
define('PAGINATION_OFFSET', 0);
define('PAGINATION_DEFAULT_ORDER', 'desc');



# USER TABLE FIELD LIST
function user_table_fields() {
	return array(
		'first_name',
		'last_name',
		'phone',
	);
}
