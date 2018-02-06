<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_JSONRPC_Server {

	/**
	 *	Array of methods (server actions)
	 * 
	 *	@var array
	 */
	protected $_methods = array();

	/**
	 *	Array of server requests
	 * 
	 *	@var Art_Model_JSONRPC_Request[]
	 */
	protected $_requests = array();

	/**
	 *	Array of server responses
	 * 
	 *	@var Art_Model_JSONRPC_Response[]
	 */		
	protected $_responses = array();
	
	/**
	 *	Associative array of request headers
	 * 
	 *	@var array
	 */
	protected $_headers = array();
	
	/**
	 *	Associative array of reuired headers
	 * 
	 *	@var array
	 */
	protected $_required_headers = array();

	/**
	 *	Method called when headers dont match
	 * 
	 *	@var callable
	 */
	protected $_invalid_headers_method = NULL;
	
	/**
	 *	If true, request is batch request
	 * 
	 *	@var bool
	 */
	protected $_is_batch = false;

	/**
	 *	JSONRPC version number
	 */
	const VERSION = '2.0';

	/**
	 *	Message sent with permission denied error
	 */
	const PERMISSION_DENIED_MESSAGE = 'Permission denied';

	/**
	 *	X-Application HTTP header name
	 */
	const HEADER_X_APPLICATION = 'X-Application';
	
	/**
	 *	X-Authentication HTTP header name
	 */
	const HEADER_X_AUTHENTICATION = 'X-Authentication';
	
	
	/**
	 *	Returns true if server is requested as batch
	 * 
	 *	@return bool
	 */
	function isBatch()
	{
		return $this->_is_batch;
	}


	/**
	 *	Returns server methods
	 * 
	 *	@return array
	 */
	function getMethods()
	{
		return $this->_methods;
	}


	/**
	 *	Returns all requests for this server
	 * 
	 *	@return Art_Model_JSONRPC_Request[]
	 */
	function getRequests()
	{
		return $this->_requests;
	}


	/**
	 *	Returns all responses this server will send
	 * 
	 *	@return Art_Model_JSONRPC_Response[]
	 */
	function getResponses()
	{
		return $this->_responses;
	}
	
	
	/**
	 *	Get header sent with request
	 * 
	 *	@param string $name
	 *	@return string
	 */
	function getHeader( $name )
	{
		if( isset($this->_headers[$name]) )
		{
			return $this->_headers[$name];
		}
		else
		{
			return NULL;
		}
	}
	
	
	/**
	 *	Get all request headers
	 * 
	 *	@return array
	 */
	function getHeaders()
	{
		return $this->_headers;
	}
	

	/**
	 *	Get authentication header
	 * 
	 *	@param string $name [optional]
	 *	@return string
	 */
	function getAuthHeader( $name = self::HEADER_X_AUTHENTICATION )
	{
		return $this->getHeader( $name );
	}
	
	
	/**
	 *	Get application header
	 * 
	 *	@param string $name [optional]
	 *	@return string
	 */
	function getAppHeader( $name = self::HEADER_X_APPLICATION )
	{
		return $this->getHeader( $name );
	}
	

	/**
	 *	Create new JSON RPC 2 server
	 *	If no param is set, server will use POST data
	 * 
	 *	@param string|array $data [optional] Can be JSON or decoded JSON
	 *	@param array $headers [optional]
	 */
	function __construct( $data = NULL, array $headers = NULL ) 
	{
		//Decode input data
		if( NULL !== $data )
		{
			if( is_string($data) )
			{
				$post = json_decode($data, true);
			}
			else
			{
				$post = $data;
			}
		}
		else
		{
			$post = Art_Main::getPost();
		}
		
		//Get request headers
		if( NULL === $headers )
		{
			$headers = apache_request_headers();
		}
		$this->_headers = $headers;

		//If not parse error
		if( NULL !== $post )
		{
			//Is batch request
			if( array_keys_numeric($post) && !empty($post) )
			{
				$this->_is_batch = true;
				foreach($post AS $item)
				{
					$this->_requests[] = Art_Model_JSONRPC_Request::fromArray($item);
				}
			}
			//Is single request
			else
			{
				$this->_requests[] = Art_Model_JSONRPC_Request::fromArray($post);
			}
		}
		else
		{
			$response = new Art_Model_JSONRPC_Response;
			$response->setError(Art_Model_JSONRPC_Response::ERROR_PARSE_ERROR);
			$this->_responses[] = $response;
		}
	}


	/**
	 *	Associate callback method with request method
	 * 
	 *	@param string $name
	 *	@param callable $callback
	 *	@param string|array $structure_validators [optional] A structure will be validated before calling callback
	 *	@return this
	 */
	function addMethod( $name, callable $callback, $structure_validators = NULL )
	{
		$this->_methods[$name] = array('callback' => $callback, 'validators' => $structure_validators);

		return $this;
	}

	
	/**
	 *	Add required header
	 * 
	 *	@param string $name
	 *	@param string $value
	 *	@return this
	 */
	function addRequiredHeader( $name, $value )
	{
		$this->_required_headers[$name] = $value;
		
		return $this;
	}
	
	
	/**
	 *	Set required authentication header
	 * 
	 *	@param string $value
	 *	@param string $name [optional]
	 *	@return this
	 */
	function setRequiredAuthHeader( $value, $name = self::HEADER_X_AUTHENTICATION )
	{
		return $this->addRequiredHeader($name, $value);
	}
	
	
	/**
	 *	Set required application header
	 * 
	 *	@param string $value
	 *	@param string $name [optional]
	 *	@return this
	 */
	function setRequiredAppHeader( $value, $name = self::HEADER_X_APPLICATION )
	{
		return $this->addRequiredHeader($name, $value);
	}
	
	
	/**
	 *	Method called when request headers does not match required headers
	 *	This method MUST RETURN TRUE, otherwise a default error (permission denied) will be returned
	 * 
	 *	@param callable $callback
	 *	@return this
	 */
	function setInvalidHeadersMethod( callable $callback )
	{
		$this->_invalid_headers_method = $callback;
		
		return $this;
	}
	
	
	/**
	 *	Match loaded request headers with required ones
	 * 
	 *	@return boolean
	 */
	protected function _matchHeaders()
	{
		foreach($this->_required_headers AS $required_name => $required_value)
		{
			if( !isset($this->_headers[$required_name]) || $this->_headers[$required_name] !== $required_value )
			{
				return false;
			}
		}
		
		return true;
	}

	
	/**
	 *	Execute server as permission denied
	 * 
	 *	@return void
	 */
	function executePermissionDenied()
	{
		$response = new Art_Model_JSONRPC_Response;
		
		if( NULL !== $this->_invalid_headers_method )
		{
			$method = $this->_invalid_headers_method;
			if( !$method( $this->_headers, $response ) )
			{
				$response->setError( Art_Model_JSONRPC_Response::ERROR_INVALID_REQUEST, static::PERMISSION_DENIED_MESSAGE );
			}
		}
		else
		{
			$response->setError( Art_Model_JSONRPC_Response::ERROR_INVALID_REQUEST, static::PERMISSION_DENIED_MESSAGE );	
		}
		
		exit( $response->encode() );
	}
	

	/**
	 *	Execute all asociated methods ( and exit with JSON )
	 * 
	 *	@return void
	 */
	function execute( $exit = true )
	{
		//If loaded headers does not match required headers
		if( !$this->_matchHeaders() )
		{
			$this->executePermissionDenied();
		}
		
		foreach($this->_requests AS $request) /* @var $request Art_Model_JSONRPC_Request */
		{			
			$method = $request->getMethod();
			
			//If method was specified
			if( isset($this->_methods[$method]) )
			{
				$request->validateStructure($this->_methods[$method]['validators']);
				
				$response = $request->createResponse();
				
				if( $request->isValid() )
				{
					$this->_methods[$method]['callback']($request, $response, $this->_headers);
				}
				
				if( !$request->isNotification() || !$request->isValid() )
				{
					$this->_responses[] = $response;
				}
			}
			else
			{
				$response = $request->createResponse();
				$this->_responses[] = $response;

				if( !$response->isError() )
				{
					$response->setError(-32601);
				}
			}
		}
		
		$encoded = '';
		
		//If requested as single
		if( !empty($this->_responses) )
		{
			if( $this->_is_batch )
			{
				$encoded = json_encode($this->_responses);
			}
			else
			{
				$encoded = json_encode($this->_responses[0]);
			}
		}
		elseif( empty($this->_requests) )
		{
			$response = new Art_Model_JSONRPC_Response;
			$response->setError(-32600);
			$encoded = json_encode($response);
		}

		if( $exit )
		{
			exit( $encoded );
		}
		else
		{
			return $encoded;
		}
	}
}