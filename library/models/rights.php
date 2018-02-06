<?php
/**
 *  @author Robin Zon <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property string	$name
 *	@property int		$value
 *	@property int		$system	If 1, this record should not be edited
 */
class Art_Model_Rights extends Art_Abstract_Model_DB {
    
	/**
	 *	Fetch all not higher cache
	 * 
	 *	@var Art_Model_Rights[]
	 */
	protected static $_fetch_all_not_higher;
	
	/**
	 *	Fetch all not higher id cache
	 * 
	 *	@var Art_Model_Rights[] 
	 */
	protected static $_fetch_all_ids_not_higher;
	
    protected static $_table = 'rights';

    protected static $_cols = array('id'		=>  array('select','insert'),
                                    'name'		=>  array('select','insert','update'),
                                    'value'		=>  array('select','insert','update'),
                                    'system'	=>  array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));     
	
    /**
     *  Save instance to DB
     *
     *  @return this
     */
	function save()
	{
		parent::save();
		
		static::$_fetch_all_ids_not_higher = NULL;
		static::$_fetch_all_not_higher = NULL;
		
		return $this;
	}
	
	
    /**
     *  Delete instance
     *
     *  @access public
     *  @return this
     */
    public function delete()	
	{
		parent::delete();
		
		static::$_fetch_all_ids_not_higher = NULL;
		static::$_fetch_all_not_higher = NULL;
		
		return $this;
	}
	
	
	/**
	 *	Fetch all items less or equals to rights [of current user]
	 *	Output is cached
	 * 
	 *	@static
	 *	@param int|Art_Model_User $rights [optional]
	 *	@return Art_Model_Rights[]
	 */
	static function fetchAllNotHigher( $rights = NULL )
	{
		if( NULL === static::$_fetch_all_not_higher )
		{
			static::$_fetch_all_not_higher = static::fetchAll();
		}
		
		if( NULL === $rights )
		{
			$rights = Art_User::getRights();
		}
		elseif( $rights instanceof Art_Model_User )
		{
			$rights = $rights->getRights();
		}
		
		$output = array();
		foreach(static::$_fetch_all_not_higher AS $item)
		{
			if( $item->value <= $rights )
			{
				$output[] = $item;
			}
		}
		
		return $output;
	}
	
	
	/**
	 *	Fetch all item ids less or equals to rights [of current user]
	 *	Output is cached
	 * 
	 *	@static
	 *	@param int|Art_Model_User $rights [optional]
	 *	@return Art_Model_Rights[]
	 */
	static function fetchAllIDsNotHigher( $rights = NULL )
	{
		if( NULL === $rights )
		{
			$rights = Art_User::getRights();
		}
		elseif( $rights instanceof Art_Model_User )
		{
			$rights = $rights->getRights();
		}
		
		if( NULL === static::$_fetch_all_ids_not_higher )
		{
			static::$_fetch_all_ids_not_higher = array();
		}
		
		if( !isset(static::$_fetch_all_ids_not_higher[$rights]) )
		{
			static::$_fetch_all_ids_not_higher[$rights] = array();
			
			$items = static::fetchAllNotHigher($rights);
			foreach($items AS $item)
			{
				static::$_fetch_all_ids_not_higher[$rights][] = $item->id;
			}
		}
		
		return static::$_fetch_all_ids_not_higher[$rights];
	}
}