<?php
/** ArtSys system version */
define('ART_VERSION', '1.6.1');

/** Global error log */
define('ART_ERROR_LOG', 'logs/error.log');

/** Debug mode */
define('ART_DEBUG', true);

//Check PHP version
if (version_compare(PHP_VERSION, '5.6') === -1) {
	exit('Error: not compatible with PHP ' . PHP_VERSION);
}

//Use only HTTP cookies
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

//Display all errors, log to our log
ini_set('error_log', ART_ERROR_LOG);
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once('config.php');

//Include functions
require('library/functions.php');

//Include Main Class
require('library/main.php');

//Execute site
Art_Main::execute();