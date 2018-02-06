<?php
/**                                                                                 
 *         d88888888888b.88888888888 .d8888b.Y88b   d88P .d8888b. 
 *        d88888888   Y88b   888    d88P  Y88bY88b d88P d88P  Y88b
 *       d88P888888    888   888    Y88b.      Y88o88P  Y88b.     
 *      d88P 888888   d88P   888     "Y888b.    Y888P    "Y888b.  
 *     d88P  8888888888P"    888        "Y88b.   888        "Y88b.
 *    d88P   888888 T88b     888          "888   888          "888
 *   d8888888888888  T88b    888    Y88b  d88P   888    Y88b  d88P
 *  d88P     888888   T88b   888     "Y8888P"    888     "Y8888P" 
 *
 *  @version    1.6.1
 *	@author		Robin Zoň		<zon@itart.cz>
 *  @author		Jakub Pastuszek	<pastuszek@itart.cz>
 *	@author		Tomáš Šujan		<sujan@itart.cz>
 *  @copyright  Copyright (c) 2015 IT ART
 *  @license    https://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3.0
 */
/** ArtSys system version */
define('ART_VERSION', '1.6.1');

/** Global error log */
define('ART_ERROR_LOG', 'logs/error.log');

/** Debug mode */
define('ART_DEBUG', true);

//Check PHP version
if( version_compare(PHP_VERSION, '5.6') === -1 )
{
	exit('Error: ArtSys is not compatible with PHP '.PHP_VERSION);
}

//Use only HTTP cookies
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

//Display all errors, log to our log
ini_set('error_log', ART_ERROR_LOG);
ini_set('display_errors', 1);
error_reporting(E_ALL);



//Include functions
require('library/functions.php');

//Include Main Class
require('library/main.php');

//Execute site
Art_Main::execute();