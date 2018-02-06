<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_Module extends Art_Abstract_Component {
	
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
    
    /**
	 *	@static
	 *	@access protected
	 *	@var array All loaded modules (names)
	 */
    protected static $_loadedModules = [];
    
    /**
	 *	@static
	 *	@access protected
	 *	@var array All modules (instances)
	 */
    protected static $_modules = [];
    
    /**
	 *	@static
	 *	@access protected
	 *	@var array All included modules (names)
	 */
    protected static $_availableModules = array();
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array All module bootstraps 
	 */
	protected static $_bootstraps = array();
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array All modules settings 
	 */
	protected static $_settings = array();
	
	
	/**
	 *	@static
	 *	@access protected
	 *	@var boolt If true, system will exit on errorNoPrivileges and errorNotFound
	 */
	protected static $_exit_on_error = false;
	
	/**
	 *	Number of recreations of content module
	 * 
	 *	@var int
	 */
	protected static $_recreatedCount = 0;
	
	/**
	 *	Limit of content module recreatins
	 */
	const RECREATIONS_LIMIT = 10;
	
	/**
	 *	Modules root folder
	 */
	const ROOT_FOLDER = 'modules';
	
    /**
     *  Default action name
     */
    const ACTION_DEFAULT = 'index';
    
    /**
     *  View autoset constant
     *  if $this->viewName == self::VIEW_AUTOSET then $this->viewName = $this->actionName
     */
    const VIEW_AUTOSET = '%autoset%';
	
	
	/**
	 *	View unset constant
	 */
	const VIEW_UNSET = '%unset%';    
	
	/**
	 *	Module classname prefix
	 */
	const CLASS_PREFIX = 'Module_';
	
	/**
	 *	Module bootstrap classname prefix
	 */
	const BOOTSTRAP_CLASS_PREFIX = 'Module_Bootstrap_';
	
	/**
	 *	Module action postfix
	 */
	const ACTION_POSTFIX = 'Action';
    
	/**
	 *	Module bootstrap file name
	 */
	const BOOTSTRAP_FILENAME = 'bootstrap.php';
    
	const STAGE_LOAD = 1;
	const STAGE_INIT = 2;
	const STAGE_CALL = 3;
	
	/**
	 *	Returns all modules
	 * 
	 *	@static
	 *	@return array Array of all modules
	 */
	static function getModules()
	{
		return self::$_modules;
	}
	
	
	/**
	 *	Return all modules bootstraps
	 * 
	 *	@static
	 *	@return array
	 */
	static function getBootstraps()
	{
		return self::$_bootstraps;
	}
    
	
    /**
     *	Returns all modules by given position
	 * 
     *	@static
     *	@param string [optional] $position Location of modules to be returned. If empty, returns all modules for whole site
     *	@return array
     */
    static function getModulesByPosition( $position = NULL )
    {
        //Input validation
        if( is_string($position) )
        {
			$output = [];

			//Foreach module
			foreach(self::$_modules as $module)
			{
				//If module position equals param position
				if($module->getPosition() == $position)
				{
					$output[] = $module;
				}
			}
			return $output;
        }
		elseif( NULL == $position )
		{
			return self::$_modules;
		}
        else
        {
            trigger_error('Invalid argument supplied for Module::getModulesByPosition()', E_USER_ERROR);
        }
    }


    /**
     *	Returns all modules by given module type (class type)
	 * 
     *	@static
     *	@param string [optional] $type Class names of modules to be returned. If empty, returns all modules for whole site
     *	@return array
     */
    static function getModulesByType( $type = NULL )
    {
        //Input validation
        if( is_string($type) )
        {
			//Normalize type
			$type = Art_Filter::moduleType($type);

			$output = [];
			
			//Foreach module
			foreach(self::$_modules as $module)
			{
				//If types equals
				if($module->getModuleType() == $type)
				{
					$output[] = $module;
				}
			}
			
			return $output;
        }
		elseif( NULL == $type )
		{
			return self::$_modules;			
		}
        else
        {
            trigger_error('Invalid argument supplied for Module::getModulesByType()',E_USER_WARNING);
        }
    }
	
	
	/**
	 *	Get node types relative to user rights
	 * 
	 *	@final
	 *	@static
	 *	@return array
	 */
	final static function getNodeTypes()
	{
		$nodes = Art_Register::in('node_types')->get();
		$user_rights = Art_User::getRights();
		$output = array();
		
		foreach( $nodes AS $node => $rights )
		{
			if( $rights <= $user_rights )
			{
				$output[$node] = __('module_'.$node);
			}
		}
		
		return $output;
	}
	
	
	/**
	 *	Get nodeable actions relative to user rights
	 * 
	 *	@final
	 *	@static 
	 *	@return array
	 */
	final static function getNodeableActions( array $node_types )
	{
		$user_rights = Art_User::getRights();
		
		$output = array();
		foreach( $node_types AS $node_type )
		{
			$output[$node_type] = array();
			$buff = Art_Register::in('nodeable_actions_'.$node_type)->get();
			foreach( $buff AS $action => $rights )
			{
				if( $rights <= $user_rights )
				{
					$output[$node_type][$action] = __('module_'.$node_type.'_action_'.$action);
				}
			}
		}
		
		return $output;
	}
	
	
	/**
	 *	Get settings by module name (type)
	 *	
	 *	@static
	 *	@param string $module_name
	 *	@return stdClass
	 */
	static function getSettings( $module_name )
	{
		if( isset(static::$_settings[$module_name]) )
		{
			return static::$_settings[$module_name];
		}
		else
		{
			return new stdClass();
		}
	}
	
	
	/**
	 *	Set settings by module name
	 * 
	 *	@static
	 *	@param string $module_name
	 *	@param stdClass $settings
	 *	@return void
	 */
	static function setSettings( $module_name, $settings )
	{
		if( is_array($settings) || is_a($settings,'stdClass') )
		{
			static::$_settings[$module_name] = $settings;
		}
		else
		{
			trigger_error('Invalid argument supplied for Art_Module::setSettings()',E_USER_ERROR);
		}
	}
	
	
	/**
	 *	Save module settings by module name
	 * 
	 *	@param stdClass $module_name
	 */
	static function saveSettings( $module_name )
	{
		$settings = new Art_Model_Module_Type(array( 'name' => $module_name ));
		if( !$settings->isLoaded() )
		{
			$settings->name = $module_name;
		}
		$settings->settings = static::getSettings($module_name);
		$settings->save();
	}
	
	
	
	/**
	 *	Load all module settings
	 * 
	 *	@static
	 *	@return void
	 */
	static function loadModuleSettings()
	{
		self::$_settings = Art_Model_Module_Type::getSettingsSimple();
	}
	

	/**
	 *	Scan and load for local (current layer) modules
	 * 
	 *	@static
	 *	@return void
	 */
	static function loadModulesCurrLayer()
	{
		self::$_availableModules = self::scanModules( Art_Router::getLayer() );
		
		self::_loadModules( self::$_availableModules );
		
		if( !self::exists('error','noAccess') || !self::exists('error','notFound') )
		{
			trigger_error('Module_Error for layer '.Art_Router::getLayer().' not found or not compatible', E_USER_ERROR);
		}
	}
	
	
	/**
	 *	Load all modules bootstraps and call init
	 * 
	 *	@static
	 *	@return array Loaded bootstraps
	 */
	static function loadBootstraps()
	{
		//If wasn't loaded before
		if( empty(self::$_bootstraps) )
		{
			//For each file
			$iterator = new FilesystemIterator(self::ROOT_FOLDER);
			foreach ($iterator AS $file_info) 
			{	
				$path = $file_info->getPathname().'/'.self::BOOTSTRAP_FILENAME;
				if( is_readable($path) )
				{
					require($path);
					$class_name = Art_Filter::moduleBootstrapClassName($file_info->getFilename());
					if( class_exists($class_name) )
					{
						self::$_bootstraps[] = $class_name;
						$class_name::initialize();
					}
					else
					{
						trigger_error('Class '.$class_name.' wasn\'t found in '.$path, E_USER_ERROR);
					}
				}
			}
		}

		return self::$_bootstraps;
	}

	
	/**
	 *	Get modules types by scanning folders
	 * 
	 *	@param string $layer
	 *	@return array
	 */
	static function scanModules( $layer )
	{
		$output = array();
		
		//Get module names
		$iterator = new FilesystemIterator('modules');
		
		//For each module
		foreach ($iterator AS $file_info) 
		{
			//Create dir and file path
			$module_name = $file_info->getFilename();
			$module_dir = 'modules/'.$module_name.'/'.$layer;
			$module_file = $module_name.'.php';
			
			//IF exists in current layer
			if( is_readable($module_dir.'/'.$module_file) )
			{
				$output[] = array('type' => $module_name,
									'class' => Art_Filter::moduleClassName($module_name),
									'dir' => $module_dir,
									'file' => $module_file);
			}
			elseif( is_readable($module_dir) )
			{
				trigger_error('Module_'.$module_name.' '.$layer.' layer is missing '.$module_file.' file', E_USER_ERROR);
			}
		}

		return $output;
	}
	

    /**
     *	Load (include) module files
     * 
     *	@static
	 *	@access protected
     *	@param array $modules
     *	@return void
     */
    static protected function _loadModules(array &$modules)
	{
		//Include all modules
		foreach($modules AS &$module)
		{
			//Include each file once only
			if( !isset($module['loaded']) || $module['loaded'] !== true )
			{
				require(ftest($module['dir'].'/'.$module['file']));
				$module['loaded'] = true;
			}
		}
    }
	
	
	/**
	 * 	Create modules from database table MODULE
	 * 
	 * 	@static
	 * 	@return void
	 */
	static function createModulesFromDb()
	{
		//Select all modules based on user rights
		$query = Art_Main::db()->prepare('SELECT * FROM `module` WHERE `active` = 1 AND `rights` <= :rights AND `layer` = :layer ORDER BY `sort` ASC ');
		$query->execute(array('rights' => Art_User::getRights(), 'layer' => Art_Router::getLayer()));
		$dbModules = $query->fetchAll(PDO::FETCH_ASSOC);
		
		//For each module from database
		foreach($dbModules AS $dbModule)
		{
			$toShow = true;
			
			//If show in is specified in database
			if( $dbModule['show_in'] )
			{
				//Show in has two parts - including and excluding
				//Including (to show) excluding (not to show)
				$showIn = array( 'in' => array(), 'ex' => array() );
				
				$url = Art_Router::getURIAfterRoute();
				
				//Get show in from DB and explode by rows (marked with *)
				$showInExploded = explode('*', $dbModule['show_in']);

				//For each showIn row from database
				foreach($showInExploded AS $si)
				{
					//Skip empty lines
					if( strlen($si) == 0 )
					{
						continue;
					}
					
					//Add trailing slash if not in showin
					if( strrpos($si, '/') != ( strlen($si) - 1 ) )
					{
						$si = $si.'/';
					}
					
					//If - put showIn to excluding array
					if( $si[0] == '-' )
					{
						$showIn['ex'][] = trim(substr($si, 1));
					}
					//Else put showIn to including array
					else
					{
						$showIn['in'][] = trim($si);
					}
				}
				
				//If any condition is set
				if( count($showIn['in']) || count($showIn['ex']) )
				{
					$toShow = false;							
					
					//If url is found in including array values, it should be shown
					foreach($showIn['in'] AS $sin)
					{
						if( strpos($url, $sin) === 0 )
						{
							$toShow = true;
						}
					}
					
					//If url is found in excluding array values, it should not be shown
					foreach($showIn['ex'] AS $sex)
					{
						if( $sex && strpos($url, $sex) === 0 )
						{
							$toShow = false;
						}
					}
				}
			}
			
			if( $toShow )
			{
				//Put everything in options except type
				$options = $dbModule;
				unset($options['type']);

				//Unserialize params array
				$options['params'] = json_decode($options['params'], true);

				//Create module
				self::createModule($dbModule['type'], $options, true);
			}
		}
	}

	
	/**
	 *	Recreate content module
	 *	Stage 1 - load
	 *	Stage 2 - load, init
	 *	Stage 3 - load, init, call
	 * 
	 *	@static
	 */
	static function recreateContentModule( $stage )
	{
		if( static::$_recreatedCount++ < static::RECREATIONS_LIMIT )
		{
			switch ( $stage )
			{
				case self::STAGE_LOAD:
				{
					return Art_Module::createContentModule();
				}
				case self::STAGE_INIT:
				{
					$module = Art_Module::createContentModule();
					$module->init();
					return $module;
				}
				case self::STAGE_CALL:
				{
					$module = Art_Module::createContentModule();
					$module->init();
					$module->call();
					return $module;
				}
			}
		}
		else
		{
			trigger_error('Content module can\'t be recreated more than '.static::RECREATIONS_LIMIT.' times', E_USER_ERROR);
		}
	}
	

	/**
	 *	Create content module and calls it's action based on URL (section&action)
	 * 
	 *	@static
	 *	@return void
	 */
	static function createContentModule()
	{		
		$section = Art_Router::getSection();
		$action = Art_Router::getAction();
		
		$module = NULL;
		
		//If section & action is set
		if( $section && $action )
		{
			//If user is trying to access embedd action from nonAjax
			if( $action == 'embedd' && !Art_Server::isAjax() )
			{
				$module = self::errorNotFound( Art_Module::STAGE_LOAD );
			}
			//If module not exists
			elseif( !self::exists($section, $action) )
			{
				$module = self::errorNotFound( Art_Module::STAGE_LOAD );
			}
			else
			{
				$module = self::createModule($section, array('action' => $action));	
			}
		}
		//If section only
		elseif($section)
		{
			//If module exists
			if( self::exists($section) )
			{
				$module = self::createModule($section);	
			}
			else
			{
				$module = self::errorNotFound( Art_Module::STAGE_LOAD );
			}
		}

		//Put module to content
		if( NULL !== $module )
		{
			Art_Template::setContentModule($module);
		}
		
		return $module;
	}
	
	
    /**
     *  Instantiate and init modules
	 * 
     *  @static
     *  @param array $modules array(type => options)
     *  @return array All created modules
     */
    static function createModules($modules)
	{
        $createdModules = [];
        
        //For each given module
        foreach($modules AS $type => $options)
        {
			//Create module
			self::createModule($type, $options);
        }
        
        return $createdModules;
	}
	
    
    /**
     *  Instantiate and init single module
     * 
     *  @static
     *  @param string $type Module type
     *  @param array $options Module options (position|action|rights|params|name|show_name)
     *  @return Art_Abstract_Module Created module
     */
    static function createModule($type, array $options = array(), $is_widget = false)
    {
		if( !Art_Main::isRendered() )
		{
			//Input validation
			if( is_string($type) )
            {		                
                //Make class name
                $class_name = Art_Filter::moduleClassName($type);
				
				//If class exists
                if( class_exists($class_name) )
                {
					//If is child of Art_Abstract_Module
					if( is_subclass_of($class_name, 'Art_Abstract_Module') )
					{
						$options['is_widget'] = $is_widget;
						
						try
						{						
							//Instantiate the module
							$module = new $class_name($options);
						}
						catch( NoPrivilegesException $e )
						{
							if( $is_widget )
							{
								return NULL;
							}
							else
							{
								self::errorNoPrivileges( static::STAGE_LOAD );
							}
						}
						
						//If module was created - init
						if( !empty($module) && $module->init() )
						{
							self::$_modules[] = $module;
							return $module;
						}
						else
						{
							return NULL;
						}
					}
					else
					{
						trigger_error('Module '.$class_name.' is not child of Art_Abstract_Module',E_USER_ERROR);
					}
                }
                else
                {
                    trigger_error('Module class '.$class_name.' was not found or wasn\'t loaded</br>',E_USER_ERROR);
                }
			}
			else
			{
				trigger_error('Invalid argument supplied for Module::loadModule()',E_USER_ERROR);
			}
		}
		else
		{
			trigger_error('Site was already rendered, cannot create '.$type.' module',E_USER_NOTICE);
		}
    }
	
	
	/**
	 *	Create, init, call and render module
	 * 
	 *	@param string $type
	 *	@param string $action_name
	 *	@param array $params
	 *	@return string
	 */
	static function createAndRenderModule($type, $action_name = 'index', $params = array(), $is_widget = false)
	{
		$view = isset($params['view']) ? $params['view'] : Art_Module::VIEW_AUTOSET;
		$module = self::createModule($type,array('action' => $action_name, 'view' => $view, 'params' => $params), $is_widget);
		
		$module->init();
		$module->call();
		return $module->render();
	}
	
	    
    /**
	 *	Returns true if module with action exists
	 * 
	 *	@static
	 *	@param string $name
	 *	@param string $action
	 *	@return bool True if exists
	 */
    static function exists($name, $action = 'index')
    {
		//Normalize name and action
        $name = Art_Filter::moduleClassName($name);
		$action = Art_Filter::moduleAction($action);
		
        if(class_exists($name) && method_exists($name,$action))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
	
	/**
	 *	If true, system will exit when errorNoPrivileges or errorNotFound occures
	 * 
	 *	@static
	 *	@param bool $to_exit
	 *	@return bool Current value
	 */
	static function exitOnError( $to_exit = NULL )
	{
		if( NULL !== $to_exit )
		{
			static::$_exit_on_error = Art_Filter::toBool( $to_exit );
		}
		
		return static::$_exit_on_error;
	}
	
    
	/**
	 *	@todo
	 *	Shows no privileges error message to user
	 * 
	 *	@param int $stage [self::STAGE_LOAD|self::STAGE_INIT|self::STAGE_CALL]
	 *	@static
	 *	@return void
	 */
	static function errorNoPrivileges( $stage )
	{
		Art_Router::setNoAccess();
		
		if( static::exitOnError() )
		{
			exit;
		}
		else
		{
			return Art_Module::recreateContentModule($stage);	
		}
	}
    
    
    
	/*
	 *	@todo
	 *	Shows not found error message to user
	 * 
	 *	@param int $stage [self::STAGE_LOAD|self::STAGE_INIT|self::STAGE_CALL]
	 *	@static
	 *	@return void
	 */
	static function errorNotFound( $stage )
	{
		Art_Router::setNotFound();

		if( static::exitOnError() )
		{
			exit;
		}
		else
		{
			return Art_Module::recreateContentModule($stage);
		}
	}
}