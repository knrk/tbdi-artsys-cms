<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Less extends Art_Abstract_Component {
    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
	
	/**
	 *	@static
	 *	@access protected
	 *  @var Less_Parser Less parser
	 */
	protected static $_parser;
	
	
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
			require(ftest('extensions/lessphp/external/lib/Less/Autoloader.php'));
			Less_Autoloader::register();
		}
    }
	
	
	/**
	 *	Get LESS parser
	 * 
	 *	@static
	 *	@return Less_Parser
	 */
	static function getParser()
	{
		if( NULL === static::$_parser )
		{
			static::init();
			static::$_parser = new Less_Parser();
		}
		
		return static::$_parser;
	}
}