<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_JSONRPC_Client {

	/**
	 *	Requested URL
	 * 
	 *	@var string
	 */
	protected $_url;
	
	/**
	 *	Headers sent with request
	 * 
	 *	@var array
	 */
	protected $_headers = array(
			'Expect'		=> '',
			'Accept'		=> 'application/json',
			'Content-Type'	=> 'application/json'
	);
	
	/**
	 *	CURL options
	 * 
	 *	@var array
	 */
	protected $_curl_options = array();
	
	/**
	 *	List of all queued requests
	 * 
	 *	@var array
	 */
	protected $_requests = array();
	
	/**
	 *	If true, HTTP requests will be shown as verbose
	 * 
	 *	@var bool
	 */
	protected $_verbose = false;
	
	/**
	 *	JSONRPC version number
	 */
	const VERSION = '2.0';
	
	
	/**
	 *	Create new JSON RPC client
	 * 
	 *	@param string $url [optional]
	 */
	function __construct( $url = NULL )
	{
		$this->setUrl($url);
	}
	
	
	/**
	 *	Adds custom header to request
	 * 
	 *	@param string $name
	 *	@param mixed $value
	 *	@return this
	 */
	function addHeader( $name, $value )
	{
		$this->_headers[$name] = $value;
		
		return $this;
	}
	

	/**
	 *	Set requested URL
	 * 
	 *	@param string $url
	 *	@return this
	 */
	function setUrl( $url )
	{
		$this->_url = $url;
		
		return $this;
	}
	
	
	/**
	 *	Set request CURL option
	 * 
	 *	@param string $name
	 *	@param mixed $value
	 *	@return this
	 */
	function setCurlOption( $name, $value )
	{
		$this->_curl_options[$name] = $value;
		
		return $this;
	}
	
	
	/**
	 *	Set authentication header (default: X-Authentication)
	 * 
	 *	@param string $value
	 *	@param string $name [optional]
	 *	@return this
	 */
	function setAuthHeader( $value, $name = 'X-Authentication' )
	{
		return $this->addHeader($name, $value);
	}
	
	
	/**
	 *	Set application header (default: X-Application)
	 * 
	 *	@param string $value
	 *	@param string $name [optional]
	 *	@return this
	 */
	function setAppHeader( $value, $name = 'X-Application' )
	{
		return $this->addHeader($name, $value);
	}
	
	
	/**
	 *	Show verbose HTTP request
	 * 
	 *	@param bool $verbose [optional]
	 *	@return this
	 */
	function verbose( $verbose = true )
	{
		$this->_verbose = (bool)$verbose;
		
		return $this;
	}
	
	
	/**
	 *	Add one request to queue
	 * 
	 *	@param string $method
	 *	@param mixed $data
	 *	@param callable $callback
	 *	@return this
	 */
	function addRequestSimple( $method, $data, callable $callback )
	{
		$request = new Art_Model_JSONRPC_Request( $method );
		$request->setData($data);
		
		return $this->addRequest($request, $callback);
	}
	
	
	/**
	 *	Add one request to queue
	 * 
	 *	@oaram Art_Model_JSONRPC_Request $request
	 *	@param callable $callback
	 *	@return this
	 */
	function addRequest( Art_Model_JSONRPC_Request $request, callable $callback )
	{
		$this->_requests[] = array('request' => $request, 'callback' => $callback);
		
		return $this;		
	}
	
	
	/**
	 *	Execute remote site instance and return response
	 * 
	 *	@param mixed $data
	 *	@return string
	 */
	protected function _remoteCall( $data )
	{
		$remote_site = new Art_Model_Remote_Site($this->_url);
		$remote_site->addHeaderArray($this->_headers);
		$remote_site->setOptions($this->_curl_options);
		$remote_site->setPOSTstring( json_encode($data) );
		$remote_site->verbose($this->_verbose);
		$remote_site->execute();
		
		return $remote_site->getBody();
	}
	
	
	/**
	 *	Call given request and return response
	 * 
	 *	@param Art_Model_JSONRPC_Request $request
	 *	@return Art_Model_JSONRPC_Response
	 */
	function callRequest( Art_Model_JSONRPC_Request $request )
	{
		return Art_Model_JSONRPC_Response::fromJSON( $this->_remoteCall($request) );
	}
	
	
	/**
	 *	Call request and return response
	 * 
	 *	@param string $method
	 *	@param mixed $data
	 *	@return Art_Model_JSONRPC_Response
	 */
	function callRequestSimple( $method, $data )
	{
		$request = new Art_Model_JSONRPC_Request( $method );
		$request->setData($data);
		
		return $this->callRequest($request);
	}
	
	
	/**
	 *	Call given notification request and return response
	 * 
	 *	@param Art_Model_JSONRPC_Request $request
	 *	@return Art_Model_JSONRPC_Response
	 */
	function callNotification( Art_Model_JSONRPC_Request $request )
	{		
		return $this->callRequest($request);
	}
	
	
	/**
	 *	Call notification and return response
	 * 
	 *	@param string $method
	 *	@param mixed $data
	 *	@return Art_Model_JSONRPC_Response
	 */
	function callNotificationSimple( $method, $data )
	{
		$request = new Art_Model_JSONRPC_Request( $method, true );
		$request->setData($data);
		
		return $this->callRequest($request);
	}	
	
	
	/**
	 *	Execute client - use second parameter if server dont support batch requests
	 *	Returns false when server rensponse is invalid
	 * 
	 *	@param bool $as_batch If false, client will send each request separately (it's safer)
	 *	@return Art_Model_JSONRPC_Response[] NULL parse error
	 */
	function execute( $as_batch = true )
	{
		if( $as_batch )
		{
			//Assemble JSON to be sent
			$output = array();
			foreach($this->_requests AS $request) 
			{
				$output[] = $request['request'];
			}
			
			//Call, get response JSON
			$response_body = $this->_remoteCall($output);
			$data = json_decode( $response_body, true );
			
			if( NULL !== $data )
			{
				$responses = array();
				
				//Server respond is batch
				if( array_keys_numeric($data) )
				{
					foreach($data AS $response_ar)
					{
						$responses[] = Art_Model_JSONRPC_Response::fromArray($response_ar);
					}
				}
				else
				{
					$responses[] = Art_Model_JSONRPC_Response::fromArray($data);
				}
				
				foreach($responses AS $response)
				{
					foreach($this->_requests AS $request)
					{
						if( $request['request']->getId() === $response->getId() )
						{
							$request['callback']($response);
						}
					}
				}
				
				return $responses;
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			$responses = array();
			
			foreach($this->_requests AS $request)
			{
				$response = $this->callRequest($request['request']);
				
				$request['callback']($response);
				
				$responses[] = $response;
			}
			
			return $responses;
		}
	}
}
