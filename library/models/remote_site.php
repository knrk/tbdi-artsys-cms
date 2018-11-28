<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Remote_Site {
	
	/**
	 * @access protected
	 * @var resource
	 */
	protected $_curl_handler;
	
	/**
	 * @access protected
	 * @var string 
	 */
	protected $_url;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $_raw_response = '';
	
	/**
	 * @access protected
	 * @var int
	 */
	protected $_response_code = 0;
	
	/**
	 * @access protected
	 * @var array
	 */
	protected $_header = array();
	
	/**
	 * @access protected
	 * @var array
	 */
	protected $_headers_sent = array();
	
	/**
	 * @access protected
	 * @var string
	 */
	protected $_body = '';
	
	/**
	 * @access protected
	 * @var array Associative array of cookies added by user
	 */
	protected $_cookies = array();
	
	/**
	 * @access protected
	 * @var array
	 */
	protected $_post_data = array();
	
	/**
	 * @access protected
	 * @var bool
	 */
	protected $_verbose = NULL;
	
	/**
	 * @access protected
	 * @static
	 * @var bool
	 */
	protected static $_verbose_static = NULL;
	
	/**
	 * @access protected
	 * @var Art_Model_DOMDocument
	 */
	protected $_dom;
	
	/**
	 *	@access protected
	 *	@var string
	 */
	protected $_cookie_file_path;
	
	/**
	 * @access protected
	 * @var string
	 */
	protected static $_cookie_file_path_static;
	
	/**
	 *	User-agent used for requests
	 */
	const USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:44.0) Gecko/20100101 Firefox/44.0';
	
	
	/**
	 * @param string [optional] $url
	 * @param string [optional] $cookie_file_path
	 */
	function __construct( $url = NULL, $cookie_file_path = NULL ) 
	{	
		if( NULL === $cookie_file_path )
		{
			$cookie_file_path = static::getCookieFilePathStatic();
		}
		
		$this->_curl_handler = curl_init();
		
		curl_setopt_array($this->_curl_handler, array( 
				CURLOPT_HEADER => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLINFO_HEADER_OUT => true,
				CURLOPT_VERBOSE => true,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_USERAGENT => static::USER_AGENT
		));
		
		if( NULL !== $url )
		{
			$this->setURL($url);
		}
		
		//Set cookie file as session cookie file
		$this->setCookieFile( $cookie_file_path );
	}
	
	
	/**
	 * Get request curl handler
	 * 
	 * @return resource
	 */
	function getHandler()
	{
		return $this->_curl_handler;
	}
	
	
	/**
	 * Get path of cookie file jar based on session
	 * 
	 * @static
	 * @return string
	 */
	static function getSessionCookieFilePath()
	{		
		return 'tmp/cookies/'.session_id().'.txt';
	}
	
	
	/**
	 * Get path of cookie file as default for this class
	 * 
	 * @return string
	 */
	static function getCookieFilePathStatic()
	{
		if( NULL === static::$_cookie_file_path_static )
		{
			static::setCookieFilePathStatic( static::getSessionCookieFilePath() );
		}
		
		return static::$_cookie_file_path_static;
	}
	
	
	/**
	 * Flush all session cookies from current jar
	 * 
	 * @static
	 */
	static function flushSessionCookies()
	{
		fopen(static::getCookieFilePath(), 'w');
	}
	
	
	/**
	 * Set verbose output for all requests
	 * 
	 * @param bool $verbose [optional]
	 */
	static function verboseStatic( $verbose = true )
	{
		static::$_verbose_static = (bool)$verbose;
	}
	
	
	/**
	 * Set verbose output for this request
	 * 
	 * @param bool $verbose [optional]
	 */
	function verbose( $verbose = true )
	{
		$this->_verbose = (bool)$verbose;
	}
	
	
	/**
	 * Set URL for request
	 * 
	 * @param string $url
	 */
	function setURL( $url )
	{
		$this->_url = $url;
		curl_setopt($this->_curl_handler, CURLOPT_URL, $url);
	}
	
	
	/**
	 * Set if request should follow header "location"
	 * 
	 * @param bool $bool [optional]
	 * @return this
	 */
	function setFollowRedirects( $bool = true )
	{
		curl_setopt($this->_curl_handler, CURLOPT_FOLLOWLOCATION, $bool);
		
		return $this;
	}
	
	
	/**
	 * Get response code
	 * 
	 * @return int
	 */
	function getResponseCode()
	{
		return $this->_response_code;
	}
	
	
	/**
	 * Get raw response
	 * 
	 * @return string
	 */
	function getRawResponse()
	{
		return $this->_raw_response;
	}
	
	
	/**
	 * Get header by name
	 * 
	 * @param string [optional] $name
	 * @return string|array
	 */
	function getHeader( $name = NULL )
	{
		if( NULL !== $name )
		{
			if( isset($this->_header[$name]) )
			{
				return $this->_header[$name];
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			return $this->_header;
		}
	}
	
	
	/**
	 * Get response body
	 * 
	 * @return string
	 */
	function getBody()
	{
		return $this->_body;
	}
	
	
	/**
	 * Get body as decoded JSON
	 * 
	 * @return stdClass
	 */
	function getBodyJSON()
	{
		return json_decode($this->_body, true);
	}
	
	
	/**
	 * Get response header size
	 * 
	 * @return int
	 */
	function getHeaderSize()
	{
		return curl_getinfo($this->_curl_handler, CURLINFO_HEADER_SIZE);
	}
	
	
	/**
	 * Get "set-cookie" header as array
	 * 
	 * @return array
	 */
	function getCookiesFromHeader()
	{
		$header = $this->getHeader('Set-Cookie');
		
		if( NULL !== $header )
		{
			$output = array();
			$cookies = explode('; ', $header);
			foreach( $cookies AS $cookie )
			{
				$pos = strpos( $cookie, '=');
				if( $pos )
				{
					$name = substr($cookie, 0, $pos);
					$value = substr($cookie, $pos + 1);
					if( !in_array($name, array('expires', 'path')) )
					{
						$output[$name] = $value;
					}
				}
			}
			
			return $output;
		}
		else
		{
			return array();
		}
	}
	
	
	/**
	 * Add cookie to request
	 * 
	 * @param string $name
	 * @param string $value
	 * @return this
	 */
	function addCookie( $name, $value )
	{
		$this->_cookies[$name] = $value;
		
		return $this;
	}
	
	
	/**
	 * Add array of cookies to request
	 * 
	 * @param array $cookies
	 * @return this
	 */
	function addCookieArray( array $cookies )
	{
		foreach( $cookies AS $name => $value )
		{
			$this->_cookies[$name] = $value;
		}
		
		return $this;
	}
	
	
	/**
	 * Add POST field to request
	 * 
	 * @param string $name
	 * @param string $value
	 * @return this
	 */
	function addPOST( $name, $value )
	{
		$this->_post_data[$name] = $value;
		
		return $this;
	}
	
	
	/**
	 * Add POST fields to request
	 * 
	 * @param array $post_data
	 * @return this
	 */
	function addPOSTArray( array $post_data )
	{
		foreach( $post_data AS $name => $value )
		{
			$this->_post_data[$name] = $value;
		}
		
		return $this;
	}
	
	
	/**
	 * Add header to be sent
	 * 
	 * @param string $name
	 * @param string $value
	 * @return this
	 */
	function addHeader( $name, $value )
	{
		$this->_headers_sent[$name] = $value;
		
		return $this;
	}
	
	
	/**
	 * Add headers array to be sent
	 * 
	 * @param array $sent_headers
	 * @return this
	 */
	function addHeaderArray( array $sent_headers )
	{
		foreach( $sent_headers AS $name => $value )
		{
			$this->_headers_sent[$name] = $value;
		}
		
		return $this;
	}
	
	
	/**
	 * Search for login form and return if found
	 * 
	 * @return DOMElement
	 */
	function getLoginForm()
	{
		$this->_domFromBody();
		
		if( NULL !== $this->_dom )
		{
			$forms = $this->_dom->getElementsByTagName('form');
			foreach( $forms AS $form )
			{
				if( strtolower($form->getAttribute('method')) != 'get' )
				{	
					$inputs = $form->getElementsByTagName('input');
					$username_input = false;
					$password_input = false;
					foreach( $inputs AS $input )
					{
						switch( strtolower($input->getAttribute('type')) )
						{
							case 'text':
							case 'email':
								$username_input = true;
								break;
							case 'password':
								$password_input = true;
								break;
						}
					}
					
					if( $username_input && $password_input )
					{
						return $form;
					}
				}
			}
			
			return NULL;
		}
		else
		{
			return NULL;
		}
	}
	
	
	/**
	 * Get POST fields from form
	 * 
	 * @param DOMElement $form
	 * @param string [optional] $username
	 * @param string [optional] $password
	 * @return array
	 */
	static function getFormPostData( DOMElement $form, $username = NULL, $password = NULL )
	{
		$inputs = $form->getElementsByTagName('input');
		
		if( !$username && !$password )
		{
			$output = array();
			foreach( $inputs AS $input )
			{
				$output[$input->getAttribute('name')] = $input->getAttribute('value');
			}
		}
		else
		{
			$output = array();
			foreach( $inputs AS $input )
			{
				if( strtolower($input->getAttribute('type')) == 'text' && $username )
				{
					$output[$input->getAttribute('name')] = $username;
				}
				elseif( strtolower($input->getAttribute('type')) == 'password' && $password )
				{
					$output[$input->getAttribute('name')] = $password;
				}
				else
				{
					$output[$input->getAttribute('name')] = $input->getAttribute('value');
				}
			}
		}
		
		$selects = $form->getElementsByTagName('select');
		foreach( $selects AS $input )
		{
			$output[$input->getAttribute('name')] = '';
		}

		$textareas = $form->getElementsByTagName('textarea');
		foreach( $textareas AS $input )
		{
			$output[$input->getAttribute('name')] = $input->getAttribute('value');
		}	

		return $output;
	}
	
	
	/**
	 * Get form action
	 * 
	 * @param DOMElement $form
	 * @param string [optional] $url
	 * @return string
	 */
	static function getFormAction( DOMElement $form, $url = NULL )
	{
		$action = $form->getAttribute('action');
		
		if( NULL !== $url )
		{
			$parsed_url = parse_url($url);
			$parsed_action = parse_url($action);
			
			//Use scheme, host and query from parsed
			$output = $parsed_url['scheme'].'://'.$parsed_url['host'];
			$output .= $parsed_action['path'];
			if( isset($parsed_action['query']) || isset($parsed_url['query']) )
			{
				$output .= '?';
				if( isset($parsed_action['query']) )
				{
					$output .= $parsed_action['query'];
				}
				else
				{
					$output .= $parsed_url['query'];
				}
			}
			
			return $output;
		}
		else
		{
			return $action;
		}
	}
	
	
	/**
	 * Get current cookie file path
	 * 
	 * @return string
	 */
	function getCookieFilePath()
	{
		return $this->_cookie_file_path;
	}
	
	
	/**
	 * Close curl handle
	 * 
	 * @return this
	 */
	function close()
	{
		curl_close($this->_curl_handler);
		
		return $this;
	}
	
	
	/**
	 * Set curl option
	 * 
	 * @param string $name
	 * @param string $value
	 * @return this
	 */
	function setOption($name, $value)
	{
		curl_setopt($this->_curl_handler, $name, $value);
		
		return $this;
	}
	

	/**
	 * Set curl options
	 * 
	 * @param array $array
	 * $return this
	 */
	function setOptions( array $array )
	{
		curl_setopt_array($this->_curl_handler, $array);
	}
	
	
	/**
	 * Set cookie file for current request
	 * 
	 * @param string $path
	 * @return this
	 */
	function setCookieFile( $path )
	{
		//Create directory if not exists
		$dir = dirname($path);
		if( !file_exists( $dir ) )
		{
			mkdir( $dir, 0755, true );
		}	
		
		$this->_cookie_file_path = $path;
		
		$this->setOptions(array(
			CURLOPT_COOKIEFILE => $path,
			CURLOPT_COOKIEJAR => $path
		));

		return $this;
	}
	
	
	/**
	 * Set default cookie file path for whole class
	 * 
	 * @static
	 * @param string $path
	 */
	static function setCookieFilePathStatic( $path )
	{
		static::$_cookie_file_path_static = $path;
	}
	
	
	/**
	 *	Set POST data as string
	 * 
	 *	@param mixed $post_string
	 *	@return this
	 */
	function setPOSTstring( $post_string )
	{
		if( !is_string($post_string) )
		{
			$post_string = http_build_query($post_string);
		}
		
		curl_setopt($this->_curl_handler, CURLOPT_POST, true);
		curl_setopt($this->_curl_handler, CURLOPT_POSTFIELDS, $post_string);
		
		return $this;
	}
	
	
	/**
	 *	Get response body as DOM
	 * 
	 *	@return Art_Model_DOMDocument
	 */
	function getBodyDOM()
	{
		if( NULL === $this->_dom )
		{
			$this->_domFromBody();
		}
		
		return $this->_dom;
	}
	
	
	/**
	 * Convert body to DOM
	 * 
	 * @access protected
	 * @return Art_Model_DOMDocument
	 */
	protected function _domFromBody()
	{
		$this->_dom = Art_DOM::loadDOMFromString($this->_body);
	}
	
	
	/**
	 * Add POST fields to CURL handler
	 * 
	 * @access protected
	 * @return this
	 */
	protected function _addPOSTDataToCurl()
	{
		if( !empty($this->_post_data) )
		{
			curl_setopt($this->_curl_handler, CURLOPT_POST, true);
			curl_setopt($this->_curl_handler, CURLOPT_POSTFIELDS, $this->_post_data);
		}

		return $this;
	}
	
	
	/**
	 * Add cookies to CURL handler
	 * 
	 * @access protected
	 * @return this
	 */
	protected function _addCookiesToCurl()
	{
		if( !empty($this->_cookies) )
		{			
			$cookies_straight = array();
			foreach($this->_cookies AS $name => $value )
			{
				$cookies_straight[] = $name.'='.$value;				
			}
			
			$cookies = implode('; ',$cookies_straight);
			
			curl_setopt($this->_curl_handler, CURLOPT_COOKIE, $cookies);
		}
		
		return $this;
	}
	
	
	/**
	 * Get response header as associative array
	 * 
	 * @access protected
	 * @return array
	 */
	protected function _getHeaderFromResponse()
	{
		$size = $this->getHeaderSize();
		$response = $this->_raw_response;
		
		$header_lines = explode("\n", substr($response, 0, $size));
		$output = array();
		foreach( $header_lines AS $line )
		{
			$pos = strpos($line, ': ');
			if( $pos )
			{
				$name = substr($line, 0, $pos);
				$value = trim(substr($line, $pos + 2));
				$output[$name] = $value;
			}
			elseif( strpos($line, 'HTTP') === 0 )
			{
				$this->_response_code = substr($line, 9, 3);
			}
		}
		
		return $output;
	}
	
	
	/**
	 * Add headers to be sent with curl
	 * 
	 * @access protected
	 * @return this
	 */
	protected function _addHeadersToCurl()
	{
		$headers = [];
		foreach($this->_headers_sent AS $name => $value)
		{
			$headers[] = $name.': '.$value;
		}
		
		curl_setopt($this->_curl_handler, CURLOPT_HTTPHEADER, $headers);
		
		return $this;
	}
	
	
	/**
	 * Execute request
	 * 
	 * @return this
	 */
	function execute()
	{
		//Add cookies
		$this->_addCookiesToCurl();
		
		//Add post
		$this->_addPOSTDataToCurl();
		
		//Add headers
		$this->_addHeadersToCurl();
		
		//Execute curl
		$this->_raw_response = curl_exec($this->_curl_handler);
		
		//If error
		if( $this->_raw_response === false )
		{
			trigger_error( static::class. ' error: '. curl_error($this->_curl_handler) );
		}
		
		//Get header
		$this->_header = $this->_getHeaderFromResponse();
		
		//Get body
		$this->_body = (string)substr($this->_raw_response, $this->getHeaderSize());
		
		//If verbose
		if( ( NULL === static::$_verbose_static && $this->_verbose ) || 
			( static::$_verbose_static && NULL === $this->_verbose ) || 
			( $this->_verbose && NULL !== static::$_verbose_static ) )
		{
			echo '<hr><h2>Requesting: '.$this->_url.'</h2>';
			echo '<h3>Status: '.($this->getHeader('Status') ? $this->getHeader('Status') : $this->getResponseCode()).'</h3>';
			echo '<h3>Request header</h3>';
			// p(curl_getinfo($this->_curl_handler, CURLINFO_HEADER_OUT ));
			echo '<h3>Response header</h3>';
			// p($this->_header);
			echo '<h3>Response body</h3>';
			// p(htmlentities($this->_body));
		}
		
		//Close handler
		$this->close();
		
		return $this;
	}
}