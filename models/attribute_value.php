<?php
/**
 *  @author Robin Zoň <zon@itart.cz>
 *  @package models
 * 
 *	@property int	$id
 *	@property int	$id_attribute
 *	@property mixed	$value
 *	@property float	$price
 *	@property int	$delivery
 *	@property int	$rights
 *	@property int	$active
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 */
class Attribute_Value extends Art_Abstract_Model_DB {
    
    protected static $_table = 'attribute_value';
    
    protected static $_foreign = array('id_attribute');
    
    protected static $_link = array('attribute' => 'Attribute');
    
    protected static $_cols =  array('id'					=>	array('select','insert'),
                                    'id_attribute'			=>	array('select','insert'),
                                    'value'					=>	array('select','insert','update'),
                                    'price'					=>	array('select','insert','update'),
                                    'delivery'				=>	array('select','insert','update'),
                                    'rights'				=>	array('select','insert','update'),
									'active'				=>	array('select','insert','update'),
									'created_by'			=>	array('select','insert'),
									'modified_by'			=>	array('select','update'),
									'created_date'			=>	array('select'),
									'modified_date'			=>	array('select'));
}