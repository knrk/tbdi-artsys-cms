<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Register extends Art_Abstract_Component {
	
	/**
     *  @var bool True if was initialized 
     */
    protected static $_initialized = false;
	
	/**
	 *	@var array
	 */
	protected static $_namespaces = array();

	/**
	 *	Default namespace
	 */
	const DEFAULT_NAMESPACE = '_default';

	/**
	 *	Path to configuration file
	 */
	const CONFIGURATION_INI_PATH = 'configuration.ini';
	
	/**
	 *	Database register namespace
	 */
	const DB_NAMESPACE = 'database';
	
	
	/**
	 *	Initialize the component
	 * 
	 *	@static
	 *	@return void
	 */
	static function init()
	{
		if( parent::init() )
		{
			//Create default namespace
			static::$_namespaces[static::DEFAULT_NAMESPACE] = new Art_Model_Register_Namespace(static::DEFAULT_NAMESPACE);
		}
	}
	
	
	/**
	 *	Set register value in default namespace
	 * 
	 *	@static
	 *	@param string $name
	 *	@param mixed $value
	 *	@param bool $save_to_db
	 */
	static function set( $name, $value, $save_to_db = false )
	{
		return static::in()->set( $name, $value, $save_to_db );
	}
	
	
	/**
	 *	Set array of values (recursive)
	 * 
	 *	@static
	 *	@param array $values
	 *	@param bool $save_to_db
	 */
	static function setFromArray( $values, $save_to_db = false )
	{
		foreach( $values AS $name => $value )
		{
			if( is_array($value) )
			{
				static::in($name)->setFromArray($value, $save_to_db);
			}
			else
			{
				static::set($name, $value, $save_to_db);
			}
		}
	}
	
	
	/**
	 *	Get value from default namespace
	 * 
	 *	@static
	 *	@param string [optional] $name
	 *	@param mixed [optional] $default_value
     *  @param boolean $error_type PHP error type to throw when variable is not found
	 *	@return mixed
	 */
	static function get( $name = NULL, $default_value = NULL, $error_type = E_USER_WARNING )
	{
		return static::in()->get($name, $default_value, $error_type);
	}
	
	
	/**
	 *	Get all namespace names
	 * 
	 *	@static
	 *	@return array
	 */
	static function getNames()
	{
		return array_keys(static::$_namespaces);
	}
	
	
	/**
	 *	Get all namespaces
	 * 
	 *	@static
	 *	@return array
	 */
	static function getAll()
	{
		return static::$_namespaces;
	}
	
	
	/**
	 *	Returns true if register exists in default namespace
	 * 
	 *	@param string $name
	 *	@return bool
	 */
	static function has( $name )
	{
		return static::in()->has( $name );
	}
	
	
	/**
	 *	Returns true if register namespace exists
	 * 
	 *	@param string $name
	 *	@return bool
	 */
	static function hasNamespace( $name )
	{
		return isset(static::$_namespaces[$name]);
	}
	
	
	/**
	 *	Get namespace
	 * 
	 *	@param string [optional] $namespace_name
	 *	@return Art_Model_Register_Namespace
	 */
	static function in( $namespace_name = NULL )
	{
		if( NULL === $namespace_name )
		{
			return static::$_namespaces[static::DEFAULT_NAMESPACE];
		}
		elseif( isset(static::$_namespaces[$namespace_name]) )
		{
			return static::$_namespaces[$namespace_name];
		}
		else
		{
			static::$_namespaces[$namespace_name] = new Art_Model_Register_Namespace($namespace_name);
			
			return static::$_namespaces[$namespace_name];
		}
	}
	
	
	/**
	 *	Load values from database
	 * 
	 *	@static
	 *	@return void
	 */
	static function loadFromDb()
	{
		$regs = Art_Model_Register_Value::fetchAllSimple();
		static::setFromArray($regs);
	}
}