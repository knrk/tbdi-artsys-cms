<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Ajax extends Art_Abstract_Component {
	    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
	
	/**
	 *	@var string Previous ajax request response status
	 */
	protected static $_response_status = null;
	
	/**
	 *	@var string Previous ajax request response name
	 */
	protected static $_response_name = null;
	
	
    /**
     *  Initialize the component
	 * 
     *  @static
     *  @return void
     */
	static function init()
	{
		if(parent::init())
		{
			//Load previous ajax response name and status
			self::$_response_status = Art_Session::get(self::SESSION_STATUS_NAME);
			self::$_response_name = Art_Session::get(self::SESSION_RESPONSE_NAME);

			//Unset session variables
			Art_Session::remove(self::SESSION_STATUS_NAME);
			Art_Session::remove(self::SESSION_RESPONSE_NAME);
		}
	}
	
	/** Response is not going to be redirected */
	const REDIRECT = 10;
	
	/** Session variable names */
	const SESSION_STATUS_NAME = 'status';
	const SESSION_RESPONSE_NAME = 'response';
	
	/** Variable name of request name */
	const REQUEST_NAME_VAR = '_name';
	
	/**	Name of request unique ID */
	const REQUEST_GUID_VAR = '_guid';
	
	
	/**
	 *	Return new Art_Model_Ajax_Response
	 *	Used to fill with errors and messages to return via AJAX
	 * 
	 *	@static
	 *	@param string $name [optional] Response name
	 *	@return Art_Model_Ajax_Response
	 */
	static function newResponse($name = null)
	{
		return new Art_Model_Ajax_Response($name);
	}
	
	
	/**
	 *	Return new Art_Model_Ajax_Request
	 *	Used to assemble JSON to parse to HTML buttons/hrefs
	 * 
	 *	@static
	 *	@param string $name
	 *	@param string [optional] $action
	 *	@return Art_Model_Ajax_Request
	 */
	static function newRequest( $name, $action = NULL )
	{
		return new Art_Model_Ajax_Request($name, $action);
	}
	
	
	/**
	 *	Get unique request ID
	 * 
	 *	@return string
	 */
	static function getRequestGUID()
	{
		return Art_Main::getPost( self::REQUEST_GUID_VAR );
	}
	
	
	/**
	 *	Return response status of previous ajax request by name
	 * 
	 *	@static
	 *	@param string $name [optional] Response name
	 *	@return string
	 */
	static function isResponseOk($name = null)
	{
		if( NULL === $name )
		{
			return self::$_response_status == Art_Main::OK;
		}
		elseif(self::$_response_name == $name)
		{
			return self::$_response_status == Art_Main::OK;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 *	Return true if site is requested by AJAX request with given name
	 * 
	 *	@static
	 *	@param string $name
	 *	@return boolean
	 */
	static function isRequestedBy( $name )
	{
		if( Art_Server::isAjax() && $name === Art_Main::getPost(self::REQUEST_NAME_VAR) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 *	Get data of AJAX request
	 * 
	 *	@static
	 *	@return array
	 */
	static function getData()
	{
		if( Art_Server::isAjax() )
		{
			$data = array_diff_key(Art_Main::getPost(), array(self::REQUEST_NAME_VAR => NULL, Art_Session::TOKEN_NAME => NULL, self::REQUEST_GUID_VAR => NULL) );
			$data = array_merge($data, Art_Main::getPostFiles());
			return $data;
		}
		else
		{
			return array();
		}
	}
}