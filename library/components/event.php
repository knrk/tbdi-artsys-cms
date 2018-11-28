<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 * 
 *  @example
 * 
	$local = 'local item';
	Art_Event::on('my_event', function(Art_Model_Event_Data $event) use ($local) {
	   echo $local;
	   echo $event->getData();
	});
	Art_Event::trigger('my_event', 'remote');
 */
final class Art_Event extends Art_Abstract_Component {
    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
	
	/**
	 *	@static
	 *	@access proteced
	 *	@var array Array of all registered events
	 */
	protected static $_events = array();
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array Array of all registered event callbacks
	 */
	protected static $_callbacks = array();
	
	/* Event names */
	const COMPONENT_INITIALIZE = 'component_initialize';
	
	const MODEL_DELETE = 'model_delete';
	const MODEL_LOAD = 'model_load';
	const MODEL_SAVE_BEFORE = 'model_save_before';
	const MODEL_SAVE_AFTER = 'model_save_after';
	const MODEL_INSERT_BEFORE = 'model_insert_before';
	const MODEL_INSERT_AFTER = 'model_insert_after';	
	const MODEL_UPDATE_BEFORE = 'model_update_before';
	const MODEL_UPDATE_AFTER = 'model_update_after';
	
	const LABEL_SETUP = 'label_setup';
	const ROUTER_SETUP = 'router_setup';
	const REGISTER_SETUP = 'register_setup';
	const NODEABLE_ACTIONS_SETUP = 'nodeable_actions_setup';
	
	const USER_LOG_IN = 'user_log_in';
	
	const MODULES_CALL = 'modules_call';
	
	const CRON = 'cron';
	const CRON_INITIAL = 'cron_initial';
	const CRON_UTILITY = 'cron_utility';
	
	const LOCALE_CHANGED = 'locale_changed';
	
	/**
	 *	Returns true if event is existing
	 * 
	 *	@param string $name
	 *	@return bool
	 */
	static function isExisting( $name )
	{
		return in_array($name, static::$_events);
	}
	
	
	/**
	 *	Create event
	 * 
	 *	@static
	 *	@param string $name
	 *	@return void
	 */
	static function createEvent( $name )
	{
		if( !static::isExisting($name) )
		{
			static::$_events[] = $name;
			static::$_callbacks[$name] = array();
		}
	}
	
	
	/**
	 *	Attach callback function to event
	 * 
	 *	@static
	 *	@param string $name
	 *	@param callable $func
	 */
	static function registerCallback( $name, callable $func )
	{
		static::createEvent($name);
		static::$_callbacks[$name][] = $func;
	}
	
	
	/**
	 *	Attach callback function to event
	 * 
	 *	@static
	 *	@param string $name
	 *	@param callable $func
	 *	@return void
	 */
	static function on( $name, callable $func )
	{
		static::registerCallback($name, $func);
	}
	
	
	/**
	 *	Call event and run all associated functions 
	 * 
	 *	@static
	 *	@param string $name
	 *	@param mixed [optional] $data
	 *	@param int [optional] $status_code Art_Main::OK, Art_Main::ALERT, Art_Main::ERROR
	 *	@return void
	 */
	static function trigger($name, $data = NULL, $status_code = Art_Main::OK) {
		if (static::isExisting($name)) {
			if ($data instanceof Art_Model_Event_Data) {
				$ev_data = $data;
			} else {
				$ev_data = new Art_Model_Event_Data($name, $data, $status_code, count(static::$_callbacks[$name]));
			}
			
			foreach (static::$_callbacks[$name] as $callback_func) {
				call_user_func($callback_func, $ev_data);
				$ev_data->nextCurrentNum();
			}
		}
	}
}
