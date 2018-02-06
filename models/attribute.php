<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property string	$type
 *	@property string	$name
 *	@property string	$units
 *	@property int		$rights
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 */
class Attribute extends Art_Abstract_Model_DB {
    
    protected static $_table = 'attribute';
    
    protected static $_cols =  array('id'		=>	array('select','insert'),
									'type'		=>	array('select','insert','update'),
									'name'		=>	array('select','insert','update'),
                                    'units'		=>	array('select','insert','update'),
                                    'rights'	=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
	
	/**
	 *	Array of attribute types
	 */
	const TYPES = array('bool','int','string','float','array','multiArray');
}