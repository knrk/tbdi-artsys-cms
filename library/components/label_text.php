<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Label_Text extends Art_Abstract_Component {
	use Art_Event_Emitter;
	
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
	
	/**
	 *	@var array All translations for this language
	 */
	protected static $_labels = array();
	
	/**
	 *	@var string Current language 
	 */
	protected static $_current_locale;	
	
	/**
	 *	@var string Default locale
	 */
	protected static $_default_locale;
	
	/**
	 *	@var array List of all locales
	 */
	protected static $_locales = array();	
		
	
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
			//Load current locale
			static::$_current_locale = Art_Main::getLocale();
			
			//Load default locale
			static::$_default_locale = Art_Main::getDefaultLocale();
			
			//Load all locales
			static::$_locales = Art_Main::getLocales();
			
			//Prepare label locale container
			foreach( static::$_locales AS $locale )
			{
				static::$_labels[$locale] = array();
				
				if( !Art_Model_Label::hasCol($locale) )
				{
					trigger_error('Lang "'.$locale.'" not found in database', E_USER_ERROR);
				}
			}

			//Get all label texts from DB and save to this component
			$labels = Art_Model_Label_Text::fetchAll();
			foreach( $labels AS $label ) /* @var $label Art_Model_Label */
			{
				foreach( static::$_locales AS $locale )
				{
					static::$_labels[$locale][$label->key] = $label->{$locale};
				}
			}
			
			//Bind event
			Art_Event::on(Art_Event::LOCALE_CHANGED, function(){
				static::_localeChanged();
			});
		}
	}
	
	
	/**
	 *	Function called on locale_changed event
	 * 
	 *	@access protected
	 *	@static
	 *	@return void
	 */
	protected static function _localeChanged()
	{
		static::$_current_locale = Art_Main::getLocale();
	}
	
	
	/**
	 *	Get label by key
	 * 
	 *	@static
	 *	@param string $key
	 *	@param string [optional] $default
	 *	@param string [optional] $locale
	 *	@return string
	 */
	static function get($key, $default = NULL, $locale = NULL )
	{
		//Use current locale if no is supplied
		if( NULL === $locale )
		{
			$locale = static::$_current_locale;
		}
		
		//If locale is not found
		if( !isset(static::$_labels[$locale]) )
		{
			trigger_error('Locale '.$locale.' not found, label can\'t be returned', E_USER_ERROR);
		}
		
		//If translation is found
		if( isset(static::$_labels[$locale][$key]) )
		{
			return static::$_labels[$locale][$key];
		}
		else
		{
			//If default is set
			if( NULL !== $default )
			{
				return $default;
			}
			else
			{
				return $key;
			}
		}
	}
}