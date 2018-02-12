<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library
 */



/**
 *	Redirects relatively or absolutely if http:// or https:// if found
 * 
 *	@param string [optional] $path
 *	@return void
 *	@example redirect_to('index.php?lol=foo')
 *	@example redirect_to('http://foo.com')
 */
function redirect_to( $path = '' )
{
	//If HTTP or HTTPS is found
	if( strpos($path, 'http://') !== false || strpos($path, 'https://') !== false || !class_exists('Art_Server') )
	{
		header('location: '.$path);
	}
	else
	{
		header('location: '.Art_Server::getServerProtocol().'://'.$_SERVER['SERVER_NAME'].Art_Server::getRelativePath().'/'.trimForeSlash($path));
	}
	
	exit();
}



/**
 *  print_r() with better design
 * 
 *  @param array|object $data what to show
 *  @return void
 */
function printr($data) 
{
	if( !empty($data) ) 
	{
		//Print clean data if ajax
		if( class_exists('Art_Server') && Art_Server::isAjax() )
		{
			print_r($data);
		}
		else
		{
			echo('<pre>');
				print_r($data);
			echo('</pre>');
		}
	}
	//If data is empty - dump the value
	else
	{
		//Print clean data if ajax
		if( class_exists('Art_Server') && Art_Server::isAjax() )
		{
			var_dump($data);
		}
		else
		{
			echo('<pre>');
				var_dump($data);
			echo('</pre>');
		}
	}
}


/**
 *  print_r() with better design and die
 * 
 *  @param array|object [optional] $data what to show
 *  @return void
 */
function printd( $data = NULL )
{
	printr($data);
	die;
}


/**
 *	Dump (print_d and die)
 * 
 *  @param array|object [optional] $data what to show
 *  @return void
 */
function d( $data = NULL )
{
	printd($data);
}


/**
 *  print_r() with better design
 * 
 *  @param array|object $data what to show
 *  @return void
 */
function p($data)
{
	printr($data);
}


/**
 *	Return if is set
 *	If variable is not set, the default value is returned
 *	Therfore this function is ommiting the "Variable is not defined" error
 * 
 *	@param mixed $variable
 *	@param mixed [optional] $default
 *	@return mixed
 */
function ri( &$variable, $default = '' )
{
	if( isset($variable) )
	{
		return $variable;
	}
	else
	{
		return $default;
	}
}


/**
 *	Return if not empty
 *	If variable is empty, the default value is returned
 * 
 *	@param mixed $variable
 *	@param mixed [optional] $default
 *	@return mixed
 */
function re( &$variable, $default = '' )
{
	if( empty($variable) )
	{
		return $default;
	}
	else
	{
		return $variable;
	}
}


/**
 *	Return if is not null
 *	If variable is null, the default value is returned
 * 
 *	@param mixed $variable
 *	@param mixed [optional] $default
 *	@return mixed
 */
function rn( &$variable, $default = '' )
{
	if( NULL === $variable )
	{
		return $default;
	}
	else
	{
		return $variable;
	}
}


/**
 *  Logs error into error.log
 *  @param string $message what to log
 *  @return void
 */
function log_error($message)
{	
	//Create dir if not exists
	$dir = dirname(ART_ERROR_LOG);
	if( !empty($dir) && !file_exists($dir) )
	{
		mkdir($dir, 0777, true);
	}
	
	//Create and open a file if not exists
	$file = fopen(ART_ERROR_LOG, 'a+');
   
    //If user was initialized
    if( class_exists('Art_User') && Art_User::isInitialized() )
    {
        $user = (Art_User::getId().' - '.Art_User::getRights().' - ');
    }
	else
	{
		$user = '';
	}
            
    //Script path from where error was called
    $errorPath = str_replace($_SERVER['DOCUMENT_ROOT'],'',debug_backtrace()[0]['file']);
    
    //String to be written to file
    $content = (date('Y-m-d H:i:s').' - '.$errorPath.' - '.$_SERVER['REQUEST_URI'].' - '.$_SERVER['REMOTE_ADDR'].' - '.$user.$message)." \n";
    
    //Saving a file
    fwrite($file,$content);
    fclose($file);
}


/**
 *	Test access to file
 *	Usefull for passing into require_once() functions
 *	@param string $path Path to file
 *	@return string|false $path Path to file
 *	@example require_once(ftest('path.jpg')) returns __FILE__ if not found or 'path.jpg' if found
 */
function ftest($path)
{
	if( !is_readable($path) )
	{
		trigger_error('File '.$path.' not found', E_USER_ERROR);
        exit();
	}
	else
	{
		return $path;
	}
}


/**
 *  Saves variable to cookie (mod_rewrite ready)
 *  @param string $name variable name
 *  @param string|int $value variable value
 *  @param int expiration time
 *  @example cookie_set('name','value',1500000) Sets cookie name=value with time expiration till 15000000
 */
function cookie_set($name, $value, $time, $domain = NULL) {
	if (NULL === $domain) {
		foreach(Art_Main::getDomains() AS $domain ) {
			setcookie($name, $value, $time, "/" . Art_Server::getRelativePath(), $domain, false, true);
		}
	} else {
		setcookie($name, $value, $time, "/" . Art_Server::getRelativePath(), $domain, false, true);
	}
	
	$_COOKIE[$name] = $value;
}


/**
 *  Unsets a cookie
 *  @param string $name variable name
 *  @example cookie_unset('foo') Unsets cookie foo
 */
function cookie_unset($name, $domain = NULL) {
	if (NULL === $domain) {
		foreach (Art_Main::getDomains() AS $domain ) {
			setcookie($name, '', 0, "/" . Art_Server::getRelativePath(), $domain, false, true);
		}
	} else {
		setcookie($name, '', 0, "/" . Art_Server::getRelativePath(), $domain, false, true);
	}	
	
	unset($_COOKIE[$name]);
}


/**
 *	Array to object convert 
 *	@param array $input
 *	@return object $output
 *	@example array_to_object(array('foo'=>'1','ofo'=>2)) returns object->foo=1...
 */
function array_to_object(array $input = NULL)
{
	$output = new stdClass();

	if( NULL !== $input )
	{
		//For each array item
		foreach($input AS $key => $value)
		{
			//If item is array - do recursive
			if( is_array($value) )
			{	
				$output->$key = array_to_object($value);
			}
			else
			{
				$output->$key = $value;
			}
		}
	}
	
	return $output;
}


/**
 *	Force associative array items
 * 
 *	@param array $array
 *	@param mixed $default Value for non-associative arrays
 *	@return array
 */
function array_assoc( array $array , $default = array() )
{
	$output = array();
	
	//For each array value
	foreach($array AS $key => $value)
	{
		//If is not associative set default value
		if( is_int($key) )
		{
			$output[$value] = $default;
		}
		else
		{
			$output[$key] = $value;
		}
		
		unset($array[$key]);
	}
	
	return $output;
}


/**
 *	Force indexed array items
 * 
 *	@param array $array
 *	@return array
 */
function array_indexed( array $array )
{
	$output = array();
	
	//For each array value
	foreach($array AS $key => $value)
	{
		//If is not indexed set default value
		if( is_int($key) )
		{
			$output[$key] = $value;
		}
		else
		{
			$output[] = $value;
		}
		
		unset($array[$key]);
	}
	
	return $output;
}


/**
 *	var_dump to string
 *	@param object $object
 *	@return string
 */
function var_dump_str($object)
{
    ob_start();
    var_dump($object);
    $result = ob_get_clean();
	
	//New line remove
	$result = trim(preg_replace('/\s+/', ' ', $result));
	return $result;
}


/**
 *	Email validator
 *	@param string $email
 *	@return bool True if is valid
 */
function is_email($email)
{
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}


/**
 *	Own error handler for PHP errors
 *
 *	@param int $type Error type
 *	@param string $message Error message
 *	@return true
 */
function error_handler($type, $message)
{
	switch($type)
	{
		case E_USER_ERROR:
		case E_ERROR:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_PARSE:
		{
			Art_Main::error($message, Art_Main::ERROR_CRITICAL, true);
			break;
		}
		case E_RECOVERABLE_ERROR:
		case E_WARNING:
		case E_CORE_WARNING:
		case E_COMPILE_WARNING:
		case E_USER_WARNING:
		{	
			Art_Main::error($message, Art_Main::ERROR, true);
			break;
		}
		case E_USER_NOTICE:
		case E_NOTICE:
		case E_USER_DEPRECATED:
		case E_DEPRECATED:
		{
			Art_Main::error($message, Art_Main::ALERT, true);
			break;
		}
	}
	
	return true;
}


/**
 *	Own exceptions handler for PHP exceptions
 *
 *	@param Exception $exception
 *	@return void
 */
function exception_handler($exception)
{
	trigger_error($exception->getMessage(), E_USER_ERROR);
}


/**
 *	Search in array of objects, return all items by their key = value
 *	Optimized for speed
 * 
 *	@param array $array Array of objects or arrays
 *	@param string $key Key to search by
 *	@param string|array $value Values to search by
 *	@return array Array of found items
 */
function search_in_array( array $array, $key, $value )
{
	$output = array();
	
	//Input validation
	if( is_string($key) )
	{
		//If is array of arrays
		if( is_array(reset($array)) )
		{
			//If searching for array of value
			if( is_array($value) )
			{
				foreach($array AS $item)
				{
					if( isset($item[$key]) && in_array($item[$key], $value) )
					{
						$output[] = $item;
					}
				}
			}
			//If searching for one value
			elseif( is_string($value) )
			{
				foreach($array AS $item)
				{
					if( isset($item[$key]) && $item[$key] == $value )
					{
						$output[] = $item;
					}
				}
			}
			else
			{
				trigger_error('Invalid argument supplied for search_in_array()',E_USER_ERROR);
			}
		}
		else
		//Is array of objects
		{
			//If searching for array of values
			if( is_array($value) )
			{
				foreach($array AS $item)
				{
					if( isset($item->$key) && in_array($item->$key, $value) )
					{
						$output[] = $item;
					}
				}
			}
			//If searching for one value
			elseif( is_string($value) )
			{
				foreach($array AS $item)
				{
					if( isset($item->$key) && $item->$key == $value )
					{
						$output[] = $item;
					}
				}
			}
			else
			{
				trigger_error('Invalid argument supplied for search_in_array()',E_USER_ERROR);
			}
		}
	}
	else
	{
		trigger_error('Invalid argument supplied for search_in_array()',E_USER_ERROR);
	}
	
	return $output;
}


/**
 *	Recursively trim values of array using trim()
 * 
 *	@param array $array
 *	@return array
 */
function &array_trim( array $array )
{
	foreach( $array AS &$item )
	{
		switch( gettype($item) )
		{
			case 'array':
				$item = array_trim($item);
				break;
			case 'string':
				$item = trim($item);
				break;
		}
	}
	
	return $array;
}


/**
 *	Returns true if array keys are numeric only
 * 
 *	@param array $array
 *	@return boolean
 */
function array_keys_numeric( array &$array )
{
	foreach( $array AS $key => &$value )
	{
		if( $key !== (int)$key )
		{
			return false;
		}
	}
	
	return true;
}


/**
 *	Returns random aplhanumeric (a-z,0-9) string by given length
 *
 *	@param int [optional] $length
 *	@return string
 */
function rand_str( $length = 32 )
{
	if( $length <= 0 )
	{
		return '';
	}
	else
	{
		$output = substr(md5(rand(100000,999999)),0,$length);
		$output .= rand_str($length - 32);
		return $output;
	}
}


/**
 *	Convert whatever date format to nice european date
 *	
 *	@param string|int $date
 *	@return string
 */
function nice_date($date)
{
	if( is_int($date) )
	{
		return date('j.n.Y', $date);
	}
	else
	{
		return date('j.n.Y', strtotime($date));
	}
}



/**
 *	Convert whatever date format to nice european datetime
 *	
 *	@param string|int $date
 *	@param bool $seconds True to output seconds also
 *	@return string
 */
function nice_datetime($date, $seconds = false)
{
	if( $seconds )
	{
		return date('d.m.Y H:i:s', strtotime($date));
	}
	else
	{
		return date('d.m.Y H:i', strtotime($date));
	}
}


/**
 *	Return date in SQL format for specified or current time
 *	
 *	@param string|int $time [optional]
 *	@return string
 */
function dateSQL( $time = NULL )
{
	if( NULL === $time )
	{
		$time = time();
	}
	
	if( $time === (int)$time )
	{
		return date('Y-m-d H:i:s', $time);
	}
	else
	{
		return date('Y-m-d H:i:s', strtotime($time));
	}
}


/**
 *	Get label by key
 * 
 *	@param string $key
 *	@param string $default
 *	@see Art_Label
 *	@return string
 */
function __($key, $default = NULL)
{
	return Art_Label::get($key, $default);
}


/**
 *	Get label text by key
 * 
 *	@param string $key
 *	@param string $default
 *	@see Art_Label_Text
 *	@return string
 */
function __text($key, $default = NULL)
{
	return Art_Label_Text::get($key, $default);
}


/**
 *	Echo and &lt;br&gt; at once
 * 
 *	@param string|int|double $arg1
 *	@return void
 */
function echoln( $arg1 )
{
	echo $arg1.'<br>';
}


/**
 *	Rewrites string from foo-bar to fooBar
 * 
 *	@param string $string Dashed string
 *	@return string Camel cased string
 */
function dashToCc($string)
{
	//Input validation
	if( is_string($string) )
	{
		$nextUc = false;
		$end = strlen($string);
		//For each character from string
		for($i=0; $i<$end; $i++)
		{
			//If previous char was dash
			if($nextUc)
			{
				//Uppercase this char
				$string[$i] = strtoupper($string[$i]);
				$nextUc = false;
			}
			//If char is dash
			elseif($string[$i] == '-')
			{
				$nextUc = true;
			}
		}

		//Remove all dashes
		return str_replace('-','',$string);
	}
	else
	{
		trigger_error('Invalid argument supplied for dashToCc()',E_USER_ERROR);
	}
}


/**
 *	Rewrites string from foo-bar to foo_bar
 * 
 *	@param string $string Dashed string
 *	@return string Underscored string
 */
function dashToUc($string)
{
	//Input validation
	if( is_string($string) )
	{
		//Remove all dashes
		return str_replace('-','_',$string);
	}
	else
	{
		trigger_error('Invalid argument supplied for dashToCc()',E_USER_ERROR);
	}
}


/**
 *	Recursively remvoe directory and its contents
 * 
 *	@param string $path
 *	@return bool
 */
function rmdirr( $path )
{
	$files = glob($path . '/*');
	
	foreach($files AS $file) 
	{
		is_dir($file) ? rmdirr($file) : unlink($file);
	}
	
	return rmdir($path);
}


/**
 *  Get timestamp of start of this week
 * 
 *	@return int
 */
function this_week()
{
	if( date('w') == 1 )
	{
		return strtotime('today');
	}
	else
	{
		return strtotime('last Monday');
	}
}


/**
 *	Search string in array as case-insensitive
 * 
 *	@param string $needle
 *	@param array $haystack
 *	@return string|int
 */
function array_search_insensitive( $needle, array &$haystack )
{
	$needle = strtolower($needle);
	
	foreach( $haystack AS $key => &$data )
	{
		if( strtolower($data) == $needle )
		{
			return $key;
		}
	}
	
	return NULL;
}


/**
 *	Trim slash from beginning of the string
 * 
 *	@param string $content
 *	@return string
 */
function trimForeSlash( $content )
{
	if( isset($content[0]) && '/' === $content[0] )
	{
		return substr( $content, 1 );
	}
	else
	{
		return $content;
	}
}


/**
 *	Get relative path from realpath (relative to document root)
 *	/var/www/clients/client0/web0/web/library/functions.php => functions.php
 * 
 *	@param string $path
 *	@return string
 */
function relativePath( $path )
{
	return str_replace(Art_Server::getDocumentRoot().'/', '', $path);
}
