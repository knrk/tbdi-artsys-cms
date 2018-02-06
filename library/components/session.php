<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Session extends Art_Abstract_Component {
	    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array All $_SESSION variables
	 */
	protected static $_data;
	
	/**
	 *	Session token name
	 */
	const TOKEN_NAME = 'tk';

	
	/**
	 *	Get one|all session variable
	 * 
	 *	@static
	 *	@param string $identifier
	 *	@param mixed $default_value
	 *	@return string|array
	 */
	static function get($identifier = NULL, $default_value = NULL)
	{
		if( NULL === $identifier )
		{
			return self::$_data;
		}
		else
		{
			if( isset(self::$_data[$identifier]) )
			{
				return self::$_data[$identifier];
			}
			else
			{
				return $default_value;
			}
		}
	}
	
	
	/**
	 *	Return token for AJAX security
	 * 
	 *	@static
	 *	@return string
	 */
	static function getToken()
	{
		return self::get( self::TOKEN_NAME );
	}
	
	
	/**
	 *	Set $_SESSION variable
	 * 
	 *	@param string $name
	 *	@param string $value
	 */
	static function set($name, $value)
	{
		if( !empty($name) )
		{
			$_SESSION[$name] = $value;
			self::$_data[$name] = $value;
		}
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
			session_start();

			//Load all session variables
			self::loadData();
			
			//Crteate token if not exists
			if( NULL === self::getToken() )
			{	
				self::set(self::TOKEN_NAME, rand_str(32));
			}
			
			Art_Main::prependJavaScript('window.token_name="'.self::TOKEN_NAME.'";window.token="'.self::getToken().'";');
		}
	}

	
	/**
	 *	Load $_SESSION and store to static variable
	 *	
	 *	@static
	 *	@access protected
	 *	@return void
	 */
	static protected function loadData()
	{
		self::$_data = $_SESSION;
	}	

	
	/**
	 *	Remove $_SESSION variable
	 * 
	 *	@static
	 *	@access protected
	 *	@param string $name
	 *	@return void
	 */
	static public function remove($name)
	{
		unset($_SESSION[$name]);
		unset(self::$_data[$name]);
	}
}