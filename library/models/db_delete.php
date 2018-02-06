<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Db_Delete extends Art_Abstract_Model_Db_Statement {
		
	/**
	 *	Table to delete
	 * 
	 *	@var string 
	 */
	protected $_table = '';
	
	
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
	 */
	function __construct( $table = NULL )
	{
		if( NULL !== $table )
		{
			$this->setTable($table);
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
				//Add statement and table
				$sql .= 'DELETE FROM '.self::QUOTE_TABLE.$this->_table.self::QUOTE_TABLE.' ';
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
		return !empty($this->_table);
	}
}