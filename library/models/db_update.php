<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Db_Update extends Art_Abstract_Model_Db_Statement {
	
	/**
	 *	
	 *	List of all cols and it's parameters
	 *		- name =>
	 * 
	 *	@access protected
	 *	@var array 
	 */
	protected $_list = array();
	
	/**
	 *	Table to insert into
	 * 
	 *	@var string 
	 */
	protected $_table = '';
	
	/**
	 *	If true, a 'ON DUPLICATE KEY' will be added
	 * 
	 *	@var bool
	 */
	protected $_on_duplicate_key = false;
	
	/**
	 *	Cols attributes list
	 */
	const ATTRIBUTES = array('name');
	
	/**
	 *	Coll name
	 */
	const ATTR_NAME = 'name';
	
	
	/**
	 *	Get list of this statement
	 * 
	 *	@return array
	 */
	function getListPlain()
	{
		return $this->_list;
	}
	
	
	/**
	 *	Get table name
	 *
	 *	@return string
	 */
	function getTableName()
	{
		return $this->_table;
	}
	
	
	/**
	 *	@param string $table
	 *	@param array $list
	 */
	function __construct( $table = NULL, $list = NULL )
	{
		if( NULL !== $table )
		{
			$this->setTable($table);
		}
		
		if( NULL !== $list )
		{
			$this->add($list);	
		}
	}
	
	
	/**
	 *	Set table to insert into
	 * 
	 *	@param string $table
	 *	@return Art_Model_Db_Select
	 */
	function setTable( $table )
	{
		$this->_unlock();
		$this->_table = $table;
		
		return $this;
	}
	
	
	/**
	 *	Adds 'ON DUPLICATE KEY' on the beginning of query
	 * 
	 *	@return Art_Model_Db_Update
	 */
	function setOnDuplicateKey()
	{
		$this->_on_duplicate_key = true;
		
		return $this;
	}
	
	
	/**
	 *	Add col or set of cols into insert statement
	 *	
	 *	@param array|string $list
	 *	@return Art_Model_Db_Insert
	 * 
	 *	@example add('text')
	 *	@example add(array('id','text'))
	 *	@example add(array( 'id', array('name' => 'text') )
	 */
	function add( $list )
	{
		//Statement has changed
		$this->_unlock();
		
		//If is one col
		if( is_string ($list) )
		{
			//Append to list array
			$this->_list[] = array( self::ATTR_NAME => $list );
		}
		elseif( is_array($list) )
		{	
			//Prepare indexed array
			foreach($list AS $key => $value)
			{
				//If value is not array
				if(!is_array($value))
				{
					//Is list of col names
					if(is_int($key))
					{
						if( !empty($value) )
						{
							$list[$key] = array(self::ATTR_NAME => $value);
						}
						else
						{
							unset($list[$key]);
						}
					}
					//Is list of attributes
					else
					{
						//If list has name - is list of one col attributes
						if( isset($list[self::ATTR_NAME]) )
						{							
							$list = array($list);
							break;
						}
					}
				}
				else
				{
					//If col has no name
					if( empty($value[self::ATTR_NAME]) )
					{							
						unset($list[$key]);
					}
				}
			}
			
			//Append array after list array
			$this->_list = array_merge($this->_list,$list);
		}
		
		return $this;
	}
	
	
	/**
	 *	Return col sources and names
	 * 
	 *	@return array
	 */
	function getCols()
	{
		$output = array();
		
		//For each col
		foreach($this->_list AS $col)
		{
			//Add col and it's source to output
			$output[] = $col[self::ATTR_NAME];
		}
		
		return $output;
	}
	
	
	/**
	 *	Return SQL of this statement
	 * 
	 *	@return string
	 */
	function getSQL()
	{
		//If is not locked - not buffered
		if(	!$this->_isLocked() )
		{
			//Lock
			$this->_lock();

			$sql = '';

			if( $this->notEmpty() )
			{
				
				if( $this->_on_duplicate_key )
				{
					//Add ON DUPLICATE KEY
					$sql .= 'ON DUPLICATE KEY UPDATE ';
				}
				else
				{
					//Add statement and table
					$sql .= 'UPDATE '.self::QUOTE_TABLE.$this->_table.self::QUOTE_TABLE.' SET ';
				}
			
				
				//For each list item
				$update_cols = '';
				foreach($this->_list AS $attributes)
				{
					//Add col name and value alias			
					$update_cols .= self::QUOTE_COL.$attributes[self::ATTR_NAME].self::QUOTE_COL.' = :'.$attributes[self::ATTR_NAME].', ';
				}
				
				//Remove tailing comma
				if( strlen($update_cols) )
				{
					$sql .= substr($update_cols,0,-2);
				}
				
				$sql .= ' ';
			}

			$this->_saveSQL($sql);
		}
		
		return $this->_getSavedSQL();
	}
	
	
	/**
	 *	Return true if this statement is not empty
	 * 
	 *	@return bool
	 */
	function notEmpty() 
	{
		return !empty($this->_list);
	}
}