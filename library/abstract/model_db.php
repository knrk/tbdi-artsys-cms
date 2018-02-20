<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/abstract
 *	@abstract
 */
abstract class Art_Abstract_Model_DB implements JsonSerializable {
    use Art_Event_Emitter;
	
    /**
     *  @static
     *  @access protected
     *  @var string Table name
     *  @example 'products'
     */
    protected static $_table = '';
    
    /**
     *  @static
     *  @access protected
     *  @var string Table primary key
     *  @example 'id_product'
     */
    protected static $_primary = self::PRIMARY_COL_NAME;
    
    /**
     *  @static
     *  @access protected
     *  @var array Foreign keys array
     *  @example array('id_country','id_currency')
     */
    protected static $_foreign = array();
    
    /**
     *  Links this table to another one (1:1 bond)
     *  Key = variable name where to store model instance
     *  Value = target model class name
     *  
     *  @static
     *  @access protected
     *  @var array
     *  @example array('currency' => 'Product_Currency')
     */
    protected static $_link = array();
    
	/**
	 *	Sets dependencies to linked tables
	 *	If linked table instance is not found or is not accessible, this instance will be emptied
	 * 
	 *	@static
	 *	@access protected
	 *	@var array()
	 *	@example array('currency')
	 */
	protected static $_dependencies = array();
	
	/**
	 *	Set the models to be fetched to this object
	 *	Key = where to put array of fetched objects
	 *	Value = fetched object name
	 *	
	 *	@static
	 *	@access protected
	 *	@var array
	 *	@example array('attributes'=>'Product_Attribute')
	 */
	protected static $_fetch = array();
	
    /**
     *  List of table columns and their usage
     *  Array of arrays
     *  
     *  @static
     *  @access protected
     *  @var array Columns list
     *  @example array('id_product'=>array('select'),'name'=>array('select','insert','update'))
     */
	protected static $_cols = array();
	
	private static $_cache = array();
	protected static $_caching = true;
	
	/**
	 *	@access protected
	 *	@var bool True if was successfully found and loaded
	 */
	protected $_is_loaded = false;
	
	/**
	 *	Array of variables used to load this instance
	 * 
	 *	@var array
	 */
	protected $_loading_params = array('active_only' => false, 'privileged_to_rights' => NULL);
	
	/**
	 *	List of table cols attributes
	 */
	const COLS_ATTRIBUTES = array('select','insert','update');
	
	/**
	 *	Inverted cols list name
	 */
	const COLS_INVERTED_NAME = '_inverted';
	
	/**
	 *	Parent col name
	 */
	const PARENT_COL_NAME = 'parent_id';
	
	/**
	 *	Parent variable name
	 */
	const PARENT_VAR_NAME = '_parent';
	
	/**
	 *	Default primary col name
	 */
	const PRIMARY_COL_NAME = 'id';
	
	/**
	 *	Child variable name
	 */
	const CHILD_VAR_NAME = '_child';
	
    
	/**
	 *	Magic method override for getters of linked and fetched instances
	 * 
	 *	@param string $name
	 *	@param array $args
	 *	@return Art_Abstract_Model_Db
	 */
	public function __call($name, $args)
	{
		//Is get...
		if( strpos($name, 'get') === 0 )
		{
			//Get called variable name
			$var_name = lcfirst( substr($name, 3) );

			//Is getLinked function call
			if( isset(static::$_link[$var_name]) )
			{
				return $this->getLinkedInstance($var_name);
			}
			//Is getFetched function call
			elseif( isset(static::$_fetch[$var_name] ))
			{
				return $this->getFetchedInstances($var_name);
			}
			else
			{
				trigger_error('Fatal error: Call to undefined method '.get_called_class().'->'.$name, E_USER_ERROR);
			}
		}
		//Is push...
		elseif( strpos($name, 'push') === 0 )
		{
			//Get called variable name
			$var_name = lcfirst(substr($name, 4));
			
			if( isset(static::$_fetch[$var_name] ))
			{
				return $this->pushFetchedInstances($var_name, reset($args));
			}
			else
			{
				trigger_error('Fatal error: Call to undefined method '.get_called_class().'->'.$name, E_USER_ERROR);
			}
		}
		//Is set...
		elseif( strpos($name, 'set') === 0 )
		{
			//Get called variable name
			$var_name = lcfirst( substr($name, 3) );

			//Is linked function call
			if( isset(static::$_link[$var_name] ))
			{
				return $this->setLinkedInstance($var_name, reset($args));
			}
			
			//Is fetched function call
			elseif( isset(static::$_fetch[$var_name] ))
			{
				return $this->setFetchedInstances($var_name, reset($args));
			}
			else
			{
				trigger_error('Fatal error: Call to undefined method '.get_called_class().'->'.$name, E_USER_ERROR);
			}
		}
		elseif( strpos($name, 'removeFrom') === 0 )
		{
			//Get called variable name
			$var_name = lcfirst( substr($name, 10) );
			
			//Is fetched function call
			if( isset(static::$_fetch[$var_name] ))
			{
				return $this->removeFromFetchedInstances($var_name, reset($args));
			}
			else
			{
				trigger_error('Fatal error: Call to undefined method '.get_called_class().'->'.$name, E_USER_ERROR);
			}
		}
		else
		{
			trigger_error('Fatal error: Call to undefined method '.get_called_class().'->'.$name, E_USER_ERROR);
		}
	}
	
	
    /**
     *  Create new model
	 *	If parameters are specified it also calls load
	 * 
     *  @param string|int|array|Art_Model_Db_Select|Art_Abstract_Model_Db $where Identifier or array of identifiers with they respective column names
	 *	@param bool|User $privileged
	 *	@param bool $active_only
	 *	@example load('3') Load article from database WHERE id_article = 3
	 *	@example load(array('name'=>'My foo article')) Load article with name = My foo article
     */
    public function __construct($where = NULL, $privileged = NULL, $active_only = false )
    {		
		//Prepare variables
		$this->cleanData();
				
		//If where is specified
		if( NULL !== $where )
		{
			//Call load
			$this->load($where, $privileged, $active_only);
			
			//Trigger event
			Art_Event::trigger(Art_Event::MODEL_LOAD, $this);
		}
    }
	
	
    /**
	 *	Return true if this instance was sucessfully found and loaded
	 * 
	 *	@final
	 *	@return bool 
	 */
	final function isLoaded()
	{
		return $this->_is_loaded;
	}
	
	
	/**
	 *	Returns true if this instance is active (based on database value)
	 * 
	 *	@final
	 *	@return bool
	 */
	final function isActive()
	{
		if( isset($this->active) )
		{
			return $this->active;
		}
		else
		{
			return true;
		}
	}
	
	
	/**
	 *	Returns true if user is privileged to show this instance
	 *	Returns false if instance is not loaded
	 * 
	 *	@param User [optional] $user
	 *	@return bool
	 */
	function isPrivileged( User $user = NULL )
	{
		//Load current user if not set
		if( NULL === $user )
		{
			$user = Art_User::getCurrentUser();
		}
		
		//This instance must be loaded
		if( $this->isLoaded() )
		{
			//User must be loaded
			if( $user->isLoaded() )
			{
				return $user->hasPrivileges($this);
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	
	/**
	 *	Returns true if this instance is privileged to user and is active
	 * 
	 *	@param User [optional] $user
	 *	@return bool
	 */
	function isPrivilegedActive( User $user = NULL )
	{
		return $this->isActive() && $this->isPrivileged($user);
	}
	
	
    /**
	 *	Returns table name
	 * 
     *	@static
	 *	@final
     *	@return string Table name
     */
    public static final function getTableName()
    {
        return static::$_table;
    }
    
    
    /**
	 *	Returns primary key name
	 * 
     *  @static
	 *	@final
     *  @return string Primary key name
     */
    public static final function getPrimaryName()
    {
        return static::$_primary;
    }
    
	
	/**
	 *	Get primary key value
	 * 
	 *	@final
	 *	@return string|int Primary key value
	 */
	public final function getPrimaryValue()
	{
		return $this->{static::$_primary};
	}
	
	
	/**
	 *	Get rights of this instance
	 *
	 *	@final
	 *	@return int
	 */
	final function getRights()
	{
		if( isset($this->rights) )
		{
			return $this->rights;
		}
		elseif( isset(static::$_link['rights']) )
		{
			return $this->getLinkedInstance('rights')->value;
		}
		elseif( isset(static::$_fetch['rights']) )
		{
			return $this->getFetchedInstances('rights');
		}
		elseif( $this instanceof Art_Model_User )
		{
			if( NULL === $this->_rights_cache )
			{
				$groups = $this->getGroups();

				$max_rights = Art_User::NONREGISTERED;
				foreach( $groups AS $group ) /* @var $group Art_Model_User_X_User_Group */
				{
					$gr = $group->getGroup();
					$rights = $gr->getRights(); /* @var $rights Art_Model_Rights */
					
					if( $rights > $max_rights )
					{
						$max_rights = $rights;
					}
				}
				
				$this->_rights_cache = $max_rights;
			}
			
			return $this->_rights_cache;
		}
		else
		{
			return Art_User::NONREGISTERED;
		}
	}
	
	
	/**
	 *	Set primary key value
	 * 
	 *	@final
	 *	@param string|int $value
	 *	@return Art_Abstract_Model_Db
	 */
	public final function setPrimaryValue($value)
	{
		$this->{static::$_primary} = $value;
		
		return $this;
	}
	
	
	/**
	 *	Returns true if this table has parent col
	 * 
	 *	@final
	 *	@static
	 *	@return bool
	 */
	public final static function hasParentCol()
	{
		return static::hasCol(static::PARENT_COL_NAME);
	}
	
	
	/**
	 *	Returns true if this class has col (by name)
	 *
	 *	@final
	 *	@static
	 *	@param string $col_name
	 *	@return bool
	 */
	public final static function hasCol( $col_name )
	{
		return in_array($col_name, static::getCols());
	}
	
	
	/**
	 *	Returns true if this instance has childs
	 * 
	 *	@return bool	
	 */
	public function hasChilds()
	{
		if( $this->hasParentCol() )
		{
			$inst = static::fetchAll( array( static::PARENT_COL_NAME => $this->{static::$_primary} ), NULL, 1 );

			if( count($inst) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	
    /**
     *  Get cols of this table by attribute
     * 
	 *	@static
     *  @param string $search_attribute (select|insert|update)
     *  @return array
     */
    static function getCols( $search_attribute = NULL)
    {
		//Return all cols
		if( NULL === $search_attribute )
		{
			return array_diff(array_keys(static::$_cols), array(static::COLS_INVERTED_NAME));
		}
        elseif(is_string($search_attribute) && in_array($search_attribute,static::COLS_ATTRIBUTES))
        {
			//Do lazy load
			if( !isset(static::$_cols[static::COLS_INVERTED_NAME]) )
			{
				//Prepare inverted array
				static::$_cols[static::COLS_INVERTED_NAME] = array();
				
				//Prepare arrays
				foreach(static::COLS_ATTRIBUTES AS $attribute)
				{
					static::$_cols[static::COLS_INVERTED_NAME][$attribute] = array();
				}

				//For each col
				foreach(static::$_cols AS $name => $attributes)
				{
					//Skip inverted array
					if($name === static::COLS_INVERTED_NAME)
					{
						continue;
					}
					
					//Store name into associative array like 'select' => 'id','name'
					foreach($attributes AS $attribute)
					{
						static::$_cols[static::COLS_INVERTED_NAME][$attribute][] = $name;
					}
				}
			}
			
			return static::$_cols[static::COLS_INVERTED_NAME][$search_attribute];
        }
        else
        {
			trigger_error('Invalid argument supplied for '.get_called_class().'::getCols()',E_USER_ERROR);
        }
    }
		
	
    /**
     *  Get col name used for WHERE between self and $object
	 *	This function is using foreign cols of this model
     *
     *  @static
     *  @param Art_Abstract_Model_Db|string $object Object to get where col with
     *  @return array
     */
    static function getBondCol($object)
    {
		//If object and self is same - parent call
		if( is_a($object,get_called_class()) )
		{
			return array(self::PARENT_COL_NAME => $object->getPrimaryName());
		}
		
		//If is string
		if( is_object($object) || is_string($object) )
		{
			//If object and method exists
			if( is_a($object,'Art_Abstract_Model_Db') || (class_exists($object) && method_exists($object,'getCols')) )
			{
				//Get external cols
				$ext_primary = $object::getPrimaryName();
				$ext_fk = $object::getForeignCols();
				$ext_table_name = $object::getTableName();
			}
			else
			{
				trigger_error('Object '.(is_string($object)?$object:get_class($object)).' is not child of Art_Abstract_Model_Db, or has not ::getCols() method');
			}
		}
		
		//If primary key name and table name is not empty
		if($ext_primary && $ext_table_name)
		{
			//Get own cols
			$own_primary = static::getPrimaryName();
			$own_fk = static::getForeignCols();
			$own_table_name = static::getTableName();
			
			//Assemble desired foreign column name
			$fk_name_ext = $ext_primary.'_'.$ext_table_name;
			$fk_name_own = $own_primary.'_'.$own_table_name;

			//Return if found
			if( in_array($fk_name_ext, $own_fk) )
			{
				return array($fk_name_ext => $ext_primary);
			}
			if( in_array($fk_name_own, $ext_fk) )
			{
				return array($own_primary => $fk_name_own);
			}
		}
		
		//Return empty array if not found or error
		return array();
    }
    
    
    /**
     *  Returns foreign cols of table
     *
     *  @final
     *  @static
     *  @return array Foreign cols
     */
    final static function getForeignCols()
    {
		//If is string
		if( is_string(static::$_foreign) )
		{
			return array(static::$_foreign);
		}
		//If is array
		else
		{
			return static::$_foreign;    
		}
    }
	
	
	/**
	 *	Get dependencies - instances which will be loaded at the same time with this instance. 
	 *	If those instances won't be found, this instance will be NULLed
	 * 
	 *	@final
	 *	@static
	 *	@return array
	 */
	final static function getDependencies()
	{
		return static::$_dependencies;
	}
    
    
    /**
     *  Returns (associative) array of values from $this instance by list
     * 
     *  @param array|list $list List of values to be parsed from this instance
     *  @param bool $associative If true output will be associative
     *  @return array
     *  @example getValues(array('id','name',true) returns array('id'=>'10','name'=>'My name')
     */
    function getValues($list, $associative = true)
    {        
        $result = array();
        
        //If $list is string, prepare col names by attribute
        if( is_string($list) )
        {
            $list = self::getCols($list);
        }
        
        if( is_array($list) )
        {
            //For each $list
            foreach($list as $col)
            {
                //If this instance has this col
                if( isset($this->$col) )
                {
                    //Is associative
                    if($associative)
                    {
                        $result[$col] = $this->$col; 
                    }
                    else
                    {
                        $result[] = $this->$col; 
                    }
                }
                //Put empty value if this instance dont have this col
                else
                {
                    if($associative)
                    {
                       $result[$col] = ''; 
                    }
                    else
                    {
                        $result[] = '';   
                    }
                }
            }
        }
        else
        {
            trigger_error('Invalid argument supplied for '.get_called_class().'->getValues()',E_USER_ERROR);
        }
        
        return $result;
    }
  	
	
    /**
     *  Fetches instances of this class
     *
     *  @static
     *  @param array|object|Art_Model_Db_Where $where Where SQL statement or abstractModel instance
	 *	@param string|array|Art_Model_Db_Order $order Order by
	 *	@param int|string|Art_Model_Db_Limit $limit
	 *	@param bool|User $privileged
	 *	@param bool $active_only
     *  @return this[] Instances of this class
     *  @example fetchAll(array('where_col_name'=>'value'))
     */
    static function fetchAll($where = NULL, $order = NULL, $limit = NULL, $privileged = NULL, $active_only = false) {

		$returned_instances = array();

		if (static::$_caching === false || empty(self::$_cache[static::$_table])) {
			//Add where
			if (NULL !== $where) {
				//Convert where to statement
				$where_stmt = static::_prepareWhere( $where );
			} else {
				$where_stmt = new Art_Model_Db_Where;
			}
			
			//Add order
			if (NULL !== $order) {
				$order_stmt = static::_prepareOrder( $order );
			} else {
				$order_stmt = new Art_Model_Db_Order;
			}
			
			//Add limit
			if (NULL !== $limit) {
				$limit_stmt = static::_prepareLimit( $limit );
			} else {
				$limit_stmt = new Art_Model_Db_Limit;
			}
			
			//Add privileged
			if (NULL !== $privileged) {
				//Get rights of privileged user
				$privilegedRights = static::_getPrivilegedRights($privileged);
				
				//Add col to WHERE
				if (static::hasCol('rights')) {
					$where_stmt->add(array('name' => 'rights', 'value' => $privilegedRights, 'relation' => '<='));
				}
				elseif (($col_name = in_array('Art_Model_Rights', static::$_link) ) !== false) {
					$bond_col = static::getBondCol('Art_Model_Rights');
					if (!empty($bond_col)) {
						$bond_col = key($bond_col);
						$ids = Art_Model_Rights::fetchAllIdsNotHigher( $privilegedRights );
						$where_stmt->add( array('name' => $bond_col, 'value' => $ids, 'relation' => Art_Model_Db_Where::REL_IN) );
					} else {
						trigger_error('No bond col found between '.__CLASS__.' and Art_Model_Rights');
					}
				}
			} else {
				$privilegedRights = 0;
			}
			
			//Add active
			if ($active_only && static::hasCol('active')) {
				$where_stmt->add(array('name' => 'active', 'value' => 1));
			}
			
			
			//Add selected cols 
			$select_stmt = new Art_Model_Db_Select(static::$_table, static::getCols('select'));
			
			//Build up query and execute
			$request = Art_Main::db()->select($select_stmt, $where_stmt, $order_stmt, $limit_stmt);
			$request->execute($where_stmt->getValues());

			//For each found instance
			while ($data = $request->fetch(PDO::FETCH_ASSOC)) {
				//Create instance
				$instance = new static();
				$instance->_is_loaded = true;
				
				//Bind all fetched values to instance variables
				foreach ($data AS $name => $value) {
					$instance->$name = $value;	
				}

				//Look for dependend tables
				foreach ($instance::$_dependencies AS $dependend_var) {
					//Get instance
					$dep_instance = $instance->getLinkedInstance($dependend_var);

					//Clean $this if instance was not loaded
					if (!$dep_instance->isLoaded() ||
						( $active_only && $dep_instance->isActive() ) ||
						( NULL !== $privileged && $dep_instance->getRights() > $privilegedRights ) )
					{
						$instance = NULL;
						break;
					}
				}
				
				//If not null - add to output
				if (NULL !== $instance) {
					$returned_instances[] = $instance;
				}
			}

			if (static::$_caching === true) {
				self::$_cache[static::$_table] = $returned_instances;
			}
		} else {
			$returned_instances = self::$_cache[static::$_table];
		}

		// if (static::$_table === 'service_payment') {
		// 	p(static::$_caching);
		// 	d(self::$_cache[static::$_table]);
		// }
		
		return $returned_instances;
	}

	/**
     *  Fetches instances of this class
     *
     *  @static
     *  @param array|object|Art_Model_Db_Where $where Where SQL statement or abstractModel instance
	 *	@param string|array|Art_Model_Db_Order $order Order by
	 *	@param int|string|Art_Model_Db_Limit $limit
	 *	@param bool|User $privileged
	 *	@param bool $active_only
     *  @return this[] Instances of this class
     *  @example fetchAll(array('where_col_name'=>'value'))
     */
    static function fetchSelected($cols = null, $where = NULL, $order = NULL, $limit = NULL, $privileged = NULL, $active_only = false)
    {
		//Add where
		if (NULL !== $where) {
			//Convert where to statement
			$where_stmt = static::_prepareWhere($where);
		} else {
			$where_stmt = new Art_Model_Db_Where;
		}
		
		//Add order
		if (NULL !== $order) {
			$order_stmt = static::_prepareOrder( $order );
		} else {
			$order_stmt = new Art_Model_Db_Order;
		}
		
		//Add limit
		if (NULL !== $limit) {
			$limit_stmt = static::_prepareLimit( $limit );
		} else {
			$limit_stmt = new Art_Model_Db_Limit;
		}
		
		//Add privileged
		if (NULL !== $privileged) {
			//Get rights of privileged user
			$privilegedRights = static::_getPrivilegedRights($privileged);
			
			//Add col to WHERE
			if (static::hasCol('rights')) {
				$where_stmt->add(array('name' => 'rights', 'value' => $privilegedRights, 'relation' => '<='));
			}
			elseif (($col_name = in_array('Art_Model_Rights', static::$_link)) !== false) {
				$bond_col = static::getBondCol('Art_Model_Rights');
				if (!empty($bond_col)) {
					$bond_col = key($bond_col);
					$ids = Art_Model_Rights::fetchAllIdsNotHigher( $privilegedRights );
					$where_stmt->add( array('name' => $bond_col, 'value' => $ids, 'relation' => Art_Model_Db_Where::REL_IN) );
				} else {
					trigger_error('No bond col found between '.__CLASS__.' and Art_Model_Rights');
				}
			}
		} else {
			$privilegedRights = 0;
		}
		
		//Add active
		if ($active_only && static::hasCol('active')) {
			$where_stmt->add(array('name' => 'active', 'value' => 1));
		}

		if (NULL === $select) {
			$cols = static::getCols('select');
		}

		// $select_stmt = new Art_Model_Db_Select(static::$_table, static::getCols('select'));
		$select_stmt = new Art_Model_Db_Select(static::$_table, $cols);
		// printr($select_stmt);
		
		
		//Build up query and execute
		$request = Art_Main::db()->select($select_stmt,$where_stmt,$order_stmt,$limit_stmt);
		$request->execute($where_stmt->getValues());

		//For each found instance
		$returned_instances = array();
		while ($data = $request->fetch(PDO::FETCH_ASSOC)) {
		//Create instance
			$instance = new static();
			$instance->_is_loaded = true;

            //Bind all fetched values to instance variables
            foreach ($data as $name => $value) {
                $instance->$name = $value;	
            }

			//Look for dependend tables
			foreach ($instance::$_dependencies as $dependend_var) {
				//Get instance
				$dep_instance = $instance->getLinkedInstance($dependend_var);

				//Clean $this if instance was not loaded
				if( !$dep_instance->isLoaded() ||
					( $active_only && $dep_instance->isActive() ) ||
					( NULL !== $privileged && $dep_instance->getRights() > $privilegedRights ) )
				{
					$instance = NULL;
					break;
				}
			}
			
			//If not null - add to output
			if (NULL !== $instance) {
				$returned_instances[] = $instance;
			}
		}
		
		return $returned_instances;
	}
	
	
	/**
	 *	Fetch all instances that are privileged to user and active
	 *	Only those with rights equals or less, active 1
	 *	Can be used for instances without "rights" or "active" cols
	 * 
	 *	@param string|array|Art_Model_Db_Where $where
	 *	@param string|Art_ModeL_Db_Order $order
	 *	@param string|int|Art_Model_Db_Limit $limit
	 *	@return this[]
	 */
	static function fetchAllPrivilegedActive($where = NULL, $order = NULL, $limit = 0)
	{
		return static::fetchAll($where,$order,$limit,true,true);
	}
	
	
	/**
	 *	Fetch all instances privileged to current user
	 *	Only those with rights equals or less, regardless active status
	 * 
	 *	@param string|array|Art_Model_Db_Where $where
	 *	@param string|Art_ModeL_Db_Order $order
	 *	@param string|int|Art_Model_Db_Limit $limit
	 *	@return this[]
	 */
	static function fetchAllPrivileged($where = NULL, $order = NULL, $limit = 0)
	{
		return static::fetchAll($where,$order,$limit,true,false);
	}
	
	
	/**
	 *	Load data from database
	 *
     *  @param array|object $where Where SQL statement or abstractModel instance
	 *	@param bool|User $privileged
	 *	@param bool $active_only
     *  @return this
     *  @example load(array('where_col_name'=>'value'))
	 */
	public function load( $where, $privileged = NULL, $active_only = false )
	{		
		//Convert where to statement
		$where_stmt = static::_prepareWhere( $where );
		
		//Add privileged
		if( NULL !== $privileged )
		{
			//Get rights of privileged user
			$privilegedRights = static::_getPrivilegedRights($privileged);
			
			//Add col to WHERE
			if( static::hasCol('rights') )
			{
				$where_stmt->add(array('name' => 'rights', 'value' => $privilegedRights, 'relation' => '<='));
			}
			
			//Save privileged rights
			$this->_loading_params['privileged_to_rights'] = $privilegedRights;
		}
		else
		{
			$privilegedRights = 0;
		}
		
		//Save active only
		$this->_loading_params['active_only'] = $active_only;
		
		//Add active
		if( $active_only && static::hasCol('active') )
		{
			$where_stmt->add(array('name' => 'active', 'value' => 1));
		}
		
		//Add selected cols 
		$select_stmt = new Art_Model_Db_Select(static::$_table, static::getCols('select'));
		
		//Build up query and execute
		$request = Art_Main::db()->select($select_stmt,$where_stmt);
		$request->execute($where_stmt->getValues());
		
		//If data is found
		if($data = $request->fetchAll(PDO::FETCH_ASSOC))
		{
			//More rows found - error
			switch( count($data) )
			{
				case 1 : 
				{
					$this->_is_loaded = true;
					
					//Store each variable from array to instance
					foreach($data[0] AS $name => $value)
					{
						$this->$name = $value;
					}	
					
					//Look for dependend tables
					foreach(static::$_dependencies AS $dependend_var)
					{
						//Get instance
						$dep_instance = $this->getLinkedInstance($dependend_var);
						
						//Clean $this if instance was not loaded
						if( !$dep_instance->isLoaded() ||
							( $active_only && $dep_instance->isActive() ) ||
							( NULL !== $privileged && $dep_instance->getRights() > $privilegedRights ) )
						{
							$this->cleanData();
							return NULL;
						}
					}
				}
				case 0 : 
				{
					break;
				}
				default :
				{
					trigger_error('More than one instance found in '.get_called_class().'->load(). Returning first one',E_USER_NOTICE);
				}
			}
		}
		
		return $this;
	}


    /**
     *  Save instance to DB
     *
     *  @return this
     */
	function save()
    {
		$is_update = (bool)$this->getPrimaryValue();
		
		//Trigger event
		Art_Event::trigger(Art_Event::MODEL_SAVE_BEFORE, $this);
		if( $is_update )
		{
			Art_Event::trigger(Art_Event::MODEL_UPDATE_BEFORE, $this);
		}
		else
		{
			Art_Event::trigger(Art_Event::MODEL_INSERT_BEFORE, $this);
		}
		
		//Set created by and modified by cols if exists
		if( static::hasCol('created_by') && !$this->created_by && !$this->getPrimaryValue() )
		{
			$this->created_by = Art_User::getId();
		}
		elseif( static::hasCol('modified_by') )
		{
			$this->modified_by = Art_User::getId();
		}		
		
		//Add insert
		$insert_stmt = new Art_Model_Db_Insert(static::$_table, static::getCols('insert'));
		
		//Add update
		$update_stmt = new Art_Model_Db_Update(static::$_table, static::getCols('update'));
		
		//Buiild up query
		$request = Art_Main::db()->insertOnDuplicateKeyUpdate($insert_stmt,$update_stmt);
		$request->execute($this->getValues(array_merge(static::getCols('insert'),static::getCols('update')), true));
		
		//Save ID to instance for later use
		if($last_id = static::lastInsertId())	
		{
			$this->{static::$_primary} = $last_id;
		}

		//Trigger event
		Art_Event::trigger(Art_Event::MODEL_SAVE_AFTER, $this);
		if( $is_update )
		{
			Art_Event::trigger(Art_Event::MODEL_UPDATE_AFTER, $this);
		}
		else
		{
			Art_Event::trigger(Art_Event::MODEL_INSERT_AFTER, $this);
		}
		
		//Fake load of this instance
		$this->_is_loaded = true;
		
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
        //Build up query and execute if ID is set
        if( !empty($this->{static::$_primary}))
        {        
			//Trigger event
			Art_Event::trigger(Art_Event::MODEL_DELETE, $this);
			
			//Add delete
			$delete_stmt = new Art_Model_Db_Delete(static::$_table);

			//Add where
			$where_stmt = new Art_Model_Db_Where(array('name' => static::$_primary, 'value' => $this->{static::$_primary}));
			
            $request = Art_Main::db()->delete($delete_stmt, $where_stmt);
            $request->execute($where_stmt->getValues());
        }        
        
        //Empty instance variables
        $this->cleanData();
		
        return $this;
    }
	
	
	/**
	 *	Dump model to string
	 *	If short, only ID will be dumped
	 * 
	 *	@param bool [optional] $short
	 *	@return string
	 */
	function dump( $short = false )
	{
		if( $short )
		{
			return json_encode(array(static::getPrimaryName() => $this->getPrimaryValue()));
		}
		else
		{
			return json_encode($this->getValues('select'));
		}
	}
	
	
	/**
	 *	Set linked instance
	 * 
	 *	@param string $name
	 *	@param Art_Abstract_Model_DB $item
	 *	@return this
	 */
	public function setLinkedInstance( $name, Art_Abstract_Model_DB $item )
	{				
		$bond = $this->getBondCol($item);
		
		if( count($bond) )
		{			
			if( isset($this::$_link[$name]) )
			{
				//Save primary key
				$this->{key($bond)} = $item->getPrimaryValue();

				//Save cache
				$this->{'_'.$name} = $item;
			}
			else
			{
				trigger_error('Unknown link between '.static::class.' and '.get_class($item), E_USER_ERROR);
			}
		}
		else
		{
			trigger_error('No bond col found between '.static::class.' and '.get_class($item), E_USER_ERROR);
		}
		
		return $this;
	}
	
	
	/**
	 *	Set fetched instances
	 * 
	 *	@param string $name
	 *	@param Art_Abstract_Model_Db|Art_Abstract_Model_Db[] $items
	 *	@return this
	 */
	public function setFetchedInstances( $name, $items )
	{	
		$variable = '_'.$name;

		//Convert to array
		if( !is_array($items) )
		{
			$items = array( $items );
		}
		
		$this->{$variable} = $items;
		
		$linked_name = NULL;
		$func_name = NULL;
		
		//Add items
		foreach($items AS $item)
		{			
			//Get linked name
			if( NULL === $linked_name )
			{
				$linked_name = array_search_insensitive(static::class, $item::$_link);
				if( $linked_name )
				{
					$func_name = 'set'.$linked_name;
				}
				else
				{
					trigger_error('Unknown link between '.static::class.' and '.get_class($item), E_USER_ERROR);
				}		
			}
			
			$item->{$func_name}($this);
		}
		
		return $this;
	}
	
	
	/**
	 *	Add instance to array of instances
	 * 
	 *	@param string $name
	 *	@param Art_Abstract_Model_Db|Art_Abstract_Model_Db[] $items
	 *	@return this
	 */
	public function pushFetchedInstances( $name, $items )
	{
		$variable = '_'.$name;

		//Convert to array
		if( !is_array($items) )
		{
			$items = array( $items );
		}
		
		//Create cache array if not exists
		if( !isset( $this->{$variable} ) )
		{
			$this->{$variable} = array();
		}
		
		$linked_name = NULL;
		$func_name = NULL;
		
		//Add items
		foreach($items AS $item)
		{
			$this->{$variable}[] = $item;
			
			//Get linked name
			if( NULL === $linked_name )
			{
				$linked_name = array_search_insensitive(static::class, $item::$_link);
				if( $linked_name )
				{
					$func_name = 'set'.$linked_name;
				}
				else
				{
					trigger_error('Unknown link between '.static::class.' and '.get_class($item), E_USER_ERROR);
				}		
			}
			
			$item->{$func_name}($this);
		}
		
		return $this;
	}
	
	
	/**
	 *	Remove instance from fetched instances
	 * 
	 *	@param string $name
	 *	@param Art_Abstract_Model_Db|Art_Abstract_Model_Db[] $items
	 *	@return this
	 */
	public function removeFromFetchedInstances( $name, $items )
	{
		$variable = '_'.$name;

		//Convert to array
		if( !is_array($items) )
		{
			$items = array($items);
		}
		
		//If caches are set
		if( isset($this->$variable) )
		{
			//For each cache instsance
			foreach( $this->$variable AS $key => $searched_item )
			{
				//For each input instance
				foreach($items AS $item)
				{
					if( $item === $searched_item )
					{					
						unset($this->{$variable}[$key]);

						break;
					}
				}
			}
		}
		
		return $this;
	}
	
	
	/**
	 *	Get instance linked to this instance by using $_link array
	 * 
	 *	@param string $name
	 *	@return Art_Abstract_Model_Db
	 */
	public function getLinkedInstance( $name )
	{
		//If name is valid
		if( isset(static::$_link[$name]) )
		{
			$variable = '_'.$name;

			//If instance is not loaded yet
			if( !isset($this->$variable) )
			{
				$class = static::$_link[$name];

				$bond = static::getBondCol($class);

				//If bond col is found
				if( isset($this->{key($bond)}) )
				{
					$this->$variable = new $class(array(reset($bond) => $this->{key($bond)}), $this->_loading_params['privileged_to_rights'], $this->_loading_params['active_only']);
				}
				else
				{
					trigger_error(get_called_class().' and '.$class.' cannot be linked',E_USER_ERROR);
				}
			}

			return $this->$variable;
		}
		else
		{
			trigger_error(get_called_class().'->getLinkedInstance error: No '.$name.' link',E_USER_ERROR);
		}
	}
	
	
	/**
	 *	Get fetched instances to this instance by using $_fetch array
	 * 
	 *	@param string $name
	 *	@return Art_Abstract_Model_Db
	 */
	public function getFetchedInstances( $name )
	{
		//If name is valid
		if( isset(static::$_fetch[$name]) )
		{
			$variable = '_'.$name;

			//If instances are not loaded yet
			if( !isset($this->$variable) )
			{
				$class = static::$_fetch[$name];

				//Fetch $_fetch classes
				$this->$variable = $class::fetchAll($this, NULL, NULL, $this->_loading_params['privileged_to_rights'], $this->_loading_params['active_only']);
			}

			return $this->$variable;
		}
		else
		{
			trigger_error(get_called_class().'->getFetchedInstances error: No '.$name.' fetch link',E_USER_ERROR);
		}
	}
	
	
	/**
	 *	Get parent instance of this instance
	 * 
	 *	@return Art_Abstract_Model_Db
	 */
	public function getParent()
	{
		//If parent is loaded
		if( isset($this->{static::PARENT_VAR_NAME}) )
		{
			return $this->{static::PARENT_VAR_NAME};
		}
		//If this table has parents
		elseif( static::hasParentCol() )
		{
			$this->{static::PARENT_VAR_NAME} = new static($this->{static::PARENT_COL_NAME},$this->_loading_params['privileged_to_rights'],$this->_loading_params['active_only']);
			return $this->{static::PARENT_VAR_NAME};
		}
		//This table is not having parents
		else
		{
			return NULL;
		}
	}
	
	
	/**
	 *	Get rights of privileged user
	 * 
	 *	@static
	 *	@param User|bool|int $privileged
	 *	@return int
	 */
	protected static function _getPrivilegedRights( $privileged )
	{
		//Get user
		if( true === $privileged )
		{
			return Art_User::getRights();
		}
		elseif( is_a($privileged,'User') )
		{
			return $privileged->getRights();
		}
		elseif( is_numeric($privileged) )
		{
			return $privileged;
		}
		else
		{
			trigger_error('Invalid privileged argument supplied to '.get_called_class().'::fetchAll()',E_USER_ERROR);
		}		
	}
	
	
	/**
	 *	Set data from array into this instance
	 * 
	 *	@param array $data
	 *	@return \Art_Abstract_Model_Db
	 */
	function setDataFromArray( array $data )
	{
		foreach($data AS $name => $value)
		{
			$this->$name = $value;
		}
		
		return $this;
	}
	
	
	/**
	 *	Prepare clean data for this instance
	 * 
	 *	@return Art_Abstract_Model_Db
	 */
	function cleanData()
	{
		foreach(self::getCols() AS $col)
		{
			$this->$col = NULL;
		}
		
		$this->_is_loaded = false;
		
		return $this;
	}
	
	
	/**
	 *	Get values for json_encode()
	 * 
	 *	@return array
	 */
	function jsonSerialize()
	{
		return $this->getValues('select');
	}
	
	
	/**
	 *	Convert limit int|Art_Model_Db_Limit into Art_Model_Db_Limit
	 * 
	 *	@access protected
	 *	@static
	 *	@param int|Art_Model_Db_Limit into Art_Model_Db_Limit $limit
	 *	@return Art_Model_Db_Limit
	 */
	protected static function _prepareLimit( $limit )
	{
		if( is_int($limit) )
		{
			return new Art_Model_Db_Limit($limit);
		}
		elseif( is_a($limit,'Art_Model_Db_Limit') )
		{
			return $limit;
		}
		elseif( is_string($limit) )
		{
			$exlimit = explode(',',$limit);
			switch( count($exlimit) )
			{
				case 1:
				{
					return new Art_Model_Db_Limit($limit);
				}
				case 2:
				{
					return new Art_Model_Db_Limit($exlimit[0],$exlimit[1]);
				}
				default:
				{
					trigger_error('Invalid argument supplied for '.get_called_class().'->_prepareLimit()',E_USER_ERROR);
				}
			}
		}
		else
		{
			trigger_error('Invalid argument supplied for '.get_called_class().'->_prepareLimit()',E_USER_ERROR);
		}
	}
	
	
	/**
	 *	Convert order array|string|Art_Model_Db_Order into Art_Model_Db_Order
	 * 
	 *	@access protected
	 *	@static
	 *	@param array|string|Art_Model_Db_Order|Art_Abstract_ModeL_Db $order
	 *	@return Art_Model_Db_Order
	 */
	protected static function _prepareOrder( $order )
	{
		switch( gettype($order) )
		{
			case 'string':
			case 'array' :
			{
				$order_stmt = new Art_Model_Db_Order($order);
				break;
			}
			case 'object' :
			{
				if( is_a($order,'Art_Model_Db_Order') )
				{
					$order_stmt = $order;
					break;
				}
				else
				{
					trigger_error('Invalid $where argument supplied for '.get_called_class().'->_prepareOrder()',E_USER_ERROR);
				}
			}
			default :
			{
				trigger_error('Invalid $where argument supplied for '.get_called_class().'->_prepareOrder()',E_USER_ERROR);	
			}
		}
		
		return $order_stmt;
	}
	
	
	/**
	 *	Convert where array|string|int|Art_Model_Db_Select|Art_Abstract_ModeL_Db into Art_Model_Db_Select
	 * 
	 *	@access protected
	 *	@static
	 *	@param array|string|int|Art_Model_Db_Select|Art_Abstract_ModeL_Db $where
	 *	@return Art_Model_Db_Where
	 */
	protected static function _prepareWhere( $where )
	{
		//Prepare where
		switch( gettype($where) )
		{
			case 'integer' :
			case 'double' :
			case 'string' :
			{
				$where_stmt = new Art_Model_Db_Where( array( 'name' => static::$_primary, 'value' => $where ) );
				break;
			}
			case 'array' :
			{
				$where_stmt = new Art_Model_Db_Where( array_keys($where) );
				$where_stmt->addValues($where);
				break;
			}
			case 'object' : 
			{
				if( is_a($where, 'Art_Model_Db_Where') )
				{
					$where_stmt = $where;
				}
				elseif( is_a($where, 'Art_Abstract_Model_DB') )
				{
					//Find bond col
					$bond_col = static::getBondCol($where);
					if(count($bond_col))
					{
						$where_stmt = new Art_Model_Db_Where( array('name' => key($bond_col), 'value' => $where->{reset($bond_col)}) );
					}
					else
					{
						trigger_error('Invalid $where argument supplied for '.get_called_class().'->prepareWhere(): No bond cols between '.  get_called_class().' and '.get_class($where),E_USER_ERROR);
					}					
				}
				else
				{
					trigger_error('Invalid $where argument supplied for '.get_called_class().'->prepareWhere()',E_USER_ERROR);
				}
				break;
			}
			default :
			{
				trigger_error('Invalid $where argument supplied for '.get_called_class().'->prepareWhere()',E_USER_ERROR);
			}
		}
		
		return $where_stmt;
	}
	
	
	/**
	 *	Returns next insert id of this model/table
	 * 
	 *	@return int
	 */
	static function nextInsertId()
	{
		Art_Main::db()->nextInsertId(static::getTableName());
	}
	
	
	/**
	 *	Returns last insert ID of this model/table
	 * 
	 *	@return int
	 */
	static function lastInsertId()
	{
		return Art_Main::db()->lastInsertId(static::$_table);
	}
}
