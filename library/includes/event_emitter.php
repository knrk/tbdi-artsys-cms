<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/includes
 */
trait Art_Event_Emitter {
	
	/**
	 *	@static
	 *	@access protected
	 *	@var array Array of all registered event callbacks
	 */
	protected static $_event_callbacks_static = array();
	
	/**
	 *	@access protected
	 *	@var array Array of all registered event callbacks
	 */
	protected $_event_callbacks = array();	
	
	
	/**
	 *	Returns true if event is existing
	 * 
	 *	@static
	 *	@param string $name
	 *	@return bool
	 */
	static function eventExistingStatic( $name )
	{
		return isset( static::$_event_callbacks_static[$name] );
	}
	
	
	/**
	 *	Returns true if event is existing
	 * 
	 *	@param string $name
	 *	@return bool
	 */
	function eventExisting( $name )
	{
		return isset( $this->_event_callbacks[$name] );
	}	
	
	
	/**
	 *	Create event
	 * 
	 *	@static
	 *	@param string $name
	 *	@return void
	 */
	static function createEventStatic( $name )
	{
		if( !static::eventExistingStatic($name) )
		{
			static::$_event_callbacks_static[$name] = array();
		}
	}
	
	
	/**
	 *	Create event
	 * 
	 *	@param string $name
	 *	@return this
	 */
	function createEvent( $name )
	{
		if( !$this->eventExisting($name) )
		{
			$this->_event_callbacks[$name] = array();
		}
		
		return $this;
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
	static function triggerEventStatic( $name, $data = NULL, $status_code = Art_Main::OK )
	{
		if( static::eventExistingStatic($name) )
		{
			if( $data instanceof Art_Model_Event_Data )
			{
				$ev_data = $data;
			}
			else
			{
				$ev_data = new Art_Model_Event_Data($name, $data, $status_code, count(static::$_event_callbacks_static[$name]));
			}
			
			foreach( static::$_event_callbacks_static[$name] AS $callback_func )
			{
				call_user_func($callback_func, $ev_data);
				$ev_data->nextCurrentNum();
			}
		}
	}
	
	
	/**
	 *	Call event and run all associated functions 
	 * 
	 *	@param string $name
	 *	@param mixed [optional] $data
	 *	@param int [optional] $status_code Art_Main::OK, Art_Main::ALERT, Art_Main::ERROR
	 *	@return this
	 */
	function triggerEvent( $name, $data = NULL, $status_code = Art_Main::OK )
	{
		if( $this->eventExisting($name) )
		{
			if( $data instanceof Art_Model_Event_Data )
			{
				$ev_data = $data;
			}
			else
			{
				$ev_data = new Art_Model_Event_Data($name, $data, $status_code, count($this->_event_callbacks[$name]));
			}
			
			foreach( $this->_event_callbacks[$name] AS $callback_func )
			{
				call_user_func($callback_func, $ev_data);
				$ev_data->nextCurrentNum();
			}
		}
		
		static::triggerEventStatic( $name, $data, $status_code);
		
		return $this;
	}
	
	
	/**
	 *	Register calback to event
	 * 
	 *	@param string $event_name
	 *	@param callable|string $callback
	 *	@param string [optional] Callback function name
	 *	@return this
	 */
	function registerEventCallback( $event_name, $callback, $callback_name = NULL )
	{
		//Create event if not exists
		$this->createEvent($event_name);
		
		//If callback is string - function name
		if( is_string($callback) )
		{
			$callback_function = function() use ($callback) { $this->$callback(); };
			if( NUll === $callback_name )
			{
				$callback_name = $callback;
			}
		}
		//If callback is callable function
		elseif( is_callable($callback) )
		{
			$callback_function = $callback;
			if( NUll === $callback_name )
			{
				$callback_name = '_anonymous_'.count( $this->_event_callbacks[$event_name] );
			}
		}
		else
		{
			trigger_error("Invalid argument supplied to registerEventCallback", E_USER_WARNING);
		}
		
		//Add callback function
		$this->_event_callbacks[$event_name][$callback_name] = $callback_function;
		
		return $this;
	}
	
	
	/**
	 *	Register calback to static event
	 * 
	 *	@static
	 *	@param string $event_name
	 *	@param callable|string $callback
	 *	@param string [optional] Callback function name
	 *	@return void
	 */
	static function registerEventCallbackStatic( $event_name, $callback, $callback_name = NULL )
	{
		//Create event if not exists
		static::createEventStatic($event_name);
		
		//If callback is string - function name
		if( is_string($callback) )
		{
			$callback_function = function() use ($callback) { $this::$callback(); };
			if( NUll === $callback_name )
			{
				$callback_name = $callback;
			}
		}
		//If callback is callable function
		elseif( is_callable($callback) )
		{
			$callback_function = $callback;
			if( NUll === $callback_name )
			{
				$callback_name = '_anonymous_'.count( $this->_event_callbacks[$event_name] );
			}
		}
		else
		{
			trigger_error("Invalid argument supplied to registerEventCallback", E_USER_WARNING);
		}
		
		//Add callback function
		static::$_event_callbacks_static[$event_name][$callback_name] = $callback_function;
	}
	
	
	/**
	 *	Register calback to event
	 *	Alias for registerEventCallback()
	 * 
	 *	@param string $event_name
	 *	@param callable|string $callback
	 *	@param string [optional] Callback function name
	 *	@return this
	 */
	function on( $event_name, $callback, $callback_name = NULL )
	{
		return $this->registerEventCallback($event_name, $callback, $callback_name );
	}
	
	
	/**
	 *	Register calback to static event
	 *	Alias for registerEventCallbackStatic()
	 * 
	 *	@static
	 *	@param string $event_name
	 *	@param callable|string $callback
	 *	@param string [optional] Callback function name
	 *	@return void
	 */
	static function onStatic( $event_name, $callback, $callback_name = NULL )
	{
		return static::registerEventCallbackStatic( $event_name, $callback, $callback_name );
	}
	
	
	/**
	 *	Remove event callback from event
	 * 
	 *	@param string $event_name
	 *	@param callable|string $callback
	 *	@return this
	 */
	function removeEventCallback( $event_name, $callback )
	{
		if( $this->eventExisting($event_name) )
		{
			//If callback is string - function name
			if( is_string($callback) )
			{
				$callback_function = NULL;
				$callback_name = $callback;
				
			}
			//If callback is callable function
			elseif( is_callable($callback) )
			{
				$callback_function = $callback;
				$callback_name = NULL;
			}
			else
			{
				trigger_error("Invalid argument supplied to removeEventCallback", E_USER_WARNING);
			}

			//For each callback
			foreach( $this->_event_callbacks[$event_name] AS $key => $cb )
			{
				//If name matches or function matches by reference
				if( $callback_name == $key || $cb === $callback_function )
				{
					unset( $this->_event_callbacks[$event_name][$key] );
				}
			}
		}
		
		return $this;
	}
	
	
	/**
	 *	Remove event callback from event
	 * 
	 *	@static
	 *	@param string $event_name
	 *	@param callable|string $callback
	 *	@return void
	 */
	static function removeEventCallbackStatic( $event_name, $callback )
	{
		if( static::eventExistingStatic($event_name) )
		{
			//If callback is string - function name
			if( is_string($callback) )
			{
				$callback_function = NULL;
				$callback_name = $callback;
				
			}
			//If callback is callable function
			elseif( is_callable($callback) )
			{
				$callback_function = $callback;
				$callback_name = NULL;
			}
			else
			{
				trigger_error("Invalid argument supplied to removeEventCallbackStatic", E_USER_WARNING);
			}

			//For each callback
			foreach( static::$_event_callbacks_static[$event_name] AS $key => $cb )
			{
				//If name matches or function matches by reference
				if( $callback_name == $key || $cb === $callback_function )
				{
					unset( static::$_event_callbacks_static[$event_name][$key] );
				}
			}
		}
	}
}