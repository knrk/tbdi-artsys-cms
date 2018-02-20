<?php
/**
 *  @package library/components
 *	@final
 */
final class Art_Label extends Art_Abstract_Component {
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
	static function init() {
		if (parent::init()) {
			//Load current locale
			static::$_current_locale = Art_Main::getLocale();
			
			$labels = self::readLocalization(static::$_current_locale);
			foreach ($labels as $k => $v) {
				static::$_labels[static::$_current_locale][$k] = $v;
			}
			
			//Bind event
			Art_Event::on(Art_Event::LOCALE_CHANGED, function() {
				static::_localeChanged();
			});
		}
	}

	protected static function readLocalization($locale) {		
		$strings = file_get_contents(sprintf("localization/%s.json", $locale));
		if (!$strings) {
			trigger_error(sprintf("Localization file %s.json not found, labels will not be translated.", $locale), E_USER_ERROR);
		}
		$json = json_decode($strings, true);

		return $json['data'];
	}
	
	/**
	 *	Function called on locale_changed event
	 * 
	 *	@access protected
	 *	@static
	 *	@return void
	 */
	protected static function _localeChanged() {
		static::$_current_locale = Art_Main::getLocale();
	}
	
	/**
	 *	Get label by key
	 * 
	 *	@static
	 *	@param string $key
	 *	@param string [optional] $category
	 *	@param string [optional] $locale
	 *	@return string
	 */
	static function get($key, $category = null, $locale = 'cs') {
		//Use current locale if no is supplied
		if (NULL === $locale) {
			$locale = static::$_current_locale;
		}

		//If locale is not found
		if (!isset(static::$_labels[$locale])) {
			trigger_error(sprintf("Locale %s not found, label can't be returned", $locale), E_USER_ERROR);
		}
		
		//If translation is found
		if (isset(static::$_labels[$locale][$key])) {
			return static::$_labels[$locale][$key];
		} else {
			return $key;
		}
	}
	
	/**
	 *	Add array of labels to this component
	 * 
	 *	@static
	 *	@param array $array
	 *	@param string [optional] $locale
	 *	@return void
	 */
	static function addLabelSet(array $array, $locale = 'cs') {
		//Use current locale if no is supplied
		if (NULL === $locale) {
			$locale = static::$_default_locale;
		}
		
		//If locale is found
		if (isset(static::$_labels[$locale])) {
			self::$_labels[$locale] = array_merge(self::$_labels[$locale], $array);
		} else {
			trigger_error(sprintf("Locale %d not found, label can't be added", $locale), E_USER_ERROR);
		}		
	}
	
	
	/**
	 *	Add one label to this component
	 * 
	 *	@static
	 *	@param string $key
	 *	@param string $value
	 *	@param string [optional] $locale
	 *	@return void
	 */
	static function addLabel($key, $value, $locale = NULL) {
		//Use current locale if no is supplied
		if (NULL === $locale) {
			$locale = static::$_default_locale;
		}
		
		//If locale is found
		if (isset(static::$_labels[$locale])) {
			self::$_labels[$locale][$key] = $value;
		} else {
			trigger_error('Locale '.$locale.' not found, label can\'t be added', E_USER_ERROR);
		}
	}
}