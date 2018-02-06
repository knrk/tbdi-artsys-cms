<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Db_Order extends Art_Abstract_Model_Db_Statement {
	
	/**
	 *	
	 *	List of all cols and it's parameters
	 *		- name =>
	 *		- source =>
	 *		- type =>
	 * 
	 *	@access protected
	 *	@var array 
	 */
	protected $_list = array();
	
	/**
	 *	Cols attributes list
	 */
	const ATTRIBUTES = array('name','source','type','function');
	
	/**
	 *	Coll name
	 */
	const ATTR_NAME = 'name';
	
	/**
	 *	Col source table
	 */
	const ATTR_SOURCE = 'source';
	
	/**
	 *	Col type (ASC / DESC)
	 */
	const ATTR_TYPE = 'type';
	
	/**
	 *	Function to call
	 */
	const ATTR_FUNCTION = 'function';
	
	const TYPE_ASC = 'ASC';
	const TYPE_DESC = 'DESC';
	
	
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
	 *	@param array $list
	 */
	function __construct( $list = NULL )
	{
		if( NULL !== $list )
		{
			$this->add($list);	
		}
	}
	
	
	/**
	 *	Add col or set of cols into select statement
	 *	
	 *	@param array|string $list
	 *	@return Art_Model_Db_Order_By
	 * 
	 *	@example add('text')
	 *	@example add(array('id','text'))
	 *	@example add(array( 'id', array('name' => 'text', 'source' => 'my_text', 'type' => 'DESC') )
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
						//List must have NAME
						if( isset($list[self::ATTR_NAME]) || isset($list[self::ATTR_FUNCTION]) )
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
	 *	Set random ordering
	 * 
	 *	@return Art_Model_Db_Order_By
	 */
	function setRandom()
	{
		$this->_unlock();
		$this->_list = array(array('function' => 'RAND()'));
		
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
				$sql .= 'ORDER BY ';
				
				//For each list item
				foreach($this->_list AS $attributes)
				{
					if( empty($attributes[self::ATTR_FUNCTION]) )
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
					else
					{
						//If source table is set
						if( !empty($attributes[self::ATTR_FUNCTION]) )
						{
							$sql.= $attributes[self::ATTR_FUNCTION];
						}
					}
					//Add type
					if( !empty($attributes[self::ATTR_TYPE]) )
					{
						$sql .= ' '.$attributes[self::ATTR_TYPE];
					}

					$sql .= ', ';
				}
				
				$sql = substr($sql,0,-2).' ';
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