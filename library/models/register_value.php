<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property string	$namespace	Namespace name
*	@property string	$name		Register key
 *	@property string	$value		Register value
 */
class Art_Model_Register_Value extends Art_Abstract_Model_DB {
    
    protected static $_table = 'register_value';
    
    protected static $_cols = array('id'			=>  array('select','insert'),
                                    'namespace'		=>  array('select','insert','update'),
                                    'name'			=>  array('select','insert','update'),
                                    'value'			=>  array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
	
	
	/**
	 *	Returns true if value with key&namespace exists
	 * 
	 *	@static
	 *	@param string $namespace
	 *	@param string $name
	 *	@return bool
	 */
	static function search( $namespace, $name = NULL )
	{
		if( NULL === $name )
		{
			return static::fetchAll(array('namespace' => $namespace));
		}
		else
		{
			$where_stmt = new Art_Model_Db_Where(array(array('name' => 'namespace', 'value' => $namespace ), array( 'name' => 'name', 'value' => $name )));
			return new Art_Model_Register_Value($where_stmt);
		}
		
	}

	
	/**
	 *	Fetch all values simplified (grouped by namespace, name as key)
	 *
	 *	@static 
	 *	@return array
	 */
	static function fetchAllSimple()
	{
		//Get all namespaces
		$namespaces = self::getNamespaces();

		//Get all register values
		$regs = self::fetchAll();
		
		$output = array();
		
		//Prepare namespaces arrays
		foreach( $namespaces AS $namespace )
		{
			$output[$namespace] = array();
		}
		
		//Put register values in arrays
		foreach( $regs AS $reg ) /* @var $reg this */
		{			
			$output[$reg->namespace][$reg->name] = $reg->value;
		}
		
		return $output;
	}
	
	
	/**
	 *	Get all namespaces names
	 * 
	 *	@static
	 *	@return array
	 */
	static function getNamespaces()
	{
		$select_stmt = new Art_Model_Db_Select( static::getTableName(), 'namespace');
		$group_stmt = new Art_Model_Db_Group_By('namespace');
		
		$query = Art_Main::db()->assemble($select_stmt, $group_stmt);
		$query->execute();
		$data = $query->fetchAll();
		
		$output = array();
		foreach( $data AS $item )
		{
			$output[] = $item['namespace'];
		}
		
		return $output;
	}
}