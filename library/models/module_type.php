<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property string	$name			System name
 *	@property stdClass	$settings		Object stored as JSON
 */
class Art_Model_Module_Type extends Art_Abstract_Model_DB {
	    
    protected static $_table = 'module_type';
	
    protected static $_cols = array('id'			=>  array('select','insert'),
									'name'			=>	array('select','insert','update'),
									'settings'		=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
	
	/**
	 *	List of names indexed as id => name
	 * 
	 *	@var array
	 */
	protected static $_simple_names = NULL;
	
	/**
	 *	List of settings indexed as name => settings
	 * 
	 *	@var array
	 */
	protected static $_simple_settings = NULL;
	
	
	/**
	 *	Load data from database
	 *
     *  @param array|object $where Where SQL statement or abstractModel instance
	 *	@param bool|User $privileged
	 *	@param bool $active_only
     *  @return this
     *  @example load(array('where_col_name'=>'value'))
	 */
	public function load($where, $privileged = NULL, $active_only = false) 
	{
		$instance = parent::load($where, $privileged, $active_only);
		$instance->settings = json_decode($instance->settings);
		return $instance;
	}
	
	
    /**
     *  Save instance to DB
     *
     *  @return this
     */
	public function save() 
	{
		$buff = $this->settings;
		$this->settings = json_encode($this->settings);
		$save_result = parent::save();
		$this->settings = $buff;
		return $save_result;
	}
	
	
	/**
	 *	Get simple lists of module types
	 * 
	 *	@static
	 *	@return void
	 */
	static function _getSimples()
	{
		$instances = static::fetchAll();
		
		static::$_simple_settings = array();
		static::$_simple_names = array();
		foreach($instances AS $instance /* @var $instance Art_Model_Module_Type */)
		{
			static::$_simple_settings[$instance->name] = json_decode($instance->settings);
			if( NULL === static::$_simple_settings[$instance->name] )
			{
				static::$_simple_settings[$instance->name] = new stdClass();
			}
			
			static::$_simple_names[$instance->id] = $instance->name;
		}		
	}
	
	
	/**
	 *	Get all settings as simple array name => settings
	 * 
	 *	@return array
	 */
	static function getSettingsSimple()
	{
		if( NULL === static::$_simple_names || NULL === static::$_simple_settings )
		{
			static::_getSimples();
		}
		
		return static::$_simple_settings;
	}
	
	
	/**
	 *	Get paired id => name list of module type names
	 * 
	 *	@return array
	 */
	static function getNamesSimple()
	{
		if( NULL === static::$_simple_names || NULL === static::$_simple_settings )
		{
			static::_getSimples();
		}
		
		return static::$_simple_names;
	}
}