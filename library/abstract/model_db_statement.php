<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/abstract
 *	@abstract
 */
abstract class Art_Abstract_Model_Db_Statement {
	use Art_Event_Emitter;
	
	/**
	 *	If true, next getSQL will output buffered SQL
	 * 
	 *	@var bool 
	 */
	protected $_locked = false;
	
	/**
	 *	Buffered getSQL output
	 * 
	 *	@var string 
	 */
	protected $_SQL = '';
	
	/**
	 *	Plain SQL statement
	 * 
	 *	@var string 
	 */
	protected $_plain;
	
	/**
	 *	Quote to use to quote col names
	 */
	const QUOTE_COL = '`';
	
	/**
	 *	Quote to use to quote table names
	 */
	const QUOTE_TABLE = '`';
	
	/**
	 *	Quote to use to quote alias names
	 */
	const QUOTE_ALIAS = '"';
	
	
	/**
	 *	Return SQL of this statement
	 * 
	 *	@return string
	 */
	public abstract function getSQL();

	
	/**
	 *	Return true if this statement is not empty
	 * 
	 *	@return bool
	 */
	protected abstract function notEmpty();
	
	
	/**
	 *	Lock this statement (next getSQL will use buffer)
	 * 
	 *	@access protected
	 *	@final
	 *	@return Art_Abstract_Model_Db_Statement
	 */
	protected final function _lock()
	{
		$this->_locked = true;
		
		return $this;
	}		
	
	
	/**
	 *	Unlock this statement (next getSQL will generate new)
	 * 
	 *	@access protected
	 *	@final
	 *	@return Art_Abstract_Model_Db_Statement
	 */
	protected final function _unlock()
	{
		$this->_locked = false;
		
		return $this;
	}
	
	
	/**
	 *	True if this instance is locked - next getSQL will output buffered query
	 * 
	 *	@access protected
	 *	@final
	 *	@return bool
	 */
	protected final function _isLocked()
	{
		return $this->_locked;
	}
	
	
	/**
	 *	Get saved SQL query from getSQL()
	 * 
	 *	@access protected
	 *	@final
	 *	@return string
	 */
	protected final function _getSavedSQL()
	{
		return $this->_SQL;
	}
	
	
	/**
	 *	Save query from getSQL() for later use
	 * 
	 *	@param string $query
	 *	@return Art_Abstract_Model_Db_Statement
	 */
	protected final function _saveSQL( $query )
	{
		$this->_SQL = $query;
		
		return $this;
	}
	
	
	/**
	 *	Set plain SQL statement
	 * 
	 *	@param string $statement
	 *	@return Art_Abstract_Model_Db_Statement
	 */
	final function setPlain( $statement )
	{
		$this->_plain = $statement;
		return $this;
	}
}