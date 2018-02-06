<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Db_Select extends Art_Abstract_Model_Db_Statement {
	
	/**
	 *	
	 *	List of all cols and it's parameters
	 *		- name =>
	 *		- source =>
	 *		- alias =>
	 *		- function =>
	 * 
	 *	@var array 
	 */
	protected $_list = array();
	
	/**
	 *	Table to select from
	 * 
	 *	@var string 
	 */
	protected $_table = '';
	
	/**
	 *	Cols attributes list
	 */
	const ATTRIBUTES = array('function','name','source','alias');
	
	/**
	 *	Coll name
	 */
	const ATTR_NAME = 'name';
	
	/**
	 *	Col source table
	 */
	const ATTR_SOURCE = 'source';
	
	/**
	 *	Col or function output alias
	 */
	const ATTR_ALIAS = 'alias';
	
	/**
	 *	Function to call
	 */
	const ATTR_FUNCTION = 'function';
	
	
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
	 *	Get list of this statement
	 * 
	 *	@return array
	 */
	function getListPlain()
	{
		return $this->_list;
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
	 *	Set table to select from
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
	 *	Add col or set of cols into select statement
	 *	
	 *	@param array|string $list
	 *	@return Art_Model_Db_Select
	 * 
	 *	@example add('text')
	 *	@example add(array('id','text'))
	 *	@example add(array( 'id', array('name' => 'text', 'alias' => 'my_text') )
	 */
	function add( $list )
	{
		//Statement has changed
		$this->_unlock();
		
		//If is one col
		if( is_string($list) && !empty($list) )
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
					//Is list of attributes - must have NAME or FUNCTION
					elseif( isset($list[self::ATTR_NAME]) || isset($list[self::ATTR_FUNCTION]) )
					{
						$list = array($list);
						break;
					}
				}
				else
				{
					//If col has no name
					if( empty($value[self::ATTR_NAME]) && empty($value[self::ATTR_FUNCTION]) )
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
			$output[] = ( isset($col[self::ATTR_SOURCE]) ? $col[self::ATTR_SOURCE].'.' : '').$col[self::ATTR_NAME];
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
				$sql .= 'SELECT ';
				
				//For each list item
				foreach($this->_list AS $attributes)
				{
					//Col is a function call
					if( !empty($attributes[self::ATTR_FUNCTION]) )
					{
						$sql .= $attributes[self::ATTR_FUNCTION];
					}
					else
					{
						//If source table is set
						if( !empty($attributes[self::ATTR_SOURCE]) )
						{
							$sql.= self::QUOTE_TABLE.$attributes[self::ATTR_SOURCE].self::QUOTE_TABLE.'.';
						}

						//Add col name
						if( !empty($attributes[self::ATTR_NAME]) )
						{
							$sql .= self::QUOTE_COL.$attributes[self::ATTR_NAME].self::QUOTE_COL;
						}
					}

					//If alias is set
					if( !empty($attributes[self::ATTR_ALIAS]) )
					{
						$sql .= ' AS '.self::QUOTE_ALIAS.$attributes[self::ATTR_ALIAS].self::QUOTE_ALIAS;
					}

					$sql .= ', ';
				}
				
				$sql = substr($sql,0,-2).' ';
				
				//Add from
				$sql .= 'FROM '.self::QUOTE_TABLE.$this->_table.self::QUOTE_TABLE.' ';
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