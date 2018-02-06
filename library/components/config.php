<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Config extends Art_Abstract_Component {
    
    /**
     *  @static
     *  @access private
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
    
    /**
     *  @static
     *  @access protected
     *  @var array Array of all variables from configuration file
     */
    protected static $_configuration;
    
	/**
	 *	Configuration.ini file path
	 */
    const CONFIGURATION_INI_PATH = 'configuration.ini';
	
	
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
            //Test the access to file
            if( !is_readable(self::CONFIGURATION_INI_PATH) )
            {
                trigger_error('Configuration file not found', E_USER_ERROR);
            }
            
            //Load configuration file, save to array
            self::$_configuration = parse_ini_file(self::CONFIGURATION_INI_PATH, true);

			if( !isset(self::$_configuration['database']) )
			{
				trigger_error('Configuration file does not contain database section', E_USER_ERROR);
			}
			
			Art_Register::setFromArray(self::$_configuration);
        }
    }
    
    
    /**
     *  Return single variable from configuration file
	 * 
     *  @param string $variable_index Name of the variable stored in config file
     *  @param boolean $error_type PHP error type to throw when variable is not found
     *  @return string
     *  @example get('template') gets template name
     *  @example get('non-existing-variable',Main::ERROR_CRITICAL) Calls error and dies when not found
     */
    static function get($variable_index, $error_type = E_USER_WARNING)
    {
        //Test if was initialized
        if( !static::isInitialized() )
        {
            trigger_error('Configuration class must be initialized first', $error_type);
            return '';
        }
        
        //Test if this variable exists
        if( !isset(self::$_configuration[$variable_index]) )
        {
            trigger_error('Configuration variable - '.$variable_index.' - was not found', $error_type);
            return '';
        }
        
        //Return the value
        return self::$_configuration[$variable_index];
    }
}