<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property string	$type
 *	@property string	$name
 *	@property string	$settings
 *  @property int		$sort
 *  @property int		id_category -- Only for sorting all have 1
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 */
class Service extends Art_Abstract_Model_DB {

	protected static $_caching = false;
    
    protected static $_table = 'service';
    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'type'			=>	array('select','insert'),
									'name'			=>	array('select','insert','update'),
									'settings'		=>	array('select','insert','update'),
									'sort'			=>	array('select','insert','update'),
									'id_category'	=>	array('select'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

	/**
	 *	Get next sort value
	 * 
	 *	@static
	 *	@return int
	 */
	static function getNextSort()
	{
		$select_stmt = new Art_Model_Db_Select(self::$_table, array('function' => 'MAX(`sort`)'));
		
		$query = Art_Main::db()->select($select_stmt);
		$query->execute();
		$id = $query->fetchColumn();
		
		if( NULL !== $id )
		{
			++$id;
		}
		else
		{
			$id = 1;
		}
		
		return $id;
	}

	
	/**
	 *	Get service upper from this one (Empty instance if not found)
	 * 
	 *	@return Service
	 */
	function getUpper()
	{
		return $this->_getByDirection('up');
	}
	
	
	/**
	 *	Get service below this one (Empty instance if not found)
	 * 
	 *	@return Service
	 */
	function getDowner()
	{
		return $this->_getByDirection('down');
	}
	
	
	/**
	 *	Swap positions of 2 services
	 * 
	 *	@static
	 *	@param Service $a1
	 *	@param Service $a2
	 *	@return void
	 */
	static function swapPositions(Service $a1, Service $a2 )
	{
		$buff = $a1->sort;
		
		$a1->sort = $a2->sort;
		$a2->sort = $buff;
		
		$a1->save();
		$a2->save();
	}
	
	
	/**
	 * Get closest Service to this one (by direction)
	 * 
	 *	@access protected
	 *	@param string $dir
	 *	@return \Service
	 */
	protected function _getByDirection( $dir )
	{
		switch( strtolower($dir) )
		{
			case 'up':
			{
				$rel = '<';
				$type = 'DESC';
				break;
			}
			case 'down':
			{
				$rel = '>';
				$type = 'ASC';
				break;
			}
			default: 
			{
				trigger_error('Invalid argument supplied to Service->_getByDirection()',E_USER_ERROR);
			}
		}
		
		$where_stmt = new Art_Model_Db_Where(array( array('name' => 'sort', 'value' => $this->sort, 'relation' => $rel )));
		$order_stmt = new Art_Model_Db_Order(array( 'name' => 'sort', 'type' => $type ));
		$limit_stmt = new Art_Model_Db_Limit(1);

		$service = Service::fetchAllPrivileged($where_stmt, $order_stmt, $limit_stmt);
		if( count($service) )
		{
			return $service[0];
		}
		else
		{
			return new Service();
		}
	}
}