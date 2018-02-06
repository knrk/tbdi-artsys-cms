<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/abstract
 *	@abstract
 */
abstract class Art_Abstract_Component {
	
    /**
     *  @var bool True if was initialized 
     */
    private static $_initialized = false;
        
	
    /**
	 *	@static
	 *	@final
     *	@return bool True if component was initialized before
     */
    static final function isInitialized()
    {
        return static::$_initialized;
    }
	
	/**
	 *	Components should not be instantiated
	 */
	private function __construct() {}
	
	
    /**
     *  Initialize component
     * 
	 *	@static
     *  @return boolean True if was initialized
     */
    static function init()
    {
        //Can be initialized once only
        if( static::$_initialized )
        {
            return false;
        }
        else
        {
            static::$_initialized = true;
			
			Art_Event::trigger(Art_Event::COMPONENT_INITIALIZE, static::class);
            return true;
        }
    }
}