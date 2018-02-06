<?php
/**
 *	@abstract
 * 
 *	All inherited method needs to implement static variable
 *	static protected $_instance;
 */
abstract class Singleton 
{
	/**
	 *	@var this This instance
	 */
	static protected $_instance;

	
	/**
	 *	Can't be instantiated from outside
	 */
	private function __construct() {}
	
	
	/**
	 *	@static
	 *	@return this
	 */
	static function getInstance()
	{
		if( NULL === static::$_instance )
		{
			static::$_instance = new static();
		}
		
		return static::$_instance;
	}
}