<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Minify extends Art_Abstract_Component {
        
    /**
     *  @static
     *  @access private
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
	
	/**
	 *	@static
	 *	@access private
	 *	@var bool True if component is enabled
	 */
	protected static $_enabled = true;
	
	
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
			require_once(ftest('extensions/minify/external/src/Converter.php'));
			require_once(ftest('extensions/minify/external/src/Exception.php'));
			require_once(ftest('extensions/minify/external/src/Minify.php'));
			require_once(ftest('extensions/minify/external/src/CSS.php'));
			require_once(ftest('extensions/minify/external/src/JS.php'));
		}
    }
	
	
	/**
	 *	Disable minifying of input
	 * 
	 *	@static
	 *	@return void
	 */
	static function disable()
	{
		static::$_enabled = false;
	}
	
	
	/**
	 *	Enable minifying of input
	 * 
	 *	@static
	 *	@return void
	 */
	static function enable()
	{
		static::$_enabled = true;
	}
	
	
	/**
	 *	Returns true if minifying is enabled
	 * 
	 *	@static
	 *	@return bool
	 */
	static function isEnabled()
	{
		return static::$_enabled;
	}
	
	
	/**
	 *	Minify CSS code
	 *
	 *	@static
	 *	@param string $script CSS string
	 *	@param bool [optional] $force If true, input will be minified regardless disabling the component
	 *	@return string Minified CSS
	 */
	static function minifyCSS( $script, $force = false )
	{
		if( $force || static::$_enabled )
		{
			static::init();

			$minifier = new MatthiasMullie\Minify\CSS($script);
			return $minifier->minify();	
		}
		else
		{
			return $script;
		}
	}
	
	
	/**
	 *	Minify JS code
	 * 
	 *	@static
	 *	@param string $script JS string
	 *	@param bool [optional] $force If true, input will be minified regardless disabling the component
	 *	@return string Minified JS
	 */
	static function minifyJS( $script, $force = false )
	{
		if( $force || static::$_enabled )
		{
			static::init();

			$minifier = new MatthiasMullie\Minify\JS($script);
			return $minifier->minify();
		}
		else
		{
			return $script;
		}
	}
}