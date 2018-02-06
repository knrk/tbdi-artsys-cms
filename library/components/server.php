<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Server extends Art_Abstract_Component {
	
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
    
	/**
	 *	@static
	 *	@access protected
	 *	@var string Server protocol (http|https)
	 */
	protected static $_serverProtocol;
    
	/**
	 *	@static
	 *	@access protected
	 *	@var string Relative path to index.php
	 */
	protected static $_relativePath;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Realpath of document root
	 */
	protected static $_document_root;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string True if request is AJAX (cached value)
	 */
	protected static $_is_ajax;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string True if requested as CRON (cached value)
	 */
	protected static $_is_cron;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array List of all HTTP request headers
	 */
	protected static $_request_headers;
	

	/**
	 *	@static
	 *	@return string Server protocol (http|https)
	 */
	static function getServerProtocol()
	{
		return self::$_serverProtocol;
	}    
	
    /**
	 *	@static
	 *	@return string Relative path to index.php
	 */
    static function getRelativePath()
    {
        return self::$_relativePath;
    }
	
	
    /**
     *  Returns user IP address
	 * 
     *  @static
     *  @return string IP Addres
     */
    static public function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
	
	
	/**
	 *	Get host of this site
	 *	
	 *	@static
	 *	@return string
	 */
	static function getHost()
	{
		return self::getServerProtocol().'://'.$_SERVER['HTTP_HOST'];
	}
	
	
	/**
	 *	Get domain
	 * 
	 *	@static
	 *	@return string
	 */
	static function getDomain()
	{
		return $_SERVER['HTTP_HOST'];
	}
	
	
	/**
	 *	Get request content type
	 * 
	 *	@return string
	 */
	static function getContentType()
	{
		if( isset($_SERVER['CONTENT_TYPE']) )
		{
			return $_SERVER['CONTENT_TYPE'];
		}
		else
		{
			return '';
		}
	}
	
	
	/**
	 *	Get document root
	 * 
	 *	@static
	 *	@return string
	 */
	static function getDocumentRoot()
	{
		if( NULL === static::$_document_root )
		{
			static::$_document_root = realpath( $_SERVER['DOCUMENT_ROOT'] );
		}
		
		return static::$_document_root;
	}
	
	
	/**
	 *	Get HTTP request header by name
	 * 
	 *	@param string $name
	 *	@param string $default_value [optional]
	 */
	static function getHeader( $name, $default_value = NULL )
	{
		if( isset(static::$_request_headers[$name]) )
		{
			return static::$_request_headers[$name];
		}
		else
		{
			return $default_value;
		}
	}
		
	
	/**
	 *	@static
	 *	@return bool True if is requested by AJAX
	 */
	static function isAjax()
	{
		if( NULL === static::$_is_ajax )
		{
			if( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
			{
				static::$_is_ajax = true;
			}
			else
			{
				static::$_is_ajax = false;
			}
		}
		
		return static::$_is_ajax;
	}
	
	
	/**
	 *	@static
	 *	@return bool True if requested by CRON
	 */
	static function isCron()
	{
		if( NULL === static::$_is_cron )
		{
			static::$_is_cron = !empty(Art_Router::getFromURI(Art_Cron::CRON_URI_PARAM_NAME));
		}
		
		return static::$_is_cron;
	}
	
	
	/**
	 *	@static
	 *	@return bool True if is POST request
	 */
	static function isPost()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}
	
			
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
            self::_loadServerProtocol();
            self::_loadRelativePath();
			self::_loadRequestHeaders();
        }
    }
	
	
	/**
	 *	Load request HTTP headers
     * 
	 *	@static
	 *	@access protected
	 *	@return void
	 */
	protected static function _loadRequestHeaders()
	{
		//This function may return false on failure
		static::$_request_headers = apache_request_headers();
		if( false === static::$_request_headers )
		{
			static::$_request_headers = array();
		}
	}
	
    
    /**
	 *	Loads server protocol (http|https)
     * 
	 *	@static
	 *	@access protected
	 *	@return void
	 */
	protected static function _loadServerProtocol()
	{
		if( strpos($_SERVER['SERVER_PROTOCOL'], 'HTTPS') === false )
		{
			self::$_serverProtocol = 'http';
		}
		else
		{
			self::$_serverProtocol = 'https';
		}
	}
	
	
    /**
     *  Load relative path to index.php
     *  @static
     *  @access protected
     *  @return void
     */
	protected static function _loadRelativePath()
	{
		//Get index.php path and explode
		self::$_relativePath = explode('/', $_SERVER['SCRIPT_NAME']);
		
		//If is not in root
		if( count(self::$_relativePath) > 2 )
		{
			//Remove last part (/index.php)
			unset(self::$_relativePath[count(self::$_relativePath)-1]);
			self::$_relativePath = implode('/',self::$_relativePath);
		}
		else
		{
            //index.php is in root
			self::$_relativePath = '';
		}
	}	
}