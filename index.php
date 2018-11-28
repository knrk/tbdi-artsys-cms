<?php
// Check PHP version
if (version_compare(PHP_VERSION, '5.6') === -1) {
	exit('Error: not compatible with PHP ' . PHP_VERSION);
}

//Use only HTTP cookies
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Include application configuration
require_once('config.php');

//Include functions
require('library/functions.php');

//Include Main Class
require('library/main.php');

//Execute site
Art_Main::execute();