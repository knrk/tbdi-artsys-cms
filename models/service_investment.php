<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$month
 *	@property int		$year
 *	@property string	$target
 *	@property int		$visible
 *	@property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 */
class Service_Investment extends Art_Abstract_Model_DB {
    
    protected static $_table = 'service_investment';
	
	protected static $_foreign = array();
	
	protected static $_link = array();
	
	protected static $_fetch = array('investmentValues'	=> 'Service_Investment_Value');
    
    protected static $_cols =  array('id'			=>	array('select','insert'),
									'month'			=>	array('select','insert','update'),
									'year'			=>	array('select','insert','update'),
									'target'		=>	array('select','insert','update'),
									'visible'		=>	array('select','insert','update'),
									'created_by'	=>	array('select','insert'),
									'modified_by'	=>	array('select','update'),
									'created_date'	=>	array('select'),
									'modified_date'	=>	array('select'));

}