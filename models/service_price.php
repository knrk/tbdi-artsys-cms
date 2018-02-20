<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_service
 *	@property int		$price
 *  @property string	$time_interval
 *	@property boolean	$is_default
 *  @property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Service		getService()
 *	@method this		setService(Service $service)
 */
class Service_Price extends Art_Abstract_Model_DB {

	// protected static $_caching = false;
    
    protected static $_table = 'service_price';
        
	protected static $_foreign = array('id_service');
	
	protected static $_link = array('service'		=> 'Service');
	
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'id_service'	=>	array('select','insert','update'),
									'price'			=>	array('select','insert','update'),
									'time_interval'	=>	array('select','insert','update'),
									'is_default'	=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));
	
}