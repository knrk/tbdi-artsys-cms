<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_JSONRPC_Response implements JsonSerializable {
		
	/**
	 *	Response ID (MUST MATCH REQUEST ID)
	 * 
	 *	@access protected
	 *	@var int
	 */
	protected $_id;		
	
	/**
	 *	Error code
	 * 
	 *	@var int
	 */
	protected $_error_code = 0;
	
	/**
	 *	Error message
	 * 
	 *	@var string
	 */
	protected $_error_message = '';
	
	/**
	 *	Response result data array
	 * 
	 *	@access protected
	 *	@var array
	 */
	protected $_result = array();
	
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
	 *	Get response data
	 * 
	 *	@return array
	 */
	function getData()
	{
		return $this->_result;
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
	 *	Get error code
	 * 
	 *	@return int
	 */
	function getErrorCode()
	{
		return $this->_error_code;
	}
	
	
	/**
	 *	Get error message
	 * 
	 *	@return string
	 */
	function getErrorMessage()
	{
		return $this->_error_message;
	}
	
	
	/**
	 *	Returns true if response is error
	 * 
	 *	@return bool
	 */
	function isError()
	{
		return $this->getErrorCode() !== 0;
	}
	
	
	/**
	 *	Create new JSON RPC 2 response
	 * 
	 *	@param int $id [optional]
	 */
	function __construct( $id = NULL )
	{
		$this->setId($id);
	}
	
	
	/**
	 *	Set response ID
	 * 
	 *	@param int $id
	 *	@return this
	 */
	function setId( $id )
	{
		$this->_id = $id;
		
		return $this;
	}
	
	
	/**
	 *	Add response data field
	 * 
	 *	@param string $name
	 *	@param mixed $value
	 *	@return this
	 */
	function addData( $name, $value )
	{
		if( !is_array($this->_result) )
		{
			$this->_result = array();
		}
		
		$this->_result[$name] = $value;
		
		return $this;
	}
	
	
	/**
	 *	Add response data fields from array
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
	 *	Set data
	 * 
	 *	@param mixed $data
	 *	@return this
	 */
	function setData( $data )
	{
		$this->_result = $data;
		
		return $this;
	}
	
	
	/**
	 *	Set error
	 * 
	 *	@param int $code
	 *	@param string $message [optional]
	 *	@return this
	 */
	function setError( $code, $message = NULL )
	{		
		$this->_error_code = $code;
		
		if( NULL === $message )
		{
			if( isset( static::$errors[$code] ) )
			{
				$this->_error_message = static::$errors[$code];
			}
		}
		else
		{
			$this->_error_message = $message;
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
		$encode = array('jsonrpc' => static::VERSION);
		
		if( $this->isError() )
		{
			$encode['error'] = array('code' => $this->_error_code, 'message' => $this->_error_message);
		}
		else
		{
			$encode['result'] = $this->_result;
		}

		$encode['id'] = $this->_id;
		
		return $encode;
	}
	
	
	/**
	 *	JSON encode this response
	 * 
	 *	@return string JSON
	 */
	function encode()
	{
		return json_encode($this);
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
	 *	Create new instance from data array
	 * 
	 *	@param array $data
	 *	@return static
	 */
	static function fromArray( array $data )
	{
		$request = new static();
		
		if( isset($data['jsonrpc']) && static::VERSION == $data['jsonrpc'] )
		{	
			if( isset($data['id']) )
			{
				$request->setId($data['id']);
			}
			
			if( isset($data['error']) )
			{
				$request->setError($data['error']['code'], $data['error']['message']);
			}
			
			if( isset($data['result']) )
			{
				$request->setData($data['result']);
			}
		}
		
		return $request;
	}
	
	
	/**
	 *	Create new instance with JSON data
	 * 
	 *	@param string $json JSON
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
			$inst = new static();
			$inst->setError( static::ERROR_INVALID_REQUEST );
			return $inst;
		}
	}
}