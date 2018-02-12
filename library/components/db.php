<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/components
 *	@final
 */
final class Art_DB extends Art_Abstract_Component {
    
    /**
     *  @static
     *  @access protected
     *  @var bool True if was initialized already
     */
    protected static $_initialized = false;
    
    /**
     *  @static
     *  @access protected
     *  @var Art_PDO DB handler
     */
    protected static $_handler;
    

    /**
     *  @static
     *  @return Art_PDO DB Handler
     */
    static function get()
    {
        return self::$_handler;
    }
    
    
    /**
     *  Initialize the component
     *  @static
     *  @return void
     */
    static function init() {
        if (parent::init()) {
            try {
                self::$_handler = new Art_PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
                self::$_handler->exec('set names utf8');
                self::$_handler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
				self::$_handler->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }  catch(PDOException $e) {
                trigger_error('Connection to database couldn\'t be established: '.$e->getMessage(),E_USER_ERROR);
            }
        }
    }
}


/**
 *	Extended PDO
 * 
 *	New method - delete($query)
 *  New method - nextInsertId($table_name)	
 * 
 *  @package library/components
 *	@final
 */
class Art_PDO extends PDO {
	
	/** 
	 *	@access protected
	 *	@var int Number of all PDO requests (query|prepare)
	 */
	protected $_requests_count = 0;

	
	/**
	 * Creates a PDO instance representing a connection to a database
	 * 
	 *	@link http://php.net/manual/en/pdo.construct.php
	 *	@param $dsn
	 *	@param $username [optional]
	 *	@param $passwd [optional]
	 *	@param $options [optional]
	 */
	public function __construct($dsn, $username = '', $passwd = '', $options = array()) 
	{
		parent::__construct($dsn, $username, $passwd, $options);

		$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('Art_PDOStatement', array($this)));
	}
	
	/**
	 *	Return number of all PDO requests (query|prepare)
	 * 
	 *	@access public
	 *	@return int Number of requests
	 */
	public function getRequestCount()
	{
		return $this->_requests_count;
	}
	
	
	/**
	 *	Prepares a statement for execution and returns a statement object
	 * 
	 *	@link http://php.net/manual/en/pdo.prepare.php
	 *	@param string $statement <p>
	 *	@param array $driver_options [optional] <p>
	 *	@return PDOStatement If the database server successfully prepares the statement,
	 */
	public function prepare($statement,$driver_options = array()) 
	{
		$result = parent::prepare($statement, $driver_options);
		if($result)
		{
			$this->_requests_count++;
		}
		return $result;
	}
	
	
	/**
	 *	Prepares a statement for execution and returns a statement object
	 * 
	 *	@link http://php.net/manual/en/pdo.prepare.php
	 *	@param Art_Abstract_Model_Db_Statement $st1
	 *	@param Art_Abstract_Model_Db_Statement $st2
	 *	@param Art_Abstract_Model_Db_Statement $st3
	 *	@param Art_Abstract_Model_Db_Statement $st4
	 *	@param Art_Abstract_Model_Db_Statement $st5
	 *	@return PDOStatement If the database server successfully prepares the statement,
	 */
	public function assemble(Art_Abstract_Model_Db_Statement $st1, Art_Abstract_Model_Db_Statement $st2 = NULL, Art_Abstract_Model_Db_Statement $st3 = NULL, Art_Abstract_Model_Db_Statement $st4 = NULL, Art_Abstract_Model_Db_Statement $st5 = NULL )
	{
		$statement = '';
		
		for($i = 1; $i <= 5; $i++)
		{
			if( NULL !== ${'st'.$i} )
			{
				$statement .= ${'st'.$i}->getSQL();
			}
		}

		$result = parent::prepare($statement);
		if($result)
		{
			$this->_requests_count++;
		}
		return $result;
	}
	
	
	/**
	 *	Prepares a statement for selecting from DB
	 * 
	 *	@param Art_Abstract_Model_Db_Select $select
	 *	@param Art_Abstract_Model_Db_Where $where
	 *	@param Art_Abstract_Model_Db_Order $order
	 *	@param Art_Abstract_Model_Db_Limit $limit
	 *	@return PDOStatement If the database server successfully prepares the statement,
	 */
	function select( Art_Model_Db_Select $select, Art_Model_Db_Where $where = NULL, Art_Model_Db_Order $order = NULL, Art_Model_Db_Limit $limit = NULL, Art_Model_Db_Group_By $group = NULL )
	{
		return $this->assemble($select, $where, $group, $order, $limit);
	}
	
	
	/**
	 *	Prepares a statement for inserting to DB
	 * 
	 *	@param Art_Abstract_Model_Db_Insert $insert
	 *	@return PDOStatement If the database server successfully prepares the statement,
	 */
	function insert( Art_Model_Db_Insert $insert )
	{
		return $this->assemble($insert);
	}
	
	
	/**
	 *	Prepares a statement for updating the DB
	 * 
	 *	@param Art_Abstract_Model_Db_Update $update
	 *	@param Art_Abstract_Model_Db_Where $where
	 *	@return PDOStatement If the database server successfully prepares the statement,
	 */
	function update( Art_Model_Db_Update $update, Art_Model_Db_Where $where = NULL )
	{
		return $this->assemble($update,$where);
	}
	
	
	/**
	 *	Prepares a statement for insert on duplicate key update DB
	 * 
	 *	@param Art_Abstract_Model_Db_Insert $insert
	 *	@param Art_Abstract_Model_Db_Update $update
	 *	@param Art_Abstract_Model_Db_Where $where
	 *	@return PDOStatement If the database server successfully prepares the statement,
	 */
	function insertOnDuplicateKeyUpdate( Art_Model_Db_Insert $insert, Art_Model_Db_Update $update, Art_Model_Db_Where $where = NULL )
	{
		$update->setOnDuplicateKey();
		
		return $this->assemble($insert,$update,$where);
	}
	
	
	/**
	 *	Executes an SQL statement, returning a result set as a PDOStatement object
	 * 
	 *	@link http://php.net/manual/en/pdo.query.php
	 *	@param string $statement
	 *	@return PDOStatement Returns a PDOStatement object, or FALSE on failure.
	 */
	public function query($statement) 
	{
		try
		{
			$result = parent::query($statement);
			if($result)
			{
				$this->_requests_count++;
			}
			return $result;
		} 
		catch (Exception $ex) 
		{
			trigger_error('Error in MySQL: '.$ex->getMessage(),E_USER_ERROR);
		}
	}
	
	
	/**
	 *	Prepares a statement for deleting in DB
	 * 
	 *	@param Art_Abstract_Model_Db_Delete $delete
	 *	@param Art_Abstract_Model_Db_Where $where
	 *	@return PDOStatement If the database server successfully prepares the statement,
	 */
	function delete( Art_Model_Db_Delete $delete, Art_Model_Db_Where $where)
	{
		return $this->assemble($delete,$where);
	}
	
	
    /**
     *  Deletes all rows by a query
	 * 
     *  Returns count of deleted rows
     *  @param string $query What should be done
     *  @return int
     *  @example delete('FROM users WHERE active=0') deletes all unactive users
     *  @example delete('FROM articles WHERE active=0 ORDER BY date DESC LIMIT 2') deletes 2 last inactive articles ordered by date
     */
    function deleteAll($query)
    {
        //Get position of WHERE 
        $where_pos = strpos($query,'WHERE');
        if($where_pos<1)
        {
            $from = $query;
            $where = '';
        }
        else
        {
            //Substr query
            $from = substr($query,0,$where_pos);
            $where = substr($query,$where_pos);
        }
        
        //Get primary key
        $primary_key = self::query('SHOW INDEX '.$from)->fetchColumn(4);
        
        //Select rows
        $query = self::query('SELECT `'.$primary_key.'` '.$from.' '.$where);
        
        //Delete rows
        $deleted_count = 0;
        while($row=$query->fetchColumn())
        {
            $deleted_count++;
            self::query('DELETE '.$from.' WHERE `'.$primary_key.'`="'.$row.'" ');
        }
        
        return $deleted_count;
    }
    
    
    /**
     *  Gets next id that will be inserted in table
	 * 
     *  @param $table_name Table name
     *  @return int next id
     *  @example nextInsertId('user') Returns next insert id of table user
     */
    function nextInsertId($table_name)
    {
		$query = self::prepare('SHOW TABLE STATUS LIKE :table_name');
		$query->execute(array(':table_name' => $table_name));
		if($result = $query->fetch(PDO::FETCH_ASSOC))
		{
			return $result['Auto_increment'];
		}
		else
		{
			trigger_error('Table '.$table_name.' not found',E_USER_ERROR);
		}
    }
}


/**
 *  @package library/components
 *	@final
 */
final class Art_PDOStatement extends PDOStatement 
{
	private function __construct() {}
	
	/**
	 * Executes a prepared statement
	 * 
	 * @link http://php.net/manual/en/pdostatement.execute.php
	 * @param array $input_parameters [optional] 
	 * @return bool TRUE on success or FALSE on failure.
	 */
	public function execute($input_parameters = null)
	{
		try
		{
			return parent::execute($input_parameters);
		} 
		catch (PDOException $ex) 
		{
			trigger_error('Error in MySQL statement [ '.$this->queryString.']'."\n".$ex->getMessage().' DEBUG: '.Art_Router::dumpRouteStr(),E_USER_ERROR);					
		}
	}
}