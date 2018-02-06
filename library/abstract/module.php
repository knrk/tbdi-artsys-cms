<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/abstract
 *	@abstract
 */
abstract class Art_Abstract_Module {
    use Art_Event_Emitter;
	
	/**
	 *	@access protected
	 *	@var string Module type - class name
	 */
    protected $_type = 'abstract';
    
    /**
     *  @var string Module class name
     *  @access protected
     */
    protected $_class = 'Module_Abstract';

    /**
	 *	@access protected
	 *	@var string Module name
	 */
    protected $_name = 'Module';
	
    
	/**
	 *	@access protected
	 *	@var bool True if name should be shown
	 */
    protected $_showName = false;
    
    /**
	 *	@access protected
	 *	@var string Action name
	 */
    protected $_actionName = Art_Module::ACTION_DEFAULT;
    
    /**
	 *	@access protected
	 *	@var string View name
	 */
    protected $_viewName = Art_Module::VIEW_AUTOSET;
	
    /**
	 *	@access protected
	 *	@var bool True if module was inited
	 */
    protected $_isInited = false;
    
    /**
	 *	@access protected
	 *	@var bool True if module action was called
	 */
    protected $_isCalled = false;
    
    /**
	 *	@access protected
	 *	@var bool True if module was rendered
	 */
    protected $_isRendered = false;

    /**
	 *	@access protected
	 *	@var int Minimal rights to show this module
	 */
    protected $_rights = Art_User::NONREGISTERED;
	
    /**
	 *	@access protected
	 *	@var string Module position name
	 */
    protected $_position;
	
	/**
	 *	@access protected
	 *	@var string This module dir name 
	 */
	protected $_dir;
	
	/**
	 *	@access protected
	 *	@var bool True if this module is rendered as widget
	 */
	protected $_is_widget = false;
	
    /**
     *  Parameters of this module from database module.params (eg. $params->color = 'red')
	 *	@access public
	 *	@var stdClass
	 */
    protected $_params;
    
	/**
	 *	@access public
	 *	@var array All parameters passed to module view
	 */
	public $view = null;
	
	
	/**
	 *	Art_Abstract_Module->aisAccessible() constants
	 */
	
	/** Allow to AJAX requests */
	const ALLOW_AJAX = 'ajax';
	/** Allow to POST requests */
	const ALLOW_POST = 'post';
	/** Allow only on frontend */
	const ALLOW_FRONTEND = 'frontend';
	/** Allow only on backend */
	const ALLOW_BACKEND = 'backend';
	/** Allow only as widget */
	const ALLOW_WIDGET = 'widget';
    
           
    /**
     *  @final
     *  @return string Module name
     */
    final function getName()
    {
        return $this->_name;
    }
    
    
    /**
     *  @final
     *  @return string Action name
     */
    final function getActionName()
    {
        return $this->_actionName;
    }
    
    
    /**
     *  @final
     *  @return string View name
     */
    final function getViewName()
    {
        return $this->_viewName;
    }
	
	
	/**
	 *	@final
	 *	@return array All values passed to module view
	 */
	final function getViewValues()
	{
		return $this->view;
	}
    
    
    /**
     *  @final
     *  @return string Module type (module class name)
     */
    final function getType()
    {
        return $this->_type;
    }
    
    
    /*
     *  @final
     *  @return string Module position
     */
    final function getPosition()
    {
        return $this->_position;
    }
    
    
    /**
     *  @final
     *  @return int Minimal required rights for this module
     */
    final function getRights()
    {
        return $this->_rights;
    }
    
    
    /**
     *  @final
     *  @return bool True if module name should be shown
     */
    final function getShowName()
    {
        return $this->_showName;
    }
    
    /**
     *  @final
     *  @return string Class name
     */
    final function getClass()
    {
        return $this->_class;
    }
    
	
    /**
     * @final
     * @return string Module root directory relative to index.php
     */
    final function getDir()
    {
        return $this->_dir;
    }
	
	
	/**
	 *	@final
	 *	@return stdClass
	 */
	final function getSettings()
	{
		return Art_Module::getSettings($this->getType());
	}	
	
	
	/**
	 *	@final
	 *	@return array
	 */
	final function getNodeTypes()
	{
		return Art_Register::in('node_types')->get();
	}
	
	
	/**
	 *	@final
	 *	@param string [optional] $name
	 *	@return stdClass
	 */
	final function getParams( $name = NULL, $default = NULL )
	{
		if( NULL !== $name )
		{
			if( isset($this->_params->{$name}) )
			{
				return $this->_params->{$name};
			}
			else
			{
				if( NULL !== $default )
				{
					return $default;
				}
				else
				{
					return NULL;
				}
			}
		}
		else
		{
			return $this->_params;	
		}
	}
	
	
	/**
	 *	Get array of items used in nodes
	 * 
	 *	@static
	 *	@param string $type Type of nodes eg. category
	 *	@return array
	 */
	static function getNodeItemsList( $type = NULL )
	{
		return array();
	}
	
	
	/**
	 *	@final
	 *	@param array|stdClass $settings
	 *	@return void
	 */
	final function setSettings($settings)
	{
		Art_Module::setSettings($this->getType(), $settings);
		Art_Module::saveSettings($this->getType());
	}
	
	
    /**
     *  @final
     *  @return bool True if is initialized
     */
    final function isInited()
    {
        return $this->_isInited;
    }
    
    
    /**
     *  @final
     *  @return bool True if is called
     */
    final function isCalled()
    {
        return $this->_isCalled;
    }
    

    /**
     *  @final
     *  @return bool True if is initialized
     */
    final function isRendered()
    {
        return $this->_isRendered;
    }
	
	
	/**
	 *	@final
	 *	@return bool True if this module is rendered as widget
	 */
    final function isWidget()
	{
		return $this->_is_widget;
	}
	
    
    /**
     *  Sets minimal rights value for this module
     * 
     *  @final
     *  @param int $rights 0 to MAX_RIGHTS
     *  @return \Art_Abstract_Module
     *  @example $this->setRights(Art_User::REGISTERED) Only registered and higher groups can view this module
     */
    final function setRights($rights)
    {
        //No sense of changing after render
        if( !$this->isRendered() )
        {
			//If value is between 0 and MAX_RIGHTS
			if( Art_Validator::isValid($rights, array(Art_Validator::IS_RIGHTS)) )
			{
				$this->_rights = $rights;
			}
			else
			{
				trigger_error('Invalid argument supplied for '.$this->_class.'->setRights()',E_USER_ERROR);
			}
        }
        else
        {
            trigger_error('Attempt to change rights of '.$this->_class.' after render. Dump: '.var_dump_str($this),E_USER_WARNING);
        }
        
        return $this;
    }
    
    
    /**
     *  Sets position of module
     * 
     *  @final
     *  @param string $position Position of module in template
     *  @return \Art_Abstract_Module
     *  @example $this->setPosition('body') Module will be show in body container
     */
    final function setPosition($position)
    {
        if( !$this->isRendered() )
        {
            if( is_string($position) )
            {
                $this->_position = $position;
            }
            else
            {
                trigger_error('Invalid argument supplied for '.$this->_class.'->setPosition()',E_USER_ERROR);  
            }
        }
        else
        {
            trigger_error('Attempt to change position of '.$this->_class.' after render. Dump: '.var_dump_str($this),E_USER_WARNING);
        }
        
        return $this;
    }
    
    
    /**
     *  Sets view
     * 
     *  @final
     *  @param string $view_name View name
     *  @return \Art_Abstract_Module
     *  @example $this->setView('foo') A foo.phtml view will be echoed
     */
    final function setView( $view_name = NULL )
    {
        //No sense to change view after render
        if( !$this->isRendered() )
        {
			if( empty($view_name) )
			{
				$this->_viewName = Art_Module::VIEW_UNSET;
			}
			elseif( is_string($view_name) )
            {			
                //Remove extension if is found
                if( strpos($view_name,'.phtml') !== false )
                {
                    $view_name = substr($view_name, 0, -6);
                }
                
                $this->_viewName = $view_name;
            }
            else
            {
                trigger_error('Invalid argument supplied for '.$this->_class.'->setView()',E_USER_ERROR);  
            }
        }
        else
        {
            trigger_error('Attempt to change view of '.$this->_class.' after render. Dump: '.var_dump_str($this),E_USER_WARNING);
        }
        
        return $this;
    }
    
    
    /**
     *  Sets module name
     * 
     *  @final
     *  @param string $name Module name
     *  @return \Art_Abstract_Module
     *  @example $this->setName('Gallery')
     */
    final function setName($name)
    {
        //No sense changing name after render
        if( !$this->isRendered() )
        {
            //Input validation
            if( is_string($name) )
            {
                $this->_name = $name; 
            }
            else
            {
                trigger_error('Invalid argument supplied for '.$this->_class.'->setName()',E_USER_ERROR);  
            }
        }
        else
        {
            trigger_error('Attempt to change name of '.$this->_class.' after module render. Dump: '.var_dump_str($this),E_USER_WARNING);
        }
        
        return $this;
    }
    

    /**
     *  Sets if module name should be shown
     * 
     *  @final
     *  @param bool $toShow True if name is to be shown
     *  @return \Art_Abstract_Module
     *  @example $this->setShowName(false) Name will not be shown
     */
    final function setShowName($toShow)
    {
        //No sense changing showName after render
        if( !$this->isRendered() )
        {
            //Input conversion
			$toShow = Art_Filter::toBool($toShow);
			
			if( NULL !== $toShow )
			{
                $this->_showName = $toShow;
            }
            else
            {
                trigger_error('Invalid argument supplied for '.$this->_class.'->setShowName()',E_USER_ERROR);  
            }
        }
        else
        {
            trigger_error('Attempt to change show_name of '.$this->_class.' after module render. Dump: '.var_dump_str($this),E_USER_WARNING);
        }
        
        return $this;
    }
    
    
    /**
     *  Sets action name
     * 
     *  @final
     *  @param string $action_name Action name
     *  @return \Art_Abstract_Module
     *  @example $this->setAction('index') An indexAction will be called
     */
    final function setAction($action_name)
    {
        //No sense changing action name after render
        if( !$this->isCalled() )
        {
            //Input validation
            if( is_string($action_name) && strlen($action_name) > 0 )
            {
                //Input normalization - remove Action postfix
                $action_name = Art_Filter::moduleAction($action_name);
                if( method_exists($this, $action_name) )
                {
                    $this->_actionName = Art_Filter::moduleActionShort($action_name);    
                }
                else
                {
                    trigger_error('Action '.$action_name.' in '.$this->_class.' does not exists',E_USER_ERROR);
                }
            }
            else
            {
                trigger_error('Invalid argument supplied for '.$this->_class.'->setAction()',E_USER_ERROR); 
            }
        }
        else
        {
            trigger_error('Attempt to change action of '.$this->_class.' after module call. Dump: '.var_dump_str($this),E_USER_WARNING);
        }
        
        return $this;
    }
    
    
    /**
     *  Sets params
     * 
     *  @final
     *  @param array $params All parameters from db in assoc array
     *  @return \Art_Abstract_Module
     *  @example $this->setParmas(array('color'=>'red'))
     */
    final function setParams( array $params = NULL )
    {
		$this->_params = array_to_object($params);
        
        return $this;
    }
	
	
	/**
	 *	Add single param to module
	 * 
	 *	@param string $name
	 *	@param mixed $value
	 *	@return this
	 */
	final function addParam( $name, $value )
	{
		$this->_params->$name = $value;
		
		return $this;
	}
	
	
	/**
	 *	Add params array to module
	 * 
	 *	@param array $params
	 *	@return this
	 */
	final function addParamsArray( array $params )
	{
		foreach($params AS $name => &$value)
		{
			$this->_params->$name = $value;
		}
		
		return $this;
	}
    
	
    /**
     *  @final
     *  @param array $options Module options associative array (position|action|rights|params|name|show_name)
     *  @example new $this(array('position'=>'body','params'=>array('color'=>'red'),'action'=>'index'))
	 *	@throws NoPrivilegesException
     */
    final function __construct( array $options = NULL )
    {
        //Get classname of child class
        $this->_class = static::class;
        //Get type of child class
        $this->_type = Art_Filter::moduleType($this->_class);
		
		//If user has no rights to read this module
		if( !Art_User::readAllowed($this->_type) )
		{
			throw new NoPrivilegesException;
		}
		
        //Create empty class for params and view           
        $this->_params = new stdClass();
		$this->view = new stdClass();

		//Save dir name
		$this->_dir = 'modules/'.$this->_type.'/'.Art_Router::getLayer();

		//If is AJAX - default VIEW is unset
		if(Art_Server::isAjax())
		{
			$this->setView(Art_Module::VIEW_UNSET);
		}
		
		//If options are supplied
		if( NULL !== $options )
		{
			//Put options to module	
			foreach($options AS $key => $value)
			{
				switch($key)
				{
					case 'position':
						$this->setPosition($value);
						break;
					case 'action':
						$this->setAction($value);
						break;
					case 'rights':
						$this->setRights($value);
						break;
					case 'params':
						$this->setParams($value);
						break;
					case 'name':
						$this->setName($value);
						break;
					case 'show_name':
						$this->setShowName($value);
						break;
					case 'view':
						$this->setView($value);
						break;
					case 'is_widget':
						if( $value )
						{
							$this->_is_widget = true;
						}
						break;
				}
			}
		}
		
		Art_Event::on(Art_Event::MODULES_CALL, function(){
			$this->call();
		});
    }
    
    
    /**
     *  Function called before actions
     * 
     *  @access protected
     *  @return void
     */
    protected function _init(){}
    
    
    /**
     *  Used as a default content action
     * 
     *  @access protected
     *  @return void
     */
    protected function indexAction()
	{
		$this->showTo(Art_User::NO_ACCESS);
	}
    
    
    /**
     *  Used as a default embedd action
     * 
     *  @access protected
     *  @return void
     */
    protected function embeddAction(){}
    
	
	/**
	 *	Used as a default AJAX action
     * 
	 *	@access protected
	 *	@return void
	 */
	protected function ajaxAction()
	{
		$this->showTo(Art_User::NO_ACCESS);
	}
    
	
    /**
     *  Function used to init module
     * 
     *  @final
     *  @return void
     */
    final function init()
    {
        if( !$this->_isInited )
        {
            //If user has privileges to init
            if( Art_User::hasPrivileges($this) )
            {
                try
                {                
                    //Initialize module
                    $this->_init();
                    $this->_isInited = true;
					
					return true;
                }
                catch(NoPrivilegesException $e)
                {
                    //User's rights are not enough for this action
                    Art_Module::errorNoPrivileges( Art_Module::STAGE_INIT );
                }
				catch(NotFoundException $e)
				{
					//User should not know about this functionality
					Art_Module::errorNotFound( Art_Module::STAGE_INIT );
				}
            }
        }
		
		return false;
    }
    
    
    /**
     *  Call module action
     * 
     *  @final
     *  @return void
     */
    final function call()
    {
        if(!$this->_isCalled)
        { 
            //Create a long action name
            $action = Art_Filter::moduleAction($this->_actionName);

            //If action is found
            if( method_exists($this, $action) )
            {
                //If user has privileges to call
                if(Art_User::hasPrivileges($this))
                {
                    //If view is not changed by user, it's setted by action name
                    if($this->_viewName == Art_Module::VIEW_AUTOSET)
                    {
                        $this->setView($this->_actionName);    
                    }
                
                    try
                    {
                        //Call the action
                        $this->$action();
                        $this->_isCalled = true;
                    }
                    catch(NoPrivilegesException $e)
                    {
                        //User's rights are not enough for this action
                        Art_Module::errorNoPrivileges( Art_Module::STAGE_CALL );
                    }
					catch(NotFoundException $e)
					{
						//User should not know about this functionality
						Art_Module::errorNotFound( Art_Module::STAGE_CALL );
					}
                }
            }
            else
            {
                trigger_error('Action '.$action.' in module '.$this->_type.' does not exists',E_USER_ERROR);
            }
        }
        else
        {
            trigger_error('Attempt to call '.$this->_type.' twice. Dump: '.var_dump_str($this),E_USER_NOTICE);
        }
    }
    
    
    /**
     *  Render module - echo PHTML file
     * 
     *  @final
     *  @return void
     */
    final function render()
    {
		//If module wasn't been rendered before
		if( !$this->_isRendered )
		{
			//If view is set
			if( $this->_viewName != Art_Module::VIEW_UNSET && !empty($this->_viewName) )
			{
				//If user has privileges to render
				if( Art_User::hasPrivileges($this) )
				{
					//Create and render view
					$view = new Art_Model_View($this->_dir.'/views/'.$this->_viewName.'.phtml');
					if($view)
					{
						$this->_isRendered = true;
						return $view->render($this->getViewValues(),$this);
					}
				}
			}
		}
		else
		{
			trigger_error('Attempt to render '.$this->_type.' twice. Dump: '.var_dump_str($this),E_USER_WARNING);
		}
    }
	
	
	/**
	 *	Returns true if user is allowed to read in this module
	 * 
	 *	@return boolean
	 */
	final function readAllowed()
	{
		return Art_User::readAllowed($this->_type);
	}
	
	
	/**
	 *	Returns true if user is allowed to add in this module
	 * 
	 *	@return boolean
	 */
	final function addAllowed()
	{
		return Art_User::addAllowed($this->_type);
	}
	
	
	/**
	 *	Returns true if user is allowed to update in this module
	 * 
	 *	@return boolean
	 */
	final function updateAllowed()
	{
		return Art_User::updateAllowed($this->_type);
	}	
	
	
	/**
	 *	Returns true if user is allowed to delete in this module
	 * 
	 *	@return boolean
	 */
	final function deleteAllowed()
	{
		return Art_User::deleteAllowed($this->_type);
	}
	
	
    /**
     *  Returns true if user has acces to this action or init
     * 
     *  @final
     *  @param int|array $options
	 *	@param bool $explicit If true - function will return true only for the same rights user levels
     *  @return bool TRUE if has access, FALSE if not
     *  @example $this->isAccessible(array(self::ALLOW_AJAX = Art_User::NONREGISTERED)) Allows everybody to acces ONLY by ajax
     */
	final function isAccessible($options, $explicit = false)
	{
		//If one of this class contants is given
		if(is_string($options) && in_array($options, array(self::ALLOW_AJAX, self::ALLOW_BACKEND, self::ALLOW_FRONTEND, self::ALLOW_POST)))
		{
			$options = array($options => Art_User::NONREGISTERED);
		}
		
		//If number given - allow to specified users (all requests and layers)
		if( is_numeric($options) )
		{
			$minRights = $options;
            //If rights are between 0 and MAX_RIGHTS
            if( $minRights <= Art_User::MAX_RIGHTS && $minRights >= 0 )
            {
                //If user has no rights
                if( Art_User::getRights() < $minRights )
                {
					if( $explicit )
					{
						if( Art_User::getRights != $minRights )
						{
							$this->setRights($minRights);
							return false;
						}
						else
						{
							return true;
						}
					}
					else
					{
						$this->setRights($minRights);
						return false;
					}
                }
				else
				{
					return true;
				}
            }
            else
            {
                trigger_error('Invalid rights value supplied for '.$this->_class.'->allowTo()',E_USER_ERROR);
            }
		}
		//If options is array
        elseif(is_array($options))
        {
			$minRights = Art_User::MAX_RIGHTS;			
			//For each option
			foreach($options AS $type => $rights)
			{
				//If option is found and matches layer or request
				switch($type)
				{
					case self::ALLOW_AJAX:
						if(Art_Server::isAjax())
						{
							$minRights = $rights;
							break 2;
						}
						break;
					case self::ALLOW_BACKEND:
						if(Art_Router::isBackend())
						{
							$minRights = $rights;
							break 2;
						}
						break;
					case self::ALLOW_FRONTEND:
						if(Art_Router::isFrontend())
						{
							$minRights = $rights;
							break 2;
						}
						break;
					case self::ALLOW_POST:
						if(Art_Server::isPost())
						{
							$minRights = $rights;
							break 2;
						}
						break;
					case self::ALLOW_WIDGET:
						if($this->isWidget())
						{
							$minRights = $rights;
							break;
						}
				}
			}
			
            //If rights are between 0 and MAX_RIGHTS
            if($minRights <= Art_User::MAX_RIGHTS && $minRights >= 0)
            {
                //If user has no rights
                if(Art_User::getRights()<$minRights)
                {
					if($explicit)
					{
						if(Art_User::getRights != $minRights)
						{
							$this->setRights($minRights);
							return false;
						}
						else
						{
							return true;
						}
					}
					else
					{
						$this->setRights($minRights);
						return false;
					}
                }
				else
				{
					return true;
				}
            }
            else
            {
                trigger_error('Invalid rights value supplied for '.$this->_class.'->allowTo()',E_USER_ERROR);
            }
        }
        else
        {
            trigger_error('Invalid argument supplied for '.$this->_class.'->setParams()',E_USER_ERROR); 
        }
	}
	
    /**
     *  Show action or init for specific user group and layer or request type
     * 
     *  @final
     *  @param int|array $options
	 *	@param bool $explicit If true - will be shown only for the specified users, not for higher rights levels
     *  @return void
     *  @example $this->showTo(array(self::ALLOW_AJAX = Art_User::NONREGISTERED)) Allows everybody to acces ONLY by ajax
     */
    final function showTo($options, $explicit = false)
    {
		if( !$this->isAccessible($options,$explicit) )
		{
			throw new NotFoundException();
		}
    }
	
	
	/**
	 *	Show action or init only for widget use
	 * 
	 *	@final
	 *	@return void
	 */
	final function showAsWidget()
	{
		$this->showTo(array(self::ALLOW_WIDGET => Art_User::ALL));
	}
	
	
    /**
     *  Allow action or init for specific user group and layer or request type
     * 
     *  @final
     *  @param int|array $options
	 *	@param bool [optional] $explicit If true - will be allowed only for the specified users, not for higher rights levels
     *  @return void
     *  @example $this->allowTo(array(self::ALLOW_AJAX = Art_User::NONREGISTERED)) Allows everybody to acces ONLY by ajax
     */
    final function allowTo($options, $explicit = false)
    {
		if( !$this->isAccessible($options,$explicit) )
		{
			throw new NoPrivilegesException();
		}
    }
	
	
	/**
	 *	Allow action or init only for widget use
	 * 
	 *	@final
	 *	@return void
	 */
	final function allowAsWidget()
	{
		$this->allowTo(array(self::ALLOW_WIDGET => Art_User::ALL));
	}
    
    
    /**
     *  Include CSS script to site HEAD
     * 
     *  @final
     *  @param string $path Path to CSS file relative to module /styles/
	 *	@param bool $to_cache [optional] If true, file will be cached (DO NOT USE IN ACTIONS!!!)
     *  @return void
     *  @example $this->includeCss('style.css');
     */
    final function includeCss($path, $to_cache = false)
    {        
        $fullpath = $this->getDir().'/styles/'.$path;
        Art_Main::appendCSS($fullpath, $to_cache);
    }
    
    
    /**
     *  Include JavaScript to site HEAD
     *  @final
     *  @param string $path Path to JS file relative to module /scripts/
	 *	@param bool $to_cache [optional] If true, file will be cached (DO NOT USE IN ACTIONS!!!)
     *  @return void
     *  @example $this->includeJs('script.js');
     */
    final function includeJs($path, $to_cache = false)
    {
        $fullpath = $this->getDir().'/scripts/'.$path;
        Art_Main::appendJS($fullpath, $to_cache);
    }
}

/**
 *	@package library/abstract
 */
class NoPrivilegesException extends Exception {}

/**
 *	@package library/abstract
 */
class NotFoundException extends Exception {}