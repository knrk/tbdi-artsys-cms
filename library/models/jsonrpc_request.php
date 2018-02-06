<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_JSONRPC_Request implements JsonSerializable {
	
	/**
	 *	Request count (used as "auto" id)
	 * 
	 *	@static
	 *	@access protected
	 *	@var int
	 */
	static protected $_count = 0;
	
	/**
	 *	Request ID (not available for notifications)
	 * 
	 *	@access protected
	 *	@var int
	 */
	protected $_id;
	
	/**
	 *	Request method name
	 * 
	 *	@access protected
	 *	@var string
	 */
	protected $_method;
	
	/**
	 *	Request params data (mostly array)
	 * 
	 *	@access protected
	 *	@var mixed
	 */
	protected $_params;
	
	/**
	 *	If true, ID will not be encoded (even if is set)
	 * 
	 *	@var bool
	 */
	protected $_is_notification = false;
	
	/**
	 *	Error code
	 * 
	 *	@var int
	 */
	protected $_error_code = 0;
	
	/**
	 *	Error codes (codes from -32000 to -32099 are reserved for others)
	 * 
	 *	@static
	 *	@var array 
	 */
	static $errors = array (
			-32600 => 'Invalid Request', 
			-32601 => 'Method not found', 
			-32602 => 'Invalid params', 
			-32603 => 'Internal error', 
			-32700 => 'Parse error'
	);	
	
	/**
	 *	JSON RPC version
	 */
	const VERSION = '2.0';
	
	/**
	 *	Error - invalid request data
	 */
	const ERROR_INVALID_REQUEST = -32600;
	
	/**
	 *	Error - unknown method requested
	 */
	const ERROR_METHOD_NOT_FOUND = -32601;
	
	/**
	 *	Error - invalid params supplied
	 */
	const ERROR_INVALID_PARAMS = -32602;
	
	/**
	 *	Error - internal error
	 */
	const ERROR_INTERNAL_ERROR = -32603;
	
	/**
	 *	Error - recieved data format not supported
	 */
	const ERROR_PARSE_ERROR = -32700;
	
	
	/**
	 *	Get request method
	 * 
	 *	@return string
	 */
	function getMethod()
	{
		return $this->_method;
	}
	
	
	/**
	 *	Get request data
	 * 
	 *	@return array
	 */
	function getData()
	{
		return $this->_params;
	}
	
	
	/**
	 *	Get request ID
	 * 
	 *	@return int
	 */
	function getId()
	{
		return $this->_id;
	}
	
	
	/**
	 *	Get request error code
	 * 
	 *	@return int
	 */
	function getErrorCode()
	{
		return $this->_error_code;
	}
	
	
	/**
	 *	Returns true if this request is notification
	 * 
	 *	@return bool
	 */
	function isNotification()
	{
		return $this->_is_notification;
	}
	
	
	/**
	 *	Returns true if request has valid syntax
	 * 
	 *	@return bool
	 */
	function isValid()
	{
		return 0 === $this->_error_code;
	}
	
	
	/**
	 *	Create new JSON RPC 2 request
	 * 
	 *	@param string $method [optional]
	 *	@param bool $is_notification [optional]
	 *	@param bool $auto_set_id [optional]
	 */
	function __construct( $method = NULL, $is_notification = false, $auto_set_id = true )
	{	
		static::$_count++;
		
		if( NULL !== $method )
		{
			$this->setMethod( $method );
		}
		
		if( $is_notification )
		{
			$this->_is_notification = true;
		}
		elseif( $auto_set_id )
		{
			$this->setId( static::$_count );
		}
	}
	
	
	/**
	 *	Set request ID (unique for script run)
	 * 
	 *	@param int $id
	 *	@return this
	 */
	function setId( $id )
	{
		if( !$this->isNotification() )
		{
			$this->_id = $id;
		}
		
		return $this;
	}
	
	
	/**
	 *	Set request method
	 * 
	 *	@param string $method
	 *	@return this
	 */
	function setMethod( $method )
	{
		$this->_method = $method;
		
		return $this;
	}
	
	
	/**
	 *	Set if this request will be sent as notification
	 * 
	 *	@param bool $is_notification
	 *	@return this
	 */
	function setAsNotification( $is_notification )
	{
		$this->_is_notification = (bool)$is_notification;
		if( $is_notification )
		{
			$this->_id = NULL;
		}
		
		return $this;
	}
	
	
	/**
	 *	Set data
	 * 
	 *	@param mixed $data
	 *	@return this
	 */
	function setData( $data )
	{
		$this->_params = $data;
		
		return $this;
	}
	
	
	/**
	 *	Adds request data field
	 * 
	 *	@param string $name
	 *	@param mixed $value
	 *	@return this
	 */
	function addData( $name, $value )
	{
		if( !is_array($this->_params) )
		{
			$this->_params = array();
		}
		
		$this->_params[$name] = $value;
		
		return $this;
	}
	
	
	/**
	 *	Set request data fields from array
	 * 
	 *	@param array $array
	 *	@return this
	 */
	function addDataArray( array $array )
	{
		foreach($array AS $key => &$value)
		{
			$this->addData($key, $value);
		}
		
		return $this;
	}
	
	
	/**
	 *	Return data that will be encoded
	 * 
	 *	@return array
	 */
	function &getDataToEncode()
	{
		$data = array(
			'jsonrpc' => static::VERSION,
			'method' => $this->_method, 
			'params' => $this->_params);
		
		if( !$this->isNotification() )
		{
			$data['id'] = $this->_id;
		}
		
		return $data;
	}
	
	
	/**
	 *	JSON encode this request
	 * 
	 *	@return string JSON
	 */
	function encode()
	{
		return json_encode( $this );
	}
	
	
	/**
	 *	Implementing jsonSearilaze will allow json_encode() over this instance
	 * 
	 *	@return array
	 */
	function jsonSerialize()
	{
		return $this->getDataToEncode();
	}
	
	
	/**
	 *	Validate structure of request data with given structure
	 *	If structure is invalid, an params error is set in request
	 *	Data types are - string|number|bool|array
	 * 
	 *	A "!" modifier can be used to test for not empty value (eg. "!string" means, that a string must be not empty)
	 *	A "?" modifier can be used for optional values (eg. "?string" means, that if value is set, it must be a string)
	 * 
	 *	Modifiers can be used alone - "?" means optional value, "!" means required value (regardless its type)
	 * 
	 *	Rules can be nested in array (this function is recursive)
	 * 
	 *	@param string|array $structure
	 *	@return bool
	 */
	function validateStructure( $structure )
	{
		if( $this->hasValidStructure($structure) )
		{
			return true;
		}
		else
		{
			$this->_error_code = static::ERROR_INVALID_PARAMS;
			return false;
		}
	}
	
	
	/**
	 *	Returns true if data structure complies with given structure
	 *	Data types are - string|number|bool|array
	 * 
	 *	A "!" modifier can be used to test for not empty value (eg. "!string" means, that a string must be not empty)
	 *	A "?" modifier can be used for optional values (eg. "?string" means, that if value is set, it must be a string)
	 * 
	 *	Modifiers can be used alone - "?" means optional value, "!" means required value (regardless its type)
	 * 
	 *	Rules can be nested in array (this function is recursive)
	 * 
	 *	@param string|array $structure
	 *	@return bool
	 */
	function hasValidStructure( $structure )
	{
		return static::hasValidStructurePart($structure, $this->getData());
	}
	
	
	/**
	 *	Returns true if data structure complies with given structure
	 *	Data types are - string|number|bool|array
	 * 
	 *	A "!" modifier can be used to test for not empty value (eg. "!string" means, that a string must be not empty)
	 *	A "?" modifier can be used for optional values (eg. "?string" means, that if value is set, it must be a string)
	 * 
	 *	Modifiers can be used alone - "?" means optional value, "!" means required value (regardless its type)
	 * 
	 *	Rules can be nested in array (this function is recursive)
	 * 
	 *	@static
	 *	@param string|array $structure
	 *	@param mixed $data
	 *	@return bool
	 */
	static function hasValidStructurePart( $structure, &$data )
	{
		if( is_string($structure) )
		{
			if( empty($structure) )
			{
				if( !isset($data) )
				{
					return false;
				}
			}
			else
			{
				//If field is required and not set - return false
				if( '!' == $structure[0] )
				{
					if( empty($data) )
					{
						return false;
					}

					$structure = substr($structure, 1);
				}
				//If field is optional and is not set - continue
				elseif( '?' == $structure[0] )
				{
					if( empty($data) )
					{
						return true;
					}

					$structure = substr($structure, 1);
				}
				elseif( !isset($data) )
				{
					return false;
				}

				switch( $structure )
				{
					case 'string':
						if( !is_string($data) || is_numeric($data) )
						{
							return false;
						}
						break;
					case 'number':
						if( !is_numeric($data) )
						{
							return false;
						}
						break;
					case 'bool':
						if( !is_bool($data) )
						{
							return false;
						}
						break;
					case 'array':
						if( !is_array($data) )
						{
							return false;
						}
						break;
				}
			}
		}
		elseif( is_array($structure) )
		{
			foreach($structure AS $name => $type)
			{
				if( !is_array($data) )
				{
					return false;
				}
				elseif( !static::hasValidStructurePart($type, $data[$name]) )
				{
					return false;
				}
			}
		}
		
		return true;
	}
		
	
	/**
	 *	Create new instance from external data
	 * 
	 *	@param string $data
	 *	@return static
	 */
	static function fromArray( $data )
	{
		$request = new static(NULL, false, false);
		
		if( isset($data['jsonrpc']) && static::VERSION == $data['jsonrpc'] && isset($data['method']) && is_string($data['method']) )
		{
			if( isset($data['id']) )
			{
				$request->setId($data['id']);				
			}
			else
			{
				$request->setAsNotification(true);
			}
			
			$request->setMethod($data['method']);
			
			if( isset($data['params']) )
			{
				$request->setData($data['params']);
			}
		}
		else
		{
			if( !isset($data['id']) )
			{
				$request->setAsNotification(true);
			}
			
			$request->_error_code = static::ERROR_INVALID_REQUEST;
		}
		
		return $request;
	}
	
	
	/**
	 *	Decode request from JSON
	 * 
	 *	@param string $json
	 *	@return static
	 */
	static function fromJSON( $json )
	{
		$decoded = NULL;
		
		if( is_string($json) )
		{
			$decoded = json_decode( $json, true );
		}
		elseif( is_array($json) )
		{
			$decoded = $json;
		}
		
		if( NULL !== $decoded )
		{
			return static::fromArray($decoded);
		}
		else
		{
			$inst = new static(NULL, false, false);
			$inst->_error_code = static::ERROR_INVALID_REQUEST;
			return $inst;
		}
	}
	
	
	/**
	 *	Create response from request
	 * 
	 *	@return Art_Model_JSONRPC_Response
	 */
	function createResponse()
	{
		$response = new Art_Model_JSONRPC_Response;
		$response->setId($this->getId());
		
		if( !$this->isValid() )
		{
			$response->setError($this->_error_code);
		}

		return $response;
	}
}