<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package library/models
 * 
 *	@property int		$id
 *	@property string	$type	Module type which this node belongs  to
 *	@property string	$name
 */
class Art_Model_Node_Type extends Art_Abstract_Model_DB {
    
    protected static $_table = 'node_type';
	
    protected static $_cols = array('id'			=>  array('select','insert'),
                                    'type'			=>  array('select','insert','update'),
                                    'name'			=>  array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
}