<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Db_Where extends Art_Abstract_Model_Db_Statement {
	
	/**
	 *	
	 *	List of all cols and it's parameters
	 *		- name =>
	 *		- value =>
	 *		- source =>
	 *		- alias =>
	 *		- relation =>
	 *		- operation =>
	 * 
	 *	@access protected
	 *	@var array 
	 */
	protected $_list = array();
	
	/**
	 *	Values used when executing statement
	 * 
	 *	@var array 
	 */
	protected $_values = array();
	
	/**
	 *	Cols attributes list
	 */
	const ATTRIBUTES = array('name','value','source','alias','relation','operation');
	
	/**
	 *	Coll name
	 */
	const ATTR_NAME = 'name';
	
	/**
	 *	Col value
	 */
	const ATTR_VALUE = 'value';
	
	/**
	 *	Col value alias
	 */
	const ATTR_ALIAS = 'alias';
	
	/**
	 *	Col source table
	 */
	const ATTR_SOURCE = 'source';
	
	/**
	 *	Col relation type
	 */
	const ATTR_RELATION = 'relation';
	
	/**
	 *	Col operation type
	 */
	const ATTR_OPERATION = 'operation';
	
	
	const REL_LIKE = 'LIKE';
	const REL_EQUALS = '=';
	const REL_NOT_EQUALS = '!=';
	const REL_GREATER = '>';
	const REL_LESS = '<';
	const REL_GREATER_EQUALS = '>=';
	const REL_LESS_EQUALS = '<=';
	const REL_IN = 'IN';
	
	const REL_DEFAULT = self::REL_EQUALS;
	
	const OP_AND = 'AND';
	const OP_OR = 'OR';
	const OP_XOR = 'XOR';
	const OP_NOT = 'NOT';
	
	const OP_DEFAULT = self::OP_AND;
	
	
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
	 *	Return associative list of values
	 * 
	 *	@return array
	 */
	function getValues()
	{
		return $this->_values;
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
	 *	Add col or set of cols into where statement
	 *	
	 *	@param array|string $list
	 *	@return Art_Model_Db_Where
	 * 
	 *	@example add('text')
	 *	@example add(array('id','text'))
	 *	@example add(array( 'id', array('name' => 'text','source' => 'products') )
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
					//Is list of attributes
					else
					{
						//If list has name - is list of one col attributes
						if( isset($list[self::ATTR_NAME]) )
						{						
							if( isset($list[self::ATTR_VALUE]) )
							{
								$this->addValue($list[self::ATTR_NAME], $list[self::ATTR_VALUE]);
							}
								
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
					//Add value if set
					elseif( isset($value[self::ATTR_VALUE]) )
					{
						if( !empty($value[self::ATTR_ALIAS]) )
						{
							$this->addValue($value[self::ATTR_ALIAS], $value[self::ATTR_VALUE]);
						}
						else
						{
							$this->addValue($value[self::ATTR_NAME], $value[self::ATTR_VALUE]);
						}
					}
				}
			}
			
			//Append array after list array
			$this->_list = array_merge($this->_list,$list);
		}
		
		return $this;
	}
		
	
	/**
	 *	Add field and it's value [optional]
	 * 
	 *	@static
	 *	@param string $name
	 *	@param string|int [optional] $value
	 *	@return \Art_Model_Db_Where
	 */
	function addField( $name, $value = NULL )
	{
		$this->add($name);
		
		if( NULL !== $value )
		{
			$this->addValue($name, $value);
		}
			
		return $this;
	}
	
	/**
	 *	Add value to use when executing statement
	 *	
	 *	@param string $name
	 *	@param string $value
	 *	@return Art_Model_Db_Where
	 */
	function addValue( $name, $value )
	{
		$this->_values[$name] = $value;
		
		return $this;
	}
	
	
	/**
	 *	Add associative list of values to use when executing statement
	 *	
	 *	@param array $list
	 *	@return Art_Model_Db_Where
	 */	
	function addValues( array $list )
	{
		$this->_values = array_merge($this->_values,$list);
		
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
				if( NULL !== $this->_plain )
				{
					$sql = $this->_plain.' ';
				}
				else
				{
					$sql .= 'WHERE ';

					//For each list item
					$i = 0;
					foreach($this->_list AS &$attributes)
					{
						//True if is IN statement
						$is_in_statement = isset($attributes[self::ATTR_RELATION]) && strtolower($attributes[self::ATTR_RELATION]) === strtolower(self::REL_IN);
						
						//Add operation
						if( !empty($attributes[self::ATTR_OPERATION]) )
						{
							$sql .= $attributes[self::ATTR_OPERATION].' ';
						}
						//If not set and not first - ADD default operation
						elseif( $i != 0 )
						{
							$sql .= ' '.self::OP_DEFAULT.' ';
						}				

						$alias_name = '';

						//If source table is set
						if( !empty($attributes[self::ATTR_SOURCE]) )
						{
							$sql.= self::QUOTE_TABLE.$attributes[self::ATTR_SOURCE].self::QUOTE_TABLE.'.';
							$alias_name = $attributes[self::ATTR_SOURCE].'.';
						}

						//Add col name			
						$sql .= self::QUOTE_COL.$attributes[self::ATTR_NAME].self::QUOTE_COL;
						$alias_name .= $attributes[self::ATTR_NAME];

						//Override alias if set externat
						if( !empty($attributes[self::ATTR_ALIAS]) )
						{
							$alias_name = $attributes[self::ATTR_ALIAS];
						}
						
						//Add relation
						if( !empty($attributes[self::ATTR_RELATION]) )
						{
							//IN statement escapes values immediately (would be very hard to implement PDO prepare with INs)
							if( $is_in_statement )
							{
								$value = $attributes[self::ATTR_VALUE];
								switch( gettype($value) )
								{
									case 'array':
									case 'object':
										$encoded_values = array();
										foreach($value AS $item)
										{
											$encoded_values[] = Art_Main::db()->quote($item);
										}
										$encoded_value = implode(',', $encoded_values);
										break;
									default:
										$encoded_value = Art_Main::db()->quote($value);
								}
								
								$sql .= ' '.self::REL_IN.'('.$encoded_value.') ';
								
								//Unset value, so it is not passed to PDO::execute
								unset($this->_values[$attributes[self::ATTR_NAME]]);
							}
							else
							{
								$sql .= ' '.$attributes[self::ATTR_RELATION].' ';
							}
						}
						else
						{
							$sql .= ' '.self::REL_DEFAULT.' ';
						}	

						if( !$is_in_statement )
						{
							//Add value alias name
							$sql .= ':'.$alias_name.' ';
						}

						$i++;
					}
				}
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
		return !empty($this->_list) || !empty($this->_plain);
	}
}