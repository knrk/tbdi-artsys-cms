<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Component extends Art_Abstract_Component {
    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
    
    
    /**
	 *	@static
	 *	@access protected
	 *	@var array Array of all loaded components name
	 */
	protected static $_loadedComponents = [];
    
	
	/**
	 *	@static
	 *	@return array Array of all loaded components name
	 */
	static function getLoadedComponents()
	{
		return self::$_loadedComponents;
	}
    
    
	/**
	 *	Initialize components by short name
	 *	
     *  @param string|array $components Component name | array of names
     *  @return void
     *  @static
     *  @example initialize(array('db') Load Art_DB component from library/components/db.php
	 */
	static function initialize( $components )
	{
        //Convert to array
		$components = (array)$components;
        		
		//Init all components
		foreach($components AS $component)
		{
			$component_class = 'Art_'.$component;
			if( method_exists($component_class,'init') )
			{
				$component_class::init();
				self::$_loadedComponents[] = $component;
			}
			else
			{
				trigger_error('Component class '.$component_class.' was not found or has no ::init() function',E_USER_ERROR);
			}
		}
	}
}