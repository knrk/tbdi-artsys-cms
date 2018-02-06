<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Register_Namespace {
	
	/**
	 *	Namespace name
	 * 
	 *	@var string 
	 */
	protected $_name;
	
	/**
	 *	All register values
	 * 
	 *	@var array 
	 */
	protected $_values = array();
	
	
	/**
	 *	If true, no other values can be added or read
	 * 
	 *	@var bool
	 */
	protected $_locked = false;
	
	
	/**
	 *	Create new register namespace
	 * 
	 *	@param string $name
	 *	@return Art_Model_Register_Namespace
	 */
	function __construct( $name ) 
	{
		$this->_name = $name;
	}
	
	
	/**
	 *	Returns true if namespace is locked
	 * 
	 *	@return bool
	 */
	function isLocked()
	{
		return $this->_locked;
	}
	
	
	/**
	 *	Get namespace name
	 * 
	 *	@return string
	 */
	function getName()
	{
		return $this->_name;
	}
	
	
	/**
	 *	Set register value
	 * 
	 *	@param string $name
	 *	@param string $value
	 *	@param bool $save_to_db
	 *	@return this
	 */
	function set( $name, $value, $save_to_db = false )
	{
		if( $this->isLocked() )
		{
			trigger_error('Register namespace '.$this->getName().' is locked');
		}
		else
		{
			$this->_values[$name] = $value;
		}
		
		if( $save_to_db )
		{
			$this->_save($name, $value);
		}
		
		return $this;
	}
	
	
	/**
	 *	Set values from array
	 * 
	 *	@param array $values
	 *	@param bool $save_to_db
	 *	@return this
	 */
	function setFromArray( $values, $save_to_db = false )
	{
		if( $this->isLocked() )
		{
			trigger_error('Register namespace '.$this->getName().' is locked');
		}
		else
		{
			foreach( $values AS $name => $value )
			{
				$this->_values[$name] = $value;
			}
			
			if( $save_to_db )
			{
				foreach( $values AS $name => $value )
				{
					$this->_save($name, $value);
				}
			}
		}
		
		return $this;
	}
	
	
	/**	
	 *	Save register value to db
	 * 
	 *	@param string $name
	 *	@param string $value
	 *	@return this
	 */
	protected function _save($name, $value)
	{
		$reg = Art_Model_Register_Value::search( $this->_name, $name );
	
		if( !$reg->isLoaded() )
		{
			$reg->name = $name;
			$reg->namespace = $this->_name;
		}
		
		$reg->value = $value;
		$reg->save();
		
		return $this;
	}
	
	
	/**
	 *	Get register value by name, or get all register values
	 * 
	 *	@param string $name
	 *	@param mixed $default_value
     *  @param boolean $error_type PHP error type to throw when variable is not found
	 *	@return mixed
	 */
	function get( $name = NULL, $default_value = NULL, $error_type = E_USER_WARNING )
	{
		if( $this->isLocked() )
		{
			trigger_error('Register namespace '.$this->getName().' is locked');
		}
		else
		{
			if( NULL === $name )
			{
				return $this->_values;
			}
			else
			{
				if( isset($this->_values[$name]) )
				{
					return $this->_values[$name];
				}
				else
				{
					if( NULL === $default_value )
					{
						trigger_error('Register value - '.$name.' - was not found',$error_type);
					}
					else
					{
						return $default_value;
					}
				}
			}
		}
	}
	
	
	/**
	 *	Returns true if namespace has value by name
	 * 
	 *	@param string $name
	 *	@return bool
	 */
	function has( $name )
	{
		if( $this->isLocked() )
		{
			trigger_error('Register namespace '.$this->getName().' is locked');
		}
		else
		{
			return isset($this->_values[$name]);
		}
	}
	
	
	/**
	 *	Lock namespace - no values can be added or read
	 * 
	 *	@return this
	 */
	function lock()
	{
		$this->_locked = true;
		return $this;
	}
	
	
	/**
	 *	Unlock namespace - other values can be added or read afterwards
	 * 
	 *	@return this
	 */
	function unlock()
	{
		$this->_locked = false;
		return $this;
	}
	
	
	/**
	 *	Remove register value by name
	 * 
	 *	@param string $name
	 *	@return this
	 */
	function remove( $name )
	{
		$reg = Art_Model_Register_Value::search( $this->getName(), $name );
		if( $reg->isLoaded() )
		{
			$reg->delete();
		}
		
		return $this;
	}
	
	
	/**
	 *	Purge (clean all values) namespace
	 * 
	 *	@return this
	 */
	function purge()
	{
		$values = Art_Model_Register_Value::search( $this->getName() );
		foreach( $values AS $value )
		{
			$value->delete();
		}
		
		return $this;
	}
}
