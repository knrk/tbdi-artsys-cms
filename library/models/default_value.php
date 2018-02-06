<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property string	$name
 *	@property string	$type
 *	@property string	$value
 *	@property string	$note
 *	@property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date

 */
class Art_Model_Default_Value extends Art_Abstract_Model_DB {
    
    protected static $_table = 'default_value';
	
	protected static $_foreign = array();
	
	protected static $_link = array();
    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'name'			=>	array('select','insert'),
									'type'			=>	array('select','insert'),
									'value'			=>	array('select','insert','update'),
									'note'			=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
	
	const TYPE_STRING = 'string';
	const TYPE_BOOLEAN = 'boolean';
	const TYPE_INTEGER = 'integer';
	const TYPE_FLOAT = 'float';
	const TYPE_HTML = 'html';
	
	/**
	 *	Returns all data types available
	 * 
	 *	@return string[]
	 */
	static function getTypes()
	{
		return array(
			self::TYPE_STRING,
			self::TYPE_BOOLEAN,
			self::TYPE_INTEGER,
			self::TYPE_FLOAT,
			self::TYPE_HTML
		);
	}

	
	/**
	 *	Returns all user readable data types available
	 * 
	 *	@return string[]
	 */
	static function getUserReadableTypes()
	{
		$types = array();
		
		foreach (static::getTypes() as $value) 
		{
			$types[$value] = __($value);
		}
		
		return $types;
	}
	
	
	/**
	 *	Returns true if type is valud
	 * 
	 *	@param bool $type
	 *	@return bool
	 */
	static function isTypeValid($type)
	{
		return in_array($type, self::getTypes());
	}
	
	
	/**
	 *	Returns true if value is valid by type
	 * 
	 *  @param type $type
	 *  @param type $value
	 *  @return bool
	 */
	static function isValueValid($type, $value)
	{
		switch($type)
		{
			case self::TYPE_STRING:
				return is_string($value);
			case self::TYPE_BOOLEAN:
				return $value === 1 || $value === 0 || is_bool($value);
			case self::TYPE_INTEGER:
				return $value == (int)$value && is_numeric($value);
			case self::TYPE_FLOAT:
				return $value == (float)$value  && is_numeric($value);
			case self::TYPE_HTML:
				return true;
			default:
				return false;
		}
	}
	
}