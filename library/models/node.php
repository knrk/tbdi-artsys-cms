<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property int		$parent_id
 *	@property string	$layer			Layer to which this node belongs to (public/admin)
 *	@property int		$id_node_type
 *	@property string	$name
 *	@property url		$url
 *	@property int		$sort
 *	@property int		$rights
 *	@property int		$active
 *  @property string	$settings
 * 
 *	@method	Art_Model_Node_Type		getType()									Returns node type
 *	@method \Art_Model_Node			setType(Art_Model_Node_Type $node_Type)		Sets node type
 */
class Art_Model_Node extends Art_Abstract_Model_DB {
    
    protected static $_table = 'node';
	
	protected static $_link = array('type' => 'Art_Model_Node_Type');
	
	protected static $_foreign = array('id_node_type');
	
    protected static $_cols = array('id'			=>  array('select','insert'),
                                    'parent_id'		=>  array('select','insert','update'),
                                    'layer'			=>  array('select','insert','update'),
                                    'id_node_type'	=>  array('select','insert','update'),
                                    'name'			=>  array('select','insert','update'),
                                    'url'			=>  array('select','insert','update'),
                                    'sort'			=>  array('select','insert','update'),
                                    'rights'		=>  array('select','insert','update'),
                                    'active'		=>  array('select','insert','update'),
									'settings'		=>  array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
	
	
	static function fetchAllPublicPermitted()
	{
		return self::fetchAllPrivileged(array( 'layer' => Art_Router::LAYER_FRONTEND ));
	}
}