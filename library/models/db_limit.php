<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 */
class Art_Model_Db_Limit extends Art_Abstract_Model_Db_Statement {
	
	/**
	 *	@var int Selected rows count 
	 */
	protected $_count;
	
	/**
	 *	@var int Selected rows offset 
	 */
	protected $_offset;

	
	/**
	 *	@param int $count
	 *	@param int $offset
	 */
	function __construct( $count = NULL, $offset = NULL )
	{
		if( NULL !== $count )
		{
			$this->set($count, $offset);	
		}
	}
	
	
	/**
	 *	Set limit (count and offset)
	 * 
	 *	@param int $count
	 *	@param int $offset
	 *	@return Art_Model_Db_Limit
	 */
	function set( $count, $offset = NULL )
	{
		$this->_unlock();
		
		$this->_count = $count;
		$this->_offset = $offset;
		
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
				$sql .= 'LIMIT '.$this->_count;
			
				if( !empty($this->_offset) )
				{
					$sql .= ', '.$this->_offset;
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
		return !empty($this->_count);
	}
}