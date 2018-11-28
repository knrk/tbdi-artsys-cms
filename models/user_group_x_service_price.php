<?php
/**
 *  @author Jakub Pastuszek <pastuszek@itart.cz>
 *  @package models
 * 
 *	@property int		$id
 *	@property int		$id_user_group
 *	@property int		$id_service_price
 *  @property string	$time_from
 *	@property string	$time_to
 *	@property int		$created_by
 *	@property int		$modified_by
 *	@property string	$created_date
 *	@property string	$modified_date
 * 
 *	@method Art_Model_User_Group	getUserGroup()
 *	@method Service_Price			getServicePrice()
 *	@method this		setUserGroup(Art_Model_User_Group $user_group)
 *	@method this		setServicePrice(Service_Price $service_price)
 */
class User_Group_X_Service_Price extends Art_Abstract_Model_DB {

	protected static $_caching = false;
    
    protected static $_table = 'user_group_x_service_price';
		        
	protected static $_foreign = array('id_user_group', 'id_service_price');
	
	protected static $_link = array('userGroup'		=> 'Art_Model_User_Group',
									'servicePrice'		=> 'Service_Price');
    
    protected static $_cols =  array('id'				=>	array('select','insert'),
									'id_user_group'		=>	array('select','insert','update'),
									'id_service_price'	=>	array('select','insert','update'),
									'time_from'			=>	array('select','insert','update'),
									'time_to'			=>	array('select','insert','update'),
									'created_by'		=>	array('select','insert'),
									'modified_by'		=>	array('select','update'),
									'created_date'		=>	array('select'),
									'modified_date'		=>	array('select'));

}

